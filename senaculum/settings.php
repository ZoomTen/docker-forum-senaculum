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

require_once('classes/errorHandler.php');
require_once('classes/menuHandler.php');
require_once('classes/control.php');

$error = new errorHandler;
$menu = new menuHandler;
$control = new control;

$menu->getTop();

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
	
$errorName = "";
$errorSlogan= "";
$errorForumDomainName = "";
$errorForumScriptPath = "";
$errorPostsPerPage = "";
$errorThreadsPerPage = "";
$errorMembersPerPage = "";
$errorDateFormat = "";
$errorNumPolls = "";
$errorSmtpHost = "";
$errorSmtpUsername = "";
$errorSmtpPassword = "";
$errorMaxAttachmentUploadSize = "";
$errorMaxNumberOfAttachments = "";
$errorAllowedDisallowedAttachmentExtensions = "";
$errorDisallowedAttachmentExtensionsAddThis = "";
$errorPostTimeLimit = "";
$errorOnlineViewExpire = "";

$name = "";
$slogan = "";
$forumDomainName = "";
$forumScriptPath = "";
$language = "";
$popup = true;
$postsPerPage = "";
$threadsPerPage = "";
$membersPerPage = "";
$dateFormat = "";
$numPolls = "";
$emailActivated = 0;
$adminEmail = "";
$smtpHost = "";
$smtpUsername = "";
$smtpPassword = "";
$useSmpt = 0;
$validateEmail = 0;
$attachmentsActivated = 0;
$maxAttachmentUploadSize = "";
$maxNumberOfAttachments = "";
$checkAllowedDisallowedAttachmentExtensions = 0;
$allowedDisallowedAttachmentExtensions = "";
$disallowedAttachmentExtensionsAdd = 0;
$disallowedAttachmentExtensionsAddThis = "";
$smartNewPosts = 0;
$markThreadsWithOwnPosts = 0;
$memberUploadAvatars = 0;
$memberUploadPublicAvatars = 0;
$postTimeLimit = 0;
$allowDeleteUser = 0;
$viewPostRepliesCount = 0;
$activateOnline = 0;
$onlineViewExpire = 0;
	
