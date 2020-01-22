<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if(!empty($_POST['language'])) {
	@include("lang/".basename($_POST['language'])."/lang.php");
}

global $lang;

require_once('dbHandler.php');
$db = new dbHandler;

//Create BBCode table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_BBcode` (
  `BBcodeID` int(3) NOT NULL auto_increment,
  `code` varchar(255) NOT NULL default '',
  `result` varchar(255) NOT NULL default '',
  `display` varchar(255) NOT NULL default 'item',
  `info` varchar(255) default NULL,
  `accesskey` char(1) default NULL,
  `scriptName` varchar(255) default NULL,
  PRIMARY KEY  (`BBcodeID`),
  UNIQUE KEY `code` (`code`)
);";
$db->runSQL($sql);

//Create PM table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_PM` (
  `PMID` int(10) NOT NULL auto_increment,
  `subject` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `date` int(12) NOT NULL default '0',
  `reciver` int(8) NOT NULL default '0',
  `sender` int(8) NOT NULL default '0',
  `read` tinyint(1) NOT NULL default '0',
  `reciverRemoved` tinyint(1) NOT NULL default '0',
  `senderRemoved` tinyint(1) NOT NULL default '0',
  `disableSmilies` tinyint(1) NOT NULL default '0',
  `disableBBCode` tinyint(1) NOT NULL default '0',
  `attachSign` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`PMID`)
) COMMENT='Table of PMs';";
$db->runSQL($sql);

//Create avatar table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_avatars` (
  `avatarID` mediumint(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `fileName` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`avatarID`)
);";
$db->runSQL($sql);

//Create censor table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_cencur` (
  `censurID` int(7) NOT NULL auto_increment,
  `find` varchar(255) NOT NULL default '',
  `replace` varchar(255) NOT NULL default '',
  `byWord` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`censurID`)
) COMMENT='Cencurwords';";
$db->runSQL($sql);

//Create forumGroup table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_forumGroups` (
  `groupID` int(5) NOT NULL auto_increment,
  `groupName` varchar(255) NOT NULL default '',
  `sort` mediumint(5) NOT NULL default '0',
  PRIMARY KEY  (`groupID`)
) COMMENT='A table of the forumgroups';";
$db->runSQL($sql);

//Create the forum table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_forums` (
  `forumID` int(4) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default 'New Forum',
  `lastEdit` int(12) NOT NULL default '0',
  `infoText` text,
  `groupID` int(5) NOT NULL default '0',
  `lastPost` mediumint(8) NOT NULL default '0',
  `posts` mediumint(8) NOT NULL default '0',
  `threads` mediumint(7) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `outloggedView` tinyint(1) NOT NULL default '1',
  `outloggedRead` tinyint(1) NOT NULL default '1',
  `outloggedThread` tinyint(1) NOT NULL default '0',
  `outloggedPost` tinyint(1) NOT NULL default '0',
  `outloggedEdit` tinyint(1) NOT NULL default '0',
  `outloggedDelete` tinyint(1) NOT NULL default '0',
  `outloggedSticky` tinyint(1) NOT NULL default '0',
  `outloggedAnnounce` tinyint(1) NOT NULL default '0',
  `outloggedVote` tinyint(1) NOT NULL default '1',
  `outloggedPoll` tinyint(1) NOT NULL default '0',
  `outloggedAttach` tinyint(1) NOT NULL default '0',
  `inloggedView` tinyint(1) NOT NULL default '1',
  `inloggedRead` tinyint(1) NOT NULL default '1',
  `inloggedThread` tinyint(1) NOT NULL default '1',
  `inloggedPost` tinyint(1) NOT NULL default '1',
  `inloggedEdit` tinyint(1) NOT NULL default '0',
  `inloggedDelete` tinyint(1) NOT NULL default '0',
  `inloggedSticky` tinyint(1) NOT NULL default '0',
  `inloggedAnnounce` tinyint(1) NOT NULL default '0',
  `inloggedVote` tinyint(1) NOT NULL default '1',
  `inloggedPoll` tinyint(1) NOT NULL default '1',
  `inloggedAttach` tinyint(1) NOT NULL default '1',
  `inloggedModerator` tinyint(1) NOT NULL default '0',
  `sort` mediumint(5) NOT NULL default '0',
  PRIMARY KEY  (`forumID`),
  FULLTEXT KEY `name` (`name`)
) COMMENT='Table for the diffrent forums in your forum';";
$db->runSQL($sql);

