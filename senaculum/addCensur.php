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
	require_once("classes/censurHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/dbHandler.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$censur = new censurHandler;
	$control = new control;
	$db = new dbHandler;
	
	$errorFind = "";
	$errorReplace = "";
	$errorByWord = "";
	
	$find = "";
	$replace = "";
	$byWord = true;
		
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
		if($control->maxLenght(50, $_POST['find']))
			$errorFind = $lang['findToLongMax50'];
		if(empty($_POST['find']))
			$errorFind = $lang['mustHaveAValue'];	
			
		if($control->maxLenght(50, $_POST['replace']))
			$errorReplace = $lang['replaceToLongMax50'];	

		if(!empty($errorFind) || !empty($errorReplace) || !empty($errorbyWord))
		{
			$find = $_POST['find'];
			$replace = $_POST['replace'];
			$byWord = $_POST['byWord'];
		}
		
		if(empty($errorFind) && empty($errorReplace) && empty($errorByWord))
		{
			$censur->add($_POST['find'], $_POST['replace'], $_POST['byWord']);
			$nextAction = "index.php";
			$error->done($lang['censorAdded1'],$lang['censorAdded2'],$nextAction);
		}
	}
	$title = $lang['addCensor2'];
	$heading = $lang['addCensor2'];
	$help = $lang['addCensorHelp'];
	
	include("include/guideTop.php");
?>
<form action="addCensur.php" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['find']; ?>: <span class="errorText"><?php if(!empty($errorFind)) echo $errorFind; else echo "&nbsp;"; ?></span><br/>
							<input name="find" type="text" size="40" value="<?php echo $find; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['replace']; ?>: <span class="errorText"><?php if(!empty($errorReplace)) echo $errorReplace; else echo "&nbsp;"; ?></span><br/>
							<input name="replace" type="text" size="40" value="<?php echo $replace; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['byWord']; ?>: <span class="errorText"><?php if(!empty($errorByWord)) echo $errorByWord; else echo "&nbsp;"; ?></span><br/>
							<input name="byWord" type="checkbox" <?php if($byWord){echo "checked=\"checked\"";} ?>  class="guideTextFields"/><br/>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?><br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['addCensorHelptext']; ?>
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