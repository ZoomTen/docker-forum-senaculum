<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
session_start();

function stripMagicQuotes($array) {
	foreach ($array as $element => $value) {
		if (is_array($value))
			$array[$element] = stripMagicQuotes($value);
		else 
			$array[$element] = stripslashes($value);
	}
	return $array;
}
if (get_magic_quotes_gpc()) {
	if(!empty($_GET))
		$_GET    = stripMagicQuotes($_GET);
	if(!empty($_POST)) 
		$_POST = stripMagicQuotes($_POST);
	if(!empty($_COOKIE))
		$_COOKIE = stripMagicQuotes($_COOKIE);
}


require_once('./classes/settingHandler.php');
$settings = new settingHandler;

require_once('./classes/logInOutHandler.php');
$auth = new logInOutHandler;

global $forumVariables;

$settings->getAll();

$correctLogin = true;
if(isset($_POST['loginUsername']) && isset($_POST['loginPassword'])) {
	setcookie('forumLastUser',$_POST['loginUsername'],time()+2678400,'/');
	$forumVariables['inlogged'] = $correctLogin = $auth->logIn($_POST['loginUsername'],$_POST['loginPassword']);
}
else
	$auth->loggedIn();
if(!$correctLogin) {
	global $alert;
	$alert = $lang['invalidPassword'];
}

if(isset($_GET['logOut'])) {
	$auth->logOut();
	/*if(!empty($_GET['id']))
		$id = "?id=".$_GET['id'];
	else
		$id = "";*/
	header("location: index.php");
}

require('./lang/default/lang.php');
if($forumVariables['inlogged'] && !empty($forumVariables['lang']) && file_exists("./lang/".$forumVariables['lang']."/lang.php")) {
	if($forumVariables['lang'] != "default") {
		if(!@include("./lang/".$forumVariables['lang']."/lang.php"))
			$forumVariables['lang'] = "default";
	}	
}
elseif($forumSettings['lang'] != "default") {
	if(@include('./lang/'.$forumSettings['lang'].'/lang.php'))
		$forumVariables['lang'] = $forumSettings['lang'];
	else
		$forumVariables['lang'] = "default";	
}
else
	$forumVariables['lang'] = "default";	
	
require('./lang/default/default.php');
if($forumSettings['lang'] != "default") {
	@include('./lang/'.$forumSettings['lang'].'/default.php');
}

/*global $moderatorInlogged;
$moderatorInlogged = false;*/

$page = substr(strrchr($_SERVER['SCRIPT_NAME'],'/'),1);

if($page == "posts.php") {
	if(!empty($_GET['dp']) && !empty($_GET['id'])) {
		//$sql = "SELECT forumID FROM threads WHERE threadID = '".$_GET['id']."'";
		//$result = $db->runSQL($sql);
		//$row = mysql_fetch_object($result);
		require_once('./classes/postHandler.php');
		require_once('./classes/threadHandler.php');
		$threads = new threadHandler;
		$thread = $threads->getOne($_GET['id'],true);
		$posts = new postHandler;
		if($posts->remove($_GET['dp'],$_GET['id']))
			header("location: threads.php?id=".$thread['forumID']);
		else	
			header("location: posts.php?id=".$_GET['id']);	
	}
	else if(!empty($_GET['dt']) && !empty($_GET['id']))
	{
		require_once('./classes/threadHandler.php');
		$threads = new threadHandler;
		$thread = $threads->getOne($_GET['dt']);
		$id = $thread['forumID'];
		$threads->remove($_GET['dt']);
		header("location: threads.php?id=".$id);	
	}
}

/*if($page == "threads.php") {
	if($login->moderator($_GET['id'],"all"))
		$moderatorInlogged = true;
}

if($page == "memberlist.php")
{
	if(!empty($_GET['id']))
	{
		require_once('./classes/memberHandler.php');
		$members = new memberHandler;
		$members -> remove($_GET['id']);
		header("location: memberlist.php");
	}
}*/
?>