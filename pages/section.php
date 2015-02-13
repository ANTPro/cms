<?
$pages['section']=array
(
	'title'=>'Разделы',
	'index'=>'',
	'templates'=>array
	(
	    'articlelistitem'=>'articlelistitem.tpl',
	    'metarss'=>'metarss.tpl'
	),
	'installsql'=>"
		CREATE TABLE IF NOT EXISTS `section` (
		  `key_section` bigint(10) NOT NULL auto_increment,
		  `title` varchar(255) NOT NULL default '',
		  `parent` bigint(20) NOT NULL default '0',
		  `level` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`key_section`),
		  UNIQUE KEY `title` (`title`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	",
	'uninstallsql'=>'DROP TABLE IF EXISTS `section`;'
);

function pagesection()
{
	global $tpl,$db,$id;

	$id=checkid($id,"SELECT COUNT(key_section) FROM section WHERE key_section=$id",
		'SELECT key_section,title FROM section WHERE parent=0 ORDER BY title LIMIT 1');

	makearticlelist();

	$result=$db->query("SELECT title FROM section WHERE key_section=$id");
	$row=$db->getassocrow($result);
	$tpl->setvar("PAGE_TITLE",'Раздел: '.check($row['title']));
	$tpl->setvar('MAIN_HEADER',check($row['title']));
	$tpl->setvar('ID',$id);
	$tpl->parse(array('META'=>'metarss'));
}

function makearticlelist()
{
	global $tpl,$db,$id,$result,$count;
	$sql="SELECT key_article,title,description,id_users,pubdate,users.login as `author`
		FROM article
		LEFT JOIN users ON id_users=key_users
		WHERE id_section=$id ORDER BY pubdate desc";
    $error='В этом разделе нет записей.';

    if (safequery($sql,$error,FALSE))
	{
		for ($i=0; $i<$count; $i++)
		{
			$row=$db->getassocrow($result);
			$tpl->setvars(array(
				'ID'=>$row['key_article'],
				'ARTICLE_DESCRIPTION'=>check($row['description']),
				'ARTICLE_TITLE'=>check($row['title']),
				'ARTICLE_AUTHOR'=>check($row['author']),
				'AUTHOR_ID'=>$row['id_users'],
				'ARTICLE_PUBDATE'=>$row['pubdate']
			));
			commentscount('article',$row['key_article']);
			$tpl->varadd('CONTENT','ARTICLELISTITEM','articlelistitem');
		}
	}
}
?>
