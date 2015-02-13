<?

$tools['group']=array
(
	'privs'=>'m',
	'actions'=>array
	(
		'main'=>'Редактор групп',
		'add'=>'Добавление новой группы',
		'edit'=>'Редактирование группы',
		'delete'=>'Удаление группы',

		'adduser'=>'Добавление нового студента в группу',
		'editlist'=>'Редактирование списка группы',
		'deletelink'=>'Удаление студента из группы'
	),
	'templates'=>array
	(
		'grouplist'=>'grouplist.tpl',
		'groupadd'=>'groupadd.tpl',
		'groupedit'=>'groupedit.tpl',
		'grouplistedit'=>'grouplistedit.tpl',
		'grouplistadd'=>'grouplistadd.tpl'
	),
	'installsql'=>
	"
		CREATE TABLE IF NOT EXISTS `groups` (
		  `key_groups` bigint(10) NOT NULL auto_increment,
		  `title` varchar(5) NOT NULL default '',
		  `id_specialization` bigint(10) NOT NULL default '0',
		  `year` tinyint(1) NOT NULL default '0',
		  PRIMARY KEY  (`key_groups`),
		  UNIQUE KEY `title` (`title`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;

		CREATE TABLE IF NOT EXISTS `linkgroups` (
		  `key_linkgroups` bigint(10) NOT NULL auto_increment,
		  `id_users` bigint(10) NOT NULL default '0',
		  `id_groups` bigint(10) NOT NULL default '0',
		  PRIMARY KEY  (`key_linkgroups`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>
	"
		DROP TABLE IF EXISTS `linkgroups`;
		DROP TABLE IF EXISTS `groups`;
	"
);

function groupmain()
{
	global $tpl,$db,$id,$result,$count,$page;

	$fields=array(
		'title'=>array(
			'title'=>'Название',
			'linkid'=>'key_groups',
			'link'=>'?page=groups&id=[]'
		),
		'year'=>array(
			'title'=>'Курс'
		),
		'faculty'=>array(
			'title'=>'Факультет'
		),
		'department'=>array(
			'title'=>'Кафедра'
		),
		'specialization'=>array(
			'title'=>'Специальность'
		),
		'useringroup'=>array(
			'title'=>'Численность'
		)
	);

	$tools=array
	(
		'delete'=>array
		(
			'title'=>'Удалить',
			'image'=>'delete',
			'linkid'=>'key_groups',
			'link'=>"?page=admin&tool=$page&action=delete&id=[]"
		),
		'edit'=>array
		(
			'title'=>'Редактировать',
			'image'=>'edit',
			'linkid'=>'key_groups',
			'link'=>"?page=admin&tool=$page&action=edit&id=[]"
		),
		'editlist'=>array
		(
			'title'=>'Просмотр',
			'image'=>'editlist',
			'linkid'=>'key_groups',
			'link'=>"?page=admin&tool=$page&action=editlist&id=[]"
		),
		'view'=>array
		(
			'title'=>'Просмотр',
			'image'=>'browse',
			'linkid'=>'key_groups',
			'link'=>'?page=group&id=[]'
		)
	);

	$sql="SELECT key_groups,groups.title,year,
	faculty.title AS faculty,
	department.title AS department,
	specialization.title AS specialization,
	(SELECT COUNT(key_linkgroups)FROM linkgroups WHERE id_groups=key_groups) AS useringroup
	FROM groups
	LEFT JOIN specialization ON id_specialization = key_specialization
	LEFT JOIN department ON id_department = key_department
    LEFT JOIN faculty ON id_faculty = key_faculty";
	makeviewtable($sql,$fields,$tools);
    $tpl->parse(array('CONTENT'=>'grouplist'));
}

function groupadd()
{
	global $tpl,$db;
 	if (isset($_POST['submitdata'])&&($_POST['choise']==''))
 	{
    	$fields['key_groups']='NULL';
    	$fields['title']=tostr('title');
        $fields['id_specialization']=postnumparam('id_specialization');
        $fields['year']=postnumparam('year');
		insertrecord('groups',$fields);
 	}
 	else
 	{
        setfields(array('faculty','department','specialization'),array('title','year'));
		$tpl->parse(array('CONTENT'=>'groupadd'));
	}
}

function groupedit()
{
	global $tpl,$db,$id,$result,$count;
 	if (isset($_POST['submitdata']))
 	{
    	$fields['key_groups']=$id;
    	$fields['title']=tostr('title');
        $fields['id_specialization']=postnumparam('id_specialization');
        $fields['year']=postnumparam('year');
		updaterecord('groups',$fields);
 	}
 	else
 	{
 		if (!isset($_POST['choise']))
 		{
			$sql="SELECT key_groups,groups.title,id_faculty,id_department,id_specialization,year
				FROM groups
				LEFT JOIN specialization ON id_specialization = key_specialization
				LEFT JOIN department ON id_department = key_department
				LEFT JOIN faculty ON id_faculty = key_faculty
				WHERE key_groups=$id";
			$error='Ошибка. Группа не существует';
			if (safequery($sql,$error))
			{
				$row=$db->getassocrow($result);
				$_POST['title']=$row['title'];
				$_POST['id_faculty']=$row['id_faculty'];
				$_POST['year']=$row['year'];
				setfields(array('faculty','department','specialization'),array('title','year'));
				$tpl->parse(array('CONTENT'=>'groupedit'));
			}
		}
	}
}

function groupdelete()
{
	global $page;
    querydelete('groups',"?page=admin&tool=$page",'Ошибка. Группа не существует','Группа удалена.');
}

function groupeditlist()
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
			'linkid'=>'key_linkgroups',
			'link'=>"?page=admin&tool=$page&action=deletelink&group=$id&id=[]"
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
	$sql="SELECT COUNT(key_groups) FROM groups WHERE key_groups=$id";
	$id=checkid($id,$sql);
	if (isset($id))
	{
		$sql="SELECT key_linkgroups,login,
			IF(((surname='')AND(name='')AND(patronymic='')),'Не указано',CONCAT(surname,' ',name,' ',patronymic)) AS SNP,
			email,key_users,birthdate,regdate
			FROM linkgroups
			LEFT JOIN users ON id_users=key_users
			LEFT JOIN groups ON id_groups=key_groups
			WHERE (id_groups=$id)";
		makeviewtable($sql,$fields,$tools);
		$tpl->setvar('GROUP',$id);
		$tpl->parse(array('CONTENT'=>'grouplistedit'));
	}
	else
	{
		$errors[]='Указанна несуществющая группа.';
		$gourl="?page=admin&tool=$page";
	}
}

function groupadduser()
{
	global $tpl,$db,$id,$errors,$gourl,$page,$selfpage;
 	if (isset($_POST['submit']))
 	{
        $sql="SELECT COUNT(key_groups) FROM groups WHERE key_groups=$id";
		$id=checkid($id,$sql);
		if (isset($id))
 		{
	    	$fields['key_linkgroups']='NULL';
	    	$fields['id_groups']=$id;
	        $fields['id_users']=postnumparam('id_users');
			insertrecord('linkgroups',$fields);
			$gourl="?page=admin&tool=$page&action=editlist&id=$id";
		}
		else
		{
			$errors[]='Группа не найдена.';
			$gourl="?page=admin&tool=$page";
		}
 	}
 	else
 	{
 		if(isset($id))
 		{
	 		$sql="SELECT COUNT(key_groups) FROM groups WHERE key_groups=$id";
	 		$id=checkid($id,$sql);
 		}
		if(isset($id))
		{
			$sql="SELECT key_users,
				IF(((surname='')AND(name='')AND(patronymic='')),
					CONCAT(login,' - Не указано'),
					CONCAT(login,' - ',surname,' ',name,' ',patronymic)) AS SNP
				FROM users
				WHERE NOT (key_users IN
				(SELECT id_users FROM linkgroups WHERE id_groups=$id))";//id_userprivs=2 AND
			makecombobox($sql,-1,'USERS');
			$tpl->parse(array('CONTENT'=>'grouplistadd'));
		}
	}
}

function groupdeletelink()
{
	global $page;
	$group='';
	if (isset($_GET['group']))
	{
		$group="&action=editlist&id=".$_GET['group'];
	}
    querydelete('linkgroups',"?page=admin&tool=$page$group",'Ошибка. Студент не может быть удален из группы.','Студент удален из группы.');
}

?>
