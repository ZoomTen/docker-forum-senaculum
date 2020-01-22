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
	
class logInOutHandler {

	function logInOutHandler() {
	}

	function logIn($userName,$password) {
		global $forumVariables;
		global $forumSettings;
		
		$db = new dbHandler;
		$userName = $db->SQLsecure($userName);
	
		$password=strtolower($password);
		$password = crypt(md5($password),md5($password));
		$password = $db->SQLsecure($password);
		
		$sql = "SELECT * FROM _'pfx'_members WHERE userName='".$userName."' AND password='".$password."' AND activated = 1";
		$result = $db->runSQL($sql);
		if($db->numRows($result) > 0) {
			$member = $db->fetchObject($result);
			$memberID = $member->memberID;
			$admin = $member->admin;
			$loginDate1 = $member->loginDate1;
			$loginDate2 = $member->loginDate2;
			$_SESSION['forumMemberID'] = $memberID;
			$_SESSION['forumPassword'] = $password;
			setcookie('forumMemberID',$memberID,time()+86400,'/');
			setcookie('forumPassword',$password,time()+86400,'/');
			
			/*if($loginDate1 == "0000-00-00 00:00:00")
				$loginDate1 = date('Y-m-d H:i:s');
			if($loginDate2 == "0000-00-00 00:00:00")
				$loginDate2 = date('Y-m-d H:i:s');	*/
			//die(strtotime($loginDate2));
			$time = time();
			$loginDate = $time;
			if($loginDate1 > $loginDate2) {
				if($time - $loginDate1 > 30) { //For last posts to work in Opera
					$sql = "UPDATE _'pfx'_members SET loginDate2 = '".$time."' WHERE memberID = '".$memberID."'";
					$lastLoginDate = $loginDate1;
				}
				else
					$lastLoginDate = $loginDate1;
			}
			else {
				if($time - $loginDate2 > 30) { //For last posts to work in Opera
					$sql = "UPDATE _'pfx'_members SET loginDate1 = '".$time."' WHERE memberID = '".$memberID."'";
					$lastLoginDate = $loginDate2;	
				}
				else
					$lastLoginDate = $loginDate2;	
			}
			$db->runSQL($sql);
			
			$forumVariables['inlogged'] = true;
			$forumVariables['adminInlogged'] = $member->admin;
			$forumVariables['inloggedMemberID'] = $member->memberID;
			$forumVariables['inloggedUserName'] = $member->userName;
			if($forumVariables['inloggedMemberID'] == 1)
				$forumVariables['superAdminInlogged'] = true;
			else
				$forumVariables['superAdminInlogged'] = false;
			$forumVariables['lastLoginDate'] = $lastLoginDate;
			$forumVariables['loginDate'] = $loginDate;	
			$forumVariables['inloggedNow'] = true;
			$forumVariables['lang'] = $member->lang;
			$forumVariables['dateFormat'] = $member->dateFormat;
			$forumVariables['alwaysAllowBBCode'] = $member->alwaysAllowBBCode;
			$forumVariables['alwaysAllowSmilies'] = $member->alwaysAllowSmilies;
			$forumVariables['alwaysNotifyOnReply'] = $member->alwaysNotifyOnReply;
			$forumVariables['notifyNewPM'] = $member->notifyNewPM;
			$forumVariables['alwaysDisplaySign'] = $member->alwaysDisplaySign;
			
			//Erase old data in viewedPosts
			if($forumSettings['smartNewPosts']) {
				$sql = "DELETE FROM _'pfx'_viewedPosts WHERE memberID = '".$forumVariables['inloggedMemberID']."' AND date < '".$forumVariables['lastLoginDate']."'";
				$db->runSQL($sql);
			}
			
			$this->updateStatus();
			return true;
		}
		else {
			$forumVariables['inlogged'] = false;
			$forumVariables['adminInlogged'] = false;
			$forumVariables['inloggedMemberID'] = false;
			$forumVariables['inloggedUserName'] = "guest";
			$forumVariables['superAdminInlogged'] = false;
			$forumVariables['lastLoginDate'] = false;
			$forumVariables['loginDate'] = false;
			$forumVariables['lang'] = "default";
			$forumVariables['dateFormat'] = $forumSettings['dateFormat'];
			$forumVariables['alwaysAllowBBCode'] = true;
			$forumVariables['alwaysAllowSmilies'] = true;
			$forumVariables['alwaysNotifyOnReply'] = false;
			$forumVariables['notifyNewPM'] = false;
			$forumVariables['alwaysDisplaySign'] = false;
			return false;
		}
	}
	
