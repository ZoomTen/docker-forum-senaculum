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

$auth = new logInOutHandler;
$error = new errorHandler;
	
if(isset($_GET['done'])) {			
	if($_GET['done']) {
		$error->done($lang['permissionsChanged1'],$lang['permissionsChanged2'],"index.php");
	}
}	

if(!empty($_GET['id'])) {
	require_once("classes/memberHandler.php");
	
	$member = new memberHandler;
		
	$currentMember = $member->getOne($_GET['id'],false);
		
	$userName = $currentMember['userName'];

}
else {
	$errorSelectedMember = "";
	$selectedMember = "";
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
	$title = $lang['memberPermissionsPage2'];
	$heading = $lang['memberPermissionsPage2'];
	$help = $lang['memberPermissionsPage2Help1'].$userName.$lang['memberPermissionsPage2Help2'];
	
	include("include/guideTop.php");
?>
<table cellpadding="0" cellspacing="10">
	<tr>
		<td align="left" valign="top" class="guideBoxHeading">
			<?php echo $lang['memberPermissions']; ?>:<br/>
			<table cellspacing="0" cellpadding="3" class="guideInputArea">
				<tr>
					<td class="guideInputs" style="width:930px;">
						<table width="905" cellpadding="2" cellspacing="0">
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
						<iframe src="editMemberPermissions.php?id=<?php echo $_GET['id']; ?>" name="permissions" frameborder="0" width="100%" height="200"></iframe>
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
									<?php echo $lang['memberPermissionsPage2Helptext1']; ?>
								</td>
								<td align="justify" valign="top" style="padding-left:15px;">
									<?php echo $lang['memberPermissionsPage2Helptext2']; ?>
								</td>
								<td valign="top" style="width:170px; padding-left:15px;">
									<?php echo $lang['memberPermissionsPage2Helptext3']; ?>
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
	$backAction = "\"window.location = 'memberPermissions.php';\"";
	$backName = "\"&lt;&lt; ".$lang['back']."\"";
	$nextName = "\"".$lang['change']." &gt;&gt;\"";
	
	if($_SERVER['HTTP_USER_AGENT'] == "MSIE")
	{
		$nextAction = "document.permissions.getElementById('memberPermissions').submit();";
	}
	else
	{
		$nextAction = "window.frames['permissions'].document.getElementById('memberPermissions').submit();";
	}
}
else {
	if(isset($_POST['submit'])) {
		if($_POST['submit'] == $lang['find']) {
			require_once("classes/searchHandler.php");
			$search = new searchHandler;
			$users = $search->user($_POST['selectedMember']);
			if(empty($users))
				$selectedMember = $_POST['selectedMember'];
			else
				$selectedMember = $users[0]['userName'];	
		}
		else {
			require_once("classes/memberHandler.php");
			$member = new memberHandler;
			$memberID = $member->getMemberID($_POST['selectedMember']);
			if(empty($memberID))
				$errorSelectedMember = $lang['userNotExist'];
		
			if(empty($errorSelectedMember))
			{
				header("location: memberPermissions.php?id=".$memberID);
			}
			else 
			{
				$selectedMember = $_POST['selectedMember'];
			}
		}
	}		
		
	$title = $lang['memberPermissionsPage1'];
	$heading = $lang['memberPermissionsPage1'];
	$help = $lang['memberPermissionsPage1Help'];
	
	include("include/guideTop.php");
?>
<form action="memberPermissions.php" id="memberPermission" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['member']; ?>: <span class="errorText"><?php if(!empty($errorSelectedMember)) echo $errorSelectedMember; else echo "&nbsp;"; ?></span><br/>
							<input name="selectedMember" type="text" size="33" maxlength="15" value="<?php echo $selectedMember; ?>" class="guideTextFields"/>
							<input type="submit" name="submit" value="<?php echo $lang['find']; ?>" class="guideButton"/>
						</td>
					</tr>
					<?php
					if(!empty($users)) {
					?>		
					<tr>
						<td class="guideInputs">
							<?php echo $lang['findResult']; ?>:<br/>
							<select name="users" class="guideDropDown" onChange="memberPermission.selectedMember.value = this.options[this.selectedIndex].value;">
					<?php			
						foreach($users as $user) {
					?>
									<option value="<?php echo $user['userName']; ?>"><?php echo $user['userName']; ?></option>
					<?php
						}
					?>
							</select>
						</td>
					</tr>
					<?php			
					}	
					?>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['memberPermissionsPage1Helptext']; ?>
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