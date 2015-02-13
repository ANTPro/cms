<?

$pages['profile']=array
(
	'title'=>'Профиль',
	'menu'=>array(
		'title'=>'Профиль'
	),
	'templates'=>array
	(
		'profileview'=>'profileview.tpl',
		'profileedit'=>'profileedit.tpl',
		'profilepass'=>'profilepass.tpl',
		'profileimage'=>'profileimage.tpl',
		'profileexstudent'=>'profileexstudent.tpl',
		'profileexteacher'=>'profileexteacher.tpl',
		'profileitemdepartment'=>'profileitemdepartment.tpl',
		'profileitemgroup'=>'profileitemgroup.tpl'
	),
	'privs'=>'u'
);

function pageprofile()
{
	global $tpl,$db,$result,$count,$page,$userid,$id,$gourl;

    $sql="SELECT COUNT(key_users) FROM users WHERE key_users=$id";
	$id=checkid($id,$sql);
	if (!isset($id))
	{
		$id=$userid;
	}

	$result=$db->query("SELECT login FROM users WHERE key_users=$userid");
	$row=$db->getassocrow($result);
	$login=$row['login'];

	if ($id==$userid)
	{
		$login='Ваш профиль';
	}
	else
	{
		$login="Профиль пользователя $login";
	}
	$tpl->setvars(array(
		'PAGE_TITLE'=>$login,
		'MAIN_HEADER'=>$login
	));
	$profilepage='profileview';
   	if ($id==$userid)
   	{
   		$profilepages=array('view','edit','pass','imageload','imagedelete');
        foreach($profilepages AS $pp)
		{
			if (isset($_GET[$pp]))
			{
				$page='';
				$profilepage='profile'.$pp;
			}
		}
    }
	$profilepage();
}

