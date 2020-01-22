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
class moderatorHandler {

	function moderatorHandler() {}

	/*function getAll() {
		$db = new dbHandler;
		$sql = "SELECT * FROM moderators";
		$result = $db->runSQL($sql);
		for($i=0;$row=mysql_fetch_array($result);$i++) {
			$moderators[$i]['forumID'] = $row['forumID'];
			$moderators[$i]['memberID'] = $row['memberID'];
		}
		return $moderators;
	}*/

	function getOne($forumID) {
		global $moderatorGetOneResult;
		if(!empty($moderatorGetOneResult))
			return $moderatorGetOneResult;
		$db = new dbHandler;
		$forumID = $db->SQLsecure($forumID);
		$sql = "SELECT _'pfx'_members.memberID, _'pfx'_members.userName FROM _'pfx'_members, _'pfx'_memberPermissions WHERE _'pfx'_members.memberID = _'pfx'_memberPermissions.memberID AND _'pfx'_memberPermissions.moderator = '1' AND _'pfx'_memberPermissions.forumID = '".$forumID."'";
		$result = $db->runSQL($sql);
		while($row = $db->fetchArray($result)) {
			if(!empty($moderators)) {
				if(!in_array($row['memberID'], $moderatorsMemberID)) {
					$moderators[] = $row;
					$moderatorsMemberID[] = $row['memberID'];
				}
			}
			else {
				$moderators[] = $row;
				$moderatorsMemberID[] = $row['memberID'];
			}
		}
		$sql = "SELECT _'pfx'_members.memberID, _'pfx'_members.userName FROM _'pfx'_members, _'pfx'_memberGroupsRelation, _'pfx'_memberGroupPermissions WHERE _'pfx'_members.memberID = _'pfx'_memberGroupsRelation.memberID AND _'pfx'_memberGroupsRelation.groupID = _'pfx'_memberGroupPermissions.memberGroupID AND _'pfx'_memberGroupPermissions.moderator = '1' AND _'pfx'_memberGroupPermissions.forumID = '".$forumID."'";
		$result = $db->runSQL($sql);
		while($row = $db->fetchArray($result)) {
			if(!empty($moderators)) {
				if(!in_array($row['memberID'], $moderatorsMemberID)) {
					$moderators[] = $row;
					$moderatorsMemberID[] = $row['memberID'];
				}
			}
			else {
				$moderators[] = $row;
				$moderatorsMemberID[] = $row['memberID'];
			}
		}
		if(empty($moderators))
			return false;
		else {
			$moderatorGetOneResult = $moderators;
			return $moderators;
		}
	}

	function checkModerator($memberID,$forumID) {
		$moderators = $this->getOne($forumID);
		if($moderators) {
			foreach($moderators as $element) {
				if($element['memberID'] == $memberID)
					return true;
			}
		}
		return false;
	}

	function getForums($memberID) {
		$db = new dbHandler;
		$memberID = $db->SQLsecure($memberID);
		$forumIDs = array();
		$i = 0;
		$sql = "SELECT _'pfx'_forums.forumID, _'pfx'_forums.name FROM _'pfx'_memberPermissions INNER JOIN _'pfx'_forums ON _'pfx'_memberPermissions.forumID = _'pfx'_forums.forumID WHERE _'pfx'_memberPermissions.memberID = '".$memberID."' AND _'pfx'_memberPermissions.moderator = 1";
		$result = $db->runSQL($sql);
		if($db->numRows($result) > 0) {
			while($row = $db->fetchArray($result)) {
				$forums[$i]['forumID'] = $forumIDs[] = $row['forumID'];
				$forums[$i]['name'] = $row['name'];
				$i++;
			}
		}

		$sql = "SELECT _'pfx'_forums.forumID, _'pfx'_forums.name FROM _'pfx'_memberGroupsRelation INNER JOIN _'pfx'_memberGroupPermissions INNER JOIN _'pfx'_forums ON _'pfx'_memberGroupPermissions.memberGroupID = _'pfx'_memberGroupsRelation.groupID ON _'pfx'_memberGroupPermissions.forumID = _'pfx'_forums.forumID WHERE _'pfx'_memberGroupsRelation.memberID = '".$memberID."' AND _'pfx'_memberGroupPermissions.moderator = 1";
		$result = $db->runSQL($sql);
		if($db->numRows($result) > 0) {
			while($row = $db->fetchArray($result)) {
				if(isset($forums) && in_array($row['forumID'],$forumIDs))
					continue;
				$forums[$i]['forumID'] = $row['forumID'];
				$forums[$i]['name'] = $row['name'];
				$i++;
			}
		}
		if(!empty($forums))
			return $forums;
		else
			return false;
	}
}
?>