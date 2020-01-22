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
	require_once("classes/smilieHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/dbHandler.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$smilie = new smilieHandler;
	$control = new control;
	$db = new dbHandler;
	
	$images=""; //array
	
	$errorFolder = "";
	
	$folder = "";
		
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
	
	if(isset($_POST['folder']))
	{
		$smilies = $smilie->getFolder($_POST['folder']);
		
		if(empty($smilies))
		{
			$errorFolder=$lang['thisFolderEmpty'];
		}
		
		$sql = "SELECT fileName FROM _'pfx'_smilies";
		$result = $db->runSQL($sql);
	
		while($row = $db->fetchArray($result))
		{
			if(stristr($row['fileName'],$_POST['folder']."/"))
			{
				$errorFolder=$lang['smilieFolderAlreadyUsed'];
			}
		}
		if(!empty($errorFolder))
		{
			$folder = $_POST['folder'];
		}
		
		if(empty($errorFolder))
		{	
			$images = $smilie->getFolder($_POST['folder']);
			
			$title = $lang['addFolderWithSmilies'];
			$heading = $lang['addSmilieFolder1'];
			$help = $lang['addSmilieFolder2'];
	
			include("include/guideTop.php");
			?>
			<table width="100%">
				<tr>
					<td valign="top" align="center">
						<iframe src="smilieFolder.php?folder=<? echo $_POST['folder']?>" name="smilieFolder" class="guideInputArea" width="700" height="470"></iframe>
					</td>
				</tr>
			</table>
			<?
			$backAction = "\"window.location = 'addFolderSmilie.php';\"";
			$backName = "\"&lt;&lt; Back\"";
			if($_SERVER['HTTP_USER_AGENT'] == "MSIE")
			{
				$nextAction = "document.smilieFolder.getElementById('smilie').submit();";
			}
			else
			{
				$nextAction = "window.frames['smilieFolder'].document.getElementById('smilie').submit();";
			}
			$nextName = "\"".$lang['next']." &gt;&gt;\"";
			
			include("include/guideBottom.php");
		die;
		}
	}
	$title = $lang['addFolderWithSmilies'];
	$heading = $lang['addSmilieFolder1'];
	$help = $lang['addSmilieFolder2'];
	
	include("include/guideTop.php");
?>
<form action="addFolderSmilie.php" method="post" enctype="multipart/form-data">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['folder']; ?>: <span class="errorText"><?php if(!empty($errorFolder)) echo $errorFolder; else echo "&nbsp;"; ?></span><br/>
							<input name="folder" type="text" size="40" value="<?php echo $folder; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['addSmilieFolderHelptext']; ?>
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

include("include/guideBottom.php");
?>
 