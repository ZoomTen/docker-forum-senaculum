<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
class forumGroupHandler {	//Handler the main forumGroup functions
	
	function formumGroupHandler() {}

	function add($headline) {					   						//Adds a group
		require_once("dbHandler.php");			//Inculdes the databasehandler
		$db = new dbHandler();											//Makes a databasehandler to db
		$headline = $db->SQLsecure($headline);
		$sql = "SELECT MAX(sort) AS sort FROM _'pfx'_forumGroups";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$sort = $row->sort;
		$sort++;
		$sql = "INSERT INTO _'pfx'_forumGroups (groupName,sort) VALUES('".$headline."','".$sort."')"; //The SQL-code that inserts the values to the database
		$db->runSQL($sql); 											   // Runs the SQL
	}
	
	function edit($headline, $groupID) {					   
		require_once("dbHandler.php");			//Inculdes the databasehandler
		$db = new dbHandler();											//Makes a databasehandler to db
		$headline = $db->SQLsecure($headline);
		$groupID = $db->SQLsecure($groupID);
		$sql = "UPDATE _'pfx'_forumGroups SET groupName='".$headline."' WHERE groupID='".$groupID."'"; //The SQL-code that inserts the values to the database
		$db->runSQL($sql); 											   // Runs the SQL
	}
	
	function getAll() {
		require_once("dbHandler.php");			//Inculdes the databasehandler
		$db = new dbHandler;
		$sql = "SELECT * FROM _'pfx'_forumGroups";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		for($i=0;$rows = $db->fetchArray($result); $i++) {
			$groups[$i]['groupID'] = $rows['groupID'];
			$groups[$i]['name'] = $rows['groupName'];
		}
		return $groups;
	}
	
	function getOne($groupID) {
		require_once("dbHandler.php");			//Inculdes the databasehandler	
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$sql = "SELECT * FROM _'pfx'_forumGroups WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$group['groupID'] = $row->groupID;
		$group['name'] = $row->groupName;
		return $group;
	}
	function remove($groupID) {
		require_once("dbHandler.php");			//Inculdes the databasehandler
		require_once("forumHandler.php");
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$forum = new forumHandler;
		$sql = "SELECT forumID FROM _'pfx'_forums WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		while($row = $db->fetchArray($result)) {
			$forum->remove($row['forumID']);
		}
		$sql = "SELECT sort FROM _'pfx'_forumGroups WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result == 0)) 
			return false;
		$row = $db->fetchObject($result);
		$newSort = $row->sort - 1;
		$sql = "SELECT groupID FROM _'pfx'_forumGroups WHERE sort = '".$newSort."'";
		$result = $db->runSQL($sql);
		
		$sql = "DELETE FROM _'pfx'_forumGroups WHERE groupID = '".$groupID."'";
		$db->runSQL($sql);
		
		if(mysql_num_rows($result) != 0) {	
			$row = $db->fetchObject($result);
			$this->moveUp($row->groupID); 
		}
	}
	
	function moveUp($groupID) {
		require_once("dbHandler.php");
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$sql = "SELECT groupID, sort FROM _'pfx'_forumGroups";
		$result = $db->runSQL($sql);
		$i = 0;
		while($row = $db->fetchArray($result)) {
			if($row['groupID'] == $groupID) {
				$groupsSorted[$row['sort']+2]['groupID'] = $row['groupID'];
			}
			else {
				$groups[$i]['groupID'] = $row['groupID'];
				$groups[$i]['sort'] = $row['sort'];
				$i++;
			}
		}
		foreach($groups as $groupsElement) {
			if(empty($groupsSorted[$groupsElement['sort']]['groupID'])) {
				$groupsSorted[$groupsElement['sort']]['groupID'] = $groupsElement['groupID'];
			}
			else {
				$j = 0;
				$done = false;
				while(!$done) {
					if(empty($groupsSorted[$groupsElement['sort']+$j]['groupID'])) {
						$groupsSorted[$groupsElement['sort']+$j]['groupID'] = $groupsElement['groupID'];
						$done = true;
					}
					$j++;
				}
			}
		}
		$groupsCount = count($groupsSorted);
		$j = 0;
		for($i=0;$i<$groupsCount;$i++) {
			while(empty($groupsSorted2[$i]['groupID'])) {
				if(!empty($groupsSorted[$j]['groupID'])) {
					$groupsSorted2[$i]['groupID'] = $groupsSorted[$j]['groupID'];
				}
				$j++;
			}
		}
		//print_r($groupsSorted);
		//die();
		$i = 0;
		foreach($groupsSorted2 as $element) {
			$sql = "UPDATE _'pfx'_forumGroups SET sort='".$i."' WHERE groupID = '".$element['groupID']."'";
			$db->runSQL($sql);
			$i++;
		}
	}
	
	function moveDown($groupID) {
		require_once("dbHandler.php");
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$sql = "SELECT groupID, sort FROM _'pfx'_forumGroups";
		$result = $db->runSQL($sql);
		$i = 0;
		while($row = $db->fetchArray($result)) {
			if($row['groupID'] == $groupID) {
				if($row['sort'] <= 0)
					$groupsSorted[0]['groupID'] = $row['groupID'];
				else
					$groupsSorted[$row['sort']-1]['groupID'] = $row['groupID'];
			}
			else {
				$groups[$i]['groupID'] = $row['groupID'];
				$groups[$i]['sort'] = $row['sort'];
				$i++;
			}
		}
		foreach($groups as $groupsElement) {
			if(empty($groupsSorted[$groupsElement['sort']]['groupID'])) {
				$groupsSorted[$groupsElement['sort']]['groupID'] = $groupsElement['groupID'];
			}
			else {
				$j = 0;
				$done = false;
				while(!$done) {
					if(empty($groupsSorted[$groupsElement['sort']+$j]['groupID'])) {
						$groupsSorted[$groupsElement['sort']+$j]['groupID'] = $groupsElement['groupID'];
						$done = true;
					}
					$j++;
				}
			}
		}
		$groupsCount = count($groupsSorted);
		$j = 0;
		for($i=0;$i<$groupsCount;$i++) {
			while(empty($groupsSorted2[$i]['groupID'])) {
				if(!empty($groupsSorted[$j]['groupID'])) {
					$groupsSorted2[$i]['groupID'] = $groupsSorted[$j]['groupID'];
				}
				$j++;
			}
		}
		//print_r($groupsSorted);
		//die();
		$i = 0;
		foreach($groupsSorted2 as $element) {
			$sql = "UPDATE _'pfx'_forumGroups SET sort='".$i."' WHERE groupID = '".$element['groupID']."'";
			$db->runSQL($sql);
			$i++;
		}
	}
}
?>