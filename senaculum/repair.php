<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once("classes/dbHandler.php");
$db = new dbHandler;
/*
$sql = "SELECT _'pfx'_threads.threadID, _'pfx'_posts.postID, MAX(_'pfx'_posts.lastEdit) AS lastEdit FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID GROUP BY _'pfx'_threads.threadID";
$result = $db->runSQL($sql);
while($row = $db->fetchArray($result)) {
	$sql = "UPDATE _'pfx'_threads SET lastEdit = '".$row['lastEdit']."', lastPost = '".$row['postID']."' WHERE threadID = '".$row['threadID']."'";
	$db->runSQL($sql);
} 

$sql = "SELECT _'pfx'_forums.forumID, MAX(_'pfx'_posts.lastEdit) AS lastEdit, _'pfx'_posts.postID FROM _'pfx'_forums INNER JOIN _'pfx'_threads ON _'pfx'_forums.forumID = _'pfx'_threads.forumID INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID GROUP BY _'pfx'_forums.forumID";
$result = $db->runSQL($sql);
while($row = $db->fetchArray($result)) {
	$sql = "UPDATE _'pfx'_forums SET lastEdit = '".$row['lastEdit']."', lastPost = '".$row['postID']."' WHERE forumID = '".$row['forumID']."'";
	$db->runSQL($sql);
}
*/
$sql = "SELECT _'pfx'_forums.forumID, count(_'pfx'_posts.postID) AS posts FROM _'pfx'_forums INNER JOIN _'pfx'_threads _'pfx'_forums.forumID = _'pfx'_threads.forumID OR _'pfx'_forums.forumID = _'pfx'_threads.movedFromID INNER JOIN _'pfx'_posts ON  _'pfx'_threads.threadID = _'pfx'_posts.threadID GROUP BY _'pfx'_forums.forumID";
$result = $db->runSQL($sql);
while($row = $db->fetchArray($result)) {
	$sql = "UPDATE _'pfx'_forums SET posts = '".$row['posts']."' WHERE forumID = '".$row['forumID']."'";
	$db->runSQL($sql);
}

$sql = "SELECT _'pfx'_forums.forumID, count(_'pfx'_threads.threadID) AS threads FROM _'pfx'_forums INNER JOIN _'pfx'_threads ON _'pfx'_forums.forumID = _'pfx'_threads.forumID OR _'pfx'_forums.forumID = _'pfx'_threads.movedFromID GROUP BY _'pfx'_forums.forumID";
$result = $db->runSQL($sql);
while($row = $db->fetchArray($result)) {
	$sql = "UPDATE _'pfx'_forums SET threads = '".$row['threads']."' WHERE forumID = '".$row['forumID']."'";
	$db->runSQL($sql);
}

$sql = "SELECT _'pfx'_threads.threadID, count(_'pfx'_posts.postID) AS posts FROM _'pfx'_threads INNER JOIN _'pfx'_posts ON _'pfx'_threads.threadID = _'pfx'_posts.threadID GROUP BY _'pfx'_threads.threadID";
$result = $db->runSQL($sql);
while($row = $db->fetchArray($result)) {
	$sql = "UPDATE _'pfx'_threads SET posts = '".$row['posts']."' WHERE threadID = '".$row['threadID']."'";
	$db->runSQL($sql);
}
?>
Klar
