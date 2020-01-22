<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
if(!empty($_GET['user']) && !empty($_GET['code'])) {
	require_once("./include/top.php");
	require_once("classes/dbHandler.php");
	$db = new dbHandler;
	
	global $lang; 
	
	$sql = "SELECT memberID, username, actKey FROM _'pfx'_members WHERE userName = '".$db->SQLsecure($_GET['user'])."'";
	$result = $db->runSQL($sql);
	if($db->numRows($result) > 0) {
		$row = $db->fetchArray($result);
		if($row['actKey'] == $_GET['code'] && $row['username'] == $_GET['user']) {
			$sql = "UPDATE _'pfx'_members SET activated = 1, actKey = NULL WHERE memberID = '".$row['memberID']."'";
			$db->runSQL($sql);
			$message = $lang['activateUserMessageCompleate'];
		}
		else
			$message = $lang['activateUserErrorMessage'];
	}
	else
		$message = $lang['activateUserErrorMessage'];
}
else
	$message = $lang['activateUserErrorMessage'];
header("location: index.php?alert=".$message);	
?>