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
class bookmarkHandler {
	
	function bookmarkHandler() {}
	
	function add($threadID) {
		global $forumVariables;
		if($forumVariables['inlogged']) {
			$db = new dbHandler;
			$threadID = $db->SQLsecure($threadID);
			
			//Get forumID
			$sql = "SELECT forumID FROM _'pfx'_threads WHERE threadID = '".$threadID."'";
			$result = $db->runSQL($sql);
			if(!$row = $db->fetchArray($result))
				return false;
			$forumID = $row['forumID'];
				
			//Check if you have the right to see the thread, if not, you can't add that bookmark
			require_once("permissionHandler.php");
			$permission = new permissionHandler;
			if($permission->permission($forumID,"read")) {
				$sql = "INSERT IGNORE INTO _'pfx'_bookmarks (memberID, threadID) VALUES('".$forumVariables['inloggedMemberID']."', '".$threadID."')";
				$db->runSQL($sql);
				return true;
			}	
			else
				return false;	
		}
		else
			return false;
	}
	
	function remove($bookmarkID) {
		global $forumVariables;
		if($forumVariables['inlogged']) {
			$db = new dbHandler;
			$bookmarkID = $db->SQLsecure($bookmarkID);
			$sql = "DELETE FROM _'pfx'_bookmarks WHERE bookmarkID = '".$bookmarkID."' AND memberID = '".$forumVariables['inloggedMemberID']."'";
			$db->runSQL($sql);
			return true;
		}
		else
			return false;
	}
	
