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
	
require_once('control.php');

$control = new control;

$error = "";

$forumName = "";
$forumSlogan = "";
$forumDomainName = $_SERVER['SERVER_NAME'];
$forumScriptPath = str_replace("install/step2.php","",$_SERVER['PHP_SELF']);
$languageForm = $language;
$adminUsername = "admin";
$adminFirstName = "";
$adminSureName = "";
$adminEmail = "";
$adminPassword = "";
$adminConfirmPassword = "";

$errorForumName = "";
$errorForumSlogan = "";
$errorForumDomainName = "";
$errorForumScriptPath = "";
$errorAdminUsername = "";
$errorAdminEmail = "";
$errorAdminPassword = "";

if(isset($_POST['forumName'])) {
	$error .= $errorForumName = $control->text($_POST['forumName'],1,255);
	$error .= $errorForumSlogan = $control->text($_POST['forumSlogan'],1,255);
	$error .= $errorForumDomainName = $control->text($_POST['forumDomainName'],1,255);
	$error .= $errorForumScriptPath = $control->text($_POST['forumScriptPath'],1,255);
	$error .= $errorAdminUsername = $control->userName($_POST['adminUsername']);
	$error .= $errorAdminEmail = $control->email($_POST['adminEmail']);
	$error .= $errorAdminPassword = $control->password($_POST['adminPassword'],$_POST['adminConfirmPassword']);
	
	if(!empty($error)) {
		$forumName = $_POST['forumName'];
		$forumSlogan = $_POST['forumSlogan'];
		$forumDomainName = $_POST['forumDomainName'];
		$forumScriptPath = $_POST['forumScriptPath'];
		$languageForm = $_POST['language'];
		$adminUsername = $_POST['adminUsername'];
		$adminFirstName = $_POST['adminFirstName'];
		$adminSureName = $_POST['adminSureName'];
		$adminEmail = $_POST['adminEmail'];
		if(!empty($errorAdminPassword)) {
			$adminPassword = $_POST['adminPassword'];
			$adminConfirmPassword = $_POST['adminConfirmPassword'];
		}
		$error = "";	
	}
	else {
		$error = "";
		require('addDatabase.php');
		header("location: compleate.php?language=".$language);
	}	
}
header("Content-type: text/html; charset=iso-8859-1");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php echo $lang['forumSenaculum']; ?> - <?php echo $lang['installation']; ?>: <?php echo $lang['step2Of2']; ?>
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
						<td valign="bottom" class="statusElementSelected">
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
				<form action="step2.php?language=<?php echo $language; ?>" method="post">
					<table style="width:600px; height:300px;" cellspacing="0" cellpadding="2">
						<tr>
							<td align="left" valign="middle" class="headline">
								<?php echo $lang['forumSenaculumInstallation']; ?>: <?php echo $lang['step2Of2']; ?>
							</td>
						</tr>
						<?php
						if(isset($error)) {
						?>
						<tr>
							<td align="left" valign="top" class="body">
								<font color="red"><?php echo $error; ?></font>
							</td>
						</tr>
						<?php
						}	
						?>
						<tr>
							<td class="body">
								<? if(!isset($_POST['forumName'])) echo $lang['configurationFileCreated']; ?>
							</td>
						</tr>	
						<tr>
							<td align="left" valign="top" class="body">
								<table width="100%">
									<tr>
										<td class="boxtext" width="400">
											<?php echo $lang['forumName']; ?>: <div style="color:red;"><?php echo $errorForumName; ?></div>
										</td>
										<td class="boxtext">
											<input type="text" name="forumName" value="<?php echo $forumName; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['forumSlogan']; ?>: <div style="color:red;"><?php echo $errorForumSlogan; ?></div>
										</td>
										<td class="boxtext">
											<input type="text" name="forumSlogan" value="<?php echo $forumSlogan; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['forumDomainName']; ?>: <div style="color:red;"><?php echo $errorForumDomainName; ?></div>
										</td>
										<td class="boxtext">
											<input type="text" name="forumDomainName" value="<?php echo $forumDomainName; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['forumScriptPath']; ?>: <div style="color:red;"><?php echo $errorForumScriptPath; ?></div>
										</td>
										<td class="boxtext">
											<input type="text" name="forumScriptPath" value="<?php echo $forumScriptPath; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['language']; ?>:
										</td>
										<td class="boxtext">
											<select name="language" class="input">
											<?php
											$dirHandler = opendir("./../lang");
											while(($file = readdir($dirHandler)) !== false) {
												if($file != "." && $file != ".." && is_dir("lang/".$file) && file_exists("./../lang/".$file."/lang.php")) {
													if($file == $languageForm)
														echo "<option value=\"".$file."\" selected=\"selected\">".ucfirst($file)."</option>\n";
													else	
														echo "<option value=\"".$file."\">".ucfirst($file)."</option>\n";
												}
											}
											?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['adminUsername']; ?>: <div style="color:red;"><?php echo $errorAdminUsername; ?></div>
										</td>
										<td class="boxtext">
											<input type="text" name="adminUsername" value="<?php echo $adminUsername; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['adminFirstName']; ?>:
										</td>
										<td class="boxtext">
											<input type="text" name="adminFirstName" value="<?php echo $adminFirstName; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['adminLastName']; ?>:
										</td>
										<td class="boxtext">
											<input type="text" name="adminSureName" value="<?php echo $adminSureName; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['adminEmail']; ?>: <div style="color:red;"><?php echo $errorAdminEmail; ?></div>
										</td>
										<td class="boxtext">
											<input type="text" name="adminEmail" value="<?php echo $adminEmail; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['adminPassword']; ?>: <div style="color:red;"><?php echo $errorAdminPassword; ?></div>
										</td>
										<td class="boxtext">
											<input type="password" name="adminPassword" value="<?php echo $adminPassword; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['adminConfirmPassword']; ?>:
										</td>
										<td class="boxtext">
											<input type="password" name="adminConfirmPassword" value="<?php echo $adminConfirmPassword; ?>" legnth="40" class="input"/>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td align="right" valign="bottom" class="body">
								<hr/>
								<input type="submit" value="<?php echo $lang['next']; ?> &gt;&gt;" class="button"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>