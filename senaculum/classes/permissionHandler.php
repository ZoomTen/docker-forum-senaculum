<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
class permissionHandler {
	
	function permissionHandler(){}
	
	function editForumPermissions($forumID, $view, $read, $thread, $post, $edit, $delete ,$sticky, $announce, $vote, $poll, $attach) {		//Edit one or more forumpermissions
		require_once("dbHandler.php");
		$db = new dbHandler;
		
		//All diffrent data is put into one variable, an array
		$typeValues['view'] = $view;
		$typeValues['read'] = $read;
		$typeValues['thread'] = $thread;
		$typeValues['post'] = $post;
		$typeValues['edit'] = $edit;
		$typeValues['delete'] = $delete;
		$typeValues['sticky'] = $sticky;
		$typeValues['announce'] = $announce;
		$typeValues['vote'] = $vote;
		$typeValues['poll'] = $poll;
		$typeValues['attach'] = $attach;
		
		//The diffrent options is put into an array for reduce code
		$options = Array(0 => "all", 1 => "reg", 2 => "pri", 3 => "mod", 4 => "adm");
		
		//The diffrent permissiontypes in put into an array for reduce code
		$types = Array(0 => "view", 1 => "read", 2 => "thread", 3 => "post", 4 => "edit", 5 => "delete", 6 => "sticky", 7 => "announce", 8 => "vote", 9 => "poll", 10 => "attach");
		
		//This is the loop that do the work
		$i = 0;
		foreach($forumID as $currentForumID) { 		//Loops the forums
			$currentForumID = $db->SQLsecure($currentForumID);
			foreach($types as $type) {				//Loops the diffrent permissiontypes
				$type = $db->SQLsecure($type);
				switch($typeValues[$type][$i]) {	//Checks option for current permissiontype
					case "all":
						$sql = "UPDATE _'pfx'_forums SET `outlogged".ucfirst($type)."` = '1', `inlogged".ucfirst($type)."` = '1' WHERE forumID = '".$currentForumID."'";
						$db->runSQL($sql);
						break;
					case "reg":
						$sql = "UPDATE _'pfx'_forums SET `outlogged".ucfirst($type)."` = '0', `inlogged".ucfirst($type)."` = '1' WHERE forumID = '".$currentForumID."'";
						$db->runSQL($sql);
						break;	
					case "pri":
						$sql = "UPDATE _'pfx'_forums SET `outlogged".ucfirst($type)."` = '0', `inlogged".ucfirst($type)."` = '0' WHERE forumID = '".$currentForumID."'";
						$db->runSQL($sql);
						break;
					case "mod":
						$sql = "UPDATE _'pfx'_forums SET `outlogged".ucfirst($type)."` = '0', `inlogged".ucfirst($type)."` = '0' WHERE forumID = '".$currentForumID."'";
						$db->runSQL($sql);
						
						$sql = "UPDATE _'pfx'_memberPermissions SET `".$type."` = '0' WHERE forumID = '".$currentForumID."' AND moderator = '0'";
						$db->runSQL($sql);
						
						$sql = "UPDATE _'pfx'_memberGroupPermissions SET `".$type."` = '0' WHERE forumID = '".$currentForumID."' AND moderator = '0'";
						$db->runSQL($sql);
						
						$sql = "UPDATE _'pfx'_memberPermissions SET `".$type."` = '1' WHERE forumID = '".$currentForumID."' AND moderator = '1'";
						$db->runSQL($sql);
						
						$sql = "UPDATE _'pfx'_memberGroupPermissions SET `".$type."` = '1' WHERE forumID = '".$currentForumID."' AND moderator = '1'";
						$db->runSQL($sql);
						break;
					case "adm":
						$sql = "UPDATE _'pfx'_forums SET `outlogged".ucfirst($type)."` = '0', `inlogged".ucfirst($type)."` = '0' WHERE forumID = '".$currentForumID."'";
						$db->runSQL($sql);
						
						$sql = "UPDATE _'pfx'_memberPermissions SET `".$type."` = '0' WHERE forumID = '".$currentForumID."'";
						$db->runSQL($sql);
						
						$sql = "UPDATE _'pfx'_memberGroupPermissions SET `".$type."` = '0' WHERE forumID = '".$currentForumID."'";
						$db->runSQL($sql);
						break;
				}				
			}
			$i++;
		}
	}
	
