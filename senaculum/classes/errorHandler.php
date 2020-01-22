<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
class errorHandler {

	function errorHandler(){}

	function error($headLine, $text)
	{
		global $lang;
		header("Content-type: text/html; charset=iso-8859-1");
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
		<html>
			<head>
				<title>
					<?php echo $lang['error']; ?>, <?php echo $headLine; ?>
				</title>
				<link rel="stylesheet" type="text/css" href="style.css"/>
			</head>
			<body class="guideBody">
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td valign="top" class="errorHeading">
							<?php echo $lang['error']; ?>, <?php echo $headLine;?>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<table cellspacing="0" cellpadding="5">
								<tr>
									<td valign="top" class="errorText">
										<?php echo $text;?><br/>
									</td>	
								</tr>
							</table>	
						</td>
					</tr>
				</table>				
			</body>
		</html>
		<?php
		die;
	}

	function guide($headLine, $text, $loginError)
	{
		global $lang;
		$title = $headLine;
		$heading = $headLine;
		$help = $text;
		$page = substr(strrchr($_SERVER['SCRIPT_NAME'],'/'),1);
		
		include("./include/guideTop.php");
		?>
		<form action="<?php echo $page; ?>?id=<?php echo $_GET['id']; ?>" method="post">		
			<table>
				<tr>
					<td>
							<?php
							if($loginError)
							{
							?>
							<?php echo $lang['username']; ?>:<br/>
							<input name="username" type="text" size="40"><br/>
							<?php echo $lang['password']; ?>:<br/>
							<input name="password" type="password" size="40">
							<?php
							}
							?>
					</td>
				</tr>
			</table>
			
		<?php
		$backAction = "\"self.close();\"";
		$backName = "\"&lt;&lt; ".$lang['close']."\"";
		$nextName = "\"".$lang['OK']." &gt;&gt;\"";

		include("include/guideBottom.php");
		die;
	}
	
	function done($title,$help,$nextAction)
	{
		global $forumSettings;
		global $lang;
			
		$page = substr(strrchr($_SERVER['SCRIPT_NAME'],'/'),1);
		$heading = $title;
		
		include("./include/guideTop.php");
		if($forumSettings['guidesInPopups'])
			$nextAction = "close";
?>
		<script type="text/javascript">
			<!--
			<?php
			if($nextAction == "close") {
			?>
			setTimeout("self.close()",5000);
			<?php
			}
			else {
			?>
			function windowLocation(url) {
				window.location = url;
			}
			setTimeout("windowLocation('<?php echo $nextAction ?>')",5000);
			<?php
				$nextAction = "window.location = '".$nextAction."';";
			}
			?>
			//-->
		</script>
		<table width="100%" cellspacing="0" cellpadding="2" border="0">
			<tr>
				<td class="guideInfotext" <?php if($forumSettings['guidesInPopups']) echo "style=\"height:400px;\""; else echo "style=\"height:100px;\""; ?>>
					<?php echo $help; ?>
				</td>
			</tr>
		</table>
<?php
		$backAction = "\"self.close();\"";
		$backName = "\"&lt;&lt; ".$lang['close']."\"";
		$nextName = "\"".$lang['OK']." &gt;&gt;\"";
		//if($forumSettings['guidesInPopups'])
		//	$page = "done";

		include("include/guideBottom.php");
		die;
	}
} 
?>