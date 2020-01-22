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
require_once('classes/censurHandler.php');
require_once('classes/menuHandler.php');
require_once('classes/logInOutHandler.php');

$censur = new censurHandler;
$error = new errorHandler;
$menu = new menuHandler;
$auth = new logInOutHandler;

$correctLogin = true;
	
if(isset($_POST['username']) && isset($_POST['password']))
{
	$correctLogin = $auth->logIn($_POST['username'],$_POST['password']);
}

if(!$correctLogin)
{
	$error->guide($lang['notLoggedIn'], $lang['notLoggedInInvalid'], true);
}

if(!$forumVariables['inlogged'])
{
	$error->guide($lang['notLoggedIn'], $lang['notLoggedInPleaseLogin'], true);
}

if(!$forumVariables['adminInlogged'])
{
	$error->guide($lang['notLoggedInAdmin1'], $lang['notLoggedInAdmin2'], true);
}

$censurs = $censur->getAll();

if($forumVariables['adminInlogged']) {
	if(!empty($_GET['delete'])) {
		$censur->remove($_GET['delete']);
		header("location: censurManagement.php");
	}	
}

$menu->getTop();
?> 

<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="generalListHeadingLeft">
			<?php echo $lang['find']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['replace']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['byWord']; ?>:
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
						<?php echo $lang['manageCensorwords']; ?>
					</td>
					<?php
					if($forumVariables['adminInlogged']) {
					?>
					<td class="generalListInfoTextBarLinks">
						<?php
						if($forumSettings['guidesInPopups']) {
						?>
						<a href="javascript:popup('addCensur.php',800,600);" class="actionLink"><?php echo $lang['newCensorword']; ?></a>
						<?php
						}
						else {
						?>
						<a href="addCensur.php" class="actionLink"><?php echo $lang['newCensorword']; ?></a>
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
	if(!$censurs) {
	?>
	<tr>
		<td align="center" colspan="4">
			<?php echo $lang['noCensorwords']; ?>
		</td>
	</tr>
</table>
	<?php
		$menu->getBottom();
		die();
	}
	$i = 0;
	foreach($censurs as $currentCensurValue) {
		if($i % 2 == 0) {
	?>
	<tr>
		<td class="generalListItem1Left">
			<?php echo $currentCensurValue['find']; ?>
		</td>
		<td class="generalListItem1Middle">
			<?php echo $currentCensurValue['replace']; ?>
		</td>
		<td class="generalListItem1Middle">
			<?php 
			if($currentCensurValue['byWord'])
				echo $lang['yes'];
			else
				echo $lang['no'];	 	
			?>
		</td>
		<td class="generalListItem1Right">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editCensur.php?id=<?php echo $currentCensurValue['censurID'] ?>',800,600);<?php } else { ?>editCensur.php?id=<?php echo $currentCensurValue['censurID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a> | 
			<a href="javascript:confirmProcess('<?php echo $lang['deleteCensoredWordConfirm']; ?>','censurManagement.php?delete=<?php echo $currentCensurValue['censurID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
		</td>
	</tr>
	<?php
		}
		else {
	?>	
	<tr>
		<td class="generalListItem2Left">
			<?php echo $currentCensurValue['find']; ?>
		</td>
		<td class="generalListItem2Middle">
			<?php echo $currentCensurValue['replace']; ?>
		</td>
		<td class="generalListItem2Middle">
			<?php 
			if($currentCensurValue['byWord'])
				echo $lang['yes'];
			else
				echo $lang['no'];	 	

			?>
		</td>
		<td class="generalListItem2Right">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editCensur.php?id=<?php echo $currentCensurValue['censurID'] ?>',800,600);<?php } else { ?>editCensur.php?id=<?php echo $currentCensurValue['censurID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a> | 
			<a href="javascript:confirmProcess('<?php echo $lang['deleteCensoredWordConfirm']; ?>','censurManagement.php?delete=<?php echo $currentCensurValue['censurID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
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