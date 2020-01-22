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
	
	require_once('classes/errorHandler.php');
	require_once('classes/memberHandler.php');
	require_once('classes/logInOutHandler.php');
	require_once('classes/control.php');
	require_once('classes/dbHandler.php');
	require_once('classes/avatarHandler.php');
	
	$error = new errorHandler;
	$members = new memberHandler;
	$control = new control;
	$auth = new logInOutHandler;
	$db = new dbHandler;
	$avatars = new avatarHandler;
	
	$memberID ="";
			
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
	
	if(isset($_GET['id']) && $forumVariables['adminInlogged'])
	{
		$memberID = $_GET['id'];
		if($memberID == $forumVariables['inloggedMemberID'])
			$myProfile = true;
		else
			$myProfile = false;
	}
	else
	{	
		$memberID = $forumVariables['inloggedMemberID'];
		$myProfile = true;
	}
	
	$member = $members->getOne($memberID, true);

	$errorFirstName = "";
	$errorSureName = "";
	$errorEmail = "";
	$errorPassword = "";
	$errorAvatar = "";
	$errorAvatarFile = "";
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
	$errorSignature = "";
	$errorDateFormat = "";
	
	$userName = "";
	$firstName = "";
	$sureName = "";
	$email = "";
	$showEmail = false;
	$avatar = "";
	$avatarFile = "";
	$website = "";
	$location = "";
	$occupation = "";
	$interests = "";
	$ICQ = "";
	$AIM = "";
	$MSN = "";
	$yahoo = "";
	$language = "";
	$shortName = "";
	$fileName = "";
	$signature = "";
	$dateFormat = "";
	$avatarFake = "";
	$doPublic = false;
	$noAvatar = false;
	$avatarID = "";
	$deleteUser = false;
	$alwaysAllowSmilies = false;
	$alwaysAllowBBCode = false;
	$alwaysNotifyOnReply = false;
	$notifyNewPM = false;
	$alwaysDisplaySign = false;
	
	$admin = false;

	if(!$myProfile && !$forumVariables['superAdminInlogged'] && ($forumVariables['adminInlogged'] && ($memberID==1 || $member['admin'])))
	{
		$error->guide($lang['notLoggedInSuperadmin1'], $lang['notLoggedInSuperadmin2'], true);
	}
	
	if(!isset($_POST['email']))
	{
		$firstName = $member['firstName'];
		$sureName = $member['sureName'];
		$email = $member['email'];
		$showEmail = $member['showEmail'];
		$admin = $member['admin'];
		$website = $member['homepage'];
		$location = $member['location'];
		$occupation = $member['occupation'];
		$interests = $member['interests'];
		$ICQ = $member['ICQ'];
		$AIM = $member['AIM'];
		$MSN = $member['MSN'];
		$yahoo = $member['yahoo'];
		$language = $forumVariables['lang'];
		if(substr($member['avatar'],0,7) == "public/") {
			$avatarFake = substr($member['avatar'],7);
			$sql = "SELECT avatarID FROM _'pfx'_avatars WHERE fileName = '".$db->SQLsecure($avatarFake)."'";
			$result = $db->runSQL($sql);
			if($db->numRows($result) > 0) {
				$row = $db->fetchArray($result);
				$avatarID = $row['avatarID'];
			}
			else
				$avatarFake = "";
		}
		else
			$avatar = $member['avatar'];
		$signature = $member['signature'];
		$dateFormat = $member['dateFormat'];
		$alwaysAllowSmilies = $member['alwaysAllowSmilies'];
		$alwaysAllowBBCode = $member['alwaysAllowBBCode'];
		$alwaysNotifyOnReply = $member['alwaysNotifyOnReply'];
		$notifyNewPM = $member['notifyNewPM'];
		$alwaysDisplaySign = $member['alwaysDisplaySign'];
	}
	else
	{
		$errorFirstName=$control->name($_POST['firstName']);
		$errorSureName=$control->name($_POST['sureName']);
		$errorEmail=$control->email($_POST['email'], $memberID);
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
		if(!empty($_POST['password']) || !empty($_POST['confirmPassword']))
		{
			$errorPassword=$control->password($_POST['password'], $_POST['confirmPassword']);
		}
		if(!empty($_FILES['avatarFile']['name']))
		{
			$errorShortName = $control->text($_POST['shortName'], 0, 50);
			$errorFileName = $control->image($_FILES, "avatarFile", 100000, 100, 100);
		}
		
		if(!empty($errorUserName)||!empty($errorFirstName)||!empty($errorSureName)||!empty($errorEmail)||!empty($errorPassword)||!empty($errorWebsite)||!empty($errorLocation)||!empty($errorOccupation)||!empty($errorInterests)||!empty($errorICQ)||!empty($errorAIM)||!empty($errorMSN)||!empty($errorYahoo)||!empty($errorShortName)||!empty($errorFileName)||!empty($errorSignature)||!empty($errorDateFormat))
		{
			$firstName = $_POST['firstName'];
			$sureName = $_POST['sureName'];
			$email = $_POST['email'];
			if(isset($_POST['showEmail']))
				$showEmail = true;
			else 
				$showEmail = false;
			if(isset($_POST['admin']))
				$admin = $_POST['admin'];
			else 
				$admin = false;
			$website = $_POST['website'];
			$location = $_POST['location'];
			$occupation = $_POST['occupation'];
			$interests = $_POST['interests'];
			$ICQ = $_POST['ICQ'];
			$AIM = $_POST['AIM'];
			$MSN = $_POST['MSN'];
			$yahoo = $_POST['yahoo'];
			$language = $_POST['language'];
			$signature = $_POST['signature'];
			$dateFormat = $_POST['dateFormat'];
			$shortName = $_POST['shortName'];
			if(isset($_FILES['avatarFile']))
			{
				$fileName = $_FILES['avatarFile']['name'];
			}
			$avatar = $_POST['avatar'];
			$avatarID = $_POST['avatarTrue'];
			$avatarFake = $_POST['avatarFake'];
			if(isset($_POST['doPublic']))
				$doPublic = true;
			else
				$doPublic = false;
			if(isset($_POST['noAvatar']))
				$noAvatar = true;
			else
				$noAvatar = false;
			if(isset($_POST['deleteUser']))
				$deleteUser = true;
			else
				$deleteUser = false;		
			if(isset($_POST['alwaysAllowBBCode']))
				$alwaysAllowBBCode = true;
			else
				$alwaysAllowBBCode = false;
			if(isset($_POST['alwaysAllowSmilies']))
				$alwaysAllowSmilies = true;
			else
				$alwaysAllowSmilies = false;
			if(isset($_POST['$alwaysNotifyOnReply']))
				$alwaysNotifyOnReply = true;
			else
				$alwaysNotifyOnReply = false;
			if(isset($_POST['$notifyNewPM']))
				$notifyNewPM = true;
			else
				$notifyNewPM = false;
			if(isset($_POST['$alwaysDisplaySign']))
				$alwaysDisplaySign = true;
			else
				$alwaysDisplaySign = false;	
			/*$sql = "SELECT * FROM _'pfx'_avatars WHERE fileName='".$db->SQLsecure($_POST['avatarTrue'])."'";
			$resultAvatars = $db->runSQL($sql);
			$rowsAvatars = $db->fetchObject($resultAvatars);
			$avatarID = $rowsAvatars->avatarID;*/
			
		}
		else
		{
			if($forumVariables['adminInlogged'] && $member['memberID'] != 1)
			{
				if(isset($_POST['admin']))
				{
					$admin = true;
				}
				else
				{
					$admin = false;
				}
			}
			else
			{
				$admin = $member['admin'];
			}
			
			if(isset($_POST['showEmail']))
				$showEmail = true;
			else 
				$showEmail = false;
			
			/*if(!empty($_POST['avatarTrue']))
			{
				$avatarID = $_POST['avatarTrue'];
			}
			else if(!empty($_FILES['avatarFile']['name']) && !empty($_POST['shortName']))
			{
				$fileName = $avatars->add($_FILES, "avatarFile", $_POST['shortName']);
				$sql = "SELECT avatarID FROM _'pfx'_avatars WHERE fileName = '".$db->SQLsecure($fileName)."'";
				$row = $db->fetchArray($db->runSQL($sql));
				$avatarID = $row['avatarID'];
			}*/
			
			if(!empty($_FILES['avatarFile']['name']) && !isset($_POST['noAvatar'])) {
				if(substr($member['avatar'],0,9) == "personal/")
					@unlink("./images/avatars/".$member['avatar']);
				if(isset($_POST['doPublic']))
					$fileName = "public/".$avatars->add($_FILES, "avatarFile", $_POST['shortName'], false);
				else
					$fileName = "personal/".$avatars->add($_FILES, "avatarFile", $_POST['shortName'], true);	
			}
			elseif(!empty($_POST['avatarTrue']) && !isset($_POST['noAvatar'])){
				if(substr($member['avatar'],0,9) == "personal/")
					@unlink("./images/avatars/".$member['avatar']);
				$avatarID = $_POST['avatarTrue'];
				$fileName = $avatars->getOne($avatarID);
				$fileName = "public/".$fileName['fileName'];
			}
			elseif(!empty($_POST['avatar']))
				$fileName = $_POST['avatar'];
			else {
				$fileName = "";
			}	
			
			if(isset($_POST['noAvatar'])) {
				$fileName = "";
				if(!empty($member['avatar']) && substr($member['avatar'],0,9) == "personal/")
					@unlink("./images/avatars/".$member['avatar']);
			}

			if((isset($_POST['deleteUser']) && $forumSettings['allowDeleteUser'] && $member['memberID'] != 1) || (isset($_POST['deleteUser']) && $forumVariables['adminInlogged'] && $member['memberID'] != 1)) {
				$members->remove($member['memberID']);
				$nextAction = "index.php";
				$error->done($lang['userDeleted1'],$lang['userDeleted2'],$nextAction);
			}	
			elseif($myProfile && !empty($_POST['password']))
			{
				$members->edit($member['memberID'],$member['userName'],$_POST['firstName'],$_POST['sureName'],$_POST['email'],$_POST['password'], $admin, $_POST['website'],$_POST['location'],$_POST['occupation'],$_POST['interests'],$_POST['ICQ'],$_POST['AIM'],$_POST['MSN'],$_POST['yahoo'],$fileName,$_POST['language'],$_POST['signature'],$_POST['dateFormat'],$_POST['alwaysAllowBBCode'],$_POST['alwaysAllowSmilies'],$_POST['alwaysNotifyOnReply'],$_POST['notifyNewPM'],$_POST['alwaysDisplaySign'],$showEmail);
				$auth->logOut();
				$auth->logIn($member['userName'], $_POST['password']);
			}
			elseif(empty($_POST['password']) && empty($_POST['confirmPassword']))
			{
				$members->edit($member['memberID'],$member['userName'],$_POST['firstName'],$_POST['sureName'],$_POST['email'],"", $admin, $_POST['website'],$_POST['location'],$_POST['occupation'],$_POST['interests'],$_POST['ICQ'],$_POST['AIM'],$_POST['MSN'],$_POST['yahoo'],$fileName,$_POST['language'],$_POST['signature'],$_POST['dateFormat'],$_POST['alwaysAllowBBCode'],$_POST['alwaysAllowSmilies'],$_POST['alwaysNotifyOnReply'],$_POST['notifyNewPM'],$_POST['alwaysDisplaySign'],$showEmail);	
			}
			else
			{
				$members->edit($member['memberID'],$member['userName'],$_POST['firstName'],$_POST['sureName'],$_POST['email'],$_POST['password'], $admin, $_POST['website'],$_POST['location'],$_POST['occupation'],$_POST['interests'],$_POST['ICQ'],$_POST['AIM'],$_POST['MSN'],$_POST['yahoo'],$fileName,$_POST['language'],$_POST['signature'],$_POST['dateFormat'],$_POST['alwaysAllowBBCode'],$_POST['alwaysAllowSmilies'],$_POST['alwaysNotifyOnReply'],$_POST['notifyNewPM'],$_POST['alwaysDisplaySign'],$showEmail);
			}
				
			$nextAction = "index.php";
			$error->done($lang['profileEdited1'],$lang['profileEdited2'],$nextAction);
		}
	}
	$title = $lang['editProfileHeading1'].$member['userName'].$lang['editProfileHeading2'];
	$heading = $lang['editProfileHeading1'].$member['userName'].$lang['editProfileHeading2'];
	$help = $lang['editProfileHelp1'].$member['userName'].$lang['editProfileHelp2'];
	
	include("include/guideTop.php");