	function editMemberGroupPermissions($groupID, $forumID, $view, $read, $thread, $post, $edit, $delete, $sticky , $announce, $vote, $poll, $attach, $moderator) {
		require_once('dbHandler.php');
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		
		$i=0;
		foreach($forumID as $currentForumID) {
			if($view[$i])
				$view[$i] = 1;
			else
				$view[$i] = 0;
			if($read[$i])
				$read[$i] = 1;
			else
				$read[$i] = 0;
			if($thread[$i])
				$thread[$i] = 1;
			else
				$thread[$i] = 0;
			if($post[$i])
				$post[$i] = 1;
			else
				$post[$i] = 0;
			if($edit[$i])
				$edit[$i] = 1;
			else
				$edit[$i] = 0;
			if($delete[$i])
				$delete[$i] = 1;
			else
				$delete[$i] = 0;
			if($moderator[$i])
				$moderator[$i] = 1;
			else
				$moderator[$i] = 0;
			if($sticky[$i])
				$sticky[$i] = 1;
			else
				$sticky[$i] = 0;
			if($announce[$i])
				$announce[$i] = 1;
			else
				$announce[$i] = 0;
			if($vote[$i])
				$vote[$i] = 1;
			else
				$vote[$i] = 0;		
			if($poll[$i])
				$poll[$i] = 1;
			else
				$poll[$i] = 0;
			if($attach[$i])
				$attach[$i] = 1;
			else
				$attach[$i] = 0;																						
			$i++;
		}
		
		$permissions = $this->getAllMemberGroupPermissionsUnsetSet($groupID);
		
		foreach($permissions as $permission) {
			$i=0;
			foreach($forumID as $currentForumID) {
				$currentForumID = $db->SQLsecure($currentForumID);
				if($permission['set']) {
					if($permission['forumID'] == $forumID[$i]) {
						$sql = "UPDATE _'pfx'_memberGroupPermissions SET `view` = '".$view[$i]."', `read` = '".$read[$i]."', thread = '".$thread[$i]."', `post` = '".$post[$i]."', `edit` = '".$edit[$i]."', `delete` = '".$delete[$i]."', `sticky` = '".$sticky[$i]."', `announce` = '".$announce[$i]."', `vote` = '".$vote[$i]."', `poll` = '".$poll[$i]."', `attach` = '".$attach[$i]."', `moderator` = '".$moderator[$i]."' WHERE memberGroupID = '".$groupID."' AND forumID = '".$forumID[$i]."'";
						$db->runSQL($sql);
					}
				}
				else {
					if($permission['forumID'] == $forumID[$i]) {
						if($view[$i] != $permission['view'] || $read[$i] != $permission['read'] || $thread[$i] != $permission['thread'] || $post[$i] != $permission['post'] || $edit[$i] != $permission['edit'] || $delete[$i] != $permission['delete'] || $sticky[$i] != $permission['sticky'] || $announce[$i] != $permission['announce'] || $vote[$i] != $permission['vote'] || $poll[$i] != $permission['poll'] || $attach[$i] != $permission['attach'] || $moderator[$i] != $permission['moderator']) {
							$sql = "INSERT INTO _'pfx'_memberGroupPermissions (`forumID`, `memberGroupID`, `view`, `read`, `thread`, `post`, `edit`, `delete`, `sticky`, `announce`, `vote`, `poll`, `attach`, `moderator`) VALUES('".$forumID[$i]."','".$groupID."','".$view[$i]."','".$read[$i]."','".$thread[$i]."','".$post[$i]."','".$edit[$i]."','".$delete[$i]."', '".$sticky[$i]."', '".$announce[$i]."', '".$vote[$i]."', '".$poll[$i]."', '".$attach[$i]."', '".$moderator[$i]."')"; 
							$db->runSQL($sql);
						}
					}
				}
				$i++;
			}
		}	
	}
	
	function editMemberPermissions($forumID, $memberID, $view, $read, $thread, $post, $edit, $delete, $sticky, $announce, $vote, $poll, $attach, $moderator) {
		require_once('dbHandler.php');
		$db = new dbHandler;
		$memberID = $db->SQLsecure($memberID);
		
		$i=0;
		foreach($forumID as $currentForumID) {
			if($view[$i])
				$view[$i] = 1;
			else
				$view[$i] = 0;
			if($read[$i])
				$read[$i] = 1;
			else
				$read[$i] = 0;
			if($thread[$i])
				$thread[$i] = 1;
			else
				$thread[$i] = 0;
			if($post[$i])
				$post[$i] = 1;
			else
				$post[$i] = 0;
			if($edit[$i])
				$edit[$i] = 1;
			else
				$edit[$i] = 0;
			if($delete[$i])
				$delete[$i] = 1;
			else
				$delete[$i] = 0;
			if($moderator[$i])
				$moderator[$i] = 1;
			else
				$moderator[$i] = 0;
			if($sticky[$i])
				$sticky[$i] = 1;
			else
				$sticky[$i] = 0;
			if($announce[$i])
				$announce[$i] = 1;
			else
				$announce[$i] = 0;	
			if($vote[$i])
				$vote[$i] = 1;
			else
				$vote[$i] = 0;		
			if($poll[$i])
				$poll[$i] = 1;
			else
				$poll[$i] = 0;
			if($attach[$i])
				$attach[$i] = 1;
			else
				$attach[$i] = 0;																				
			$i++;
		}
		
		$permissions = $this->getAllMemberPermissionsUnsetSet($memberID);
		
		foreach($permissions as $permission) {
			$i=0;
			foreach($forumID as $currentForumID) {
				$currentForumID = $db->SQLsecure($currentForumID);
				if($permission['set']) {
					if($permission['forumID'] == $forumID[$i]) {
						$sql = "UPDATE _'pfx'_memberPermissions SET `view` = '".$view[$i]."', `read` = '".$read[$i]."', thread = '".$thread[$i]."', `post` = '".$post[$i]."', `edit` = '".$edit[$i]."', `delete` = '".$delete[$i]."', `sticky` = '".$sticky[$i]."', `announce` = '".$announce[$i]."', `vote` = '".$vote[$i]."', `poll` = '".$poll[$i]."', `attach` = '".$attach[$i]."', `moderator` = '".$moderator[$i]."' WHERE memberID = '".$memberID."' AND forumID = '".$forumID[$i]."'";
						$db->runSQL($sql);
					}
				}
				else {
					if($permission['forumID'] == $forumID[$i]) {
						if($view[$i] != $permission['view'] || $read[$i] != $permission['read'] || $thread[$i] != $permission['thread'] || $post[$i] != $permission['post'] || $edit[$i] != $permission['edit'] || $delete[$i] != $permission['delete'] || $sticky[$i] != $permission['sticky'] || $announce[$i] != $permission['announce'] || $vote[$i] != $permission['vote'] || $poll[$i] != $permission['poll'] || $attach[$i] != $permission['attach'] || $moderator[$i] != $permission['moderator']) {
							$sql = "INSERT INTO _'pfx'_memberPermissions (`forumID`, `memberID`, `view`, `read`, `thread`, `post`, `edit`, `delete`, `sticky`, `announce`, `vote`, `poll`, `attach`, `moderator`) VALUES('".$forumID[$i]."','".$memberID."','".$view[$i]."','".$read[$i]."','".$thread[$i]."','".$post[$i]."','".$edit[$i]."','".$delete[$i]."','".$sticky[$i]."','".$announce[$i]."','".$vote[$i]."','".$poll[$i]."','".$attach[$i]."','".$moderator[$i]."')"; 
							$db->runSQL($sql);
						}
					}
				}
				$i++;
			}
		}	
	}
	
