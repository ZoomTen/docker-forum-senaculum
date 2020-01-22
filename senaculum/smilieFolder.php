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
	require_once("classes/dbHandler.php");
	require_once("classes/control.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$smilie = new smilieHandler;
	$db = new dbHandler;
	$control = new control;
	
	if(isset($_GET['done']))
	{
		$nextAction = "index.php";
		$error->done($lang['smiliesAdded1'],$lang['smiliesAdded2'],$nextAction);
	}
	
	$find = ""; //array
	$description = ""; //array
	$images = $smilie->getFolder($_GET['folder']); //array
	$lenght = count($images);
	
	$errorFind = ""; //array
	$errorDescription = ""; //array
	
	$errorFolder = "";
	
		
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
	
	if(isset($_POST['validate']))
	{
		for($i=0; $i<$lenght; $i++)
		{
			for($j=0; $j<$lenght; $j++)
			{
				if($_POST["find".$i] == $_POST["find".$j] && $i!=$j)
				{
					$errorFind[$i] = $lang['smilieAlreadyExist'];
				}
				else
				{}
			}
			$db=new dbHandler();
			$sql="SELECT find FROM _'pfx'_smilies WHERE find='".$db->SQLsecure($_POST["find".$i])."'";
			$result=$db->runSQL($sql);
			if($db->numRows($result)>0)
			{
				$errorFind[$i]=$lang['smilieAlreadyExist'];
			}
			if($control->maxLenght(50, $_POST["find".$i]))
				$errorFind[$i] = $lang['findToLongMax50'];
			if($control->minLenght(1, $_POST["find".$i]))
				$errorFind[$i] = $lang['findToShortMin1'];
			if($control->maxLenght(50, $_POST["description".$i]))
				$errorDescription[$i] = $lang['descriptionToLongMax50'];
			if($control->minLenght(1, $_POST["description".$i]))
				$errorDescription[$i] = $lang['descriptionToShortMin1'];
		}
		
		$anyError = false;
		
		for($i=0; $i<$lenght; $i++)
		{
			if(!empty($errorFind[$i]) || !empty($errorDescription[$i]))
			{
				for($j=0; $j<$lenght; $j++)
				{
					$find[$j] = $_POST["find".$j];
					$description[$j] = $_POST["description".$j];

				}
				$anyError=true;
				break;
			}
			else
			{
				$anyError=false;
			}
		}
		if(!$anyError)
		{
			for($i=0; $i<$lenght; $i++)
			{
				$find[$i] = $_POST["find".$i];
				$description[$i] = $_POST["description".$i];
				$images[$i] = $_GET['folder']."/".$images[$i];
			}
			$smilie->addFolder($find,$images,$description);
			$nextAction = "index.php";
			?>
			<script type="text/javascript">
			<!--
			if (window.top!=window.self) 
			{
				window.top.location="smilieFolder.php?done=true"
			}
			//-->
			</script>
			<?php
		}
	}
	header("Content-type: text/html; charset=iso-8859-1");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php echo $lang['addSmilieFolder']; ?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css"/>
	</head>
	<body>
		<form id="smilie" action="smilieFolder.php?folder=<?php echo $_GET['folder']; ?>" method="post" enctype="multipart/form-data">
			<table cellpadding="0" cellspacing="10">
				<tr>
					<td align="left" valign="top" class="guideBoxHeading">
						<?php echo $lang['input']; ?>:<br/>
						<input type="hidden" name="validate"/>
						<table cellspacing="0" cellpadding="3" class="guideInputArea">
							<?php
							for($i=0; $i<$lenght; $i++)
							{
								if(!isset($_POST['validate']))
								{
									$errorFind[$i]=null;
									$errorDescription[$i]=null;
									$description[$i]=null;
									$find[$i]=null;
								}
								if(!isset($errorFind[$i]))
								{
									$errorFind[$i]=null;
								}
								if(!isset($errorDescription[$i]))
								{
									$errorDescription[$i]=null;
								}
							?>
							<tr>
								<td class="guideInputs">
									<?php echo $lang['textVersion']; ?>: <span class="errorText"><?php if(!empty($errorFind[$i])) echo $errorFind[$i]; else echo "&nbsp;"; ?></span>
								</td>
								<td class="guideInputs">
									<?php echo $lang['description']; ?>: <span class="errorText"><?php if(!empty($errorDescription[$i])) echo $errorDescription[$i]; else echo "&nbsp;"; ?></span>
								</td>
								<td class="guideInputs">
									<?php echo $lang['image']; ?>:
								</td>
							</tr>
							<tr>
								<td>
									<input name="<?php echo "find".$i; ?>" type="text" size="40" value="<?php echo $find[$i]; ?>" class="guideTextFields"/>
								</td>
								<td>
									<input name="<?php echo "description".$i; ?>" type="text" size="40" value="<?php echo $description[$i]; ?>" class="guideTextFields"/>
								</td>
								<td>
									<img src="images/smilies/<?php echo $_GET['folder']."/".$images[$i] ?>" alt="<?php echo $lang['smilie']; ?>"/>
								</td>
							</tr>
							<?php
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>