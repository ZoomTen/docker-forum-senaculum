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

require_once("classes/logInOutHandler.php");
require_once("classes/errorHandler.php");

$auth = new logInOutHandler;
$error = new errorHandler;
			
$done = false;
if(isset($_GET['page'])) {
	/*if($_GET['page'] == 4) {
		$error->done("The permissions is changed","The permissions for this member group is changed","index.php");
	}*/
	
	if($_GET['page'] == 3) {
		if(empty($_GET['id'])) {
			header("location: editMemberGroup.php");
			die();
		}
		
		require_once("classes/memberGroupHandler.php");
		require_once("classes/memberHandler.php");
	
		$memberGroup = new memberGroupHandler;
		$member = new memberHandler;
	
		$errorSelectedGroupMembers = "";
		$addMember = "";
		
		$currentMemberGroup = $memberGroup->getOne($_GET['id']);
		$selectedGroupMembers = $currentMemberGroup['groupMemberUserName'];
		
		$groupName = $currentMemberGroup['name'];
		$groupModerator = $currentMemberGroup['groupModeratorUserName'];
		
		$done = true;
	
	}
	elseif($_GET['page'] == 2) {
		if(empty($_GET['id'])) {
			header("location: editMemberGroup.php");
			die();
		}
		
		require_once("classes/memberGroupHandler.php");
		require_once("classes/control.php");
		require_once("classes/memberHandler.php");
	
		$memberGroup = new memberGroupHandler;
		$control = new control;
		$member = new memberHandler;
	
		$errorGroupName = "";
		$errorDescription = "";
		$errorGroupModerator = "";
		
		$currentMemberGroup = $memberGroup->getOne($_GET['id']);
		$groupName = $currentMemberGroup['name'];
		$description = $currentMemberGroup['description'];
		$currentMember = $member->getOne($currentMemberGroup['groupModeratorMemberID'],false);
		$groupModerator = $currentMember['userName'];	
	
		$done = true;
	}	
}
if(!$done) {
	require_once("classes/memberGroupHandler.php");
	
	$memberGroup = new memberGroupHandler;
	
	$memberGroups = $memberGroup->getAll();
}		
		
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
if(empty($_GET['id']) && empty($_GET['page'])) {		
	if(!$forumVariables['adminInlogged'])
	{
		$error->guide($lang['notLoggedInAdmin1'], $lang['notLoggedInAdmin2'], true);
	}
}
else {
	if(!$forumVariables['adminInlogged'] && (!$auth->groupModerator() == $_GET['id'] && $_GET['page'] == 3))
	{
		if($auth->groupModerator() == $_GET['id'] && $_GET['page'] != 3)
			header("location: editMemberGroup.php?page=3&id=".$_GET['id']);
		else	
			$error->guide($lang['notLoggedInAdminOrGroupmoderator1'], $lang['notLoggedInAdminOrGroupmoderator2'], true);
	}
}	
$done = false;
if(isset($_GET['page'])) {
	if($_GET['page'] == 3) {
	
			if(isset($_POST['submit']))
		{
			if($_POST['submit'] == $lang['find']) {
				require_once("classes/searchHandler.php");
				$search = new searchHandler;
				$users = $search->user($_POST['addMember']);
				if(empty($users))
					$addMember = $_POST['addMember'];
				else
					$addMember = $users[0]['userName'];
				$addMember = $_POST['addMember'];
				if(isset($_POST['selectedGroupMembers'])) 
					$selectedGroupMembers = $_POST['selectedGroupMembers'];	
			}
			else {
				require_once("classes/memberHandler.php");
				$member = new memberHandler;
				$i=0;
				if(!empty($_POST['selectedGroupMembers'])) {
					foreach($_POST['selectedGroupMembers'] as $members) {
						$memberID[$i] = $member->getMemberID($members);
						if(empty($memberID[$i]))
							$errorSelectedGroupMembers[] = $members;
						$i++;		
					}
				}		
		
				if(empty($errorSelectedGroupMembers))
				{
					$memberGroup->editMembers($_GET['id'],$memberID);
					$error->done($lang['usergroupMembersChanged1'],$lang['usergroupMembersChanged2'], "index.php");
					//header("location: editMemberGroup.php?page=4");	
				}
				else 
				{
				$selectedGroupMembers = $_POST['selectedGroupMembers'];	
				}
			}
		}
		
		$title = $lang['editUsergroupPage3'];
		$heading = $lang['editUsergroupPage3'];
		$help = $lang['editUsergroupPage3Help1'].$groupName.$lang['editUsergroupPage3Help2'];
	
		include("include/guideTop.php");
?>
<script type="text/javascript">
<!--
	function addItemToList() {
		lenght = document.getElementById("selectedGroupMembers").length;
		if(document.getElementById("addMember").value == "") {
			document.getElementById("addMember").focus();
			return false;
		}
		for (i=0; i<lenght; i++) {
			if(document.getElementById("selectedGroupMembers").options[i].value == document.getElementById("addMember").value) {
				alert(document.getElementById("addMember").value+" is already selected!");
				return false;
			}	
		}	
		//memberGroup.selectedGroupMembers
		document.getElementById("selectedGroupMembers").options[lenght] = new Option(document.getElementById("addMember").value);
		document.getElementById("addMember").value = "";
		document.getElementById("addMember").focus();
	}
	
	function removeItemInList() {
		lenght = document.getElementById("selectedGroupMembers").length;
		for ( i=(lenght-1); i>=0; i--) {
            if (document.getElementById("selectedGroupMembers").options[i].selected == true ) {
                document.getElementById("selectedGroupMembers").options[i] = null;
            }
        }
	}
	
	function submitMembers() {
		lenght = document.getElementById("selectedGroupMembers").length;
		for (i=0; i<lenght; i++) {
			document.getElementById("selectedGroupMembers").options[i].selected = true;
		}
		return true; 
	}
// -->	
</script>
<form action="editMemberGroup.php?id=<?php echo $_GET['id']; ?>&amp;page=3" id="memberGroup" method="post" onsubmit="return submitMembers();">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['addMember']; ?>:<br/>
							<input name="addMember" id="addMember" type="text" value="<?php if(!empty($users)) echo $users[0]['userName']; ?>" size="33" maxlength="15" class="guideTextFields"/>
							<input type="submit" name="submit" value="<?php echo $lang['find']; ?>" class="guideButton"/>
							<input type="button" name="addmemberButton" value="<?php echo $lang['add']; ?>" onclick="addItemToList();" class="guideButton"/>
						</td>
					</tr>
					<?php
					if(!empty($users)) {
					?>		
					<tr>
						<td class="guideInputs">
							<?php echo $lang['findResult']; ?>:<br/>
							<select name="users" class="guideDropDown" onchange="document.getElementById('addMember').value = this.options[this.selectedIndex].value;">
					<?php			
						foreach($users as $user) {
					?>
									<option value="<?php echo $user['userName']; ?>"><?php echo $user['userName']; ?></option>
					<?php
						}
					?>
							</select>
						</td>
					</tr>
					<?php			
					}	
					?>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['members']; ?>: <span class="errorText"><?php if(!empty($errorSelectedGroupMembers)) echo $lang['selectedMemberNotExist']; else echo "&nbsp;"; ?></span><br/>
							<select name="selectedGroupMembers[]" id="selectedGroupMembers" size="10" multiple="multiple" style="width:250px;" class="guideDropDown">
							<?php
							if(!empty($selectedGroupMembers)) {
								foreach($selectedGroupMembers as $selectedGroupMember) {
							?>
								<option value="<?php echo $selectedGroupMember; ?>"<?php if(!empty($errorSelectedGroupMembers)) { if(in_array($selectedGroupMember,$errorSelectedGroupMembers)) echo " selected";}?>><?php echo $selectedGroupMember; ?></option>
							<?php
								}
							}
							else {				
							?>	
								<option value="<?php echo $groupModerator; ?>"><?php echo $groupModerator; ?></option>
							<?php
							}
							?>
							</select><br/>
							<input type="button" value="<?php echo $lang['deleteSelectedMember']; ?>" onclick="removeItemInList();" class="guideButton"/>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['editUsergroupPage3Helptext']; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?php
		$backAction = "\"window.location = 'editMemberGroup.php?id=".$_GET['id']."&amp;page=2';\"";
		$backName = "\"&lt;&lt; ".$lang['back']."\"";
		$nextName = "\"".$lang['edit']." &gt;&gt;\"";	
		
		$done = true;
	
	}
	elseif($_GET['page'] == 2) {
		if(isset($_POST['submit']))
		{
			if($_POST['submit'] == $lang['find']) {
				require_once("classes/searchHandler.php");
				$search = new searchHandler;
				$users = $search->user($_POST['groupModerator']);
				if(empty($users))
					$groupModerator = $_POST['groupModerator'];
				else
					$groupModerator = $users[0]['userName'];
				$groupName = $_POST['groupName'];
				$description = $_POST['description'];	
			}
			else {
				if(isset($_POST['delete'])) {
					if($_POST['delete']) {
						$memberGroup->remove($_GET['id']);
						header("location: editMemberGroup.php");
						die();
					}
				}	
				if($control->maxLenght(50, $_POST['groupName']))
					$errorGroupName = $lang['nameToLongMax50'];;
				if($control->maxLenght(255, $_POST['description']))
					$errorDescription = $lang['descriptionToLongMax255'];
				require_once("classes/memberHandler.php");
				$member = new memberHandler;
				$memberID = $member->getMemberID($_POST['groupModerator']);
				if(empty($memberID))
					$errorGroupModerator = $langh['userNotExist'];
		
				if(empty($errorName) && empty($errorDescription) && empty($errorGroupModerator))
				{
					$memberGroup->edit($_GET['id'],$_POST['groupName'], $_POST['description'], $memberID);
					header("location: editMemberGroup.php?id=".$_GET['id']."&page=3");
				}
				else 
				{
					$groupName = $_POST['groupName'];
					$description = $_POST['description'];
					$groupModerator = $_POST['groupModerator'];
				}
			}	
		}
		$title = $lang['editUsergroupPage2'];
		$heading = $lang['editUsergroupPage2'];
		$help = $lang['editUsergroupPage2Help'];
		
		include("include/guideTop.php");
?>
<form action="editMemberGroup.php?id=<?php echo $_GET['id']; ?>&amp;page=2" id="memberGroup" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['name']; ?>: <span class="errorText"><?php if(!empty($errorGroupName)) echo $errorGroupName; else echo "&nbsp;"; ?></span><br/>
							<input name="groupName" type="text" size="40"  maxlength="50" value="<?php echo $groupName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['description']; ?>: <span class="errorText"><?php if(!empty($errorDescription)) echo $errorDescription; else echo "&nbsp;"; ?></span><br/>
							<input name="description" type="text" size="40" maxlength="255" value="<?php echo $description; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['groupModerator']; ?>: <span class="errorText"><?php if(!empty($errorGroupModerator)) echo $errorGroupModerator; else echo "&nbsp;"; ?></span><br/>
							<input name="groupModerator" id="groupModerator" type="text" size="33" maxlength="15" value="<?php echo $groupModerator; ?>" class="guideTextFields"/>
							<input type="submit" name="submit" value="<?php echo $lang['find']; ?>" class="guideButton"/>
						</td>
					</tr>
					<?php
					if(!empty($users)) {
					?>		
					<tr>
						<td class="guideInputs">
							<?php echo $lang['findResult']; ?>:<br/>
							<select name="users" class="guideDropDown" onchange="document.getElementById('groupModerator').value = this.options[this.selectedIndex].value;">
					<?php			
						foreach($users as $user) {
					?>
									<option value="<?php echo $user['userName']; ?>"><?php echo $user['userName']; ?></option>
					<?php
						}
					?>
							</select>
						</td>
					</tr>
					<?php			
					}	
					?>
					<tr>	
						<td class="guideInputs">
							<?php echo $lang['deleteUsergroup']; ?>:<br/>
							<input name="delete" type="checkbox" class="guideTextFields"/><br/>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['editUsergroupPage2Helptext']; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>			
<?php
		$backAction = "\"window.location = 'editMemberGroup.php';\"";
		$backName = "\"&lt;&lt; ".$lang['back']."\"";
		$nextName = "\"".$lang['next']." &gt;&gt;\"";	
		$done = true;	
	}	

}
if(!$done) {
	
	if(isset($_POST['submit']))
	{
		header("location: editMemberGroup.php?id=".$_POST['groupName']."&page=2");
	}
	$title = $lang['editUsergroupPage1'];
	$heading = $lang['editUsergroupPage1'];
	$help = $lang['editUsergroupPage1Help'];
	
	include("include/guideTop.php");
?>
<form action="editMemberGroup.php" id="memberGroup" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['groupName']; ?>:<br/>
							<select name="groupName" class="guideDropDown">
								<?php
								if(!empty($memberGroups)) {
									foreach($memberGroups as $element) {
								?>
								<option value="<?php echo $element['groupID']; ?>"><?php echo $element['name']; ?></option>
								<?php		
									}
								
								}
								?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>			
<?php
	$backAction = "\"self.close();\"";
	$backName = "\"&lt;&lt; ".$lang['close']."\"";
	$nextName = "\"".$lang['next']." &gt;&gt;\"";	
}	

include("include/guideBottom.php");
?>