//Create memberGroupPermissions table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_memberGroupPermissions` (
  `forumID` int(6) NOT NULL default '0',
  `memberGroupID` int(7) NOT NULL default '0',
  `view` tinyint(1) NOT NULL default '0',
  `read` tinyint(1) NOT NULL default '0',
  `thread` tinyint(1) NOT NULL default '0',
  `post` tinyint(1) NOT NULL default '0',
  `edit` tinyint(1) NOT NULL default '0',
  `delete` tinyint(1) NOT NULL default '0',
  `sticky` tinyint(1) NOT NULL default '0',
  `announce` tinyint(1) NOT NULL default '0',
  `vote` tinyint(1) NOT NULL default '0',
  `poll` tinyint(1) NOT NULL default '0',
  `attach` tinyint(1) NOT NULL default '0',
  `moderator` tinyint(1) NOT NULL default '0'
);";
$db->runSQL($sql);

//Create memberGroup table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_memberGroups` (
  `groupID` int(5) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `Description` varchar(255) default NULL,
  `groupModerator` int(8) NOT NULL default '0',
  PRIMARY KEY  (`groupID`)
);";
$db->runSQL($sql);

//Create memberGroups table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_memberGroupsRelation` (
  `groupID` int(5) NOT NULL default '0',
  `memberID` varchar(8) NOT NULL default ''
);";
$db->runSQL($sql);

//Create memberPermissions table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_memberPermissions` (
  `forumID` int(6) NOT NULL default '0',
  `memberID` int(8) NOT NULL default '0',
  `view` tinyint(1) NOT NULL default '0',
  `read` tinyint(1) NOT NULL default '0',
  `thread` tinyint(1) NOT NULL default '0',
  `post` tinyint(1) NOT NULL default '0',
  `edit` tinyint(1) NOT NULL default '0',
  `delete` tinyint(1) NOT NULL default '0',
  `sticky` tinyint(1) NOT NULL default '0',
  `announce` tinyint(1) NOT NULL default '0',
  `vote` tinyint(1) NOT NULL default '0',
  `poll` tinyint(1) NOT NULL default '0',
  `attach` tinyint(1) NOT NULL default '0',
  `moderator` tinyint(1) NOT NULL default '0'
);";
$db->runSQL($sql);

//Create member table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_members` (
  `memberID` int(7) NOT NULL auto_increment,
  `userName` varchar(50) NOT NULL default 'New User',
  `firstName` varchar(50) NOT NULL default 'New',
  `sureName` varchar(50) NOT NULL default 'User',
  `email` varchar(50) NOT NULL default 'user@user.com',
  `showEmail` tinyint(1) NOT NULL default '1',
  `password` varchar(50) NOT NULL default '',
  `admin` tinyint(1) NOT NULL default '0',
  `loginDate1` int(12) NOT NULL default '0',
  `loginDate2` int(12) NOT NULL default '0',
  `lastActive` int(12) NOT NULL default '0',
  `homepage` varchar(255) default NULL,
  `location` varchar(50) default NULL,
  `occupation` varchar(50) default NULL,
  `interests` varchar(50) default NULL,
  `ICQ` varchar(50) default NULL,
  `AIM` varchar(50) default NULL,
  `MSN` varchar(50) default NULL,
  `yahoo` varchar(50) default NULL,
  `signature` varchar(255) default NULL,
  `avatar` varchar(255) default NULL,
  `lang` varchar(50) default NULL,
  `dateFormat` varchar(20) NOT NULL default 'Y-m-d H:i',
  `activated` tinyint(1) NOT NULL default '1',
  `actKey` varchar(50) default NULL,
  `registerDate` int(12) NOT NULL default '0',
  `alwaysAllowBBCode` tinyint(1) default '1',
  `alwaysAllowSmilies` tinyint(1) default '1',
  `alwaysNotifyOnReply` tinyint(1) default '0',
  `notifyNewPM` tinyint(1) default '0',
  `alwaysDisplaySign` tinyint(1) default '1',
  PRIMARY KEY  (`memberID`),
  UNIQUE KEY `userName` (`userName`,`email`)
) PACK_KEYS=0 COMMENT='Table for the users in your forum';";
$db->runSQL($sql);

