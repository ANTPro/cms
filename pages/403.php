<?
$pages['403']=array
(
	'title'=>'Ошибка 403. Доступ запрещен.',
	'notindex'=>'',
	'notuninstall'=>''
);

function page403()
{
	global $tpl,$errors;
	$errors[]='У вас нет необходимых привилегий для просмотра содержимого данной страницы.';
}
?>
