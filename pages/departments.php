<?
$pages['departments']=array
(
	'title'=>'Список кафедр',
	'menu'=>array(
		'title'=>'Кафедры'
	),
	'templates'=>array
	(
		'departmentsviewlist'=>'departmentsviewlist.tpl',
		'departmentsview'=>'departmentsview.tpl'
	),
	'privs'=>'u',
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

function pagedepartments()
{
	global $tpl,$db,$result,$count,$id,$page;

	$sql="SELECT COUNT(key_department) FROM department WHERE key_department=$id";
	$id=checkid($id,$sql);
	if (isset($id))
	{		pagedepartmentslist();	}
	else
	{
		$fields=array(
			'title'=>array(
				'title'=>'Название',
				'linkid'=>'key_department',
				'link'=>'?page=departments&id=[]'
			),
			'faculty'=>array(
				'title'=>'Факультет'
			)
		);
		$sql="SELECT key_department,department.title,faculty.title AS faculty
		FROM department
		LEFT JOIN faculty ON id_faculty = key_faculty";
		makeviewtable($sql,$fields);
	    $tpl->parse(array('CONTENT'=>'departmentsviewlist'));
    }
}

function pagedepartmentslist()
{
	global $tpl,$db,$result,$count,$id,$page,$errors,$gourl;
    $page='';
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
		'mail'=>array
		(
			'title'=>'Написать письмо',
			'image'=>'mail',
			'linkid'=>'key_users',
			'link'=>"?page=mail&type=write&user=[]"
		),
		'view'=>array
		(
			'title'=>'Просмотр',
			'image'=>'browse',
			'linkid'=>'key_users',
			'link'=>'?page=profile&id=[]'
		)
	);

	$sql="SELECT key_linkdepartment,login,
		IF(((surname='')AND(name='')AND(patronymic='')),'Не указано',CONCAT(surname,' ',name,' ',patronymic)) AS SNP,
		email,key_users,birthdate,regdate
		FROM linkdepartment
		LEFT JOIN users ON id_users=key_users
		LEFT JOIN department ON id_department=key_department
		WHERE id_department=$id";
	makeviewtable($sql,$fields,$tools);
	$tpl->setvar('DEPARTMENT',$id);
	$tpl->parse(array('CONTENT'=>'departmentsview'));
}

?>
