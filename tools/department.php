<?

$tools['department']=array
(
	'privs'=>'a',
	'actions'=>array
	(
	    'main'=>'Редактор кафедр',
		'add'=>'Добавление новой кафедры',
		'edit'=>'Редактирование кафедры',
		'delete'=>'Удаление кафедры',

		'adduser'=>'Добавление нового преподавателя на кафедру',
		'editlist'=>'Редактирование списка кафедры',
		'deletelink'=>'Удаление преподавателя с кафедры'
	),
	'templates'=>array
	(
		'departmentlist'=>'departmentlist.tpl',
		'departmentadd'=>'departmentadd.tpl',
		'departmentedit'=>'departmentedit.tpl',
		'departmentlistedit'=>'departmentlistedit.tpl',
		'departmentlistadd'=>'departmentlistadd.tpl'
	),
	'installsql'=>
	"
		CREATE TABLE IF NOT EXISTS `department` (
		  `key_department` bigint(10) NOT NULL auto_increment,
		  `title` varchar(50) NOT NULL default '',
		  `id_faculty` bigint(10) NOT NULL default '0',
		  PRIMARY KEY  (`key_department`),
		  UNIQUE KEY `title` (`title`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;

		CREATE TABLE IF NOT EXISTS `linkdepartment` (
		  `key_linkdepartment` bigint(10) NOT NULL auto_increment,
		  `id_users` bigint(10) NOT NULL default '0',
		  `id_department` bigint(10) NOT NULL default '0',
		  PRIMARY KEY  (`key_linkdepartment`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>
	"
		DROP TABLE IF EXISTS `linkdepartment`;
		DROP TABLE IF EXISTS `department`;
	"
);

function departmentmain()
{
	global $tpl,$db,$result,$count,$id,$page;

	$fields=array(
		'title'=>array(
			'title'=>'Название'
		),
		'faculty'=>array(
			'title'=>'Факультет'
		)
	);
	$tools=array
	(
		'delete'=>array
		(
			'title'=>'Удалить',
			'image'=>'delete',
			'linkid'=>'key_department',
			'link'=>"?page=admin&tool=$page&action=delete&id=[]"
		),
		'edit'=>array
		(
			'title'=>'Редактировать',
			'image'=>'edit',
			'linkid'=>'key_department',
			'link'=>"?page=admin&tool=$page&action=edit&id=[]"
		),
		'editlist'=>array
		(
			'title'=>'Просмотр',
			'image'=>'editlist',
			'linkid'=>'key_department',
			'link'=>"?page=admin&tool=$page&action=editlist&id=[]"
		),
		'view'=>array
		(
			'title'=>'Просмотр',
			'image'=>'browse',
			'linkid'=>'key_department',
			'link'=>'?page=department&id=[]'
		)
	);

	$sql="SELECT key_department,department.title,faculty.title AS faculty
	FROM department
	LEFT JOIN faculty ON id_faculty = key_faculty";
	makeviewtable($sql,$fields,$tools);
    $tpl->parse(array('CONTENT'=>'departmentlist'));
}
function departmentadd()
{
	global $tpl,$db;
 	if (isset($_POST['submit']))
 	{
    	$fields['key_department']='NULL';
    	$fields['title']=tostr('title');
        $fields['id_faculty']=postnumparam('id_faculty');
		insertrecord('department',$fields);
 	}
 	else
 	{
		$sql="SELECT key_faculty, title FROM faculty";
		makecombobox($sql,-1,'FACULTY');
		$tpl->parse(array('CONTENT'=>'departmentadd'));
	}
}

function departmentedit()
{
	global $tpl,$db,$id,$result;

 	if (isset($_POST['submit']))
 	{
    	$fields['key_department']=$id;
    	$fields['title']=tostr('title');
    	$fields['id_faculty']=postnumparam('id_faculty');
		updaterecord('department',$fields);
 	}
 	else
 	{
	 	$sql="SELECT key_department, title, id_faculty FROM department WHERE key_department=$id";
	 	$error="Ошибка. Кафедра не найдена.";
	 	if(safequery($sql,$error))
	 	{
			$row=$db->getassocrow($result);
			$tpl->setvars(array(
			    'ID'=>$id,
				'TITLE'=>check($row['title'])
			));
            $sql="SELECT key_faculty, title FROM faculty";
			makecombobox($sql,$row['id_faculty'],'FACULTY');
			$tpl->parse(array('CONTENT'=>'departmentedit'));
		}
	}
}

function departmentdelete()
{
	global $page;
    querydelete('department',"?page=admin&tool=$page",'Ошибка. Кафедра не существует','Кафедра удалена.');
}

function departmenteditlist()
{
	global $tpl,$db,$result,$count,$id,$page,$errors,$gourl;

	$fields=array(
		'login'=>array(
			'title'=>'Логин',
			'linkid'=>'key_users',
			'link'=>'?page=profile&id=[]'
		),
		'SNP'=>array(
			'title'=>'ФИО'
		),
		'email'=>array(
			'title'=>'Почта'
		),
		'birthdate'=>array(
			'title'=>'Дата рождения'
		),
		'regdate'=>array(
			'title'=>'Дата регистрации'
		)
	);
	$tools=array
	(
		'delete'=>array
		(
			'title'=>'Удалить',
			'image'=>'delete',
			'linkid'=>'key_linkdepartment',
			'link'=>"?page=admin&tool=$page&action=deletelink&department=$id&id=[]"
		),
		'view'=>array
		(
			'title'=>'Просмотр',
			'image'=>'browse',
			'linkid'=>'key_users',
			'link'=>'?page=profile&id=[]'
		),
		'mail'=>array
		(
			'title'=>'Написать письмо',
			'image'=>'mail',
			'linkid'=>'key_users',
			'link'=>"?page=mail&type=write&user=[]"
		)
	);
	$sql="SELECT COUNT(key_department) FROM department WHERE key_department=$id";
	$id=checkid($id,$sql);
	if (isset($id))
	{
		$sql="SELECT key_linkdepartment,login,
			IF(((surname='')AND(name='')AND(patronymic='')),'Не указано',CONCAT(surname,' ',name,' ',patronymic)) AS SNP,
			email,key_users,birthdate,regdate
			FROM linkdepartment
			LEFT JOIN users ON id_users=key_users
			LEFT JOIN department ON id_department=key_department
			WHERE id_department=$id";
		makeviewtable($sql,$fields,$tools);
		$tpl->setvar('DEPARTMENT',$id);
		$tpl->parse(array('CONTENT'=>'departmentlistedit'));
	}
	else
	{
		$errors[]='Указанна несуществющая кафедра.';
		$gourl="?page=admin&tool=$page";
	}
}

function departmentadduser()
{
	global $tpl,$db,$id,$errors,$gourl,$page,$selfpage;
 	if (isset($_POST['submit']))
 	{
        $sql="SELECT COUNT(key_department) FROM department WHERE key_department=$id";
		$id=checkid($id,$sql);
		if (isset($id))
 		{
	    	$fields['key_linkdepartment']='NULL';
	    	$fields['id_department']=$id;
	        $fields['id_users']=postnumparam('id_users');
			insertrecord('linkdepartment',$fields);
			$gourl="?page=admin&tool=$page&action=editlist&id=$id";
		}
		else
		{
			$errors[]='Кафедра не найдена.';
			$gourl="?page=admin&tool=$page";
		}
 	}
 	else
 	{
 		//{
			$sql="SELECT key_users,
				IF(((surname='')AND(name='')AND(patronymic='')),
					CONCAT(login,' - Не указано'),
					CONCAT(login,' - ',surname,' ',name,' ',patronymic)) AS SNP
				FROM users
				WHERE NOT (key_users IN
				(SELECT id_users FROM linkdepartment WHERE id_department=$id))";// id_userprivs=3 AND
			makecombobox($sql,-1,'USERS');
			$tpl->parse(array('CONTENT'=>'departmentlistadd'));
		//}
	}
}

function departmentdeletelink()
{
	global $page;
	$group='';
	if (isset($_GET['department']))
	{
		$department="&action=editlist&id=".$_GET['department'];
	}
    querydelete('linkgroups',"?page=admin&tool=$page$department",'Ошибка. Преподаватель не может быть удален с кафедры.','Преподаватель удален с кафедры.');
}

?>
