<?

$tools['faculty']=array
(
	'privs'=>'a',
	'actions'=>array
	(
		'add'=>'Добавление нового факультета',
		'edit'=>'Редактирование факультета',
		'delete'=>'Удаление факультета',
		'main'=>'Редактор факультетов'
	),
	'templates'=>array
	(
		'facultylist'=>'facultylist.tpl',
		'facultyadd'=>'facultyadd.tpl',
		'facultyedit'=>'facultyedit.tpl'
	),
	'installsql'=>
	"
		CREATE TABLE IF NOT EXISTS `faculty` (
		`key_faculty` bigint(10) NOT NULL auto_increment,
		`title` varchar(50) NOT NULL default '',
		PRIMARY KEY  (`key_faculty`),
		UNIQUE KEY `faculty` (`title`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>
	"
		DROP TABLE IF EXISTS `faculty`;
	"
);

function facultymain()
{
	global $tpl,$db,$result,$count,$id,$page;

	$fields=array(
		'title'=>array(
			'title'=>'Название',
		),
	);
	$tools=array
	(
		'delete'=>array
		(
			'title'=>'Удалить',
			'image'=>'delete',
			'linkid'=>'key_faculty',
			'link'=>"?page=admin&tool=$page&action=delete&id=[]"
		),
		'edit'=>array
		(
			'title'=>'Редактировать',
			'image'=>'edit',
			'linkid'=>'key_faculty',
			'link'=>"?page=admin&tool=$page&action=edit&id=[]"
		),
	);

	$sql="SELECT key_faculty,title FROM faculty";
	makeviewtable($sql,$fields,$tools);
    $tpl->parse(array('CONTENT'=>'facultylist'));
}
function facultyadd()
{
	global $tpl,$db;
 	if (isset($_POST['submit']))
 	{
    	$fields['key_faculty']='NULL';
    	$fields['title']=tostr('title');
   		insertrecord('faculty',$fields);
 	}
 	else
 	{
		$tpl->parse(array('CONTENT'=>'facultyadd'));
	}
}

function facultyedit()
{
	global $tpl,$db,$result,$count,$id;

 	if (isset($_POST['submit']))
 	{
    	$fields['key_faculty']=$id;
    	$fields['title']=tostr('title');
		updaterecord('faculty',$fields);
 	}
 	else
 	{
	 	$sql="SELECT key_faculty, title FROM faculty WHERE key_faculty=$id";
	 	$error="Ошибка. Оформление не найдено.";
	 	if(safequery($sql,$error))
	 	{
			$row=$db->getassocrow($result);
			$tpl->setvars(array(
			    'ID'=>$id,
				'TITLE'=>check($row['title'])
			));
			$tpl->parse(array('CONTENT'=>'facultyedit'));
		}
	}
}

function facultydelete()
{
	global $page;
    querydelete('faculty',"?page=admin&tool=$page",'Ошибка. Оформление не существует','Оформление удалено.');
}

?>
