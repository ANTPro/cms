<?
$tools['plugins']=array
(
	'privs'=>'a',
	'actions'=>array
	(
		'main'=>'Установка расширений'
	),
	'templates'=>array
	(
		'pluginslist'=>'pluginslist.tpl',
		'pluginsitem'=>'pluginsitem.tpl'
	),
	'notuninstall'=>''
);

function moveplugins($var)
{
	global $$var,$infos,$errors;
	$installdir='install';
	foreach($$var AS $name=>$properties)
    {
    	if(!isset($properties['notuninstall']))
    	{
		    if(!isset($_POST[$var[0].$name]))
			{
				$dirsrc="./$var/";
				$dirdst="./$installdir/$var/";
				$dirtplsrc='./templates/';
				$dirtpldst="./$installdir/$var/";
				$info="Плагин: $name удален";
			}
			else
			{
				$dirsrc="./$installdir/$var/";
				$dirdst="./$var/";
				$dirtplsrc="./$installdir/$var/";
				$dirtpldst='./templates/';
				$info="Плагин: $name установлен";
			}
			$err=array();
            if (!file_exists("$dirdst$name.php"))
            {
				if (file_exists("$dirsrc$name.php"))
				{
					rename("$dirsrc$name.php","$dirdst$name.php");
				}
				else
				{					$err[]="$dirsrc$name.php";				}
				if (isset($properties['templates']))
				{
					foreach($properties['templates'] AS $tplname=>$filename)
				    {
				    	if (file_exists("$dirtplsrc$filename"))
				    	{
							rename("$dirtplsrc$filename","$dirtpldst$filename");
						}
						else
						{
							$err[]="$dirtplsrc$filename";
						}
				    }
				}
				if (count($err)==0)
				{					$infos[]=$info;				}
				else
				{
					$error=implode('<br/>Файл не найден: ',$err);
					$errors[]=$info.$error;				}
			}
		}
    }
}

function pluginsmain()
{
	global $tpl,$tools,$pages,$setuppages,$page,$infos,$errors;
	$backuptools=$tools;
	$backuppages=$pages;
	if (!@opendir('./install')) mkdir('./install');
	if (!@opendir('./install/pages')) mkdir('./install/pages');
	if (!@opendir('./install/tools')) mkdir('./install/tools');

	foreach($pages AS $pagename=>$properties)
    {
    	$pages[$pagename]['notinstall']='';
    }
    loadplugins('install/pages','pages');
    foreach($tools AS $toolname=>$properties)
    {
    	$tools[$toolname]['notinstall']='';
    }
    loadplugins('install/tools','tools');

    if(isset($_POST['install']))
	{		moveplugins('pages');
		moveplugins('tools');
		$infos[]='Установка/удаление завершено';
	}
	else
	{
	    foreach($pages AS $pagename=>$properties)
	    {
			$tpl->setvars(array(
				'TITLE'=>$pagename,
				'DESCRIPTION'=>$properties['title']
			));
			if(isset($properties['notinstall']))
			{
				$tpl->setvar('INSTALLED','');
			}
			else
			{
				$tpl->vardelete('INSTALLED');
			}
			if(isset($properties['notuninstall']))
			{
				$tpl->setvar('NOTUNINSTALL','');
				$tpl->vardelete('PLUGINNAME');
			}
			else
			{
				$tpl->setvar('PLUGINNAME',"p$pagename");
				$tpl->vardelete('NOTUNINSTALL');
			}
			$tpl->varadd('PAGESPLUGINS','PAGESPLUGIN','pluginsitem');
	    }

	    foreach($tools AS $toolname=>$properties)
	    {			$tpl->setvars(array(
				'TITLE'=>$toolname,
				'DESCRIPTION'=>$properties['actions']['main']
			));
			if(isset($properties['notinstall']))
			{
				$tpl->setvar('INSTALLED','');
			}
			else
			{
				$tpl->vardelete('INSTALLED');
			}
			if(isset($properties['notuninstall']))
			{
				$tpl->setvar('NOTUNINSTALL','');
				$tpl->vardelete('PLUGINNAME');
			}
			else
			{
				$tpl->setvar('PLUGINNAME',"t$toolname");
				$tpl->vardelete('NOTUNINSTALL');
			}
			$tpl->varadd('TOOLSPLUGINS','TOOLSPLUGIN','pluginsitem');
	    }
		$tpl->parse(array('CONTENT'=>'pluginslist'));
    }
	$tools=$backuptools;
	$pages=$backuppages;
}
?>
