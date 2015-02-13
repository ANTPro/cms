<?
$pages['mail']=array
(
	'title'=>'Почта',
	'menu'=>array(
		'title'=>'Почта'
	),
	'templates'=>array
	(
		'mail'=>'mail.tpl',
		'maillist'=>'maillist.tpl',
		'mailwrite'=>'mailwrite.tpl',
		'mailview'=>'mailview.tpl'
	),
	'privs'=>'u',
	'installsql'=>"
		CREATE TABLE IF NOT EXISTS `messages` (
		  `key_messages` bigint(10) NOT NULL auto_increment,
		  `author` bigint(10) NOT NULL default '0',
		  `message` text NOT NULL,
		  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`key_messages`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		CREATE TABLE IF NOT EXISTS `mail` (
		  `key_mail` bigint(10) NOT NULL auto_increment,
		  `foruser` bigint(10) NOT NULL default '0',
		  `id_messages` bigint(10) NOT NULL default '0',
		  `isnew` enum('нет','да') NOT NULL default 'да',
		  `title` varchar(255) NOT NULL default '',
		  PRIMARY KEY  (`key_mail`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>
	"
		DROP TABLE IF EXISTS `messages`;
		DROP TABLE IF EXISTS `mail`;
	"
);

function pagemail()
{
    global $db,$tpl,$page,$userid,$id;

	$result=$db->query("SELECT key_users,login FROM users WHERE key_users=$userid");
	$row=$db->getassocrow($result);
	$tpl->setvar("PAGE_TITLE","Почта пользователя ".$row['login']);
	$tpl->setvar('MAIN_HEADER',"Почта пользователя ".$row['login']);

	if (isset($_GET['type']))
	{
		$type=$_GET['type'];
		if (!in_array($type,array('in','out','write')))
		{
			unset($type);
		}
	}
	$sql="
		SELECT COUNT(key_mail)
		FROM mail
		LEFT JOIN messages ON id_messages=key_messages
		WHERE (key_mail=$id)AND((author=$userid)OR(foruser=$userid))";
	$id=checkid($id,$sql);
	if (isset($type))
	{
		$page='';
		if ($type=='write')
		{
	       	makewritemail();
		}
		else
		{
			makemaillist();
		}
	}
	else
	{
		if(!isset($id))
		{
			makemail();
		}
		else
		{
			$page='';
			makemailview();
		}
	}
	/*
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
	*/
}

function setcount($sql,$varname)
{
	global $db,$tpl;
	$result=$db->query($sql);
	$count=$db->result($result, 0);
	$tpl->setvar($varname,$count);
}

function makemailview()
{
	global $db,$tpl,$userid,$login,$result,$id;

	$sql="
		SELECT key_mail,author,foruser,isnew,title,date,message,
			IF (isnew='да','нет','да') AS `isread`,
			usersforuser.login AS `foruserlogin`,
			usersauthor.login AS `authorlogin`
		FROM mail
		LEFT JOIN messages ON id_messages=key_messages
		LEFT JOIN users AS usersforuser ON foruser=usersforuser.key_users
		LEFT JOIN users AS usersauthor ON author=usersauthor.key_users
		WHERE (key_mail=$id)AND
			((author=$userid)OR(foruser=$userid))";
    $error="Ошибка такое сообщение отсутсвует на сервере.";
 	if (safequery($sql,$error))
	{
		$row=$db->getassocrow($result);
		if($row['authorlogin']==$login)
		{			$user='foruser';
			$type='для пользователя';
		}
		else
		{			$user='author';
			$type='от пользователя';
		}

		$tpl->setvars(array(
			'MAIN_HEADER'=>"Сообщение $type: ".$row[$user.'login'],
			'PAGE_TITLE'=>"Сообщение $type: ".$row[$user.'login'],
			'ID'=>$id,
			'USER'=>$user,
			'TITLE'=>$row['title'],
			'MESSAGE'=>$row['message'],
			'DATE'=>$row['date']
		));
		$db->query("UPDATE mail SET isnew='нет' WHERE (foruser=$userid)AND(key_mail=$id)");
		$tpl->parse(array('CONTENT'=>'mailview'));
	}
}
function makemail()
{
	global $db,$tpl,$userid;

    setcount("SELECT COUNT(key_mail) FROM mail WHERE foruser=$userid",'COUNTIN');
    setcount("
    SELECT COUNT(key_messages)
    	FROM mail
		LEFT JOIN messages ON id_messages=key_messages
		WHERE (author=$userid)AND(id_messages=key_messages)",'COUNTOUT');

    setcount("SELECT COUNT(key_mail) FROM mail WHERE (foruser=$userid)AND(isnew='да')",'COUNTNEWIN');
    setcount("SELECT COUNT(key_mail) FROM mail LEFT JOIN messages ON id_messages=key_messages
    	WHERE (author=$userid)AND(id_messages=key_messages)AND(isnew='да')",'COUNTNEWOUT');

	$tpl->parse(array('CONTENT'=>'mail'));
}

function makemaillist()
{
	global $db,$tpl,$userid,$type;
	switch ($type)
	{		case 'in':
		$sql="
			SELECT key_mail,author,foruser,isnew,title,date,
			IF (isnew='да','нет','да') AS `isread`,
			usersforuser.login AS `foruserlogin`,
			usersauthor.login AS `authorlogin`
			FROM mail
			LEFT JOIN messages ON id_messages=key_messages
			LEFT JOIN users AS usersforuser ON foruser=usersforuser.key_users
			LEFT JOIN users AS usersauthor ON author=usersauthor.key_users
			WHERE foruser=$userid";
		$user='author';
		$type='От пользователя';
		$header='Входящие сообщения:';
		break;
		case 'out':
		$sql="
			SELECT key_mail,author,foruser,isnew,title,date,
			IF (isnew='да','нет','да') AS `isread`,
			usersforuser.login AS `foruserlogin`,
			usersauthor.login AS `authorlogin`
			FROM mail
			LEFT JOIN messages ON id_messages=key_messages
			LEFT JOIN users AS usersforuser ON foruser=usersforuser.key_users
			LEFT JOIN users AS usersauthor ON author=usersauthor.key_users
			WHERE author=$userid";
		$user='foruser';
		$type='Для пользователя';
		$header='Исходящие сообщения:';
		break;
	}

	$fields=array(
		$user.'login'=>array(
			'title'=>$type,
			'linkid'=>$user,
			'link'=>'?page=profile&id=[]'
		),
		'title'=>array(
			'title'=>'Тема',
			'linkid'=>'key_mail',
			'link'=>'?page=mail&id=[]'
		),
		'date'=>array(
			'title'=>'Дата'
		),
		'isread'=>array(
			'title'=>'Прочитано'
		)
	);
	makeviewtable($sql,$fields);
	$tpl->parse(array('CONTENT'=>'maillist'));
}

function sendmailtouser($sendto,$errmsg=TRUE)
{
	global $db,$tpl,$userid,$type,$errors,$gourl;    $sql="SELECT key_users FROM users WHERE (login=$sendto)AND NOT (key_users=$userid) LIMIT 1";
	$result=$db->query($sql);
	$msg=TRUE;
	if ($result)
	{
		$count=$db->rowcount($result);
		if ($count==1)
		{
			$row=$db->getassocrow($result);
			$user=$row['key_users'];

            $sql="SELECT MAX(key_messages) FROM messages";
            $result=$db->query($sql);
            $message=$db->result($result,0);

			$fields=array();
            $fields['key_mail']='NULL';
			$fields['title']=tostr('title');
			$fields['foruser']=$user;
			$fields['id_messages']=$message;
			insertrecord('mail',$fields,$errmsg);
			$msg=FALSE;
		}
	}

	if($errmsg&&$msg)
	{		$errors[]="Указанно неверное имя пользователя: $sendto";	}}

function makewritemail()
{
	global $db,$tpl,$userid,$type,$errors,$infos,$result,$count,$gourl,$selfpage;
	$tpl->setvar('MAIN_HEADER','Новое сообщение');


	if(isset($_POST['submit']))
	{
		$sendto=tostr('sendto');
        $groupid=postnumparam('group');

		$fields=array();
		$fields['key_messages']='NULL';
		$fields['message']=tostr('message');
		$fields['author']=$userid;
		insertrecord('messages',$fields,FALSE);

        if ($groupid==1)
        {
			sendmailtouser($sendto,TRUE);
        }
        else
			if ($groupid==2)
			{
				$sql="SELECT key_groups,title FROM groups WHERE (title=$sendto) LIMIT 1";
				$error="Указанно неверное имя группы: $sendto";
				if (safequery($sql,$error))
				{					$row=$db->getassocrow($result);
					$group=$row['key_groups'];
					$sql="
						SELECT key_groups,users.login
						FROM linkgroups
						LEFT JOIN groups ON id_groups = key_groups
						LEFT JOIN users ON id_users = key_users
						WHERE id_groups=$group";

					if (safequery($sql,$error))
					{
						for ($i=0; $i<$db->rowcount($result); $i++)
						{
							$row=$db->getassocrow($result);
							sendmailtouser("'".$row['login']."'",FALSE);
						}
					}
				}
			}
        if (count($errors)!=0)
        {
        	$gourl=$selfpage;
			$errors=array('При отправке собщения возникла неустранимая ошибка.');
		}
        else
		{
			$gourl='?page=mail';
			$infos=array('Сообщение отправлено.');
		}
	}
    else
    {
        $id=getnumparam('user');
    	$sql="SELECT COUNT(key_users) FROM users WHERE key_users=$id";
    	$id=checkid($id,$sql);
    	if (isset($id))
    	{            $sql="SELECT login FROM users WHERE key_users=$id";
			$result=$db->query($sql);
			if($result)
			{
				$count=$db->rowcount($result);
				if ($count>0)
				{
					$login=$db->result($result,0);
					$tpl->setvar('SENDTO',$login);
				}
			}
		}
		$tpl->parse(array('CONTENT'=>'mailwrite'));
	}
}
?>