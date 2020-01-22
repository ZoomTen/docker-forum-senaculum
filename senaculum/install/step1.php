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
	
$server = "localhost";
$username = "";
$password = "";
$name = "forumSenaculum";
$prefix = "sena_";

if(isset($_POST['server'])) {
	if(empty($_POST['server']) || empty($_POST['username']) || empty($_POST['name']) || empty($_POST['prefix'])) {
		$error = $lang['allFieldsMustHaveValueExceptPassword'];
	}
	else {
		$contents = "<?php\n";
		$contents .= "\$dbHost = \"".$_POST['server']."\";\n";
		$contents .= "\$dbUser = \"".$_POST['username']."\";\n";
		$contents .= "\$dbPassword = \"".$_POST['password']."\";\n\n";

		$contents .= "\$dbName = \"".$_POST['name']."\";\n";
		$contents .= "\$dbTablePrefix = \"".$_POST['prefix']."\";\n";
		$contents .= "?>";
		
		if(!$connection = mysql_connect($_POST['server'], $_POST['username'], $_POST['password'])) //Connect to the database
			$error = $lang['couldNotConnectDatabase'];	
		elseif(!mysql_select_db($_POST['name'], $connection))
			$error = $lang['couldNotChooseDatabase'];
		elseif(!file_exists("../conf"))
			$error = $lang['confDirNotExist'];	
		elseif(!is_writable("../conf"))	
			$error2 = $lang['noPermissionSaveConfFile'];	
		else {
			//$_SERVER['DOCUMENT_ROOT'].$_POST['path']."/conf/conf.php
			if($fs = fopen("../conf/conf.php", "w+" )) {
       			fwrite($fs, $contents); 
        		fclose($fs);
				header("location: step2.php?language=".$language);
			}
			else
				$error2 = $lang['unableWriteConfFile'];
		}	
	}
	$server = $_POST['server'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$name = $_POST['name'];
	$prefix = $_POST['prefix'];
}
if(!empty($error2)) {				
	header("Content-type: text/html; charset=iso-8859-1");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php echo $lang['forumSenaculum']; ?> - <?php echo $lang['installation']; ?>: <?php echo $lang['step1Of2']; ?>
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
						<td valign="bottom" class="statusElementSelected">
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
				<form action="step1.php?language=<?php echo $language; ?>" method="post">
					<table style="width:600px; height:300px;" cellspacing="0" cellpadding="2">
						<tr>
							<td align="left" valign="middle" class="headline">
								<?php echo $lang['forumSenaculumInstallation']; ?>: <?php echo $lang['step1Of2']; ?>
							</td>
						</tr>
							<td align="left" valign="top" class="body">
								<div style="color:red;"><?php echo $error2; ?></div><br/>
								<br/>
								<div align="center">
									<?php echo $lang['confFileFixMessage']; ?><br/>
									<textarea class="input" rows="20" cols="70"><?php echo htmlentities($contents)?></textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right" valign="bottom" class="body">
								<hr/>
								<input type="button" value="<?php echo $lang['next']; ?> &gt;&gt;" onclick="window.location='step2.php?language=<?php echo $language; ?>'" class="button"/>
							</td>
						</tr>
					</table> 
				</form>
			</div>
		</div>
	</body>
</html>
<?php
die();					
}
?>
<html>
	<head>
		<title>
			<?php echo $lang['forumSenaculum']; ?> - <?php echo $lang['installation']; ?>: <?php echo $lang['step1Of2']; ?>
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
						<td valign="bottom" class="statusElementSelected">
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
				<form action="step1.php?language=<?php echo $language; ?>" method="post">
					<table style="width:600px; height:300px;" cellspacing="0" cellpadding="2">
						<tr>
							<td align="left" valign="middle" class="headline">
								<?php echo $lang['forumSenaculumInstallation']; ?>: <?php echo $lang['step1Of2']; ?>
							</td>
						</tr>
						<?php
						if(isset($error)) {
						?>
						<tr>
							<td align="left" valign="top" class="body">
								<div style="color:red;"><?php echo $error; ?></div>
							</td>
						</tr>
						<?php
						}	
						?>	
						<tr>
							<td align="left" valign="top" class="body">
								<table width="100%">
									<tr>
										<td class="boxtext">
											<?php echo $lang['databaseServerAddress']; ?>:
										</td>
										<td class="boxtext">
											<input type="text" name="server" value="<?php echo $server; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['databaseUsername']; ?>:
										</td>
										<td class="boxtext">
											<input type="text" name="username" value="<?php echo $username; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['databasePassword']; ?>:
										</td>
										<td class="boxtext">
											<input type="text" name="password" value="<?php echo $password; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['databaseName']; ?>:
										</td>
										<td class="boxtext">
											<input type="text" name="name" value="<?php echo $name; ?>" legnth="40" class="input"/>
										</td>
									</tr>
									<tr>
										<td class="boxtext">
											<?php echo $lang['databaseTablePrefix']; ?>:
										</td>
										<td class="boxtext">
											<input type="text" name="prefix" value="<?php echo $prefix; ?>" legnth="40" class="input"/>
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