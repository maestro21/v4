<?php include('autoload.php');

if(!$_SESSION['logged']) $_GET['q']='users/login';

$_PATH = split('/',@$_GET['q']); 
if(@$_PATH[sizeof($_PATH)-1] == '') unset($_PATH[sizeof($_PATH)-1]);
if(@$_PATH[0]=='' && G('defmodule')!='') $_PATH[0] = G('defmodule');
$cl = $_PATH[0];

//checking for filter
if($cl=='filter'){ setVar(@$_PATH[1],@$_PATH[2]); goBack(); die(); }
include('mapping.php');

//calling class
if(!DBfield("SELECT 1 FROM modules WHERE url='$cl'")) $cl = G('defmodule');
if(file_exists("classes/$cl.php")){
	include("classes/$cl.php");
	$class = new $cl(); //echo $cl;
}else
	$class = new masterclass();


	
//drawing	
if($_POST['ajax'])
	echo $class->parse();
else	
	echo tpl('index', array(
		'path' 		=> BASE_PATH,
		'class' 	=> $class->className,
		'langs' 	=> getVar('langs'),
		'modules' 	=> getModules(),
		'content' 	=> $class->parse(),
		'blogs'		=> $blogs,
		'sites'		=> $sites,
		'title'		=> $class->title,
		'buttons'	=> $class->buttons,
		'do'		=> $class->do,
		)
	);		
?>