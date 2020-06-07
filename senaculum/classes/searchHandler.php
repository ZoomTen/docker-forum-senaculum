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

class searchHandler {

	function searchHandler() {}

	function search($keywords,$mode,$start=0,$limit=false) {
		$db = new dbHandler;
		require_once('process.php');
		require_once('permissionHandler.php');
		global $forumVariables;
		global $forumSettings;
		$process = new process;
		$permission = new permissionHandler;
		$keywords = $process->censur($keywords);
		$keywords = $db->SQLsecure($keywords);
		$start = $db->SQLsecure($start);
		$permissions = $permission->permissions("read");

		//Get hidden forums and make sql to not view hidden forums
		$sqlHidden = "";
		foreach($permissions as $element) {
			if(!$element['permission']) {
				$sqlHidden .= " AND _'pfx'_threads.forumID != '".$element['forumID']."'";
			}
		}

		switch($mode) {
			case "posts":
				$sql = "SELECT _'pfx'_posts.*, _'pfx'_threads.headline AS threadHeadline, _'pfx'_threads.memberID AS threadOwnerID, _'pfx'_threads.type, _'pfx'_threads.status, _'pfx'_threads.poll, _'pfx'_threads.ownerGuestName, _'pfx'_forums.forumID, _'pfx'_forums.name, _'pfx'_threads.posts,_'pfx'_members.userName AS threadOwnerUserName, m.memberID AS lastPostMemberID, m.userName AS lastPostUserName, p.threadID AS lastPostThreadID, p.lastEdit AS lastPost, p.postID AS lastPostID, p.guestName AS lastPostGuestName, MATCH (_'pfx'_posts.headline,_'pfx'_posts.text) AGAINST ('".$keywords."' IN BOOLEAN MODE) AS score FROM _'pfx'_posts INNER JOIN _'pfx'_threads ON _'pfx'_posts.threadID = _'pfx'_threads.threadID INNER JOIN _'pfx'_forums ON _'pfx'_threads.forumID = _'pfx'_forums.forumID INNER JOIN _'pfx'_members ON _'pfx'_threads.memberID = _'pfx'_members.memberID INNER JOIN _'pfx'_members m ON p.postID = _'pfx'_threads.lastPost INNER JOIN _'pfx'_posts p ON p.madeBy = m.memberID WHERE MATCH (_'pfx'_posts.headline,_'pfx'_posts.text) AGAINST ('".$keywords."' IN BOOLEAN MODE) GROUP BY _'pfx'_threads.threadID";
				$sqlNumRows = "SELECT count(DISTINCT _'pfx'_threads.threadID) FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE MATCH (_'pfx'_posts.headline,_'pfx'_posts.text) AGAINST ('".$keywords."' IN BOOLEAN MODE) ".$sqlHidden." GROUP BY _'pfx'_threads.type";
				break;
			case "postsAndAuthors":
				$sql = "SELECT _'pfx'_posts.*, _'pfx'_threads.headline AS threadHeadline, _'pfx'_threads.memberID AS threadOwnerID, _'pfx'_threads.type, _'pfx'_threads.status, _'pfx'_threads.poll, _'pfx'_threads.ownerGuestName, _'pfx'_forums.forumID, _'pfx'_forums.name, _'pfx'_threads.posts,_'pfx'_members.userName AS threadOwnerUserName, m.memberID AS lastPostMemberID, m.userName AS lastPostUserName, p.threadID AS lastPostThreadID, p.lastEdit AS lastPost, p.postID AS lastPostID, p.guestName AS lastPostGuestName, MATCH (_'pfx'_posts.headline,_'pfx'_posts.text, m2.userName) AGAINST ('".$keywords."' IN BOOLEAN MODE) AS score FROM _'pfx'_posts INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_forums INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m INNER JOIN _'pfx'_posts p INNER JOIN _'pfx'_members m2 ON _'pfx'_posts.threadID = _'pfx'_threads.threadID ON _'pfx'_threads.forumID = _'pfx'_forums.forumID ON _'pfx'_threads.memberID = _'pfx'_members.memberID ON p.postID = _'pfx'_threads.lastPost ON p.madeBy = m.memberID ON m2.memberID = _'pfx'_posts.madeBy WHERE MATCH (_'pfx'_posts.headline,_'pfx'_posts.text, m2.userName) AGAINST ('".$keywords."' IN BOOLEAN MODE) GROUP BY _'pfx'_threads.threadID";
				$sqlNumRows = "SELECT count(DISTINCT _'pfx'_threads.threadID) FROM _'pfx'_threads INNER JOIN _'pfx'_posts INNER JOIN _'pfx'_members ON _'pfx'_threads.threadID = _'pfx'_posts.threadID ON _'pfx'_posts.madeBy = _'pfx'_members.memberID WHERE MATCH (_'pfx'_posts.headline,_'pfx'_posts.text, _'pfx'_members.userName) AGAINST ('".$keywords."' IN BOOLEAN MODE) ".$sqlHidden." GROUP BY _'pfx'_threads.type";
				break;
			case "authors":
				$sql = "SELECT _'pfx'_posts.*, _'pfx'_threads.headline AS threadHeadline, _'pfx'_threads.memberID AS threadOwnerID, _'pfx'_threads.type, _'pfx'_threads.status, _'pfx'_threads.poll, _'pfx'_threads.ownerGuestName, _'pfx'_forums.forumID, _'pfx'_forums.name, _'pfx'_threads.posts,_'pfx'_members.userName AS threadOwnerUserName, m.memberID AS lastPostMemberID, m.userName AS lastPostUserName, p.threadID AS lastPostThreadID, p.lastEdit AS lastPost, p.postID AS lastPostID, p.guestName AS lastPostGuestName, MATCH (m2.userName) AGAINST ('".$keywords."' IN BOOLEAN MODE) AS score FROM _'pfx'_posts INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_forums INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m INNER JOIN _'pfx'_posts p INNER JOIN _'pfx'_members m2 ON _'pfx'_posts.threadID = _'pfx'_threads.threadID ON _'pfx'_threads.forumID = _'pfx'_forums.forumID ON _'pfx'_threads.memberID = _'pfx'_members.memberID ON p.postID = _'pfx'_threads.lastPost ON p.madeBy = m.memberID ON m2.memberID = _'pfx'_posts.madeBy WHERE MATCH (m2.userName) AGAINST ('".$keywords."' IN BOOLEAN MODE) GROUP BY _'pfx'_threads.threadID";
				$sqlNumRows = "SELECT count(DISTINCT _'pfx'_threads.threadID) FROM _'pfx'_threads INNER JOIN _'pfx'_posts INNER JOIN _'pfx'_members ON _'pfx'_threads.threadID = _'pfx'_posts.threadID ON _'pfx'_posts.madeBy = _'pfx'_members.memberID WHERE MATCH (_'pfx'_members.userName) AGAINST ('".$keywords."' IN BOOLEAN MODE) ".$sqlHidden." GROUP BY _'pfx'_threads.type";
				break;
			case "my":
				$sql = "SELECT _'pfx'_posts.*, _'pfx'_threads.headline AS threadHeadline, _'pfx'_threads.memberID AS threadOwnerID, _'pfx'_threads.type, _'pfx'_threads.status, _'pfx'_threads.poll, _'pfx'_threads.ownerGuestName, _'pfx'_forums.forumID, _'pfx'_forums.name, _'pfx'_threads.posts,_'pfx'_members.userName AS threadOwnerUserName, m.memberID AS lastPostMemberID, m.userName AS lastPostUserName, p.threadID AS lastPostThreadID, p.lastEdit AS lastPost, p.postID AS lastPostID, p.guestName AS lastPostGuestName FROM _'pfx'_posts INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_forums INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m INNER JOIN _'pfx'_posts p ON _'pfx'_posts.threadID = _'pfx'_threads.threadID ON _'pfx'_threads.forumID = _'pfx'_forums.forumID ON _'pfx'_threads.memberID = _'pfx'_members.memberID ON p.postID = _'pfx'_threads.lastPost ON p.madeBy = m.memberID WHERE _'pfx'_posts.madeBy = '".$forumVariables['inloggedMemberID']."' GROUP BY _'pfx'_threads.threadID ORDER BY _'pfx'_threads.lastEdit DESC";
				$sqlNumRows = "SELECT count(DISTINCT _'pfx'_threads.threadID) FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_posts.madeBy = '".$forumVariables['inloggedMemberID']."' ".$sqlHidden." GROUP BY _'pfx'_threads.type";
				break;
			case "new":
				$sql = "SELECT _'pfx'_posts.*, _'pfx'_threads.headline AS threadHeadline, _'pfx'_threads.memberID AS threadOwnerID, _'pfx'_threads.type, _'pfx'_threads.status, _'pfx'_threads.poll, _'pfx'_threads.ownerGuestName, _'pfx'_forums.forumID, _'pfx'_forums.name, _'pfx'_threads.posts,_'pfx'_members.userName AS threadOwnerUserName, m.memberID AS lastPostMemberID, m.userName AS lastPostUserName, p.threadID AS lastPostThreadID, p.lastEdit AS lastPost, p.postID AS lastPostID, p.guestName AS lastPostGuestName FROM _'pfx'_posts INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_forums INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m INNER JOIN _'pfx'_posts p ON _'pfx'_posts.threadID = _'pfx'_threads.threadID ON _'pfx'_threads.forumID = _'pfx'_forums.forumID ON _'pfx'_threads.memberID = _'pfx'_members.memberID ON p.postID = _'pfx'_threads.lastPost ON p.madeBy = m.memberID WHERE _'pfx'_posts.lastEdit > '".$forumVariables['lastLoginDate']."' GROUP BY _'pfx'_threads.threadID ORDER BY _'pfx'_threads.lastEdit DESC";
				$sqlNumRows = "SELECT count(DISTINCT _'pfx'_threads.threadID) FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_posts.lastEdit > '".$forumVariables['lastLoginDate']."' ".$sqlHidden." GROUP BY _'pfx'_threads.type";
				break;
			case "unanswered":
				$sql = "SELECT _'pfx'_posts.*, _'pfx'_threads.headline AS threadHeadline, _'pfx'_threads.memberID AS threadOwnerID, _'pfx'_threads.type, _'pfx'_threads.status, _'pfx'_threads.poll, _'pfx'_threads.ownerGuestName, _'pfx'_forums.forumID, _'pfx'_forums.name, _'pfx'_threads.posts,_'pfx'_members.userName AS threadOwnerUserName, m.memberID AS lastPostMemberID, m.userName AS lastPostUserName, p.threadID AS lastPostThreadID, p.lastEdit AS lastPost, p.postID AS lastPostID, p.guestName AS lastPostGuestName FROM _'pfx'_posts INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_forums INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m INNER JOIN _'pfx'_posts p ON _'pfx'_posts.threadID = _'pfx'_threads.threadID ON _'pfx'_threads.forumID = _'pfx'_forums.forumID ON _'pfx'_threads.memberID = _'pfx'_members.memberID ON p.postID = _'pfx'_threads.lastPost ON p.madeBy = m.memberID WHERE _'pfx'_threads.posts <= 1 GROUP BY _'pfx'_threads.threadID ORDER BY _'pfx'_threads.lastEdit DESC";
				$sqlNumRows = "SELECT count(threadID) FROM _'pfx'_threads WHERE posts <= 1 ".$sqlHidden;
				break;
			default:
				$sql = "SELECT _'pfx'_posts.*, _'pfx'_threads.headline AS threadHeadline, _'pfx'_threads.memberID AS threadOwnerID, _'pfx'_threads.type, _'pfx'_threads.status, _'pfx'_threads.poll, _'pfx'_threads.ownerGuestName, _'pfx'_forums.forumID, _'pfx'_forums.name, _'pfx'_threads.posts,_'pfx'_members.userName AS threadOwnerUserName, m.memberID AS lastPostMemberID, m.userName AS lastPostUserName, p.threadID AS lastPostThreadID, p.lastEdit AS lastPost, p.postID AS lastPostID, p.guestName AS lastPostGuestName, MATCH (_'pfx'_posts.headline,_'pfx'_posts.text) AGAINST ('".$keywords."' IN BOOLEAN MODE) AS score FROM _'pfx'_posts INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_forums INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m INNER JOIN _'pfx'_posts p ON _'pfx'_posts.threadID = _'pfx'_threads.threadID ON _'pfx'_threads.forumID = _'pfx'_forums.forumID ON _'pfx'_threads.memberID = _'pfx'_members.memberID ON p.postID = _'pfx'_threads.lastPost ON p.madeBy = m.memberID WHERE MATCH (_'pfx'_posts.headline,_'pfx'_posts.text) AGAINST ('".$keywords."' IN BOOLEAN MODE) GROUP BY _'pfx'_threads.threadID";
				$sqlNumRows = "SELECT count(DISTINCT _'pfx'_threads.threadID) FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE MATCH (_'pfx'_posts.headline,_'pfx'_posts.text) AGAINST ('".$keywords."' IN BOOLEAN MODE) ".$sqlHidden." GROUP BY _'pfx'_threads.type";
				break;
		}
		if($limit)
			$sql .= " LIMIT ".$start.", ".$db->SQLsecure($limit);
		$result = $db->runSQL($sql);

		$numRows = 0;
		$resultNumRows = $db->runSQL($sqlNumRows);
		while($rowNumRows = $db->fetchArray($resultNumRows))
			$numRows += $rowNumRows[0];
		if($forumVariables['inlogged']) {
			if($forumSettings['smartNewPosts']) {
				$sqlSmartNewPosts = "SELECT postID FROM _'pfx'_viewedPosts WHERE memberID = '".$forumVariables['inloggedMemberID']."'";
				$resultSmartNewPosts = $db->runSQL($sqlSmartNewPosts);
				while($rowSmartNewPosts = $db->fetchArray($resultSmartNewPosts)) {
					$viewedPosts[] = $rowSmartNewPosts['postID'];
				}
				$sqlNewPostsPosts = "SELECT _'pfx'_posts.postID, _'pfx'_posts.threadID, _'pfx'_posts.lastEdit FROM _'pfx'_posts WHERE _'pfx'_posts.lastEdit > '".$forumVariables['lastLoginDate']."'";
				$resultNewPostsPosts = $db->runSQL($sqlNewPostsPosts);
				$k = 0;
				while($rowNewPostsPosts = $db->fetchArray($resultNewPostsPosts)) {
					$newPosts2[$k]['postID'] = $rowNewPostsPosts['postID'];
					$newPosts2[$k]['threadID'] = $rowNewPostsPosts['threadID'];
					$newPosts2[$k]['lastEdit'] = $rowNewPostsPosts['lastEdit'];
					$k++;
				}
			}
			if($forumSettings['markThreadsWithOwnPosts']) {
				$sqlOwnThread = "SELECT _'pfx'_threads.threadID FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_posts.madeBy = '".$forumVariables['inloggedMemberID']."' GROUP BY _'pfx'_threads.threadID";
				$resultOwnThread = $db->runSQL($sqlOwnThread);
				while($rowOwnThread = $db->fetchArray($resultOwnThread)) {
					$ownThreads[] = $rowOwnThread['threadID'];
				}
			}
			$sqlNewPosts = "SELECT _'pfx'_threads.threadID, count(_'pfx'_posts.postID) AS newPosts FROM _'pfx'_posts INNER JOIN _'pfx'_threads ON _'pfx'_posts.threadID = _'pfx'_threads.threadID WHERE _'pfx'_posts.lastEdit>'".$forumVariables['lastLoginDate']."' AND _'pfx'_posts.editedBy != '".$forumVariables['inloggedMemberID']."' GROUP BY _'pfx'_threads.threadID";
			$resultNewPosts = $db->runSQL($sqlNewPosts);
			$i=0;
			while($row = $db->fetchArray($resultNewPosts)) {
				$newPosts[$i]['threadID'] = $row['threadID'];
				$newPosts[$i]['newPosts'] = $row['newPosts'];
				$i++;
			}
		}

		$i = 0;
		while($row = $db->fetchArray($result)) {
			foreach($permissions as $element) {
				if($element['permission'] && $row['forumID'] == $element['forumID']) {
					$search[$i]['forumID'] = $row['forumID'];
					$processForumName[$i] = $search[$i]['forumName'] = $row['name'];
  					$search[$i]['threadID'] = $row['threadID'];
  					$processThreadHeadline[$i] = $search[$i]['threadHeadline'] = $row['threadHeadline'];
  					$search[$i]['postID'] = $row['postID'];
  					$search[$i]['editedBy'] = $row['editedBy'];
  					$search[$i]['lastEdit'] = $row['lastEdit'];
  					$processHeadline[$i] = $search[$i]['headline'] = $row['headline'];
  					$search[$i]['text'] = $row['text'];
  					$search[$i]['date'] = $row['date'];
					$search[$i]['threadOwnerID'] = $row['threadOwnerID'];
					$search[$i]['threadOwnerUserName'] = $row['threadOwnerUserName'];
					$processOwnerGuestName[$i] = $search[$i]['threadOwnerGuestName'] = $row['ownerGuestName'];
					$search[$i]['lastPost'] = $row['lastPost'];
					$search[$i]['lastPostMemberID'] = $row['lastPostMemberID'];
					$search[$i]['lastPostUserName'] = $row['lastPostUserName'];
					$search[$i]['lastPostThreadID'] = $row['lastPostThreadID'];
					$search[$i]['lastPostID'] = $row['lastPostID'];
					$processGuestName[$i] = $search[$i]['lastPostGuestName'] = $row['lastPostGuestName'];
					$search[$i]['posts'] = $row['posts'];
					$search[$i]['status'] = $row['status'];
					$search[$i]['type'] = $row['type'];
					$search[$i]['poll'] = $row['poll'];
					if(!empty($ownThreads) && in_array($search[$i]['threadID'],$ownThreads))
						$search[$i]['ownPostsInThread'] = true;
					else
						$search[$i]['ownPostsInThread'] = false;

					if(!empty($newPosts)) {
						foreach($newPosts as $newPost) {
							if($row['threadID'] == $newPost['threadID'])
								$search[$i]['newPosts'] = $newPost['newPosts'];
						}
					}
					if(empty($search[$i]['newPosts']))
						$search[$i]['newPosts'] = 0;
					if(!empty($viewedPosts) && !empty($newPosts2)) {
						foreach($newPosts2 as $newPost) {
							if($newPost['threadID'] == $search[$i]['threadID'] && in_array($newPost['postID'],$viewedPosts)) {
								if($search[$i]['newPosts'] != 0)
									$search[$i]['newPosts']--;
							}
						}
					}

					$search[$i]['numRows'] = $numRows;
					$i++;
				}
			}
		}
		if(empty($search))
			return false;

		$processForumName = $process->headline($processForumName);
		$processThreadHeadline = $process->headline($processThreadHeadline);
		$processHeadline = $process->headline($processHeadline);
		$processOwnerGuestName = $process->name($processOwnerGuestName);
		$processGuestName = $process->name($processGuestName);

		$i=0;
		foreach($processForumName as $element) {
			$search[$i]['forumName'] = $element;
			$i++;
		}
		$i=0;
		foreach($processThreadHeadline as $element) {
			$search[$i]['threadHeadline'] = $element;
			$i++;
		}
		$i=0;
		foreach($processHeadline as $element) {
			$search[$i]['headline'] = $element;
			$i++;
		}
		$i=0;
		foreach($processOwnerGuestName as $element) {
			$search[$i]['threadOwnerGuestName'] = $element;
			$i++;
		}
		$i=0;
		foreach($processGuestName as $element) {
			$search[$i]['lastPostGuestName'] = $element;
			$i++;
		}


		/*require_once('memberHandler.php');
		$member = new memberHandler;
		$member->getOne($_SESSION['forumMemberID'];
		$loginDate = min($member['loginDate1'],$member['loginDate2']);
		$sql = "SELECT forums.forumID, COUNT( posts.postID ) AS newPosts FROM forums INNER  JOIN threads INNER  JOIN posts ON forums.forumID = threads.forumID ON threads.threadID = posts.threadID WHERE posts.lastEdit >  '".date('Y-m-d H:i:s', $loginDate)."' AND posts.editedBy != '".$_SESSION['forumMemberID']."' GROUP BY forums.forumID";
		$result = $db->runSQL($sql);
		while($row = mysql_fetch_array($result)) {
			$i=0;
			foreach($search as $element) {
				if($element['forumID'] == row['forumID']) {
					$search[$i]['newPosts'] = $row['newPosts'];
				}
				$i++;
			}
		}*/

		return $search;
	}

	function user($userName) {
		$db = new dbHandler;
		$userName = $db->SQLsecure($userName);

		$sql = "SELECT memberID, userName,MATCH (userName) AGAINST ('".$userName."' IN BOOLEAN MODE) AS score FROM _'pfx'_members WHERE MATCH (userName) AGAINST ('".$userName."' IN BOOLEAN MODE)";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$i=0;
		while($row = $db->fetchArray($result)) {
			$users[$i]['memberID'] = $row['memberID'];
			$users[$i]['userName'] = $row['userName'];
			$i++;
		}
		return $users;
	}
}
?>
