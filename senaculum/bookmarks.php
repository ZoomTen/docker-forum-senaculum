<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once("./include/top.php");
global $forumVariables;
global $forumSettings;

require_once('classes/menuHandler.php');
require_once('classes/bookmarkHandler.php');
require_once('classes/forumHandler.php');
require_once('classes/errorHandler.php');
require_once('classes/permissionHandler.php');
require_once('classes/settingHandler.php');
require_once('classes/other.php');

$menu = new menuHandler;
$bookmark = new bookmarkHandler;
$forum = new forumHandler;
$error = new errorHandler;
$permission = new permissionHandler;
$setting = new settingHandler;
$other = new other;

if(!empty($_GET['delete'])) {
	$bookmark->remove($_GET['delete']);
	global $alert;
	$alert = $lang['bookmarkDeleted'];
}

if(empty($_GET['page']))
	$pageNum = 1;
else
	$pageNum = $_GET['page'];		
$limit = $forumSettings['threadsPerPage'];		
$startRow = ($pageNum - 1) * $limit;

if($forumVariables['inlogged']) {
	$menu->getTop();
	if(empty($_GET['sort'])) 
		$bookmarks = $bookmark->getAll(1,$startRow,$limit,$pageNum);
	else
		$bookmarks = $bookmark->getAll($_GET['sort'],$startRow,$limit,$pageNum);
}
else {
	header("location: index.php?alert=".$lang['notLoggedInPleaseLogin']);
	die();
}

//Get sort
if(empty($_GET['sort']))
	$sort = null;
else
	$sort = $_GET['sort'];
	
//Paginate
$numAnnounce = $bookmarks[0]['numAnnounce'];
$numRows = $bookmark->countBookmarks() - $numAnnounce;
$limit -= $numAnnounce; 
$getVariables[0]['name'] = "sort";
$getVariables[0]['value'] = $sort;		
$paginate = $other->paginate($pageNum, $numRows, $limit, "bookmarks.php", $getVariables);	
if($numRows == 0)
	$numOfPages = 1;
