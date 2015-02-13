<?

require_once ('template.php');
require_once ('database.php');
require_once ('functions.php');

require_once ('config.php');
$db=new TDataBase();
$db->debug=TRUE;
$db->selectdb();

$tpl=new Template("rss");

$id=getnumparam('id');
//Проверка на правильность ID
if ($id==0)
{
	$result=$db->query('SELECT key_section,title FROM section WHERE parent=0 ORDER BY title LIMIT 1');
	$row=$db->getassocrow($result);
	$id=$row['key_section'];
}

$tpl->load(array(
    'rss'=>'rss.tpl',
    'rssitem'=>'rssitem.tpl'
));

	$result=$db->query("SELECT key_section,title FROM section WHERE key_section=$id");
	$row=$db->getassocrow($result);
    $website='http://'.$GLOBALS['SERVER_NAME'].'/';
	$tpl->setvars(array(
		'SITE_TITLE'=>$vuz_title,
		'SITE_URL'=>$website,
		'RSS_TITLE'=>$row['title'],
		'RSS_URL'=>"$website?page=rss&amp;id=$id",
		'RSS_DESCRIPTION'=>"RSS канал сайта: $website"
	));

$result=$db->query("
	SELECT key_article,title,description,id_users,pubdate,
		users.login as `author`
	FROM article
	LEFT JOIN users ON id_users=key_users
	WHERE id_section=$id ORDER BY pubdate desc");

$count=$db->rowcount($result);
for ($i=0; $i<$count; $i++)
{
	$row=$db->getassocrow($result);

    $tpl->setvars(array(
		'ID'=>$row['key_article'],
		'DESCRIPTION'=>$row['description'],
		'TITLE'=>$row['title'],
		'AUTHOR'=>$row['author'],
		'PUBDATE'=>$row['pubdate'],
		'URL'=>$website.'?page=article&amp;id='.$row['key_article']
	));
	$tpl->varadd('RSSITEMS','RSSITEM','rssitem');
}

$tpl->parse(array('MAIN'=>'rss'));
$tpl->exec();
?>