<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once("dbHandler.php");			//Includes the databasehandler
require_once("process.php");

class memberHandler {	//Handler the main member functions
	
	function memberHandler() {}
	
	function add($userName,$firstName,$sureName,$email,$password,$website="",$location="",$occupation="",$interests="",$ICQ="",$AIM="",$MSN="",$yahoo="",$avatar="",$language="",$signature="",$dateFormat="Y-m-d H:i",$activated=1,$actKey="",$showEmail=true) {	//Adds a member
		$db = new dbHandler();				//Makes a databasehandler to db
		$userName = $db->SQLsecure($userName);
		$firstName = $db->SQLsecure($firstName);
		$sureName = $db->SQLsecure($sureName);
		$email = $db->SQLsecure($email);
		if($showEmail)
			$showEmail = 1;
		else
			$showEmail = 0;	
		$website = $db->SQLsecure($website);
		$location = $db->SQLsecure($location);
		$interests = $db->SQLsecure($interests);
		$ICQ = $db->SQLsecure($ICQ);
		$AIM = $db->SQLsecure($AIM);
		$MSN = $db->SQLsecure($MSN);
		$yahoo = $db->SQLsecure($yahoo);
		$avatar = $db->SQLsecure($avatar);
		$language = $db->SQLsecure($language);
		$signature = $db->SQLsecure($signature); 
		$dateFormat = $db->SQLsecure($dateFormat); 
		$activated = $db->SQLsecure($activated);
		$time = $db->SQLsecure($time);

		$password=strtolower($password);
		$password=crypt(md5($password),md5($password));
		if(!empty($website))
		{
			if(!preg_match("§http://§i",$website) && !empty($website))  
	        { 
		    	$website = "http://".$website; 
	       	}
       	}
		$time1 = time();
		$time2 = time()+1;
		
		$sql = "INSERT INTO _'pfx'_members (userName,firstName,sureName,email,password,admin,loginDate1,loginDate2,homepage,location,occupation,interests,ICQ,AIM,MSN,yahoo,avatar,lang,signature,dateFormat,activated,actKey,registerDate,showEmail) VALUES('".$userName."','".$firstName."','".$sureName."','".$email."','".$password."','0','".$time1."','".$time2."','".$website."','".$location."','".$occupation."','".$interests."','".$ICQ."','".$AIM."','".$MSN."','".$yahoo."','".$avatar."','".$language."','".$signature."','".$dateFormat."','".$activated."','".$actKey."','".time()."','".$showEmail."')"; //The SQL-code that inserts the values to the database
		$db->runSQL($sql); // Runs the SQL
		return $db->insertID();
		
		//$sql = "SELECT memberID FROM members WHERE userName = '".$userName."'";
		//$result = $db->runSQL($sql);
		//$row = mysql_fetch_object($result);
		//$sql = "INSERT INTO memberGroupsRelation (groupID, memberID) VALUES(1,'".$row->memberID."')";
		//$db->runSQL($sql);
	}
	function remove($id) {					//Removes a member
		$db = new dbHandler();				// Makes a databasehander to db
		$id = $db->SQLsecure($id);
		$sql = "DELETE FROM _'pfx'_memberPermissions WHERE memberID = '".$id."'";
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_memberGroupsRelation WHERE memberID = '".$id."'";
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_viewedPosts WHERE memberID = '".$id."'";
		$db->runSQL($sql);
		$sql = "UPDATE _'pfx'_PM SET sender = '2' WHERE sender = '".$id."'";
		$db->runSQL($sql);
		$sql = "Update _'pfx'_PM set reciver = '2' WHERE reciver = '".$id."'";
		$db->runSQL($sql);
		$sql = "SELECT userName FROM _'pfx'_members WHERE memberID = '".$id."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchArray($result);
		$username = $row['userName'];
		$sql = "UPDATE _'pfx'_threads SET memberID='2', ownerGuestName='".$username."' WHERE memberID='".$id."'";
		$db->runSQL($sql);
		$sql = "UPDATE _'pfx'_posts SET madeBy='2', editedBy='2', guestName='".$username."', deletedUser='1' WHERE madeBy='".$id."' OR editedBy='".$id."'";
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_members WHERE memberID='".$id."'";	//The SQL-code that deletes a member by the ID
		$db->runSQL($sql);				//Runs the SQL		
	}
	function getAll($sort=0,$order=0,$start=0,$limit=false) {					//Get member data
		global $forumVariables;
		$db = new dbHandler();				//Creates a databasehandler
		$start = $db->SQLsecure($start);
		$sqlMember = "SELECT _'pfx'_members.*, count(_'pfx'_posts.postID) AS posts FROM _'pfx'_members LEFT JOIN _'pfx'_posts ON _'pfx'_members.memberID = _'pfx'_posts.madeBy WHERE _'pfx'_members.memberID != 2 AND _'pfx'_members.activated = 1 GROUP BY _'pfx'_members.memberID ORDER BY";		//SQL-code that fetch information from groups's table
		
		switch($sort) {
			case 1:
				$sqlMember .= " _'pfx'_members.memberID";
				break;
			case 2:
				$sqlMember .= " _'pfx'_members.userName";
				break;	
			case 3:
				$sqlMember .= " _'pfx'_members.location";
				break;
			case 4:
				$sqlMember .= " posts";
				break;
			case 5:
				$sqlMember .= " _'pfx'_members.email";
				break;
			case 6:
				$sqlMember .= " _'pfx'_members.homepage";
				break;
			case 7:
				$sqlMember .= " posts";
				break;
			default:
				$sqlMember .= " _'pfx'_members.memberID";
		}
		
		if($order) {
			if($order == 1)
				$sqlMember .= " ASC";
			else
				$sqlMember .= " DESC";	
		}
		else
			$sqlMember .= " ASC";
			
		if($sort == 7) {
			if($start+$limit > 10)
				$limit = 10-$start;
			if($limit < 0)
				$limit = 0;	
		}	
		if($limit) {
			$limit = $db->SQLsecure($limit);
			$sqlMember .= " LIMIT ".$start.", ".$limit;	
		}	
		$resultMember = $db->runSQL($sqlMember);		//Runs the SQL
		if($db->numRows($resultMember) == 0)
			return false;
		
		//Get how many members the forum has
		$sql = "SELECT count(memberID) FROM _'pfx'_members WHERE memberID != 2 AND activated = 1";
		$result = $db->runSQL($sql);
		$row = $db->fetchArray($result);
		$numRows = $row[0];
		
		$i = 0;						//Sets the conuntvariable to 0
		while($rowsMember = $db->fetchArray($resultMember)) { //Loops upp the table
			$members[$i]['memberID'] = $rowsMember['memberID'];		//Set values to an array
			$members[$i]['userName'] = $rowsMember['userName'];
			$members[$i]['firstName'] = $rowsMember['firstName'];
			$members[$i]['sureName'] = $rowsMember['sureName'];
			if($rowsMember['showEmail'] || $forumVariables['adminInlogged'])
				$members[$i]['email'] = $rowsMember['email'];
			else
				$members[$i]['email'] = "";
			$members[$i]['showEmail'] = $rowsMember['showEmail'];
			$members[$i]['password'] = $rowsMember['password'];
			$members[$i]['admin'] = $rowsMember['admin'];
			$members[$i]['loginDate1'] = $rowsMember['loginDate1'];
			$members[$i]['loginDate2'] = $rowsMember['loginDate2'];
			$members[$i]['homepage'] = $rowsMember['homepage'];
			$members[$i]['location'] = $rowsMember['location'];
			$members[$i]['occupation'] = $rowsMember['occupation'];
			$members[$i]['interests'] = $rowsMember['interests'];
			$members[$i]['ICQ'] = $rowsMember['ICQ'];
			$members[$i]['AIM'] = $rowsMember['AIM'];
			$members[$i]['MSN'] = $rowsMember['MSN'];
			$members[$i]['yahoo'] = $rowsMember['yahoo'];
			$members[$i]['signature'] = $rowsMember['signature'];
			$members[$i]['dateFormat'] = $rowsMember['dateFormat'];
			$members[$i]['avatar'] = $rowsMember['avatar'];
			$members[$i]['lang'] = $rowsMember['lang'];
			$members[$i]['posts'] = $rowsMember['posts'];
			$members[$i]['numRows'] = $numRows;
			$member[$i]['registerDate'] = $rowsMember['registerDate'];
			$i++; 
		}
		return $members; //Returns an two dimentional array 
	}
	
