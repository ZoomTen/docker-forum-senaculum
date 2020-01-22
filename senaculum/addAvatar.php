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
	
	require_once("classes/avatarHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/dbHandler.php");
	require_once("classes/logInOutHandler.php");
	
	$auth = new logInOutHandler;
	$error = new errorHandler;
	$avatar = new avatarHandler;
	$control = new control;
	$db = new dbHandler;
	
	$errorFileName = "";
	$errorName= "";
	
	$fileName = "";
	$shortName = "";
		
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
		$errorFileName = $control->image($_FILES, "fileName", 100000, 100, 100);

		if(!empty($errorName) || !empty($errorFileName))
		{
			$shortName = $_POST['name'];
		}
		
		if(empty($errorName) && empty($errorFileName))
		{
			$avatar->add($_FILES, "fileName", $_POST['name']);
			$nextAction = "index.php";
			$error->done($lang['avatarAdded1'],$lang['avatarAdded2'],$nextAction);
		}
	}
	$title = $lang['addAvatar2'];
	$heading = $lang['addAvatar2'];
	$help = $lang['addAvatarHelp'];
	
	include("include/guideTop.php");
?>
<form action="addAvatar.php" method="post" enctype="multipart/form-data">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['file']; ?>: <span class="errorText"><?php if(!empty($errorFileName)) echo $errorFileName; else echo "&nbsp;";?></span><br/>
							<input name="fileName" type="file" size="40" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['name']; ?>: <span class="errorText"><?php if(!empty($errorName)) echo $errorName; else echo "&nbsp;";?></span><br/>
							<input name="name" type="text" size="40" value="<?php echo $shortName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['addAvatarHelptext']; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>			
<?php
$backAction = "\"self.close();\"";
$backName = "\"&lt;&lt; ".$lang['close']."\"";
$nextName = "\"".$lang['add']." &gt;&gt;\"";

include("include/guideBottom.php");
?>
 