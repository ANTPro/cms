<?

$pages['admin']=array
(
	'title'=>'Инструменты',
	'menu'=>array(
		'title'=>'Админка'
	),
	'templates'=>array
	(
	    'adminmenu'=>'adminmenu.tpl',
	    'adminmenuitem'=>'adminmenuitem.tpl'
	),
	'privs'=>'e',
	//'notmenu'=>'',
	'notuninstall'=>''
);

function pageadmin()
{
	global $tpl,$tools,$page,$privs;
	$tools=array();

	loadplugins('tools','tools');

	if (isset($_GET['tool']))
	{
		$page=$_GET['tool'];

		if ((isset($tools[$page]))&&
			(isset($tools[$page]['privs'],$privs)))
		{
			$properties=$tools[$page];
			$tpl->setvar('TOOLNAME',$page);
            if (!isset($properties['notprivs']))
            {            	$properties['notprivs']=array();            }
			if (in_array($properties['privs'],$privs)&&(!array_intersect($properties['notprivs'],$privs)))
			{
				if (isset($_GET['action']))
				{					$action=$_GET['action'];
				}
				else
				{
				    $action='main';
				}
				if (isset($properties['actions'][$action]))
				{					$func=$page.$action;
					$tpl->setvar('MAIN_HEADER',$properties['actions'][$action]);
					if (isset($properties['templates']))
						$tpl->load($properties['templates']);
					$func();				}			}
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
	else
	{		foreach($tools AS $name=>$properties)
		{
			if (in_array($properties['privs'],$privs))
			{
				$tpl->setvars(array(
					'TITLE'=>$properties['actions']['main'],
					'TOOLNAME'=>$name
				));
				$tpl->varadd('TOOLS','TOOL','adminmenuitem');
			}		}
		$tpl->setvar('MAIN_HEADER','Инструменты');
		$tpl->parse(array('CONTENT'=>'adminmenu'));	}
	$tpl->setvar('PAGE_TITLE','Панель администрирования');
}
?>
