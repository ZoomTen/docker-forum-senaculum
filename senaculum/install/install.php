<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require("lang/default/lang.php");

if(!empty($_GET['language'])) {
	if(@include("lang/".basename($_GET['language'])."/lang.php"))
		$language = basename($_GET['language']);
	else
		$language = "default";	
}
else
	$language = "default";

global $lang;
	
header("Content-type: text/html; charset=iso-8859-1");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php echo $lang['forumSenaculum']; ?> - <?php echo $lang['installation']; ?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css"/>
	</head>
	<body>
		<div align="center">
			<form action="step1.php" method="get">
				<div style="float:left;">
					<table style="width:150px;" cellspacing="0" cellpadding="2">
						<tr>
							<td align="left" valign="middle" class="headline">
								<?php echo $lang['status']; ?>:
							</td>
						</tr>
						<tr>
							<td valign="top" class="statusElementSelected">
								<?php echo $lang['intro']; ?>
							</td>
						</tr>
						<tr>
							<td valign="bottom" class="statusElement">
								<?php echo $lang['step1']; ?>
							</td>
						</tr>
						<tr>
							<td valign="bottom" class="statusElement">
								<?php echo $lang['step2']; ?>
							</td>
						</tr>
						<tr>
							<td valign="bottom" class="statusElement">
								<?php echo $lang['compleate']; ?>
							</td>
						</tr>
					</table>
				</div>
				<div>
				<table style="width:600px; height:300px;" cellspacing="0" cellpadding="2">
					<tr>
						<td align="left" valign="middle" class="headline">
							<?php echo $lang['forumSenaculumInstallation']; ?>:
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" class="body">
							<?php echo $lang['welcomeMessage']; ?><br/>
							<br/>
							<?php
							if(!is_writable("./../conf"))
								echo "<div style=\"color:red;\">".$lang['warningMessageConfFolder']."</div><br/>";
							if(!is_writable("./../images/avatars"))
								echo "<div style=\"color:red;\">".$lang['warningMessageAvatarFolder']."</div><br/>";
							if(!is_writable("./../images/smilies"))
								echo "<div style=\"color:red;\">".$lang['warningMessageSmilieFolder']."</div><br/>";
							if(!is_writable("./../attachments"))
								echo "<div style=\"color:red;\">".$lang['warningMessageAttachmentFolder']."</div><br/>";		
							?>
							<br/>
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="boxtext">
										<?php echo $lang['chooseLanguage']; ?>:
									</td>
									<td class="boxtext">
										<select name="language" class="input" onchange="window.location='install.php?language='+this.options[this.selectedIndex].value;">
										<?php
										$dirHandler = opendir("lang");
										while(($file = readdir($dirHandler)) !== false) {
											if($file != "." && $file != ".." && is_dir("lang/".$file) && file_exists("lang/".$file."/lang.php")) {
												if($file == $language)
													echo "<option value=\"".$file."\" selected=\"selected\">".ucfirst($file)."</option>\n";
												else	
													echo "<option value=\"".$file."\">".ucfirst($file)."</option>\n";
											}
										}
										?>
										</select>
									</td>
								</tr>
							</table>
							<br/>
							<br/>	
							<?php echo $lang['clickNextContinue']; ?>
						</td>
					</tr>
					<tr>
						<td align="right" valign="bottom" class="body">
							<hr/>
							<input type="submit" value="<?php echo $lang['next']; ?> &gt;&gt;" class="button">
						</td>
					</tr>
				</table>
				</div>
			</form>	
		</div>
	</body>
</html>