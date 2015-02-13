<?
$pages['login']=array
(
	'title'=>'Авторизация',
	'menu'=>array(
		'title'=>'Вход'
	),
	'templates'=>array
	(
	    'loginform'=>'loginform.tpl'
	),
	'notprivs'=>array('u'),
	'notuninstall'=>''
);

# Функция для генерации случайной строки
function generateCode($length=6)
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	$code = "";
	$clen = strlen($chars) - 1;
	while (strlen($code) < $length)
	{
		$code .= $chars[mt_rand(0,$clen)];
	}
	return $code;
}

function pagelogin()
{
	global $tpl,$db,$id,$page,$errors,$infos,$gourl,$selfpage;

	if(isset($_POST['submit']))
	{
		$login=$db->escstr($_POST['login']);
	    # Вытаскиваем из БД запись, у которой логин равнятся введенному
	    $result = $db->query("SELECT key_users, password FROM users WHERE login='$login' LIMIT 1");
	    $data = $db->getassocrow($result);
		//echo $data['password'].$data['password'].'==='.md5(md5($_POST['password']));
	    # Соавниваем пароли
	    if($data['password'] === md5(md5($_POST['password'])))
	    {
	        # Генерируем случайное число и шифруем его
	        $hash = md5(generateCode(10));

	        # Записываем в БД новый хеш авторизации
	        $db->query("UPDATE users SET hash='$hash' WHERE key_users='".$data['key_users']."'");
	        # Ставим куки на 30 дней
	        setcookie("userid", $data['key_users'], time()+60*60*24*30);
	        setcookie("hash", $hash, time()+60*60*24*30);

	        # Переадресовываем браузер на страницу проверки нашего скрипта
	        header("Location:?page=index");exit();
	    }
	    else
	    {
	    	$gourl=$selfpage;
	        $errors[]='Вы ввели неправильный логин или пароль.';
	    }
	}
	else
		$tpl->parse(array('CONTENT'=>'loginform'));
}
?>