		function getAllOnline($sort=0,$order=0,$start=0,$limit=false) {				
		global $forumSettings;
		global $forumVariables;
		$db = new dbHandler();				//Creates a databasehandler
		$start = $db->SQLsecure($start);
		$sqlMember = "SELECT _'pfx'_members.*, count(_'pfx'_posts.postID) AS posts FROM _'pfx'_members LEFT JOIN _'pfx'_posts ON _'pfx'_members.memberID = _'pfx'_posts.madeBy WHERE _'pfx'_members.memberID != 2 AND _'pfx'_members.activated = 1 AND _'pfx'_members.lastActive > '".(time()-$forumSettings['onlineViewExpire'])."' GROUP BY _'pfx'_members.memberID ORDER BY";		//SQL-code that fetch information from groups's table
		
		switch($sort) {
			case 1:
				$sqlMember .= " _'pfx'_members.memberID";
				break;
			case 2:
				$sqlMember .= " _'pfx'_members.userName";
				break;	
			case 3:
				$sqlMember .= " _'pfx'_members.location";
				break;
			case 4:
				$sqlMember .= " posts";
				break;
			case 5:
				$sqlMember .= " _'pfx'_members.email";
				break;
			case 6:
				$sqlMember .= " _'pfx'_members.homepage";
				break;
			case 7:
				$sqlMember .= " posts";
				break;
			default:
				$sqlMember .= " _'pfx'_members.memberID";
		}
		
		if($order) {
			if($order == 1)
				$sqlMember .= " ASC";
			else
				$sqlMember .= " DESC";	
		}
		else
			$sqlMember .= " ASC";
			
		if($sort == 7) {
			if($start+$limit > 10)
				$limit = 10-$start;
			if($limit < 0)
				$limit = 0;	
		}	
		if($limit) {
			$limit = $db->SQLsecure($limit);
			$sqlMember .= " LIMIT ".$start.", ".$limit;	
		}	
		$resultMember = $db->runSQL($sqlMember);		//Runs the SQL
		if($db->numRows($resultMember) == 0)
			return false;
		
		//Get how many members the forum has
		$sql = "SELECT count(memberID) FROM _'pfx'_members WHERE memberID != 2 AND activated = 1 AND lastActive > '".(time()-$forumSettings['onlineViewExpire'])."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchArray($result);
		$numRows = $row[0];
		
		$i = 0;						//Sets the conuntvariable to 0
		while($rowsMember = $db->fetchArray($resultMember)) { //Loops upp the table
			$members[$i]['memberID'] = $rowsMember['memberID'];		//Set values to an array
			$members[$i]['userName'] = $rowsMember['userName'];
			$members[$i]['firstName'] = $rowsMember['firstName'];
			$members[$i]['sureName'] = $rowsMember['sureName'];
			if($rowsMember['showEmail'] || $forumVariables['adminInlogged'])
				$members[$i]['email'] = $rowsMember['email'];
			else
				$members[$i]['email'] = "";
			$members[$i]['showEmail'] = $rowsMember['showEmail'];
			$members[$i]['password'] = $rowsMember['password'];
			$members[$i]['admin'] = $rowsMember['admin'];
			$members[$i]['loginDate1'] = $rowsMember['loginDate1'];
			$members[$i]['loginDate2'] = $rowsMember['loginDate2'];
			$members[$i]['homepage'] = $rowsMember['homepage'];
			$members[$i]['location'] = $rowsMember['location'];
			$members[$i]['occupation'] = $rowsMember['occupation'];
			$members[$i]['interests'] = $rowsMember['interests'];
			$members[$i]['ICQ'] = $rowsMember['ICQ'];
			$members[$i]['AIM'] = $rowsMember['AIM'];
			$members[$i]['MSN'] = $rowsMember['MSN'];
			$members[$i]['yahoo'] = $rowsMember['yahoo'];
			$members[$i]['signature'] = $rowsMember['signature'];
			$members[$i]['dateFormat'] = $rowsMember['dateFormat'];
			$members[$i]['avatar'] = $rowsMember['avatar'];
			$members[$i]['lang'] = $rowsMember['lang'];
			$members[$i]['posts'] = $rowsMember['posts'];
			$members[$i]['numRows'] = $numRows;
			$member[$i]['registerDate'] = $rowsMember['registerDate'];
			$i++; 
		}
		return $members; //Returns an two dimentional array 
	}