?>
<script type="text/javascript">
<!--
	function popup(page,width,height) {
		window.open(page,'browser','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+width+', height='+height+', top=100, left=100');
	}
		function upLoad() {
		document.getElementById('avatarFake').value="";
		document.getElementById('avatarTrue').value="";
	}
	
	function deleteConfirm() {
		if(document.getElementById("deleteUser").checked)
			return confirm('<?php echo $lang['doYouWantDeleteUserAccount'] ?>');
		else
			return true;	
	}
//-->	
</script>
<form action="editProfile.php<?php if($forumVariables['adminInlogged']){ echo "?id=".$memberID;} ?>" method="post" id="member" enctype="multipart/form-data"<?php if(($forumSettings['allowDeleteUser'] && $member['memberID'] != 1) || ($forumVariables['adminInlogged'] && $member['memberID'] != 1)) echo " onsubmit=\"return deleteConfirm();\""; ?>>
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['required']; ?>:<br/>
				<table width="100%" cellspacing="0" cellpadding="3" class="guideInputArea">	
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['email']; ?>: <span class="errorText"><?php if(!empty($errorEmail)) echo $errorEmail; else echo "&nbsp;"; ?></span><br/>
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
							<?php echo $lang['password']; ?>: <span class="errorText"><?php if(!empty($errorPassword)) echo $errorPassword; else echo "&nbsp;"; ?></span><br/>
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
					<?php
					if(($forumVariables['adminInlogged'] && $member['memberID'] != 1) || ($forumVariables['inloggedMemberID'] == 1 && $member['memberID'] != 1)) {
					?>	
					<tr>
						<td class="guideInputs">		
							<?php echo $lang['admin']; ?>:<br/>
							<input type="checkbox" name="admin" value="1"<?php if($admin){ echo " checked=\"checked\"";} ?> class="guideTextFields"/>
						</td>
					</tr>
					<?php
					}
					if(($forumSettings['allowDeleteUser'] && $member['memberID'] != 1) || ($forumVariables['adminInlogged'] && $member['memberID'] != 1)) {
					?>
					<tr>
						<td class="guideInputs">		
							<?php echo $lang['deleteUserAccount']; ?>:<br/>
							<input type="checkbox" name="deleteUser" id="deleteUser" value="1"<?php if($deleteUser){ echo " checked=\"checked\"";} ?> class="guideTextFields"/>
						</td>
					</tr>
					<?php
					}
					?>
				</table>
				<br/>
				<?php echo $lang['avatar']; ?>:<br/>
				<table width="100%" cellspacing="0" cellpadding="3" class="guideInputArea">	
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['availableAvatars']; ?>: <span class="errorText"><?php if(!empty($errorAvatar)) echo $errorAvatar; else echo "&nbsp;"; ?></span><br/>
							<input name="avatarFake" id="avatarFake" type="text" size="28" value="<?php echo $avatarFake; ?>" class="guideTextFields"/>
							<input type="button" value="<?php echo $lang['browse']; ?>" class="guideButton" onclick="javascript:popup('avatarBrowser.php',800,600);"/><br/>
							<input name="avatarTrue" id="avatarTrue" type="hidden" value="<?php echo $avatarID; ?>"/>
							<input name="avatar" type="hidden" value="<?php echo $avatar; ?>"/>
						</td>
					</tr>
					<?php
					if(!empty($member['avatar']) && file_exists("./images/avatars/".$member['avatar'])) {
					?>
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['currentAvatar']; ?>:<br/>
							<img src="images/avatars/<?php echo $member['avatar']; ?>" alt="<?php echo $lang['avatar']; ?>"/>
						</td>
					</tr>
					<?php
					}
					if((!empty($avatar) && file_exists("./images/avatars/".$avatar)) || (!empty($avatarFake) && !empty($avatarID))) {
					?>
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['noAvatar']; ?>:<br/>
							<input name="noAvatar" type="checkbox" value="1"<?php if($noAvatar) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>	
					<?php
					}
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
				<br/>
				<?php echo $lang['preferences']; ?>:<br/>
				<table width="100%" cellspacing="0" cellpadding="3" class="guideInputArea">	
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['alwaysAllowBBCode']; ?>:<br/>
							<input name="alwaysAllowBBCode" type="checkbox" value="1"<?php if($alwaysAllowBBCode) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['alwaysAllowSmilies']; ?>:<br/>
							<input name="alwaysAllowSmilies" type="checkbox" value="1"<?php if($alwaysAllowSmilies) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
					<?php
					if($forumSettings['emailActivated']) {
					?>
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['alwaysNotifyMeOfReplies']; ?>:<br/>
							<input name="alwaysNotifyOnReply" type="checkbox" value="1"<?php if($alwaysNotifyOnReply) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['notifyMeOnNewPrivateMessage']; ?>:<br/>
							<input name="notifyNewPM" type="checkbox" value="1"<?php if($notifyNewPM) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td class="guideInputs">	
							<?php echo $lang['alwaysAttachMySignature']; ?>:<br/>
							<input name="alwaysDisplaySign" type="checkbox" value="1"<?php if($alwaysDisplaySign) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
				</table>	
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['optional']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['firstName']; ?>: <span class="errorText"><?php if(!empty($errorFirstName)) echo $errorFirstName; else echo "&nbsp;"; ?></span><br/>
							<input name="firstName" type="text" size="40" value="<?php echo $firstName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>		
					<tr>
						<td class="guideInputs">		
							<?php echo $lang['lastName']; ?>: <span class="errorText"><?php if(!empty($errorSureName)) echo $errorSureName; else echo "&nbsp;"; ?></span><br/>
							<input name="sureName" type="text" size="40" value="<?php echo $sureName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['website']; ?>: <span class="errorText"><?php if(!empty($errorWebsite)) echo $errorWebsite; else echo "&nbsp;"; ?></span><br/>
							<input name="website" type="text" size="40" value="<?php echo $website; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['location']; ?>: <span class="errorText"><?php if(!empty($errorLocation)) echo $errorLocation; else echo "&nbsp;"; ?></span><br/>
							<input name="location" type="text" size="40" value="<?php echo $location; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['occupation']; ?>: <span class="errorText"><?php if(!empty($errorOccupation)) echo $errorOccupation; else echo "&nbsp;"; ?></span><br/>
							<input name="occupation" type="text" size="40" value="<?php echo $occupation; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['interests']; ?>: <span class="errorText"><?php if(!empty($errorInterests)) echo $errorInterests; else echo "&nbsp;"; ?></span><br/>
							<input name="interests" type="text" size="40" value="<?php echo $interests; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['ICQ']; ?>: <span class="errorText"><?php if(!empty($errorICQ)) echo $errorICQ; else echo "&nbsp;"; ?></span><br/>
							<input name="ICQ" type="text" size="40" value="<?php echo $ICQ; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['AIM']; ?>: <span class="errorText"><?php if(!empty($errorAIM)) echo $errorAIM; else echo "&nbsp;"; ?></span><br/>
							<input name="AIM" type="text" size="40" value="<?php echo $AIM; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['MSN']; ?>: <span class="errorText"><?php if(!empty($errorMSN)) echo $errorMSN; else echo "&nbsp;"; ?></span><br/>
							<input name="MSN" type="text" size="40" value="<?php echo $MSN; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['yahoo']; ?>: <span class="errorText"><?php if(!empty($errorYahoo)) echo $errorYahoo; else echo "&nbsp;"; ?></span><br/>
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
							<?php echo $lang['editProfileHelptext']; ?>
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
$nextName = "\"".$lang['edit']." &gt;&gt;\"";

include("include/guideBottom.php");
?>