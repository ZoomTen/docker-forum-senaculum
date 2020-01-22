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
require_once("classes/forumHandler.php");

$auth = new logInOutHandler;
$error = new errorHandler;
$forum = new forumHandler;
	
if(isset($_GET['done'])) {			
	if($_GET['done']) {
		$error->done($lang['permissionsChanged1'],$lang['permissionsChanged2'],"index.php");
	}
}	

if(empty($_GET['id'])) {
	$forums = $forum->getAllSimple();
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
	$title = $lang['forumPermissionsPage2'];
	$heading = $lang['forumPermissionsPage2'];
	$help = $lang['forumPermissionsPage2Help'];
	
	include("include/guideTop.php");
?>
<table cellpadding="0" cellspacing="10">
	<tr>
		<td align="left" valign="top" class="guideBoxHeading">
			<?php echo $lang['forumPermissions']; ?>:<br/>
			<table cellspacing="0" cellpadding="3" class="guideInputArea">
				<tr>
					<td class="guideInputs" style="width:930px;">
						<table style="width:905px;" cellpadding="2" cellspacing="0">
							<tr>
								<td style="width:125px;" class="guidePermissionHeading">
									<?php echo $lang['forum']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['view']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['read']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['thread']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['post']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['edit']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['delete']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['sticky']; ?>
								</td>
								<td style="width:70px;" class="guidePermissionHeading">
									<?php echo $lang['announce']; ?>
								</td>
								<td style="width:60px;" class="guidePermissionHeading">
									<?php echo $lang['vote']; ?>
								</td>
								<td style="width:100px;" class="guidePermissionHeading">
									<?php echo $lang['pollCreate']; ?>
								</td>
								<td style="width:80px;" class="guidePermissionHeading">
									<?php echo $lang['attachments']; ?>
								</td>
							</tr>
						</table>
						<iframe src="editForumPermissions.php?id=<?php echo urlencode($_GET['id']); ?>" name="permissions" frameborder="0" width="100%" height="200"></iframe>
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
									<?php echo $lang['forumPermissionsPage2Helptext1']; ?>
								</td>
								<td align="justify" valign="top" style="padding-left:15px;">
									<?php echo $lang['forumPermissionsPage2Helptext2']; ?>
								</td>
								<td valign="top" style="width:170px; padding-left:15px;">
									<?php echo $lang['forumPermissionsPage2Helptext3']; ?>
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
	$backAction = "\"window.location = 'forumPermissions.php';\"";
	$backName = "\"&lt;&lt; ".$lang['back']."\"";
	$nextName = "\"".$lang['change']." &gt;&gt;\"";
	
	if($_SERVER['HTTP_USER_AGENT'] == "MSIE")
	{
		$nextAction = "document.permissions.getElementById('forumPermissions').submit();";
	}
	else
	{
		$nextAction = "window.frames['permissions'].document.getElementById('forumPermissions').submit();";
	}
}
else {
	if(!empty($_POST['forumName']))
	{
		header("location: forumPermissions.php?id=".implode("+",$_POST['forumName']));
	}	
	
	$title = $lang['forumPermissionsPage1'];
	$heading = $lang['forumPermissionsPage1'];
	$help = $lang['forumPermissionsPage1Help'];
	
	include("include/guideTop.php");
?>
<form action="forumPermissions.php" id="memberGroup" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['forumName']; ?>:<br/>
							<select name="forumName[]" multiple="multiple" size="25" class="guideDropDown">
								<?php
								if(!empty($forums)) {
									foreach($forums as $element) {
								?>
								<option value="<?php echo $element['forumID']; ?>"><?php echo $element['name']; ?></option>
								<?php		
									}
								}
								?>
							</select>
						</td>
					</tr>	
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['forumPermissionsPage1Helptext']; ?>
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