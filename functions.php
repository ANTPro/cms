<?
# Замер скорости работы.
function utime()
{
	$time = explode(" ",microtime());
	$usec = (double)$time[0];
	$sec = (double)$time[1];
	return $sec + $usec;
}

function loadplugins($dir,$var)
{
	global $tpl,$$var;

	$plugins=array();
	$handle=opendir($dir);
    while ($file = readdir($handle))
		if (($file!=".")&&($file!="..")&&(ereg("\.php*$",$file)))
		{
			$plugins[]=$file;
		}
    closedir($handle);

	foreach($plugins AS $file)
	{
		require_once($dir.'/'.$file);
	}
}

function imageview($url,$align='center',$alt='',$zoom=1.5)
{
	global $tpl;
	if (file_exists($url))
	{
		$tpl->load(array('imageview'=>'imageview.tpl'));

		$image=getimagesize($url);
		$width=$image[1]*$zoom;
		$height=$image[0]*$zoom;
		//$size=formatsize(filesize($url));
		//if ($alt=="") $alt=$width."x".$height."=".$size;
	    $tpl->setvars(array(
			'URL'=>$url,
			'WIDTH'=>$width,
			'HEIGHT'=>$height,
			'ALIGN'=>$align,
			'ALT'=>$alt
		));
		$tpl->parse(array('IMAGE'=>'imageview'));
	}
}

