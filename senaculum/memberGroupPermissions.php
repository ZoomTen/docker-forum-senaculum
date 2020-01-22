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
	
require_once("classes/logInOutHandler.php");
require_once("classes/errorHandler.php");
require_once("classes/memberGroupHandler.php");

$auth = new logInOutHandler;
$error = new errorHandler;
$memberGroup = new memberGroupHandler;
	
if(isset($_GET['done'])) {			
	if($_GET['done']) {
		$error->done($lang['permissionsChanged1'],$lang['permissionsChanged2'],"index.php");
	}
}	

if(empty($_GET['id'])) {
	$memberGroups = $memberGroup->getAll();
}
else {
	require_once("classes/memberGroupHandler.php");
	require_once("classes/memberHandler.php");
	
	$memberGroup = new memberGroupHandler;
	$member = new memberHandler;
		
	$currentMemberGroup = $memberGroup->getOne($_GET['id']);
	//$selectedGroupMembers = $currentMemberGroup['groupMemberUserName'];
		
	$groupName = $currentMemberGroup['name'];
	$groupModerator = $currentMemberGroup['groupModeratorUserName'];
}

		
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
$done = false;
if(!empty($_GET['id'])) {	
	$title = $lang['usergroupPermissionsPage2'];
	$heading = $lang['usergroupPermissionsPage2'];
	$help = $lang['usergroupPermissionsPage2Help1'].$groupName.$lang['usergroupPermissionsPage2Help2'];
	
	include("include/guideTop.php");
?>
<table cellpadding="0" cellspacing="10">
	<tr>
		<td align="left" valign="top" class="guideBoxHeading">
			<?php echo $lang['usergroupPermissions']; ?>:<br/>
			<table cellspacing="0" cellpadding="3" class="guideInputArea">
				<tr>
					<td class="guideInputs" style="width:930px;">
						<table style="width:905px;" cellpadding="2" cellspacing="0">
							<tr>
								<td style="width:115px;" class="guidePermissionHeading">
									<?php echo $lang['forum']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['view']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['read']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['thread']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['post']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['edit']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['delete']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['sticky']; ?>
								</td>
								<td style="width:70px;" class="guidePermissionHeading">
									<?php echo $lang['announce']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['vote']; ?>
								</td>
								<td style="width:100px;" class="guidePermissionHeading">
									<?php echo $lang['pollCreate']; ?>
								</td>
								<td style="width:50px;" class="guidePermissionHeading">
									<?php echo $lang['attachmentsShort']; ?>
								</td>
								<td style="width:40px;" class="guidePermissionHeading">
									<?php echo $lang['moderatorShort']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['default']; ?>
								</td>
							</tr>
						</table>
						<iframe src="editMemberGroupPermissions.php?id=<?php echo $_GET['id']; ?>" name="permissions" frameborder="0" width="100%" height="200"></iframe>
					</td>
				</tr>
			</table>	
		</td>
		</tr>
		<tr>
		<td align="left" valign="top" class="guideBoxHeading">
			<?php echo $lang['help']; ?>:<br/>
			<table class="guideEHelp" cellpadding="3" cellspacing="0">
				<tr>
					<td style="width:780px;">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td align="justify" valign="top">
									<?php echo $lang['usergroupPermissionsPage2Helptext1']; ?>
								</td>
								<td valign="top" align="justify" style="padding-left:15px;">
									<?php echo $lang['usergroupPermissionsPage2Helptext2']; ?>
								</td>
								<td align="left" valign="top" style="width:170px; padding-left:15px;">
									<?php echo $lang['usergroupPermissionsPage2Helptext3']; ?>
								</td>
							</tr>
						</table>		
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
	$backAction = "\"window.location = 'memberGroupPermissions.php';\"";
	$backName = "\"&lt;&lt; ".$lang['back']."\"";
	$nextName = "\"".$lang['change']." &gt;&gt;\"";
	
	if($_SERVER['HTTP_USER_AGENT'] == "MSIE")
	{
		$nextAction = "document.permissions.getElementById('memberGroupPermissions').submit();";
	}
	else
	{
		$nextAction = "window.frames['permissions'].document.getElementById('memberGroupPermissions').submit();";
	}
}		
else {
	if(!empty($_POST['groupName']))
	{
		header("location: memberGroupPermissions.php?id=".$_POST['groupName']);
	}	
		
	$title = $lang['usergroupPermissionsPage1'];
	$heading = $lang['usergroupPermissionsPage1'];
	$help = $lang['usergroupPermissionsPage1Help'];
	
	include("include/guideTop.php");
?>
<form action="memberGroupPermissions.php" id="memberGroup" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['usergroup']; ?>:<br/>
							<select name="groupName" class="guideDropDown">
								<?php
								if(!empty($memberGroups)) {
									foreach($memberGroups as $element) {
								?>
								<option value="<?php echo $element['groupID']; ?>"><?php echo $element['name']; ?></option>
								<?php		
									}
								}
								?>
							</select>
						</td>
					</tr>	
				</table>
			</td>
		</tr>
	</table>			
<?php
	$backAction = "\"self.close();\"";
	$backName = "\"&lt;&lt; ".$lang['close']."\"";
	$nextName = "\"".$lang['next']." &gt;&gt;\"";
}	

include("include/guideBottom.php");
?>