	function getAll($sort,$start=0,$limit=false,$pageNum=1) {					//Lists threads
		global $forumVariables;
		global $forumSettings;
		if($forumVariables['inlogged']) {
			$process = new process;
			$threads = "";
			$db = new dbHandler();				//Creates a databasehandler
			
			//SQL for fetch the threads and lastPost
			$sqlThreads = "SELECT _'pfx'_bookmarks.bookmarkID, _'pfx'_threads.* ,_'pfx'_posts.postID, _'pfx'_posts.madeBy, _'pfx'_posts.lastEdit AS lastPostDate, _'pfx'_posts.guestName, _'pfx'_members.userName, m.userName AS threadOwnerUserName FROM _'pfx'_threads INNER JOIN _'pfx'_posts INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m INNER JOIN _'pfx'_bookmarks ON _'pfx'_threads.threadID = _'pfx'_posts.threadID ON _'pfx'_posts.madeBy = _'pfx'_members.memberID ON _'pfx'_threads.memberID = m.memberID ON _'pfx'_bookmarks.threadID = _'pfx'_threads.threadID WHERE _'pfx'_bookmarks.memberID = '".$forumVariables['inloggedMemberID']."' AND _'pfx'_threads.lastPost = _'pfx'_posts.postID AND _'pfx'_threads.type != 2 GROUP BY _'pfx'_threads.threadID ";
			$sqlAnnounce = "SELECT _'pfx'_bookmarks.bookmarkID, _'pfx'_threads.* ,_'pfx'_posts.postID, _'pfx'_posts.madeBy, _'pfx'_posts.lastEdit AS lastPostDate, _'pfx'_posts.guestName, _'pfx'_members.userName, m.userName AS threadOwnerUserName FROM _'pfx'_threads INNER JOIN _'pfx'_posts INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m INNER JOIN _'pfx'_bookmarks ON _'pfx'_threads.threadID = _'pfx'_posts.threadID ON _'pfx'_posts.madeBy = _'pfx'_members.memberID ON _'pfx'_threads.memberID = m.memberID ON _'pfx'_bookmarks.threadID = _'pfx'_threads.threadID WHERE _'pfx'_bookmarks.memberID = '".$forumVariables['inloggedMemberID']."' AND _'pfx'_threads.lastPost = _'pfx'_posts.postID AND _'pfx'_threads.type = 2 GROUP BY _'pfx'_threads.threadID ";
			switch($sort) {
				case 1:
					$sqlThreads .= "ORDER BY _'pfx'_threads.`type` DESC, _'pfx'_threads.lastEdit DESC";		//Add sql for sort
					$sqlAnnounce .= "ORDER BY _'pfx'_threads.lastEdit DESC";
					break;
				case 2:
					$sqlThreads .= "ORDER BY _'pfx'_threads.`type` DESC, _'pfx'_threads.lastEdit ASC";
					$sqlAnnounce .= "ORDER BY _'pfx'_threads.lastEdit ASC";
					break;
				case 3:
					$sqlThreads .= "ORDER BY _'pfx'_threads.`type` DESC, _'pfx'_threads.date DESC";
					$sqlAnnounce .= "ORDER BY _'pfx'_threads.date DESC";
					break;
				case 4:
					$sqlThreads .= "ORDER BY _'pfx'_threads.`type` DESC, _'pfx'_threads.date ASC";
					$sqlAnnounce .= "ORDER BY _'pfx'_threads.date ASC";
					break;
				case 5:
					$sqlThreads .= "ORDER BY _'pfx'_threads.`type` DESC, _'pfx'_threads.headline ASC";
					$sqlAnnounce .= "ORDER BY _'pfx'_threads.headline ASC";
					break;
				default:
					$sqlThreads .= "ORDER BY _'pfx'_threads.`type` DESC, _'pfx'_threads.lastEdit DESC";
					$sqlAnnounce .= "ORDER BY _'pfx'_threads.lastEdit DESC";
					break;
			}
			//die($sqlAnnounce);
			$resultAnnounce = $db->runSQL($sqlAnnounce);
			$numAnnounce = $db->numRows($resultAnnounce);
			
			if($limit) {
				//Remove announce threads from the limit so the pagination will work
				$start -= $numAnnounce * ($pageNum-1);
				if($start < 0)
					$start = 0;
				$limit -= $numAnnounce;
				$start = $db->SQLsecure($start);
				$limit = $db->SQLsecure($limit);
				$sqlThreads .= " LIMIT ".$start.", ".$limit;
			}
				
			$resultThreads = $db->runSQL($sqlThreads);	//Runs the SQL
			
			$i = 0;						//Sets the conuntvariable to 0
			
			//Fetch announce threads
			while($rowsThreads = $db->fetchArray($resultAnnounce)) { //Loops the table
				$threads[$i]['bookmarkID'] = $rowsThreads['bookmarkID'];
				$threads[$i]['threadID'] = $rowsThreads['threadID']; //Sets the values to an array
				$threads[$i]['headline'] = $rowsThreads['headline'];
				$threads[$i]['date'] = $rowsThreads['date'];
				$threads[$i]['lastEdit'] = $rowsThreads['lastEdit'];
				$threads[$i]['memberID'] = $rowsThreads['memberID'];
				$threads[$i]['forumID'] = $rowsThreads['forumID'];
				$threads[$i]['lastPost'] = $rowsThreads['lastPostDate'];
				$threads[$i]['lastPostUsername'] = $rowsThreads['userName'];;
				$threads[$i]['lastPostMemberID'] = $rowsThreads['madeBy'];
				$threads[$i]['lastPostPostID'] = $rowsThreads['postID'];
				$threads[$i]['lastPostGuestName'] = $rowsThreads['guestName'];
				$threads[$i]['type'] = $rowsThreads['type'];
				$threads[$i]['status'] = $rowsThreads['status'];
				$threads[$i]['countPosts'] = $rowsThreads['posts'];
				$threads[$i]['memberName'] = $rowsThreads['threadOwnerUserName'];
				$threads[$i]['movedFromID'] = $rowsThreads['movedFromID'];
				$threads[$i]['poll'] = $rowsThreads['poll'];
				$threads[$i]['numAnnounce'] = $numAnnounce;
				$threads[$i]['ownerGuestName'] = $rowsThreads['ownerGuestName'];
				
				$processHeadline[$i] = $threads[$i]['headline'];
				$processOwnerGuestName[$i] = $threads[$i]['ownerGuestName'];
				$processGuestName[$i] = $threads[$i]['lastPostGuestName'];
				
				$i++;							//Count the countvariable
			}
			
			//Fetch the other threads
			while($rowsThreads = $db->fetchArray($resultThreads)) { //Loops the table
				$threads[$i]['bookmarkID'] = $rowsThreads['bookmarkID'];
				$threads[$i]['threadID'] = $rowsThreads['threadID']; //Sets the values to an array
				$threads[$i]['headline'] = $rowsThreads['headline'];
				$threads[$i]['date'] = $rowsThreads['date'];
				$threads[$i]['lastEdit'] = $rowsThreads['lastEdit'];
				$threads[$i]['memberID'] = $rowsThreads['memberID'];
				$threads[$i]['forumID'] = $rowsThreads['forumID'];
				$threads[$i]['lastPost'] = $rowsThreads['lastPostDate'];
				$threads[$i]['lastPostUsername'] = $rowsThreads['userName'];;
				$threads[$i]['lastPostMemberID'] = $rowsThreads['madeBy'];
				$threads[$i]['lastPostPostID'] = $rowsThreads['postID'];
				$threads[$i]['lastPostGuestName'] = $rowsThreads['guestName'];
				$threads[$i]['type'] = $rowsThreads['type'];
				$threads[$i]['status'] = $rowsThreads['status'];
				$threads[$i]['countPosts'] = $rowsThreads['posts'];
				$threads[$i]['memberName'] = $rowsThreads['threadOwnerUserName'];
				$threads[$i]['movedFromID'] = $rowsThreads['movedFromID'];
				$threads[$i]['poll'] = $rowsThreads['poll'];
				$threads[$i]['numAnnounce'] = $numAnnounce;
				$threads[$i]['ownerGuestName'] = $rowsThreads['ownerGuestName'];
				
				$processHeadline[$i] = $threads[$i]['headline'];
				$processOwnerGuestName[$i] = $threads[$i]['ownerGuestName'];
				$processGuestName[$i] = $threads[$i]['lastPostGuestName'];
				
				$i++;							//Count the countvariable
			}
			if(!empty($threads)) {
				if($forumSettings['smartNewPosts']) {
					$sqlSmartNewPosts = "SELECT postID FROM _'pfx'_viewedPosts WHERE memberID = '".$forumVariables['inloggedMemberID']."'";
					$resultSmartNewPosts = $db->runSQL($sqlSmartNewPosts);
					while($rowSmartNewPosts = $db->fetchArray($resultSmartNewPosts)) {
						$viewedPosts[] = $rowSmartNewPosts['postID'];
					}
					$sqlNewPostsPosts = "SELECT _'pfx'_posts.postID, _'pfx'_posts.threadID, _'pfx'_posts.lastEdit FROM _'pfx'_posts INNER JOIN _'pfx'_threads ON _'pfx'_posts.threadID = _'pfx'_threads.threadID WHERE _'pfx'_threads.forumID = '".$forumID."' AND _'pfx'_posts.lastEdit > '".$forumVariables['lastLoginDate']."'";
					$resultNewPostsPosts = $db->runSQL($sqlNewPostsPosts);
					$k = 0;
					while($rowNewPostsPosts = $db->fetchArray($resultNewPostsPosts)) {
						$newPosts[$k]['postID'] = $rowNewPostsPosts['postID'];
						$newPosts[$k]['threadID'] = $rowNewPostsPosts['threadID'];
						$newPosts[$k]['lastEdit'] = $rowNewPostsPosts['lastEdit'];
						$k++;
					}
				}
				if($forumSettings['markThreadsWithOwnPosts']) {
					$sqlOwnThread = "SELECT _'pfx'_threads.threadID FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_posts.madeBy = '".$forumVariables['inloggedMemberID']."' AND _'pfx'_threads.forumID = '".$forumID."' GROUP BY _'pfx'_threads.threadID";
					$resultOwnThread = $db->runSQL($sqlOwnThread);
					while($rowOwnThread = $db->fetchArray($resultOwnThread)) {
						$ownThreads[] = $rowOwnThread['threadID']; 
					}
				}
				$sqlNewPosts = "SELECT _'pfx'_threads.threadID, COUNT( _'pfx'_posts.postID ) AS newPosts FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_posts.lastEdit >  '".$forumVariables['lastLoginDate']."' AND _'pfx'_threads.forumID = '".$forumID."' AND _'pfx'_posts.editedBy != '".$forumVariables['inloggedMemberID']."' GROUP BY _'pfx'_threads.threadID";
				$resultNewPosts = $db->runSQL($sqlNewPosts);
				while($rowNewPosts = $db->fetchArray($resultNewPosts)) {
					$k = 0;
					foreach($threads as $forumsElements) {
						if($forumsElements['threadID'] == $rowNewPosts['threadID'])
							$threads[$k]['newPosts'] = $rowNewPosts['newPosts'];	
						$k++;	
					}
				}
				$k = 0;
				foreach($threads as $forumsElements) {
					if(empty($threads[$k]['newPosts']))
						$threads[$k]['newPosts'] = 0;	
					if(!empty($ownThreads) && in_array($threads[$k]['threadID'],$ownThreads))
						$threads[$k]['ownPostsInThread'] = true;
					else
						$threads[$k]['ownPostsInThread'] = false;
					if(!empty($viewedPosts) && !empty($newPosts)) {
						foreach($newPosts as $newPost) {
							if($newPost['threadID'] == $threads[$k]['threadID'] && in_array($newPost['postID'],$viewedPosts)) {
								if($threads[$k]['newPosts'] != 0)
									$threads[$k]['newPosts']--;
							}
						}		
					}		
					$k++;	
				}	
			}
			if(!empty($processHeadline))
			{
				$processHeadline = $process->headline($processHeadline);
				$k = 0;
				foreach($processHeadline as $processHeadlineElement) {
					$threads[$k]['headline'] = $processHeadlineElement;
					$k++;
				}
			} 
			if(!empty($processOwnerGuestName))
			{
				$processOwnerGuestName = $process->name($processOwnerGuestName);
				$k = 0;
				foreach($processOwnerGuestName as $processOwnerGuestNameElement) {
					$threads[$k]['ownerGuestName'] = $processOwnerGuestNameElement;
					$k++;
				}
			}
			if(!empty($processGuestName))
			{
				$processGuestName = $process->name($processGuestName);
				$k = 0;
				foreach($processGuestName as $processGuestNameElement) {
					$threads[$k]['lastPostGuestName'] = $processGuestNameElement;
					$k++;
				}
			}
			if(!empty($threads))
				return $threads; 				//Returns an two dimentional array 
		}
		else
			return false;		
	}
	
	function countBookmarks() {
		global $forumVariables;
		if($forumVariables['inlogged']) {
			$db = new dbHandler;
			$sql = "SELECT count(*) AS bookmarks FROM _'pfx'_bookmarks WHERE memberID = '".$forumVariables['inloggedMemberID']."'";
			$result = $db->runSQL($sql);
			if($db->numRows($result) <= 0)
				return false;
			$row = $db->fetchArray($result);
			return $row['bookmarks'];	
		}
		else
			return false;
	}
}
?>