<?

$tools['query']=array
(
	'privs'=>'a',
	'actions'=>array
	(
		'exec'=>'Выполнене запроса',
		'main'=>'Выполнение запросов'
	),
	'templates'=>array
	(
		'query'=>'query.tpl',
		'queryexec'=>'queryexec.tpl'
	)
);

function querymain()
{
	global $tpl,$page;
    $tpl->parse(array('CONTENT'=>'query'));
}

function queryexec()
{
	global $tpl,$db,$errors,$infos,$gourl,$page;
 	if (isset($_GET['query']))
 	{
		$query=$_GET['query'];
		$tpl->setvar('QUERY',str_replace("\n",'<br/>',$query));
		$result=$db->query($query,FALSE);
        if ($result)
        {			$infos[]="Запрос выполнен успешно.";
			if (gettype($result)!='boolean')
			{
				$count=$db->fieldcount($result);
				$rowcount=$db->rowcount($result);
				if (($count!=0)&&($rowcount!=0))
				{
					for ($i=0;$i<$count;$i++)
					{						$field=$db->getfieldname($result,$i);
						$fields[$field]=array('title'=>$field);					}
					makeviewtable($query,$fields);
				}
			}
        }
        else
        {        	$errors[]='Запрос содержит ошибку.';
        	$errors[]='Номер ошибки MySQL: '.mysql_errno();
        	$errors[]='MySQL вернул ошибку: '.mysql_error();
        }
 	}
 	else
 	{
		$errors[]="Нет запроса на выполнение";
		$gourl="?page=admin&tool=$page";
	}
	$tpl->parse(array('CONTENT'=>'queryexec'));
}
?>