function commentscount($page,$id)
{
	global $db,$tpl;
	if(function_exists('commentspage'))
	{
		$sql="SELECT key_comments FROM comments	WHERE (page='$page')AND(`key`=$id)";
		$result=$db->query($sql,FALSE);
		$count=$db->rowcount($result);
		$tpl->setvar('COMMENTS_COUNT',$count);
	}
	else
	{
		$tpl->vardelete('COMMENTS_COUNT');
	}
}
function processproperty($properties,$row)
{
	global $tpl;

	foreach($properties AS $propname=>$value)
	{
		if (($propname=='linkid')||($propname=='link'))
		{
			$tpl->setvar('URL',str_replace('[]',$row[$properties['linkid']],$properties['link']));
		}
		else
		{
			if (isset($properties[$propname]))
			{
				$tpl->setvar(strtoupper($propname),$value);
			}
			else
			{
				$tpl->vardelete(strtoupper($propname));
			}
		}
	}

}
# Создает таблицу для просмотра данных
# $sql - запрос к таблице. Обязательно должны выбираться все поля которые используются при выводе.
# $fields - поля для вывода(ключ - имя поля в запросе, значение - массив свойств)
# Свойства:
# title - заголовок
# linkid - идификатор для замены в шаблоне []
# link - шаблон ссылки [] - заменится на указанное поле для данной записи
# width - ширина колонки
# colspan - количество колонк
# Например:
#	$fields=array(
#		'title'=>array(
#			'title'=>'Название',
#			'linkid'=>'key_groups',
#			'link'=>'?page=groups&id=[]'
#		),
#		'useringroup'=>array(
#			'title'=>'Численность'
#		)
#	);
#	$tools=array(
#		'add'=>array(
#			'title'=>'Название',
#			'image'=>'key_groups',
#			'linkid'=>'key_groups'
#			'link'=>'?page=groups&id=[]'
#		),
#		'edit'=>array(
#			'title'=>'Численность'
#		)
#	);
function makeviewtable($sql,$fields,$tools=array())
{
	global $db,$tpl,$result,$count;
	$tpl->load(array(
		'table'=>'table.tpl',
		'tableheader'=>'tableheader.tpl',
		'tablerow'=>'tablerow.tpl',
		'tablelinkcol'=>'tablelinkcol.tpl',
		'tablecol'=>'tablecol.tpl',
		'tabletoolcol'=>'tabletoolcol.tpl'
	));
	$propvars=array('WIDTH','COLSPAN','URL');
 	safequery($sql,'',FALSE);
	$sortfields=array();
	foreach($fields AS $field=>$properties)
	{
		$sortfields[$field]=$properties['title'];
	}
	$sort=sortform($sortfields,$count);
    $error="Нет записей.";
 	if (safequery("$sql $sort",$error,FALSE))
 	{
 		$tpl->vardelete('ROWS');
 		$tpl->vardelete('HEADERS');
 		$tpl->setvar('TITLE','№');
 		$tpl->varadd('HEADERS','HEADER','tableheader');
		if (count($tools)!=0)
		{
	 		$tpl->setvars(array('TITLE'=>'Действие','COLSPAN'=>count($tools)));
	 		$tpl->varadd('HEADERS','HEADER','tableheader');
	 		$tpl->varsdelete($propvars);
		}
 		foreach($fields AS $field=>$properties)
 		{
			$tpl->setvar('TITLE',$properties['title']);
 			$tpl->varadd('HEADERS','HEADER','tableheader');
 		}
		for ($i=0; $i<$count; $i++)
		{
			$row=$db->getassocrow($result);
            $tpl->setvar('COL','#'.($i+1));
            $tpl->setvar('WIDTH',30);
            $tpl->parse(array('COLS'=>'tablecol'));
            $tpl->varsdelete($propvars);
            if (count($tools)!=0)
            {
				foreach($tools AS $tool=>$properties)
		 		{
                    processproperty($properties,$row);
                    $tpl->setvar('WIDTH',20);
					$tpl->varadd('COLS','COL','tabletoolcol');
					$tpl->varsdelete($propvars);
		 		}
            }
			foreach($fields AS $field=>$properties)
	 		{
	 		    $tpl->setvar('COL',check($row[$field]));

                processproperty($properties,$row);
	 			if (((isset($properties['linkid'])||(isset($properties['link'])))&&($row[$field]!='')))
	 			{
		 			$tpl->varadd('COLS','COL','tablelinkcol');
	 			}
	 			else
	 			{
		 			$tpl->varadd('COLS','COL','tablecol');
	 			}
	 			$tpl->varsdelete($propvars);
	 		}
			$tpl->varadd('ROWS','ROW','tablerow');
		}
		$tpl->parse(array('TABLE'=>'table'));
	}
}
# Выполнение запроса с проверкой выполнения.
# $sql - запрос (если в нем будет ошибка, то она не будет показана системой.)
# $url - переход на страницу в случае успеха.
# $error - текст сообщения об ошибке.
# $success - текст сообщения об успешном выполнении запроса.
function checkquery($sql,$url,$error,$success)
{
 	global $tpl,$db,$errors,$infos;
 	if($db->query($sql,FALSE))
 	{
		$tpl->setvars(array(
			'META_URL'=>$url
		));
		$tpl->parse(array("META"=>"metarefer"));
		$infos[]=$success;
 	}
	else
	{
		$errors[]=$error;
	}
}
# Запрос на удаление с проверкой ошибок.
# $menuid - текущее меню
# $subsec - подразделы
function makesectioncombobox($menuid,$subsec=TRUE)
{
	global $tpl,$db,$id;
	if($subsec)
	{
		$sql="";
	}
	else
	{
		$sql="AND NOT(key_section=$id)";
	}
	$result=$db->query("SELECT key_section,title FROM section WHERE (parent=0)$sql ORDER BY title");
	for ($i=0; $i<$db->rowcount($result); $i++)
	{
		$row=$db->getassocrow($result);
		$sel='';
		if ($menuid==$row['key_section'])
		{
            $sel='selected';
		}

		$tpl->setvars(array(
			'OPTION_ID'=>$row['key_section'],
			'OPTION_TITLE'=>$row['title'],
			'OPTION_SEL'=>$sel
		));
		$tpl->varadd('SECTIONS','SECTION','options');

        if($subsec)
        {
		    $result2=$db->query("SELECT key_section,title FROM section WHERE parent=".$row['key_section']." ORDER BY title");
			for ($j=0; $j<$db->rowcount($result2); $j++)
			{
				$row2=$db->getassocrow($result2);
				$sel2='';
				if ($menuid==$row2['key_section'])
				{
					$sel2='selected';
				}

				$tpl->setvars(array(
					'OPTION_ID'=>$row2['key_section'],
					'OPTION_TITLE'=>'&nbsp;&nbsp;'.$row2['title'],
					'OPTION_SEL'=>$sel2
				));
				$tpl->varadd('SECTIONS','SECTION','options');
			}
		}
		$tpl->vardelete('SECTION');
	}
}
# Запрос на удаление с проверкой ошибок.
# $table - таблица из которой надо удалить запись.
# $id - ключь записи которую надо удалить.
# $url - переход на страницу в случае успеха.
# $error - текст сообщения об ошибке.
# $success - текст сообщения об успешном выполнении запроса.
function querydelete($table,$url,$error,$success)
{
 	global $tpl,$db,$id,$errors,$infos;

 	$sql="SELECT COUNT(key_$table) FROM $table WHERE key_$table=$id";
	$id=checkid($id,$sql);
	if (isset($id))
	{
	 	checkquery("DELETE FROM $table WHERE key_$table=$id",$url,$error,$success);
	}
	else
	{
		$errors[]=$error;
	}
}
# Настройка формы сортировки и возвращение куска запроса на сортировку.
# $sortfields - должен состоять из
# ключ - название поля при сортировке,
# значение - название поля при выборе пользователем
function sortform($sortfields,$count=1)
{
	global $tpl;
	$tpl->load(array(
		'sortform'=>'sortform.tpl'
	));
    $fieldtitles[-1]='нет';
    foreach($sortfields as $fieldname=>$fieldtitle)
    {
    	$fieldnames[]=$fieldname;
    	$fieldtitles[]=$fieldtitle;
    }

	$sort='';
 	if (isset($_POST['sortsubmit']))
 	{
 		$selected=postnumparam('fields');
 		if (array_key_exists($selected,$fieldnames))
 		{
			$sort='ORDER BY '.$fieldnames[$selected];

			if (isset($_POST['desc']))
			{
				$sort.=' desc';
				$tpl->setvar('SORTTYPE',' checked');
			}
			else
			{
				$sort.=' asc';
			}
		}
		$curpage=postnumparam('showpage');
 	}
    else
    {
    	$curpage=1;
    	$selected=-1;
    }
	$limit='';
	$pagecount=round($count/10);
	if ($pagecount<1) $pagecount=1;
	if ($curpage<1)$curpage=1;
	if ($curpage>($pagecount))$curpage=($pagecount);
	$s=10*($curpage-1);
	$limit=' LIMIT '.$s.' , 10';
	$sort.=$limit;
    $pages=array();
	for($i=1;$i<=$pagecount;$i++)
	{
		$pages[$i]=$i;
	}
 	makecombobox($fieldtitles,$selected,'SORTFIELDS');
 	makecombobox($pages,$curpage,'PAGES');
	$tpl->parse(array('SORTFORM'=>'sortform'));
	return $sort;
}
# Генерация списка из базы или массива
# $source - массив для генерации. или
# $source - запрос для генерации. Должно быть 2 поля 1 - ключ, 2 - название
# $select - элемент который будет выделен.
# $varsave - переменная в которую будет сохранен список.
function makecombobox($source,$select,$varsave)
{
 	global $tpl,$db;
	$tpl->vardelete('OPTION');
 	if(gettype($source)=="array")
 	{
		foreach($source as $key=>$value)
		{
			$sel='';
			if ($select==$key)
			{
	            $sel='selected';
			}
			$tpl->setvars(array(
				'OPTION_ID'=>$key,
				'OPTION_TITLE'=>$value,
				'OPTION_SEL'=>$sel
			));
			$tpl->varadd($varsave,'OPTION','options');
		}
 	}
 	else
 	{
		$result=$db->query($source);
		$count=$db->rowcount($result);
		if ($count>0)
		{
			for ($i=0; $i<$count; $i++)
			{
				$row=$db->getassocrow($result);
				$sel='';
				if ($select==$row[0])
				{
		            $sel='selected';
				}
				$tpl->setvars(array(
					'OPTION_ID'=>$row[0],
					'OPTION_TITLE'=>$row[1],
					'OPTION_SEL'=>$sel
				));
				$tpl->varadd($varsave,'OPTION','options');
			}
	    }
	    else
	    {
	   		$tpl->setvars(array(
				'OPTION_ID'=>-1,
				'OPTION_TITLE'=>'Нет',
				'OPTION_SEL'=>'selected'
			));
			$tpl->varadd($varsave,'OPTION','options');
	    }
    }
}
# Запрос с обработкой ошибок.
# Для использования в функциях надо добавлять в global $result,$count
# Нельзя использовать вложенно.
function safequery($sql,$error='',$refresh=TRUE)
{
   	global $db,$result,$errors,$count,$gourl,$selfpage;
	$result=$db->query($sql);
	if ($result)
	{
		$count=$db->rowcount($result);
		if ($count>0)
		{
			return TRUE;
		}
		else
		{
			if ($error)
			{
				if ($refresh)
				{
					$gourl=$selfpage;
				}
				$errors[]=$error;
			}
			return FALSE;
		}
	}
}
function insertrecord($table,$fields,$showmsg=TRUE)
{
	global $db,$page,$infos,$errors,$gourl,$selfpage;
	if($result=$db->execinsert($table,$fields))
	{
		if($showmsg)
		{
			$gourl="?page=admin&tool=$page";
			$infos[]='Добавлено.';
			return $result;
		}
	}
	else
	{
		if($showmsg)
		{
			$gourl=$selfpage;
			$errors[]='Ошибка. При добавлении записи возникла ошибка. Проверьте введенные данные.';
		}
	}
}
function updaterecord($table,$fields)
{
	global $db,$page,$infos,$errors,$gourl,$selfpage;
	if($result=$db->execupdate($table,$fields))
	{
		$gourl="?page=admin&tool=$page";
		$infos[]='Изменения сохранены.';
		return $result;
	}
	else
	{
		$gourl=$selfpage;
		$errors[]='Ошибка. При редактировании записи возникла ошибка. Проверьте введенные данные.';
	}
}
# Проверка на NULL
function check($field)
{
	if (!isset($field)) return 'Нет';
	if ($field=='') return 'Нет';
	return $field;
}
# Функция с созданием
# $fields - массив полей с иерархией.
# $constfields - массив полей.
# Пример вызова: setfields(array('faculty','department','specialization'),array('title','year'));
function setfields($fields,$constfields)
{
	global $tpl;

    if (isset($_POST['choise']))
    {
    	$choise=$_POST['choise'];
    }
    else
    {
    	$choise=$fields[0];
    }

	foreach($fields AS $key=>$field)
	{
		if ($key>0)
		{
			if (!isset($_POST["id_$field"])||($choise==$fields[$key-1]))
			{
				$_POST["id_$field"]=0;
			}
		}
		else
		{
			if (!isset($_POST["id_$field"]))
			{
				$_POST["id_$field"]=0;
			}
		}
		$sql="SELECT COUNT(key_$field) FROM $field WHERE key_$field=".$_POST["id_$field"];
        $getsql="SELECT key_$field FROM $field";
		$sqlfield="SELECT key_$field, title FROM $field";
		if ($key>0)
		{
			$prevfield=$fields[$key-1];
			$getsql.=" WHERE id_$prevfield=".$_POST["id_$prevfield"];
			$sqlfield.=" WHERE id_$prevfield=".$_POST["id_$prevfield"];
		}
		$getsql.=' LIMIT 1';
		$_POST["id_$field"]=checkid($_POST["id_$field"],$sql,$getsql);
		makecombobox($sqlfield,$_POST["id_$field"],strtoupper($field));
	}

	foreach($constfields AS $key=>$field)
	{
		if (isset($_POST[$field]))
		{
			$tpl->setvar(strtoupper($field),$_POST[$field]);
		}
	}
}
# Обезопасивание строки и обкавычивание
function tostr($value)
{
 	global $db;
	return '\''.$db->escstr(htmlspecialchars(poststrparam($value))).'\'';
}
# Проверка на существование индификатора.
# Если результат запроса $checksql - 0, то выполняется $getsql и возвращается полученное им значение.
# Если $getsql не указан, то результат не возвращается(Т.е. только проверка без поправки).
function checkid($id,$checksql,$getsql='')
{
	global $db;
	$result=$db->query($checksql);
	if ($db->result($result, 0)==0)
	{
		if (!$getsql=='')
		{
			$result=$db->query($getsql);
			if ($db->rowcount($result)!=0)
			{
				return $db->result($result, 0);
			}
			else
			{
				return $id;
			}
		}
	}
	else
	{
		return $id;
	}
}

