<?php
/*

    ANTTPL v2.0

	Цель написания данного класса - это очередная попытка разделить код от дизайна.
	Лицензия - GNU
	© ANTPro

TODO:
	+1. Если между {} - скобок есть переменная-шаблон [], то удалить весь текст внутри {}
    2. Сделать хранение шаблонов в базе и прозрачную загрузку.

*/
class Template
{
	# путь до папки с шаблонами
	var $rootdir;
	# Текущая тема
	var $theme;
	# Загруженные шаблоны
	var $templates=array();
	# Переменные
	var $variables=array();
    # Режим отладки
    var $debug;
    # Режим отладки вставки шаблонов.
    var $debugtpl;
    # Сохранение пути к шаблонам	function Template($theme)
	{		$this->rootdir='./templates/';
		$this->theme="$theme/";
		$this->setvar('TPLDIR',$this->rootdir.$this->theme);
		$this->debug=FALSE;
		$this->debugtpl=FALSE;	}
	function errors($msg)
	{		print ($msg."<br/>");	}
	# заебло прописывать load :)
	function loadall()
	{
		$path.='/';
		$handle=opendir($this->rootdir.$this->theme.'/');
	    while ($file = readdir($handle))
			if (($file!=".")&&($file!=".."))
				load(array(filenamewithoutext($file)=>$file));
	    closedir($handle);
	}
	function loadtpl($tplfilename,$tplname)
	{		$ftpl=fopen($tplfilename,"r");
		$tplf=fread($ftpl,filesize($tplfilename));
		# 3 magic letters
		$tplf=substr($tplf,3,strlen($tplf));
		$this->templates[$tplname]=$tplf;
		fclose($ftpl);
	}
	# Загрузка шаблонов
    # ключ - псевдоним шаблона, значение - имя файла шаблона
	function load($tplfilename)
	{
		if(is_array($tplfilename))
		{
			//if($this->debug){$i=0;}
    		foreach($tplfilename as $Key=>$Val)
			{
				$tplname=$this->rootdir.$this->theme.$Val;
				if (file_exists($tplname))
				{
					$this->loadtpl($tplname,$Key);
				}
				else
				{
					$tplname=$this->rootdir.$Val;
					if (file_exists($tplname))
					{						$this->loadtpl($tplname,$Key);
					}
					else
						$this->errors('Не удалось открыть шаблон: '.$this->rootdir.$this->theme.$Val);				}
				//if($this->debug){echo "load[".$i."]:templates[".$Key."]=".$this->templates[$Key]."<br/>";$i++;}
			}
		}
	}
	# Установка переменной
	function setvar($varname,$varvalue)
    {
		$this->variables[$varname]=str_replace(array('[',']','{','}'),array("&#91","&#93","&#123","&#125"),$varvalue);
    }
	# Установка переменных (Cкобки - '{}[]' - будут заменены на HTML эквиваленты)
    # ключ - имя переменной, значение - значение которое надо установить
    function setvars($vars)
    {
		if(gettype($vars)== "array")
		{
    		foreach($vars as $Key=>$Val)
			{
				$this->variables[$Key]=str_replace(array('[',']','{','}'),array("&#91","&#93","&#123","&#125"),$Val);
			}
		}
    }
	# Копирование шаблона в переменную
	function tpl2var($tplvar)
    {
		if(is_array($tplvar))
		{
    		foreach($tplvar as $tplname=>$varname)
			{				if(!isset($this->templates[$tplname]))
    				$this->errors('Не удалось скопировать шаблон: '.$tplname);
				else
					$this->variables[$varname]=$this->templates[$tplname];
			}
    	}
    }
	# Замена переменных в строке
	function genstr($tplval)
	{		$s=$tplval;    	foreach($this->variables as $Key=>$Val)
		{
			if ($this->debugtpl)
			{
				$s=str_replace("[".$Key."]","<!--(".$Key."-->".$Val."<!--".$Key.")-->",$s);
				//$s=str_replace("{".$Key."}","<!--".$Key."-->".$Val."<!--".$Key."-->"."{".$Key."}",$s);			}
			else
			{				$s=str_replace("[".$Key."]",$Val,$s);
				//$s=str_replace("{".$Key."}",$Val."{".$Key."}",$s);
			}
			//$s=ereg_replace("({".$Key."})",$Val."{".$Key."}",$s);
			//$s=ereg_replace("(\[".$Key."\])",$Val,$s);
		}
		return $s;	}
	# Динамический парсер :)
	# Предназначен для создания шаблонных таблиц. Как получится в реале хз.
	# varto - имя переменной к значению которой будет добавлено.
	# addvar - имя переменной значение которой будет заполнено из шаблона
	# tplname - имя шаблона
	function varadd($varto,$addvar,$tplname)
	{
		$this->parse(array($addvar=>$tplname));
    	if(!isset($this->variables[$varto]))
  			$this->variables[$varto]="";
  		if(isset($this->variables[$addvar]))
			$this->variables[$varto].=$this->variables[$addvar];
	}
	# Распарсивание условий.
	# Если между {} - скобок есть переменная-шаблон [], то удалить весь текст внутри {}
	# Если нет, то только {}
	function parseterms($varname)
	{
		if(!isset($this->variables[$varname]))
		{			$this->errors('Переменная не может быть распарсенна: '.$varname);		}
		else
		{			$s=$this->variables[$varname];
			$r='';
			$a=0;
			$b=0;
			while(strpos($s,'{',$b))
			{
				$a=strpos($s,'{',$b);
				$r.=substr($s,$b,$a-$b);
				$b=strpos($s,'}',$a)+1;
				if ($b!=1)
				{
					$c=substr($s,$a+1,$b-$a-2);
					if (ereg("(\[[A-Z0-9_]+\])",$c))
					{
						$c='';
					};
					$r.=$c;
				}
				else
				{
					$b=$a+1;
					break;
				}
			}
			$r.=substr($s,$b);
			$this->variables[$varname]=$r;		}

	}
    # Замена переменных в шаблонах и сохранение результата в переменные
    # ключ - имя переменной, значение - имя шаблона
	function parse($names)
	{
		if($this->debug){$i=0;};
		if(gettype($names)=="array")
		{			foreach($names as $Key=>$Val)
			{

				# Если нет переменной на парсинг, то копируем шаблон				if (isset($this->variables[$Val]))
				{			    	if(!isset($this->variables[$Val]))
	    				$this->errors('Переменная не может быть обработанна: '.$Val);
					else
						$this->variables[$Key]=$this->genstr($this->variables[$Val]);				}
				else
				{					if(!isset($this->templates[$Val]))
		    			$this->errors('Шаблон не может быть обработан: '.$Val);
					else
		        		$this->variables[$Key]=$this->genstr($this->templates[$Val]);
				}
				$this->parseterms($Key);
				if($this->debug){
				echo "&nbsp;&nbsp;parse[".$i."]:\$variables[".$Key."]=".$this->variables[$Key]."<br/>";
				$i++;};
			}

		}
		else
			$this->errors('Указаные неверные параметры');	}
	# Процедура которая реализует многократную подстановку данных в шаблон
	# Используется, например, для генерации ячеек в таблице
	# Имя шаблона инициализации
	# Имя рабочей переменной
	# Количество шагов
	# Функция, в которой должны устанавливаться новые значения переменных
	function cyclegen($tplname,$varname/*,$mainvarname*/,$stepcount,$callback)
	{
		$this->tpl2var(array($tplname=>$varname));
		for($i=0;$i<$stepcount;$i++)
		{
			$callback($i);//Для установки переменных
			$this->parse(array($varname=>$varname));
			if($this->debug){echo "cyclegen[".$i."]:\$varname=".$varname."<br/>";};
		}
		$this->cleanvar($varname);//$tpl->vardelete($varname);
		//$this->parse(array($mainvarname=>$mainvarname));
	}
	# Костыль для шаблонов с условием.
	function varsdelete($vars)
	{
		if(gettype($vars)=="array")
		{
			foreach($vars AS $varname)
			{
				$this->vardelete($varname);
			}
		}
	}
	# Смерть предателям!!!
	function vardelete($varname)
	{		unset($this->variables[$varname]);	}
    # Удаление не подставленных переменных в указанной переменной
    function cleanvar($varname)
	{
	   	if(!isset($this->variables[$varname]))
    		$this->errors('Нет переменной для очистки: '.$varname);
		else
		{			$this->variables[$varname]=ereg_replace("([[A-Z0-9_]+])","",$this->variables[$varname]);
		}
	}
	# Удаление не подставленных переменных
	function cleanvars()
	{
    	foreach($this->variables as $Key=>$Val)
		{
		    $this->cleanvar($Key);
		}
	}
    # Вывод переменной
	function exec($varname='MAIN')
	{
		if($this->debug){$this->debug();};
	   	if(!isset($this->variables[$varname]))
    		$this->errors('Переменная не может быть выведена: '.$varname);
		else
		{
			//print("<!--Сгенерированно модулем ANTTPL-->\n");
			$this->cleanvar($varname);
			print($this->variables[$varname]);
		}	}
    # Чтоб долго неибацо
	function debug()
	{
		echo "templates<br>";
		while (list($Key,$Val)=each($this->templates))
		{
			echo "template[".$Key."]=".$Val."<br>";
		}
        echo "<br>";
		echo "variables<br>";
		while (list($Key,$Val)=each($this->variables))
		{
			echo "variable[".$Key."]=".$Val."<br>";
		}
		echo "<br>";
		echo "<br>";
    }}
?>