	function logOut() {
		global $forumVariables;
		global $forumSettings;
		
		$this->clearStatus($_SESSION['forumMemberID']);
		
		session_destroy();
		if(isset($_COOKIE['forumMemberID']))
			$_COOKIE['forumMemberID'] = "";
		if(isset($_COOKIE['forumPassword']))
			$_COOKIE['forumPassword'] = "";
				
		$forumVariables['inlogged'] = false;
		$forumVariables['adminInlogged'] = false;
		$forumVariables['inloggedMemberID'] = false;
		$forumVariables['inloggedUserName'] = "guest";
		$forumVariables['superAdminInlogged'] = false;
		$forumVariables['lastLoginDate'] = false;
		$forumVariables['loginDate'] = false;
		$forumVariables['lang'] = "default";
		$forumVariables['dateFormat'] = $forumSettings['dateFormat'];
		$forumVariables['alwaysAllowBBCode'] = true;
		$forumVariables['alwaysAllowSmilies'] = true;
		$forumVariables['alwaysNotifyOnReply'] = false;
		$forumVariables['notifyNewPM'] = false;
		$forumVariables['alwaysDisplaySign'] = false;
	}
	
	function loggedIn() {
		global $forumVariables;
		global $forumSettings;
		if(isset($forumVariables['inlogged'])) {
			return $forumVariables['inlogged'];
		}	
			
		if(isset($_SESSION['forumMemberID']) && isset($_COOKIE['forumMemberID']) && isset($_SESSION['forumPassword']) && isset($_COOKIE['forumPassword'])) {
			if(($_SESSION['forumMemberID'] == $_COOKIE['forumMemberID']) && ($_SESSION['forumPassword'] == $_COOKIE['forumPassword'])) {
				$memberID = $_SESSION['forumMemberID'];
				$password = $_SESSION['forumPassword'];
			}
			else {
				$forumVariables['inlogged'] = false;
				$forumVariables['adminInlogged'] = false;
				$forumVariables['inloggedMemberID'] = false;
				$forumVariables['inloggedUserName'] = "guest";
				$forumVariables['superAdminInlogged'] = false;
				$forumVariables['lastLoginDate'] = false;
				$forumVariables['loginDate'] = false;
				$forumVariables['lang'] = "default";
				$forumVariables['dateFormat'] = $forumSettings['dateFormat'];
				$forumVariables['alwaysAllowBBCode'] = true;
				$forumVariables['alwaysAllowSmilies'] = true;
				$forumVariables['alwaysNotifyOnReply'] = false;
				$forumVariables['notifyNewPM'] = false;
				$forumVariables['alwaysDisplaySign'] = false;
				return false;	
			}
		}
		else {
			$forumVariables['inlogged'] = false;
			$forumVariables['adminInlogged'] = false;
			$forumVariables['inloggedMemberID'] = false;
			$forumVariables['inloggedUserName'] = "guest";
			$forumVariables['superAdminInlogged'] = false;
			$forumVariables['lastLoginDate'] = false;
			$forumVariables['loginDate'] = false;
			$forumVariables['lang'] = "default";
			$forumVariables['dateFormat'] = $forumSettings['dateFormat'];
			$forumVariables['alwaysAllowBBCode'] = true;
			$forumVariables['alwaysAllowSmilies'] = true;
			$forumVariables['alwaysNotifyOnReply'] = false;
			$forumVariables['notifyNewPM'] = false;
			$forumVariables['alwaysDisplaySign'] = false;
			return false;	
		}
		$db = new dbHandler;
		$sql = "SELECT * FROM _'pfx'_members WHERE memberID='".$db->SQLsecure($memberID)."' AND password='".$db->SQLsecure($password)."' AND activated = 1";
		$result = $db->runSQL($sql);
		if($db->numRows($result) > 0) {
			$row = $db->fetchObject($result);
			$forumVariables['adminInlogged'] = $row->admin;
			$forumVariables['inlogged'] = true;
			$forumVariables['inloggedMemberID'] = $row->memberID;
			$forumVariables['inloggedUserName'] = $row->userName;
			if($forumVariables['inloggedMemberID'] == 1)
				$forumVariables['superAdminInlogged'] = true;
			else	
				$forumVariables['superAdminInlogged'] = false;
			$forumVariables['lastLoginDate'] = min($row->loginDate1, $row->loginDate2);
			$forumVariables['loginDate'] = max($row->loginDate1, $row->loginDate2);
			$forumVariables['lang'] = $row->lang;
			$forumVariables['dateFormat'] = $row->dateFormat;
			$forumVariables['alwaysAllowBBCode'] = $row->alwaysAllowBBCode;
			$forumVariables['alwaysAllowSmilies'] = $row->alwaysAllowSmilies;
			$forumVariables['alwaysNotifyOnReply'] = $row->alwaysNotifyOnReply;
			$forumVariables['notifyNewPM'] = $row->notifyNewPM;
			$forumVariables['alwaysDisplaySign'] = $row->alwaysDisplaySign;
			$this->updateStatus();
			return true;
		}
		else {
			$forumVariables['inlogged'] = false;
			$forumVariables['adminInlogged'] = false;
			$forumVariables['inloggedMemberID'] = false;
			$forumVariables['inloggedUserName'] = "guest";
			$forumVariables['superAdminInlogged'] = false;
			$forumVariables['lastLoginDate'] = false;
			$forumVariables['loginDate'] = false;
			$forumVariables['lang'] = "default";
			$forumVariables['dateFormat'] = $forumSettings['dateFormat'];
			$forumVariables['alwaysAllowBBCode'] = true;
			$forumVariables['alwaysAllowSmilies'] = true;
			$forumVariables['alwaysNotifyOnReply'] = false;
			$forumVariables['notifyNewPM'] = false;
			$forumVariables['alwaysDisplaySign'] = false;
			return false;	
		}
	}
	
