<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once('include/top.php'); //Put importent information into variables

require_once('classes/menuHandler.php');
require_once('classes/forumHandler.php');
require_once('classes/other.php');

$menu = new menuHandler;
$forum = new forumHandler;
$setting = new settingHandler;
$other = new other;

$menu->getTop();

if(isset($_GET['delete']) && $forumVariables['adminInlogged']) 
	$forum->remove($_GET['delete']);

$forums = $forum->getAll();
?>
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="forumListHeadingForum" colspan="2">
			<?php echo $lang['forum']; ?>:
		</td>
		<td class="forumListHeadingThreads">
			<?php echo $lang['threads']; ?>:
		</td>
		<td class="forumListHeadingPosts">
			<?php echo $lang['posts']; ?>:
		</td>
		<td class="forumListHeadingLastPost">
			<?php echo $lang['lastPost']; ?>:
		</td>
	</tr>
	<?php
	if(!empty($forums)) {
		foreach($forums as $group => $forumValue) {
			if(count($forumValue) > 1) {
				$i = 0; 
				foreach($forumValue as $currentForumValue) {
					if($i == 0) {
	?>
	<tr>
		<td class="forumListGroupHeading" colspan="5">
			<a href="index.php"><?php echo $forumSettings['forumName']; ?></a> &gt; <a href="forumGroup.php?id=<?php echo $currentForumValue['groupID']; ?>"><?php echo $currentForumValue['groupName']; ?></a>
		</td>
	</tr>
	<?php
					}
					else {
						if($i % 2 == 0) {
	?>
	
	<tr>
		<td class="forumListStatus1">
			<?php
			if($currentForumValue['forumNewPosts'] == 0) {
				if($currentForumValue['forumLocked']) {
			?>
			<div class="iconStatusLocked" title="<?php echo $lang['locked']." - ".$lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
				}
				else {
			?>	
			<div class="iconStatus" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
				}
			}
			else {
				if($currentForumValue['forumLocked']) {
			?>
			<div class="iconStatusLockedNewPosts" title="<?php echo $lang['locked']." - ".$lang['newPosts1'].$currentForumValue['forumNewPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
				}
				else {
			?>
			<div class="iconStatusNewPosts" title="<?php echo $lang['newPosts1'].$currentForumValue['forumNewPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
				}
			}
			?>	
		</td>
		<td class="forumListForum1" valign="top">
			<a href="threads.php?id=<?php echo $currentForumValue['forumID']; ?>" title="<?php echo $lang['threads']; ?>:<?php echo $currentForumValue['forumCountThreads']; ?> <?php echo $lang['posts']; ?>:<?php echo $currentForumValue['forumCountPosts']; ?>" class="bigLink"><?php echo $currentForumValue['forumName']; ?></a><br/>
			<?php echo $currentForumValue['forumInfoText']; ?>
		</td>
		<td class="forumListThreads1">
			<?php echo $currentForumValue['forumCountThreads']?>
		</td>
		<td class="forumListPosts1">
			<?php echo $currentForumValue['forumCountPosts']?>
		</td>
		<td class="forumListLastPost1">
			<?php 
			if(empty($currentForumValue['forumLastPost'])) 
				echo $lang['noPosts'];
			else {
				echo "<a href=\"posts.php?id=".$currentForumValue['forumLastPostThreadID']."&amp;pid=".$currentForumValue['forumLastPostID']."#".$currentForumValue['forumLastPostID']."\">".$other->dateParse($forumVariables['dateFormat'], $currentForumValue['forumLastPost'])."</a><br/>\n";
				if($currentForumValue['forumLastPostMemberID'] != 2)
					echo "<a href=\"profile.php?id=".$currentForumValue['forumLastPostMemberID']."\">".$currentForumValue['forumLastPostUsername']."</a>";
				elseif(!empty($currentForumValue['forumLastPostGuestName']))
					echo $currentForumValue['forumLastPostGuestName'];
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
		<td class="forumListStatus2">
			<?php
			if($currentForumValue['forumNewPosts'] == 0) {
				if($currentForumValue['forumLocked']) {
			?>
			<div class="iconStatusLocked" title="<?php echo $lang['locked']." - ".$lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
				}
				else {
			?>	
			<div class="iconStatus" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
				}
			}
			else {
				if($currentForumValue['forumLocked']) {
			?>
			<div class="iconStatusLockedNewPosts" title="<?php echo $lang['locked']." - ".$lang['newPosts1'].$currentForumValue['forumNewPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
				}
				else {
			?>
			<div class="iconStatusNewPosts" title="<?php echo $lang['newPosts1'].$currentForumValue['forumNewPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
				}
			}
			?>	
		</td>
		<td class="forumListForum2" valign="top">
			<a href="threads.php?id=<?php echo $currentForumValue['forumID']; ?>" title="<?php echo $lang['threads']; ?>:<?php echo $currentForumValue['forumCountThreads']; ?> <?php echo $lang['posts']; ?>:<?php echo $currentForumValue['forumCountPosts']; ?>" class="bigLink"><?php echo $currentForumValue['forumName']; ?></a><br/>
			<?php echo $currentForumValue['forumInfoText']; ?>
		</td>
		<td class="forumListThreads2">
			<?php echo $currentForumValue['forumCountThreads']?>
		</td>
		<td class="forumListPosts2">
			<?php echo $currentForumValue['forumCountPosts']?>
		</td>
		<td class="forumListLastPost2">
			<?php 
			if(empty($currentForumValue['forumLastPost'])) 
				echo $lang['noPosts'];
			else {
				echo "<a href=\"posts.php?id=".$currentForumValue['forumLastPostThreadID']."&amp;pid=".$currentForumValue['forumLastPostID']."#".$currentForumValue['forumLastPostID']."\">".$other->dateParse($forumVariables['dateFormat'], $currentForumValue['forumLastPost'])."</a><br/>\n";
				if($currentForumValue['forumLastPostMemberID'] != 2)
					echo "<a href=\"profile.php?id=".$currentForumValue['forumLastPostMemberID']."\">".$currentForumValue['forumLastPostUsername']."</a>";
				elseif(!empty($currentForumValue['forumLastPostGuestName']))
					echo $currentForumValue['forumLastPostGuestName'];
				else
					echo $lang['guest'];
			}	
			?>
		</td>
	</tr>
	<?php
						}
					}
					$i++;
				}	
			}
		}
	}
	else {	
	?>
	<tr>
		<td align="center" colspan="3">
			<?php echo $lang['noForums']; ?>
		</td>
	</tr>	
	<?php
	}
	?>																	
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
				</table>
			</td>
		</tr>
	</table>
</div>
<?php
$menu->getBottom();
//echo $sqls."<br/><br/>";
//echo $masterCount;
?>