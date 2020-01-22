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
	require_once("classes/forumGroupHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/dbHandler.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$group = new forumGroupHandler;
	$control = new control;
	$db = new dbHandler;
	
	$errorHeadline = "";
	
	$headline = "";
		
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
		
		if(!empty($errorHeadline))
		{
			$headline = $_POST['headline'];
		}
		
		if(empty($errorHeadline))
		{
			$group->add($_POST['headline']);
			$nextAction = "index.php";
			$error->done($lang['forumGroupCreated1'],$lang['forumGroupCreated2'],$nextAction);
		}
	}	
	$title = $lang['addForumGroup'];
	$heading = $lang['addForumGroup'];
	$help = $lang['addForumGroupHelp'];
	
	include("include/guideTop.php");
?>
<form action="addForumGroup.php" method="post">
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