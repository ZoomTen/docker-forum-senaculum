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
	
	$errorFind = "";
	$errorFileName = "";
	$errorDescription = "";
	
	$find = "";
	$fileName = "";
	$description = "";
		
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
	
	if(isset($_POST['find']))
	{
		$db=new dbHandler();
		$sql="SELECT find FROM _'pfx'_smilies WHERE find='".$db->SQLsecure($_POST['find'])."'";
		$result=$db->runSQL($sql);
		if($db->numRows($result)>0)
		{
			$errorFind = $lang['smilieAlreadyExist'];
		}
		
		if($control->maxLenght(50, $_POST['find']))
			$errorFind = $lang['findToLongMax50'];
		if(empty($_POST['find']))
			$errorFind = $lang['mustContainValue'];	
			
		if($control->maxLenght(50, $_POST['description']))
			$errorDescription = $lang['descriptionToLongMax50'];
		$errorFileName = $control->image($_FILES, "fileName", 100000, 400, 400);
		

		if(!empty($errorFind) || !empty($errorDescription) || !empty($errorFileName))
		{
			$find = $_POST['find'];
			//$fileName = $_POST['fileName'];
			$description = $_POST['description'];
		}
		
		if(empty($errorFind) && empty($errorDescription) && empty($errorFileName))
		{
			$smilie->add($_FILES ,$_POST['find'], "fileName", $_POST['description']);
			$nextAction = "index.php";
			$error->done($lang['smilieAdded1'],$lang['smilieAdded2'],$nextAction);
		}
	}
	$title = $lang['addSmilie2'];
	$heading = $lang['addSmilie2'];
	$help = $lang['addSmilieHelp'];
	
	include("include/guideTop.php");
?>
<form action="addSmilie.php" method="post" enctype="multipart/form-data">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['textVersion']; ?>: <span class="errorText"><?php if(!empty($errorFind)) echo $errorFind; else echo "&nbsp;"; ?></span><br/>
							<input name="find" type="text" size="40" value="<?php echo $find; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['file']; ?>: <span class="errorText"><?php if(!empty($errorFileName)) echo $errorFileName; else echo "&nbsp;"; ?></span><br/>
							<input name="fileName" type="file" size="40" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['description']; ?>: <span class="errorText"><?php if(!empty($errorDescription)) echo $errorDescription; else echo "&nbsp;"; ?></span><br/>
							<input name="description" type="text" size="40" value="<?php echo $description; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['addSmilieHelptext']; ?>
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
 