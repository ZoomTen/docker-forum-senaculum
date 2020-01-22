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
require_once('classes/memberHandler.php');
require_once('classes/menuHandler.php');
require_once('classes/other.php');

$error = new errorHandler;
$members = new memberHandler;
$menu = new menuHandler;
$other = new other;

if(empty($_GET['id']))
	$error->error($lang['incorrectURL1'],$lang['incorrectURL2']);
	
$member = $members->getOne($_GET['id'],false);	

$menu->getTop();
?>

<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="profileHeading">
			<?php echo $lang['profile']; ?>:
		</td>			
	</tr>	
	<tr>
		<td class="profileUsernameHeading">
			<?php echo $member['userName']; ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" class="profileInfoArea">
			<table cellpadding="0" cellspacing="10">
				<tr>
					<td valign="top">
						<b><?php echo $lang['aboutMember1'].$member['userName'].$lang['aboutMember2']?>:</b>
						<table width="100%" cellpadding="5" cellspacing="0" class="profileBoxes">
							<?php 
							if(!empty($member['firstName'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['firstName']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['firstName']; ?>
								</td>
							</tr>	
							<?php
							}
							?>
							<?php 
							if(!empty($member['sureName'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['lastName']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['sureName']; ?>
								</td>
							</tr>
							<?php 
							}
							if(!empty($member['location'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['location']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['location']; ?>
								</td>
							</tr>
							<?php 
							}
							if(!empty($member['occupation'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['occupation']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['occupation']; ?>
								</td>
							</tr>
							<?php 
							}
							if(!empty($member['interests'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['interests']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['interests']; ?>
								</td>
							</tr>
							<?php 
							}
							if(!empty($member['homepage'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['website']; ?>:
								</td>
								<td class="profileInfoText">
									<a href="<?php echo $member['homepage']; ?>" class="link" target="_blank"><?php echo $member['homepage']; ?></a>
								</td>
							</tr>
							<?php 
							}
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['registered']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $other->dateParse($forumVariables['dateFormat'],$member['registerDate']); ?>
								</td>
							</tr>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['totalNumberPosts']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['posts']; ?>
								</td>
							</tr>
							<?php
							if($member['status']['type'] != "none") {
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['status']; ?>:
								</td>
								<td class="profileInfoText">
									<?php 
									if($member['status']['type'] == "admin")
										echo "<span class=\"postListAdmin\">".$lang['administrator']."</span>";
									elseif($member['status']['type'] == "moderator") {
										echo $lang['moderatorFor1']."<span class=\"postListModerator\">".$lang['moderator']."</span>".$lang['moderatorFor2'].":";
										echo "<br/>";
										foreach($member['status']['forums'] as $forums) {
											echo "<a href=\"threads.php?id=".$forums['forumID']."\">".$forums['name']."</a><br/>";
										}
									}	
									?>
								</td>
							</tr>
							<?php
							}
							?>
						</table>
					</td>
					<td valign="top">
						<b><?php echo $lang['contact']; ?>:</b>
						<table width="100%" cellpadding="5" cellspacing="0" class="profileBoxes">
							<?php 
							if(!empty($member['email'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['email']; ?>:
								</td>
								<td class="profileInfoText">
									<a href="mailto:<?php echo $member['email']; ?>" class="link"><?php echo $member['email']; ?></a>
								</td>
							</tr>	
							<?php
							}
							?>
							<?php 
							if(!empty($member['ICQ'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['ICQ']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['ICQ']; ?>
								</td>
							</tr>
							<?php 
							}
							if(!empty($member['AIM'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['AIM']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['AIM']; ?>
								</td>
							</tr>
							<?php 
							}
							if(!empty($member['MSN'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['MSN']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['MSN']; ?>
								</td>
							</tr>
							<?php 
							}
							if(!empty($member['yahoo'])) { 
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['yahoo']; ?>:
								</td>
								<td class="profileInfoText">
									<?php echo $member['yahoo']; ?>
								</td>
							</tr>
							<?php 
							}
							if($forumVariables['inlogged']) {
								if($member['memberID'] != $forumVariables['inloggedMemberID']) {
							?>
							<tr>
								<td class="profileCaptionText">
									<?php echo $lang['PM']; ?>:
								</td>
								<td class="profileInfoText">
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPM.php?id=<?php echo $member['memberID']; ?>',800,600);<?php } else { ?>addPM.php?id=<?php echo $member['memberID']; } ?>" class="link"><?php echo $lang['sendPM']; ?></a>
								</td>
							</tr>
							<?php
								}
							}
							?>
						</table>
					</td>
					<?php
					if(!empty($member['avatar'])) {
					?>	
					<td valign="top">
						<b><?php echo $lang['avatar']; ?>:</b>
						<table width="100%" cellpadding="10" cellspacing="0" class="profileBoxes">
							<tr>
								<td align="center" valign="middle">
									<img src="images/avatars/<?php echo $member['avatar']; ?>" alt="<?php echo $lang['avatar']; ?>"/>
								</td>
							</tr>
						</table>	
					</td>
					<?php
					}
					?>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
$menu->getBottom();
?> 