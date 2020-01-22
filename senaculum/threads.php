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
require_once('classes/threadHandler.php');
require_once('classes/forumHandler.php');
require_once('classes/errorHandler.php');
require_once('classes/permissionHandler.php');
require_once('classes/settingHandler.php');
require_once('classes/other.php');

$menu = new menuHandler;
$thread = new threadHandler;
$forum = new forumHandler;
$error = new errorHandler;
$permission = new permissionHandler;
$setting = new settingHandler;
$other = new other;

if(empty($_GET['page']))
	$pageNum = 1;
else
	$pageNum = $_GET['page'];		
$limit = $forumSettings['threadsPerPage'];		
$startRow = ($pageNum - 1) * $limit;

if(isset($_GET['id'])) {
	if($_GET['markRead'] && $forumVariables['inlogged'] && $forumSettings['smartNewPosts']) {
		$thread->markAllAsRead($_GET['id']);
	}

	$menu->getTop();
	if(empty($_GET['sort'])) 
		$threads = $thread->getAll($_GET['id'],1,$startRow,$limit,$pageNum);
	else
		$threads = $thread->getAll($_GET['id'],$_GET['sort'],$startRow,$limit,$pageNum);
	$currentForum = $forum->getOne($_GET['id'], false);
	if(empty($currentForum))
		$error->error($lang['forumNotExist1'],$lang['forumNotExist2']);
}
else
	$error->error($lang['wrongURL'],$lang['URLChanged']);

//Get sort
if(empty($_GET['sort']))
	$sort = null;
else
	$sort = $_GET['sort'];		
	
//Paginate
$numAnnounce = $threads[0]['numAnnounce'];
$numRows = $currentForum['threads'] - $numAnnounce;
$limit -= $numAnnounce; 
$getVariables[0]['name'] = "id";
$getVariables[0]['value'] = $_GET['id'];
$getVariables[1]['name'] = "sort";
$getVariables[1]['value'] = $sort;		
$paginate = $other->paginate($pageNum, $numRows, $limit, "threads.php", $getVariables);	
if($numRows == 0)
	$numOfPages = 1;
else	
	$numOfPages = ceil($numRows / $limit);	
	

$permissionRead = $permission->permission($_GET['id'],"read");		
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
					<td class="threadListHeadingLastPost">
						<?php echo $lang['lastPost']; ?>:
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
						<span class="threadListForumHeadingName"><?php echo "<a href=\"index.php\">".$forumSettings['forumName']."</a> &gt; <a href=\"forumGroup.php?id=".$currentForum['groupID']."\">".$currentForum['groupName']."</a> &gt; <a href=\"threads.php?id=".$currentForum['forumID']."\">".$currentForum['name']."</a>"; ?></span>
						<?php
						$currentModerators = ""; 
						if(!empty($currentForum['moderators'])) {
							$i=0;
							foreach($currentForum['moderators'] as $moderatorValues) {
								if(!empty($currentModerators))
									$currentModerators .= ", ";
								$currentModerators .= "<a href=\"profile.php?id=".$moderatorValues['moderatorID']."\">".$moderatorValues['moderatorName']."</a>";
								if($i >= 5) {
									$currentModerators .= "...";
									break;
								}
								$i++;
							}
						}
						else
							$currentModerators = $lang['none']; 
						?> 
						&nbsp;&nbsp;<span class="threadListForumHeadingModerators"><?php echo $lang['moderators']; ?>: <?php echo $currentModerators; ?></span>
					</td>
					<td class="threadListForumHeadingNewThread">
						<?php
						if($forumVariables['inlogged'] && $forumSettings['smartNewPosts']) {
						?>
							<a href="threads.php?id=<?php echo $_GET['id']; ?>&amp;sort=<?php echo $sort; ?>&amp;markRead=1" class="actionLink"><?php echo $lang['markAllThreadsRead']; ?></a> |
						<?php
						}
						?>
						<a href="javascript:<?php if($forumSettings['guidesInPopups']) { ?>popup('addThread.php?id=<?php echo $currentForum['forumID']; ?>',800,600);<?php } else { ?>window.location = 'addThread.php?id=<?php echo $currentForum['forumID']; ?>';<?php } ?>" class="actionLink"><?php echo $lang['newThread']; ?></a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php
