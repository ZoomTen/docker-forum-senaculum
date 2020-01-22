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
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$smilies = new smilieHandler;
	$control = new control;
	$errorFind = "";
	$errorDescription = "";
	$find = "";
	$description = "";
	
	if(empty($_GET['id']))
	{
		$error->guide($lang['incorrectURL1'], $lang['incorrectURL2'], false);
	}
	
	$smilie = $smilies->getOne($_GET['id'], true);
		
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
		if($db->numRows($result)>0 && $_POST['find'] != $smilie['find'])
		{
			$errorFind=$lang['smilieAlreadyExist'];
		}
		
		if($control->maxLenght(50, $_POST['find']))
			$errorFind = $lang['findToLongMax50'];
		if(empty($_POST['find']))
			$errorFind = $lang['mustContainValue'];
				
		if($control->maxLenght(50, $_POST['description']))
			$errorDescription = $lang['descriptionToLongMax50'];
		
		if(!empty($errorFind) || !empty($errorDescription))
		{
			$find = $_POST['find'];
			$description = $_POST['description'];
		}
		
		if(empty($errorFind) && empty($errorDescription))
		{
			$smilies->edit($smilie['smilieID'],$_POST['find'], $smilie['fileName'], $_POST['description']);
			$nextAction = "index.php";
			$error->done($lang['smilieEdited1'],$lang['smilieEdited2'],$nextAction);
		}
	}
	else
	{
		$find = $smilie['find'];
		$description = $smilie['description'];
	}
	$title = $lang['editSmilieHeading'];
	$heading = $lang['editSmilieHeading'];
	$help = $lang['editSmilieHelp'];
	
	include("include/guideTop.php");
	
?>
<form action="editSmilie.php?id=<?php echo $_GET['id']?>" method="post">
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
							<?php echo $lang['description']; ?>: <span class="errorText"><?php if(!empty($errorDescription)) echo $errorDescription; else echo "&nbsp;"; ?></span><br/>
							<input name="description" type="text" size="40" value="<?php echo $description; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>	
				</table>			
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideEHelp">
					<tr>
						<td>
							<?php echo $lang['editSmilieHelptext']; ?>
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