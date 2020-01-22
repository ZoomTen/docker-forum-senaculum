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

require_once("classes/menuHandler.php");
require_once("classes/memberGroupHandler.php");
require_once("classes/logInOutHandler.php");

$menu = new menuHandler;
$memberGroup = new memberGroupHandler;
$auth = new logInOutHandler;
if(!$forumVariables['inlogged'])
	header("location: index.php");
if(!empty($_GET['delete']) && !empty($_GET['id'])) {
	if($memberGroup->removeMember($_GET['id'],$_GET['delete'])) {
		header("location: memberGroupList?id=".$_GET['id']);
	}	
}

if(!empty($_GET['join']) && !empty($_GET['id'])) {
	$memberGroups = $memberGroup->getOne($_GET['id']);
	require_once("classes/memberHandler.php");
	$member = new memberHandler;
	$members = $member->getOne($_GET['join'],true);
	if(!empty($memberGroups) && !empty($members)) {
		require_once("classes/PMHandler.php");
		$PM = new PMHandler;
		$subject = $lang['joinUsergroupPMMessageSubject1'].ucfirst($members['userName']).$lang['joinUsergroupPMMessageSubject2'].$memberGroups['name'].$lang['joinUsergroupPMMessageSubject3'];
		$text = $lang['joinUsergroupPMMessageText1'].$members['userName'].$lang['joinUsergroupPMMessageText2'].$memberGroups['name'].$lang['joinUsergroupPMMessageText3'];
		$text .= $lang['joinUsergroupPMMessageText4'].$members['userName'].$lang['joinUsergroupPMMessageText5'].$members['userName'].$lang['joinUsergroupPMMessageText6'];
		$PM->add($subject,$text,$_GET['join'],$memberGroups['groupModeratorMemberID']);
		header("location: memberGroupList.php?id=".$_GET['id']."&request=1");
	}	
}		
$menu->getTop();
if(empty($_GET['id'])) {
	$memberGroups = $memberGroup->getAll();
	$groupMembership = $memberGroup->groupMembership();
	if(empty($groupMembership))
		$groupMembership[0] = "";
	if(empty($memberGroups)) {
?>
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="memberGroupListHeadingGroup">
			<?php echo $lang['group']; ?>:
		</td>
		<td class="memberGroupListHeadingModerator">
			<?php echo $lang['moderator']; ?>:
		</td>
		<td class="memberGroupListHeadingDescription">
			<?php echo $lang['description']; ?>:
		</td>
	</tr>	
	<tr>
		<td class="memberGroupListHeading" colspan="3">
			<?php echo $lang['groupMembership']; ?>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="3">
			<?php echo $lang['notMemberGroup']; ?>
		</td>
	</tr>
	<tr>
		<td class="memberGroupListHeading" colspan="3">
			<?php echo $lang['joinAGroup']; ?>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="3">
			<?php echo $lang['noAvailableUsergroups']; ?>
		</td>
	</tr>
</table>	
<?php
	$menu->getBottom();
	die();
	}
?>
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="memberGroupListHeadingGroup">
			<?php echo $lang['group']; ?>:
		</td>
		<td class="memberGroupListHeadingModerator">
			<?php echo $lang['moderator']; ?>:
		</td>
		<td class="memberGroupListHeadingDescription">
			<?php echo $lang['description']; ?>:
		</td>
	</tr>	
	<tr>
		<td class="memberGroupListHeading" colspan="3">
			<?php echo $lang['groupMembership']; ?>
		</td>
	</tr>	
<?php
	$i = 0;
	foreach($memberGroups as $currentMemberGroup) {
		if(in_array($currentMemberGroup['groupID'],$groupMembership)) {
			if($i % 2 == 0) {
?>
	<tr>
		<td class="memberGroupListItem1Group">
			<a href="memberGroupList.php?id=<?php echo $currentMemberGroup['groupID']; ?>" class="bigLink"><?php echo $currentMemberGroup['name']; ?></a>
		</td>
		<td class="memberGroupListItem1Moderator">
			<?php echo $currentMemberGroup['groupModeratorUserName']; ?>
		</td>
		<td class="memberGroupListItem1Description">
			<?php echo $currentMemberGroup['description']; ?>
		</td>
	</tr>
<?php				
			}
			else {
?>
	<tr>
		<td class="memberGroupListItem2Group">
			<a href="memberGroupList.php?id=<?php echo $currentMemberGroup['groupID']; ?>" class="bigLink"><?php echo $currentMemberGroup['name']; ?></a>
		</td>
		<td class="memberGroupListItem2Moderator">
			<?php echo $currentMemberGroup['groupModeratorUserName']; ?>
		</td>
		<td class="memberGroupListItem2Description">
			<?php echo $currentMemberGroup['description']; ?>
		</td>
	</tr>
<?php				
			}
			$i++;
		}
	}
	if($i == 0) {
?>
	<tr>
		<td align="center" colspan="3">
			<?php echo $lang['notMemberGroup']; ?>
		</td>
	</tr>
<?php
	}
?>		
	<tr>
		<td class="memberGroupListHeading" colspan="3">
			<?php echo $lang['joinAGroup']; ?>
		</td>
	</tr>	
<?php
	$i = 0;
	foreach($memberGroups as $currentMemberGroup) {
		if(!in_array($currentMemberGroup['groupID'],$groupMembership)) {
			if($i % 2 == 0) {
?>
	<tr>
		<td class="memberGroupListItem1Group">
			<a href="memberGroupList.php?id=<?php echo $currentMemberGroup['groupID']; ?>" class="bigLink"><?php echo $currentMemberGroup['name']; ?></a>
		</td>
		<td class="memberGroupListItem1Moderator">
			<?php echo $currentMemberGroup['groupModeratorUserName']; ?>
		</td>
		<td class="memberGroupListItem1Description">
			<?php echo $currentMemberGroup['description']; ?>
		</td>
	</tr>
<?php				
			}
			else {
?>
	<tr>
		<td class="memberGroupListItem2Group">
			<a href="memberGroupList.php?id=<?php echo $currentMemberGroup['groupID']; ?>" class="bigLink"><?php echo $currentMemberGroup['name']; ?></a>
		</td>
		<td class="memberGroupListItem2Moderator">
			<?php echo $currentMemberGroup['groupModeratorUserName']; ?>
		</td>
		<td class="memberGroupListItem2Description">
			<?php echo $currentMemberGroup['description']; ?>
		</td>
	</tr>
<?php				
			}
			$i++;
		}
	}
	if($i == 0) {
?>
	<tr>
		<td align="center" colspan="3">
			<?php echo $lang['youMemberAllGroups']; ?>
		</td>
	</tr>
<?php
	}			
}
else {
	if(isset($_GET['request'])) {
?>
<script type="text/javascript">
	alert('<?php echo $lang['PMSentRequestJoin']; ?>');	
</script>
<?php	
	}
	$memberGroups = $memberGroup->getOne($_GET['id']);
	if(empty($memberGroups)) {
?>
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="memberGroupListHeadingUserName">
			<?php echo $lang['username']; ?>:
		</td>
		<td class="memberGroupListHeadingPosts">
			<?php echo $lang['posts']; ?>:
		</td>
		<td class="memberGroupListHeadingLocation">
			<?php echo $lang['location']; ?>:
		</td>
		<td class="memberGroupListHeadingPM">
			<?php echo $lang['privateMessage']; ?>:
		</td>
		<td class="memberGroupListHeadingEmail">
			<?php echo $lang['email']; ?>:
		</td>
		<td class="memberGroupListHeadingHomepage">
			<?php echo $lang['website']; ?>:
		</td>
	</tr>	
	<tr>
		<td class="memberGroupListHeading" colspan="6">
			<?php echo $lang['notExist']; ?>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="6">
			<?php echo $lang['usergroupNotExist']; ?>
		</td>
	</tr>
</table>	
<?php
	$menu->getBottom();
	die();
	}
	$groupModerators = $auth->groupModerator();
	if($groupModerators) {
		if(in_array($_GET['id'],$groupModerators))
			$groupModerator = true;
		else
			$groupModerator = false;	
	}
	else
		$groupModerator = false;
		
	$groupMemberships = $memberGroup->groupMembership();
	if($groupMemberships) {
		if(in_array($_GET['id'],$groupMemberships)) 
			$groupMembership = true;			
		else
			$groupMembership = false;
	}
	else
		$groupMembership = false;	
?>

<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="memberGroupListHeadingUserName">
			<?php echo $lang['username']; ?>:
		</td>
		<td class="memberGroupListHeadingLocation">
			<?php echo $lang['location']; ?>:
		</td>
		<td class="memberGroupListHeadingPM">
			<?php echo $lang['privateMessage']; ?>:
		</td>
		<td class="memberGroupListHeadingEmail">
			<?php echo $lang['email']; ?>:
		</td>
		<td class="memberGroupListHeadingHomepage">
			<?php echo $lang['website']; ?>:
		</td>
		<?php 
		if($forumVariables['inlogged']) {
		?>
		<td class="memberGroupListHeadingAction">
			<?php echo $lang['action']; ?>:
		</td>
		<?php
		}
		?>
	</tr>	
	<tr>
		<td class="memberGroupListHeading" colspan="<?php if($forumVariables['inlogged']) echo "6"; else echo "5"?>">
			<table width="100%" cellpadding="2" cellspacing="0">
				<tr>
					<td class="memberGroupListHeadingGroupName">
						<?php echo $memberGroups['name']; ?>
						&nbsp;&nbsp;<span class="memberGroupListHeadingCurrentModerator"><?php echo $lang['moderator']; ?>: <a href="profile.php?id=<?php echo $memberGroups['groupModeratorMemberID']; ?>"><?php echo $memberGroups['groupModeratorUserName']; ?></a></span>
					</td>
					<?php
					if($forumVariables['adminInlogged'] || $groupModerator || (!$groupMembership && $forumVariables['inlogged'])) {
					?>
					<td class="memberGroupListHeadingManage">
						<?php
						if($forumVariables['adminInlogged'] || $groupModerator) {
						?>
						<a href="<?php if($forumSettings['guidesInPopups']) echo "javascript:popup('editMemberGroup.php?id=".$_GET['id']."&amp;page=3',800,600);"; else echo "editMemberGroup.php?id=".$_GET['id']."&amp;page=3";?>" class="actionLink"><?php echo $lang['manageGroupMembers']; ?></a>
						<?php
						}
						if($forumVariables['adminInlogged']) {
						?>
						- <a href="<?php if($forumSettings['guidesInPopups']) echo "javascript:popup('editMemberGroup.php?id=".$_GET['id']."&amp;page=2',800,600);"; else echo "editMemberGroup.php?id=".$_GET['id']."&amp;page=2";?>" class="actionLink"><?php echo $lang['editGroup']; ?></a>
						<?php
						}
						if(!$groupMembership && $forumVariables['inlogged']) {
							if($forumVariables['adminInlogged'] || $groupModerator)
								echo " - ";
						?>
						<a href="memberGroupList.php?id=<?php echo $_GET['id']; ?>&amp;join=<?php echo $forumVariables['inloggedMemberID'] ?>" class="actionLink"><?php echo $lang['joinGroup']; ?></a>
						<?php	
						}
						?>
					</td>
					<?php
					}
					?>
				</tr>
			</table>
		</td>
	</tr>
	<?php
	if(empty($memberGroups['groupMemberID'])) {
	?>
	<tr>
		<td align="center" colspan="6">
			<?php echo $lang['noMembersInGroup']; ?>
		</td>
	</tr>
	<?php
	}
	else {
		$i=0;
		foreach($memberGroups['groupMemberID'] as $currentMember) {
			if($i % 2 == 0) {
	?>
	<tr>
		<td class="memberGroupListItem1UserName">
			<a href="profile.php?id=<?php echo $memberGroups['groupMemberID'][$i]; ?>" class="bigLink"><?php echo $memberGroups['groupMemberUserName'][$i]; ?></a>
		</td>
		<td class="memberGroupListItem1Location">
			<?php
			if(!empty($memberGroups['groupMemberLocation'][$i]))
				echo $memberGroups['groupMemberLocation'][$i];
			else
				echo "&nbsp;";
			?>		
		</td>
		<td class="memberGroupListItem1PM">
			<a href="<?php if($forumSettings['guidesInPopups']) echo "popup('addPM.php?id=".$memberGroups['groupMemberID'][$i]."',800,600);"; else echo "addPM.php?id=".$memberGroups['groupMemberID'][$i]; ?>" class="actionLink2"><?php echo $lang['PM']; ?></a>
		</td>
		<td class="memberGroupListItem1Email">
			<?php
		if(!empty($memberGroups['groupMemberEmail'][$i])) {
		?>
			<a href="mailto:<?php echo $memberGroups['groupMemberEmail'][$i]; ?>" class="link"><?php echo $memberGroups['groupMemberEmail'][$i]; ?></a>
		<?php
		}
		else
			echo "&nbsp;";
		?>	
		</td>
		<td class="memberGroupListItem1Homepage">
			<?php
			if(!empty($memberGroups['groupMemberHomepage'][$i])) {
			?>
			<a href="<?php echo $memberGroups['groupMemberHomepage'][$i]; ?>" class="link"><?php echo $memberGroups['groupMemberHomepage'][$i]; ?></a>
			<?php
			}
			else
				echo "&nbsp;";
			?>
		</td>
		<?php
		if($forumVariables['inlogged']) {
			if($forumVariables['adminInlogged'] || $groupModerator || $memberGroups['groupMemberID'][$i] == $forumVariables['inloggedMemberID']) {
				if($memberGroups['groupMemberID'][$i] != $memberGroups['groupModerator']) {
		?>
		<td class="memberGroupListItem1Action">
			<a href="javascript:confirmProcess('<?php echo $lang['removeMemberFromUsergroupConfirm1'].$memberGroups['groupMemberUserName'][$i].$lang['removeMemberFromUsergroupConfirm2']; ?>','memberGroupList.php?id=<?php echo $_GET['id']; ?>&amp;delete=<?php echo $memberGroups['groupMemberID'][$i]; ?>');" class="actionLink2"><?php echo $lang['remove']; ?></a>
		</td>
		<?php
				}
				else {
		?>
		<td class="memberGroupListItem1Action">
			&nbsp;
		</td>
		<?php		
				}
			}
			else {		
		?>
		<td class="memberGroupListItem1Action">
			&nbsp;
		</td>
		<?php	
			}
		}
		?>
	</tr>		
	<?php		
			}
			else {
	?>
	<tr>
		<td class="memberGroupListItem2UserName">
			<a href="profile.php?id=<?php echo $memberGroups['groupMemberID'][$i]; ?>" class="bigLink"><?php echo $memberGroups['groupMemberUserName'][$i]; ?></a>
		</td>
		<td class="memberGroupListItem2Location">
			<?php
			if(!empty($memberGroups['groupMemberLocation'][$i]))
				echo $memberGroups['groupMemberLocation'][$i];
			else
				echo "&nbsp;";	 
			?>
		</td>
		<td class="memberGroupListItem2PM">
			<a href="<?php if($forumSettings['guidesInPopups']) echo "popup('addPM.php?id=".$memberGroups['groupMemberID'][$i]."',800,600);"; else echo "addPM.php?id=".$memberGroups['groupMemberID'][$i]; ?>" class="actionLink2"><?php echo $lang['PM']; ?></a>
		</td>
		<td class="memberGroupListItem2Email">
		<?php
		if(!empty($memberGroups['groupMemberEmail'][$i])) {
		?>
			<a href="mailto:<?php echo $memberGroups['groupMemberEmail'][$i]; ?>" class="link"><?php echo $memberGroups['groupMemberEmail'][$i]; ?></a>
		<?php
		}
		else
			echo "&nbsp;";
		?>		
		</td>
		<td class="memberGroupListItem2Homepage">
			<?php
			if(!empty($memberGroups['groupMemberHomepage'][$i])) {
			?>
			<a href="<?php echo $memberGroups['groupMemberHomepage'][$i]; ?>" class="link"><?php echo $memberGroups['groupMemberHomepage'][$i]; ?></a>
			<?php
			}
			else
				echo "&nbsp;";
			?>
		</td>
		<?php
		if($forumVariables['inlogged']) {
			if($forumVariables['adminInlogged'] || $groupModerator || $memberGroups['groupMemberID'][$i] == $forumVariables['inloggedMemberID']) {
				if($memberGroups['groupMemberID'][$i] != $memberGroups['groupModerator']) {
		?>
		<td class="memberGroupListItem2Action">
			<a href="javascript:confirmProcess('<?php echo $lang['removeMemberFromUsergroupConfirm1'].$memberGroups['groupMemberUserName'][$i].$lang['removeMemberFromUsergroupConfirm2']; ?>','memberGroupList.php?id=<?php echo $_GET['id']; ?>&amp;delete=<?php echo $memberGroups['groupMemberID'][$i]; ?>');" class="actionLink2"><?php echo $lang['remove']; ?></a>
		</td>
		<?php
				}
				else {
		?>
		<td class="memberGroupListItem2Action">
			&nbsp;
		</td>
		<?php		
				}
			}
			else {		
		?>
		<td class="memberGroupListItem2Action">
			&nbsp;
		</td>
		<?php	
			}
		}
		?>
	</tr>
	<?php
			}
			$i++;
		}
	?>
	
	<?php
	}
	?>
<?php	
	
}
?>
</table>
<?php
$menu->getBottom();
?>