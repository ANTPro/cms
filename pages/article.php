<?
$pages['article']=array
(
	'title'=>'Просмотр статей',
	'templates'=>array
	(
	    'articleview'=>'articleview.tpl'
	),
	'installsql'=>"
		CREATE TABLE IF NOT EXISTS `article` (
		  `key_article` bigint(20) NOT NULL auto_increment,
		  `id_section` bigint(20) NOT NULL default '0',
		  `title` varchar(255) NOT NULL default '',
		  `content` text NOT NULL,
		  `description` varchar(255) NOT NULL default '',
		  `id_users` bigint(10) NOT NULL default '0',
		  `pubdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`key_article`),
		  UNIQUE KEY `title` (`title`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>'DROP TABLE IF EXISTS `article`;'
);

function pagearticle()
{
	global $tpl,$db,$id,$result,$count;

	$sql="SELECT key_article,content,title,id_users,pubdate,id_section,
	users.login as author
	FROM article
	LEFT JOIN users ON id_users=key_users
	WHERE key_article=$id";
    $error="Статья не найдена.";
 	if (safequery($sql,$error,FALSE))
	{		$row=$db->getassocrow($result);
		$tpl->setvars(array(
			'ID'=>$row['key_article'],
			'ARTICLE_CONTENT'=>check($row['content']),
			'ARTICLE_TITLE'=>check($row['title']),
			'ARTICLE_AUTHOR'=>check($row['author']),
			'AUTHOR_ID'=>$row['id_users'],
			'ARTICLE_PUBDATE'=>$row['pubdate']
		));
		commentscount('article',$row['key_article']);
		$tpl->parse(array('CONTENT'=>'articleview'));	}
	else
	{		header("Location:?page=404");exit();	}
	$result=$db->query("SELECT key_article,title FROM article WHERE key_article=$id");
	$row=$db->getassocrow($result);
	$tpl->setvar('PAGE_TITLE',check($row['title']));
	$tpl->setvar('MAIN_HEADER',check($row['title']));
}
?>
