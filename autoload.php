<?php
session_name("engine");
session_start(); 
/*if(!file_exists('config.php')){
	include('install.php');
	die();
}*/
include('config.php');
include("engine/functions.php");
include("engine/dbquery.php");
include("engine/" . DB_TYPE . ".php");
include("db/schema.php");
include('engine/masterclass.php');
/*
DBconnect();
getGlobals();
getRights();
getLangs(); */ 