else	
	$numOfPages = ceil($numRows / $limit);	

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<table width="100%" cellpadding="2" cellspacing="0">
				<tr>
					<td class="threadListHeadingThread">
						<?php echo $lang['thread']; ?>:
					</td>
					<td class="threadListHeadingReplies">
						<?php echo $lang['replies']; ?>:
					</td>	
					<td class="threadListHeadingOwner">
						<?php echo $lang['owner']; ?>:
					</td>			
					<td class="bookmarkListHeadingLastPost">
						<?php echo $lang['lastPost']; ?>:
					</td>
					<td class="bookmarkListHeadingDelete">
						<?php echo $lang['delete?']; ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>	
	<tr>
		<td class="threadListForumHeading">
			<table width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td align="left" valign="middle">
						<span class="threadListForumHeadingName"><?php echo "<a href=\"index.php\">".$forumSettings['forumName']."</a> &gt; <a href=\"bookmarks.php\">".$lang['bookmarks']."</a></span>"; ?>
					</td>
					<td class="threadListForumHeadingNewThread">
						&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="pageViewAreaTop">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="pageView">
						<?php echo $lang['pageOf1']." ".$pageNum." ".$lang['pageOf2']." ".$numOfPages ?>
					</td>
					<td class="pageSort">
						<?php echo $lang['sort']; ?>:
						<select name="sort" class="pageDropDown" onchange="window.location = 'threads.php?id=<?php echo $_GET['id']; ?>&amp;sort='+this.options[this.selectedIndex].value">
							<option value="1" class="pageDropDownOption1" <?php if($sort == 1) echo "selected"; ?>><?php echo $lang['lastPost']; ?></option>
							<option value="2" class="pageDropDownOption2" <?php if($sort == 2) echo "selected"; ?>><?php echo $lang['oldestPost']; ?></option>
							<option value="3" class="pageDropDownOption1" <?php if($sort == 3) echo "selected"; ?>><?php echo $lang['newestThread']; ?></option>
							<option value="4" class="pageDropDownOption2" <?php if($sort == 4) echo "selected"; ?>><?php echo $lang['oldestThread']; ?></option>
							<option value="5" class="pageDropDownOption1" <?php if($sort == 5) echo "selected"; ?>><?php echo $lang['ABC']; ?></option>
						</select>
					</td>
					<td class="pageGoto">
						<?php 
						if(!empty($paginate))
							echo "<b>".$lang['gotoPage'].":</b> ".$paginate;
						else
							echo "&nbsp;";	 
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<?php
	$i=0;
	if(!empty($bookmarks)) {
		foreach($bookmarks as $currentBookmarkValue) {
			if($i % 2 == 0) {
	?>	
				<tr>
					<td class="threadListStatus1">
						<?php
						if($currentBookmarkValue['newPosts'] == 0) {
							if($$currentBookmarkValue['status'] == 1) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusLockedOwn" title="<?php echo $lang['locked']; ?>  - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusLocked" title="<?php echo $lang['locked']; ?>  - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php	
								}
							}
							elseif($currentBookmarkValue['type'] == 0) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusOwn" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatus" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentBookmarkValue['type'] == 1) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusStickyOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php
								}
								else {
						?>		
						<div class="iconStatusSticky" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php	
								}
							}
							else if($currentBookmarkValue['type'] == 2) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusAnnouncementOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusAnnouncement" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php
								}
							}	
						}
						else {
							if($currentBookmarkValue['status'] == 1) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusLockedNewPostsOwn" title="<?php echo $lang['locked']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusLockedNewPosts" title="<?php echo $lang['locked']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							elseif($currentBookmarkValue['type'] == 0) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusNewPostsOwn" title="<?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusNewPosts" title="<?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentBookmarkValue['type'] == 1) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusStickyNewPostsOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusStickyNewPosts" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentBookmarkValue['type'] == 2) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusAnnouncementNewPostsOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusAnnouncementNewPosts" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}			
						}
						?>
					</td>
					<td class="threadListThread1">
						<a href="posts.php?id=<?php echo $currentBookmarkValue['threadID']; ?>" title="<?php echo $lang['posts']; ?>:<?php echo $currentBookmarkValue['countPosts']; ?>" class="bigLink"><?php if($currentBookmarkValue['movedFromID'] != 0) echo $lang['moved']; echo $currentBookmarkValue['headline']; ?></a>
						<?php
						if($currentBookmarkValue['poll'])
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"threadListPoll\">[".$lang['poll']."]</span>";
						if($currentBookmarkValue['countPosts'] > $forumSettings['postsPerPage']) {
							$getVariables2[0]['name'] = "id";
							$getVariables2[0]['value'] = $currentBookmarkValue['threadID'];
							echo "\n<br/><span class=\"threadListGoto\">".$lang['gotoPage'].": </span><span class=\"threadListPaginate\">".$other->paginate2($currentBookmarkValue['countPosts'], $forumSettings['postsPerPage'], "posts.php", $getVariables2)."</span>";
						}
						?>
					</td>
					<td class="threadListReplies1">
						<?php 
						if($currentBookmarkValue['countPosts'] < 1)
							echo "0";
						else	
							echo $currentBookmarkValue['countPosts']-1; 
						?>
					</td>
					<td class="threadListOwner1">
						<?php
						if($currentBookmarkValue['memberID'] != 2) {
						?>
						<a href="profile.php?id=<?php echo $currentBookmarkValue['memberID']; ?>"><?php echo $currentBookmarkValue['memberName']; ?></a>
						<?php
						}
						elseif(!empty($currentBookmarkValue['ownerGuestName']))
							echo $currentBookmarkValue['ownerGuestName'];
						else
							echo $lang['guest'];	
						?>
					</td>
					<td class="threadListLastPost1">
						<?php 
						if(empty($currentBookmarkValue['lastPost'])) 
							echo $lang['noPosts']; 
						else {
							echo "<a href=\"posts.php?id=".$currentBookmarkValue['threadID']."&amp;pid=".$currentBookmarkValue['lastPostPostID']."#".$currentBookmarkValue['lastPostPostID']."\">".$other->dateParse($forumVariables['dateFormat'], $currentBookmarkValue['lastPost'])."</a><br/>\n";
							if($currentBookmarkValue['lastPostMemberID'] != 2)
								echo "<a href=\"profile?id=".$currentBookmarkValue['lastPostMemberID']."\">".$currentBookmarkValue['lastPostUsername']."</a>";
							elseif(!empty($currentBookmarkValue['lastPostGuestName']))
								echo $currentBookmarkValue['lastPostGuestName'];
							else
								echo $lang['guest'];	
						}	
						?>
					</td>
					<td class="bookmarkListDelete1">
						<a href="bookmarks.php?delete=<?php echo $currentBookmarkValue['bookmarkID']; ?>" class="actionLink2"><?php echo $lang['delete']; ?></a>
					</td>
				</tr>
	<?php
			}
			else { 
	?>
				<tr>
					<td class="threadListStatus2">
						<?php
						if($currentBookmarkValue['newPosts'] == 0) {
							if($currentBookmarkValue['status'] == 1) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusLockedOwn" title="<?php echo $lang['locked']; ?>  - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusLocked" title="<?php echo $lang['locked']; ?>  - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php	
								}
							}
							elseif($currentBookmarkValue['type'] == 0) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusOwn" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatus" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentBookmarkValue['type'] == 1) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusStickyOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php
								}
								else {
						?>		
						<div class="iconStatusSticky" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php	
								}
							}
							else if($currentBookmarkValue['type'] == 2) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusAnnouncementOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusAnnouncement" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
						<?php
								}
							}	
						}
						else {
							if($currentBookmarkValue['status'] == 1) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusLockedNewPostsOwn" title="<?php echo $lang['locked']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusLockedNewPosts" title="<?php echo $lang['locked']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							elseif($currentBookmarkValue['type'] == 0) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusNewPostsOwn" title="<?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusNewPosts" title="<?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentBookmarkValue['type'] == 1) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusStickyNewPostsOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusStickyNewPosts" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentBookmarkValue['type'] == 2) {
								if($currentBookmarkValue['ownPostsInThread']) {
						?>
						<div class="iconStatusAnnouncementNewPostsOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusAnnouncementNewPosts" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentBookmarkValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}			
						}
						?>
					</td>
					<td class="threadListThread2">
						<a href="posts.php?id=<?php echo $currentBookmarkValue['threadID']; ?>" title="<?php echo $lang['posts']; ?>:<?php echo $currentBookmarkValue['countPosts']; ?>" class="bigLink"><?php if($currentBookmarkValue['movedFromID'] != 0) echo $lang['moved']; echo $currentBookmarkValue['headline']; ?></a>
						<?php
						if($currentBookmarkValue['poll'])
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"threadListPoll\">[".$lang['poll']."]</span>";
						if($currentBookmarkValue['countPosts'] > $forumSettings['postsPerPage']) {
							$getVariables2[0]['name'] = "id";
							$getVariables2[0]['value'] = $currentBookmarkValue['threadID'];
							echo "\n<br/><span class=\"threadListGoto\">".$lang['gotoPage'].": </span><span class=\"threadListPaginate\">".$other->paginate2($currentBookmarkValue['countPosts'], $forumSettings['postsPerPage'], "posts.php", $getVariables2)."</span>";
						}
						?>
					</td>
					<td class="threadListReplies2">
						<?php 
						if($currentBookmarkValue['countPosts'] < 1)
							echo "0";
						else	
							echo $currentBookmarkValue['countPosts']-1; 
						?>
					</td>
					<td class="threadListOwner2">
						<?php
						if($currentBookmarkValue['memberID'] != 2) {
						?>
						<a href="profile.php?id=<?php echo $currentBookmarkValue['memberID']; ?>"><?php echo $currentBookmarkValue['memberName']; ?></a>
						<?php
						}
						elseif(!empty($currentBookmarkValue['ownerGuestName']))
							echo $currentBookmarkValue['ownerGuestName'];
						else
							echo $lang['guest'];	
						?>
					</td>
					<td class="threadListLastPost2">
						<?php 
						if(empty($currentBookmarkValue['lastPost'])) 
							echo $lang['noPosts']; 
						else {
							echo "<a href=\"posts.php?id=".$currentBookmarkValue['threadID']."&amp;pid=".$currentBookmarkValue['lastPostPostID']."#".$currentBookmarkValue['lastPostPostID']."\">".$other->dateParse($forumVariables['dateFormat'], $currentBookmarkValue['lastPost'])."</a><br/>\n";
							if($currentBookmarkValue['lastPostMemberID'] != 2)
								echo "<a href=\"profile?id=".$currentBookmarkValue['lastPostMemberID']."\">".$currentBookmarkValue['lastPostUsername']."</a>";
							elseif(!empty($currentBookmarkValue['lastPostGuestName']))
								echo $currentBookmarkValue['lastPostGuestName'];
							else
								echo $lang['guest'];	
						}	
						?>
					</td>
					<td class="bookmarkListDelete2">
						<a href="bookmarks.php?delete=<?php echo $currentBookmarkValue['bookmarkID']; ?>" class="actionLink2"><?php echo $lang['delete']; ?></a>
					</td>
				</tr>
	<?php
			}
			$i++;
		}
	}
	else {
	?>		
				<tr>
					<td align="center" colspan="5">
						<?php echo $lang['noBookmarks']; ?>
					</td>
				</tr>		
	<?php
	}
	?>
			</table>	
		</td>
	</tr>
	<tr>
		<td class="pageViewAreaBottom">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="pageView">
						<?php echo $lang['pageOf1']." ".$pageNum." ".$lang['pageOf2']." ".$numOfPages ?>
					</td>
					<td class="pageSort">
						<?php echo $lang['sort']; ?>:
						<select name="sort" class="pageDropDown" onchange="window.location = 'threads.php?id=<?php echo $_GET['id']; ?>&amp;sort='+this.options[this.selectedIndex].value">
							<option value="1" class="pageDropDownOption1" <?php if($sort == 1) echo "selected"; ?>><?php echo $lang['lastPost']; ?></option>
							<option value="2" class="pageDropDownOption2" <?php if($sort == 2) echo "selected"; ?>><?php echo $lang['oldestPost']; ?></option>
							<option value="3" class="pageDropDownOption1" <?php if($sort == 3) echo "selected"; ?>><?php echo $lang['newestThread']; ?></option>
							<option value="4" class="pageDropDownOption2" <?php if($sort == 4) echo "selected"; ?>><?php echo $lang['oldestThread']; ?></option>
							<option value="5" class="pageDropDownOption1" <?php if($sort == 5) echo "selected"; ?>><?php echo $lang['ABC']; ?></option>
						</select>
					</td>
					<td class="pageGoto">
						<?php 
						if(!empty($paginate))
							echo "<b>".$lang['gotoPage'].":</b> ".$paginate;
						else
							echo "&nbsp;";	 
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>										
</table>
<br/>
<div align="center">
	<table cellpadding="0" cellspacing="0" style="width:50%;">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatus">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['noNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusNewPosts">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['newPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusAnnouncement">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['announcementNoNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusAnnouncementNewPosts">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['announcementNewPosts']; ?>
						</td>
					</tr>
				</table>
			</td>
			<td align="right">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusLocked">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['lockedNoNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusLockedNewPosts">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['lockedNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusSticky">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['stickyNoNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusStickyNewPosts">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['stickyNewPosts']; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<?php
$menu->getBottom();
//echo $masterCount;
?>  