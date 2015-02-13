<?
$pages['setup']=array
(
	'title'=>"Установка CMS 'ВУЗ'",
	'menu'=>array(
		'title'=>'Установка'
	),
	'notmenu'=>'',
	'notmainmenu'=>'',
	'templates'=>array
	(
		'form1'=>'setupform1.tpl',
		'form2'=>'setupform2.tpl',
		'form3'=>'setupform3.tpl'
	),
	'setuponly'=>'',
	'notindex'=>''
);

function genvar($title)
{
	return "\$vuz_$title=\"".htmlspecialchars($_POST[$title])."\";\n";
}
//Конец запроса - ';#13'. У последнего
function split_sql($sql)
{
	//$sql = trim($sql);
	$sql = ereg_replace("\n#[^\n]*\n", "\n", $sql);

	$ret = array();
    $j=0;
	for($i=0; $i<strlen($sql)-1; $i++)
	{
		if(($sql[$i] == ";")&&(($sql[$i+1]==chr(10))||($sql[$i+1]==chr(13))))
		{
			$ret[] = substr($sql, $j, $i-$j+1);
			$j=$i+1;
		}
	}

	return($ret);
}
# Загружет файл с запросами парсит его, и пытается выполнить распарсенные запросы(с обработкой ошибок)
function execsql($sqlfilename)
{
	global $db,$errors,$infos;
	$file="./sql/$sqlfilename.sql";
	if (file_exists($file))
	{
		$fsql=fopen($file,"r");
		$sql=fread($fsql,filesize($file));
		fclose($fsql);

		$query_arr = split_sql($sql);

		foreach($query_arr as $query)
		{
			$result=$db->query($query,FALSE);
			$txtsql=str_replace("\n",'<br/>',htmlspecialchars($query));
			if ($result)
			{
				$infos[]='Запрос выполнен успешно:<br/>'.$txtsql;
			}
			else
			{
				$errors[]='Запрос содержит ошибку:<br/>'.$txtsql;
			}
		}
	}
	else
	{
		$errors[]='Файл с запросами отсутствует: '.$sqlfilename;
	}
}

# Генерация конфига.
function makeconfig()
{
	$cfg="<?\n";
	$cfg.=genvar('title');
	$cfg.=genvar('dbhost');
	$cfg.=genvar('dbname');
	$cfg.=genvar('dbusername');
	$cfg.=genvar('dbpassword');
	$cfg.="?>";
	$cfgfilename='./config.php';
	$fcfg=fopen($cfgfilename,"w");
	fwrite($fcfg,$cfg);
	fclose($fcfg);
}

function pagesetup()
{
	global $tpl,$db,$vuz_title,$vuz_dbhost,$vuz_dbusername,$vuz_dbpassword,$vuz_dbname,$page,$infos;
	if(isset($_POST['submitstep1']))
	{
		$page='';
		$tpl->setvar('MAIN_HEADER','Шаг 2 из 3: Создание базы данных');
		$tpl->parse(array('CONTENT'=>'form2'));
        makeconfig();
        $tpl->setvars(array(
        	'ADMIN'=>$_POST['adminlogin'],
			'PASS'=>md5(md5(trim($_POST['adminpassword'])))
        ));
	}
	else
	{
		if(isset($_POST['submitstep2']))
		{
			$page='';
			$tpl->setvar('MAIN_HEADER','Шаг 3 из 3: Завершение установки');
			$tpl->parse(array('CONTENT'=>'form3'));

			if(isset($_POST['execstruct']))
			{
				include('./config.php');
				$db=new TDataBase();
				$db->createdb();
				$db=new TDataBase();
				$db->selectdb();
				execsql('struct');

				$fields['key_users']='NULL';
				$fields['login']=tostr('admin');
				$fields['password']=tostr('pass');
				$fields['id_themes']=1;
				$fields['id_userprivs']=6;
				if ($db->execinsert('users',$fields))
				{
					$infos[]='Учетная запись администратора зарегистрирована.';
				}
				else
				{
					$errors[]='Ошибка. Учетная запись администратора не может быть создана.';
				}
			}
			if(isset($_POST['execinsert']))
			{
				execsql('insert');
			}
			$infos[]='Установка базы успешно завершена!';
			unlink('./setup');
		}
		else
		{
			$tpl->setvar('MAIN_HEADER','Шаг 1 из 3: Настройка параметров');
			$tpl->parse(array('CONTENT'=>'form1'));
		}
	}
}
?>
