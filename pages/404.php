<?
$pages['404']=array
(
	'title'=>'Ошибка 404. Страница не найдена.',
	'notindex'=>'',
	'notuninstall'=>''
);

function page404()
{
	global $tpl,$errors;	$tpl->setvar('MAIN_HEADER','Ошибка 404');
	$errors[]='Ошибка 404. Страница не найдена.';
}
?>
