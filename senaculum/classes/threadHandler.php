<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

require_once("dbHandler.php");			//includes the databasehandler
require_once("process.php");

class threadHandler {	//Handler the main thread functions

	function threadHandler() {}

	function add($headline,$text,$forumID,$type,$disableBBCode,$disableSmilies,$notifyWhenReply,$attachSign,$ownerGuestName="",$attachments="",$pollQuestion="",$pollOptions="",$pollRunFor="") {				//Adds a thread
		global $forumVariables;
		$db = new dbHandler();				//Makes a databasehandler to db

		$headline = $db->SQLsecure($headline);
		$text = $db->SQLsecure($text);
		$forumID = $db->SQLsecure($forumID);
		$type = $db->SQLsecure($type);
		if($disableBBCode)
			$disableBBCode = 1;
		else
			$disableBBCode = 0;
		if($disableSmilies)
			$disableSmilies = 1;
		else
			$disableSmilies = 0;
		if($notifyWhenReply)
			$notifyWhenReply = 1;
		else
			$notifyWhenReply = 0;
		if($attachSign)
			$attachSign = 1;
		else
			$attachSign = 0;

		if($forumVariables['inlogged'])
			$memberID = $forumVariables['inloggedMemberID'];
		else
			$memberID = 2; //If not a inlogged user sett author to guest-user
		$date = time();
		if(!empty($pollQuestion) && !empty($pollOptions))
			$poll = 1;
		else
			$poll = 0;

		$sql = "INSERT INTO _'pfx'_threads (headline,date,lastEdit,memberID,forumID,type,posts,poll,ownerGuestName) VALUES('".$headline."','".$date."','".$date."','".$memberID."','".$forumID."','".$type."','1','".$poll."','".$ownerGuestName."')"; //The SQL-code that inserts the threadvalues to the database
		$result = $db->runSQL($sql); 						// Runs the SQL
		/*$sql = "SELECT MAX(threadID) AS 'threadID' FROM _'pfx'_threads WHERE forumID='".$forumID."' AND headline='".$headline."' AND memberID='".$memberID."'"; //The SQL-code that fetch the threadID for the created thread
		$result = $db->runSQL($sql);
		$row = mysql_fetch_object($result);
		$threadID = $row->threadID;*/
		global $dbLastID;
		$threadID = $dbLastID;
		$sql = "INSERT INTO _'pfx'_posts (editedBy,lastEdit,headline,text,threadID,madeBy,date,guestName,disableBBCode,disableSmilies,notifyWhenReply,attachSign) VALUES('".$memberID."','".$date."','".$headline."','".$text."','".$threadID."','".$memberID."','".$date."','".$ownerGuestName."','".$disableBBCode."','".$disableSmilies."','".$notifyWhenReply."','".$attachSign."')";	//SQL that inserts a post in the created thread
		$db->runSQL($sql);

		$lastPost = $dbLastID;
		$sql = "UPDATE _'pfx'_threads SET lastPost='".$lastPost."' WHERE threadID='".$threadID."'";
		$db->runSQL($sql);

		$sql = "UPDATE _'pfx'_forums SET lastEdit='".$date."', lastPost='".$lastPost."', threads=threads+1, posts=posts+1 WHERE forumID = '".$forumID."'";
		$db->runSQL($sql);

		if($poll) {
			$pollQuestion = $db->SQLsecure($pollQuestion);
			if(empty($pollRunFor))
				$pollRunFor = 0;
			$pollRunFor = $db->SQLsecure($pollRunFor);
			$sql = "INSERT INTO _'pfx'_polls (threadID,question,startDate,endDate) VALUES('".$threadID."','".$pollQuestion."','".$date."','".($date + $pollRunFor * 86400)."')";
			$db->runSQL($sql);
			$pollID = $dbLastID;

			foreach($pollOptions as $option) {
				$option = $db->SQLsecure($option);
				$sql = "INSERT INTO _'pfx'_pollOptions (threadID,pollID,`option`) VALUES('".$threadID."','".$pollID."','".$option."')";
				$db->runSQL($sql);
			}
		}

		if(!empty($attachments) && is_array($attachments)) {
			require_once("attachmentHandler.php");
			$attachment = new attachmentHandler;
			foreach($attachments as $element) {
				$attachment->add($lastPost,$element['attachmentNumber'],$element['tempName'],$element['filename']);
			}
		}
	}
	function remove($id) {					//Removes a thread
		global $forumVariables;
		global $forumSettings;
		$db = new dbHandler();				// Makes a databasehandler to db
		$id = $db->SQLsecure($id);
		require_once("permissionHandler.php");
		$permission = new permissionHandler;
		require_once("logInOutHandler.php");
		$auth = new logInOutHandler;

		//Update last edited
		$sql = "SELECT forumID, memberID, movedFromID FROM _'pfx'_threads WHERE threadID = '".$id."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$forumID = $row->forumID;
		$owner = $row->memberID;
		$movedFromID = $row->movedFromID;

		//Check permission for delete
		if(!$forumVariables['adminInlogged'] && !$auth->moderator($forumID,"delete")) {
			if(!$permission->permission($forumID,"delete") || $owner != $forumVariables['inloggedMemberID'])
				return false;
		}

		//Delete attached files
		if($forumSettings['attachmentsActivated']) {
			require_once("classes/attachmentHandler.php");
			$attachment = new attachmentHandler;
			$sql = "SELECT postID FROM _'pfx'_posts WHERE threadID = '".$id."'";
			$result = $db->runSQL($sql);
			while($row = $db->fetchArray($result)) {
				$attachment->removeAll($row['postID']);
			}
		}

		$sql = "SELECT _'pfx'_posts.date, _'pfx'_posts.postID, _'pfx'_threads.threadID FROM _'pfx'_forums INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_forums.forumID = _'pfx'_threads.forumID ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_forums.forumID = '".$forumID."' AND _'pfx'_threads.threadID != '".$id."' ORDER BY _'pfx'_posts.date DESC LIMIT 1";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$lastEdit = $row->date;
		$lastPost = $row->postID;
		$lastPostThreadID = $row->threadID;

		/*$sql = "SELECT posts.threadID FROM posts INNER JOIN threads INNER JOIN forums ON posts.threadID = threads.threadID ON threads.forumID = forums.forumID WHERE posts.lastEdit = '".$lastEdit."' AND forums.forumID = '".$forumID."' LIMIT 1";
		$result = $db->runSQL($sql);
		$row = mysql_fetch_object($result);
		$lastEditThreadID = $row->threadID;*/

		/*$sql = "UPDATE threads SET lastEdit = '".$lastEdit."' WHERE threadID = '".$lastEditThreadID."'";
		$db->runSQL($sql);*/

		//Remove thread width including posts
		$sql = "DELETE FROM _'pfx'_threads WHERE threadID=".$id;	//The SQL-code that deletes a thread by the ID
		$db->runSQL($sql);				//Runs the SQL
		$sql = "DELETE FROM _'pfx'_posts WHERE threadID=".$id;
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_polls WHERE threadID=".$id;
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_pollOptions WHERE threadID=".$id;
		$db->runSQL($sql);
		$sql = "DELETE FROM _'pfx'_pollVotes WHERE threadID=".$id;
		$db->runSQL($sql);

		$sql = "SELECT count(_'pfx'_posts.postID) AS posts FROM _'pfx'_posts INNER JOIN _'pfx'_threads ON _'pfx'_posts.threadID = _'pfx'_threads.threadID WHERE _'pfx'_threads.forumID = '".$forumID."' OR _'pfx'_threads.movedFromID = '".$forumID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$posts = $row->posts;

		$sql = "UPDATE _'pfx'_forums SET lastEdit='".$lastEdit."', lastPost='".$lastPost."', threads=threads-1, posts='".$posts."' WHERE forumID = '".$forumID."'";
		$db->runSQL($sql);

		if($movedFromID != 0) {
			$sql = "SELECT count(_'pfx'_posts.postID) AS posts FROM _'pfx'_posts INNER JOIN _'pfx'_threads ON _'pfx'_posts.threadID = _'pfx'_threads.threadID WHERE _'pfx'_threads.forumID = '".$movedFromID."' OR _'pfx'_threads.movedFromID = '".$movedFromID."'";
			$result = $db->runSQL($sql);
			if($row = $db->fetchObject($result))
				$posts = $row->posts;
			else
				$posts = false;
			if($posts !== false) {
				$sql = "UPDATE _'pfx'_forums SET threads=threads-1, posts='".$posts."' WHERE forumID = '".$movedFromID."'";
				$db->runSQL($sql);
			}
		}
	}
	function getAll($forumID,$sort,$start=0,$limit=false,$pageNum=1) {					//Lists threads
		global $forumVariables;
		global $forumSettings;

		$process = new process;
		$threads = "";
		$db = new dbHandler();				//Creates a databasehandler
		if(!is_numeric($forumID))
			return false;
		$forumID = $db->SQLsecure($forumID);

		//SQL for fetch the threads and lastPost
		$sqlThreads = "SELECT _'pfx'_threads.* ,_'pfx'_posts.postID, _'pfx'_posts.madeBy, _'pfx'_posts.lastEdit AS lastPostDate, _'pfx'_posts.guestName, _'pfx'_members.userName, m.userName AS threadOwnerUserName FROM _'pfx'_threads INNER JOIN _'pfx'_posts INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m ON _'pfx'_threads.threadID = _'pfx'_posts.threadID ON _'pfx'_posts.madeBy = _'pfx'_members.memberID ON _'pfx'_threads.memberID = m.memberID WHERE (_'pfx'_threads.forumID = '".$forumID."' OR _'pfx'_threads.movedFromID = '".$forumID."') AND _'pfx'_threads.lastPost = _'pfx'_posts.postID AND _'pfx'_threads.type != 2 GROUP BY _'pfx'_threads.threadID ";
		$sqlAnnounce = "SELECT _'pfx'_threads.* ,_'pfx'_posts.postID, _'pfx'_posts.madeBy, _'pfx'_posts.lastEdit AS lastPostDate, _'pfx'_posts.guestName, _'pfx'_members.userName, m.userName AS threadOwnerUserName FROM _'pfx'_threads INNER JOIN _'pfx'_posts INNER JOIN _'pfx'_members INNER JOIN _'pfx'_members m ON _'pfx'_threads.threadID = _'pfx'_posts.threadID ON _'pfx'_posts.madeBy = _'pfx'_members.memberID ON _'pfx'_threads.memberID = m.memberID WHERE (_'pfx'_threads.forumID = '".$forumID."' OR _'pfx'_threads.movedFromID = '".$forumID."') AND _'pfx'_threads.lastPost = _'pfx'_posts.postID AND _'pfx'_threads.type = 2 GROUP BY _'pfx'_threads.threadID ";
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
			if($forumVariables['inlogged']) {
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
 	function getOne($threadID, $raw=false) {
		global $forumVariables;
		$process = new process;
		$thread = "";
		$db = new dbHandler();						//Makes databasehandler to db
		$threadID = $db->SQLsecure($threadID);
		$sql = "SELECT _'pfx'_threads.*, _'pfx'_forums.name, _'pfx'_forumGroups.*, _'pfx'_members.userName FROM _'pfx'_threads INNER JOIN _'pfx'_forums INNER JOIN _'pfx'_forumGroups INNER JOIN _'pfx'_members ON _'pfx'_threads.forumID = _'pfx'_forums.forumID ON _'pfx'_forums.groupID = _'pfx'_forumGroups.groupID ON _'pfx'_threads.memberID = _'pfx'_members.memberID WHERE threadID='".$threadID."'";	//SQL-code that fetch the thread data
		$result = $db->runSQL($sql);					//Run the SQL-code
		if($db->numRows($result) <= 0)
			return $thread;
		$row = $db->fetchObject($result);				//Fetch the result
		$thread['threadID'] = $row->threadID;				//Set the data in to an array
		if(!$raw)
		{
		$thread['headline'] = $process->headline($row->headline);
		}
		else
		{
		$thread['headline'] = $row->headline;
		}
		$thread['date'] = $row->lastEdit;
		$thread['lastEdit'] = $row->lastEdit;
		$thread['memberID'] = $row->memberID;
		$thread['forumID'] = $row->forumID;
		$thread['type'] = $row->type;
		$thread['status'] = $row->status;
		$thread['posts'] = $row->posts;

		$thread['forumName'] = $row->name;		//Set the forumname to the array
		$thread['forumGroupID'] = $row->groupID;	//Set the forumgroupID to the array

		$thread['forumGroupName'] = $row->groupName;

		$thread['memberName'] = $row->userName;	//Set the membername to the array
		$thread['movedFromID'] = $row->movedFromID;

		$thread['poll'] = $row->poll;
		if($thread['poll']) {
			$sql = "SELECT * FROM _'pfx'_polls WHERE threadID = '".$threadID."' ORDER BY threadID DESC LIMIT 1";
			$result = $db->runSQL($sql);
			$row = $db->fetchArray($result);
			$thread['pollID'] = $row['pollID'];
			if($raw)
				$thread['pollQuestion'] = $row['question'];
			else
				$thread['pollQuestion'] = $process->headline($row['question']);
			$thread['pollStartDate'] = $row['startDate'];
			$thread['pollEndDate'] = $row['endDate'];

			if($thread['pollEndDate'] < time() && $thread['pollEndDate'] != $thread['pollStartDate']) {
				$sql = "DELETE FROM _'pfx'_polls WHERE threadID=".$threadID;
				$db->runSQL($sql);
				$sql = "DELETE FROM _'pfx'_pollOptions WHERE threadID=".$threadID;
				$db->runSQL($sql);
				$sql = "DELETE FROM _'pfx'_pollVotes WHERE threadID=".$threadID;
				$db->runSQL($sql);
				$sql = "UPDATE _'pfx'_threads SET poll = '0' WHERE threadID = '".$threadID."'";
				$db->runSQL($sql);
				$thread['poll'] = 0;
				$thread['pollID'] = "";
				$thread['pollQuestion'] = "";
				$thread['pollStartDate'] = "";
				$thread['pollEndDate'] = "";
			}
			else {
				$sql = "SELECT * FROM _'pfx'_pollOptions WHERE threadID = '".$threadID."' ORDER BY optionID ASC";
				$result = $db->runSQL($sql);
				$i = 0;
				while($row = $db->fetchArray($result)) {
					$thread['pollOptions'][$i]['optionID'] = $row['optionID'];
					$thread['pollOptions'][$i]['option'] = $row['option'];
					if(!$raw)
						$processPollOption[$i] = $row['option'];
					$thread['pollOptions'][$i]['votes'] = $row['votes'];
					$i++;
				}
				$processPollOption = $process->headline($processPollOption);
				if(!empty($processPollOption)) {
					$i = 0;
					foreach($processPollOption as $processPollOptionElement) {
						$thread['pollOptions'][$i]['option'] = $processPollOptionElement;
						$i++;
					}
				}

				if($forumVariables['inlogged']) {
					$sql = "SELECT memberID FROM _'pfx'_pollVotes WHERE memberID = '".$forumVariables['inloggedMemberID']."' AND threadID = '".$threadID."'";
					$result = $db->runSQL($sql);
					if($db->numRows($result) != 0)
						$thread['pollVoted'] = true;
					else
						$thread['pollVoted'] = false;
				}
				else {
					$sql = "SELECT userIP FROM _'pfx'_pollVotes WHERE userIP = '".$_SERVER['REMOTE_ADDR']."' AND threadID = '".$threadID."'";
					$result = $db->runSQL($sql);
					if($db->numRows($result) != 0)
						$thread['pollVoted'] = true;
					else
						$thread['pollVoted'] = false;
				}
			}
		}
		return $thread;							//Return thread data
	}
	function edit($threadID,$headline,$type,$pollQuestion="",$pollOptions="",$pollRunFor="") { //Edit a thread
		$db = new dbHandler();
		$threadID =  $db->SQLsecure($threadID);
		$headline = $db->SQLsecure($headline);
		$type = $db->SQLsecure($type);
		if($disableBBCode)
			$disableBBCode = 1;
		else
			$disableBBCode = 0;
		if($disableSmilies)
			$disableSmilies = 1;
		else
			$disableSmilies = 0;
		if($notifyWhenReply)
			$notifyWhenReply = 1;
		else
			$notifyWhenReply = 0;
		if($attachSign)
			$attachSign = 1;
		else
			$attachSign = 0;

		if(empty($pollQuestion) || empty($pollOptions)) {
			$sql = "SELECT poll FROM _'pfx'_threads WHERE threadID = '".$threadID."'";
			$result = $db->runSQL($sql);
			$row = $db->fetchArray($result);
			if($row['poll']) {
				$sql = "DELETE FROM _'pfx'_polls WHERE threadID=".$threadID;
				$db->runSQL($sql);
				$sql = "DELETE FROM _'pfx'_pollOptions WHERE threadID=".$threadID;
				$db->runSQL($sql);
				$sql = "DELETE FROM _'pfx'_pollVotes WHERE threadID=".$threadID;
				$db->runSQL($sql);
			}
			$poll = 0;
		}
		elseif(!empty($pollQuestion) && !empty($pollOptions)) {
			$sql = "SELECT * FROM _'pfx'_pollOptions WHERE threadID = '".$threadID."'";
			$result = $db->runSQL($sql);
			while($row = $db->fetchArray($result)) {
				if(!in_array($row['option'],$pollOptions)) {
					$sql = "DELETE FROM _'pfx'_pollOptions WHERE optionID = '".$db->SQLsecure($row['optionID'])."'";
					$db->runSQL($sql);
					$sql = "DELETE FROM _'pfx'_pollVotes WHERE optionID = '".$db->SQLsecure($row['optionID'])."'";
					$db->runSQL($sql);
				}
				$options[] = $row['option'];
				$pollID = $row['pollID'];
			}

				$pollQuestion = $db->SQLsecure($pollQuestion);
				if(empty($pollRunFor))
					$pollRunFor = 0;
				$pollRunFor = $db->SQLsecure($pollRunFor);
				$date = time();

			if($db->numRows($result) != 0) {
				foreach($pollOptions as $option) {
					if(!in_array($option,$options)) {
						$option = $db->SQLsecure($option);
						$sql = "INSERT INTO _'pfx'_pollOptions (pollID,threadID,`option`) VALUES('".$pollID."','".$threadID."','".$option."')";
						$db->runSQL($sql);
					}
				}
				$sql = "UPDATE _'pfx'_polls SET question = '".$pollQuestion."',startDate = '".$date."',endDate = '".($date + $pollRunFor * 86400)."' WHERE threadID = '".$threadID."'";
				$db->runSQL($sql);
			}
			else {
				$sql = "INSERT INTO _'pfx'_polls (threadID,question,startDate,endDate) VALUES('".$threadID."','".$pollQuestion."','".$date."','".($date + $pollRunFor * 86400)."')";
				$db->runSQL($sql);
				global $dbLastID;
				$pollID = $dbLastID;

				$sql = "SELECT pollID FROM _'pfx'_polls WHERE threadID = '".$threadID."'";
				$result = $db->runSQL($sql);
				$row = $db->fetchArray($result);
				$pollID = $row['pollID'];
				foreach($pollOptions as $option) {
					$option = $db->SQLsecure($option);
					$sql = "INSERT INTO _'pfx'_pollOptions (pollID,threadID,`option`) VALUES('".$pollID."','".$threadID."','".$option."')";
					$db->runSQL($sql);
				}
			}
			$poll = 1;
		}

		$sql = "UPDATE _'pfx'_threads SET headline='".$headline."', type='".$type."', poll='".$poll."' WHERE threadID='".$threadID."'";
		$db->runSQL($sql);
	}

	function lock($threadID) {
		$db = new dbHandler();
		$threadID = $db->SQLsecure($threadID);
		$sql = "UPDATE _'pfx'_threads SET status = '1' WHERE threadID = '".$threadID."'";
		$db->runSQL($sql);
	}

	function unlock($threadID) {
		$db = new dbHandler();
		$threadID = $db->SQLsecure($threadID);
		$sql = "UPDATE _'pfx'_threads SET status = '0' WHERE threadID = '".$threadID."'";
		$db->runSQL($sql);
	}

	function move($threadID,$forumIDFrom,$forumIDTo,$shadow=true) {
		$db = new dbHandler();
		$threadID = $db->SQLsecure($threadID);
		$forumIDFrom = $db->SQLsecure($forumIDFrom);
		$forumIDTo = $db->SQLsecure($forumIDTo);
		if($shadow)
			$sql = "UPDATE _'pfx'_threads SET forumID = '".$forumIDTo."', movedFromID = '".$forumIDFrom."' WHERE threadID = '".$threadID."'";
		else
			$sql = "UPDATE _'pfx'_threads SET forumID = '".$forumIDTo."' WHERE threadID = '".$threadID."'";
		$db->runSQL($sql);

		$sql = "SELECT _'pfx'_forums.forumID, count(_'pfx'_posts.postID) AS posts FROM _'pfx'_forums INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_forums.forumID = _'pfx'_threads.forumID OR _'pfx'_forums.forumID = _'pfx'_threads.movedFromID ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_forums.forumID = '".$forumIDTo."' OR _'pfx'_forums.forumID = '".$forumIDFrom."' GROUP BY _'pfx'_forums.forumID";
		$result = $db->runSQL($sql);
		while($row = $db->fetchArray($result)) {
			$sql = "UPDATE _'pfx'_forums SET posts = '".$row['posts']."' WHERE forumID = '".$row['forumID']."'";
			$db->runSQL($sql);
		}

		$sql = "SELECT _'pfx'_forums.forumID, count(_'pfx'_threads.threadID) AS threads FROM _'pfx'_forums INNER JOIN _'pfx'_threads ON _'pfx'_forums.forumID = _'pfx'_threads.forumID OR _'pfx'_forums.forumID = _'pfx'_threads.movedFromID WHERE _'pfx'_forums.forumID = '".$forumIDTo."' OR _'pfx'_forums.forumID = '".$forumIDFrom."' GROUP BY _'pfx'_forums.forumID";
		$result = $db->runSQL($sql);
		while($row = $db->fetchArray($result)) {
			$sql = "UPDATE _'pfx'_forums SET threads = '".$row['threads']."' WHERE forumID = '".$row['forumID']."'";
			$db->runSQL($sql);
		}

		$sql = "SELECT _'pfx'_forums.forumID, MAX(_'pfx'_posts.lastEdit) AS lastEdit, _'pfx'_posts.postID FROM _'pfx'_forums INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_forums.forumID = _'pfx'_threads.forumID ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_forums.forumID = '".$forumIDTo."' OR _'pfx'_forums.forumID = '".$forumIDFrom."' GROUP BY _'pfx'_forums.forumID";
		$result = $db->runSQL($sql);
		while($row = $db->fetchArray($result)) {
			$sql = "UPDATE _'pfx'_forums SET lastEdit = '".$row['lastEdit']."', lastPost = '".$row['postID']."' WHERE forumID = '".$row['forumID']."'";
			$db->runSQL($sql);
		}
	}

	function split($forumID, $forumIDFrom, $threadID, $postID, $headline, $sort) {
		global $forumVariables;
		$db = new dbHandler;
		$forumID = $db->SQLsecure($forumID);
		$forumIDFrom = $db->SQLsecure($forumIDFrom);
		$forumIDTo = $db->SQLsecure($forumIDTo);
		$threadID = $db->SQLsecure($threadID);
		$postID = $db->SQLsecure($postID);
		$headline = $db->SQLsecure($headline);
		if($sort==2)
			$orderBy = "DESC";
		else
			$orderBy = "ASC";
		$sql = "SELECT postID FROM _'pfx'_posts WHERE threadID = '".$threadID."' ORDER BY postID ".$orderBy."";
		$result = $db->runSQL($sql);
		$start = false;
		while($row = $db->fetchArray($result)) {
			if($start)
				$rows[] = $row['postID'];
			if($row['postID'] == $postID)
				$start = true;
		}
		$date = time();
		$sql = "INSERT INTO _'pfx'_threads (headline,date,lastEdit,memberID,forumID,type,posts) VALUES('".$headline."','".$date."','".$date."','".$db->SQLsecure($forumVariables['inloggedMemberID'])."','".$forumID."','0','".count($rows)."')";
		$db->runSQL($sql);
		global $dbLastID;
		$newThreadID = $dbLastID;
		if(!empty($rows)) {
			$i=0;
			foreach($rows as $row) {
				if($i == 0) {
					$sql = "UPDATE _'pfx'_posts SET threadID = '".$newThreadID."', headline = '".$headline."', lastEdit = '".$date."' WHERE postID = '".$row."'";
					$db->runSQL($sql);
					$sql = "UPDATE _'pfx'_forums SET lastEdit = '".$date."', lastPost = '".$row."', threads=threads+1, posts=posts+".count($rows)." WHERE forumID = '".$forumID."'";
					$db->runSQL($sql);
					$sql = "UPDATE _'pfx'_forums SET threads = threads-".count($rows)." WHERE forumID = '".$forumIDFrom."'";
					$db->runSQL($sql);

					$sql = "SELECT _'pfx'_forums.forumID, MAX(_'pfx'_posts.lastEdit) AS lastEdit, _'pfx'_posts.postID FROM _'pfx'_forums INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_forums.forumID = _'pfx'_threads.forumID ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_forums.forumID = '".$forumIDTo."' OR _'pfx'_forums.forumID = '".$forumIDFrom."' GROUP BY _'pfx'_forums.forumID";
					$result = $db->runSQL($sql);
					while($row = $db->fetchArray($result)) {
						$sql = "UPDATE _'pfx'_forums SET lastEdit = '".$row['lastEdit']."', lastPost = '".$row['postID']."' WHERE forumID = '".$row['forumID']."'";
						$db->runSQL($sql);
					}

					$sql = "SELECT _'pfx'_threads.threadID, _'pfx'_posts.postID, MAX(_'pfx'_posts.lastEdit) AS lastEdit FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_threads.threadID = '".$threadID."' OR _'pfx'_threads.threadID = '".$newThreadID."' GROUP BY _'pfx'_threads.threadID";
					$result = $db->runSQL($sql);
					while($row = $db->fetchArray($result)) {
						$sql = "UPDATE _'pfx'_threads SET lastEdit = '".$row['lastEdit']."', lastPost = '".$row['postID']."' WHERE threadID = '".$row['threadID']."'";
						$db->runSQL($sql);
					}
				}
				else {
					$sql = "UPDATE _'pfx'_posts SET threadID = '".$newThreadID."' WHERE postID = '".$row."'";
					$db->runSQL($sql);
				}
				$i++;
			}
		}
	}

	function pollVote($threadID,$optionID) {
		require_once("dbHandler.php");
		$db = new dbHandler;
		$threadID = $db->SQLsecure($threadID);
		$optionID = $db->SQLsecure($optionID);
		global $forumVariables;
		if($forumVariables['inlogged']) {
			$sql = "INSERT INTO _'pfx'_pollVotes (optionID,memberID,userIP,threadID) VALUES('".$optionID."','".$db->SQLsecure($forumVariables['inloggedMemberID'])."','".$db->SQLsecure($_SERVER['REMOTE_ADDR'])."','".$threadID."')";
			$db->runSQL($sql);
		}
		else {
			$sql = "INSERT INTO _'pfx'_pollVotes (optionID,memberID,userIP,threadID) VALUES('".$optionID."','0','".$db->SQLsecure($_SERVER['REMOTE_ADDR'])."','".$threadID."')";
			$db->runSQL($sql);
		}
		$sql = "UPDATE _'pfx'_pollOptions SET votes = votes + 1 WHERE optionID = '".$optionID."'";
		$db->runSQL($sql);
	}

	function markAllAsRead($forumID) {
		global $forumSettings;
		if($forumSettings['smartNewPosts']) {
			global $forumVariables;
			require_once("dbHandler.php");
			$db = new dbHandler;
			$forumID = $db->SQLsecure($forumID);
			$sql = "SELECT _'pfx'_posts.postID FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID  WHERE _'pfx'_posts.lastEdit > '".$forumVariables['lastLoginDate']."' AND _'pfx'_threads.forumID = '".$forumID."'";
			$result = $db->runSQL($sql);
			while($row = $db->fetchArray($result)) {
				$sql = "INSERT IGNORE INTO _'pfx'_viewedPosts (memberID, postID, date) VALUES('".$forumVariables['inloggedMemberID']."','".$row['postID']."','".time()."')";
				$db->runSQL($sql);
			}
		}
		else
			return false;
	}
}
?>
