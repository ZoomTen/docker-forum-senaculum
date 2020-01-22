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
	global $forumSettings;
	global $lang;
	
	if($forumVariables['inlogged'])
		header("location: index.php");

	require_once("classes/memberHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/avatarHandler.php");
	require_once("classes/dbHandler.php");
	
	$error = new errorHandler;
	$member = new memberHandler;
	$control = new control;
	$db = new dbHandler;
	$avatars = new avatarHandler;
	
	$member->removeUnactivated();
	
	$errorUserName = "";
	$errorFirstName = "";
	$errorSureName = "";
	$errorEmail = "";
	$errorPassword = "";
	$errorWebsite = "";
	$errorLocation = "";
	$errorOccupation = "";
	$errorInterests = "";
	$errorICQ = "";
	$errorAIM = "";
	$errorMSN = "";
	$errorYahoo = "";
	$errorShortName = "";
	$errorFileName = "";
	$errorAvatar = "";
	$errorSignature = "";
	$errorDateFormat = "";
	
	$userName = "";
	$firstName = "";
	$sureName = "";
	$email = "";
	$showEmail = 1;
	$website = "";
	$location = "";
	$occupation = "";
	$interests = "";
	$ICQ = "";
	$AIM = "";
	$MSN = "";
	$yahoo = "";
	$language = $forumSettings['lang'];
	$shortName = "";
	$fileName = "";
	$avatarID = "";
	$avatar = "";
	$signature = "";
	$dateFormat = $forumSettings['dateFormat'];
	$doPublic = false;
	
	if(isset($_POST['userName']))
	{
		$errorUserName=$control->userName($_POST['userName']);
		$errorFirstName=$control->name($_POST['firstName']);
		$errorSureName=$control->name($_POST['sureName']);
		$errorEmail=$control->email($_POST['email']);
		$errorPassword=$control->password($_POST['password'], $_POST['confirmPassword']);
		$errorWebsite=$control->website($_POST['website']);
		$errorLocation=$control->text($_POST['location'], 0, 50);
		$errorOccupation=$control->text($_POST['occupation'], 0, 50);
		$errorInterests=$control->text($_POST['interests'], 0, 50);
		$errorICQ=$control->ICQ($_POST['ICQ']);
		$errorAIM=$control->AIM($_POST['AIM']);
		$errorMSN=$control->MSN($_POST['MSN']);
		$errorYahoo=$control->yahoo($_POST['yahoo']);
		$errorSignature=$control->text($_POST['signature'], 0, 255);
		$errorDateFormat=$control->text($_POST['dateFormat'],1,20);
		
		if(!empty($_FILES['avatarFile']['name']))
		{
			$errorShortName = $control->text($_POST['shortName'], 0, 50);
			$errorFileName = $control->image($_FILES, "avatarFile", 100000, 100, 100);
		}
		
		if(!empty($errorUserName)||!empty($errorFirstName)||!empty($errorSureName)||!empty($errorEmail)||!empty($errorPassword)||!empty($errorWebsite)||!empty($errorLocation)||!empty($errorOccupation)||!empty($errorInterests)||!empty($errorICQ)||!empty($errorAIM)||!empty($errorMSN)||!empty($errorYahoo)||!empty($errorShortName)||!empty($errorFileName)||!empty($errorDateFormat))
		{
			$userName = $_POST['userName'];
			$firstName = $_POST['firstName'];
			$sureName = $_POST['sureName'];
			$email = $_POST['email'];
			if(isset($_POST['showEmail']))
				$showEmail = true;
			else
				$showEmail = false;	
			$website = $_POST['website'];
			$location = $_POST['location'];
			$occupation = $_POST['occupation'];
			$interests = $_POST['interests'];
			$ICQ = $_POST['ICQ'];
			$AIM = $_POST['AIM'];
			$MSN = $_POST['MSN'];
			$yahoo = $_POST['yahoo'];
			$signature = $_POST['signature'];
			$dateFormat = $_POST['dateFormat'];
			$language = $_POST['language'];
			$shortName = $_POST['shortName'];
			if(isset($_FILES['avatarFile']))
			{
				$fileName = $_FILES['avatarFile']['name'];
			}
			$avatar = $_POST['avatarFake'];
			$avatarID = $_POST['avatarTrue'];
			if(isset($_POST['doPublic']))
				$doPublic = true;
			else
				$doPublic = false;	
			/*$sql = "SELECT * FROM _'pfx'_avatars WHERE fileName='".$db->SQLsecure($_POST['avatarTrue'])."'";
			$resultAvatars = $db->runSQL($sql);
			$rowsAvatars = $db->fetchObject($resultAvatars);
			$avatarID = $rowsAvatars->avatarID;*/
		}
		if(empty($errorUserName)&&empty($errorFirstName)&&empty($errorSureName)&&empty($errorEmail)&&empty($errorPassword)&&empty($errorWebsite)&&empty($errorLocation)&&empty($errorOccupation)&&empty($errorInterests)&&empty($errorICQ)&&empty($errorAIM)&&empty($errorMSN)&&empty($errorYahoo)&&empty($errorShortName)&&empty($errorFileName)&&empty($errorSignature)&&empty($errorDateFormat))
		{
			if(isset($_POST['showEmail']))
				$showEmail = true;
			else
				$showEmail = false;	
				
			if(!empty($_FILES['avatarFile']['name'])) {
				if(isset($_POST['doPublic']))
					$fileName = "public/".$avatars->add($_FILES, "avatarFile", $_POST['shortName'], false);
				else
					$fileName = "personal/".$avatars->add($_FILES, "avatarFile", $_POST['shortName'], true);
			}
			elseif(!empty($_POST['avatarTrue'])){
				$avatarID = $_POST['avatarTrue'];
				$fileName = $avatars->getOne($avatarID);
				$fileName = "public/".$fileName['fileName'];
			}
			else
				$fileName = "";
			/*else if(!empty($_FILES['avatarFile']['name']) && !empty($_POST['shortName']))
			{
				$fileName = $avatars->add($_FILES, "avatarFile", $_POST['shortName']);
				$sql = "SELECT avatarID FROM _'pfx'_avatars WHERE fileName = '".$db->SQLsecure($fileName)."'";
				$row = $db->fetchArray($db->runSQL($sql));
				$avatarID = $row['avatarID'];
			}
			else if(empty($avatarID))
			{
				$avatarID=1;
			}*/
			if($forumSettings['validateEmail'] && $forumSettings['emailActivated']) {
				require_once("classes/mail.php");
				$mail = new mail;
				
				srand((double)microtime()*1000000);
				$code = md5(rand(0,999999999));
				
				$subject = $lang['emailActivateUserSubject1'].$forumSettings['forumName'];
				$message = $lang['emailActivateUserMessage1'].$_POST['userName'].$lang['emailActivateUserMessage2'].$forumSettings['forumName'].$lang['emailActivateUserMessage3']."http://".$forumSettings['forumDomainName'].$forumSettings['forumScriptPath']."activateUser.php?user=".$_POST['userName']."&code=".$code.$lang['emailActivateUserMessage4'];
				if(!($errorMessage = $mail->send($_POST['email'],$subject,$message))) {
					$member->add($_POST['userName'],$_POST['firstName'],$_POST['sureName'],$_POST['email'],$_POST['password'],$_POST['website'],$_POST['location'],$_POST['occupation'],$_POST['interests'],$_POST['ICQ'],$_POST['AIM'],$_POST['MSN'],$_POST['yahoo'],$fileName,$_POST['language'],$_POST['signature'],$_POST['dateFormat'],0,$code,$showEmail);
					$nextAction = "index.php";
					$error->done($lang['memberAddedValidateEmail1'],$lang['memberAddedValidateEmail2'],$nextAction);
				}	
				else
					$error->error($lang['mailError'],$errorMessage);	
			}
			$member->add($_POST['userName'],$_POST['firstName'],$_POST['sureName'],$_POST['email'],$_POST['password'],$_POST['website'],$_POST['location'],$_POST['occupation'],$_POST['interests'],$_POST['ICQ'],$_POST['AIM'],$_POST['MSN'],$_POST['yahoo'],$fileName,$_POST['language'],$_POST['signature'],$_POST['dateFormat'],1,"",$showEmail);
			$nextAction = "index.php";
			$error->done($lang['memberAdded1'],$lang['memberAdded2'],$nextAction);
		}
	}
	$title = $lang['registerNewMember'];
	$heading = $lang['registerNewMember'];
	$help = $lang['addMemberHelp'];
	
	include("include/guideTop.php");