if(!isset($_POST['name']))
{
	$name = $forumSettings['forumName'];
	$slogan = $forumSettings['forumSlogan'];
	$forumDomainName = $forumSettings['forumDomainName'];
	$forumScriptPath = $forumSettings['forumScriptPath'];
	
	if(file_exists("lang/".$forumSettings['lang']."/lang.php"))
		$language = $forumSettings['lang'];
	else
		$language = "default";	
	
	$popup = $forumSettings['guidesInPopups'];
	$postsPerPage = $forumSettings['postsPerPage'];
	$threadsPerPage = $forumSettings['threadsPerPage'];
	$membersPerPage = $forumSettings['membersPerPage'];
	$dateFormat = $forumSettings['dateFormat'];
	$numPolls = $forumSettings['numPolls'];
	$emailActivated = $forumSettings['emailActivated'];
	$adminEmail = $forumSettings['adminEmail'];
	$smtpHost = $forumSettings['smtpHost'];
	$smtpUsername = $forumSettings['smtpUsername'];
	$smtpPassword = $forumSettings['smtpPassword'];
	$useSmtp = $forumSettings['useSmtp'];
	$validateEmail = $forumSettings['validateEmail'];
	$attachmentsActivated = $forumSettings['attachmentsActivated'];
	$maxAttachmentUploadSize = $forumSettings['maxAttachmentUploadSize'];
	$maxNumberOfAttachments = $forumSettings['maxNumberOfAttachments'];
	$checkAllowedDisallowedAttachmentExtensions = $forumSettings['checkAllowedDisallowedAttachmentExtensions'];
	$allowedDisallowedAttachmentExtensions = $forumSettings['allowedDisallowedAttachmentExtensions'];
	$disallowedAttachmentExtensionsAdd = $forumSettings['disallowedAttachmentExtensionsAdd'];
	$disallowedAttachmentExtensionsAddThis = $forumSettings['disallowedAttachmentExtensionsAddThis'];
	$smartNewPosts = $forumSettings['smartNewPosts'];
	$markThreadsWithOwnPosts = $forumSettings['markThreadsWithOwnPosts'];
	$memberUploadAvatars = $forumSettings['memberUploadAvatars'];
	$memberUploadPublicAvatars = $forumSettings['memberUploadPublicAvatars'];
	$postTimeLimit = $forumSettings['postTimeLimit'];
	$allowDeleteUser = $forumSettings['allowDeleteUser'];
	$viewPostRepliesCount = $forumSettings['viewPostRepliesCount'];
	$activateOnline = $forumSettings['activateOnline'];
	$onlineViewExpire = $forumSettings['onlineViewExpire'];
}
else
{
	require_once('classes/settingHandler.php');
	$settings = new settingHandler;
	
	$forumDomainName = $_POST['forumDomainName'];
	if(substr($forumDomainName,-1) == "/")
		$forumDomainName = substr($forumDomainName,0,-1);
	if(substr($forumDomainName,0,1) == "/")
		$forumDomainName = substr($forumDomainName,1);
		
	$forumScriptPath = $_POST['forumScriptPath'];
	if(substr($forumScriptPath,-1) != "/")
		$forumScriptPath = $forumScriptPath."/";
	if(substr($forumScriptPath,0,1) != "/")
		$forumScriptPath = "/".$forumScriptPath;	
	
	$errorName=$control->text($_POST['name'],1,255);
	$errorSlogan=$control->text($_POST['slogan'],1,255);
	$errorForumDomainName=$control->text($forumDomainName,1,255);
	$errorForumScriptPath=$control->text($forumScriptPath,0,255);
	$errorPostsPerPage=$control->numValue($_POST['postsPerPage'],true,true);
	$errorThreadsPerPage=$control->numValue($_POST['threadsPerPage'],true,true);
	$errorMembersPerPage=$control->numValue($_POST['membersPerPage'],true,true);
	$errorDateFormat=$control->text($_POST['dateFormat'],1,20);
	$errorNumPolls=$control->text($_POST['numPolls'],0,4);
	$errorNumPolls=$control->numValue($_POST['numPolls'], true, true);
	$errorAdminEmail=$control->checkMail($_POST['adminEmail']);
	$errorAdminEmail=$control->text($_POST['adminEmail'],0,255);
	$errorSmtpHost=$control->text($_POST['smtpHost'],0,255);
	$errorSmtpUsername=$control->text($_POST['smtpUsername'],0,255);
	$errorSmtpPassword=$control->text($_POST['smtpPassword'],0,255);
	$errorMaxAttachmentUploadSize=$control->numValue($_POST['maxAttachmentUploadSize'],true,true);
	$errorMaxNumberOfAttachments=$control->numValue($_POST['maxNumberOfAttachments'],true,true);
	$errorAllowedDisallowedAttachmentExtensions=$control->text($forumSettings['allowedDisallowedAttachmentExtensions'],0,255);
	$errorDisallowedAttachmentExtensionsAddThis=$control->text($forumSettings['disallowedAttachmentExtensionsAddThis'],0,20);
	$errorPostTimeLimit=$control->numValue($_POST['postTimeLimit'],true,false);
	$errorOnlineViewExpire=$control->numValue($_POST['onlineViewExpire'],true,true);
	
	$name = $_POST['name'];
	$slogan = $_POST['slogan'];
	$language = $_POST['language'];
	if(!empty($_POST['popup']))
		$popup = $_POST['popup'];
	else
		$popup = false;
	$postsPerPage = $_POST['postsPerPage'];
	$threadsPerPage = $_POST['threadsPerPage'];	
	$membersPerPage	= $_POST['membersPerPage'];	
	$dateFormat = $_POST['dateFormat'];
	$numPolls = $_POST['numPolls'];
	if(!empty($_POST['emailActivated']))
		$emailActivated = 1;
	else
		$emailActivated = 0;	
	$adminEmail = $_POST['adminEmail'];
	$smtpHost = $_POST['smtpHost'];
	$smtpUsername = $_POST['smtpUsername'];
	$smtpPassword = $_POST['smtpPassword'];
	if(!empty($_POST['useSmtp']))
		$useSmtp = 1;
	else
		$useSmtp = 0;	
	if(!empty($_POST['validateEmail']))
		$validateEmail = 1;
	else
		$validateEmail = 0;		
	if(!empty($_POST['attachmentsActivated']))
		$attachmentsActivated = 1;
	else
		$attachmentsActivated = 0;	
	$maxAttachmentUploadSize = $_POST['maxAttachmentUploadSize'];		
	$maxNumberOfAttachments = $_POST['maxNumberOfAttachments'];
	if(!empty($_POST['checkAllowedDisallowedAttachmentExtensions']))
		$checkAllowedDisallowedAttachmentExtensions = 1;
	else
		$checkAllowedDisallowedAttachmentExtensions = 0;	
	$allowedDisallowedAttachmentExtensions = $_POST['allowedDisallowedAttachmentExtensions'];
	if(!empty($_POST['disallowedAttachmentExtensionsAdd']))
		$disallowedAttachmentExtensionsAdd = 1;
	else
		$disallowedAttachmentExtensionsAdd = 0;
	$disallowedAttachmentExtensionsAddThis = $_POST['disallowedAttachmentExtensionsAddThis'];
	if(!empty($_POST['smartNewPosts']))
		$smartNewPosts = 1;
	else
		$smartNewPosts = 0;
	if(!empty($_POST['markThreadsWithOwnPosts']))
		$markThreadsWithOwnPosts = 1;
	else
		$markThreadsWithOwnPosts = 0;	
	if(!empty($_POST['memberUploadAvatars']))
		$memberUploadAvatars = 1;
	else
		$memberUploadAvatars = 0;	
	if(!empty($_POST['memberUploadPublicAvatars']))
		$memberUploadPublicAvatars = 1;
	else
		$memberUploadPublicAvatars = 0;
	$postTimeLimit = $_POST['postTimeLimit'];	
	if(!empty($_POST['allowDeleteUser']))
		$allowDeleteUser = 1;
	else
		$allowDeleteUser = 0;
	if(!empty($_POST['viewPostRepliesCount']))
		$viewPostRepliesCount = 1;
	else
		$viewPostRepliesCount = 0;
	if(!empty($_POST['activateOnline']))
		$activateOnline = 1;
	else
		$activateOnline = 0;
	$onlineViewExpire = $_POST['onlineViewExpire'];
		
		
	if(empty($errorName)&&empty($errorSlogan)&&empty($errorPostsPerPage)&&empty($errorThreadsPerPage)&&empty($errorMembersPerPage)&&empty($errorDateFormat)&&empty($errorNumPolls)&&empty($errorAdminEmail)&&empty($errorSmtpHost)&&empty($errorSmtpUsername)&&empty($errorSmtpPassword)&&empty($errorMaxAttachmentUploadSize)&&empty($errorMaxNumberOfAttachments)&&empty($errorAllowedDisallowedAttachmentExtensions)&&empty($errorDisallowedAttachmentExtensionsAddThis)&&empty($errorPostTimeLimit)&&empty($errorOnlineViewExpire))
	{
		$settings->edit("forumName",$name);
		$settings->edit("forumSlogan",$slogan);
		$settings->edit("forumDomainName",$forumDomainName);
		$settings->edit("forumScriptPath",$forumScriptPath);
		$settings->edit("lang",$language);
		if($popup)
			$settings->edit("guidesInPopups",1);
		else
			$settings->edit("guidesInPopups",0);
		$settings->edit("postsPerPage",$postsPerPage);
		$settings->edit("threadsPerPage",$threadsPerPage);		
		$settings->edit("membersPerPage",$membersPerPage);	
		$settings->edit("dateFormat",$dateFormat);
		$settings->edit("numPolls",$numPolls);
		$settings->edit("emailActivated",$emailActivated);
		$settings->edit("adminEmail",$adminEmail);
		$settings->edit("smtpHost",$smtpHost);
		$settings->edit("smtpUsername",$smtpUsername);
		$settings->edit("smtpPassword",$smtpPassword);
		$settings->edit("useSmtp",$useSmtp);
		$settings->edit("validateEmail",$validateEmail);
		$settings->edit("attachmentsActivated",$attachmentsActivated);
		$settings->edit("maxAttachmentUploadSize",$maxAttachmentUploadSize);
		$settings->edit("maxNumberOfAttachments",$maxNumberOfAttachments);
		$settings->edit("checkAllowedDisallowedAttachmentExtensions",$checkAllowedDisallowedAttachmentExtensions);
		$settings->edit("allowedDisallowedAttachmentExtensions",$allowedDisallowedAttachmentExtensions);
		$settings->edit("disallowedAttachmentExtensionsAdd",$disallowedAttachmentExtensionsAdd);
		$settings->edit("disallowedAttachmentExtensionsAddThis",$disallowedAttachmentExtensionsAddThis);
		$settings->edit("smartNewPosts",$smartNewPosts);
		$settings->edit("markThreadsWithOwnPosts",$markThreadsWithOwnPosts);
		$settings->edit("memberUploadAvatars",$memberUploadAvatars);
		$settings->edit("memberUploadPublicAvatars",$memberUploadPublicAvatars);
		$settings->edit("postTimeLimit",$postTimeLimit);
		$settings->edit("allowDeleteUser",$allowDeleteUser);
		$settings->edit("viewPostRepliesCount",$viewPostRepliesCount);
		$settings->edit("activateOnline",$activateOnline);
		$settings->edit("onlineViewExpire",$onlineViewExpire);
			
		?>
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr>
				<td class="profileHeading">
					<?php echo $lang['settings']; ?>:
				</td>			
			</tr>	
			<tr>
				<td class="profileUsernameHeading">
					<?php echo $lang['settingsForForum']; ?>:
				</td>
			</tr>
			<tr>
				<td class="profileInfoArea">
					<?php echo $lang['settingsChanged']; ?>
				</td>
			</tr>
		</table>
		<?php
		$menu->getBottom();
		die();
	}
}

