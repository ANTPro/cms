<?
$pages['registration']=array
(
	'title'=>'Регистрация нового пользователя',
	'menu'=>array(
		'title'=>'Регистрация'
	),
	'templates'=>array
	(
		'registrationform'=>'registrationform.tpl'
	),
	'notprivs'=>array('u')
);

function registeruser($login,$password)
{
	global $db,$errors,$infos,$gourl,$selfpage;
	$sql="SELECT key_themes FROM themes LIMIT 1";
	$result=$db->query($sql);
	$theme=$db->result($result,0);

	$fields['key_users']='NULL';
	$fields['login']="'".$login."'";
	$fields['password']="'".$password."'";
	$fields['id_themes']=$theme;
	if($db->execinsert('users',$fields))
	{
		$gourl='?page=login';
		$infos[]='Регистрация прошла успешно. Сейчас вы будете перемещены на страницу авторизации, где введите свой логин и пароль.';
	}
	else
	{
		$gourl=$selfpage;
		$errors[]='Ошибка. При регистрации возникла ошибка. Проверьте введенные данные.';
	}
}

function pageregistration()
{
	global $tpl,$db,$id,$errors,$infos,$gourl,$selfpage;

	if(isset($_POST['submit']))
	{
		$login=trim($_POST['login']);
	    # проверям логин
	    if(!preg_match("/^[a-zA-Z0-9]+$/",$login))
	    {
	        $errors[] = "Ошибка. Логин может состоять только из букв английского алфавита и цифр.";
	    }

	    if(strlen($login) < 3 or strlen($login) > 30)
	    {
	        $errors[] = "Ошибка. Логин должен быть не меньше 3-х символов и не больше 30.";
	    }

	    if(strlen($_POST['password']) < 3)
	    {
	        $errors[] = "Ошибка. Пароль должен быть не меньше 3-х символов.";
	    }

	    # проверяем, не сущестует ли пользователя с таким именем
	    $result = $db->query("SELECT COUNT(key_users) FROM users WHERE login='".$db->escstr($login)."'");
	    if($db->result($result, 0) > 0)
	    {
	        $errors[] = "Ошибка. Пользователь с таким логином уже существует в базе данных.";
	    }

	    # Если нет ошибок, то добавляем в БД нового пользователя
	    if(count($errors) == 0)
	    {
	        # Убераем лишние пробелы и делаем двойное шифрование
	        $password = md5(md5(trim($_POST['password'])));
	        registeruser($login,$password);
	    }
	    else
	    {
	        $gourl=$selfpage;
	    }
	}
	else
	{
		$tpl->parse(array('CONTENT'=>'registrationform'));
	}
}
?>
