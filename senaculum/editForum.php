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
require_once('classes/forumHandler.php');
require_once('classes/menuHandler.php');
require_once("classes/forumGroupHandler.php");
require_once('classes/logInOutHandler.php');
require_once('classes/control.php');

$error = new errorHandler;
$forums = new forumHandler;
$menu = new menuHandler;
$groups = new forumGroupHandler;
$control = new control;
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
	if(!$auth->moderator($_GET['id'],"all"))
		$error->guide($lang['noPermissionDoThis1'], $lang['noPermissionDoThis2'], false);
}

$forum = $forums->getOne($_GET['id'], true);

	$errorHeadline = "";
	$errorInfoText = "";
	
	$headline = "";
	$infoText = "";
	$groupID = "";
	$locked = "0";
	
	if(!isset($_POST['headline']))
	{
		$headline = $forum['name'];
		$infoText = $forum['infoText'];
		$groupID = $forum['groupID'];
		$locked = $forum['locked'];
	}
	else
	{
		$errorHeadline=$control->text($_POST['headline'],1 , 50);
		$errorInfoText=$control->text($_POST['infoText'], 1, 1000);
		
		if(!empty($errorHeadline)||!empty($errorInfoText))
		{
			$headline = $_POST['headline'];
			$infoText = $_POST['infoText'];
			$groupID = $_POST['group'];
			if(isset($_POST['locked']))
				$locked = 1;
			else
				$locked = 0;	
		}
		else
		{
			if(isset($_POST['locked']))
				$locked = 1;
			else
				$locked = 0;	
			$forums->edit($_GET['id'],$_POST['headline'],$_POST['infoText'],$_POST['group'],$locked);
			$nextAction = "index.php";
			$error->done($lang['forumEdited1'],$lang['forumEdited2'],$nextAction);
		}
	}
	$title = $lang['editForumHeading1'].$forum['name'].$lang['editForumHeading2'];
	$heading = $lang['editForumHeading1'].$forum['name'].$lang['editForumHeading2'];
	$help = $lang['editForumHelp1'].$forum['name'].$lang['editForumHelp2'];
	
	include("include/guideTop.php");
?>
<form action="editForum.php?id=<?php echo $_GET['id'] ?>" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['headline']; ?>: <span class="errorText"><?php if(!empty($errorHeadline)) echo $errorHeadline; else echo "&nbsp;"; ?></span><br/>
							<input name="headline" type="text" size="40" value="<?php echo $headline; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>		
					<tr>
						<td class="guideInputs">		
							<?php echo $lang['infoText']; ?>: <span class="errorText"><?php if(!empty($errorInfoText)) echo $errorInfoText; else echo "&nbsp;"; ?></span><br/>
							<textarea name="infoText"rows="20" cols="93" class="guideTextFields"><?php echo $infoText; ?></textarea>
						</td>
					</tr>	
					<tr>
						<td class="guideInputs">
							<?php echo $lang['group']; ?>:
							<select name="group" class="guideTextFields">
							<?php
							$groups = $groups->getAll();
							foreach($groups as $groupsElement) {
							?>
								<option value="<?php echo $groupsElement['groupID']; ?>" <?php if($groupsElement['groupID'] == $groupID) echo "selected=\"selected\""; ?> class="guideDropDown"><?php echo $groupsElement['name']; ?></option>
							<?php
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['lockForum']; ?>:<br/>
							<input type="checkbox" name="locked"<?php if($locked) echo " checked=\"checked\""; ?>/>	
						</td>	
					</tr>	
				</table>
			</td>
		</tr>
	</table>			
<?php
$backAction = "\"self.close();\"";
$backName = "\"&lt;&lt; ".$lang['close']."\"";
$nextName = "\"".$lang['edit']." &gt;&gt;\"";

include("include/guideBottom.php");
?>