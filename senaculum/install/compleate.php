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
			<?php echo $lang['forumSenaculum']; ?> - <?php echo $lang['installationCompleate']; ?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css"/>
	</head>
	<body>
		<div align="center">
			<div style="float:left;">
				<table style="width:150px;" cellspacing="0" cellpadding="2">
					<tr>
						<td align="left" valign="middle" class="headline">
							<?php echo $lang['status']; ?>:
						</td>
					</tr>
					<tr>
						<td valign="top" class="statusElement">
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
						<td valign="bottom" class="statusElementSelected">
							<?php echo $lang['compleate']; ?>
						</td>
					</tr>
				</table>
			</div>
			<div>
				<table style="width:600px; height:300px;" cellspacing="0">
					<tr>
						<td align="left" valign="middle" class="headline">
							<?php echo $lang['forumSenaculumInstallation']; ?>: <?php echo $lang['installationCompleate']; ?>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" class="body">
							<?php echo $lang['compleateMessage']; ?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="bottom" class="body">
							<input type="button" value="<?php echo $lang['goToForum']; ?>" class="button" onClick="window.location='../index.php'"/>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>