function profileview()
{
	global $tpl,$db,$page,$userid,$id,$result,$count,$gourl,$theme;
	$tpl->load(array(
		'profileview'=>'profileview.tpl',
		'profileexstudent'=>'profileexstudent.tpl',
		'profileexteacher'=>'profileexteacher.tpl',
		'profileitemdepartment'=>'profileitemdepartment.tpl',
		'profileitemgroup'=>'profileitemgroup.tpl'
	));

	$sex=array('Не указано'=>'Не указано','Мужской'=>'Мужской','Женский'=>'Женский');
	$result=$db->query("
		SELECT key_users,login,sex,image,status,
		email,name,surname,patronymic,birthdate,regdate,note,
		userprivs.title AS usertype,
		themes.title AS theme
		FROM users
		LEFT JOIN userprivs ON id_userprivs=key_userprivs
		LEFT JOIN themes ON id_themes=key_themes
		WHERE key_users=$id LIMIT 1");
	$row=$db->getassocrow($result);
    $tpl->setvars(array(
    	'ID'=>$row['key_users'],
		'PROFILE_LOGIN'=>$row['login'],
		'PROFILE_EMAIL'=>$row['email'],
		'PROFILE_NAME'=>$row['name'],
		'PROFILE_SURNAME'=>$row['surname'],
		'PROFILE_PATRONYMIC'=>$row['patronymic'],
		'PROFILE_STATUS'=>$row['status'],
		'PROFILE_THEME'=>$row['theme'],
		'PROFILE_SEX'=>$row['sex'],
		'PROFILE_USERTYPE'=>$row['usertype'],
		'PROFILE_BIRTHDATE'=>$row['birthdate'],
		'PROFILE_REGDATE'=>$row['regdate'],
		'PROFILE_NOTE'=>$row['note']
	));
	commentscount('profile',$row['key_users']);
	$url="./usersimages/middle/".$row['image'];
	if (!file_exists($url)||($row['image']==''))
	{
		$url="./templates/$theme/images/nophotomiddle.png";
		$tpl->vardelete('PROFILE_IMAGEDELETE');
	}
	else
	{
		$tpl->setvar('PROFILE_IMAGEDELETE','');
	}
	imageview($url,'center',$row['login'],1);
	$sql="
		SELECT key_linkgroups,year,id_groups,id_department,
		specialization.title AS specialization,
		department.title AS department,
		faculty.title AS faculty,
		groups.title AS groups
		FROM linkgroups
		LEFT JOIN groups ON id_groups = key_groups
		LEFT JOIN specialization ON id_specialization = key_specialization
		LEFT JOIN department ON id_department = key_department
		LEFT JOIN faculty ON id_faculty = key_faculty
		WHERE id_users=$id";
	if(safequery($sql))
	{
		for($i=0;$i<$count;$i++)
		{
			$row=$db->getassocrow($result);
		    $tpl->setvars(array(
		    	'GROUPSID'=>$row['id_groups'],
		    	'DEPARTMENTID'=>$row['id_department'],
				'GROUP'=>$row['groups'],
				'FACULTY'=>$row['faculty'],
				'DEPARTMENT'=>$row['department'],
				'SPECIALIZATION'=>$row['specialization'],
				'YEAR'=>$row['year']
			));
			$tpl->varadd('TABLE','ITEMGROUP','profileitemgroup');
		}
		$tpl->parse(array('EXTENDEDINFO'=>'profileexstudent'));
	}
	$tpl->vardelete('TABLE');
	$sql="
		SELECT key_linkdepartment,id_department,
			department.title AS department,
			faculty.title AS faculty
		FROM linkdepartment
        LEFT JOIN department ON id_department = key_department
		LEFT JOIN faculty ON id_faculty = key_faculty
		WHERE id_users=$id";
	if(safequery($sql))
	{
		for($i=0;$i<$count;$i++)
		{
			$row=$db->getassocrow($result);
		    $tpl->setvars(array(
		    	'DEPARTMENTID'=>$row['id_department'],
				'FACULTY'=>$row['faculty'],
				'DEPARTMENT'=>$row['department']
			));
			$tpl->varadd('TABLE','ITEMDEPART','profileitemdepartment');
		}
		$tpl->varadd('EXTENDEDINFO','EXINFO','profileexteacher');
	}

	if(($id==$userid)&&(!isset($_GET['print'])))
	{
		$tpl->setvar('PROFILE_EDIT','');
	}
	else
	{
		$page='';
		$tpl->vardelete('PROFILE_EDIT');
	}
	$tpl->parse(array('CONTENT'=>'profileview'));
}

function profileedit()
{
	global $tpl,$db,$userid,$gourl;

	$sex=array('Не указано'=>'Не указано','Мужской'=>'Мужской','Женский'=>'Женский');
	if(isset($_POST['submit']))
	{
		$fields['key_users']=$userid;
		$fields['name']=tostr('name');
		$fields['email']=tostr('email');
		$fields['surname']=tostr('surname');
		$fields['patronymic']=tostr('patronymic');
		$fields['id_themes']=postnumparam('id_themes');
		$fields['sex']=tostr('sex');
		$fields['note']=tostr('note');
		$fields['birthdate']='\''.postnumparam('year').'-'.
		postnumparam('month').'-'.postnumparam('day').'\'';
		updaterecord('users',$fields);
		$gourl="?page=profile";
	}
	else
	{
		$result=$db->query(
			"SELECT key_users,login,email,sex,
				name,surname,patronymic,birthdate,note,id_themes,
				themes.title AS theme
			FROM users
			LEFT JOIN themes ON id_themes=key_themes
			WHERE key_users=$userid");
		$row=$db->getassocrow($result);
		$tpl->setvars(array(
			'PROFILE_LOGIN'=>$row['login'],
			'PROFILE_EMAIL'=>$row['email'],
			'PROFILE_NAME'=>$row['name'],
			'PROFILE_SURNAME'=>$row['surname'],
			'PROFILE_PATRONYMIC'=>$row['patronymic'],
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
		makecombobox("SELECT key_themes,title FROM themes",$row['id_themes'],'THEMES');
		$tpl->parse(array('CONTENT'=>'profileedit'));
	}
}

function profilepass()
{
	global $tpl,$db,$userid,$infos,$errors,$gourl,$selfpage;
	$tpl->load(array(
		'profilepass'=>'profilepass.tpl'
	));
	if(isset($_POST['submit']))
	{
	    $result = $db->query("SELECT key_users, password FROM users WHERE key_users=$userid");
	    $row = $db->getassocrow($result);
	    if($row['password'] === md5(md5($_POST['oldpassword'])))
	    {
		    $fields['key_users']=$userid;
			$fields['password']='\''.md5(md5($_POST['newpassword'])).'\'';
			if($db->execupdate('users',$fields))
			{
				$gourl='?page=profile';
				$infos[]='Пароль изменен успешно.';
			}
			else
			{
				$gourl=$selfpage;
				$errors[]='Ошибка. При смене пароля возникла ошибка.';
			}
		}
		else
		{
			$gourl=$selfpage;
			$errors[]='Ошибка. Неверно введен старый пароль.';
		}
	}
	else
	    $tpl->parse(array('CONTENT'=>'profilepass'));
}
# Создание именьшеной копии изображения
# $srcurl - исходная картинка
# $desturl - путь для сохранения превьюшки
# $type - тип исходной картинки
# $size - максимальный размер превьюшки
function imagemakepreview($srcurl,$desturl,$type,$size)
{
	switch ($type)
	{
		case 'image/gif':
		$from = imageCreateFromGif($srcurl);
		break;
		case 'image/jpeg':
		$from = imageCreateFromJpeg($srcurl);
		break;
		case 'image/png':
		$from = imageCreateFromPng($srcurl);
		break;
	}
	$max=max(imageSX($from),imageSY($from));
	$k=($size/$max);
	$x=imageSX($from)*$k;$y=imageSY($from)*$k;
	$to = imageCreateTrueColor($x, $y);
	imageCopyResampled($to, $from, 0, 0, 0, 0,
		imageSX($to), imageSY($to), imageSX($from), imageSY($from));
	imageJpeg($to, $desturl);
}

function profileimageload()
{
	global $tpl,$db,$login,$userid,$errors,$infos,$gourl,$selfpage;
	$tpl->load(array(
		'profileimage'=>'profileimage.tpl'
	));

	if(isset($_POST['submit']))
	{
		$dir='usersimages';
		if ($_FILES['foto']['name']!='')
		{
	        $tmpfiletype=$_FILES['foto']['type'];
	        if ($tmpfiletype!='')
	        {
	        	if (!in_array($tmpfiletype,array('image/jpeg','image/gif','image/png')))
				{
					$gourl=$selfpage;
					$errors[]='Разрешено присылать только файлы форматов jpeg, gif и png';
				}
				else
				{
					$name = $login;
					if (ereg ("\.([[:alnum:]]+)$", $_FILES['foto']['name'], $regs))
					{
						$name = $login.$regs[0];
					}
					$fname = "./$dir/full/".$name;
					imagedelete($userid,FALSE);
					copy($_FILES['foto']['tmp_name'], $fname);

                    imagemakepreview($fname,"./$dir/small/".$name,$tmpfiletype,70);
                    imagemakepreview($fname,"./$dir/middle/".$name,$tmpfiletype,200);

					$fields['key_users']=$userid;
					$fields['image']="'$name'";
					if($db->execupdate('users',$fields))
					{
						$gourl='?page=profile';
						$infos[]='Загрузка завершена успешно.';
					}
					else
					{
						$gourl=$selfpage;
						$errors[]='Ошибка. При загрузке возникла ошибка.';
					}
				}

			}
			else
				$errors[]='Ошибка. Файл не может быть обработан.';
		}
		else
			$errors[]='Ошибка. Файл не может быть загружен.';
	}
	else
		$tpl->parse(array('CONTENT'=>'profileimage'));
}

function profileimagedelete($userid,$showmsg=TRUE)
{
	global $userid;
	imagedelete($userid,TRUE);
}

function imagedelete($userid,$showmsg=TRUE)
{
	global $tpl,$db,$result,$errors,$infos,$gourl,$selfpage;

	$sql="SELECT image FROM users WHERE key_users=$userid";
	if (safequery($sql))
	{
		$image=$db->result($result,0);
		if($image!='')
		{
			if(file_exists("./usersimages/full/$image"))
			{
				unlink("./usersimages/full/$image");
			}
			if(file_exists("./usersimages/middle/$image"))
			{
				unlink("./usersimages/middle/$image");
			}
			if(file_exists("./usersimages/small/$image"))
			{
				unlink("./usersimages/small/$image");
			}
		}
	}
	$fields['key_users']=$userid;
	$fields['image']="''";
	if($db->execupdate('users',$fields))
	{
		if($showmsg)
		{
			$gourl='?page=profile';
			$infos[]='Удалено.';
		}
		return TRUE;
	}
	else
	{
		if($showmsg)
		{
			$gourl=$selfpage;
			$errors[]='Ошибка. При удалении возникла ошибка.';
		}
		return FALSE;
	}
}
?>
