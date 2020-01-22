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
	require_once("classes/avatarHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$avatars = new avatarHandler;
	$control = new control;
	$errorName = "";
	$shortName = "";
	
	if(empty($_GET['id']))
	{
		$error->guide($lang['incorrectURL1'], $lang['incorrectURL2'], false);
	}
	
	$avatar = $avatars->getOne($_GET['id'], true);
		
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
	
	if(isset($_POST['name']))
	{
		if($control->maxLenght(50, $_POST['name']))
			$errorName = $lang['nameToLongmax50'];
		
		if(!empty($errorName))
		{
			$shortName = $_POST['name'];
		}
		
		if(empty($errorName))
		{
			$avatars->edit($avatar['avatarID'],$avatar['fileName'], $_POST['name']);
			$nextAction = "index.php";
			$error->done($lang['avatarEdited1'],$lang['avatarEdited2'],$nextAction);
		}
	}
	else
	{
		$shortName = $avatar['name'];
	}
	$title = $lang['editAvatarHeading'];
	$heading = $lang['editAvatarHeading'];
	$help = $lang['editAvatarHelp'];
	
	include("include/guideTop.php");
	
?>
<form action="editAvatar.php?id=<?php echo $_GET['id']?>" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['name']; ?>: <span class="errorText"><?php if(!empty($errorName)) echo $errorName; else echo "&nbsp;"; ?></span><br/>
							<input name="name" type="text" size="40" value="<?php echo $shortName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>			
				</table>			
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideEHelp">
					<tr>
						<td>
							<?php echo $lang['editAvatarHelptext']; ?>
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