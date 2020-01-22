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

require_once("classes/permissionHandler.php");

$permission = new permissionHandler;

if(!$forumVariables['adminInlogged'])
	die($lang['noPermissionBeHere']);
if(empty($_GET['id']))
	die($lang['wrongURL']);	
$groupID = $_GET['id'];

if(isset($_POST['forumID'])) {
	$forumCount = 0;
	$viewCount = 0;
	$readCount = 0;
	$threadCount = 0;
	$postCount = 0;
	$editCount = 0;
	$deleteCount = 0;
	$stickyCount = 0;
	$announceCount = 0;
	$voteCount = 0;
	$pollCount = 0;
	$attachCount = 0;
	$moderatorCount = 0;
	$i=0;
	foreach($_POST['forumID'] as $currentForumID) {
		$forumID[$forumCount] = $currentForumID;
		$forumCount++;
		if(isset($_POST['view'][$viewCount])) {
			if($_POST['view'][$viewCount] == $currentForumID) {
				$view[$i] = true;
				$viewCount++;
			}
			else
				$view[$i] = false;
		}
		else
			$view[$i] = false;		
		
		if(isset($_POST['read'][$readCount])) {
			if($_POST['read'][$readCount] == $currentForumID) {
				$read[$i] = true;
				$readCount++;
			}
			else
				$read[$i] = false;
		}
		else
			$read[$i] = false;
			
		if(isset($_POST['thread'][$threadCount])) {
			if($_POST['thread'][$threadCount] == $currentForumID) {
				$thread[$i] = true;
				$threadCount++;
			}
			else
				$thread[$i] = false;
		}
		else
			$thread[$i] = false;	
			
		if(isset($_POST['post'][$postCount])) {
			if($_POST['post'][$postCount] == $currentForumID) {
				$post[$i] = true;
				$postCount++;
			}
			else
				$post[$i] = false;
		}
		else
			$post[$i] = false;
			
		if(isset($_POST['edit'][$editCount])) {
			if($_POST['edit'][$editCount] == $currentForumID) {
				$edit[$i] = true;
				$editCount++;
			}
			else
				$edit[$i] = false;
		}
		else
			$edit[$i] = false;	
			
		if(isset($_POST['delete'][$deleteCount])) {
			if($_POST['delete'][$deleteCount] == $currentForumID) {
				$delete[$i] = true;
				$deleteCount++;
			}
			else
				$delete[$i] = false;
		}
		else
			$delete[$i] = false;
			
		if(isset($_POST['sticky'][$stickyCount])) {
			if($_POST['sticky'][$stickyCount] == $currentForumID) {
				$sticky[$i] = true;
				$stickyCount++;
			}
			else
				$sticky[$i] = false;
		}
		else
			$sticky[$i] = false;
			
		if(isset($_POST['announce'][$announceCount])) {
			if($_POST['announce'][$announceCount] == $currentForumID) {
				$announce[$i] = true;
				$announceCount++;
			}
			else
				$announce[$i] = false;
		}
		else
			$announce[$i] = false;	
			
		if(isset($_POST['vote'][$voteCount])) {
			if($_POST['vote'][$voteCount] == $currentForumID) {
				$vote[$i] = true;
				$voteCount++;
			}
			else
				$vote[$i] = false;
		}
		else
			$vote[$i] = false;
			
		if(isset($_POST['poll'][$pollCount])) {
			if($_POST['poll'][$pollCount] == $currentForumID) {
				$poll[$i] = true;
				$pollCount++;
			}
			else
				$poll[$i] = false;
		}
		else
			$poll[$i] = false;		
			
		if(isset($_POST['attach'][$attachCount])) {
			if($_POST['attach'][$attachCount] == $currentForumID) {
				$attach[$i] = true;
				$attachCount++;
			}
			else
				$attach[$i] = false;
		}
		else
			$attach[$i] = false;	
			
		if(isset($_POST['moderator'][$moderatorCount])) {
			if($_POST['moderator'][$moderatorCount] == $currentForumID) {
				$moderator[$i] = true;
				$moderatorCount++;
			}
			else
				$moderator[$i] = false;
		}
		else
			$moderator[$i] = false;
		$i++;				
	}
	
	$permission->editMemberGroupPermissions($groupID, $forumID, $view, $read, $thread, $post, $edit, $delete, $sticky, $announce, $vote, $poll, $attach, $moderator);
	
	if(isset($_POST['default'])) {
		$permission->removeMemberGroupPermission($groupID,$_POST['default']);
	}
	
header("Content-type: text/html; charset=iso-8859-1");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">	
<html>
	<head>
		<title>
			<?php echo $lang['usergroupPermissions']; ?>
		</title>
		<script type="text/javascript">
			top.location.href = "memberGroupPermissions.php?done=1";
		</script>
	</head>
	<body>
		<?php echo $lang['javascriptMustBeEnabled']; ?>
	</body>
</html>
<?php	
	die();
}	
$memberGroupPermissions = $permission->getAllMemberGroupPermissionsUnsetSet($groupID);