	function admin()
	{	
		global $forumVariables;
		if(isset($forumVariables['adminInlogged'])) {
			return $forumVariables['adminInlogged'];
		}	
				
		if(!$this->loggedIn())
		{
				$forumVariables['adminInlogged'] = false;
				return false;
		}
		
		$db = new dbHandler;
		$sql = "SELECT admin FROM _'pfx'_members WHERE memberID = '".$db->SQLsecure($_SESSION['forumMemberID'])."'";
		$result = $db->runSQL($sql);
		
		$row = $db->fetchObject($result);
		
		if($row->admin)
		{
			$forumVariables['adminInlogged'] = true;
			return true;
		}
		else
		{
			$forumVariables['adminInlogged'] = false;
			return false;
		}
	}
	function superAdmin() {
		global $forumVariables;
		if(isset($forumVariables['superAdminInlogged']))  
			return $forumVariables['superAdminInlogged'];	
		if($this->loggedIn()) {			
			if($forumVariables['inloggedMemberID'] == 1) {
				$forumVariables['superAdminInlogged'] = true;
				return true;
			}
			else {
				$forumVariables['superAdminInlogged'] = false;
				return false;
			}
		}	
		else {
			$forumVariables['superAdminInlogged'] = false;
			return false;
		}	
	}
	
