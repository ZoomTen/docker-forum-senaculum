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
require_once('classes/avatarHandler.php');
require_once('classes/menuHandler.php');
require_once('classes/dbHandler.php');
require_once('classes/logInOutHandler.php');

$avatar = new avatarHandler;
$error = new errorHandler;
$menu = new menuHandler;
$db = new dbHandler;
$auth = new logInOutHandler;

if(!$forumVariables['adminInlogged'])
{
	$error->error($lang['notLoggedInAdmin1'], $lang['notLoggedInAdmin2']);
}

$avatars = $avatar->getAll();

if(!empty($_GET['delete'])) {
	$avatar->remove($_GET['delete']);
	header("location: avatarManagement.php");
}	

$menu->getTop();
?> 
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['avatar']; ?>:
		</td>
		<td class="generalListHeadingMiddle">
			<?php echo $lang['name']; ?>:
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
						<?php echo $lang['manageAvatars']; ?>
					</td>
					<?php
					if($forumVariables['adminInlogged']) {
					?>
					<td class="generalListInfoTextBarLinks">
						<?php
						if($forumSettings['guidesInPopups']) {
						?>
						<a href="javascript:popup('addAvatar.php',800,600);" class="actionLink"><?php echo $lang['newAvatar']; ?></a>
						<?php
						}
						else {
						?>
						<a href="addAvatar.php" class="actionLink"><?php echo $lang['newAvatar']; ?></a>
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
		<td align="center" colspan="3">
			<?php echo $lang['noPermissionBeHere']; ?>
		</td>
	</tr>
</table>
	<?php
		$menu->getBottom();
		die();
	}
	if(!$avatars) {
	?>
	<tr>
		<td align="center" colspan="3">
			<?php echo $lang['noAvatars']; ?>
		</td>
	</tr>
</table>
	<?php
		$menu->getBottom();
		die();
	}
	/*$sql = "SELECT avatarID FROM _'pfx'_members";
	$result = $db->runSQL($sql);

	$i=0;
	$usedAvatars = "";
	
	while($row = $db->fetchArray($result))
	{
		$usedAvatars[$i]=$row['avatarID'];
		$i++;
	}
	$lenght=count($usedAvatars);
	$i = 0;
	$j = 0;
	
	foreach($avatars as $currentAvatarValue)
	{
		$inUse[$i]=false;
		$j = 0;
		while($j<$lenght)
		{
			if($currentAvatarValue['avatarID']==$usedAvatars[$j])
			{
				$inUse[$i]=true;
			}
		$j ++;
		}
	$i++;
	}*/
	
	$i = 0;
	foreach($avatars as $currentAvatarValue) {
		if(!($i % 2 == 0)) {
	?>
	<tr>
		<td class="generalListItem1Middle">
			<img src="images/avatars/public/<?php echo $currentAvatarValue['fileName']; ?>" alt="<?php echo $lang['avatar']; ?>\'"/>
		</td>
		<td class="generalListItem1Middle">
			<?php echo $currentAvatarValue['name']; ?>
		</td>
		<td class="generalListItem1Right">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editAvatar.php?id=<?php echo $currentAvatarValue['avatarID'] ?>',800,600);<?php } else { ?>editAvatar.php?id=<?php echo $currentAvatarValue['avatarID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
			| <a href="javascript:confirmProcess('<?php echo $lang['deleteAvatarConfirm']; ?>','avatarManagement.php?delete=<?php echo $currentAvatarValue['avatarID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
		</td>
	</tr>
	<?php
		}
		else {
	?>	
	<tr>
		<td class="generalListItem2Middle">
			<img src="images/avatars/public/<?php echo $currentAvatarValue['fileName']; ?>" alt="<?php echo $lang['avatar']; ?>"/>
		</td>
		<td class="generalListItem2Middle">
			<?php echo $currentAvatarValue['name']; ?>
		</td>
		<td class="generalListItem2Right">
			<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editAvatar.php?id=<?php echo $currentAvatarValue['avatarID'] ?>',800,600);<?php } else { ?>editAvatar.php?id=<?php echo $currentAvatarValue['avatarID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
			| <a href="javascript:confirmProcess('<?php echo $lang['deleteAvatarConfirm']; ?>','avatarManagement.php?delete=<?php echo $currentAvatarValue['avatarID']; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a>
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