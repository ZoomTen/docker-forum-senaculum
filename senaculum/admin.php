<?php
require("include/top.php");
global $forumVariables;
if(!$forumVariables['adminInlogged']) {
	header("location: index.php");
	die;
}
?>
<html>
	<title>
		Admin
	</title>
	<head>
	</head>
	<frameset cols="150,*" frameborder="NO" border="0" framespacing="0">
		<frame name="menu"  src="adminMenu.php" noresize="noresize">
		<frame name="forum" src="index.php" noresize="noresize">
	</frameset>
</html>