//Create posts table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_posts` (
  `postID` int(8) NOT NULL auto_increment,
  `editedBy` int(7) NOT NULL default '0',
  `lastEdit` int(12) NOT NULL default '0',
  `headline` varchar(50) default NULL,
  `text` text NOT NULL,
  `threadID` int(7) NOT NULL default '0',
  `madeBy` int(7) NOT NULL default '0',
  `date` int(12) NOT NULL default '0',
  `guestName` varchar(20) default NULL,
  `deletedUser` tinyint(1) default '0',
  `disableBBCode` tinyint(1) default '0',
  `disableSmilies` tinyint(1) default '0',
  `notifyWhenReply` tinyint(1) default '0',
  `attachSign` tinyint(1) default '1',
  PRIMARY KEY  (`postID`),
  FULLTEXT KEY `IdxText` (`headline`,`text`)
) COMMENT='Table for the posts in your threads';";
$db->runSQL($sql);

//Create settings table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_settings` (
  `settingName` varchar(50) NOT NULL default '',
  `settingValue` varchar(255) default NULL
) COMMENT='Settings for the forum';";
$db->runSQL($sql);

//Create smilies table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_smilies` (
  `smilieID` mediumint(5) NOT NULL auto_increment,
  `find` varchar(255) NOT NULL default '',
  `fileName` varchar(255) NOT NULL default '',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`smilieID`)
) COMMENT='Smilies';";
$db->runSQL($sql);

//Create thread table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_threads` (
  `threadID` int(6) NOT NULL auto_increment,
  `headline` varchar(50) NOT NULL default 'New Thread',
  `date` int(12) NOT NULL default '0',
  `lastEdit` int(12) NOT NULL default '0',
  `memberID` int(7) NOT NULL default '0',
  `forumID` int(4) NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `lastPost` mediumint(8) NOT NULL default '0',
  `posts` mediumint(8) NOT NULL default '0',
  `poll`tinyint(1) NOT NULL default '0',
  `movedFromID`int(6) NOT NULL default '0',
  `ownerGuestName` varchar(20) default NULL,
  PRIMARY KEY  (`threadID`),
  FULLTEXT KEY `headline` (`headline`)
) COMMENT='Table for the threads in your forums';";
$db->runSQL($sql);

//Create poll options table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_pollOptions` (
  `optionID` int(8) NOT NULL auto_increment,
  `pollID` int(6) NOT NULL default '0',
  `threadID` int(6) NOT NULL default '0',
  `option` varchar(255) NOT NULL default '',
  `votes` int(8) NOT NULL default '0',
  PRIMARY KEY  (`optionID`)
);";
$db->runSQL($sql);

//Create poll vote table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_pollVotes` (
  `optionID` int(8) NOT NULL default '0',
  `memberID` int(6) NOT NULL default '0',
  `userIP` varchar(20) NOT NULL default '',
  `pollID` int(6) NOT NULL default '0',
  `threadID` int(6) NOT NULL default '0'
);";
$db->runSQL($sql);

//Create poll table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_polls` (
  `pollID` int(6) NOT NULL auto_increment,
  `threadID` int(6) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `startDate` int(12) NOT NULL default '0',
  `endDate` int(12) NOT NULL default '0',
  PRIMARY KEY  (`pollID`)
);";
$db->runSQL($sql);