header("Content-type: text/html; charset=iso-8859-1");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php echo $lang['usergroupPermissions']; ?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css"/>
	</head>
	<body style="margin:0px; padding:0px;">
		<form action="editMemberGroupPermissions.php?id=<?php echo $_GET['id']; ?>" id="memberGroupPermissions" method="post">
			<table width="905" cellpadding="2" cellspacing="0">
			<?php
			foreach($memberGroupPermissions as $currentPermission) {
				if($currentPermission['set']) {
			?>
				<tr>
					<td style="width:115px;" class="guidePermissionSet">
						<?php echo substr($currentPermission['forumName'],0,15) ?>
						<input type="hidden" name="forumID[]" value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="view[]" <?php if($currentPermission['view']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="read[]" <?php if($currentPermission['read']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="thread[]" <?php if($currentPermission['thread']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="post[]" <?php if($currentPermission['post']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="edit[]" <?php if($currentPermission['edit']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="delete[]" <?php if($currentPermission['delete']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="sticky[]" <?php if($currentPermission['sticky']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:70px;" class="guidePermissionSet">
						<input type="checkbox" name="announce[]" <?php if($currentPermission['announce']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="vote[]" <?php if($currentPermission['vote']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:100px;" class="guidePermissionSet">
						<input type="checkbox" name="poll[]" <?php if($currentPermission['poll']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionSet">
						<input type="checkbox" name="attach[]" <?php if($currentPermission['attach']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:40px;" class="guidePermissionSet">
						<input type="checkbox" name="moderator[]" <?php if($currentPermission['moderator']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<input type="checkbox" name="default[]" value="<?php echo $currentPermission['forumID'] ?>">
					</td>
				</tr>
			<?php
				}
				else {
			?>	
				<tr>
					<td style="width:115px;" class="guidePermissionUnset">
						<?php echo substr($currentPermission['forumName'],0,15) ?>
						<input type="hidden" name="forumID[]" value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="view[]" <?php if($currentPermission['view']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="read[]" <?php if($currentPermission['read']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="thread[]" <?php if($currentPermission['thread']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="post[]" <?php if($currentPermission['post']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="edit[]" <?php if($currentPermission['edit']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="delete[]" <?php if($currentPermission['delete']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="sticky[]" <?php if($currentPermission['sticky']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:70px;" class="guidePermissionUnset">
						<input type="checkbox" name="announce[]" <?php if($currentPermission['announce']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="vote[]" <?php if($currentPermission['vote']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:100px;" class="guidePermissionUnset">
						<input type="checkbox" name="poll[]" <?php if($currentPermission['poll']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:50px;" class="guidePermissionUnset">
						<input type="checkbox" name="attach[]" <?php if($currentPermission['attach']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:40px;" class="guidePermissionUnset">
						<input type="checkbox" name="moderator[]" <?php if($currentPermission['moderator']) echo "checked=\"checked\" "; ?>value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:60px;" class="guidePermissionUnset">
						&nbsp;
					</td>
				</tr>
			<?php
				}
			}
			?>		
			</table>
		</form>
	</body>
</html>