?>
<script type='text/javascript'>
	function popup(page,width,height) {
		window.open(page,'browser','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+width+', height='+height+', top=100, left=100');
	}
</script>
<form action="addMember.php" method="post" id="member" enctype="multipart/form-data">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['required']; ?>:<br/>
				<table width="100%" cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['username']; ?>: <span class="errorText"><?php if(!empty($errorUserName))echo $errorUserName; else echo "&nbsp;"; ?></span><br/>
							<input name="userName" type="text" size="40" value="<?php echo $userName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['email']; ?>: <span class="errorText"><?php if(!empty($errorEmail))echo $errorEmail; else echo "&nbsp;"; ?></span><br/>
							<input name="email" type="text" size="40" value="<?php echo $email; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['showEmail']; ?>:<br/>
							<input name="showEmail" type="checkbox" value="1"<?php if($showEmail) echo " checked=\"checked\""; ?>/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['password']; ?>: <span class="errorText"><?php if(!empty($errorPassword))echo $errorPassword; else echo "&nbsp;"; ?></span><br/>
							<input name="password" type="password" size="40" class="guideTextFields"/><br/>
						</td>
					</tr>		
					<tr>
						<td class="guideInputs">
							<?php echo $lang['confirmPassword']; ?>:<br/>
							<input name="confirmPassword" type="password" size="40" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['language']; ?>:<br/>
							<select name="language" class="guideTextFields">
								<?php
								$dirHandler = opendir("lang");
								while(($file = readdir($dirHandler)) !== false) {
									if($file != "." && $file != ".." && is_dir("lang/".$file) && file_exists("lang/".$file."/lang.php")) {
										if($language == $file)
											echo "<option value=\"".$file."\" selected=\"selected\">".ucfirst($file)."</option>\n";
										else	
											echo "<option value=\"".$file."\">".ucfirst($file)."</option>\n";
									}
								}
								closedir($dirHandler);
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">	
							<script type="text/javascript">
							<!--
								function openCloseDFDropDown() {
									if(document.getElementById('dateDropdown').style.display == 'none')
										document.getElementById('dateDropdown').style.display = 'block';
									else
										document.getElementById('dateDropdown').style.display = 'none';	
								}
								function DFSelect(format){
									document.getElementById('dateFormat').value = format;
									document.getElementById('dateDropdown').style.display= 'none';
								}
							//-->
							</script>
							<?php echo $lang['dateFormat']; ?>: <span class="errorText"><?php if(!empty($errorDateFormat)) echo $errorDateFormat; else echo "&nbsp;"; ?></span><br/>
							<input name="dateFormat" id="dateFormat" type="text" style="width:200px;" value="<?php echo $dateFormat; ?>" onclick="document.getElementById('dateDropdown').style.display='none';" class="guideTextFields"/><input type="button" value="<?php echo $lang['select']; ?>" onclick="openCloseDFDropDown();"/ class="guideButton">
							<div id="dateDropdown" class="profileSelectDateFormat">
								<div class="profileSelectDateFormatElement" onclick="DFSelect('Y-m-d H:i:s');" onmouseover="this.className='profileSelectDateFormatElementOver';" onmouseout="this.className='profileSelectDateFormatElement';">
									<?php echo date("Y-m-d H:i:s"); ?>
								</div>
								<div class="profileSelectDateFormatElement" onclick="DFSelect('Y-m-d(H:i)');" onmouseover="this.className='profileSelectDateFormatElementOver';" onmouseout="this.className='profileSelectDateFormatElement';">
									<?php echo date("Y-m-d (H:i)"); ?>
								</div>
								<div class="profileSelectDateFormatElement" onclick="DFSelect('M j Y G:i:s');" onmouseover="this.className='profileSelectDateFormatElementOver';" onmouseout="this.className='profileSelectDateFormatElement';">
									<?php echo date("M j Y G:i:s"); ?>
								</div>
							</div>
						</td>
					</tr>	
				</table>
				<br/>
				<?php echo $lang['avatar']; ?>:<br/>
				<table width="100%" cellspacing="0" cellpadding="3" class="guideInputArea">	
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['availableAvatars']; ?>: <span class="errorText"><?php if(!empty($errorAvatar))echo $errorAvatar; else echo "&nbsp;"; ?></span><br/>
							<input name="avatarFake" type="text" size="28" value="<?php echo $avatar; ?>" class="guideTextFields" readonly="readonly"/>
							<input type="button" value="<?php echo $lang['browse']; ?>" class="guideButton" onclick="javascript:popup('avatarBrowser.php',800,600);"/><br/>
							<input name="avatarTrue" type="hidden" size="28" value="<?php echo $avatarID; ?>" class="guideTextFields"/>
						</td>
					</tr>
					<?php
					if($forumSettings['memberUploadAvatars']) {
					?>
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['uploadAvatar']; ?>: <span class="errorText"><?php if(!empty($errorFileName))echo $errorFileName; else echo "&nbsp;"; ?></span><br/>
							<input name="avatarFile" type="file" size="27" class="guideButton" value="<?php echo $fileName; ?>"/>
						</td>
					</tr>	
					<?php
						if($forumSettings['memberUploadPublicAvatars']) {
					?>
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['doUploadedAvatarPublic']; ?>:<br/>
							<input name="doPublic" type="checkbox" value="1"<?php if($doPublic) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>	
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['nameForUploadedAvatar']; ?>: <span class="errorText"><?php if(!empty($errorShortName))echo $errorShortName; else echo "&nbsp;"; ?></span><br/>
							<input name="shortName" type="text" size="28" value="<?php echo $shortName; ?>" class="guideTextFields"/>
						</td>
					</tr>
					<?php
						}
					}
					?>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['optional']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['firstName']; ?>: <span class="errorText"><?php if(!empty($errorFirstName))echo $errorFirstName; else echo "&nbsp;"; ?></span><br/>
							<input name="firstName" type="text" size="40" value="<?php echo $firstName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['lastName']; ?>: <span class="errorText"><?php if(!empty($errorSureName))echo $errorSureName; else echo "&nbsp;"; ?></span><br/>
							<input name="sureName" type="text" size="40" value="<?php echo $sureName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['website']; ?>: <span class="errorText"><?php if(!empty($errorWebsite))echo $errorWebsite; else echo "&nbsp;"; ?></span><br/>
							<input name="website" type="text" size="40" value="<?php echo $website; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['location']; ?>: <span class="errorText"><?php if(!empty($errorLocation))echo $errorLocation; else echo "&nbsp;"; ?></span><br/>
							<input name="location" type="text" size="40" value="<?php echo $location; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['occupation']; ?>: <span class="errorText"><?php if(!empty($errorOccupation))echo $errorOccupation; else echo "&nbsp;"; ?></span><br/>
							<input name="occupation" type="text" size="40" value="<?php echo $occupation; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['interests']; ?>: <span class="errorText"><?php if(!empty($errorInterests))echo $errorInterests; else echo "&nbsp;"; ?></span><br/>
							<input name="interests" type="text" size="40" value="<?php echo $interests; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['ICQ']; ?>: <span class="errorText"><?php if(!empty($errorICQ))echo $errorICQ; else echo "&nbsp;"; ?></span><br/>
							<input name="ICQ" type="text" size="40" value="<?php echo $ICQ; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['AIM']; ?>: <span class="errorText"><?php if(!empty($errorAIM))echo $errorAIM; else echo "&nbsp;"; ?></span><br/>
							<input name="AIM" type="text" size="40" value="<?php echo $AIM; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['MSN']; ?>: <span class="errorText"><?php if(!empty($errorMSN))echo $errorMSN; else echo "&nbsp;"; ?></span><br/>
							<input name="MSN" type="text" size="40" value="<?php echo $MSN; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['yahoo']; ?>: <span class="errorText"><?php if(!empty($errorYahoo))echo $errorYahoo; else echo "&nbsp;"; ?></span><br/>
							<input name="yahoo" type="text" size="40" value="<?php echo $yahoo; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['signature']; ?>: <span class="errorText"><?php if(!empty($errorSignature)) echo $errorSignature; else echo "&nbsp;"; ?></span><br/>
							<textarea name="signature" cols="30" rows="3" class="guideTextFields"><?php echo $signature; ?></textarea>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['addMemberHelptext']; ?>
						</td>
					</tr>
				</table>
			</td>		
		</tr>
	</table>
	<br/>
	<br/>
	<br/>
<?php
$backAction = "\"self.close();\"";
$backName = "\"&lt;&lt; ".$lang['close']."\"";
$nextName = "\"".$lang['register']." &gt;&gt;\"";

include("include/guideBottom.php");
?>