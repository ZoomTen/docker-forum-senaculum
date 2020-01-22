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
	require_once("classes/dbHandler.php");
	require_once("classes/control.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$avatar = new avatarHandler;
	$db = new dbHandler;
	$control = new control;
	
	if(isset($_GET['done']))
	{
		$nextAction = "index.php";
		$error->done("Avatars added!","New avatars has been added!","addFolderAvatar",$nextAction);
	}
	
	$shortName = ""; //array
	$images = $avatar->getFolder($_GET['folder']); //array
	$lenght = count($images);
	
	$errorName = ""; //array
	
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
			if($control->maxLenght(50, $_POST["name".$i]))
				$errorDescription[$i] = $lang['descriptionToLongMax50'];
		}
		
		$anyError = false;
		
		for($i=0; $i<$lenght; $i++)
		{
			if(!empty($errorName[$i]))
			{
				for($j=0; $j<$lenght; $j++)
				{
					$shortName[$j] = $_POST["name".$j];
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
				$shortName[$i] = $_POST["name".$i];
				$images[$i] = $_GET['folder']."/".$images[$i];
			}
			$avatar->addFolder($images,$shortName);
			$nextAction = "index.php";
			?>
			<script type="text/javascript">
			if (window.top!=window.self) 
			{
				window.top.location="avatarFolder.php?done=true"
			}
			</script>
			<?php
		}
	}
header("Content-type: text/html; charset=iso-8859-1");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			avatarFolder
		</title>
		<link rel="stylesheet" type="text/css" href="style.css"/>
	</head>
	<body>
		<form id="avatar" action="avatarFolder.php?folder=<?php echo $_GET['folder']; ?>" method="post" enctype="multipart/form-data">
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
									$errorName[$i]=null;
									$shortName[$i]=null;
								}
								if(!isset($errorName[$i]))
								{
									$errorName[$i]=null;
								}
							?>
							<tr>
								<td class="guideInputs">
									<?php echo $lang['input']; ?>: <span class="errorText"><?php if(!empty($errorName[$i])) echo $errorName[$i]; else echo "&nbsp;"; ?></span>
								</td>
								<td class="guideInputs">
									<?php echo $lang['image']; ?>:
								</td>
							</tr>
							<tr>
								<td>
									<input name=<?php echo "name".$i; ?> type="text" size="40" value="<?php echo $shortName[$i]; ?>" class="guideTextFields"/>
								</td>
								<td>
									<img src="images/avatars/public/<?php echo $_GET['folder']."/".$images[$i] ?>" alt="Avatar"/>
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