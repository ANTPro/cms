<?

$tools['specialization']=array
(
	'privs'=>'a',
	'actions'=>array
	(
		'add'=>'Добавление новой специальности',
		'edit'=>'Редактирование специальности',
		'delete'=>'Удаление специальности',
		'main'=>'Редактор специальностей'
	),
	'templates'=>array
	(
		'specializationlist'=>'specializationlist.tpl',
		'specializationadd'=>'specializationadd.tpl',
		'specializationedit'=>'specializationedit.tpl'
	),
	'installsql'=>
	"
		CREATE TABLE IF NOT EXISTS `specialization` (
		  `key_specialization` bigint(10) NOT NULL auto_increment,
		  `title` varchar(255) NOT NULL default '',
		  `id_department` bigint(10) NOT NULL default '0',
		  PRIMARY KEY  (`key_specialization`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>
	"
		DROP TABLE IF EXISTS `specialization`;
	"
);

function specializationmain()
{
	global $tpl,$db,$result,$count,$id,$page;

	$fields=array(
		'title'=>array(
			'title'=>'Название'
		),
		'faculty'=>array(
			'title'=>'Факультет'
		),
		'department'=>array(
			'title'=>'Кафедра'
		)
	);
	$tools=array
	(
		'delete'=>array
		(
			'title'=>'Удалить',
			'image'=>'delete',
			'linkid'=>'key_specialization',
			'link'=>"?page=admin&tool=$page&action=delete&id=[]"
		),
		'edit'=>array
		(
			'title'=>'Редактировать',
			'image'=>'edit',
			'linkid'=>'key_specialization',
			'link'=>"?page=admin&tool=$page&action=edit&id=[]"
		),
	);

	$sql="SELECT key_specialization,specialization.title,
	department.title AS department,faculty.title AS faculty
	FROM specialization
	LEFT JOIN department ON id_department = key_department
	LEFT JOIN faculty ON id_faculty = key_faculty";
	makeviewtable($sql,$fields,$tools);
    $tpl->parse(array('CONTENT'=>'specializationlist'));
}
function specializationadd()
{
	global $tpl,$db;
 	if (isset($_POST['submitdata'])&&($_POST['choise']==''))
 	{
    	$fields['key_specialization']='NULL';
    	$fields['title']=tostr('title');
        $fields['id_department']=postnumparam('id_department');
		insertrecord('specialization',$fields);
 	}
 	else
 	{
		setfields(array('faculty','department'),array('title'));
		$tpl->parse(array('CONTENT'=>'specializationadd'));
	}
}

function specializationedit()
{
	global $tpl,$db,$result,$count,$id;

 	if (isset($_POST['submitdata']))
 	{
    	$fields['key_specialization']=$id;
    	$fields['title']=tostr('title');
    	$fields['id_department']=postnumparam('id_department');
		updaterecord('specialization',$fields);
 	}
 	else
 	{
 		if (!isset($_POST['choise']))
 		{
			$sql="SELECT key_specialization, specialization.title,id_faculty,id_department
				FROM specialization
				LEFT JOIN department ON id_department = key_department
				LEFT JOIN faculty ON id_faculty = key_faculty
				WHERE key_specialization=$id";
			$error='Ошибка. Специальность не существует';
			if (safequery($sql,$error))
			{
				$row=$db->getassocrow($result);
				$_POST['title']=$row['title'];
				$_POST['id_faculty']=$row['id_faculty'];
				$_POST['id_department']=$row['id_department'];
				setfields(array('faculty','department'),array('title'));
				$tpl->parse(array('CONTENT'=>'specializationedit'));
			}
		}
	}
}

function specializationdelete()
{
	global $page;
    querydelete('specialization',"?page=admin&tool=$page",'Ошибка. Специальность не существует','Специальность удалена.');
}

?>
