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
$auth = new logInOutHandler;
	
$done = false;
if(isset($_GET['page'])) {
	if($_GET['page'] == 3) {
		require_once("classes/errorHandler.php");
		$error = new errorHandler;
		$error->done($lang['usergroupCreated1'],$lang['usergroupCreated2'],"index.php");
	}
		
	elseif($_GET['page'] == 2) {
		require_once("classes/memberGroupHandler.php");
		require_once("classes/errorHandler.php");
		require_once("classes/memberHandler.php");
	
		$memberGroup = new memberGroupHandler;
		$error = new errorHandler;
		$member = new memberHandler;
	
		$errorSelectedGroupMembers = "";
	
		$selectedGroupMembers = "";
		
		if(empty($_GET['id'])) {
			$error->guide($lang['incorrectURL1'],$lang['incorrectURL2'],false);
		}
		
		$currentMemberGroup = $memberGroup->getOne($_GET['id']);
		$groupName = $currentMemberGroup['name'];
		$groupModerator = $currentMemberGroup['groupModeratorUserName'];
	
		$done = true;
	}	
}
if(!$done) {
	require_once("classes/memberGroupHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/dbHandler.php");
	
	$error = new errorHandler;
	$memberGroup = new memberGroupHandler;
	$control = new control;
	$db = new dbHandler;
	
	$errorGroupName = "";
	$errorDescription = "";
	$errorGroupModerator = "";
	
	$groupName = "";
	$description = "";
	$groupModerator = "";
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

if(!$forumVariables['adminInlogged'])
{
	$error->guide($lang['notLoggedInAdmin1'], $lang['notLoggedInAdmin2'], true);
}
$done = false;
if(isset($_GET['page'])) {
	
	if($_GET['page'] == 3) {
		$done = true;
	
	}
	elseif($_GET['page'] == 2) {
	if(isset($_POST['find'])) {
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
	elseif(isset($_POST['submit'])) {
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
			$memberGroup->addMember($_GET['id'],$memberID);
			header("location: addMemberGroup.php?page=3");
		}
		else 
		{
			$selectedGroupMembers = $_POST['selectedGroupMembers'];	
		}
	}
		
		
		$title = "Add Usergroup - Page 2/2";
		$heading = "Add Usergroup - Page 2/2";
		$help = $lang['addUsergroupPage2Help1']." ".$groupName." ".$lang['addUsergroupPage2Help2'];
	
		include("include/guideTop.php");
?>
<script type="text/javascript">
	function addItemToList() {
		lenght = document.getElementById("selectedGroupMembers").length;
		if(document.getElementById("addMember").value == "") {
			document.getElementById("addMember").focus();
			return false;
		}
		for (i=0; i<lenght; i++) {
			if(document.getElementById("selectedGroupMembers").options[i].value == document.getElementById("addMember").value) {
				alert("<?php echo $lang['memberSelected1'] ?>"+document.getElementById("addMember").value+"<?php $lang['memberSelected2']; ?>");
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
	
</script>
<form action="addMemberGroup.php?id=<?php echo $_GET['id']; ?>&amp;page=2" id="memberGroup" method="post" onSubmit="return submitMembers();">
<table cellpadding="0" cellspacing="10">
	<tr>
		<td align="left" valign="top" class="guideBoxHeading">
			<?php echo $lang['input']; ?>:<br/>
			<table cellspacing="0" cellpadding="3" class="guideInputArea">
				<tr>
					<td class="guideInputs">
						<?php echo $lang['addMember']; ?>:<br/>
						<input name="addMember" id="addMember" type="text" size="33" maxLength="15" <?php if(!empty($users)) echo "value=\"".$users[0]['userName']."\" "; ?>class="guideTextFields"/>
						<input type="submit" name="find" value="<?php echo $lang['find']; ?>" class="guideButton"/>
						<input type="button" name="addmemberButton" value="<?php echo $lang['add']; ?>" onClick="addItemToList();" class="guideButton"/>
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
						<?php echo $lang['members']; ?>: <span class="errorText"><?php if(!empty($errorSelectedGroupMembers)) echo "Selected members does not exist"; else echo "&nbsp;"; ?></span><br/>
						<select name="selectedGroupMembers[]" id="selectedGroupMembers" size="10" multiple style="width:250px;" class="guideDropDown">
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
						<input type="button" value="<?php echo $lang['deleteSelectedMember']; ?>" onClick="removeItemInList();" class="guideButton"/>
					</td>
				</tr>
			</table>
		</td>
		<td align="left" valign="top" class="guideBoxHeading">
			<?php echo $lang['help']; ?>:<br/>
			<table class="guideEHelp" cellpadding="3" cellspacing="0">
				<tr>
					<td>
						<?php echo $lang['addMemberGroupPage2Helptext']; ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
		$backAction = "\"window.location = 'editMemberGroup.php?page=2&amp;id=".$_GET['id']."';\"";
		$backName = "\"<< ".$lang['back']."\"";
		$nextName = "\"".$lang['OK']." >>\"";
		
		$done = true;	
	}	
}
if(!$done) {
	
	if(isset($_POST['find'])) {
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
	elseif(isset($_POST['submit'])) {
		$errorGroupName = $control->text($_POST['groupName'],1,50);
		if($control->maxLenght(255, $_POST['description']))
			$errorDescription = $lang['descriptionToLongMax255'];
		if(empty($_POST['groupModerator']))
			$errorGroupModerator = $lang['userNotExist'];
		else {	
			require_once("classes/memberHandler.php");
			$member = new memberHandler;
			$memberID = $member->getMemberID($_POST['groupModerator']);
			if(empty($memberID))
				$errorGroupModerator = $lang['userNotExist'];
		}		
	
		if(empty($errorGroupName) && empty($errorDescription) && empty($errorGroupModerator))
		{
			$memberGroup->add($_POST['groupName'], $_POST['description'], $memberID);
			$newestID = $memberGroup->getGroupIDOfNewest();
			header("location: addMemberGroup.php?id=".$newestID."&page=2");
		}
		else 
		{
			$groupName = $_POST['groupName'];
			$description = $_POST['description'];
			$groupModerator = $_POST['groupModerator'];
		}
	}	
	$title = $lang['addUsergroupPage1'];
	$heading = $lang['addUsergroupPage1'];
	$help = $lang['addUsergroupPage1Help'];
	
	include("include/guideTop.php");
?>
<form action="addMemberGroup.php" id="memberGroup" method="post">
<table cellpadding="0" cellspacing="10">
	<tr>
		<td align="left" valign="top" class="guideBoxHeading">
			<?php echo $lang['input']; ?>:<br/>
			<table cellspacing="0" cellpadding="3" class="guideInputArea">
				<tr>
					<td class="guideInputs">
						<?php echo $lang['name']; ?>: <span class="errorText"><?php if(!empty($errorGroupName)) echo $errorGroupName; else echo "&nbsp;"; ?></span><br/>
						<input name="groupName" type="text" size="40" maxLength="50" value="<?php echo $groupName; ?>" class="guideTextFields"/><br/>
					</td>
				</tr>
				<tr>	
					<td class="guideInputs">
						<?php echo $lang['description']; ?>: <span class="errorText"><?php if(!empty($errorDescription)) echo $errorDescription; else echo "&nbsp;"; ?></span><br/>
						<input name="description" type="text" size="40" maxLength="255" value="<?php echo $description; ?>" class="guideTextFields"/><br/>
					</td>
				</tr>
				<tr>	
					<td class="guideInputs">
						<?php echo $lang['groupModerator']; ?>: <span class="errorText"><?php if(!empty($errorGroupModerator)) echo $errorGroupModerator; else echo "&nbsp;"; ?></span><br/>
						<input name="groupModerator" type="text" size="33" maxLength="15" value="<?php echo $groupModerator; ?>" class="guideTextFields"/>
						<input type="submit" name="find" value="<?php echo $lang['find']; ?>" class="guideButton"/>
					</td>
				</tr>
				<?php
				if(!empty($users)) {
				?>		
				<tr>
					<td class="guideInputs">
						<?php echo $lang['findResult']; ?>:<br/>
						<select name="users" class="guideDropDown" onChange="memberGroup.groupModerator.value = this.options[this.selectedIndex].value;">
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
			</table>
		</td>
		<td align="left" valign="top" class="guideBoxHeading">
			<?php echo $lang['help']; ?>:<br/>
			<table class="guideEHelp" cellpadding="3" cellspacing="0">
				<tr>
					<td>
						<?php echo $lang['addMemberGroupPage1Helptext']; ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>			
<?php
	$backAction = "\"self.close();\"";
	$backName = "\"&lt;&lt; ".$lang['close']."\"";
	$nextName = "\"".$lang['add']." &gt;&gt;\"";
}	

include("include/guideBottom.php");
?>