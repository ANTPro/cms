<?
$pages['404']=array
(
	'title'=>'Ошибка 404. Страница не найдена.',
	'notindex'=>'',
	'notuninstall'=>''
);

function page404()
{
	global $tpl,$errors;
	$errors[]='Ошибка 404. Страница не найдена.';
}
?>