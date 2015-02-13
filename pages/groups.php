<?
$pages['groups']=array
(
	'title'=>'Список групп',
	'menu'=>array(
		'title'=>'Группы'
	),
	'templates'=>array
	(
		'groupviewlist'=>'groupviewlist.tpl',
		'groupview'=>'groupview.tpl'
	),
	'privs'=>'u',
	'installsql'=>"
		CREATE TABLE IF NOT EXISTS `groups` (
		  `key_groups` bigint(10) NOT NULL auto_increment,
		  `title` varchar(5) NOT NULL default '',
		  `id_specialization` bigint(10) NOT NULL default '0',
		  `year` tinyint(1) NOT NULL default '0',
		  PRIMARY KEY  (`key_groups`),
		  UNIQUE KEY `title` (`title`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>'DROP TABLE IF EXISTS `groups`;'
);

function pagegroups()
{
	global $tpl,$db,$id,$result,$count,$page;

	$sql="SELECT COUNT(key_groups) FROM groups WHERE key_groups=$id";
	$id=checkid($id,$sql);
	if (isset($id))
	{
		pagegrouplist();	}
	else
	{
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
				'title'=>'Кафедра',
				'linkid'=>'id_department',
				'link'=>'?page=department&id=[]'
			),
			'specialization'=>array(
				'title'=>'Специальность'
			),
			'useringroup'=>array(
				'title'=>'Численность'
			)
		);

		$sql="SELECT key_groups,groups.title,year,id_department,
		faculty.title AS faculty,
		department.title AS department,
		specialization.title AS specialization,
		(SELECT COUNT(key_linkgroups)FROM linkgroups WHERE id_groups=key_groups) AS useringroup
		FROM groups
		LEFT JOIN specialization ON id_specialization = key_specialization
		LEFT JOIN department ON id_department = key_department
	    LEFT JOIN faculty ON id_faculty = key_faculty";
		makeviewtable($sql,$fields);
	    $tpl->parse(array('CONTENT'=>'groupview'));
    }
}

function pagegrouplist()
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
	$sql="SELECT key_linkgroups,login,
		IF(((surname='')AND(name='')AND(patronymic='')),'Не указано',CONCAT(surname,' ',name,' ',patronymic)) AS SNP,
		email,key_users,birthdate,regdate
		FROM linkgroups
		LEFT JOIN users ON id_users=key_users
		LEFT JOIN groups ON id_groups=key_groups
		WHERE (id_groups=$id)";
	makeviewtable($sql,$fields,$tools);
	$tpl->setvar('GROUP',$id);
	$tpl->parse(array('CONTENT'=>'groupviewlist'));
}
?>
