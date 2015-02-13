<?

$tools['people']=array
(
	'privs'=>'m',
	'actions'=>array
	(
		'edit'=>'Редактирование профиля пользователя',
		'delete'=>'Удаление пользователя',
		'main'=>'Редактор пользователей'
	),
	'templates'=>array
	(
		'peoplelist'=>'peoplelist.tpl',
		'peopleedit'=>'peopleedit.tpl'
	)
);

function peoplemain()
{
	global $tpl,$db,$result,$count,$id,$page;

	$fields=array(
		'login'=>array(
			'title'=>'Логин',
			'linkid'=>'key_users',
			'link'=>'?page=profile&id=[]'
		),
		'SNP'=>array(
			'title'=>'ФИО'
		),
		'usertype'=>array(
			'title'=>'Тип'
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
			'linkid'=>'key_users',
			'link'=>"?page=admin&tool=$page&action=delete&id=[]"
		),
		'edit'=>array
		(
			'title'=>'Редактировать',
			'image'=>'edit',
			'linkid'=>'key_users',
			'link'=>"?page=admin&tool=$page&action=edit&id=[]"
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
	$sql="
		SELECT key_users,login,
		email,status,
		name,surname,patronymic,
		IF(((surname='')AND(name='')AND(patronymic='')),'Не указано',CONCAT(surname,' ',name,' ',patronymic)) AS SNP,
		birthdate,regdate,note,
		userprivs.title AS usertype,
		themes.title AS theme
		FROM users
		LEFT JOIN userprivs ON id_userprivs=key_userprivs
		LEFT JOIN themes ON id_themes=key_themes";
	makeviewtable($sql,$fields,$tools);
    $tpl->parse(array('CONTENT'=>'peoplelist'));
}

function peopleedit()
{
	global $tpl,$db,$id,$result,$count,$page,$errors,$gourl;
	$sex=array('Не указанно'=>'Не указанно','Мужской'=>'Мужской','Женский'=>'Женский');
	if(isset($_POST['submit']))
	{

        $sql="SELECT COUNT(key_users) FROM users WHERE key_users=$id";
		$id=checkid($id,$sql);
		if (isset($id))
		{
			$fields['key_users']=$id;
			$fields['name']=tostr('name');
			$fields['surname']=tostr('surname');
			$fields['patronymic']=tostr('patronymic');
			$fields['email']=tostr('email');
			$fields['status']=tostr('status');
			$fields['id_themes']=postnumparam('id_themes');
			$fields['id_userprivs']=postnumparam('id_userprivs');
			$fields['sex']=tostr('sex');
			$fields['note']=tostr('note');
			$fields['birthdate']='\''.postnumparam('year').'-'.
			postnumparam('month').'-'.postnumparam('day').'\'';
			updaterecord('users',$fields);
		}
		else
		{
			$errors[]='Пользователь не найден.';
		}
	}
	else
	{
        $sql="SELECT COUNT(key_users) FROM users WHERE key_users=$id";
		$id=checkid($id,$sql);
		if (isset($id))
		{
			$result=$db->query(
				"SELECT key_users,login,email,id_userprivs,sex,status,
				name,surname,patronymic,birthdate,note,id_themes,
				themes.title AS theme
				FROM users
				LEFT JOIN themes ON id_themes=key_themes
				WHERE key_users=$id");
			$row=$db->getassocrow($result);
		    $tpl->setvars(array(
				'PROFILE_LOGIN'=>$row['login'],
				'PROFILE_EMAIL'=>$row['email'],
				'PROFILE_NAME'=>$row['name'],
				'PROFILE_SURNAME'=>$row['surname'],
				'PROFILE_PATRONYMIC'=>$row['patronymic'],
				'PROFILE_STATUS'=>$row['status'],
				'PROFILE_BIRTHDATE'=>$row['birthdate'],
				'PROFILE_NOTE'=>$row['note']
			));
			$birthdate=split('-',$row['birthdate']);
			$days=array();$months=array();$years=array();
			for($i=1;$i<32;$i++){$days[$i]=$i;}
			for($i=1;$i<13;$i++){$months[$i]=$i;}
			for($i=1900;$i<2000;$i++){$years[$i]=$i;}
			makecombobox($sex,$row['sex'],'SEX');
			makecombobox($days,$birthdate[2],'DAYS');
			makecombobox($months,$birthdate[1],'MONTHS');
			makecombobox($years,$birthdate[0],'YEARS');
			makecombobox("SELECT key_userprivs,title FROM userprivs",$row['id_userprivs'],'USERPRIVS');
			makecombobox("SELECT key_themes,title FROM themes",$row['id_themes'],'THEMES');
			$tpl->parse(array('CONTENT'=>'peopleedit'));
		}
		else
		{
			$errors[]='Пользователь не найден.';
			$gourl="?page=admin&tool=$page";
		}
	}
}

function peopledelete()
{
	global $page;
	profileimagedelete($id,FALSE);
    querydelete('users',"?page=admin&tool=$page",'Ошибка. Пользователь не существует','Пользователь удален.');
}

?>
