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

class postHandler {	//Handler the main post functions
	
	function postHandler() {}
	
	function add($headline,$text,$threadID,$disableBBCode,$disableSmilies,$notifyWhenReply,$attachSign,$guestName="",$attachments=0) {		//Adds a post
		global $forumVariables;
		if($forumVariables['inlogged'])
			$madeBy = $forumVariables['inloggedMemberID'];		//Check who is logged in
		else
			$madeBy = 2;	
		$db = new dbHandler();				//Makes a databasehandler to db
		$date = time();
		$madeBy = $db->SQLsecure($madeBy);
		$headline = $db->SQLsecure($headline);
		$text = $db->SQLsecure($text);
		$threadID = $db->SQLsecure($threadID);
		$guestName = $db->SQLsecure($guestName);
		
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
		
		$sql = "INSERT INTO _'pfx'_posts (editedBy,lastEdit,headline,text,threadID,madeBy,date,guestName,disableBBCode,disableSmilies,notifyWhenReply,attachSign) VALUES('".$madeBy."','".$date."','".$headline."','".$text."','".$threadID."','".$madeBy."','".$date."','".$guestName."','".$disableBBCode."','".$disableSmilies."','".$notifyWhenReply."','".$attachSign."')"; //The SQL-code that inserts the values to the database
		$db->runSQL($sql); // Runs the SQL
		global $dbLastID;
		$lastPost = $dbLastID;
		
		$sql = "SELECT forumID FROM _'pfx'_threads WHERE threadID = '".$threadID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$sql = "UPDATE _'pfx'_forums SET lastEdit='".$date."', lastPost='".$lastPost."', posts=posts+1 WHERE forumID = '".$row->forumID."'";
		$db->runSQL($sql);
		$sql = "UPDATE _'pfx'_threads SET lastEdit='".$date."', lastPost='".$lastPost."', posts=posts+1 WHERE threadID = '".$threadID."'";
		$db->runSQL($sql);
		
		if(!empty($attachments) && is_array($attachments)) {
			require_once("attachmentHandler.php");
			$attachment = new attachmentHandler;
			foreach($attachments as $element) {
				$attachment->add($lastPost,$element['attachmentNumber'],$element['tempName'],$element['filename']);
			}
		}
		if(!$forumVariables['inlogged']) {
			$_SESSION['forumLastPostTime'] = time();
			$_SESSION['forumLastPostThread'] = $threadID;
		}
		return $lastPost;
	}
	function remove($postID,$threadID) {	//Removes a post and a thread if the thread is empty
		$db = new dbHandler();				// Makes a databasehander to db
		$postID = $db->SQLsecure($postID);
		$threadID = $db->SQLsecure($threadID);
		require_once("permissionHandler.php");
		$permission = new permissionHandler;
		
		//Update last post
		$sql = "SELECT forumID FROM _'pfx'_threads WHERE threadID = '".$threadID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$forumID = $row->forumID;
		
		//Check permission for delete
		if(!$permission->permission($forumID,"delete"))
			return false;
		
		$sql = "SELECT date, postID FROM  _'pfx'_posts WHERE threadID = '".$threadID."' AND postID != '".$postID."' ORDER BY date DESC LIMIT 1";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$lastEdit = $row->date;
		$lastPost = $row->postID;
		
		$sql = "SELECT _'pfx'_posts.lastEdit, _'pfx'_posts.postID FROM _'pfx'_forums INNER JOIN _'pfx'_threads ON _'pfx'_forums.forumID = _'pfx'_threads.forumID INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_forums.forumID = '".$forumID."' AND _'pfx'_posts.postID != '".$postID."' ORDER BY _'pfx'_posts.lastEdit DESC LIMIT 1";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$lastEditForum = $row->date;
		$lastPostForum = $row->postID;
		
		//die($lastEdit);
		
		/*$sql = "SELECT _'pfx'_posts.threadID FROM _'pfx'_posts INNER JOIN _'pfx'_threads INNER JOIN _'pfx'_forums ON _'pfx'_posts.threadID = _'pfx'_threads.threadID ON _'pfx'_threads.forumID = _'pfx'_forums.forumID WHERE _'pfx'_posts.postID = '".$lastPost."' AND _'pfx'_forums.forumID = '".$forumID."' LIMIT 1";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$lastEditThreadID = $row->threadID;*/
		
		//Delete post
		$sql = "DELETE FROM _'pfx'_posts WHERE postID='".$postID."'";	//The SQL-code that deletes a forum by the ID
		$db->runSQL($sql);				//Runs the SQL
		
		//Delete thread if it contains no posts
		$sql = "SELECT * FROM _'pfx'_posts WHERE threadID='".$threadID."'"; 
		$result = $db->runSQL($sql);
		if($db->numRows($result) <= 0){
			$sql="DELETE FROM _'pfx'_threads WHERE threadID = '".$threadID."'";
			$db->runSQL($sql);
			$threadDeleted = true;
		}
		else
			$threadDeleted = false;
		
		if($threadDeleted)	
			$sql = "UPDATE _'pfx'_forums SET lastEdit = '".$lastEditForum."', lastPost='".$lastPostForum."', threads=threads-1, posts=posts-1 WHERE forumID = '".$forumID."'";
		else
			$sql = "UPDATE _'pfx'_forums SET lastEdit = '".$lastEditForum."', lastPost='".$lastPostForum."', posts=posts-1 WHERE forumID = '".$forumID."'";
		$db->runSQL($sql);
		
		$sql = "UPDATE _'pfx'_threads SET lastEdit = '".$lastEdit."', lastPost='".$lastPost."', posts=posts-1 WHERE threadID = '".$threadID."'";
		$db->runSQL($sql);
		
		//Remove attached files if exist
		require_once("attachmentHandler.php");
		$attachment = new attachmentHandler;
		$attachment->removeAll($postID);
		
		return $threadDeleted;	
	}
	function getAll($threadID, $sort, $start=0, $limit=false, $goToPost=false) {					//Lists posts
		global $forumVariables;
		global $forumSettings;
		$process = new process();
		$posts = "";
		$db = new dbHandler();				//Creates a databasehandler
		$threadID = $db->SQLsecure($threadID);
		
		if($limit) {
			if($goToPost) {
				$start = $db->SQLsecure($start);
				$limit = $db->SQLsecure($limit);
				$sql = "SELECT postID FROM _'pfx'_posts WHERE threadID = '".$threadID."' ORDER BY postID ASC";
				$result = $db->runSQL($sql);
				$i=1;
				while($row = $db->fetchArray($result)) {
					if($row['postID'] == $goToPost)
						break;
					$i++;	
				}
				$start = $page = ceil($i/$limit);
				if($start > 0)
					$start--;
				$start = $start * $limit;
				
				if($page < 1)
					$page = 1;	
			}
			$sqlLimit = " LIMIT ".$start.", ".$limit;
		}	
		else
			$sqlLimit = "";	
		if($sort==2)
		{
			$sqlPost = "SELECT * FROM _'pfx'_posts INNER JOIN _'pfx'_members ON _'pfx'_posts.madeBy = _'pfx'_members.memberID WHERE _'pfx'_posts.threadID ='".$threadID."' ORDER BY _'pfx'_posts.postID DESC".$sqlLimit;	
		}
		else
		{
			$sqlPost = "SELECT * FROM _'pfx'_posts INNER JOIN _'pfx'_members ON _'pfx'_posts.madeBy = _'pfx'_members.memberID WHERE _'pfx'_posts.threadID ='".$threadID."' ORDER BY _'pfx'_posts.postID ASC".$sqlLimit;
		}
		$resultPost = $db->runSQL($sqlPost);		//Runs the SQL
		
		if($db->numRows($resultPost) <= 0)
			return $posts;
		$i = 0;						//Sets the conuntvariable to 0
		while($rowsPost = $db->fetchArray($resultPost)) { //Loops upp the table
			$posts[$i]['postID'] = $rowsPost['postID'];		//Set values to an array
			$posts[$i]['editedBy'] = $rowsPost['editedBy'];
			$posts[$i]['lastEdit'] = $rowsPost['lastEdit'];
			$posts[$i]['headline'] = $rowsPost['headline'];
			$posts[$i]['text'] = $rowsPost['text'];
			$posts[$i]['threadID'] = $rowsPost['threadID'];
			$posts[$i]['authorID'] = $rowsPost['madeBy'];
			$posts[$i]['authorName'] = $rowsPost['userName'];
			$posts[$i]['date'] = $rowsPost['date'];
			$posts[$i]['authorLastActive'] = $rowsPost['lastActive'];
			$posts[$i]['authorRegisterDate'] = $rowsPost['registerDate'];
			$posts[$i]['homepage'] = $rowsPost['homepage'];
			$posts[$i]['location'] = $rowsPost['location'];
			$posts[$i]['occupation'] = $rowsPost['occupation'];
			$posts[$i]['interests'] = $rowsPost['interests'];
			$posts[$i]['ICQ'] = $rowsPost['ICQ'];
			$posts[$i]['AIM'] = $rowsPost['AIM'];
			$posts[$i]['MSN'] = $rowsPost['MSN'];
			$posts[$i]['yahoo'] = $rowsPost['yahoo'];
			if($rowsPost['attachSign'])
				$posts[$i]['signature'] = $rowsPost['signature'];
			else
				$posts[$i]['signature'] = "";	
			$posts[$i]['guestName'] = $rowsPost['guestName'];
			$posts[$i]['deletedUser'] = $rowsPost['deletedUser'];
			$posts[$i]['avatar'] = $rowsPost['avatar'];
			$posts[$i]['admin'] = $rowsPost['admin'];
			$posts[$i]['disableBBCode'] = $rowsPost['disableBBCode'];
			$posts[$i]['disableSmilies'] = $rowsPost['disableSmilies'];
			$posts[$i]['notifyWhenReply'] = $rowsPost['notifyWhenReply'];
			$posts[$i]['attachSign'] = $rowsPost['attachSign'];
			if(!empty($page))
				$posts[$i]['page'] = $page;
			
			/*$sqlThreadOwnerID = "SELECT memberID,headline FROM threads WHERE threadID = '".$threadID."'";	//SQL-code that get the threadowner-ID
			$resultThreadOwnerID = $db->runSQL($sqlThreadOwnerID); //Runs the SQL
			while($rowsThreadOwnerID = mysql_fetch_array($resultThreadOwnerID)) { //Loop the table information
				$posts[$i]['threadOwnerID'] = $rowsThreadOwnerID['memberID'];
				$posts[$i]['threadHeadline'] = $rowsThreadOwnerID['headline'];
			}
			
			$sqlThreadOwnerName = "SELECT userName FROM members WHERE memberID = '".$posts[$i]['threadOwnerID']."'";	//SQL-code that get the threadowner's name
			$resultThreadOwnerName = $db->runSQL($sqlThreadOwnerName);
			while($rowsThreadOwnerName = mysql_fetch_array($resultThreadOwnerName))
				$posts[$i]['threadOwnerName'] = $rowsThreadOwnerName['userName'];*/
				
			$processHeadline[$i] = $posts[$i]['headline'];
			if($posts[$i]['disableBBCode'] || $posts[$i]['disableSmilies']) {
				$processText[$i]['text'] = $posts[$i]['text'];
				$processText[$i]['disableBBCode'] = $posts[$i]['disableBBCode'];
				$processText[$i]['disableSmilies'] = $posts[$i]['disableSmilies'];
			}
			else
				$processText[$i] = $posts[$i]['text'];
			$processSignature[$i] = $posts[$i]['signature'];	
			$i++; //Count up the counter
		}
		$processHeadline = $process->headline($processHeadline);
		$processText = $process->text($processText);
		$processSignature = $process->text($processSignature);
		$k = 0;
		foreach($processHeadline as $processHeadlineElement) {
			$posts[$k]['headline'] = $processHeadlineElement;
			$k++;
		}
		$k = 0;
		foreach($processText as $processTextElement) {
			$posts[$k]['text'] = $processTextElement;
			$k++;
		}
		$k = 0;
		foreach($processSignature as $processTextElement) {
			$posts[$k]['signature'] = $processTextElement;
			$k++;
		}
		
		//Make new posts viewed
		if($forumVariables['inlogged'] && $forumSettings['smartNewPosts']) {
			foreach($posts as $post) {
				if($post['editedBy'] != $forumVariables['inloggedMemberID'] && $post['lastEdit'] > $forumVariables['lastLoginDate']) {
					$sql = "INSERT IGNORE INTO _'pfx'_viewedPosts (memberID, postID, date) VALUES('".$forumVariables['inloggedMemberID']."','".$post['postID']."','".time()."')";
					$db->runSQL($sql);
				}
			}
		}
		
		return $posts; //Returns an two dimentional array 
	}
	function getOne($postID, $raw=false) {
		$process = new process();
		$db = new dbHandler();						//Makes databasehandler to db
		$postID = $db->SQLsecure($postID);
		$sql = "SELECT * FROM _'pfx'_posts INNER JOIN _'pfx'_members ON _'pfx'_posts.madeBy = _'pfx'_members.memberID WHERE _'pfx'_posts.postID='".$postID."'";	//SQL-code that fetch the post data
		$result = $db->runSQL($sql);					//Run the SQL-code
		$row = $db->fetchObject($result);				//Fetch the result
		$post['postID'] = $row->postID;					//Set the data in to an array
		$post['editedBy'] = $row->editedBy;
		$post['lastEdit'] = $row->lastEdit;
		if(!$raw)
		{
		$post['headline'] = $process->headline($row->headline);
		$post['text'] = $process->text($row->text);
		}
		else
		{
		$post['headline'] = $row->headline;
		$post['text'] = $row->text;
		}
		$post['threadID'] = $row->threadID;
		$post['authorID'] = $row->madeBy;	
		$post['authorName'] = $row->userName;
		$post['authorLastActive'] = $row->lastActive;
		$post['authorRegisterDate'] = $row->registerDate;
		$post['date'] = $row->date;
		$post['guestName'] = $row->guestName;
		$post['disableBBCode'] = $row->disableBBCode;
		$post['disableSmilies'] = $row->disableSmilies;
		$post['notifyWhenReply'] = $row->notifyWhenReply;
		$post['attachSign'] = $row->attachSign;
		return $post;							//Return post data
	}
	function edit($postID,$headline,$text,$threadID,$disableBBCode,$disableSmilies,$notifyWhenReply,$attachSign,$attachments="",$deletedOldAttachments="") { //Edit a post
		$db = new dbHandler();
		$date = time();
		$postID = $db->SQLsecure($postID);
		$headline = $db->SQLsecure($headline);
		$text = $db->SQLsecure($text);
		$threadID = $db->SQLsecure($threadID);
		
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
		
		$sql = "UPDATE _'pfx'_posts SET headline='".$headline."', text='".$text."', threadID='".$threadID."', lastEdit='".$date."', disableBBCode='".$disableBBCode."', disableSmilies='".$disableSmilies."', notifyWhenReply='".$notifyWhenReply."', attachSign='".$attachSign."' WHERE postID='".$postID."'";
		$db->runSQL($sql);
		/*$sql = "UPDATE _'pfx'_threads SET lastEdit = '".$date."', lastPost='".$postID."' WHERE threadID = '".$threadID."'";
		$db->runSQL($sql);
		$sql = "SELECT forumID FROM _'pfx'_threads WHERE threadID = '".$threadID."'";
		$result = $db->runSQL($sql);
		$row = mysql_fetch_object($result);
		$sql = "UPDATE _'pfx'_forums SET lastEdit = '".$date."', lastPost='".$postID."' WHERE forumID = '".$row->forumID."'";
		$db->runSQL($sql);*/
		
		if(!empty($deletedOldAttachments) && is_array($deletedOldAttachments)) {
			require_once("attachmentHandler.php");
			$attachment = new attachmentHandler;
			foreach($deletedOldAttachments as $element) {
				$attachment->remove($postID, $element);
			}	
		}
		if(!empty($attachments) && is_array($attachments)) {
			require_once("attachmentHandler.php");
			$attachment = new attachmentHandler;
			foreach($attachments as $element) {
				$attachment->add($postID,$element['attachmentNumber'],$element['tempName'],$element['filename']);
			}
		}
	}
	function forumIDFromPostID($postID) {
		$db = new dbHandler;
		$postID = $db->SQLsecure($postID);
		$sql = "SELECT _'pfx'_threads.forumID FROM _'pfx'_posts, _'pfx'_threads WHERE _'pfx'_threads.threadID = _'pfx'_posts.threadID AND _'pfx'_posts.postID = '".$postID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		return $row->forumID;
	}
	function forumIDFromThreadID($threadID) {
		$db = new dbHandler;
		$threadID = $db->SQLsecure($threadID);
		$sql = "SELECT forumID FROM _'pfx'_threads WHERE threadID = '".$threadID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		return $row->forumID;
	}
	