function getnumparam($param)
{
	$r=0;
	if (isset($_GET[$param]))
	{
		$r=$_GET[$param];
	}
	if (!is_numeric($r))
	{
		$r=0;
	}
	return $r;
}
function postnumparam($param)
{
	$r=0;
	if (isset($_POST[$param]))
	{
		$r=$_POST[$param];
	}
	if (!is_numeric($r))
	{
		$r=0;
	}
	return $r;
}
function setmainvars()
{
	global $tpl,$selfpage,$start,$errors,$infos,$selfpage,$vuz_title;
	$start = utime();
	$errors=array();
	$infos=array();
	//print_r($GLOBALS);
	$selfpage=$_SERVER['REQUEST_URI'];
	$website='http://'.$_SERVER['SERVER_NAME'].'/';
	$tpl->setvars(array(
		//'SITE_WEBSITE'=>$vuz_title,
		'SITE_TITLE'=>$vuz_title,
		'COPYRIGHT'=>'© SFI',
		'SELF'=>$selfpage,
		'META_TIME'=>'2'
	));
}
function stats()
{
	global $tpl,$start;
	$tpl->setvars(array(
		'TPLTEMPLATESCOUNT'=>count($tpl->templates),
		'TPLVARIABLESCOUNT'=>count($tpl->variables)
	));
	$runtime=utime()-$start;
	$tpl->setvar('RUNTIME',$runtime);
}

