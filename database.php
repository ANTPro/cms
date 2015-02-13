<?
# Класс для работы с базой
class TDataBase
{
	# Соединение с базой
	var $connect;
	var $db;
	var $sql;
    var $debug;

    # При создании экземпляра создается соединение с базой
	function TDataBase()
	{
		global $vuz_dbhost,$vuz_dbusername,$vuz_dbpassword;
        $this->debug=FALSE;
		$this->connect=@mysql_connect($vuz_dbhost,$vuz_dbusername,$vuz_dbpassword)or
		die('Ошибка: Невозможно установить соединение с сервером баз данных. Проверьте настройки подключения');
	}
	function createdb()
	{
		global $vuz_dbname;
        $this->query("DROP DATABASE $vuz_dbname",FALSE);
		$this->query("CREATE DATABASE $vuz_dbname DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;")or
			$this->error('Ошибка: Невозможно создать базу данных: '.$vuz_dbname);
		mysql_close($this->connect);
		$this->TDataBase();
		$this->selectdb();
	}
	function selectdb()
	{
		global $vuz_dbname;
		$this->db=mysql_select_db($vuz_dbname,$this->connect)or $this->error('Ошибка: Не возможно открыть базу данных: '.$vuz_dbname);
		$this->query("SET NAMES 'utf8'")or $this->error('Ошибка: Неудалось включить UTF8.');
	}
    # выполнение запроса и возврат результата
	function query($sql,$checkerr=TRUE)
	{
		$this->sql=$sql;
		$result=mysql_query($sql,$this->connect);
		if (!$result&&$checkerr) $this->error('query: запрос содержит ошибку.');
		return $result;
	}
	# хз что
	function result($result,$row)
	{
		if (!$result) $this->error('result: не верные исходные данные.');
		return mysql_result($result,$row);
	}
	# Количество колонок в результате запроса
	function fieldcount($result)
	{
		if (!$result) $this->error('fieldcount: не верные исходные данные.');
		return mysql_num_fields($result);
	}
	# Количество записей в результате запроса
	function rowcount($result)
	{
		if (!$result) $this->error('rowcount: не верные исходные данные.');
		return mysql_num_rows($result);
	}
	# Получение массива записи
	function getrow($result)
	{
		if (!$result) $this->error('getrow: не верные исходные данные.');
		return mysql_fetch_row($result);
	}
	# Получение инени поля
	function getfieldname($result,$fieldnum)
	{
		if (!$result) $this->error('getfields: не верные исходные данные.');
		return mysql_field_name($result,$fieldnum);
	}
	# Получение массива таблицы
	function gettable($result)
	{
		if (!$result) $this->error('gettable: не верные исходные данные.');
		return mysql_fetch_array($result);
	}
	# экран для одинарных кавычек
	function escstr($str)
	{
		return mysql_real_escape_string($str);
	}
	# получение записи с ключами - названиями полей
	function getassocrow($result)
	{
		if (!$result) $this->error('getassocrow: не верные исходные данные.');
    	return mysql_fetch_assoc($result);
    }
    function error($msg)
    {
    	if ($this->debug)
    	{
	    	die("<html>
					<head>
						<title>Запрос содержит ошибку</title>
						<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
						<style type=\"text/css\">
							#MSGERROR{border: 1px solid #000000;padding: 8px 11px;
							font-size: 10pt; font-weight:bold;margin: 0px 10px 10px 10px;text-align:left;}
						</style>
					</head>
					<body>
						<div id='msgerror'>
							Номер ошибки MySQL: ".mysql_errno($this->connect)."<br/>
							MySQL вернул ошибку: ".mysql_error($this->connect)."
						</div>
						Текст запроса:
						<div id='msgerror'>
							".str_replace("\n",'<br/>',$this->sql)."
						</div>
					</body>
				</html>");
        }
        else
        {	    	die("<html>
					<head>
					<title>Запрос содержит ошибку</title>
					<style type=\"text/css\">
						#MSGERROR{border: 1px solid #000000;padding: 8px 11px;
						font-size: 10pt; font-weight:bold;margin: 0px 10px 10px 10px;text-align:left;}
					</style>
					</head>
					<body>
						<div id='msgerror'>
							Номер ошибки MySQL: ".mysql_errno($this->connect)."
						</div>
					</body>
				</html>");        }
    }

	# Генерация и выполнение запроса на добавление данных
	# $table - назване таблицы.
	# $fields - поля (уже в кавычках).
	function execinsert($table,$fields)
	{
		$sql="INSERT INTO $table (";
		$sqlf='';
		foreach($fields as $key=>$value)
		{
			$sql.=$key.', ';
			$sqlf.=$value.', ';
		}
		$sql=substr($sql,0,strlen($sql)-2).')VALUES ('.$sqlf;
		$sql=substr($sql,0,strlen($sql)-2).');';
    	if ($this->debug){echo $sql;}
		return $this->query($sql,FALSE);
	}

	# Генерация и выполнение запроса на обновление данных
	# $table - назване таблицы.
	# $fields - поля (уже в кавычках). $fields["key_$table"] - ключ
	function execupdate($table,$fields)
	{
		$sql="UPDATE $table SET ";
		foreach($fields as $key=>$value)
		{
			$sql.=$key.'='.$value.', ';
		}
		$sql=substr($sql,0,strlen($sql)-2)." WHERE key_$table=".$fields["key_$table"];
    	if ($this->debug){echo $sql;}		return $this->query($sql,FALSE);
	}
}
?>