	function getOne($memberID, $raw=false) {
		global $forumVariables;
		$db = new dbHandler();						//Makes databasehandler to db
		$memberID = $db->SQLsecure($memberID);
		
		$sql = "SELECT * FROM _'pfx'_members WHERE memberID='".$memberID."'";	//SQL-code that fetch the member data
		$result = $db->runSQL($sql);					//Run the SQL-code
		$row = $db->fetchObject($result);				//Fetch the result
		
		$member['memberID'] = $row->memberID;				//Set the data in to an array
		$member['userName'] = $row->userName;
		$member['firstName'] = $row->firstName;
		$member['sureName'] = $row->sureName;
		if($row->showEmail || $raw || $forumVariables['adminInlogged'])
			$member['email'] = $row->email;
		else
			$member['email'] = "";
		$member['showEmail'] = $row->showEmail;
		$member['password'] = $row->password;
		$member['admin'] = $row->admin;
		$member['loginDate1'] = $row->loginDate1;
		$member['loginDate2'] = $row->loginDate2;
		$member['homepage'] = $row->homepage;
		$member['location'] = $row->location;
		$member['occupation'] = $row->occupation;
		$member['interests'] = $row->interests;
		$member['ICQ'] = $row->ICQ;
		$member['AIM'] = $row->AIM;
		$member['MSN'] = $row->MSN;
		$member['yahoo'] = $row->yahoo;
		$member['yahoo'] = $row->yahoo;
		$member['lang'] = $row->lang;
		$member['signature'] = $row->signature;
		$member['dateFormat'] = $row->dateFormat;
		$member['avatar'] = $row->avatar;
		$member['registerDate'] = $row->registerDate;
		$member['alwaysAllowBBCode'] = $row->alwaysAllowBBCode;
		$member['alwaysAllowSmilies'] = $row->alwaysAllowSmilies;
		$member['alwaysNotifyOnReply'] = $row->alwaysNotifyOnReply;
		$member['notifyNewPM'] = $row->notifyNewPM;
		$member['alwaysDisplaySign'] = $row->alwaysDisplaySign;
		
		$sql = "SELECT count(postID) AS posts FROM _'pfx'_posts WHERE madeBy = '".$memberID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchArray($result);
		$member['posts'] = $row['posts'];
		
		if($member['admin'])
			$member['status']['type'] = "admin";
		else {		 
			require_once("./classes/moderatorHandler.php");
			$moderator = new moderatorHandler;
			if($moderatorForums = $moderator->getForums($memberID)) {
				$member['status']['type'] = "moderator";
				$member['status']['forums'] = $moderatorForums;
			}
			else
				$member['status']['type'] = "none";
		}	

		return $member;							//Return member data
	}
		
