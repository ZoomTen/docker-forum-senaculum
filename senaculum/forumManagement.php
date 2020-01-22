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
global $lang;

require_once('classes/errorHandler.php');
require_once('classes/menuHandler.php');
require_once('classes/forumHandler.php');
require_once('classes/forumGroupHandler.php');

$menu = new menuHandler;
$forum = new forumHandler;
$forumGroup = new forumGroupHandler;
$error = new errorHandler;

if($forumVariables['adminInlogged']) {
	if(isset($_GET['delete'])) {
		$forum->remove($_GET['delete']);
		header("location: forumManagement.php");
	}
	if(isset($_GET['deleteG'])) {
		$forumGroup->remove($_GET['deleteG']);
		header("location: forumManagement.php");
	}
	if(isset($_GET['moveUpF']) && isset($_GET['gid'])) {
		$forum->moveUp($_GET['moveUpF'],$_GET['gid']);
		header("location: forumManagement.php");
	}
	if(isset($_GET['moveDownF']) && isset($_GET['gid'])) {
		$forum->moveDown($_GET['moveDownF'],$_GET['gid']);
		header("location: forumManagement.php");
	}
	if(isset($_GET['moveUpG'])) {
		$forumGroup->moveUp($_GET['moveUpG']);
		header("location: forumManagement.php");
	}
	if(isset($_GET['moveDownG'])) {
		$forumGroup->moveDown($_GET['moveDownG']);
		header("location: forumManagement.php");
	}
}	
	
$menu->getTop();

$forums = $forum->getAll();
?>
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="generalListHeadingLeft">
			<?php echo $lang['forum']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['edit']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['delete']; ?>:
		</td>
		<td class="generalListHeadingRight">
			<?php echo $lang['move']; ?>:
		</td>
	</tr>
	<?php
	if(!$forumVariables['adminInlogged']) {
	?>
	<tr>
		<td align="center" colspan="4">
			<?php echo $lang['noPermissionBeHere']; ?>
		</td>
	</tr>
</table>
	<?php
		$menu->getBottom("censurManagement");
		die();
	}
?>
	<tr>
		<td class="generalListInfoTextBar" colspan="4">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="generalListInfoText">
						<?php echo $lang['manageForums']; ?>
					</td>
					<td class="generalListInfoTextBarLinks">
						<?php
						if($forumSettings['guidesInPopups']) {
						?>
						<a href="javascript:popup('addForumGroup.php',800,600);" class="actionLink"><?php echo $lang['newForumGroup']; ?></a> |
						<a href="javascript:popup('addForum.php',800,600);" class="actionLink"><?php echo $lang['newForum']; ?></a>
						<?php
						}
						else {
						?>
						<a href="addForumGroup.php" class="actionLink"><?php echo $lang['newForumGroup']; ?></a> |
						<a href="addForum.php"class="actionLink"><?php echo $lang['newForum']; ?></a>
						<?php
						}
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>

<?php	
	if(!empty($forums)) {
		foreach($forums as $group => $forumValue) {
			$i = 0; 
			foreach($forumValue as $currentForumValue) {
				if($i == 0) {
	?>
	<tr>
		<td class="generalListGroupLeft">
			<?php echo $currentForumValue['groupName']; ?>
		</td>
		<td class="generalListGroupMiddle">
			<a href="javascript:<?php if($forumSettings['guidesInPopups']) { ?>popup('editForumGroup.php?id=<?php echo $currentForumValue['groupID']; ?>',800,600);<?php } else { ?>window.location='editForumGroup.php?id=<?php echo $currentForumValue['groupID']; ?>';<?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
		</td>
		<td class="generalListGroupMiddle">
			<a href="javascript:confirmProcess('<?php echo $lang['deleteForumGroupConfirm']; ?>','forumManagement.php?deleteG=<?php echo $currentForumValue['groupID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
		</td>
		<td class="generalListGroupRight">
			<a href="forumManagement.php?moveUpG=<?php echo $currentForumValue['groupID']; ?>" class="actionLink2"><?php echo $lang['moveUp']; ?></a> |
			<a href="forumManagement.php?moveDownG=<?php echo $currentForumValue['groupID']; ?>" class="actionLink2"><?php echo $lang['moveDown']; ?></a>
		</td>
	</tr>
	<?php
				}
				else {
					if($i % 2 == 0) {
	?>
	<tr>
		<td class="generalListItem1Left">
			<?php echo $currentForumValue['forumName'];?>
		</td>
		<td class="generalListItem1Middle">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editForum.php?id=<?php echo $currentForumValue['forumID'];?>',800,600);<?php } else {?>editForum.php?id=<?php echo $currentForumValue['forumID'];?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
		</td>
		<td class="generalListItem1Middle">
			<a href="javascript:confirmProcess('<?php echo $lang['deleteForumConfirm']; ?>','forumManagement.php?delete=<?php echo $currentForumValue['forumID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
		</td>
		<td class="generalListItem1Right">
			<a href="forumManagement.php?moveUpF=<?php echo $currentForumValue['forumID']; ?>&amp;gid=<?php echo $currentForumValue['forumGroupID']; ?>" class="actionLink2"><?php echo $lang['moveUp']; ?></a><br/>
			<a href="forumManagement.php?moveDownF=<?php echo $currentForumValue['forumID']; ?>&amp;gid=<?php echo $currentForumValue['forumGroupID']; ?>" class="actionLink2"><?php echo $lang['moveDown']; ?></a>
		</td>
	</tr>
	<?php 			
					}
					else {
	?>
	<tr>
		<td class="generalListItem2Left">
			<?php echo $currentForumValue['forumName'];?>
		</td>
		<td class="generalListItem2Middle">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editForum.php?id=<?php echo $currentForumValue['forumID'];?>',800,600);<?php } else {?>editForum.php?id=<?php echo $currentForumValue['forumID'];?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
		</td>
		<td class="generalListItem2Middle">
			<a href="javascript:confirmProcess('<?php echo $lang['deleteForumConfirm']; ?>','forumManagement.php?delete=<?php echo $currentForumValue['forumID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
		</td>
		<td class="generalListItem2Right">
			<a href="forumManagement.php?moveUpF=<?php echo $currentForumValue['forumID']; ?>&amp;gid=<?php echo $currentForumValue['forumGroupID']; ?>" class="actionLink2"><?php echo $lang['moveUp']; ?></a><br/>
			<a href="forumManagement.php?moveDownF=<?php echo $currentForumValue['forumID']; ?>&amp;gid=<?php echo $currentForumValue['forumGroupID']; ?>" class="actionLink2"><?php echo $lang['moveDown']; ?></a>
		</td>
	</tr>
	<?php
					}
				}
				$i++;
			}
		}
	}
	else {	
	?>
	<tr>
		<td align="center" colspan="4">
			<?php echo $lang['noForums']; ?>
		</td>
	</tr>	
	<?php
	}
	?>																	
</table>

<?php
$menu->getBottom("index");
?>