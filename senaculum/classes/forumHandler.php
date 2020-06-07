<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once("dbHandler.php");			//Inculdes the databasehandler
require_once("process.php");

class forumHandler {	//Handler the main forum functions
	
	function forumHandler() {}
	
	function add($name,$infoText,$groupID,$memberID) {				//Adds a forum
		$db = new dbHandler();				//Makes databasehandler to db
		$name = $db->SQLsecure($name);
		$infoText = $db->SQLsecure($infoText);
		$groupID = $db->SQLsecure($groupID);
		$memberID = $db->SQLsecure($memberID);
		$sql = "SELECT MAX(sort) AS sort FROM _'pfx'_forums WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$sort = $row->sort;
		$sort = $sort + 1;
		$sql = "INSERT INTO _'pfx'_forums (name,lastEdit,infoText,groupID,sort) VALUES('".$name."','".time()."','".$infoText."','".$groupID."','".$sort."')"; //The SQL-code that inserts the values to the database (name,last edit date infotext and the group-ID to the forum)
		$db->runSQL($sql); // Runs the SQL
		//Get the ID of current forum
		/*
		$sql = "SELECT MAX(forumID) AS forumID FROM _'pfx'_forums WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$row = mysql_fetch_object($result);
		//Insert the moderator
		$sql = "INSERT INTO _'pfx'_memberPermissions(memberID,forumID,`view`,`read`,`thread`,`post`,`edit`,`delete`,`moderator`) VALUES('".$memberID."','".$row->forumID."','1','1','1','1','1','1','1')";
		$db->runSQL($sql); */
	}
	function remove($id) {					//Removes a forum
		$db = new dbHandler();				//Makes databasehandler to db
		$id = $db->SQLsecure($id);
		
		$sql = "SELECT groupID FROM _'pfx'_forums WHERE forumID = '".$id."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$groupID = $row->groupID;
		
		$sql = "SELECT threadID FROM _'pfx'_threads WHERE forumID = '".$id."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) > 0) {
			$sql = "DELETE FROM _'pfx'_posts WHERE threadID IN (";
			for($i=0;$rows = $db->fetchArray($result);$i++) {
				if($i==0)
					$sql .= "'".$rows['threadID']."'";
				else
					$sql .= ",'".$rows['threadID']."'";
			}
			$sql .= ")";
			$db->runSQL($sql);				//Runs the SQL
		}
		$sql = "DELETE FROM _'pfx'_threads WHERE forumID = '".$id."'";
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_forums WHERE forumID = '".$id."'";
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_memberGroupPermissions WHERE forumID = '".$id."'";
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_memberPermissions WHERE forumID = '".$id."'";
		$db->runSQL($sql);
		
