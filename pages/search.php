<?
$pages['search']=array
(
	'title'=>'Поиск по сайту',
	'menu'=>array(
		'title'=>'Поиск'
	),
	'templates'=>array
	(
		'searchform'=>'searchform.tpl'
	)
);

function pagesearch()
{
    global $tpl,$db,$errors,$page,$gourl,$selfpage;
	$location=0;
	if(isset($_GET['submit']))
	{
		$query=$db->escstr(htmlspecialchars($_GET['query']));
		$location=getnumparam('location');
		if (isset($query))
		{
		    $tpl->setvar('SEARCH_TEXT',stripslashes($query));

			switch ($location)
			{
				default:
				$location=0;
				$fields=array
				(
					'title'=>array
					(
						'title'=>'Название',
						'linkid'=>'key_article',
						'link'=>'?page=article&id=[]'
					),
					'description'=>array
					(
						'title'=>'Описание'
					)
				);

				$sql="SELECT key_article,content,description,title,
					id_users,pubdate,id_section,users.login as author
					FROM article LEFT JOIN users ON id_users=key_users
					WHERE (content LIKE '%$query%')OR(description LIKE '%$query%')OR
					(title LIKE '%$query%')OR(users.login LIKE '%$query%')";
				break;
				case 1:
				$fields=array
				(
					'login'=>array
					(
						'title'=>'Название',
						'linkid'=>'key_users',
						'link'=>'?page=profile&id=[]'
					),
					'SNP'=>array
					(
						'title'=>'ФИО'
					)
				);
                $sql="
					SELECT key_users,login,status,
					email,name,surname,patronymic,birthdate,regdate,note,
					userprivs.title AS usertype,
					themes.title AS theme,
					IF(((surname='')AND(name='')AND(patronymic='')),'Не указано',CONCAT(surname,' ',name,' ',patronymic)) AS SNP
					FROM users
					LEFT JOIN userprivs ON id_userprivs=key_userprivs
					LEFT JOIN themes ON id_themes=key_themes
					WHERE (login LIKE '%$query%')OR(status LIKE '%$query%')OR
						(email LIKE '%$query%')OR(name LIKE '%$query%')OR
						(patronymic LIKE '%$query%')OR(birthdate LIKE '%$query%')OR(note LIKE '%$query%')";
				break;
			}                                                          // OR(usertype LIKE '%$query%')OR(theme LIKE '%$query%')
			makeviewtable($sql,$fields);
	    }
	    else
	    {	    	$tpl->setvar('SEARCH_TEXT','Поиск...');
	    }
	}
	else
	{
		$tpl->setvar('SEARCH_TEXT','Поиск...');
	}
	$locations=array('В разделах','В профилях');
	makecombobox($locations,$location,'LOCATIONS');
	$tpl->parse(array('CONTENT'=>'searchform'));
}
?>
