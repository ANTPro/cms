<?
/*
$pages['page']=array
(
	'title'=>'Название страницы.',
	'menu'=>array(
		'title'=>'Название пункта меню.'
	),
	'privs'=>'Привилегии необходимые для доступа',
	'notprivs'=>array('Привилегии при присутствии которых 403'),
	'index'=>'Определяет главную страницу',
	'notmenu'='Скрыть меню',
	'notmainmenu'='Скрыть главное меню',
	'notmain'='Не парсить главный шаблон, для полной смены шаблонов(печать,ajax)',
);
*/
function checkprivs($login,$privs,$properties)
{
    $go=0;
	if (!isset($properties['notprivs']))
	{
		$properties['notprivs']=array();
	}
	if (!isset($login))
	{
		if(!isset($properties['privs']))
		{
			$go=1;
		}
	}
	else
	{
		if(!isset($properties['privs']))
		{
			$go=1;
		}
		else
		{
			if (in_array($properties['privs'],$privs))
			{
				$go=1;
			}
		}
		if (array_intersect($properties['notprivs'],$privs))
		{
			$go=0;
		}
	}
	return $go;
}

function makemainmenu()
{
	global $tpl,$db,$id,$page,$login,$privs,$pages;

	$tpl->load(array(
		"mainmenu"=>"mainmenu.tpl",
	    'mainmenuitem'=>'mainmenuitem.tpl',
	    'mainmenuitemsel'=>'mainmenuitemsel.tpl'
	));
	foreach($pages AS $pagename=>$properties)
	{
		if (isset($properties['menu']))
		{
			if (checkpage($properties))
			{
				$menu=$properties['menu'];
				$sel='';
				if ($page==$pagename)
				{
		            $sel='sel';
				}
				$tpl->setvars(array(
					'PAGE'=>$pagename,
					'TITLE'=>check($menu['title']),
				));
				$tpl->varadd('LINKMENU','MAINMENUITEM','mainmenuitem'.$sel);
			}
		}
	}
	$tpl->vardelete('TITLE');
	$tpl->parse(array('MAINMENU'=>'mainmenu'));
}

function makemenu()
{
	global $tpl,$db,$id,$page;

	if ($page=='section')
	{
		$menuid=$id;
	}
	else
	{
		$menuid=-1;
	}
    $sql='SELECT key_section,title FROM section WHERE parent=0 ORDER BY title';
	$result=$db->query($sql,FALSE);
	if ($result)
	{
		if ($db->rowcount($result)!=0)
		{
			$tpl->load(array(
				'menu'=>'menu.tpl',
				'menuitem'=>'menuitem.tpl',
				'menuitemsel'=>'menuitemsel.tpl',
				'menuitem2'=>'menuitem2.tpl',
				'menuitem2sel'=>'menuitem2sel.tpl'
			));
			for ($i=0; $i<$db->rowcount($result); $i++)
			{
				$row=$db->getassocrow($result);
				$tpl->setvars(array(
					'ID'=>$row['key_section'],
					'MENUITEM'=>check($row['title']),
				));
				$sel='';
				if ($menuid==$row['key_section'])
				{
		            $sel='sel';
				}
			    $result2=$db->query('SELECT key_section,title FROM section WHERE parent='.$row['key_section']." ORDER BY title");
				for ($j=0; $j<$db->rowcount($result2); $j++)
				{
					$row2=$db->getassocrow($result2);
					$tpl->setvars(array(
						'ID2'=>$row2['key_section'],
						'MENUITEM2'=>check($row2['title']),
					));
					$sel2='';
					if ($menuid==$row2['key_section'])
					{
						$sel2='sel';
					}
					$tpl->varadd('MENUITEMS2','MENUITEM2','menuitem2'.$sel2);
				}
				$tpl->varadd('MENUITEMS','MENUITEM','menuitem'.$sel);
				$tpl->vardelete('MENUITEMS2');
			}
			$tpl->parse(array('MENU'=>'menu'));
		}
	}
}

function checkmode()
{
	global $pages,$page,$mode;
	if ((!file_exists('./config.php'))||(file_exists('./setup')))
    {
    	$mode=FALSE;
    }
    else
    {
    	$mode=TRUE;
    }
}

function getpagename()
{
	global $pages,$page,$mode;
	if (isset($_GET['page']))
	{
		$page=$_GET['page'];
	}
	else
	{
		header("Location:?page=index");exit();
	}
	if ($page=='index')
	{
		$page='404';
		foreach($pages AS $pagename=>$properties)
		{
			if (checkpage($properties))
			{
				if (!isset($properties['notindex']))
				{
					if ($page=='404')
						$page=$pagename;
					if (isset($properties['index']))
						$page=$pagename;
				}
			}
		}
    }
}

function setmessages($messages,$var)
{
	global $tpl,$gourl;
	foreach($messages as $message)
	{
		$tpl->setvar(strtoupper($var).'TEXT',$message);
		$tpl->varadd('MESSAGES',strtoupper($var),$var.'item');
	}
	if (isset($gourl))
	{
		$tpl->setvar('META_URL',$gourl);
		$tpl->varadd('META','METAREFER','metarefer');
	}
}

function checkpage($properties)
{
	global $db,$login,$privs,$mode;
	$openpage=FALSE;
	if (checkprivs($login,$privs,$properties))
	{
		if ($mode || isset($properties['setuponly']))
		{
			$openpage=TRUE;
		}
	}
	return $openpage;
}
function loadpages()
{
	global $tpl,$pages,$page,$privs,$mode,$errors,$infos;
	$tpl->load(array(
	    'main'=>'main.tpl',
		'options'=>'options.tpl',
		'metarefer'=>'metarefer.tpl',
		'erroritem'=>'erroritem.tpl',
		'infoitem'=>'infoitem.tpl'
	));

    getpagename();

	if (isset($pages[$page]))
	{
		$properties=$pages[$page];
		$tpl->setvar('PAGENAME',$page);

		if (checkpage($properties))
		{
			$func='page'.$page;
			$tpl->setvars(array(
				'MAIN_HEADER'=>$properties['title'],
				'PAGE_TITLE'=>$properties['title']
			));
			if (isset($properties['templates']))
				$tpl->load($properties['templates']);
			$func();
			if (!isset($_GET['print']))
			{
				if (!isset($properties['notmainmenu']))
				{
					makemainmenu();
				}
				if ($mode)
				{
					if (!isset($properties['notmenu']))
					{
						makemenu();
					}
				}
				if (!isset($properties['notmain']))
				{
					$notmain='';
				}
			}
			if (count($errors)!=0)
			{
				setmessages($errors,'error');
			}
			if (count($infos)!=0)
			{
				setmessages($infos,'info');
			}
		}
		else
		{
			header("Location:?page=403");exit();
		}
	}
	else
	{
		header("Location:?page=404");exit();
	}
}
?>