	function activated($username, $password) {
		$db = new dbHandler;
		$userName = $db->SQLsecure($userName);
	
		$password=strtolower($password);
		$password = crypt(md5($password),md5($password));
		$password = $db->SQLsecure($password);
		
		$sql = "SELECT activated FROM _'pfx'_members WHERE userName='".$userName."' AND password='".$password."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) >= 1) {
			$row = $db->fetchArray($result);
			return $row['activated'];
		}	
		else
			return true;
	}
	
	function moderator($forumID,$type) {
		global $forumVariables;
		global $moderatorLoggedIn;
		if(isset($moderatorLoggedIn[$forumID])) 
			return $moderatorLoggedIn[$forumID][$type];
		if(!$this->loggedIn()) 
			return false;
		require_once("permissionHandler.php");
		$permissions = new permissionHandler;	
		$permission = $permissions->getOneMemberPermissions($forumID,$forumVariables['inloggedMemberID']);
		$permissionGroup2 = $permissions->getOneMemberGroupPermissions($forumID,$forumVariables['inloggedMemberID']);
		
		if(!empty($permissionGroup2)) {
			$i = 0;
			foreach($permissionGroup2 as $element) {
				$permissionGroup['view'][$i] = $permissionGroup2[$i]['view'];
				$permissionGroup['read'][$i] = $permissionGroup2[$i]['read'];
				$permissionGroup['thread'][$i] = $permissionGroup2[$i]['thread'];
				$permissionGroup['post'][$i] = $permissionGroup2[$i]['post'];
				$permissionGroup['edit'][$i] = $permissionGroup2[$i]['edit'];
				$permissionGroup['delete'][$i] = $permissionGroup2[$i]['delete'];
				$permissionGroup['sticky'][$i] = $permissionGroup2[$i]['sticky'];
				$permissionGroup['announce'][$i] = $permissionGroup2[$i]['announce'];
				$permissionGroup['vote'][$i] = $permissionGroup2[$i]['vote'];
				$permissionGroup['poll'][$i] = $permissionGroup2[$i]['poll'];
				$permissionGroup['attach'][$i] = $permissionGroup2[$i]['attach'];
				$permissionGroup['moderator'][$i] = $permissionGroup2[$i]['moderator'];
				$i++;
			}
		}	
		
		if(empty($permission) && empty($permissionGroup)) {
			$moderator['view'] = false;
			$moderator['read'] = false;
			$moderator['thread'] = false;
			$moderator['post'] = false;
			$moderator['edit'] = false;
			$moderator['delete'] = false;
			$moderator['sticky'] = false;
			$moderator['announce'] = false;
			$moderator['vote'] = false;
			$moderator['poll'] = false;
			$moderator['attach'] = false;
			if($moderator['view'] && $moderator['read'] && $moderator['thread'] && $moderator['post'] && $moderator['edit'] && $moderator['delete'] && $moderator['sticky'] && $moderator['announce'] && $moderator['vote'] && $moderator['poll'] && $moderator['attach'])	
				$moderator['all'] = true;
			else
				$moderator['all'] = false;	
			$moderatorLoggedIn[$forumID] = $moderator;
			return $moderator[$type];
		}
		if(!empty($permission)) {
			if($permission['moderator']) {
				$moderator['view'] = $permission['view'];
				$moderator['read'] = $permission['read'];
				$moderator['thread'] = $permission['thread'];
				$moderator['post'] = $permission['post'];
				$moderator['edit'] = $permission['edit'];
				$moderator['delete'] = $permission['delete'];
				$moderator['sticky'] = $permission['sticky'];
				$moderator['announce'] = $permission['announce'];
				$moderator['vote'] = $permission['vote'];
				$moderator['poll'] = $permission['poll'];
				$moderator['attach'] = $permission['attach'];
			}	
		}
		elseif(!empty($permissionGroup)) {
			if(empty($moderator)) {
				if($permission['mederator']) {
					$moderator['view'] = in_array(true,$permissionGroup['view']);
					$moderator['read'] = in_array(true,$permissionGroup['read']);
					$moderator['thread'] = in_array(true,$permissionGroup['thread']);
					$moderator['post'] = in_array(true,$permissionGroup['post']);
					$moderator['edit'] = in_array(true,$permissionGroup['edit']);
					$moderator['delete'] = in_array(true,$permissionGroup['delete']);
					$moderator['sticky'] = in_array(true,$permissionGroup['sticky']);
					$moderator['announce'] = in_array(true,$permissionGroup['announce']);
					$moderator['vote'] = in_array(true,$permissionGroup['vote']);
					$moderator['poll'] = in_array(true,$permissionGroup['poll']);
					$moderator['attach'] = in_array(true,$permissionGroup['attach']);
					if($moderator['view'] && $moderator['read'] && $moderator['thread'] && $moderator['post'] && $moderator['edit'] && $moderator['delete'] && $moderator['sticky'] && $moderator['announce'] && $moderator['vote'] && $moderator['poll'] && $moderator['attach'])	
						$moderator['all'] = true;
					else
						$moderator['all'] = false;	
					$moderatorLoggedIn[$forumID] = $moderator;
					return $moderator[$type];
				}
				else {
					$moderator['view'] = false;
					$moderator['read'] = false;
					$moderator['thread'] = false;
					$moderator['post'] = false;
					$moderator['edit'] = false;
					$moderator['delete'] = false;
					$moderator['sticky'] = false;
					$moderator['announce'] = false;
					$moderator['vote'] = false;
					$moderator['poll'] = false;
					$moderator['attach'] = false;
					if($moderator['view'] && $moderator['read'] && $moderator['thread'] && $moderator['post'] && $moderator['edit'] && $moderator['delete'] && $moderator['sticky'] && $moderator['announce'] && $moderator['vote'] && $moderator['poll'] && $moderator['attach'])	
						$moderator['all'] = true;
					else
						$moderator['all'] = false;	
					$moderatorLoggedIn[$forumID] = $moderator;
					return $moderator[$type];
				}	
			}
			else {
				if(in_array(true,$permissionGroup['moderator'])) {
					if(!$moderator['view'] && $permissionGroup['view'])
						$moderator['view'] = true;
					if(!$moderator['read'] && $permissionGroup['read'])
						$moderator['read'] = true;
					if(!$moderator['thread'] && $permissionGroup['thread'])
						$moderator['thread'] = true;	
					if(!$moderator['post'] && $permissionGroup['post'])
						$moderator['post'] = true;
					if(!$moderator['edit'] && $permissionGroup['edit'])
						$moderator['edit'] = true;		
					if(!$moderator['delete'] && $permissionGroup['delete'])
						$moderator['delete'] = true;
					if(!$moderator['sticky'] && $permissionGroup['sticky'])
						$moderator['sticky'] = true;	
					if(!$moderator['announce'] && $permissionGroup['announce'])
						$moderator['announce'] = true;	
					if(!$moderator['vote'] && $permissionGroup['vote'])
						$moderator['vote'] = true;	
					if(!$moderator['poll'] && $permissionGroup['poll'])
						$moderator['poll'] = true;		
					if(!$moderator['attach'] && $permissionGroup['attach'])
						$moderator['attach'] = true;	
					if($moderator['view'] && $moderator['read'] && $moderator['thread'] && $moderator['post'] && $moderator['edit'] && $moderator['delete'] && $moderator['sticky'] && $moderator['announce'] && $moderator['vote'] && $moderator['poll'] && $moderator['attach'])	
						$moderator['all'] = true;
					else
						$moderator['all'] = false;	
					$moderatorLoggedIn[$forumID] = $moderator;
					return $moderator[$type];			
				}
				else {
					if($moderator['view'] && $moderator['read'] && $moderator['thread'] && $moderator['post'] && $moderator['edit'] && $moderator['delete'] && $moderator['sticky'] && $moderator['announce'] && $moderator['vote'] && $moderator['poll'] && $moderator['attach'])	
						$moderator['all'] = true;
					else
						$moderator['all'] = false;	
					$moderatorLoggedIn[$forumID] = $moderator;
					return $moderator[$type];	
				}
			}
		}		
		if(isset($moderator)) {
			if($moderator['view'] && $moderator['read'] && $moderator['thread'] && $moderator['post'] && $moderator['edit'] && $moderator['delete'] && $moderator['sticky'] && $moderator['announce'] && $moderator['vote'] && $moderator['poll'] && $moderator['attach'])	
				$moderator['all'] = true;
			else
				$moderator['all'] = false;
		}
		else {			
			$moderator['view'] = false;	
			$moderator['read'] = false;
			$moderator['thread'] = false;
			$moderator['post'] = false;
			$moderator['edit'] = false;
			$moderator['delete'] = false;
			$moderator['sticky'] = false;
			$moderator['announce'] = false;
			$moderator['vote'] = false;
			$moderator['poll'] = false;
			$moderator['attach'] = false;
			$moderator['all'] = false;
		}	
		$moderatorLoggedIn[$forumID] = $moderator;
		return $moderator[$type];	
	}
	function moderators($type) {
		global $forumVariables;
		global $moderatorsLoggedIn;
		if(isset($moderatorsLoggedIn))
			return $moderatorsLoggedIn;
		if(!$this->loggedIn())
			return false;
		require_once("permissionHandler.php");
		$permission = new permissionHandler;	
		$permissions = $permission->getAllMemberPermissions($forumVariables['inloggedMemberID']);
		$permissionsGroup = $permission->getAllMemberGroupPermissions($forumVariables['inloggedMemberID']);	
		
		if(empty($permissions) && empty($permissionsGroup)) {	
			$moderatorsLoggedIn = false;
			return false;
		}	
		
		if(!empty($permission)) {
			foreach($permissions as $currentPermission) {
				if($currentPermission['moderator']) {
					$moderator[] = $currentPermission;
				}	
			}
		}
		
		if(!empty($permissionsGroup)) {
			foreach($permissionsGroup as $currentPermissionGroup) {
				if(empty($moderator)) {
					if($currentPermissionGroup['moderator']) {
						$moderator[] = $currentPermissionGroup;
					}
				}
				else {
					$i=0;
					foreach($moderator as $currentModerator) {
						if($currentPermissionGroup['forumID'] == $currentModerator) {
							if(!$moderator[$i]['view'] && $currentPermissionGroup['view'])
								$moderator[$i]['view'] = true;
							if(!$moderator[$i]['read'] && $currentPermissionGroup['read'])
								$moderator[$i]['read'] = true;
							if(!$moderator[$i]['thread'] && $currentPermissionGroup['thread'])
								$moderator[$i]['thread'] = true;	
							if(!$moderator[$i]['post'] && $currentPermissionGroup['post'])
								$moderator[$i]['post'] = true;
							if(!$moderator[$i]['edit'] && $currentPermissionGroup['edit'])
								$moderator[$i]['edit'] = true;		
							if(!$moderator[$i]['delete'] && $currentPermissionGroup['delete'])
								$moderator[$i]['delete'] = true;
							if(!$moderator[$i]['sticky'] && $currentPermissionGroup['sticky'])
								$moderator[$i]['sticky'] = true;
							if(!$moderator[$i]['announce'] && $currentPermissionGroup['announce'])
								$moderator[$i]['announce'] = true;	
							if(!$moderator[$i]['vote'] && $currentPermissionGroup['vote'])
								$moderator[$i]['vote'] = true;		
							if(!$moderator[$i]['poll'] && $currentPermissionGroup['poll'])
								$moderator[$i]['poll'] = true;	
							if(!$moderator[$i]['attach'] && $currentPermissionGroup['attach'])
								$moderator[$i]['attach'] = true;			
							$i++;	
						}		
					}
				}
			}	
		}
		if(!empty($moderator)) {
			$i = 0;
			foreach($moderator as $element) {
				if($moderator[$i]['view'] && $moderator[$i]['read'] && $moderator[$i]['thread'] && $moderator[$i]['post'] && $moderator[$i]['edit'] && $moderator[$i]['delete'] && $moderator[$i]['sticky'] && $moderator[$i]['announce'] && $moderator[$i]['vote'] && $moderator[$i]['poll'] && $moderator[$i]['attach'])
					$moderator[$i]['all'] = true;
				else
					$moderator[$i]['all'] = false;	
				$i++;
			}
			return $moderator;
		}
		else
			return false;
	}

	function inloggedMember() {
		if(empty($_SESSION['forumMemberID'])) {
			$db = new dbHandler;
			$sql = "SELECT memberID FROM _'pfx'_members WHERE userName = '".$db->SQLsecure($_POST['userName'])."'";
			$result = $db->runSQL($sql);
			$row = $db->fetchObject($result);
			if($db->numRows($result) > 0)
				return $row->memberID;
		}
		else
			return $_SESSION['forumMemberID'];	
	}
	
	function groupModerator() {
		global $forumVariables;
		if(!$this->loggedIn())
			return false;
		global $groupModeratorInlogged;
		if(!empty($groupModeratorInlogged))
			return $groupModeratorInlogged;	
		$db = new dbHandler;
		$sql = "SELECT groupID FROM _'pfx'_memberGroups WHERE groupModerator = '".$db->SQLsecure($forumVariables['inloggedMemberID'])."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0) {
			$groupModeratorInlogged = false;
			return false;
		}	
		else {
			while($row = $db->fetchArray($result)) {
				$moderator[] = $row['groupID'];
		}
		$groupModeratorInlogged = $moderator;
		return $moderator;	
		}	
	}
	
	function updateStatus() {
		global $forumVariables;
		global $forumSettings;
		if($forumSettings['activateOnline'] && $forumVariables['inlogged']) {
			$db = new dbHandler;
			$sql = "UPDATE _'pfx'_members SET lastActive = '".time()."' WHERE memberID = '".$forumVariables['inloggedMemberID']."'";
			$db->runSQL($sql);
			return true;
		}
		return false;
	}
	
	function clearStatus($memberID) {
		global $forumSettings;
		if($forumSettings['activateOnline'] && !empty($memberID)) {
			$db = new dbHandler;
			$memberID = $db->SQLsecure($memberID);
			$sql = "UPDATE _'pfx'_members SET lastActive = '0' WHERE memberID = '".$memberID."'";
			$db->runSQL($sql);
			return true;
		}
		return false;
	}
}
?>