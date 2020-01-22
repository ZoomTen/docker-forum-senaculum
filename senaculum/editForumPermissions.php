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
$forumID = explode(" ",$_GET['id']);

if(isset($_POST['forumID'])) {
	/*print_r($_POST['forumID']);
	echo "<br/><br/>";
	print_r($_POST['view']);
	die();
	*/
	$permission->editForumPermissions($_POST['forumID'], $_POST['view'], $_POST['read'], $_POST['thread'], $_POST['post'], $_POST['edit'], $_POST['delete'], $_POST['sticky'], $_POST['announce'], $_POST['vote'], $_POST['poll'], $_POST['attach']);
	header("Content-type: text/html; charset=iso-8859-1");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php echo $lang['forumPermissions']; ?>
		</title>
		<script type="text/javascript">
			<!--
			top.location.href = "forumPermissions.php?done=1";
			//-->
		</script>
	</head>
	<body>
		<?php echo $lang['javascriptMustBeEnabled']; ?>
	</body>
</html>
<?php	
	die();
}	
$forumPermissions = $permission->getForumPermissions($forumID);	
//print_r($forumPermissions);
//die();

header("Content-type: text/html; charset=iso-8859-1");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php echo $lang['forumPermissions']; ?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css"/>
	</head>
	<body style="margin:0px; padding:0px;">
		<form action="editForumPermissions.php?id=<?php echo $forumID; ?>" id="forumPermissions" method="post">
			<table width="905" cellpadding="2" cellspacing="0">
			<?php
			foreach($forumPermissions as $currentPermission) {
			?>
				<tr>
					<td style="width:125px;" class="guidePermissionSet">
						<?php echo substr($currentPermission['forumName'],0,15) ?>
						<input type="hidden" name="forumID[]" value="<?php echo $currentPermission['forumID'] ?>"/>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<select name="view[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['view'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['view'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['view'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['view'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['view'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<select name="read[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['read'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['read'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['read'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['read'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['read'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<select name="thread[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['thread'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['thread'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['thread'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['thread'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['thread'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<select name="post[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['post'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['post'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['post'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['post'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['post'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<select name="edit[]" class="guideDropDown">
							<option value="reg"<?php if($currentPermission['edit'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['edit'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['edit'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['edit'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<select name="delete[]" class="guideDropDown">
							<option value="reg"<?php if($currentPermission['delete'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['delete'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['delete'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['delete'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<select name="sticky[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['sticky'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['sticky'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['sticky'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['sticky'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['sticky'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:70px;" class="guidePermissionSet">
						<select name="announce[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['announce'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['announce'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['announce'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['announce'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['announce'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:60px;" class="guidePermissionSet">
						<select name="vote[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['vote'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['vote'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['vote'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['vote'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['vote'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:100px;" class="guidePermissionSet">
						<select name="poll[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['poll'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['poll'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['poll'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['poll'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['poll'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
					<td style="width:80px;" class="guidePermissionSet">
						<select name="attach[]" class="guideDropDown">
							<option value="all"<?php if($currentPermission['attach'] == "all") echo " selected=\"selected\"";?>><?php echo $lang['all']; ?></option>
							<option value="reg"<?php if($currentPermission['attach'] == "reg") echo " selected=\"selected\"";?>><?php echo $lang['reg']; ?></option>
							<option value="pri"<?php if($currentPermission['attach'] == "pri") echo " selected=\"selected\"";?>><?php echo $lang['pri']; ?></option>
							<option value="mod"<?php if($currentPermission['attach'] == "mod") echo " selected=\"selected\"";?>><?php echo $lang['mod']; ?></option>	
							<option value="adm"<?php if($currentPermission['attach'] == "adm") echo " selected=\"selected\"";?>><?php echo $lang['adm']; ?></option>	
						</select>
					</td>
				</tr>
			<?php
			}
			?>		
			</table>
		</form>
	</body>
</html>