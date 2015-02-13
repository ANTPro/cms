<?

$tools['theme']=array
(
	'privs'=>'a',
	'actions'=>array
	(
		'add'=>'Добавление нового оформления',
		'edit'=>'Редактирование оформления',
		'delete'=>'Удаление оформления',
		'main'=>'Редактор оформлений'
	),
	'templates'=>array
	(
		'themeslist'=>'themeslist.tpl',
		'themesadd'=>'themesadd.tpl',
		'themesedit'=>'themesedit.tpl'
	),
	'installsql'=>
	"
		CREATE TABLE IF NOT EXISTS `themes` (
		  `key_themes` bigint(10) NOT NULL auto_increment,
		  `title` varchar(50) NOT NULL default '',
		  PRIMARY KEY  (`key_themes`),
		  UNIQUE KEY `title` (`title`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>
	"
		DROP TABLE IF EXISTS `themes`;
	"
);

function thememain()
{
	global $tpl,$page;

	$fields=array(
		'title'=>array(
			'title'=>'Название'
		),
	);
	$tools=array
	(
		'delete'=>array
		(
			'title'=>'Удалить',
			'image'=>'delete',
			'linkid'=>'key_themes',
			'link'=>"?page=admin&tool=$page&action=delete&id=[]"
		),
		'edit'=>array
		(
			'title'=>'Редактировать',
			'image'=>'edit',
			'linkid'=>'key_themes',
			'link'=>"?page=admin&tool=$page&action=edit&id=[]"
		),
	);

	$sql="SELECT key_themes,title FROM themes";
	makeviewtable($sql,$fields,$tools);
    $tpl->parse(array('CONTENT'=>'themeslist'));
}
function themeadd()
{
	global $tpl,$db;
 	if (isset($_POST['submit']))
 	{
    	$fields['key_themes']='NULL';
    	$fields['title']=tostr('title');
		insertrecord('themes',$fields);
 	}
 	else
 	{
		$tpl->parse(array('CONTENT'=>'themesadd'));
	}
}

function themeedit()
{
	global $tpl,$db,$result,$count,$id;

 	if (isset($_POST['submit']))
 	{
    	$fields['key_themes']=$id;
    	$fields['title']=tostr('title');
		updaterecord('themes',$fields);
 	}
 	else
 	{
	 	$sql="SELECT key_themes, title FROM themes WHERE key_themes=$id";
	 	$error="Ошибка. Оформление не найдено.";
	 	if(safequery($sql,$error))
	 	{
			$row=$db->getassocrow($result);
			$tpl->setvars(array(
			    'ID'=>$id,
				'TITLE'=>check($row['title'])
			));
			$tpl->parse(array('CONTENT'=>'themesedit'));
		}
	}
}

function themedelete()
{
	global $page;
    querydelete('themes',"?page=admin&tool=$page",'Ошибка. Оформление не существует','Оформление удалено.');
}

?>
