<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once("dbHandler.php");

class memberGroupHandler {

	function memberGroupHandler() {}
	
	function add($name, $description, $groupModerator) {
		$db = new dbHandler;
		$name = $db->SQLsecure($name);
		$description = $db->SQLsecure($description);
		$groupModerator = $db->SQLsecure($groupModerator);
		
		$sql = "INSERT INTO _'pfx'_memberGroups (name,Description,groupModerator) VALUES('".$name."','".$description."','".$groupModerator."')";
		$db->runSQL($sql);
		
		$groupID = $this->getGroupIDOfNewest();
		$this->addMember($groupID, $groupModerator);
	}
	
	function remove($groupID) {
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$sql = "DELETE FROM _'pfx'_memberGroups WHERE groupID = '".$groupID."'";
		$db->runSQL($sql);
		
		$sql = "DELETE FROM _'pfx'_memberGroupsRelation WHERE groupID = '".$groupID."'";
		$db->runSQL($sql);
		
		$sql = "DELETE FROM _'pfx'_memberGroupPermissions WHERE memberGroupID = '".$groupID."'";
		$db->runSQL($sql);
	}
	
	function getOne($groupID) {
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$sql = "SELECT _'pfx'_memberGroups.*, _'pfx'_members.userName, _'pfx'_members.memberID FROM _'pfx'_memberGroups, _'pfx'_members WHERE _'pfx'_members.memberID = _'pfx'_memberGroups.groupModerator AND _'pfx'_memberGroups.groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$group['groupID'] = $row->groupID;
		$group['name'] = $row->name;
		$group['description'] = $row->Description;
		$group['groupModerator'] = $row->groupModerator;
		$group['groupModeratorUserName'] = $row->userName;
		$group['groupModeratorMemberID'] = $row->memberID;
		
		$sql = "SELECT _'pfx'_members.* FROM _'pfx'_members, _'pfx'_memberGroupsRelation WHERE _'pfx'_members.memberID = _'pfx'_memberGroupsRelation.memberID AND _'pfx'_memberGroupsRelation.groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$i=0;
		while($row = $db->fetchArray($result)) {
			$group['groupMemberID'][$i] = $row['memberID'];
			$group['groupMemberUserName'][$i] = $row['userName'];
			$group['groupMemberEmail'][$i] = $row['email'];
			$group['groupMemberHomepage'][$i] = $row['homepage'];
			$group['groupMemberLocation'][$i] = $row['location'];
			$i++;
		}
		return $group;
	}
	
	function getAll() {
		$db = new dbHandler;
		$sql = "SELECT _'pfx'_memberGroups.*, _'pfx'_members.userName, _'pfx'_members.memberID FROM _'pfx'_memberGroups, _'pfx'_members WHERE _'pfx'_members.memberID = _'pfx'_memberGroups.groupModerator";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$i=0;
		while($row = $db->fetchArray($result)) {
			$groups[$i]['groupID'] = $row['groupID'];
			$groups[$i]['name'] = $row['name'];
			$groups[$i]['description'] = $row['Description'];
			$groups[$i]['groupModerator'] = $row['groupModerator'];
			$groups[$i]['groupModeratorUserName'] = $row['userName'];
			$groups[$i]['groupModeratorMemberID'] = $row['memberID'];
			$i++;
		}
		
		$sql = "SELECT _'pfx'_memberGroupsRelation.groupID, _'pfx'_members.userName, _'pfx'_members.memberID FROM _'pfx'_members, _'pfx'_memberGroupsRelation WHERE _'pfx'_members.memberID = _'pfx'_memberGroupsRelation.memberID";
		$result = $db->runSQL($sql);
		$k=0;
		foreach($groups as $element) {
			$groupMemberCount[$k] = 0;
			$k++;
		}
		while($row = $db->fetchArray($result)) {
			$k=0;
			foreach($groups as $element) {
				if($row['groupID'] == $groups[$k]['groupID']) {
					$groups[$k]['groupMemberID'][$groupMemberCount[$k]] = $row['memberID'];
					$groups[$k]['groupMemberUserName'][$groupMemberCount[$k]] = $row['userName'];
					$groupMemberCount[$k]++;
				}	
				$k++;
			}	
		}
		return $groups;
	}
	
	function edit($groupID, $name, $description, $groupModerator) {
		$db = new dbHandler;
		$grouID = $db->SQLsecure($groupID);
		$name = $db->SQLsecure($name);
		$description = $db->SQLsecure($description);
		$groupModerator = $db->SQLsecure($groupModerator);
		
		$sql = "SELECT groupModerator FROM _'pfx'_memberGroups WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		if($row->groupModerator != $groupModerator)
			$this->addMember($groupID, $groupModerator);
		$sql = "UPDATE _'pfx'_memberGroups SET name = '".$name."', Description = '".$description."', groupModerator = '".$groupModerator."' WHERE groupID = '".$groupID."'";
		$db->runSQL($sql);
	}
	
