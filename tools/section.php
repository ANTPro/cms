<?

$tools['section']=array
(
	'privs'=>'a',
	'actions'=>array
	(
		'add'=>'Добавление нового раздела',
		'edit'=>'Редактирование раздела',
		'delete'=>'Удаление раздела',
		'main'=>'Редактор разделов'
	),
	'templates'=>array
	(
	    'sectionadd'=>'sectionadd.tpl',
	    'sectionedit'=>'sectionedit.tpl',
	    'sectionlist'=>'sectionlist.tpl'
	),
	'installsql'=>"
		CREATE TABLE IF NOT EXISTS `section` (
		  `key_section` bigint(10) NOT NULL auto_increment,
		  `title` varchar(255) NOT NULL default '',
		  `parent` bigint(20) NOT NULL default '0',
		  `level` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`key_section`),
		  UNIQUE KEY `title` (`title`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>'DROP TABLE IF EXISTS `section`;'
);

function sectionmain()
{
 	global $tpl,$db,$result,$count,$page;

	$fields=array(
		'title'=>array(
			'title'=>'Заголовок',
			'linkid'=>'key_section',
			'link'=>'?page=section&id=[]'
		),
		'ptitle'=>array(
			'title'=>'Родитель',
			'linkid'=>'parent',
			'link'=>'?page=section&id=[]'
		)
	);
	$tools=array
	(
		'delete'=>array
		(
			'title'=>'Удалить',
			'image'=>'delete',
			'linkid'=>'key_section',
			'link'=>"?page=admin&tool=$page&action=delete&id=[]"
		),
		'edit'=>array
		(
			'title'=>'Редактировать',
			'image'=>'edit',
			'linkid'=>'key_section',
			'link'=>"?page=admin&tool=$page&action=edit&id=[]"
		),
		'view'=>array
		(
			'title'=>'Просмотр',
			'image'=>'browse',
			'linkid'=>'key_section',
			'link'=>'?page=section&id=[]'
		)
	);

 	$sql="SELECT ss1.key_section, ss1.title,ss1.parent, ss2.title as ptitle
		FROM section AS ss1	LEFT JOIN section AS ss2 ON ss1.parent = ss2.key_section";
	makeviewtable($sql,$fields,$tools);
    $tpl->parse(array('CONTENT'=>'sectionlist'));
}

function sectionadd()
{
 	global $tpl,$db;

 	if (isset($_POST['submit']))
 	{

    	$fields['key_section']='NULL';
    	$fields['title']=tostr('title');
    	$fields['parent']=postnumparam('id_section');
    	if ($fields['parent']==0)
    	{
    		$fields['level']=0;
    	}
    	else
    	{
    		$fields['level']=1;
    	}
		insertrecord('section',$fields);
 	}
 	else
 	{
		makesectioncombobox(-1,FALSE);
		$tpl->parse(array('CONTENT'=>'sectionadd'));
	}
}

function sectionedit()
{
 	global $tpl,$db,$id,$result;

 	if (isset($_POST['submit']))
 	{
    	$fields['key_section']=$id;
    	$fields['title']=tostr('title');
	   	$fields['parent']=tostr('id_section');
    	if ($fields['parent']==0)
    	{
    		$fields['level']=0;
    	}
    	else
    	{
    		$fields['level']=1;
    	}
		updaterecord('section',$fields);
 	}
 	else
 	{
 	    $result=$db->query("SELECT COUNT(key_section) FROM section WHERE parent=$id");
 		$subyes=$db->result($result, 0)==0;

	 	$sql="SELECT key_section, title, parent FROM section WHERE key_section=$id";
	 	$error="Ошибка. Раздел не найден.";
	 	if(safequery($sql,$error))
	 	{
			$row=$db->getassocrow($result);
			$tpl->setvars(array(
			    'ID'=>$id,
				'TITLE'=>check($row['title'])
			));
			if ($subyes)
			{
				makesectioncombobox($row['parent'],FALSE);
			}
			$tpl->parse(array('CONTENT'=>'sectionedit'));
		}
	}
}

function sectiondelete()
{
	global $page;
    querydelete('section',"?page=admin&tool=$page",'Ошибка. Раздел не существует','Раздел удален.');
}

?>