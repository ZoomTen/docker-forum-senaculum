<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
if(!isset($page))
{
	$page = substr(strrchr($_SERVER['SCRIPT_NAME'],'/'),1);
}

global $forumSettings;

if($page == "avatarBrowser.php")
{
	$forumSettings['guidesInPopups'] = true;
}
if(!$forumSettings['guidesInPopups']) {
	require_once("classes/menuHandler.php");
	$menu = new menuHandler;
	$menu->getTop($page);
}
else {
	if($page != "addPM.php") {
		if(!empty($_POST['updateOpener'])) {
			if($_POST['updateOpener'])
				setcookie('updateOpener',1,time()+2678400,'/');
			else
				setcookie('updateOpener',0,time()+2678400,'/');	
		}	
		elseif(!empty($nextAction)) {
			setcookie('updateOpener',0,time()+2678400,'/');
		}
	}			

	header("Content-type: text/html; charset=iso-8859-1");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php
			echo $forumSettings['forumName']." - ".$title; 
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css"/>
		<?php
		if(isset($_POST['updateOpener']) && isset($nextAction)){ //Check if the opener window will relode
			if($_POST['updateOpener']) {
		?>	
		<script type='text/javascript'>
			opener.location = '<?php echo $nextAction; ?>';
		</script>
		<?php
			}
		}
		?>	
	</head>
	<body class="guideBody">
<?php
}
if(!$forumSettings['guidesInPopups']) {
?>
	<div class="guideBody2">
<?php
}
?>
		<table width="100%" cellspacing="0" border="0">
			<tr>
				<td class="guideHeading">
					<?php echo $heading; ?>
				</td>
			</tr>
			<tr>
				<td class="guideHelp">
					<?php echo $help; ?>
				</td>
			</tr>
		</table>