	function edit($memberID,$userName,$firstName,$sureName,$email,$password,$admin,$website,$location,$occupation,$interests,$ICQ,$AIM,$MSN,$yahoo,$avatar,$language,$signature,$dateFormat,$alwaysAllowBBCode,$alwaysAllowSmilies,$alwaysNotifyOnReply,$notifyNewPM,$alwaysDisplaySign,$showEmail=true) { //Edit a member
		$db = new dbHandler();
		$memberID = $db->SQLsecure($memberID);
		$userName = $db->SQLsecure($userName);
		$firstName = $db->SQLsecure($firstName);
		$sureName = $db->SQLsecure($sureName);
		$email = $db->SQLsecure($email);
		if($showEmail)
			$showEmail = 1;
		else
			$showEmail = 0;	
		$website = $db->SQLsecure($website);
		$location = $db->SQLsecure($location);
		$interests = $db->SQLsecure($interests);
		$ICQ = $db->SQLsecure($ICQ);
		$AIM = $db->SQLsecure($AIM);
		$MSN = $db->SQLsecure($MSN);
		$yahoo = $db->SQLsecure($yahoo);
		$avatar = $db->SQLsecure($avatar);
		$language = $db->SQLsecure($language);
		$signature = $db->SQLsecure($signature);
		$dateFormat = $db->SQLsecure($dateFormat);
		
		if(!empty($password))
		{
			$password=strtolower($password);
			$password=crypt(md5($password),md5($password));
		}
		if(!empty($website))
		{
			if(!preg_match("§http://§i",$website))  
	        { 
		    	$website = "http://".$website; 
	       	}
       	}
		if($admin)
			$admin=1;
		else
			$admin=0;
		if($alwaysAllowBBCode)
			$alwaysAllowBBCode = true;
		else
			$alwaysAllowBBCode = false;
		if($alwaysAllowSmilies)
			$alwaysAllowSmilies = true;
		else
			$alwaysAllowSmilies = false;
		if($alwaysNotifyOnReply)
			$alwaysNotifyOnReply = true;
		else
			$alwaysNotifyOnReply = false;
		if($notifyNewPM)
			$notifyNewPM = true;
		else
			$notifyNewPM = false;
		if($alwaysDisplaySign)
			$alwaysDisplaySign = true;
		else
			$alwaysDisplaySign = false;									
		if(empty($password))
			$sql = "UPDATE _'pfx'_members SET userName='".$userName."', firstName='".$firstName."', sureName='".$sureName."', email='".$email."', admin='".$admin."', homepage='".$website."', location='".$location."', occupation='".$occupation."', interests='".$interests."', ICQ='".$ICQ."', AIM='".$AIM."', MSN='".$MSN."', yahoo='".$yahoo."', avatar='".$avatar."', lang='".$language."', signature='".$signature."', dateFormat='".$dateFormat."', alwaysAllowBBCode='".$alwaysAllowBBCode."', alwaysAllowSmilies='".$alwaysAllowSmilies."', alwaysNotifyOnReply='".$alwaysNotifyOnReply."', notifyNewPM='".$notifyNewPM."', alwaysDisplaySign='".$alwaysDisplaySign."', showEmail = '".$showEmail."' WHERE memberID='".$memberID."'";
		else
			$sql = "UPDATE _'pfx'_members SET userName='".$userName."', firstName='".$firstName."', sureName='".$sureName."', email='".$email."', password='".$password."', admin='".$admin."', homepage='".$website."', location='".$location."', occupation='".$occupation."', interests='".$interests."', ICQ='".$ICQ."', AIM='".$AIM."', MSN='".$MSN."', yahoo='".$yahoo."', avatar='".$avatar."', lang='".$language."', signature='".$signature."', dateFormat='".$dateFormat."', alwaysAllowBBCode='".$alwaysAllowBBCode."', alwaysAllowSmilies='".$alwaysAllowSmilies."', alwaysNotifyOnReply='".$alwaysNotifyOnReply."', notifyNewPM='".$notifyNewPM."', alwaysDisplaySign='".$alwaysDisplaySign."', showEmail = '".$showEmail."' WHERE memberID='".$memberID."'";
		$db->runSQL($sql);
	}
	