	function removeMemberGroupPermission($groupID,$forumID) {
		require_once("dbHandler.php");
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		if(is_array($forumID)) {
			$i = 0;
			foreach($forumID as $element) {
				$element = $db->SQLsecure($element);
				$sql = "DELETE FROM _'pfx'_memberGroupPermissions WHERE memberGroupID = '".$groupID."' AND forumID = '".$forumID[$i]."'";
				$db->runSQL($sql);
				$i++;
				//die($sql);
			}
		}
		else {
			$forumID = $db->SQLsecure($forumID);
			$sql = "DELETE FROM _'pfx'_memberGroupPermissions WHERE memberGroupID = '".$groupID."' AND forumID = '".$forumID."'";
			$db->runSQL($sql);
		}		
	}
	
	function removeMemberPermission($memberID,$forumID) {
		require_once("dbHandler.php");
		$db = new dbHandler;
		$memberID = $db->SQLsecure($memberID);
		if(is_array($forumID)) {
			$i = 0;
			foreach($forumID as $element) {
				$sql = "DELETE FROM _'pfx'_memberPermissions WHERE memberID = '".$memberID."' AND forumID = '".$db->SQLsecure($forumID[$i])."'";
				$db->runSQL($sql);
				$i++;
				//die($sql);
			}
		}
		else {
			$forumID = $db->SQLsecure($forumID);
			$sql = "DELETE FROM _'pfx'_memberPermissions WHERE memberID = '".$memberID."' AND forumID = '".$forumID."'";
			$db->runSQL($sql);
		}		
	}
	
