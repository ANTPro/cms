<?
@ini_set("magic_quotes_gpc","1");
require_once ('template.php');
require_once ('database.php');
require_once ('functions.php');
require_once ('globals.php');

loadplugins('pages','pages');
checkmode();

if (!$mode)
{
	$theme='default';
	$vuz_title='CMS \'ВУЗ\'';
}
else
{
	require_once ('config.php');
	$db=new TDataBase();
	//$db->debug=TRUE;
	$db->selectdb();
	unset($login);
	getuserinfo();
}

$tpl=new Template($theme);
//$tpl->debugtpl=TRUE;
setmainvars();
$id=getnumparam('id');

$tpl->load(array(
    'main'=>'main.tpl',
    'options'=>'options.tpl'
));

loadpages();
//$tpl->setvar('META','');
if (isset($privs))
{
	if(in_array('a',$privs))
	{
		stats();
	}
}
if (!isset($notmain))
{
	$tpl->parse(array('MAIN'=>'main'));
	$tpl->exec();
}
?>