	function count() {
		$db = new dbHandler;
		$sql = "SELECT COUNT(memberID) AS members FROM _'pfx'_members WHERE memberID != '2' AND activated = 1";
		$result = $db->runSQL($sql);
		$members = $db->fetchObject($result);
		return $members->members;
	}
	
	function countOnline() {
		global $forumSettings;
		if($forumSettings['activateOnline']) {
			$db = new dbHandler;
			$sql = "SELECT COUNT(memberID) AS online FROM _'pfx'_members WHERE lastActive > '".(time()-$forumSettings['onlineViewExpire'])."' AND activated = 1";
			$result = $db->runSQL($sql);
			if($row = $db->fetchArray($result))
				return $row['online'];
			else
				return false;	
		}
		else
			return false;
	}
	
	function getMemberID($userName) {
		$db = new dbHandler;
		$userName = $db->SQLsecure($userName);
		
		$sql = "SELECT memberID FROM _'pfx'_members WHERE userName = '".$userName."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$row = $db->fetchObject($result);
		return $row->memberID;
	}
	
	function removeUnactivated() {
		$db = new dbHandler;
		$time = time() - 86400;
		$sql = "DELETE FROM _'pfx'_members WHERE activated = 0 AND loginDate1 < '".$time."'";
		$result = $db->runSQL($sql);
		
		return $db->affectedRows();
		//while($row = $db->fetchArray($result)) {
		//	$sql = "DELETE FROM _'pfx'_members WHERE memberID = '".$row['memberID']."'";
		//	$db->runSQL($sql);
		//}
	}
}
?>