	function addMember($groupID, $members) {
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$memberGroup = $this->getOne($groupID);
		if(is_array($members)) {
			$i=0;
			foreach($members as $member) {
				if(!in_array($member,$memberGroup['groupMemberID'])) {
					$member = $db->SQLsecure($member);
					if($i==0)
						$sql = "INSERT INTO _'pfx'_memberGroupsRelation (groupID,memberID) VALUES('".$groupID."','".$member."')";
					else
						$sql .= ",('".$groupID."','".$member."')";
					//$sql = "INSERT INTO memberGroupsRelation (groupID,memberID) VALUES('".$groupID."','".$member."')";
					//$db->runSQL($sql);	
					$i++;
				}			
			}
			//$sql .= ")";
		}
		else {
			if($members == $memberGroup['groupMemberID']) {
				return false;
			}	
			$members = $db->SQLsecure($members);
			$sql = "INSERT INTO _'pfx'_memberGroupsRelation (groupID,memberID) VALUES('".$groupID."','".$members."')";
			//$db->runSQL($sql);
		}
		//die($sql);
		if(empty($sql)) {
			return false;
		}
		$db->runSQL($sql);	
	}
	
	function editMembers($groupID, $members) {
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$memberGroup = $this->getOne($groupID);
		$memberGroupModerator = $memberGroup['groupModeratorMemberID'];
		if(empty($members)) {
			$sql = "DELETE FROM _'pfx'_memberGroupsRelation WHERE groupID = '".$groupID."' AND memberID != '".$memberGroupModerator."'";
			$db->runSQL($sql);
			return true;
		}
		$sql = "SELECT * FROM _'pfx'_memberGroupsRelation WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) > 0) {
			while($row = $db->fetchArray($result)) {
				$done = false;
				foreach($members as $member) {
					if($row['memberID'] == $member) {
						$ignoreMember[] = $member;
						$done = true;
						break;
					}	
					elseif($row['memberID'] == $memberGroupModerator) {
						$ignoreMember[] = $memberGroupModerator;
						$done = true;
						break;
					}	
				}
				if(!$done)
					$deleteMember[] = $row['memberID'];
			}
		
			if(isset($ignoreMember)) {
				foreach($members as $membersElement) {
					$match = false;
					foreach($ignoreMember as $ignoreMemberElement) {
						if($membersElement == $ignoreMemberElement) {
							$match = true;
						}
					}
					if(!$match)
						$addMember[] = $membersElement;
				}
			}
		}	
		else {
			$addMember = $members;
		}	
		if(isset($deleteMember)) {
			$i=0;
			foreach($deleteMember as $element) {
				$element = $db->SQLsecure($element);
				if($i == 0)
					$sql = "DELETE FROM _'pfx'_memberGroupsRelation WHERE groupID = '".$groupID."' AND (memberID = '".$element."'";
				else
					$sql .= " OR memberID = '".$element."'";
				$i++;	
			}
			$sql .= ")";
			$db->runSQL($sql);
		}
		
		if(isset($addMember)) {
			$i=0;
			foreach($addMember as $element) {
				$element = $db->SQLsecure($element);
				if($i==0)
					$sql = "INSERT INTO _'pfx'_memberGroupsRelation (groupID,memberID) VALUES('".$groupID."','".$element."')";
				else
					$sql .= ",('".$groupID."','".$element."')";
				$i++;
				//$sql = "INSERT INTO memberGroupsRelation (groupID,memberID) VALUES('".$groupID."','".$element."')";	
				//$db->runSQL($sql);	
			}
			//$sql .= ")";
			$db->runSQL($sql);
		}
	}
	
	function removeMember($groupID,$memberID) {
		global $forumVariables;
		$db = new dbHandler;
		$groupID = $db->SQLsecure($groupID);
		$memberID = $db->SQLsecure($memberID);
		
		$sql = "SELECT groupModerator FROM _'pfx'_memberGroups WHERE groupID = '".$groupID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		if($forumVariables['adminInlogged'] || $row['groupModerator'] == $forumVariables['inloggedMemberID'] || $memberID == $forumVariables['inloggedMemberID']) {
			if($row['groupModerator'] != $memberID) {
				$sql = "DELETE FROM _'pfx'_memberGroupsRelation WHERE memberID = '".$memberID."' AND groupID = '".$groupID."'";
				$db->runSQL($sql);
				return true;
			}
			else
				return false;
		}		
		else
			return false;
	}
	
	function getGroupIDOfNewest() {
		$db = new dbHandler;
		$sql = "SELECT max(groupID) AS newest FROM _'pfx'_memberGroups";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$row = $db->fetchObject($result);
		return $row->newest;	
	}
	
	function groupMembership() {
		global $forumVariables;
		global $groupMembershipInlogged;
		if(!empty($groupMembershipInlogged))
			return $groupMembershipInlogged;
		$db = new dbHandler;
		if(!$forumVariables['inlogged']) {
			$groupMembershipInlogged = false;
			return false;
		}	
		$sql = "SELECT groupID FROM _'pfx'_memberGroupsRelation WHERE memberID = '".$forumVariables['inloggedMemberID']."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0) {
			$groupMembershipInlogged = false;
			return false;
		}	
		while($row = $db->fetchArray($result)) {
			$memberships[] = $row['groupID'];
		}
		$groupMembershipInlogged = $memberships;
		return $memberships;	
	}
}
?>