	function getAllMemberGroupPermissions($memberID) {
		require_once('dbHandler.php');
		$db = new dbHandler;
		$memberID = $db->SQLsecure($memberID);
		global $getAllMemberGroupPermissionsAnswer;
		if(isset($getAllMemberGroupPermissionsAnswer[$memberID]))
			return $getAllMemberGroupPermissionsAnswer[$memberID];
		$sql = "SELECT _'pfx'_memberGroupPermissions.* , _'pfx'_memberGroupsRelation.memberID FROM _'pfx'_memberGroupPermissions INNER JOIN _'pfx'_memberGroupsRelation ON _'pfx'_memberGroupPermissions.memberGroupID = _'pfx'_memberGroupsRelation.groupID WHERE _'pfx'_memberGroupsRelation.memberID = '".$memberID."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0) {
			$getAllMemberGroupPermissionsAnswer[$memberID] = false;
			return false;
		}	
		for($i=0;$row = $db->fetchArray($result); $i++) {
			$permissions[$i]['forumID'] = $row['forumID'];
			$permissions[$i]['memberGroupID'] = $row['memberGroupID'];
			$permissions[$i]['view'] = $row['view'];
			$permissions[$i]['read'] = $row['read'];
			$permissions[$i]['thread'] = $row['thread'];
			$permissions[$i]['post'] = $row['post'];
			$permissions[$i]['edit'] = $row['edit'];
			$permissions[$i]['delete'] = $row['delete'];
			$permissions[$i]['sticky'] = $row['sticky'];
			$permissions[$i]['announce'] = $row['announce'];
			$permissions[$i]['vote'] = $row['vote'];
			$permissions[$i]['poll'] = $row['poll'];
			$permissions[$i]['attach'] = $row['attach'];
			$permissions[$i]['moderator'] = $row['moderator'];
		}
		$getAllMemberGroupPermissionsAnswer[$memberID] = $permissions;
		return $permissions;
	}
	
	function getAllMemberGroupPermissionsUnsetSet($groupID) {
		require_once('dbHandler.php');
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$sql = "SELECT * FROM _'pfx'_memberGroupPermissions WHERE memberGroupID = '".$groupID."'";
		$resultMemberGroup = $db->runSQL($sql);
		$sql = "SELECT * FROM _'pfx'_forums";
		$resultForum = $db->runSQL($sql);
		if($db->numRows($resultMemberGroup) != 0) {	
			for($i=0;$row = $db->fetchArray($resultMemberGroup); $i++) {
				$permissions[$i]['forumID'] = $row['forumID'];
				$permissions[$i]['view'] = $row['view'];
				$permissions[$i]['read'] = $row['read'];
				$permissions[$i]['thread'] = $row['thread'];
				$permissions[$i]['post'] = $row['post'];
				$permissions[$i]['edit'] = $row['edit'];
				$permissions[$i]['delete'] = $row['delete'];
				$permissions[$i]['sticky'] = $row['sticky'];
				$permissions[$i]['announce'] = $row['announce'];
				$permissions[$i]['vote'] = $row['vote'];
				$permissions[$i]['poll'] = $row['poll'];
				$permissions[$i]['attach'] = $row['attach'];
				$permissions[$i]['moderator'] = $row['moderator'];
				$permissions[$i]['set'] = true;
			}
			$i = count($permissions);
			while($row = $db->fetchArray($resultForum)) {
				$set = false;
				$k=0;
				foreach($permissions as $permission) {
					if($row['forumID'] == $permission['forumID']) {
						$permissions[$k]['forumName'] = $row['name'];
						$set = true;
						break;
					}
					$k++;	
				}
				if(!$set) {
					$permissions[$i]['forumID'] = $row['forumID'];
					$permissions[$i]['forumName'] = $row['name'];
					$permissions[$i]['view'] = $row['inloggedView'];
					$permissions[$i]['read'] = $row['inloggedRead'];
					$permissions[$i]['thread'] = $row['inloggedThread'];
					$permissions[$i]['post'] = $row['inloggedPost'];
					$permissions[$i]['edit'] = $row['inloggedEdit'];
					$permissions[$i]['delete'] = $row['inloggedDelete'];
					$permissions[$i]['sticky'] = $row['inloggedSticky'];
					$permissions[$i]['announce'] = $row['inloggedAnnounce'];
					$permissions[$i]['vote'] = $row['inloggedVote'];
					$permissions[$i]['poll'] = $row['inloggedPoll'];
					$permissions[$i]['attach'] = $row['inloggedAttach'];
					$permissions[$i]['moderator'] = $row['inloggedModerator'];
					$permissions[$i]['set'] = false;
					$i++;
				}
			}
		}
		else {
			$i=0;
			while($row = $db->fetchArray($resultForum)) {
				$permissions[$i]['forumID'] = $row['forumID'];
				$permissions[$i]['forumName'] = $row['name'];
				$permissions[$i]['view'] = $row['inloggedView'];
				$permissions[$i]['read'] = $row['inloggedRead'];
				$permissions[$i]['thread'] = $row['inloggedThread'];
				$permissions[$i]['post'] = $row['inloggedPost'];
				$permissions[$i]['edit'] = $row['inloggedEdit'];
				$permissions[$i]['delete'] = $row['inloggedDelete'];
				$permissions[$i]['sticky'] = $row['inloggedSticky'];
				$permissions[$i]['announce'] = $row['inloggedAnnounce'];
				$permissions[$i]['vote'] = $row['inloggedVote'];
				$permissions[$i]['poll'] = $row['inloggedPoll'];
				$permissions[$i]['attach'] = $row['inloggedAttach'];
				$permissions[$i]['moderator'] = $row['inloggedModerator'];
				$permissions[$i]['set'] = false;
				$i++;
			}	
		}	
		return $permissions;
	}
	
	function getAllMemberPermissions($memberID) {
		require_once('dbHandler.php');
		$db = new dbHandler;
		$memberID = $db->SQLsecure($memberID);
	
		global $getAllMemberPermissionsAnswer;
		if(isset($getAllMemberPermissionsAnswer[$memberID]))
			return $getAllMemberPermissionsAnswer[$memberID];
				
		$sql = "SELECT * FROM _'pfx'_memberPermissions WHERE memberID = '".$memberID."'";
		$result = $db->runSQL($sql);

		if($db->numRows($result) == 0) {
			$getAllMemberPermissionsAnswer[$memberID] = false;
			return false;
		}	
		for($i=0;$row = $db->fetchArray($result); $i++) {
			$permissions[$i]['forumID'] = $row['forumID'];
			$permissions[$i]['memberID'] = $row['memberID'];
			$permissions[$i]['view'] = $row['view'];
			$permissions[$i]['read'] = $row['read'];
			$permissions[$i]['thread'] = $row['thread'];
			$permissions[$i]['post'] = $row['post'];
			$permissions[$i]['edit'] = $row['edit'];
			$permissions[$i]['delete'] = $row['delete'];
			$permissions[$i]['sticky'] = $row['sticky'];
			$permissions[$i]['announce'] = $row['announce'];
			$permissions[$i]['vote'] = $row['vote'];
			$permissions[$i]['poll'] = $row['poll'];
			$permissions[$i]['attach'] = $row['attach'];
			$permissions[$i]['moderator'] = $row['moderator'];
		}
		$getAllMemberPermissionsAnswer[$memberID] = $permissions;
		return $permissions;
	}
	
	function getAllMemberPermissionsUnsetSet($memberID) {
		require_once('dbHandler.php');
		$db = new dbHandler;
		$memberID = $db->SQLsecure($memberID);
		$sql = "SELECT * FROM _'pfx'_memberPermissions WHERE memberID = '".$memberID."'";
		$resultMember = $db->runSQL($sql);
		$sql = "SELECT * FROM _'pfx'_forums";
		$resultForum = $db->runSQL($sql);
		if($db->numRows($resultForum) == 0)
			return false;
		if($db->numRows($resultMember) != 0) {	
			for($i=0;$row = $db->fetchArray($resultMember); $i++) {
				$permissions[$i]['forumID'] = $row['forumID'];
				$permissions[$i]['view'] = $row['view'];
				$permissions[$i]['read'] = $row['read'];
				$permissions[$i]['thread'] = $row['thread'];
				$permissions[$i]['post'] = $row['post'];
				$permissions[$i]['edit'] = $row['edit'];
				$permissions[$i]['delete'] = $row['delete'];
				$permissions[$i]['sticky'] = $row['sticky'];
				$permissions[$i]['announce'] = $row['announce'];
				$permissions[$i]['vote'] = $row['vote'];
				$permissions[$i]['poll'] = $row['poll'];
				$permissions[$i]['attach'] = $row['attach'];
				$permissions[$i]['moderator'] = $row['moderator'];
				$permissions[$i]['set'] = true;
			}
			$i = count($permissions);
			while($row = $db->fetchArray($resultForum)) {
				$set = false;
				$k=0;
				foreach($permissions as $permission) {
					if($row['forumID'] == $permission['forumID']) {
						$permissions[$k]['forumName'] = $row['name'];
						$set = true;
						break;
					}
					$k++;	
				}
				if(!$set) {
					$permissions[$i]['forumID'] = $row['forumID'];
					$permissions[$i]['forumName'] = $row['name'];
					$permissions[$i]['view'] = $row['inloggedView'];
					$permissions[$i]['read'] = $row['inloggedRead'];
					$permissions[$i]['thread'] = $row['inloggedThread'];
					$permissions[$i]['post'] = $row['inloggedPost'];
					$permissions[$i]['edit'] = $row['inloggedEdit'];
					$permissions[$i]['delete'] = $row['inloggedDelete'];
					$permissions[$i]['sticky'] = $row['inloggedSticky'];
					$permissions[$i]['announce'] = $row['inloggedAnnounce'];
					$permissions[$i]['vote'] = $row['inloggedVote'];
					$permissions[$i]['poll'] = $row['inloggedPoll'];
					$permissions[$i]['attach'] = $row['inloggedAttach'];
					$permissions[$i]['moderator'] = $row['inloggedModerator'];
					$permissions[$i]['set'] = false;
					$i++;
				}
			}
		}
		else {
			$i=0;
			while($row = $db->fetchArray($resultForum)) {
				$permissions[$i]['forumID'] = $row['forumID'];
				$permissions[$i]['forumName'] = $row['name'];
				$permissions[$i]['view'] = $row['inloggedView'];
				$permissions[$i]['read'] = $row['inloggedRead'];
				$permissions[$i]['thread'] = $row['inloggedThread'];
				$permissions[$i]['post'] = $row['inloggedPost'];
				$permissions[$i]['edit'] = $row['inloggedEdit'];
				$permissions[$i]['delete'] = $row['inloggedDelete'];
				$permissions[$i]['sticky'] = $row['inloggedSticky'];
				$permissions[$i]['announce'] = $row['inloggedAnnounce'];
				$permissions[$i]['vote'] = $row['inloggedVote'];
				$permissions[$i]['poll'] = $row['inloggedPoll'];
				$permissions[$i]['attach'] = $row['inloggedAttach'];
				$permissions[$i]['moderator'] = $row['inloggedModerator'];
				$permissions[$i]['set'] = false;
				$i++;
			}	
		}	
		return $permissions;
	}
	
	function getForumPermissions($forumID) {
		if(empty($forumID)) 
			return false;
		if(!is_array($forumID))
			$forumID[0] = $forumID;		
		require_once("dbHandler.php");
		$db = new dbHandler;
		$sql = "SELECT * FROM _'pfx'_forums WHERE ";
		$i=0;
		foreach($forumID as $currentForumID) {
			$currentForumID = $db->SQLsecure($currentForumID);
			if($i == 0)
				$sql .= "forumID = '".$currentForumID."'";
			else 
				$sql .= " OR forumID = '".$currentForumID."'";
			$i++;		
		}
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		
		$otherPermissions['forumID'][0] = "";
		$otherPermissions['view'][0] = "";
		$otherPermissions['read'][0] = "";
		$otherPermissions['thread'][0] = "";
		$otherPermissions['post'][0] = "";
		$otherPermissions['edit'][0] = "";
		$otherPermissions['delete'][0] = "";
		$otherPermissions['sticky'][0] = "";
		$otherPermissions['announce'][0] = "";
		$otherPermissions['vote'][0] = "";
		$otherPermissions['poll'][0] = "";
		$otherPermissions['attach'][0] = "";
		$otherPermissions['moderator'][0] = "";
		
		//Fetch forums with memberPermissions
		$sql = "SELECT * FROM _'pfx'_memberPermissions WHERE ";
		$i=0;
		foreach($forumID as $currentForumID) {
			if($i == 0)
				$sql .= "forumID = '".$currentForumID."'";
			else 
				$sql .= " OR forumID = '".$currentForumID."'";
			$i++;		
		}
		$memberResult = $db->runSQL($sql);
		$j = 0;
		if($db->numRows($memberResult) != 0) {
			while($row = $db->fetchArray($memberResult)) {
				$otherPermissions['forumID'][$j] = $row['forumID'];
				$otherPermissions['view'][$j] = $row['view'];
				$otherPermissions['read'][$j] = $row['read'];
				$otherPermissions['thread'][$j] = $row['thread'];
				$otherPermissions['post'][$j] = $row['post'];
				$otherPermissions['edit'][$j] = $row['edit'];
				$otherPermissions['delete'][$j] = $row['delete'];
				$otherPermissions['sticky'][$j] = $row['sticky'];
				$otherPermissions['announce'][$j] = $row['announce'];
				$otherPermissions['vote'][$j] = $row['vote'];
				$otherPermissions['poll'][$j] = $row['poll'];
				$otherPermissions['attach'][$j] = $row['attach'];
				$otherPermissions['moderator'][$j] = $row['moderator'];
				$j++;
			}
		}
		
		//Fetch forums width memberGroupPermissions
		$sql = "SELECT * FROM _'pfx'_memberGroupPermissions WHERE ";
		$i=0;
		foreach($forumID as $currentForumID) {
			if($i == 0)
				$sql .= "forumID = '".$currentForumID."'";
			else 
				$sql .= " OR forumID = '".$currentForumID."'";
			$i++;		
		}
		$memberGroupResult = $db->runSQL($sql);
		if($db->numRows($memberGroupResult) != 0) {
			while($row = $db->fetchArray($memberGroupResult)) {
				$otherPermissions['forumID'][$j] = $row['forumID'];
				$otherPermissions['view'][$j] = $row['view'];
				$otherPermissions['read'][$j] = $row['read'];
				$otherPermissions['thread'][$j] = $row['thread'];
				$otherPermissions['post'][$j] = $row['post'];
				$otherPermissions['edit'][$j] = $row['edit'];
				$otherPermissions['delete'][$j] = $row['delete'];
				$otherPermissions['sticky'][$j] = $row['sticky'];
				$otherPermissions['announce'][$j] = $row['announce'];
				$otherPermissions['vote'][$j] = $row['vote'];
				$otherPermissions['poll'][$j] = $row['poll'];
				$otherPermissions['attach'][$j] = $row['attach'];
				$otherPermissions['moderator'][$j] = $row['moderator'];
				$j++;
			}
		}
		//print_r($otherPermissions);
		//die();
		$types = Array(0 => "view", 1 => "read", 2 => "thread", 3 => "post", 4 => "edit", 5 => "delete", 6 => "sticky", 7 => "announce", 8 => "vote", 9 => "poll", 10 => "attach");			
		$i = 0;	
		while($row = $db->fetchArray($result)) {
			$permissions[$i]['forumID'] = $row['forumID'];
			$permissions[$i]['forumName'] = $row['name'];
			foreach($types as $type) {
				if($row['outlogged'.ucfirst($type)])
					$permissions[$i][$type] = "all";
				elseif($row['inlogged'.ucfirst($type)])
					$permissions[$i][$type] = "reg";
				elseif(in_array($row['forumID'],$otherPermissions['forumID']) || in_array($row['forumID'],$otherPermissions['forumID'])) {
					$k=0;
					foreach($otherPermissions['forumID'] as $otherPermission) {
						if($row['forumID'] == $otherPermission) {
							if($otherPermissions['moderator'][$k] && $otherPermissions[$type][$k]) {
								$permissions[$i][$type] = "mod";
								break;
							}
							elseif($otherPermissions[$type][$k]) {
								$permissions[$i][$type] = "pri";
								break;
							}
							else {
								$permissions[$i][$type] = "adm";
								break;
							}						
						}
						$k++;
					}
				}
				else
					$permissions[$i][$type] = "adm";
			}			 
			$i++;
		}
		return $permissions;
	}
	
	function getOneMemberGroupPermissions($forumID,$memberID) {
		require_once('dbHandler.php');
		$db = new dbHandler;
		$forumID = $db->SQLsecure($forumID);
		$memberID = $db->SQLsecure($memberID);
		
		global $getOneMemberGroupPermissionsAnswer;
		if(isset($getOneMemberGroupPermissionsAnswer[$forumID][$memberID]))
			return $getOneMemberGroupPermissionsAnswer[$forumID][$memberID];
			
		$sql = "SELECT _'pfx'_memberGroupPermissions.* , _'pfx'_memberGroupsRelation.memberID FROM _'pfx'_memberGroupPermissions INNER JOIN _'pfx'_memberGroupsRelation ON _'pfx'_memberGroupPermissions.memberGroupID = _'pfx'_memberGroupsRelation.groupID WHERE _'pfx'_memberGroupPermissions.forumID = '".$forumID."' AND _'pfx'_memberGroupsRelation.memberID = '".$memberID."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0){
			return false;
		}
		$i=0;
		while($row = $db->fetchArray($result)) {
			$permissions[$i]['forumID'] = $row['forumID'];
			$permissions[$i]['memberGroupID'] = $row['memberGroupID'];
			$permissions[$i]['view'] = $row['view'];
			$permissions[$i]['read'] = $row['read'];
			$permissions[$i]['thread'] = $row['thread'];
			$permissions[$i]['post'] = $row['post'];
			$permissions[$i]['edit'] = $row['edit'];
			$permissions[$i]['delete'] = $row['delete'];
			$permissions[$i]['sticky'] = $row['sticky'];
			$permissions[$i]['announce'] = $row['announce'];
			$permissions[$i]['vote'] = $row['vote'];
			$permissions[$i]['poll'] = $row['poll'];
			$permissions[$i]['attach'] = $row['attach'];
			$permissions[$i]['moderator'] = $row['moderator'];
			$i++;
		}
		
		$getOneMemberGroupPermissionsAnswer[$forumID][$memberID] = $permissions;

		return $permissions;
	}
	
	function getOneMemberPermissions($forumID,$memberID) {
		require_once('dbHandler.php');
		$db = new dbHandler;
		$forumID = $db->SQLsecure($forumID);
		$memberID = $db->SQLsecure($memberID);
	
		global $getOneMemberPermissionsAnswer;
		if(isset($getOneMemberPermissionsAnswer[$forumID][$memberID]))
			return $getOneMemberPermissionsAnswer[$forumID][$memberID];
			
		$sql = "SELECT * FROM _'pfx'_memberPermissions WHERE forumID = '".$forumID."' AND memberID = '".$memberID."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$row = $db->fetchObject($result);
		
		$permissions['forumID'] = $row->forumID;
		$permissions['memberID'] = $row->memberID;
		$permissions['view'] = $row->view;
		$permissions['read'] = $row->read;
		$permissions['thread'] = $row->thread;
		$permissions['post'] = $row->post;
		$permissions['edit'] = $row->edit;
		$permissions['delete'] = $row->delete;
		$permissions['sticky'] = $row->sticky;
		$permissions['announce'] = $row->announce;
		$permissions['vote'] = $row->vote;
		$permissions['poll'] = $row->poll;
		$permissions['attach'] = $row->attach;
		$permissions['moderator'] = $row->moderator;
		
		$getOneMemberPermissionsAnswer[$forumID][$memberID] = $permissions;
		
		return $permissions;
	}

