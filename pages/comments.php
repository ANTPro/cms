<?
$pages['comments']=array
(
	'title'=>'Комментарии к записям из раздела',
	'notindex'=>'',
	'templates'=>array
	(
		'commentlist'=>'commentlist.tpl',
		'commentitem'=>'commentitem.tpl'
	)
);

function commentspage($page,$id)
{
	global $db,$result;	switch ($page)
	{
		case 'article':
		$sql="SELECT COUNT(key_article)FROM article WHERE key_article=$id";
		$id=checkid($id,$sql);
		if (isset($id))
		{			$where="WHERE (page='article')AND(`key`=$id)";
			$sql="SELECT title,description FROM article WHERE key_article=$id";
			safequery($sql);
			$row=$db->getassocrow($result);
			$title='Обсуждение статьи: '.check($row['title']);
			$description=check($row['description']);
			return array('title'=>$title,'description'=>$description,'where'=>$where);		}
		break;
		case 'profile':
		$sql="SELECT COUNT(key_users)FROM users WHERE key_users=$id";
		$id=checkid($id,$sql);
		if (isset($id))
		{			$where="WHERE (page='profile')AND(`key`=$id)";
			$sql="SELECT login,note,
			IF(((surname='')AND(name='')AND(patronymic='')),'Не указано',CONCAT(surname,' ',name,' ',patronymic)) AS SNP
			FROM users WHERE key_users=$id";
			safequery($sql);
			$row=$db->getassocrow($result);
			$title='Обсуждение пользователя: '.$row['login'].' (ФИО - '.$row['SNP'].')';
			$description=check($row['note']);
			return array('title'=>$title,'description'=>$description,'where'=>$where);		}
		break;
	}
}
function pagecomments()
{
	global $tpl,$db,$id,$result,$count,$userid,$theme,$gourl,$selfpage;
	$prop=array();$page='';
    if (isset($_GET['article']))
    {
    	$page='article';    }
    if (isset($_GET['profile']))
    {
    	$page='profile';
    }
	$prop=commentspage($page,$id);
	if (!isset($prop['where']))
	{		header("Location:?page=404");exit();
	}
    if (isset($_POST['submit']))
    {
		$fields=array();
		$fields['key_messages']='NULL';
		$fields['message']=tostr('comment');
		$fields['author']=$userid;
		insertrecord('messages',$fields,FALSE);

		$sql="SELECT MAX(key_messages) FROM messages";
		$result=$db->query($sql);
		$message=$db->result($result,0);
		$fields=array();
    	$fields['key_comments']='NULL';
    	$fields['page']="'$page'";
    	$fields['`key`']=$id;
    	$fields['id_messages']=$message;
		insertrecord('comments',$fields);
		$gourl=$selfpage;    }
    else
    {
		$sql="
			SELECT key_comments, page, `key`,
				users.login as `login`, author, message, date, image
			FROM comments
			LEFT JOIN messages ON id_messages=key_messages
			LEFT JOIN users ON author=key_users ".$prop['where'];
	    $error='Здесь еще никто не оставил комментарий.';
	    if (safequery($sql,$error,FALSE))
		{
			for ($i=0; $i<$count; $i++)
			{
				$row=$db->getassocrow($result);
				$url="./usersimages/small/".$row['image'];
				if (!file_exists($url)||($row['image']==''))
				{
					$url="./templates/$theme/images/nophotosmall.png";
					$tpl->vardelete('PROFILE_IMAGEDELETE');
				}
				else
				{
					$tpl->setvar('PROFILE_IMAGEDELETE','');
				}
				imageview($url,'center',$row['login'],1);
				$tpl->setvars(array(
					'ID'=>$row['key_comments'],
					'COMMENT'=>check($row['message']),
					'COMMENT_AUTHOR'=>check($row['login']),
					'COMMENT_DATE'=>check($row['date']),
					'AUTHOR_ID'=>$row['author'],
					'COMMENT_PUBDATE'=>$row['date']
				));
				$tpl->varadd('COMMENTS','COMMENTITEM','commentitem');
			}
		}
		$tpl->setvar('DESCRIPTION',$prop['description']);
		$tpl->parse(array('CONTENT'=>'commentlist'));
    }
	$tpl->setvars(array(
		'PAGE_TITLE'=>$prop['title'],
		'MAIN_HEADER'=>$prop['title'],

	));
}

?>