function poststrparam($param)
{
	$r=0;
	if (isset($_POST[$param]))
	{
	  $r=$_POST[$param];
	}
	if (!is_string($r))
	{
		$r="";
	}
	return $r;
}
# Возвращает имя файла
function filenamewithoutext($filename)
{
	//Удаление всего пути
	$filename=ereg_replace(".*[\\/]","",$filename);
    //Удаление расширения
	return ereg_replace("\.[[:alnum:]]*$","",$filename);
}
# Проверка авторизации, установка привилегий и темы.
function getuserinfo()
{
	global $db,$userid,$login,$privs,$result,$count,$theme;
	if (isset($_COOKIE['userid']) and isset($_COOKIE['hash']))
	{
		$result = $db->query("SELECT key_users,login,hash FROM users WHERE key_users= '".intval($_COOKIE['userid'])."' LIMIT 1");
		if ($result)
		{
			$userdata = $db->getassocrow($result);
			if(($userdata['hash']!==$_COOKIE['hash'])OR($userdata['key_users']!==$_COOKIE['userid']))
			{
				setcookie("userid");
				setcookie("hash");
			}
			else
			{
				$login=$userdata['login'];
				$userid=$userdata['key_users'];
			}
		}
	}

	if (isset($login))
    {
	    $sql="SELECT key_users,id_userprivs,themes.title AS theme,
	    userprivs.title AS userprivs
	    FROM users
	    LEFT JOIN userprivs ON id_userprivs=key_userprivs
	    LEFT JOIN themes ON id_themes=key_themes
	    WHERE key_users=$userid LIMIT 1";
	    $error="Ошибка определения информации о пользователе";
		if (safequery($sql,$error))
		{
			$row=$db->getassocrow($result);
	        switch ($row['userprivs'])
	        {
	          default:
	            $privs=array('u');
	            break;
	          case 'Студент':
	            $privs=array('s','u');
	            break;
	          case 'Преподаватель':
	            $privs=array('t','s','u');
	            break;
	          case 'Редактор':
	            $privs=array('e','t','s','u');
	            break;
	          case 'Модератор':
	            $privs=array('m','e','t','s','u');
	            break;
	          case 'Администратор':
	            $privs=array('a','m','e','t','s','u');
	            break;
	        }
	        if (isset($row['theme']))
	        {
	        	$theme=$row['theme'];
	        }
	        else
	        {
	        	$theme='default';
			}
	    }
    }
    else
    {
    	$theme='default';
    }
}
?>