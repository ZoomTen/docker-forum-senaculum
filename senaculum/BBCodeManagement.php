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
require_once('classes/BBCodeHandler.php');
require_once('classes/menuHandler.php');
require_once('classes/logInOutHandler.php');

$BBCode = new BBCodeHandler;
$error = new errorHandler;
$menu = new menuHandler;
$auth = new logInOutHandler;

if(!$forumVariables['adminInlogged'])
{
	$error->error($lang['notLoggedInAdmin1'], $lang['notLoggedInAdmin2']);
}

$BBCodes = $BBCode->getAll();

if($forumVariables['adminInlogged']) {
	if(!empty($_GET['delete'])) {
		$BBCode->remove($_GET['delete']);
		header("location: BBCodeManagement.php");
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
			<?php echo $lang['result']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['info']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['accesskey']; ?>:
		</td>
		<td class="generalListHeadingRight">
			<?php echo $lang['action']; ?>:
		</td>
	</tr>	
	<tr>
		<td class="generalListInfoTextBar" colspan="5">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="generalListInfoText">
						<?php echo $lang['manageBBCodes']; ?>
					</td>
					<?php
					if($forumVariables['adminInlogged']) {
					?>
					<td class="generalListInfoTextBarLinks">
						<?php
						if($forumSettings['guidesInPopups']) {
						?>
						<a href="javascript:popup('addBBCode.php',800,600);" class="actionLink"><?php echo $lang['newBBCode']; ?></a>
						<?php
						}
						else {
						?>
						<a href="addBBCode.php" class="actionLink"><?php echo $lang['newBBCode']; ?></a>
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
		<td align="center" colspan="5">
			<?php echo $lang['noPermissionBeHere']; ?>
		</td>
	</tr>
</table>
	<?php
		$menu->getBottom();
		die();
	}
	if(!$BBCodes) {
	?>
	<tr>
		<td align="center" colspan="5">
			<?php echo $lang['noBBCodes']; ?>
		</td>
	</tr>
</table>
	<?php
		$menu->getBottom();
		die();
	}
	$i = 0;
	foreach($BBCodes as $currentBBCodeValue) {
		if($i % 2 == 0) {
	?>
	<tr>
		<td class="generalListItem1Left">
			<?php echo htmlspecialchars($currentBBCodeValue['code']); ?>
		</td>
		<td class="generalListItem1Middle">
			<?php echo htmlspecialchars($currentBBCodeValue['result']); ?>
		</td>
		<td class="generalListItem1Middle">
			<?php echo $currentBBCodeValue['info']; ?>
		</td>
		<td class="generalListItem1Middle">
			<?php echo strtoupper($currentBBCodeValue['accesskey']); ?>
		</td>
		<td class="generalListItem1Right">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editBBCode.php?id=<?php echo $currentBBCodeValue['BBCodeID'] ?>',800,600);<?php } else { ?>editBBCode.php?id=<?php echo $currentBBCodeValue['BBCodeID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a> | 
			<a href="javascript:confirmProcess('<?php echo $lang['deleteBBCodeConfirm']; ?>','BBCodeManagement.php?delete=<?php echo $currentBBCodeValue['BBCodeID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
		</td>
	</tr>
	<?php
		}
		else {
	?>	
	<tr>
		<td class="generalListItem2Left">
			<?php echo htmlspecialchars($currentBBCodeValue['code']); ?>
		</td>
		<td class="generalListItem2Middle">
			<?php echo htmlspecialchars($currentBBCodeValue['result']); ?>
		</td>
		<td class="generalListItem2Middle">
			<?php echo $currentBBCodeValue['info']; ?>
		</td>
		<td class="generalListItem2Middle">
			<?php echo strtoupper($currentBBCodeValue['accesskey']); ?>
		</td>
		<td class="generalListItem2Right">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editBBCode.php?id=<?php echo $currentBBCodeValue['BBCodeID'] ?>',800,600);<?php } else { ?>editBBCode.php?id=<?php echo $currentBBCodeValue['BBCodeID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a> | 
			<a href="javascript:confirmProcess('<?php echo $lang['deleteBBCodeConfirm']; ?>','BBCodeManagement.php?delete=<?php echo $currentBBCodeValue['BBCodeID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
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
//echo $masterCount;
?> 