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
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$censurs = new censurHandler;
	$control = new control;
	$errorFind = "";
	$errorReplace = "";
	$find = "";
	$replace = "";
	$byWord = "";
	
	if(empty($_GET['id']))
	{
		$error->guide($lang['incorrectURL1'], $lang['incorrectURL2'], false);
	}
	
	$censur = $censurs->getOne($_GET['id'], true);
		
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
		
		if(!empty($errorFind)||!empty($errorReplace))
		{
			$find = $_POST['find'];
			$replace = $_POST['replace'];
			if(isset($_POST['byWord']))
				$byWord = $_POST['byWord'];
			else
				$byWord = false;
			
		}
		
		if(empty($errorFind) && empty($errorReplace))
		{
			$censurs->edit($censur['censurID'], $_POST['find'], $_POST['replace'], $_POST['byWord']);
			$nextAction = "index.php";
			$error->done($lang['censurwordEdited1'],$lang['censurwordEdited2'],$nextAction);
		}
	}
	else
	{
		$find = $censur['find'];
		$replace = $censur['replace'];
		$byWord = $censur['byWord'];
	}
	$title = $lang['editCensorHeading'];
	$heading = $lang['editCensorHeading'];
	$help = $lang['editCensorHelp'];
	
	include("include/guideTop.php");
	
?>
<form action="editCensur.php?id=<?php echo $_GET['id']?>" method="post">
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
							<?php echo $lang['byWord']; ?>:<br/>
							<input name="byWord" type="checkbox" <?php if($byWord){echo "checked=\"checked\"";}?> class="guideTextFields"/><br/>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>		
				<table cellspacing="0" cellpadding="3" class="guideEHelp">
					<tr>
						<td>
							<?php echo $lang['editCensorHelptext']; ?>
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