//Create viewed posts table
$sql = "CREATE TABLE IF NOT EXISTS `_'pfx'_viewedPosts` (
  `memberID` int(7) NOT NULL default '0',
  `postID` int(9) NOT NULL default '0',
  `date` int(12) NOT NULL default '0',
  UNIQUE KEY `memberID` (`memberID`,`postID`)
);";
$db->runSQL($sql);

$sql = "CREATE TABLE `_'pfx'_bookmarks` (
  `bookmarkID` int(7) NOT NULL auto_increment,
  `memberID` int(7) NOT NULL default '0',
  `threadID` int(9) NOT NULL default '0',
  PRIMARY KEY  (`bookmarkID`),
  UNIQUE KEY `memberID` (`memberID`,`threadID`)
) COMMENT='Store bookmarks';";
$db->runSQL($sql);

//Insert the forum settings
$sql = "INSERT INTO _'pfx'_settings (settingName, settingValue) VALUES ('forumName','".$db->SQLsecure($_POST['forumName'])."')";
$db->runSQL($sql);
$sql = "INSERT INTO _'pfx'_settings (settingName, settingValue) VALUES ('forumSlogan','".$db->SQLsecure($_POST['forumSlogan'])."')";
$db->runSQL($sql);
$sql = "INSERT INTO _'pfx'_settings (settingName, settingValue) VALUES ('guidesInPopups','1')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('threadsPerPage', '50')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('postsPerPage', '15')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('membersPerPage', '50')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('lang', '".$db->SQLsecure($_POST['language'])."')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('dateFormat', 'Y-m-d H:i')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('numPolls', '10')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('adminEmail', '')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('smtpHost', '')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('smtpUsername', '')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('smtpPassword', '')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('useSmtp', '0')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('validateEmail', '0')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('emailActivated', '0')";
$db->runSQL($sql);

$forumDomainName = $_POST['forumDomainName'];
if(substr($forumDomainName,-1) == "/")
	$forumDomainName = substr($forumDomainName,0,-1);
if(substr($forumDomainName,0,1) == "/")
	$forumDomainName = substr($forumDomainName,1);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('forumDomainName', '".$db->SQLsecure($forumDomainName)."')";
$db->runSQL($sql);

$forumScriptPath = $_POST['forumScriptPath'];
if(substr($forumScriptPath,-1) != "/")
	$forumScriptPath = $forumScriptPath."/";
if(substr($forumScriptPath,0,1) != "/")
	$forumScriptPath = "/".$forumScriptPath;
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('forumScriptPath', '".$db->SQLsecure($forumScriptPath)."')";
$db->runSQL($sql);

