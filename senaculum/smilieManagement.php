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
global $lang;
	
require_once('classes/errorHandler.php');
require_once('classes/smilieHandler.php');
require_once('classes/menuHandler.php');

$smilie = new smilieHandler;
$error = new errorHandler;
$menu = new menuHandler;

$smilies = $smilie->getAll();

if($forumVariables['adminInlogged']) {
	if(!empty($_GET['delete'])) {
		$smilie->remove($_GET['delete']);
		header("location: smilieManagement.php");
	}	
}

$menu->getTop();
?> 

<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="generalListHeadingLeft">
			<?php echo $lang['code']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['smilie']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['description']; ?>:
		</td>
		<td class="generalListHeadingRight">
			<?php echo $lang['action']; ?>:
		</td>
	</tr>	
	<tr>
		<td class="generalListInfoTextBar" colspan="4">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="generalListInfoText">
						<?php echo $lang['manageSmilies']; ?>
					</td>
					<?php
					if($forumVariables['adminInlogged']) {
					?>
					<td class="generalListInfoTextBarLinks">
						<?php
						if($forumSettings['guidesInPopups']) {
						?>
						<a href="javascript:popup('addSmilie.php',800,600);" class="actionLink"><?php echo $lang['newSmilie']; ?></a>
						<?php
						}
						else {
						?>
						<a href="addSmilie.php" class="actionLink"><?php echo $lang['newSmilie']; ?></a>
						<?php
						}
						?>
					</td>
					<?php
					}
					?>	
				</tr>
			</table>
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
		$menu->getBottom();
		die();
	}
	if(!$smilies) {
	?>
	<tr>
		<td align="center" colspan="4">
			<?php echo $lang['noSmilies']; ?>
		</td>
	</tr>
</table>
	<?php
		$menu->getBottom();
		die();
	}

	$i = 0;
	foreach($smilies as $currentSmilieValue) {
		if($i % 2 == 0) {
	?>
	<tr>
		<td class="generalListItem1Left">
			<?php echo $currentSmilieValue['find']; ?>
		</td>
		<td class="generalListItem1Middle">
			<img src="images/smilies/<?php echo $currentSmilieValue['fileName']; ?>" alt="<?php echo $lang['smilie']; ?>"/>
		</td>
		<td class="generalListItem1Middle">
			<?php echo $currentSmilieValue['description']; ?>
		</td>
		<td class="generalListItem1Right">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editSmilie.php?id=<?php echo $currentSmilieValue['smilieID'] ?>',800,600);<?php } else { ?>editSmilie.php?id=<?php echo $currentSmilieValue['smilieID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a> | 
			<a href="javascript:confirmProcess('<?php echo $lang['deleteSmilieConfirm']; ?>','smilieManagement.php?delete=<?php echo $currentSmilieValue['smilieID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
		</td>
	</tr>
	<?php
		}
		else {
	?>	
	<tr>
		<td class="generalListItem2Left">
			<?php echo $currentSmilieValue['find']; ?>
		</td>
		<td class="generalListItem2Middle">
			<img src="images/smilies/<?php echo $currentSmilieValue['fileName']; ?>" alt="<?php echo $lang['smilie']; ?>"/>
		</td>
		<td class="generalListItem2Middle">
			<?php echo $currentSmilieValue['description']; ?>
		</td>
		<td class="generalListItem2Right">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editSmilie.php?id=<?php echo $currentSmilieValue['smilieID'] ?>',800,600);<?php } else { ?>editSmilie.php?id=<?php echo $currentSmilieValue['smilieID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a> | 
			<a href="javascript:confirmProcess('<?php echo $lang['deleteSmilieConfirm']; ?>','smilieManagement.php?delete=<?php echo $currentSmilieValue['smilieID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
		</td>
	</tr>
	<?php
		}
		$i++;
	 }
	 ?>		
</table>

<?php
$menu->getBottom();
?> 