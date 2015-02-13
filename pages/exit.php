<?
$pages['exit']=array
(
	'title'=>'Выход',
	'menu'=>array(
		'title'=>'Выход'
	),
	'privs'=>'u',
	'notindex'=>'',
	'notuninstall'=>''
);

function pageexit()
{
	setcookie('userid',time()-3600);
	setcookie('hash',time()-3600);
	header("Location:?page=index");exit();
}
?>