$sql = "INSERT INTO `_'pfx'_settings` VALUES ('attachmentsActivated', '0')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('maxAttachmentUploadSize', '1000')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('maxNumberOfAttachments', '3')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('checkAllowedDisallowedAttachmentExtensions', '0')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('allowedDisallowedAttachmentExtensions', 'php,php4')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('disallowedAttachmentExtensionsAdd', '1')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('disallowedAttachmentExtensionsAddThis', 'removeThis');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('smartNewPosts', '1')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('markThreadsWithOwnPosts', '1')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('memberUploadAvatars', '1')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('memberUploadPublicAvatars', '0')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('postTimeLimit', '0')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('allowDeleteUser', '0')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('viewPostRepliesCount', '1')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('activateOnline', '1')";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_settings` VALUES ('onlineViewExpire', '600')";
$db->runSQL($sql);

//Insert a group
$sql = "INSERT INTO _'pfx'_forumGroups (groupID, groupName) VALUES ('1','".$db->SQLsecure($_POST['forumName'])."')";
$db->runSQL($sql);

//Insert a forum
$sql = "INSERT INTO _'pfx'_forums (name,infoText,lastEdit,groupID) VALUES('".$db->SQLsecure($lang['yourFirstForum1'])."','".$db->SQLsecure($lang['yourFirstForum2'])."','".time()."','1')";
$db->runSQL($sql);

//Insert the admin user
$sql = "INSERT INTO _'pfx'_members (userName,firstName,sureName,email,password,admin,lang,registerDate) VALUES ('".$db->SQLsecure($_POST['adminUsername'])."','".$db->SQLsecure($_POST['adminFirstName'])."','".$db->SQLsecure($_POST['adminSureName'])."','".$db->SQLsecure($_POST['adminEmail'])."','".$db->SQLsecure(crypt(md5($_POST['adminPassword']),md5($_POST['adminPassword'])))."','1','".$db->SQLsecure($_POST['language'])."','".time()."')";
$db->runSQL($sql);

//Insert the guest user
$sql = "INSERT INTO _'pfx'_members (userName,email,password,admin) VALUES ('".$db->SQLsecure($lang['guest'])."','none@none.com','XXX','0')";
$db->runSQL($sql);

//Insert default BBCode
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (1, '[b]§[/b]', '<b>§</b>', '".$db->SQLsecure($lang['B'])."', '".$db->SQLsecure($lang['bold'])."', 'b','');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (2, '[i]§[/i]', '<i>§</i>', '".$db->SQLsecure($lang['I'])."', '".$db->SQLsecure($lang['italic'])."', 'i','');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (3, '[u]§[/u]', '<u>§</u>', '".$db->SQLsecure($lang['U'])."', '".$db->SQLsecure($lang['underlined'])."', 'u','');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (4, '[url=§]§[/url]', '<a href=\"§\" class=\"link\">§</a>', '".$db->SQLsecure($lang['URL'])."', '".$db->SQLsecure($lang['link'])."', 'w','');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (5, '[size=§]§[/size]', '<span style=\"font-size:§pt\">§</span>', '".$db->SQLsecure($lang['size'])."', '".$db->SQLsecure($lang['fontSize'])."', NULL,'');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (6, '[li]§[/li]', '<li>§</li>', '".$db->SQLsecure($lang['list'])."', '".$db->SQLsecure($lang['listDesc'])."', 'l','');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (7, '[ul]§[/ul]', '<ul>§</ul>', '".$db->SQLsecure($lang['UL'])."', '".$db->SQLsecure($lang['undefinedList'])."', NULL,'');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (8, '[ol]§[/ol]', '<ol>§</ol>', '".$db->SQLsecure($lang['OL'])."', '".$db->SQLsecure($lang['orderedList'])."', NULL,'');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (9, '[color=§]§[/color]', '<span style=\"color: §;\">§</span>', '".$db->SQLsecure($lang['color'])."', '".$db->SQLsecure($lang['fontColor'])."', 'c','');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_BBcode` VALUES (10, '[quote=§]§[/quote]', '".$db->SQLsecure($lang['script'])."', '".$db->SQLsecure($lang['quote'])."', '".$db->SQLsecure($lang['quote'])."', 'q','quote.php');";
$db->runSQL($sql);

//Insert default smilies
$sql = "INSERT INTO `_'pfx'_smilies` VALUES (1, ':)', 'smile.gif', '".$db->SQLsecure($lang['smile'])."');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_smilies` VALUES (2, ':(', 'sad.gif', '".$db->SQLsecure($lang['sad'])."');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_smilies` VALUES (3, ':S', 'confused.gif', '".$db->SQLsecure($lang['confused'])."');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_smilies` VALUES (4, '8)', 'cool.gif', '".$db->SQLsecure($lang['cool'])."');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_smilies` VALUES (5, ':''(', 'cry.gif', '".$db->SQLsecure($lang['cry'])."');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_smilies` VALUES (6, ':|', 'neutral.gif', '".$db->SQLsecure($lang['neutral'])."');";
$db->runSQL($sql);
$sql = "INSERT INTO `_'pfx'_smilies` VALUES (7, ';)', 'wink.gif', '".$db->SQLsecure($lang['wink'])."');";
$db->runSQL($sql);

if(!empty($_GET['language'])) {
	if(@include("lang/".basename($_GET['language'])."/lang.php"))
		$language = basename($_GET['language']);
	else
		$language = "default";
}
else
	$language = "default";
?>