?>
<form action="settings.php" method="post">
	<table width="100%" cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td class="profileHeading">
				<?php echo $lang['settings']; ?>:
			</td>			
		</tr>	
		<tr>
			<td class="profileUsernameHeading">
				<?php echo $lang['settingsForForum']; ?>:
			</td>
		</tr>
		<tr>
			<td class="profileInfoArea">
				<table cellpadding="0" cellspacing="10">
					<tr>
						<td align="left" valign="top" class="guideBoxHeading">
							<?php echo $lang['general']; ?>:<br/>
							<table cellspacing="0" cellpadding="3" class="guideInputArea">
								<tr>
									<td class="guideInputs">
										<?php echo $lang['name']; ?>:<br/>
										<input name="name" type="text" size="40" value="<?php echo $name; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorName)) echo $errorName; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['slogan']; ?>:<br/>
										<input name="slogan" type="text" size="40" value="<?php echo $slogan; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorSlogan)) echo $errorSlogan; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['domainName']; ?>:<br/>
										<input name="forumDomainName" type="text" size="40" value="<?php echo $forumDomainName; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorForumDomainName)) echo $errorForumDomainName; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['scriptPath']; ?>:<br/>
										<input name="forumScriptPath" type="text" size="40" value="<?php echo $forumScriptPath; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorForumScriptPath)) echo $errorForumScriptPath; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['defaultLanguage']; ?>:<br/>
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
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['guidesInPopups']; ?>:<br/>
										<input name="popup" type="checkbox" class="guideTextFields"<?php if($popup) echo " checked=\"checked\""; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['postsPerPage']; ?>:<br/>
										<input name="postsPerPage" type="text" size="4" value="<?php echo $postsPerPage; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorPostsPerPage)) echo $errorPostsPerPage; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['threadsPerPage']; ?>:<br/>
										<input name="threadsPerPage" type="text" size="4" value="<?php echo $threadsPerPage; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorThreadsPerPage)) echo $errorThreadsPerPage; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['membersPerPage']; ?>:<br/>
										<input name="membersPerPage" type="text" size="4" value="<?php echo $membersPerPage; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorMembersPerPage)) echo $errorMembersPerPage; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['maxNumPollOptions']; ?>:<br/>
										<input name="numPolls" type="text" size="4" value="<?php echo $numPolls; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorNumPolls)) echo $errorNumPolls; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['oftenPostInSecounds']; ?>:<br/>
										<input name="postTimeLimit" type="text" size="4" value="<?php echo $postTimeLimit; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorPostTimeLimit)) echo $errorPostTimeLimit; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['dateFormat']; ?>:<br/>
										<input name="dateFormat" type="text" size="40" value="<?php echo $dateFormat; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorDateFormat)) echo $errorDateFormat; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['activateSmartNewPosts']; ?>:<br/>
										<input name="smartNewPosts" type="checkbox" value="1" <?php if($smartNewPosts) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['activateMarkThreadsWithOwnPostsIn']; ?>:<br/>
										<input name="markThreadsWithOwnPosts" type="checkbox" value="1" <?php if($markThreadsWithOwnPosts) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['viewNewPostReplyCount']; ?>:<br/>
										<input name="viewPostRepliesCount" type="checkbox" value="1" <?php if($viewPostRepliesCount) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['viewWhichUsersOnline']; ?>:<br/>
										<input name="activateOnline" type="checkbox" value="1" <?php if($activateOnline) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['howManySecondsLastActiveOnline']; ?>:<br/>
										<input name="onlineViewExpire" type="text" size="4" value="<?php echo $onlineViewExpire; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorOnlineViewExpire)) echo $errorOnlineViewExpire; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['allowUsersOwnUserAccount']; ?>:<br/>
										<input name="allowDeleteUser" type="checkbox" value="1" <?php if($allowDeleteUser) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
							</table>
						</td>
					</tr>		
				</table>
			</td>
		</tr>
		<tr>
			<td class="profileInfoArea">
				<table cellpadding="0" cellspacing="10">
					<tr>
						<td align="left" valign="top" class="guideBoxHeading">
							<?php echo $lang['avatars']; ?>:<br/>
							<table cellspacing="0" cellpadding="3" class="guideInputArea">
								<tr>
									<td class="guideInputs">
										<?php echo $lang['allowUsersUploadAvatars']; ?>:<br/>
										<input name="memberUploadAvatars" type="checkbox" value="1" <?php if($memberUploadAvatars) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['allowUsersUploadPublicAvatars']; ?>:<br/>
										<input name="memberUploadPublicAvatars" type="checkbox" value="1" <?php if($memberUploadPublicAvatars) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>					
		</tr>
		<tr>
			<td class="profileInfoArea">
				<table cellpadding="0" cellspacing="10">
					<tr>
						<td align="left" valign="top" class="guideBoxHeading">
							<?php echo $lang['email']; ?>:<br/>
							<table cellspacing="0" cellpadding="3" class="guideInputArea">
								<tr>
									<td class="guideInputs">
										<?php echo $lang['activateEmail']; ?>:<br/>
										<input name="emailActivated" type="checkbox" value="1" <?php if($emailActivated) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['adminEmail']; ?>:<br/>
										<input name="adminEmail" type="text" size="40" value="<?php echo $adminEmail; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorAdminEmail)) echo $errorAdminEmail; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['smtpHost']; ?>:<br/>
										<input name="smtpHost" type="text" size="40" value="<?php echo $smtpHost; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorSmtpHost)) echo $errorSmtpHost; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['smtpUsername']; ?>:<br/>
										<input name="smtpUsername" type="text" size="40" value="<?php echo $smtpUsername; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorSmtpUsername)) echo $errorSmtpUsername; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['smtpPassword']; ?>:<br/>
										<input name="smtpPassword" type="password" size="40" value="<?php echo $smtpPassword; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorSmtpPassword)) echo $errorSmtpPassword; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['useSmtp']; ?>:<br/>
										<input name="useSmtp" type="checkbox" value="1" <?php if($useSmtp) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['validateEmailWhenRegister']; ?>:<br/>
										<input name="validateEmail" type="checkbox" value="1" <?php if($validateEmail) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
							</table>
						</td>
					</tr>		
				</table>
			</td>
		</tr>
		<tr>
			<td class="profileInfoArea">
				<table cellpadding="0" cellspacing="10">
					<tr>
						<td align="left" valign="top" class="guideBoxHeading">
							<?php echo $lang['attachments']; ?>:<br/>
							<table cellspacing="0" cellpadding="3" class="guideInputArea">
								<tr>
									<td class="guideInputs">
										<?php echo $lang['activateAttachments']; ?>:<br/>
										<input name="attachmentsActivated" type="checkbox" value="1" <?php if($attachmentsActivated) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['attachmentsMaxUploadSize']; ?>:<br/>
										<input name="maxAttachmentUploadSize" type="text" size="4" value="<?php echo $maxAttachmentUploadSize; ?>"  class="guideTextFields"/><?php echo $lang['KB']; ?> <span class="errorText"><?php if(!empty($errorMaxAttachmentUploadSize)) echo $errorMaxAttachmentUploadSize; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['maxNumberAttachments']; ?>:<br/>
										<input name="maxNumberOfAttachments" type="text" size="4" value="<?php echo $maxNumberOfAttachments; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorMaxNumberOfAttachments)) echo $errorMaxNumberOfAttachments; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['checkAttachmentExtensions']; ?>:<br/>
										<?php echo $lang['allowed']; ?> <input name="checkAllowedDisallowedAttachmentExtensions" type="radio" value="1" <?php if($checkAllowedDisallowedAttachmentExtensions) echo "checked=\"checked\" "; ?>/>
										<?php echo $lang['disallowed']; ?> <input name="checkAllowedDisallowedAttachmentExtensions" type="radio" value="0" <?php if(!$checkAllowedDisallowedAttachmentExtensions) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['allowedDisallowedAttachmentExtensions']; ?>:<br/>
										<input name="allowedDisallowedAttachmentExtensions" type="text" size="40" value="<?php echo $allowedDisallowedAttachmentExtensions; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorAllowedDisallowedAttachmentExtensions)) echo $errorAllowedDisallowedAttachmentExtensions; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['addStringWhenExtensionDisallowed']; ?>:<br/>
										<input name="disallowedAttachmentExtensionsAdd" type="checkbox" value="1" <?php if($disallowedAttachmentExtensionsAdd) echo "checked=\"checked\" "; ?>/>
									</td>
								</tr>
								<tr>
									<td class="guideInputs">
										<?php echo $lang['addThisStringWhenExtensionDisallowed']; ?>:<br/>
										<input name="disallowedAttachmentExtensionsAddThis" type="text" size="40" value="<?php echo $disallowedAttachmentExtensionsAddThis; ?>"  class="guideTextFields"/> <span class="errorText"><?php if(!empty($errorDisallowedAttachmentExtensionsAddThis)) echo $errorDisallowedAttachmentExtensionsAddThis; else echo "&nbsp;"; ?></span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>						
	</table>
	<div align="center" class="guideActionArea">
		<input name="next" type="submit" class="guideButton" value="<?php echo $lang['update']; ?> &gt;&gt;"/>
	</div>	
</form>
<?php
$menu->getBottom();
?> 