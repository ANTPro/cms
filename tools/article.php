<?

$tools['article']=array
(
	'privs'=>'e',
	'actions'=>array
	(
		'add'=>'Добавление новой статьи',
		'edit'=>'Редактирование статьи',
		'delete'=>'Удаление статьи',
		'main'=>'Редактор статей'
	),
	'templates'=>array
	(
	    'articlelist'=>'articlelist.tpl',
	    'articleadd'=>'articleadd.tpl',
	    'articleedit'=>'articleedit.tpl',
	    'editor'=>'editor.tpl'
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

function articlemain()
{
 	global $tpl,$db,$userid,$result,$count,$page;

	$fields=array(
		'arttitle'=>array(
			'title'=>'Заголовок',
			'linkid'=>'key_article',
			'link'=>'?page=article&id=[]'
		),
		'section'=>array(
			'title'=>'Раздел',
			'linkid'=>'id_section',
			'link'=>'?page=section&id=[]'
		),
		'author'=>array(
			'title'=>'Автор',
			'linkid'=>'id_users',
			'link'=>'?page=profile&id=[]'
		),
		'pubdate'=>array(
			'title'=>'Дата'
		)
	);
	$tools=array
	(
		'delete'=>array
		(
			'title'=>'Удалить',
			'image'=>'delete',
			'linkid'=>'key_article',
			'link'=>"?page=admin&tool=$page&action=delete&id=[]"
		),
		'edit'=>array
		(
			'title'=>'Редактировать',
			'image'=>'edit',
			'linkid'=>'key_article',
			'link'=>"?page=admin&tool=$page&action=edit&id=[]"
		),
		'view'=>array
		(
			'title'=>'Просмотр',
			'image'=>'browse',
			'linkid'=>'key_article',
			'link'=>'?page=article&id=[]'
		)
	);

 	$sql="SELECT key_article,article.title AS arttitle,section.title as section,
 	users.login as author,pubdate,id_users,id_section
	FROM article
	LEFT JOIN users ON id_users=key_users
	LEFT JOIN section ON id_section=key_section";
	makeviewtable($sql,$fields,$tools);
    $tpl->parse(array('CONTENT'=>'articlelist'));
}

function articleadd()
{
 	global $tpl,$db,$result,$login,$userid;

 	if (isset($_POST['submit']))
 	{
    	$fields['key_article']='NULL';
    	$fields['title']=tostr('title');
    	$fields['content']='\''.$db->escstr(poststrparam('content')).'\'';
    	$fields['description']=tostr('description');
    	$fields['id_users']=$userid;
    	$fields['id_section']=tostr('id_section');
		insertrecord('article',$fields);
 	}
 	else
 	{
	 	$sql='SELECT key_article,article.title,id_users,
	 	pubdate,id_section,description,content,users.login as `author`
	 	FROM article
	 	LEFT JOIN users ON id_users=key_users';
	 	$error='Статья не найдена.';
	 	if (safequery($sql,$error,FALSE))
		{
			$row=$db->getassocrow($result);
			$tpl->setvar('AUTHOR',$login);
			makesectioncombobox(-1);

			$tpl->parse(array('EDITOR'=>'editor','CONTENT'=>'articleadd'));
		}
	}
}

function articleedit()
{
 	global $tpl,$db,$userid,$id,$result,$count;

 	if (isset($_POST['submit']))
 	{
    	$fields['key_article']=$id;
    	$fields['title']=tostr('title');
    	$fields['content']='\''.$db->escstr(poststrparam('content')).'\'';
    	$fields['description']=tostr('description');
    	$fields['id_users']=$userid;
    	$fields['id_section']=tostr('id_section');
		updaterecord('article',$fields);
 	}
 	else
 	{
	 	$sql="SELECT key_article,article.title,id_users,
	 	pubdate,id_section,description,content,users.login as `author`
	 	FROM article
	 	LEFT JOIN users ON id_users=key_users
	 	WHERE key_article=$id";
	 	$error="Ошибка. Статья не найдена.";
	 	if (safequery($sql,$error))
	 	{
			$row=$db->getassocrow($result);
			$tpl->setvars(array(
			    'ID'=>$id,
				'DESCRIPTION'=>check($row['description']),
				'EDITOR_TEXT'=>check($row['content']),
				'AUTHOR'=>check($row['author']),
				'TITLE'=>check($row['title']),
				'PUBDATE'=>$row['pubdate']
			));
			makesectioncombobox($row['id_section']);
			$tpl->parse(array('EDITOR'=>'editor','CONTENT'=>'articleedit'));
		}
	}
}

function articledelete()
{
	global $page;
    querydelete('article',"?page=admin&tool=$page",'Ошибка. Статья не существует','Статья удалена.');
}

?>