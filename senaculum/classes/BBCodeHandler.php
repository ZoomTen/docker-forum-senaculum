<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once('dbHandler.php');

class BBcodeHandler{

	function BBCodeHandler() {}
	
	function getOne($BBCodeID){	
		$db = new dbHandler();						//Makes databasehandler to db
		$BBCodeID = $db->SQLsecure($BBCodeID);
		$sql = "SELECT * FROM _'pfx'_BBcode WHERE BBcodeID='".$BBCodeID."'";	
		$result = $db->runSQL($sql);					//Run the SQL-code
		if($db->numRows($result) == 0)
			return false;
		$row = $db->fetchObject($result);				//Fetch the result
		$BBCode['BBCodeID'] = $row->BBcodeID;				//Set the data in to an array
		$BBCode['code'] = $row->code;
		$BBCode['result'] = $row->result;
		$BBCode['display'] = $row->display;
		$BBCode['info'] = $row->info;
		$BBCode['accesskey'] = $row->accesskey;
		$BBCode['scriptName'] = $row->scriptName;
		return $BBCode;
	}
	
	function getAll() {
		$db = new dbHandler;
		$sql = "SELECT * FROM _'pfx'_BBcode";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$i = 0;
		while($row = $db->fetchArray($result)) {
			$BBCode[$i]['BBCodeID'] = $row['BBcodeID'];
			$BBCode[$i]['code'] = $row['code'];
			$BBCode[$i]['result'] = $row['result'];
			$BBCode[$i]['display'] = $row['display'];
			$BBCode[$i]['info'] = $row['info'];
			$BBCode[$i]['accesskey'] = $row['accesskey'];
			$BBCode[$i]['scriptName'] = $row['scriptName'];
			$i++;
		}
		return $BBCode;
	}	

	function add($code, $html, $display, $info, $accesskey) {
		$db = new dbHandler;
		$code = $db->SQLsecure($code);
		$html = $db->SQLsecure($html);
		$display = $db->SQLsecure($display);
		$info = $db->SQLsecure($info);
		$accesskey = $db->SQLsecure($accesskey);
		
		$sql = "INSERT INTO _'pfx'_BBcode(code, result, display, info, accesskey) VALUES ('".$code."','".$html."','".$display."','".$info."','".$accesskey."')";
		$result = $db->runSQL($sql);
	}
	
	function edit($BBCodeID, $code, $html, $display, $info, $accesskey) {
		$db = new dbHandler;
		$BBCodeID = $db->SQLsecure($BBCodeID);
		$code = $db->SQLsecure($code);
		$html = $db->SQLsecure($html);
		$display = $db->SQLsecure($display);
		$info = $db->SQLsecure($info);
		$accesskey = $db->SQLsecure($accesskey);
		
		$sql = "UPDATE _'pfx'_BBcode SET code = '".$code."', result = '".$html."', display = '".$display."', info = '".$info."', accesskey = '".$accesskey."' WHERE BBCodeID = '".$BBCodeID."'";
		$db->runSQL($sql);
	}
	
	function remove($id)
	{
		$db = new dbHandler;
		$db->SQLsecure($id);
		$sql = "DELETE FROM _'pfx'_BBcode WHERE BBCodeID='".$id."'";
		$db->runSQL($sql);
	}
	
	function BBCodeScript($code, $scriptName) {
		include("./include/BBCodeScripts/".$scriptName);
		return $replace;
	}
}
?>