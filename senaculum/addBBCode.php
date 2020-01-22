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
	require_once("classes/BBCodeHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$BBCode = new BBCodeHandler;
	$control = new control;
	
	$errorCode = "";
	$errorHtml = "";
	$errorDisplay = "";
	$errorInfo = "";
	$errorAccesskey = "";
	
	$code = "";
	$html = "";
	$display = "";
	$info = "";
	$accesskey = "";
	
		
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
	
	if(isset($_POST['code']))
	{
		if($control->maxLenght(255, $_POST['code']))
			$errorCode = $lang['codeToLongMax255'];
		if(empty($_POST['code']))
			$errorCode = $lang['mustHaveAValue'];	
			
		if($control->maxLenght(255, $_POST['html']))
			$errorHtml = $lang['HTMLToLongMax255'];
		if(empty($_POST['html']))
			$errorHtml = $lang['mustHaveAValue'];	
				
		if($control->maxLenght(20, $_POST['display']))
			$errorDiplay = $lang['displayToLongMax20'];
		if(empty($_POST['display']))
			$errorDisplay = $lang['mustHaveAValue'];	
			
		if($control->maxLenght(255, $_POST['info']))
			$errorInfo = $lang['infoToLongMax255'];
		if($control->BBCodeAccesskey($_POST['accesskey']))
			$errorAccesskey = $lang['accesskeyAlreadyUsed'];	
		if($control->BBCodeCode($_POST['code']))
			$errorCode = $lang['BBCodeAlreadyExist'];
		
		if(!empty($errorCode) || !empty($errorHtml) || !empty($errorDisplay) || !empty($errorInfo) || !empty($errorAccesskey))
		{
			$code = $_POST['code'];
			$html = $_POST['html'];
			$display = $_POST['display'];
			$info = $_POST['info'];
			$accesskey = $_POST['accesskey'];
		}
		else
		{
			$BBCode->add($_POST['code'], $_POST['html'], $_POST['display'], $_POST['info'], $_POST['accesskey']);
			$nextAction = "index.php";
			$error->done($lang['BBCodeAdded1'],$lang['BBCodeAdded2'],$nextAction);
		}
	}	
	$title = $lang['addBBCode2'];
	$heading = $lang['addBBCode2'];
	$help = $lang['addBBCodeHelp'];
	
	include("include/guideTop.php");
?>
<form action="addBBCode.php" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['code']; ?>: <span class="errorText"><?php if(!empty($errorCode)) echo $errorCode; else echo "&nbsp;"; ?></span><br/>
							<input name="code" type="text" size="40" value="<?php echo $code; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['HTML']; ?>: <span class="errorText"><?php if(!empty($errorHtml)) echo $errorHtml; else echo "&nbsp;"; ?></span><br/>
							<input name="html" type="text" size="40" value="<?php echo $html; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['display']; ?>: <span class="errorText"><?php if(!empty($errorDisplay)) echo $errorDisplay; else echo "&nbsp;"; ?></span><br/>
							<input name="display" type="text" size="40" value="<?php echo $display; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['info']; ?>: <span class="errorText"><?php if(!empty($errorInfo)) echo $errorInfo; else echo "&nbsp;"; ?></span><br/>
							<input name="info" type="text" size="40" value="<?php echo $info; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['accesskey']; ?>: <span class="errorText"><?php if(!empty($errorAccesskey)) echo $errorAccesskey; else echo "&nbsp;"; ?></span><br/>
							<select name="accesskey" class="guideDropDown">
							<?php
								$keys[] = "";
								$keys[] = "a";
								$keys[] = "b";
								$keys[] = "c";
								$keys[] = "d";
								$keys[] = "e";
								$keys[] = "f";
								$keys[] = "g";
								$keys[] = "h";
								$keys[] = "i";
								$keys[] = "j";
								$keys[] = "k";
								$keys[] = "l";
								$keys[] = "m";
								$keys[] = "o";
								$keys[] = "p";
								$keys[] = "q";
								$keys[] = "r";
								$keys[] = "s";
								$keys[] = "t";
								$keys[] = "u";
								$keys[] = "v";
								$keys[] = "w";
								$keys[] = "x";
								$keys[] = "y";
								$keys[] = "z";
								
								foreach($keys as $key) {
							?>
								<option value="<?php echo $key; ?>"<?php if($accesskey == $key) echo " selected=\"selected\""; ?>><?php echo strtoupper($key); ?></option>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['addBBCodeHelptext']; ?>
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