if(!$permissionRead) {
?>	
	<tr>
		<td align="center">
			<?php echo $lang['noPermissionReadForum']; ?>
		</td>
	</tr>	
<?php
	$menu->getBottom();
	die();
}
?>
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
	if(!empty($threads)) {
		foreach($threads as $currentThreadValue) {
			if($i % 2 == 0) {
	?>	
				<tr>
					<td class="threadListStatus1">
						<?php
						if($currentThreadValue['newPosts'] == 0) {
							if($currentThreadValue['status'] == 1) {
								if($currentThreadValue['ownPostsInThread']) {
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
							elseif($currentThreadValue['type'] == 0) {
								if($currentThreadValue['ownPostsInThread']) {
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
							else if($currentThreadValue['type'] == 1) {
								if($currentThreadValue['ownPostsInThread']) {
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
							else if($currentThreadValue['type'] == 2) {
								if($currentThreadValue['ownPostsInThread']) {
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
							if($currentThreadValue['status'] == 1) {
								if($currentThreadValue['ownPostsInThread']) {
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
							elseif($currentThreadValue['type'] == 0) {
								if($currentThreadValue['ownPostsInThread']) {
						?>
						<div class="iconStatusNewPostsOwn" title="<?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusNewPosts" title="<?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentThreadValue['type'] == 1) {
								if($currentThreadValue['ownPostsInThread']) {
						?>
						<div class="iconStatusStickyNewPostsOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusStickyNewPosts" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentThreadValue['type'] == 2) {
								if($currentThreadValue['ownPostsInThread']) {
						?>
						<div class="iconStatusAnnouncementNewPostsOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusAnnouncementNewPosts" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}			
						}
						?>
					</td>
					<td class="threadListThread1">
						<a href="posts.php?id=<?php echo $currentThreadValue['threadID']; ?>" title="<?php echo $lang['posts']; ?>:<?php echo $currentThreadValue['countPosts']; ?>" class="bigLink"><?php if($currentThreadValue['movedFromID'] != 0) echo $lang['moved']; echo $currentThreadValue['headline']; ?></a>
						<?php
						if($currentThreadValue['poll'])
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"threadListPoll\">[".$lang['poll']."]</span>";
						if($currentThreadValue['countPosts'] > $forumSettings['postsPerPage']) {
							$getVariables2[0]['name'] = "id";
							$getVariables2[0]['value'] = $currentThreadValue['threadID'];
							echo "\n<br/><span class=\"threadListGoto\">".$lang['gotoPage'].": </span><span class=\"threadListPaginate\">".$other->paginate2($currentThreadValue['countPosts'], $forumSettings['postsPerPage'], "posts.php", $getVariables2)."</span>";
						}
						?>
					</td>
					<td class="threadListReplies1">
						<?php 
						if($currentThreadValue['countPosts'] < 1)
							echo "0";
						else	
							echo $currentThreadValue['countPosts']-1; 
						?>
					</td>
					<td class="threadListOwner1">
						<?php
						if($currentThreadValue['memberID'] != 2) {
						?>
						<a href="profile.php?id=<?php echo $currentThreadValue['memberID']; ?>"><?php echo $currentThreadValue['memberName']; ?></a>
						<?php
						}
						elseif(!empty($currentThreadValue['ownerGuestName']))
							echo $currentThreadValue['ownerGuestName'];
						else
							echo $lang['guest'];	
						?>
					</td>
					<td class="threadListLastPost1">
						<?php 
						if(empty($currentThreadValue['lastPost'])) 
							echo $lang['noPosts']; 
						else {
							echo "<a href=\"posts.php?id=".$currentThreadValue['threadID']."&amp;pid=".$currentThreadValue['lastPostPostID']."#".$currentThreadValue['lastPostPostID']."\">".$other->dateParse($forumVariables['dateFormat'], $currentThreadValue['lastPost'])."</a><br/>\n";
							if($currentThreadValue['lastPostMemberID'] != 2)
								echo "<a href=\"profile?id=".$currentThreadValue['lastPostMemberID']."\">".$currentThreadValue['lastPostUsername']."</a>";
							elseif(!empty($currentThreadValue['lastPostGuestName']))
								echo $currentThreadValue['lastPostGuestName'];
							else
								echo $lang['guest'];	
						}	
						?>
					</td>
				</tr>
	<?php
			}
			else { 
	?>
				<tr>
					<td class="threadListStatus2">
						<?php
						if($currentThreadValue['newPosts'] == 0) {
							if($currentThreadValue['status'] == 1) {
								if($currentThreadValue['ownPostsInThread']) {
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
							elseif($currentThreadValue['type'] == 0) {
								if($currentThreadValue['ownPostsInThread']) {
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
							else if($currentThreadValue['type'] == 1) {
								if($currentThreadValue['ownPostsInThread']) {
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
							else if($currentThreadValue['type'] == 2) {
								if($currentThreadValue['ownPostsInThread']) {
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
							if($currentThreadValue['status'] == 1) {
								if($currentThreadValue['ownPostsInThread']) {
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
							elseif($currentThreadValue['type'] == 0) {
								if($currentThreadValue['ownPostsInThread']) {
						?>
						<div class="iconStatusNewPostsOwn" title="<?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusNewPosts" title="<?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentThreadValue['type'] == 1) {
								if($currentThreadValue['ownPostsInThread']) {
						?>
						<div class="iconStatusStickyNewPostsOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusStickyNewPosts" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}
							else if($currentThreadValue['type'] == 2) {
								if($currentThreadValue['ownPostsInThread']) {
						?>
						<div class="iconStatusAnnouncementNewPostsOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php		
								}
								else {
						?>
						<div class="iconStatusAnnouncementNewPosts" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentThreadValue['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
						<?php
								}
							}			
						}
						?>
					</td>
					<td class="threadListThread2">
						<a href="posts.php?id=<?php echo $currentThreadValue['threadID']; ?>" title="<?php echo $lang['posts']; ?>:<?php echo $currentThreadValue['countPosts']; ?>" class="bigLink"><?php if($currentThreadValue['movedFromID'] != 0) echo $lang['moved']; echo $currentThreadValue['headline']; ?></a>
						<?php
						if($currentThreadValue['poll'])
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"threadListPoll\">[".$lang['poll']."]</span>";
						if($currentThreadValue['countPosts'] > $forumSettings['postsPerPage']) {
							$getVariables2[0]['name'] = "id";
							$getVariables2[0]['value'] = $currentThreadValue['threadID'];
							echo "\n<br/><span class=\"threadListGoto\">".$lang['gotoPage'].": </span><span class=\"threadListPaginate\">".$other->paginate2($currentThreadValue['countPosts'], $forumSettings['postsPerPage'], "posts.php", $getVariables2)."</span>";
						}
						?>
					</td>
					<td class="threadListReplies2">
						<?php 
						if($currentThreadValue['countPosts'] < 1)
							echo "0";
						else	
							echo $currentThreadValue['countPosts']-1; 
						?>
					</td>
					<td class="threadListOwner2">
						<?php
						if($currentThreadValue['memberID'] != 2) {
						?>
						<a href="profile.php?id=<?php echo $currentThreadValue['memberID']; ?>"><?php echo $currentThreadValue['memberName']; ?></a>
						<?php
						}
						elseif(!empty($currentThreadValue['ownerGuestName']))
							echo $currentThreadValue['ownerGuestName'];
						else
							echo $lang['guest'];	
						?>
					</td>
					<td class="threadListLastPost2">
						<?php 
						if(empty($currentThreadValue['lastPost'])) 
							echo $lang['noPosts']; 
						else {
							echo "<a href=\"posts.php?id=".$currentThreadValue['threadID']."&amp;pid=".$currentThreadValue['lastPostPostID']."#".$currentThreadValue['lastPostPostID']."\">".$other->dateParse($forumVariables['dateFormat'], $currentThreadValue['lastPost'])."</a><br/>\n";
							if($currentThreadValue['lastPostMemberID'] != 2)
								echo "<a href=\"profile?id=".$currentThreadValue['lastPostMemberID']."\">".$currentThreadValue['lastPostUsername']."</a>";
							elseif(!empty($currentThreadValue['lastPostGuestName']))
								echo $currentThreadValue['lastPostGuestName'];
							else
								echo $lang['guest'];	
						}	
						?>
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
						<?php echo $lang['noThreads']; ?>
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