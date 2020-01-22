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
	require_once("classes/forumHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/forumGroupHandler.php");
	require_once("classes/dbHandler.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$forum = new forumHandler;
	$control = new control;
	$group = new forumGroupHandler;
	$db = new dbHandler;
	
	$errorHeadline = "";
	$errorInfoText = "";
	$errorGroup = "";
	
	$headline = "";
	$infoText = "";
		
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
	
	if(isset($_POST['headline']))
	{
		$errorHeadline=$control->text($_POST['headline'],1, 50);
		$errorInfoText=$control->text($_POST['infoText'],1, 255);
		if(empty($_POST['group']))
			$errorGroup = $lang['noGroupSelected'];
		
		if(!empty($errorHeadline)||!empty($errorInfoText)||!empty($errorGroup))
		{
			$headline = $_POST['headline'];
			$infoText = $_POST['infoText'];
		}
		
		if(empty($errorHeadline)&&empty($errorInfoText)&&empty($errorGroup))
		{
			$forum->add($_POST['headline'], $_POST['infoText'],$_POST['group'], $forumVariables['inloggedMemberID']);
			$nextAction = "index.php";
			$error->done($lang['forumCreated1'],$lang['forumCreated2'],$nextAction);
		}
	}

	$title = $lang['addForum'];
	$heading = $lang['addForum'];
	$help = $lang['addForumHelp'];
	
	include("include/guideTop.php");
?>
<form action="addForum.php" method="post">
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
						<td valign="bottom" class="guideInputs">
							<?php echo $lang['infoText']; ?>: <span class="errorText"><?php if(!empty($errorInfoText)) echo $errorInfoText; else echo "&nbsp;"; ?></span><br/>
							<textarea name="infoText" rows="20" cols="93" class="guideTextFields"><?php echo $infoText; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['group']; ?>: <span class="errorText"><?php if(!empty($errorGroup)) echo $errorGroup; else echo "&nbsp;"; ?></span><br/>
							<select name="group" class="guideTextFields">
							<?php
							$groups = $group->getAll();
							foreach($groups as $groupsElement) {
							?>
								<option value="<?php echo $groupsElement['groupID']; ?>" class="guideDropDown"><?php echo $groupsElement['name']; ?></option>
							<?php
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
$nextName = "\"".$lang['create']." &gt;&gt;\"";

include("include/guideBottom.php");
?>