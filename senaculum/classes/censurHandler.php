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

class censurHandler{

	function censurHandler() {}

	function getOne($censurID){	
		$db = new dbHandler();						//Makes databasehandler to db
		$censurID = $db->SQLsecure($censurID);
		$sql = "SELECT * FROM _'pfx'_cencur WHERE censurID='".$censurID."'";	
		$result = $db->runSQL($sql);					//Run the SQL-code
		if($db->numRows($result) == 0)
			return false;
		$row = $db->fetchObject($result);				//Fetch the result
		$censur['censurID'] = $row->censurID;				//Set the data in to an array
		$censur['find'] = $row->find;
		$censur['replace'] = $row->replace;
		$censur['byWord'] = $row->byWord;
		return $censur;
	}
	
	function getAll() {
		$db = new dbHandler();
		$sql = "SELECT * FROM _'pfx'_cencur";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$i = 0;
		while($row = $db->fetchArray($result)) {
			$censur[$i]['censurID'] = $row['censurID'];
			$censur[$i]['find'] = $row['find'];
			$censur[$i]['replace'] = $row['replace'];
			$censur[$i]['byWord'] = $row['byWord'];
			$i++;
		} 
		return $censur;
	}
	function add($find, $replace, $byWord) {
		$db = new dbHandler;
		$find = $db->SQLsecure($find);
		$replace = $db->SQLsecure($replace);
		if($byWord)
		$byWord=1;
		else
		$byWord=0;
		//$sql = "INSERT INTO cencur (find, replace, byWord) VALUES ('".$find."','".$replace."','".$byWord."')";
        $sql = "INSERT INTO _'pfx'_cencur (`find` , `replace` , `byWord` ) VALUES ('".$find."', '".$replace."', '".$byWord."')";
		$result = $db->runSQL($sql);
	}
	
	function edit($censurID, $find, $replace, $byWord) {
		$db = new dbHandler;
		$censurID = $db->SQLsecure($censurID);
		$find = $db->SQLsecure($find);
		$replace = $db->SQLsecure($replace);
		if($byWord)
		$byWord=1;
		else
		$byWord=0;
		$sql = "UPDATE _'pfx'_cencur SET find = '".$find."', `replace` = '".$replace."', byWord = '".$byWord."' WHERE censurID = '".$censurID."'";
		$db->runSQL($sql);
	}
	
	function remove($id)
	{
		$db = new dbHandler;
		$id = $db->SQLsecure($id);
		$sql = "DELETE FROM _'pfx'_cencur WHERE censurID='".$id."'";
		$db->runSQL($sql);
	}
}
?>