function permission($forumID,$type) {
		global $forumVariables;
		global $permissionRow;

		require_once('dbHandler.php');
		$db = new dbHandler;
		$forumID = $db->SQLsecure($forumID);
		
		require_once('logInOutHandler.php');
		$auth = new logInOutHandler;
		if($forumVariables['inlogged']) {
			$permission = $this->getOneMemberPermissions($forumID,$forumVariables['inloggedMemberID']);
			$permissionGroup = $this->getOneMemberGroupPermissions($forumID,$forumVariables['inloggedMemberID']);
		}
		//$permission = $this->getAllMemberPermissions();
		//$permissionGroup = $this->getAllMemberGroupPermissions();
		
		if(isset($permissionRow[$forumID])) 
			$row = $permissionRow[$forumID];
		else {	
			$sql = "SELECT * FROM _'pfx'_forums WHERE forumID = '".$forumID."'";
			$result = $db->runSQL($sql);
			$permissionRow[$forumID] = $row = $db->fetchObject($result);
		}
		$done = false;
		while(!$done) {
			switch($type) {
				case "view":
					$outloggedType = $row->outloggedView;
					$inloggedType = $row->inloggedView;
					break;
				case "read":
					$outloggedType = $row->outloggedRead;
					$inloggedType = $row->inloggedRead;
					break;
				case "thread":
					$outloggedType = $row->outloggedThread;
					$inloggedType = $row->inloggedThread;
					break;
				case "post":
					$outloggedType = $row->outloggedPost;
					$inloggedType = $row->inloggedPost;
					break;
				case "edit":
					$outloggedType = $row->outloggedEdit;
					$inloggedType = $row->inloggedEdit;
					break;
				case "delete":
					$outloggedType = $row->outloggedDelete;
					$inloggedType = $row->inloggedDelete;
					break;
				case "sticky":
					$outloggedType = $row->outloggedSticky;
					$inloggedType = $row->inloggedSticky;
					break;
				case "announce":
					$outloggedType = $row->outloggedAnnounce;
					$inloggedType = $row->inloggedAnnounce;
					break;
				case "vote":
					$outloggedType = $row->outloggedVote;
					$inloggedType = $row->inloggedVote;
					break;		
				case "poll":
					$outloggedType = $row->outloggedPoll;
					$inloggedType = $row->inloggedPoll;
					break;
				case "attach":
					$outloggedType = $row->outloggedAttach;
					$inloggedType = $row->inloggedAttach;
					break;						
				case "moderator":
					$outloggedType = $row->outloggedModerator;
					$inloggedType = $row->inloggedModerator;
					break;
				default:
					return false;		
			}

			if($forumVariables['inlogged']){	//Check inlogged permissions
				if($inloggedType)
					$able = true;
				else
					$able = false;	
			}
			else {					//Check outlogged permissions
				if($outloggedType)
					$able = true;
				else
					$able = false;	
			}
			if($forumVariables['inlogged']) {
				/*for($k=0;$k<count($permissionGroup);$k++) { //Check member permissions
					if($permissionGroup[$k]['forumID'] == $forumID) {
						if($permissionGroup[$k][$type])
							$able = true;
						else
							$able = false;	
					}
				}*/
				if($permissionGroup) {
					$able2 = false;
					foreach($permissionGroup as $element) {
						if($element[$type] || $able2)
							$able2 = true;
						else
							$able2 = false;
					}
					$able = $able2;		
				}
					
				if($auth->moderator($forumID,$type) || $forumVariables['adminInlogged']) //Check if moderator
					$able = true;
				/*for($k=0;$k<count($permission);$k++) {	//Check member group permissions
					if($permission[$k]['forumID'] == $forumID) {
						if($permission[$k][$type])
							$able = true;
						else
							$able = false;	
					}
				}*/
				
				if($permission) {
					if($permission[$type])
						$able = true;
					else
						$able = false;	
				}
			}
			if($row->locked && !$forumVariables['adminInlogged']) {
				if($type == "thread")
					$able = false;
				if($type == "post")
					$able = false;
				if($type == "edit")	
					$able = false;
				if($type == "delete")	
					$able = false;
				if($type == "poll")	
					$able = false;
				if($type == "attach")	
					$able = false;
			}
			if($able && $type != "view") {
				if($type == "thread" || $type == "edit")
					$type = "post";
				else {
					if($type != "read")
						$type = "read";
					else
						$type = "view";
				}
			}
			else
				$done = true;
		}
			
		return $able;
	}
	
	function permissions($type) {
		global $forumVariables;
		global $permissionsResult;
		require_once('dbHandler.php');
		$db = new dbHandler;
		require_once('logInOutHandler.php');
		$auth = new logInOutHandler;
		
		if($forumVariables['inlogged']) {
			$permission = $this->getAllMemberPermissions($forumVariables['inloggedMemberID']);
			$permissionGroup = $this->getAllMemberGroupPermissions($forumVariables['inloggedMemberID']);
			
		}
		$moderators = $auth->moderators($type);
		if(isset($permissionsResult))
			$result = $permissionsResult;
		else {	
			$sql = "SELECT * FROM _'pfx'_forums";
			$permissionsResult = $result = $db->runSQL($sql);
		}	
		$i=0;
		while($row = $db->fetchArray($result)) {
			if($forumVariables['adminInlogged']) {
				$ables[$i]['permission'] = true;
				$ables[$i]['forumID'] = $row['forumID'];
				$i++;
				continue;
			}
				
			$forumID = $row['forumID'];
			$done = false;
			while(!$done) {
				switch($type) {
					case "view":
						$outloggedType = $row['outloggedView'];
						$inloggedType = $row['inloggedView'];
						break;
					case "read":
						$outloggedType = $row['outloggedRead'];
						$inloggedType = $row['inloggedRead'];
						break;
					case "thread":
						$outloggedType = $row['outloggedThread'];
						$inloggedType = $row['inloggedThread'];
						break;
					case "post":
						$outloggedType = $row['outloggedPost'];
						$inloggedType = $row['inloggedPost'];
						break;
					case "edit":
						$outloggedType = $row['outloggedEdit'];
						$inloggedType = $row['inloggedEdit'];
						break;
					case "delete":
						$outloggedType = $row['outloggedDelete'];
						$inloggedType = $row['inloggedDelete'];
						break;
					case "sticky":
						$outloggedType = $row['outloggedSticky'];
						$inloggedType = $row['inloggedSticky'];
						break;
					case "announce":
						$outloggedType = $row['outloggedAnnounce'];
						$inloggedType = $row['inloggedAnnounce'];
						break;
					case "vote":
						$outloggedType = $row['outloggedVote'];
						$inloggedType = $row['inloggedVote'];
						break;				
					case "poll":
						$outloggedType = $row['outloggedPoll'];
						$inloggedType = $row['inloggedPoll'];
						break;
					case "attach":
						$outloggedType = $row['outloggedAttach'];
						$inloggedType = $row['inloggedAttach'];
						break;			
					case "moderator":
						$outloggedType = $row['outloggedModerator'];
						$inloggedType = $row['inloggedModerator'];
						break;
					default:
						return false;		
				}
	
				if($forumVariables['inlogged']){	//Check inlogged permissions
					if($inloggedType)
						$able = true;
					else
						$able = false;	
				}
				else {					//Check outlogged permissions
					if($outloggedType)
						$able = true;
					else
						$able = false;	
				}
				
				if($forumVariables['inlogged']) {
					if($permissionGroup) {
						$able2 = false;
						$done = false;
						for($k=0;$k<count($permissionGroup);$k++) { //Check member permissions
							if($permissionGroup[$k]['forumID'] == $forumID) {
								$done = true;
								if($permissionGroup[$k][$type] || $able2)
									$able2 = true;
								else
									$able2 = false;	
							}
						}
						if($done)
							$able = $able2;
					}
					if(!empty($moderators)) {
						foreach($moderators as $moderator) {	
							if($moderator['forumID'] == $forumID) {
								if($moderator['moderator']) //Check if moderator
									$able = true;
							}		
						}		
					}
					if($permission) {	
						for($k=0;$k<count($permission);$k++) {	//Check member group permissions
							if($permission[$k]['forumID'] == $forumID) {
								if($permission[$k][$type])
									$able = true;
								else
									$able = false;	
							}
						}
					}	
				}
				if($row['locked'] && !$forumVariables['adminInlogged']) {
					if($type == "thread")
						$able = false;
					if($type == "post")
						$able = false;
					if($type == "edit")	
						$able = false;
					if($type == "delete")	
						$able = false;
					if($type == "poll")	
						$able = false;
					if($type == "attach")	
						$able = false;
				}
				if($able && $type != "view") {
					if($type == "thread" || $type == "edit")
						$type = "post";
					else {
						if($type != "read")
							$type = "read";
						else
							$type = "view";
					}
				}
				else
					$done = true;	
			}
			$ables[$i]['permission'] = $able;
			$ables[$i]['forumID'] = $forumID;
			$i++;
		}
		if(empty($ables)) {
			$ables = false;
		}
		//print_r($ables);
		//die();			
		return $ables;
	}
}
?>