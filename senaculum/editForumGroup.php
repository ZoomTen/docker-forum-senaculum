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
require_once("classes/control.php");
require_once("classes/forumGroupHandler.php");

$auth = new logInOutHandler;
$error = new errorHandler;
$forumGroup = new forumGroupHandler;
$control = new control;
			
	$groupName = "";
	$errorName = "";
	$id = "";
	$oneGroup = "";
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



if(isset($_GET['id']) || isset($_POST['id']) || isset($_POST['name']))
{
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
		$oneGroup = $forumGroup->getOne($id);
		$groupName = $oneGroup['name'];
	}
	else if(isset($_POST['id']))
	{
		$id = $_POST['id'];
		$oneGroup = $forumGroup->getOne($id);
		$groupName = $oneGroup['name'];
	}
	
	if(isset($_POST['name']))
	{		
		$errorName = $control->name($_POST['name']);
		
		if(!empty($errorName))
		{
			$groupName = $_POST['name'];
		}
		if(empty($errorName))
		{
			$groupName = $_POST['name'];
			$forumGroup->edit($groupName, $id);
			$nextAction = "index.php";
			$error->done($lang['forumGroupEdited1'],$lang['forumGroupEdited2'],$nextAction);
		}
		
	}
	
	$title = $lang['editForumGroupPage2Heading'];
	$heading = $lang['editForumGroupPage2Heading'];
	$help = $lang['editForumGroupPage2Help'];
	
	include("include/guideTop.php");
	
?>
<form action="editForumGroup.php?id=<?php echo $id ?>" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['name']; ?>: <span class="errorText"><?php if(!empty($errorName)) echo $errorName; else echo "&nbsp;"; ?></span><br/>
							<input name="name" type="text" size="40" value="<?php echo $groupName; ?>" class="guideTextFields"/><br/>
						</td>
					</tr>		
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>		
				<table cellspacing="0" cellpadding="3" class="guideEHelp">
					<tr>
						<td>
							<?php echo $lang['editForumGroupPage2Helptext']; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?php
$backAction = "\"window.location = 'editForumGroup.php';\"";
$backName = "\"&lt;&lt; ".$lang['back']."\"";
$nextName = "\"".$lang['edit']." &gt;&gt;\"";

include("include/guideBottom.php");

}

else {

	$forumGroups = $forumGroup->getAll();

	$title = $lang['editForumGroupPage1Heading'];
	$heading = $lang['editForumGroupPage1Heading'];
	$help = $lang['editForumGroupPage1Help'];
	
	include("include/guideTop.php");
?>
<form action="editForumGroup.php" id="memberGroup" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['groupName']; ?>:<br/>
							<select name="id" class="guideDropDown">
								<?php
								if(!empty($forumGroups))
								{
									foreach($forumGroups as $element)
									{
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
	
	include("include/guideBottom.php");
}	
?>