	function newPostReplyCount() {
		global $forumVariables;
		global $forumSettings;
		if(!$forumSettings['viewPostRepliesCount'])
			return false;
		$db = new dbHandler;
		//Get threads where you have written in
		$sql = "SELECT _'pfx'_threads.threadID FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID WHERE _'pfx'_posts.madeBy = '".$forumVariables['inloggedMemberID']."' AND _'pfx'_threads.lastEdit > '".$forumVariables['lastLoginDate']."' GROUP BY _'pfx'_threads.threadID";
		$result = $db->runSQL($sql);
		if($db->numRows($result) <= 0)
			return false;	
		//Do the count
		$sql = "SELECT count(postID) AS posts FROM _'pfx'_posts WHERE lastEdit > '".$forumVariables['lastLoginDate']."' AND editedBy != '".$forumVariables['inloggedMemberID']."'";
		$first = true;
		$threads = "";
		while($row = $db->fetchArray($result)) {
			if($first) {
				$threads .= " AND (threadID='".$row['threadID']."'";
				$first = false;
			}
			else	
				$threads .= " OR threadID='".$row['threadID']."'";
		}
		if($db->numRows($result) > 0)
			$threads .= ")"; 
		$sql .= $threads;	
		$result = $db->runSQL($sql);
		if($db->numRows($result) <= 0)
			return false;	
		$row = $db->fetchArray($result);
		$posts = $row['posts'];
		
		//Pick away posts that have been viewed
		$sql = "SELECT count(_'pfx'_posts.postID) AS posts FROM _'pfx'_viewedPosts INNER JOIN _'pfx'_posts ON _'pfx'_viewedPosts.postID = _'pfx'_posts.postID WHERE _'pfx'_viewedPosts.memberID = '".$forumVariables['inloggedMemberID']."' AND _'pfx'_posts.lastEdit > '".$forumVariables['lastLoginDate']."'";
		$sql .= $threads; //Add threads to look in
		$result = $db->runSQL($sql);
		if($db->numRows($result) > 0) {
			$row = $db->fetchArray($result);
			$posts -= $row['posts'];
		}
		if($posts < 0)
			$posts = 0;
		return $posts;
	}
}
?>
