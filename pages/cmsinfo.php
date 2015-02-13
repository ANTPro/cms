<?
$pages['cmsinfo']=array
(
	'title'=>"Описание CMS 'ВУЗ'",
	'menu'=>array(
		'title'=>'Описание'
	),
	'setuponly'=>''
);

function loadinfo($txtfilename)
{
	$file='./'.$txtfilename;
	if (file_exists($file))
	{
		$hfile=fopen($file,"r");
		$txt=fread($hfile,filesize($file));
		fclose($hfile);
		return $txt;
		//return str_replace("\n",'<br/>',$txt);
	}
}
function pagecmsinfo()
{
	global $tpl,$db,$infos;

    $txt=loadinfo('readme.txt');
	if (isset($txt))
		$tpl->setvar('CONTENT',$txt);
}
?>