		$this->sortInGroup($groupID);
	}
	
	function getAllSimple() {
		$db = new dbHandler;
		$sql = "SELECT forumID, name FROM _'pfx'_forums";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$i = 0;	
		while($row = $db->fetchArray($result)) {
			$forums[$i]['forumID'] = $row['forumID'];
			$forums[$i]['name'] = $row['name'];
			$i++;
		}
		return $forums;
	}
	
	function getAll() {					//Lists a forum
		global $forumVariables;
		global $forumSettings;
		$process = new process;
		$forums = "";
		$db = new dbHandler();				//Makes databasehandler to db
		
		require_once('permissionHandler.php');
		$permissions = new permissionHandler;

		//Get member permissions
		$permission = $permissions->permissions("view");
			
		$processForumName = "";
		$processInfoText = "";
		$processGuestName = "";
			
		//Get forums
		$sql = "SELECT _'pfx'_forums.*, _'pfx'_posts.postID, _'pfx'_posts.madeBy, _'pfx'_posts.threadID, _'pfx'_posts.lastEdit AS lastPostDate, _'pfx'_posts.guestName, _'pfx'_members.memberID, _'pfx'_members.userName, _'pfx'_forumGroups.groupName, _'pfx'_forumGroups.groupID AS forumGroupID, _'pfx'_forumGroups.sort AS groupSort FROM (((_'pfx'_forumGroups LEFT JOIN _'pfx'_forums ON _'pfx'_forumGroups.groupID = _'pfx'_forums.groupID) LEFT JOIN _'pfx'_posts ON _'pfx'_forums.lastPost = _'pfx'_posts.postID) LEFT JOIN _'pfx'_members ON _'pfx'_members.memberID = _'pfx'_posts.madeBy) ORDER BY _'pfx'_forumGroups.sort DESC, _'pfx'_forums.sort DESC";
		$resultForum = $db->runSQL($sql);
		if($db->numRows($resultForum) == 0)
			return false;
		$i = 0;						//Sets the count variable to 0	
		while($rowsForum = $db->fetchArray($resultForum)) {
			$continue = false;	
			foreach($permission as $element) {
				if($element['forumID'] == $rowsForum['forumID'] && !$element['permission'])
					$continue = true;
			}
			if($continue) {		//Hide forum if user not has permission to see it 
				continue;
			}
			
			$forums[$i]['groupID'] = $rowsForum['forumGroupID'];
			$forums[$i]['groupName'] = $rowsForum['groupName'];
			$forums[$i]['groupSort'] = $rowsForum['groupSort'];
			
			$forums[$i]['forumID'] = $rowsForum['forumID'];		//Sets the forumIDs to each group in the three dimentional array
			$forums[$i]['forumName'] = $rowsForum['name'];		//Sets the forumnames to each group in the three dimentional array
			$forums[$i]['forumLastEdit'] = $rowsForum['lastEdit'];	//Sets the forum last edit to each group in the three dimentional array
			$forums[$i]['forumInfoText'] = $rowsForum['infoText'];	//Sets the forum info text to each group in the three dimentional array	
			$forums[$i]['forumGroupID'] = $rowsForum['groupID'];	//Sets the forum group name to each group in the three dimentional array
			$forums[$i]['forumLocked'] = $rowsForum['locked'];
			
			$forums[$i]['forumCountPosts'] = $rowsForum['posts'];
			$forums[$i]['forumCountThreads'] = $rowsForum['threads'];
			
			$forums[$i]['forumLastPost'] = $rowsForum['lastPostDate'];
			if($forums[$i]['forumLastPost'] == "0000-00-00 00:00:00") //If date is null(default value) then put nothing to variable
				$forums[$i]['forumLastPost'] = "";
			$forums[$i]['forumLastPostUsername'] = $rowsForum['userName'];
			$forums[$i]['forumLastPostMemberID'] = $rowsForum['memberID'];
			$forums[$i]['forumLastPostThreadID'] = $rowsForum['threadID'];
			$forums[$i]['forumLastPostGuestName'] = $rowsForum['guestName'];
			$forums[$i]['forumLastPostID'] = $rowsForum['lastPost'];
	
			$processForumName[$i] = $forums[$i]['forumName'];
			$processInfoText[$i] = $forums[$i]['forumInfoText'];
			$processGuestName[$i] = $forums[$i]['forumLastPostGuestName'];
			
			$i++;
		}
		
		if($forumVariables['inlogged']) {
			if($forumSettings['smartNewPosts']) {
				$sqlSmartNewPosts = "SELECT postID FROM _'pfx'_viewedPosts WHERE memberID = '".$forumVariables['inloggedMemberID']."'";
				$resultSmartNewPosts = $db->runSQL($sqlSmartNewPosts);
				while($rowSmartNewPosts = $db->fetchArray($resultSmartNewPosts)) {
					$viewedPosts[] = $rowSmartNewPosts['postID'];
				}
				$sqlNewPostsPosts = "SELECT _'pfx'_posts.postID, _'pfx'_posts.threadID, _'pfx'_posts.lastEdit, _'pfx'_threads.forumID FROM _'pfx'_posts INNER JOIN _'pfx'_threads ON _'pfx'_posts.threadID = _'pfx'_threads.threadID WHERE _'pfx'_posts.lastEdit > '".$forumVariables['lastLoginDate']."'";
				$resultNewPostsPosts = $db->runSQL($sqlNewPostsPosts);
				$k = 0;
				while($rowNewPostsPosts = $db->fetchArray($resultNewPostsPosts)) {
					$newPosts[$k]['postID'] = $rowNewPostsPosts['postID'];
					$newPosts[$k]['threadID'] = $rowNewPostsPosts['threadID'];
					$newPosts[$k]['lastEdit'] = $rowNewPostsPosts['lastEdit'];
					$newPosts[$k]['forumID'] = $rowNewPostsPosts['forumID'];
					$k++;
				}
			}
			$sqlNewPosts = "SELECT _'pfx'_forums.forumID, _'pfx'_forums.groupID, _'pfx'_forums.sort, COUNT( _'pfx'_posts.postID ) AS newPosts FROM _'pfx'_forums INNER JOIN _'pfx'_threads ON _'pfx'_forums.forumID = _'pfx'_threads.forumID INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_posts.lastEdit >  '".$forumVariables['lastLoginDate']."' AND _'pfx'_posts.editedBy != '".$forumVariables['inloggedMemberID']."' GROUP BY _'pfx'_forums.forumID";
			//die($sqlNewPosts);
			$resultNewPosts = $db->runSQL($sqlNewPosts);
			while($rowNewPosts = $db->fetchArray($resultNewPosts)) {
				$i=0;
				foreach($forums as $forumsElements) {
					if($forumsElements['forumID'] == $rowNewPosts['forumID'])
						$forums[$i]['forumNewPosts'] = $rowNewPosts['newPosts'];	
					$i++;		
				}
			}	
		}
		$i=0;
		foreach($forums as $forumElement) {
			if(!isset($forums[$i]['forumNewPosts']))
				$forums[$i]['forumNewPosts'] = 0;
			if(!empty($viewedPosts) && !empty($newPosts)) {
				foreach($newPosts as $newPost) {
					if($newPost['forumID'] == $forums[$i]['forumID'] && in_array($newPost['postID'],$viewedPosts)) {
						if($forums[$i]['forumNewPosts'] != 0)
							$forums[$i]['forumNewPosts']--;
					}
				}		
			}			
			$i++;			
		}
		
		if(!empty($processForumName) && !empty($processInfoText) && !empty($processGuestName)) {
			$processForumName = $process->headline($processForumName);
			$processInfoText = $process->text($processInfoText);
			$processGuestName = $process->name($processGuestName);

			$i = 0;
			foreach($processForumName as $processForumNameElement) {
				$forums[$i]['forumName'] = $processForumNameElement;
				$i++;
			}
			$i = 0;
			foreach($processInfoText as $processInfoTextElement) {
				$forums[$i]['forumInfoText'] = $processInfoTextElement;
				$i++;
			}
			$i = 0;
			foreach($processGuestName as $processGuestNameElement) {
				$forums[$i]['forumLastPostGuestName'] = $processGuestNameElement;
				$i++;
			}
		}
		
		$i=0;
		$j=0;
		$k=1;
		foreach($forums as $forumElement) {
			if($i == 0) {
				$forums2[$j][0]['groupID'] = $forums[$i]['groupID'];
				$forums2[$j][0]['groupName'] = $forums[$i]['groupName'];
				if(!empty($forums[$i]['forumID']))
					$forums2[$j][$k] = $forums[$i];
				$i++;
				$k++; 
			}
			elseif($forums[$i-1]['groupID'] == $forums[$i]['groupID']) {
				$forums2[$j][$k] = $forums[$i];
				$i++;
				$k++;
			}
			else {
				$j++;
				$forums2[$j][0]['groupID'] = $forums[$i]['groupID'];
				$forums2[$j][0]['groupName'] = $forums[$i]['groupName'];
				$k=1;
				if(!empty($forums[$i]['forumID']))
					$forums2[$j][$k] = $forums[$i];
				$i++;
				$k++;
			}
		}
		
		return $forums2; //Returns an three dimentional array 
	}
	
	function getInGroup($groupID) {					//Lists a forums in a group
		global $forumVariables;
		$process = new process;
		$forums = "";
		$db = new dbHandler();				//Makes databasehandler to db
		$groupID = $db->SQLsecure($groupID);
		
		require_once('permissionHandler.php');
		$permissions = new permissionHandler;

		//Get member permissions
		$permission = $permissions->permissions("view");
	
		$i = 0;						//Sets the conuntvariable to 0
		$last = "";				//Variable for forumID in last loop
			
		$processForumName = "";
		$processInfoText = "";
		
		$sql = "SELECT _'pfx'_forums.*, _'pfx'_posts.postID, _'pfx'_posts.madeBy, _'pfx'_posts.threadID, _'pfx'_posts.lastEdit AS lastPostDate, _'pfx'_members.memberID, _'pfx'_members.userName FROM ((_'pfx'_forums LEFT JOIN _'pfx'_posts ON _'pfx'_forums.lastPost = _'pfx'_posts.postID) LEFT JOIN _'pfx'_members ON _'pfx'_members.memberID = _'pfx'_posts.madeBy) WHERE _'pfx'_forums.groupID = '".$groupID."' ORDER BY _'pfx'_forums.sort DESC";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		while($row = $db->fetchArray($result)) {
			$continue = false;	
			foreach($permission as $element) {
				if($element['forumID'] == $row['forumID'] && !$element['permission'])
					$continue = true;
			}
			if($continue) {		//Hide forum if user not has permission to see it 
				continue;
			}
		
			$forums[$i]['forumID'] = $row['forumID'];		//Sets the forumIDs to each group in the three dimentional array
			$forums[$i]['forumName'] = $row['name'];		//Sets the forumnames to each group in the three dimentional array
			$forums[$i]['forumLastEdit'] = $row['lastEdit'];	//Sets the forum last edit to each group in the three dimentional array
			$forums[$i]['forumInfoText'] = $row['infoText'];	//Sets the forum info text to each group in the three dimentional array	
			$forums[$i]['forumGroupID'] = $row['groupID'];	//Sets the forum group name to each group in the three dimentional array
			$forums[$i]['forumLocked'] = $row['locked'];
						
			$forums[$i]['forumCountPosts'] = $row['posts'];
			$forums[$i]['forumCountThreads'] = $row['threads'];
			
			$forums[$i]['forumLastPost'] = $row['lastPostDate'];
			if($forums[$i]['forumLastPost'] == "0000-00-00 00:00:00") //If date is null(default value) then put nothing to variable
				$forums[$i]['forumLastPost'] = "";
			$forums[$i]['forumLastPostUsername'] = $row['userName'];
			$forums[$i]['forumLastPostMemberID'] = $row['memberID'];
			$forums[$i]['forumLastPostThreadID'] = $row['threadID'];
			$forums[$i]['forumLastPostID'] = $row['postID'];
			
			$processForumName[$i] = $forums[$i]['forumName'];
			$processInfoText[$i] = $forums[$i]['forumInfoText'];
			
			$i++;
		}
		if(empty($forums))
			return false;
		
		if($forumVariables['inlogged']) {
			$sqlNewPosts = "SELECT _'pfx'_forums.forumID, COUNT( _'pfx'_posts.postID ) AS newPosts FROM _'pfx'_forums INNER JOIN _'pfx'_threads ON _'pfx'_forums.forumID = _'pfx'_threads.forumID INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_posts.lastEdit > '".$forumVariables['lastLoginDate']."' AND _'pfx'_posts.editedBy != '".$forumVariables['inloggedMemberID']."' GROUP BY _'pfx'_forums.forumID";
			$resultNewPosts = $db->runSQL($sqlNewPosts);
			while($rowNewPosts = $db->fetchArray($resultNewPosts)) {
				$k = 0;
				foreach($forums as $forumsElements) {
					if($forumsElements['forumID'] == $rowNewPosts['forumID'])
						$forums[$k]['forumNewPosts'] = $rowNewPosts['newPosts'];
					$k++;	
				}
			}
		}
		$k = 0;
		foreach($forums as $forumsElements) {
			if(empty($forums[$k]['forumNewPosts']))
				$forums[$k]['forumNewPosts'] = 0;		
			$k++;	
		}	
			
		if(!empty($processForumName) && !empty($processInfoText)) {
			$processForumName = $process->headline($processForumName);
			$processInfoText = $process->text($processInfoText);

			$k = 0;
			foreach($processForumName as $processForumNameElement) {
				$forums[$k]['forumName'] = $processForumNameElement;
				$k++;
			}
			$k = 0;
			foreach($processInfoText as $processInfoTextElement) {
				$forums[$k]['forumInfoText'] = $processInfoTextElement;
				$k++;
			}
		}
		return $forums; //Returns an three dimentional array 
	}
	
	function getOne($forumID, $raw) {
		$process = new process;
		$forum = "";
		$db = new dbHandler();						//Makes databasehandler to db
		$forumID = $db->SQLsecure($forumID);
		
		$sql = "SELECT * FROM _'pfx'_forums INNER JOIN _'pfx'_forumGroups ON _'pfx'_forums.groupID = _'pfx'_forumGroups.groupID WHERE _'pfx'_forums.forumID='".$forumID."'";	//SQL-code that fetch the forum data
		$result = $db->runSQL($sql);					//Run the SQL-code
		$row = $db->fetchObject($result);				//Fetch the result
		$forum['forumID'] = $row->forumID;				//Set the data in to an array
		if(!$raw)
		{
		$forum['infoText'] = $process->text($row->infoText);
		$forum['name'] = $process->headline($row->name);
		}
		else
		{
		$forum['infoText'] = $row->infoText;
		$forum['name'] = $row->name;		
		}
		$forum['lastEdit'] = $row->lastEdit;
		$forum['threads'] = $row->threads;
		$forum['posts'] = $row->posts;
		$forum['lastPost'] = $row->lastPost;
		$forum['groupID'] = $row->groupID;
		$forum['groupName'] = $process->headline($row->groupName);
		$forum['locked'] = $row->locked;
		
		//Get the moderators
		require_once("moderatorHandler.php");
		$moderator = new moderatorHandler;
		$moderators = $moderator->getOne($forumID);
		if($moderators) {
			$i = 0;
			foreach($moderators as $element) {
				$forum['moderators'][$i]['moderatorID'] = $element['memberID'];
				$forum['moderators'][$i]['moderatorName'] = $element['userName'];
				$i++;
			}
		}	
		return $forum;							//Return forum data
	}
	function edit($forumID,$name,$infoText,$groupID,$locked) { //Edit a forum
		$db = new dbHandler();
		$forumID = $db->SQLsecure($forumID);
		$infoText = $db->SQLsecure($infoText);
		$name = $db->SQLsecure($name);
		$groupID = $db->SQLsecure($groupID);
		$locked = $db->SQLsecure($locked);
		
		$sql = "UPDATE _'pfx'_forums SET name='".$name."', infoText='".$infoText."', groupID='".$groupID."', locked='".$locked."' WHERE forumID='".$forumID."'";
		$db->runSQL($sql);
		
		$this->sortInGroup($groupID);
	}
	function moveUp($forumID,$groupID) {
		$db = new dbHandler;
		$forumID = $db->SQLsecure($forumID);
		$groupID = $db->SQLsecure($groupID);
		$sql = "SELECT forumID, sort FROM _'pfx'_forums WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$i = 0;
		while($row = $db->fetchArray($result)) {
			if($row['forumID'] == $forumID) {
				//if($row['sort'] == 0)
					$forumsSorted[$row['sort']+2]['forumID'] = $row['forumID'];
				//else
				//	$forumsSorted[$row['sort']+1]['forumID'] = $row['forumID'];
			}
			else {
				$forums[$i]['forumID'] = $row['forumID'];
				$forums[$i]['sort'] = $row['sort'];
				$i++;
			}
		}
		foreach($forums as $forumsElement) {
			if(empty($forumsSorted[$forumsElement['sort']]['forumID'])) {
				$forumsSorted[$forumsElement['sort']]['forumID'] = $forumsElement['forumID'];
			}
			else {
				$j = 0;
				$done = false;
				while(!$done) {
					if(empty($forumsSorted[$forumsElement['sort']+$j]['forumID'])) {
						$forumsSorted[$forumsElement['sort']+$j]['forumID'] = $forumsElement['forumID'];
						$done = true;
					}
					$j++;
				}
			}
		}
		$forumsCount = count($forumsSorted);
		$j = 0;
		for($i=0;$i<$forumsCount;$i++) {
			while(empty($forumsSorted2[$i]['forumID'])) {
				if(!empty($forumsSorted[$j]['forumID'])) {
					$forumsSorted2[$i]['forumID'] = $forumsSorted[$j]['forumID'];
				}
				$j++;
			}
		}
		//print_r($forumsSorted);
		//die();
		$i = 0;
		foreach($forumsSorted2 as $element) {
			$sql = "UPDATE _'pfx'_forums SET sort='".$i."' WHERE forumID = '".$element['forumID']."'";
			$db->runSQL($sql);
			$i++;
		}
	}

	function moveDown($forumID,$groupID) {
		$db = new dbHandler;
		$forumID = $db->SQLsecure($forumID);
		$groupID = $db->SQLsecure($groupID);
		
		$sql = "SELECT forumID, sort FROM _'pfx'_forums WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$i = 0;
		while($row = $db->fetchArray($result)) {
			if($row['forumID'] == $forumID) {
				if($row['sort'] <= 0)
					$forumsSorted[0]['forumID'] = $row['forumID'];
				else
					$forumsSorted[$row['sort']-1]['forumID'] = $row['forumID'];
			}
			else {
				$forums[$i]['forumID'] = $row['forumID'];
				$forums[$i]['sort'] = $row['sort'];
				$i++;
			}
		}
		foreach($forums as $forumsElement) {
			if(empty($forumsSorted[$forumsElement['sort']]['forumID'])) {
				$forumsSorted[$forumsElement['sort']]['forumID'] = $forumsElement['forumID'];
			}
			else {
				$j = 1;
				$done = false;
				while(!$done) {
					if(empty($forumsSorted[$forumsElement['sort']+$j]['forumID'])) {
						$forumsSorted[$forumsElement['sort']+$j]['forumID'] = $forumsElement['forumID'];
						$done = true;
					}
					$j++;
				}
			}
		}
		$forumsCount = count($forumsSorted);
		$j = 0;
		for($i=0;$i<$forumsCount;$i++) {
			while(empty($forumsSorted2[$i]['forumID'])) {
				if(!empty($forumsSorted[$j]['forumID'])) {
					$forumsSorted2[$i]['forumID'] = $forumsSorted[$j]['forumID'];
				}
				$j++;
			}
		}
		$i = 0;
		foreach($forumsSorted2 as $element) {
			$sql = "UPDATE _'pfx'_forums SET sort='".$i."' WHERE forumID = '".$element['forumID']."'";
			$db->runSQL($sql);
			$i++;
		}
	}
	
	function sortInGroup($groupID) {
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$sql = "SELECT forumID, sort FROM _'pfx'_forums WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$i=0;
		while($row = $db->fetchArray($result)) {
			$forums[$i]['forumID'] = $row['forumID'];
			$forums[$i]['sort'] = $row['sort'];
			if(!isset($max))
				$max = $row['sort'];
			if(!isset($min))
				$min = $row['sort'];
			if($max < $row['sort'])
				$max = $row['sort'];
			if($min > $row['sort'])
				$min = $row['sort'];
			$i++;				 
		}
		$j=0;
		for($i=$min;$i<=$max;$i++) {
			foreach($forums as $forum) {
				if($i == $forum['sort']) {
					$sql = "UPDATE _'pfx'_forums SET sort='".$j."' WHERE forumID = '".$forum['forumID']."'";
					$db->runSQL($sql);
					$j++;
				}	
			}
		}
		
	}
}
?>
