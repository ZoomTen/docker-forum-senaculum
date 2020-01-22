<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

require_once('./classes/logInOutHandler.php');
require_once('./classes/dbHandler.php');
require_once('./classes/PMHandler.php');
require_once("./classes/menuHandler.php");

$login = new logInOutHandler;
$db = new dbHandler;
$PM = new PMHandler;
$page = substr(strrchr($_SERVER['SCRIPT_NAME'],'/'),1);
$menu = new menuHandler;

global $forumVariables;
global $forumSettings;
global $lang;

header("Content-type: text/html; charset=iso-8859-1");
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>
			<?php echo $forumSettings['forumName']; ?>
		</title>
		<link rel="stylesheet" type="text/css" href="menu/default/style.css"/>

		<?php require("./include/head.php"); ?>

		<script type="text/javascript">
		<!--
			//Menufunctions

			function fadeIn(i,j,fadeObject){
				if(navigator.appName == "Microsoft Internet Explorer")
					document.getElementById(fadeObject).filters.alpha.opacity=i;
				else
					document.getElementById(fadeObject).style.opacity=i/100;
				i++;
				if(i<j){
					setTimeout("fadeIn("+i+","+j+",'"+fadeObject+"')",0);
				}
			}
			function fadeOut(i,j,fadeObject){
				if(navigator.appName == "Microsoft Internet Explorer")
					document.getElementById(fadeObject).filters.alpha.opacity=i;
				else
					document.getElementById(fadeObject).style.opacity=i/100;
				i=i-1;
				if (i>j)
					setTimeout("fadeOut("+i+","+j+",'"+fadeObject+"')", 0);
			}

			function setFocus() {	//Set focus to loginform
				<?php
				if(empty($_COOKIE['forumLastUser']))
					echo "document.getElementById('login').loginUsername.focus();";
				else
					echo "document.getElementById('login').loginPassword.focus();";
				?>
			}
			function onEnter(event) { //Login when press enter
				if(event.witch == 13)
					document.getElementById('login').submit;
				else {
					if(event.keyCode == 13)
						document.getElementById('login').submit();
				}
			}
			function menuReset() { 		//Reset the menu
				if(document.getElementById){	//Look if the browser support this
					var menu = document.getElementById("menu").getElementsByTagName("div"); //All div-tags in menu section will be put into menu variable
					var menuHeading = document.getElementById("menuTable").getElementsByTagName("td");	//All td-tags in the menuTable will be put into menuHeading variable
					for (var i=0; i<menu.length; i++) { 					//Loop all div-tags for the menu
						if (menu[i].className=="menuHeadingBox") 			//Check if the divtag belongs to menuHeadingBox-class
							menu[i].style.display = "none"; 			//Hide all menuboxes
					}
					for (var j=0; j<menuHeading.length; j++) {				//Loop all td-tags in menutable
						if(menuHeading[j].id != "menuTitle")
							menuHeading[j].style.borderStyle = "solid";		//Hide the border to current menu heading
					}
				}
			}
			function menuChange(heading, element) {						//Change menubox to view
				if(document.getElementById){						//Check if browser support this
					var menu = document.getElementById("menu").getElementsByTagName("div"); //All div-tags in menu section will be put into menu variable
					var active = false;
					for (var i=0; i<menu.length; i++){	//Loop all div-tags
						if (menu[i].className=="menuHeadingBox")		//Check if the divtag belongs to menuHeadingBox-class
							if(menu[i].style.display == "block")		//Check if current menubox is visible
								active = true;				//Tells that the menu is active
					}
				}
				if(active) { 								//Check if menu is active
					menuReset();							//Reset the menu
					document.getElementById(element).style.display = "block";	//View the current menubox
					heading.style.borderStyle = "inset";				//Change border on current heading
				}
			}

			function menuHeadingBoxChangeColorIn(heading, element) {	//Changes style on menu heading
				heading.style.borderStyle = "outset"; 			//Change the borderstyle to outset
				menuChange(heading, element);					//Change active menuheading
			}
			function menuHeadingBoxChangeColorOut(heading, element) {	//Changes style on menu heading
				heading.style.borderStyle = "solid";			//Change the borderstyle to outset
				menuChange(heading, element);					//Change active menuheading
			}

			function menuElementChangeColorIn(element) {			//Changes style on menuitem
				//element.style.backgroundColor = "#CC8800";		//Change background color on menuitem
				//element.style.color = "#FFFFFF";			//Change text color on menuitem
				element.className = "menuElementMouseOver";
			}
			function menuElementChangeColorOut(element) {			//Changes style on menuitem
				//element.style.backgroundColor = "#FDF4DB";		//Change background color on menuitem
				//element.style.color = "#000000";			//Change text color on menuitem
				element.className = "menuElement";
			}

			function toggleMenu(heading,currElem) {					//Open or close a menubox
				if (document.getElementById) {
					if(document.getElementById(currElem).style.display == "block") {	//Check if the current menubox is visible
						document.getElementById(currElem).style.display = "none";	//Set current menubox to visible
						heading.style.borderStyle = "solid";				//Hide current border
					}
					else {
						document.getElementById(currElem).style.display = "block";	//Set current menubox to not be visible
						heading.style.borderStyle = "inset";				//Set border inset to current
					}
				}
				//else {
				//	document.currElem.display = "none";
				//}
			}
		//-->
		</script>
	</head>
	<body <?php if(!$forumVariables['inlogged']) echo "onload=\"setFocus();\"";?>>
		<div id="menu">
			<div onclick="menuReset()" id="forumMenu" class="menuHeadingBox" style="left: 3px;">
				<?php
				$forumMenu = $menu->generateForumMenu();
				if(isset($forumMenu['addPost'])) {
				?>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['addPost']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['addPost']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<?php
				}
				?>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['forumIndex']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['forumIndex']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['members']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['members']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['membersOnline']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['membersOnline']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['search']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['search']['name']; ?>
						</td>
					</tr>
				</table>
				<?php
				if($forumVariables['inlogged']) {
				?>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['pm']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['pm']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['bookmarks']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['bookmarks']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['viewNewPosts']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['viewNewPosts']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['viewMyPosts']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['viewMyPosts']['name']; ?>
						</td>
					</tr>
				<?php
				}
				else {
				?>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
				<?php
				}
				?>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['viewUnansweredPosts']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['viewUnansweredPosts']['name']; ?>
						</td>
					</tr>
				</table>
				<?php
				if($forumVariables['inlogged']) {
				?>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['editMyProfile']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['editMyProfile']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['viewMyProfile']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['viewMyProfile']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['usergroups']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['usergroups']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['logout']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['logout']['name']; ?>
						</td>
					</tr>
				</table>
				<?php
				}
				else {
				?>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumMenu['register']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumMenu['register']['name']; ?>
						</td>
					</tr>
				</table>
				<?php
				}
				?>
			</div>
			<?php
				if($forumAdminMenu = $menu->generateForumAdminMenu())
				{
			?>
			<div onclick="menuReset()" id="forumAdminMenu" class="menuHeadingBox" style="left: 67px;">
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<?php
					if(isset($forumAdminMenu['addForum'])) {
					?>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumAdminMenu['addForum']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumAdminMenu['addForum']['name']; ?>
						</td>
					</tr>
					<?php
					}
					if(isset($forumAdminMenu['editForum'])) {
					?>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumAdminMenu['editForum']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumAdminMenu['editForum']['name']; ?>
						</td>
					</tr>
					<?php
					}
					if(isset($forumAdminMenu['deleteForum'])) {
					?>
					<tr>
						<td class="menuElement" onclick="<?php echo $forumAdminMenu['deleteForum']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumAdminMenu['deleteForum']['name']; ?>
						</td>
					</tr>
					<?php
					}
					?>
				</table>
				<?php
				if($forumVariables['adminInlogged']) {
				?>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumAdminMenu['addForumGroup']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumAdminMenu['addForumGroup']['name']; ?>
						</td>
					</tr>
				</table>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumAdminMenu['editForumGroup']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumAdminMenu['editForumGroup']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumAdminMenu['forumPermissions']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumAdminMenu['forumPermissions']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $forumAdminMenu['forumManagement']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $forumAdminMenu['forumManagement']['name']; ?>
						</td>
					</tr>
				</table>
				<?php
				}
				?>
			</div>
			<?php
			}
			if($generalAdminMenu = $menu->generateGeneralAdminMenu()) {
			?>
			<div onclick="menuReset()" id="generalAdminMenu" class="menuHeadingBox" style="left: 170px;">
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['addUsergroup']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['addUsergroup']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['editUsergroup']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['editUsergroup']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['addBBCode']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['addBBCode']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['BBCodeManagement']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['BBCodeManagement']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['addSmilie']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['addSmilie']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['addSmilieFolder']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['addSmilieFolder']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['smilieManagement']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['smilieManagement']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['addAvatar']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['addAvatar']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['addAvatarFolder']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['addAvatarFolder']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['avatarManagement']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['avatarManagement']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['addCensor']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['addCensor']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['censorManagement']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['censorManagement']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['memberPermissions']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['memberPermissions']['name']; ?>
						</td>
					</tr>
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['usergroupPermissions']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['usergroupPermissions']['name']; ?>
						</td>
					</tr>
				</table>
				<hr/>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="menuElement" onclick="<?php echo $generalAdminMenu['forumSettings']['onClick']; ?>" onmouseover="menuElementChangeColorIn(this);" onmouseout="menuElementChangeColorOut(this);">
							<?php echo $generalAdminMenu['forumSettings']['name']; ?>
						</td>
					</tr>
				</table>
			</div>
			<?php
			}
			?>
		</div>
		<table width="100%" cellspacing="2" border ="0" cellpadding="0" class="menuHeadingTable" onclick="menuReset();">
			<tr>
				<td class="menuHeading">
					<a href="index.php" class="headingTextLink"><?php echo $forumSettings['forumName']; ?></a> - <i><?php echo $forumSettings['forumSlogan']; ?></i>
				</td>
			</tr>
		</table>
		<table id="menuTable" width="100%" cellspacing="2" cellpadding="0" border="0" class="menuTable">
			<tr>
				<td class="menuHeadingForum" onmouseover="menuHeadingBoxChangeColorIn(this,'forumMenu');" onmouseout="menuHeadingBoxChangeColorOut(this,'forumMenu');" onclick="toggleMenu(this,'forumMenu');">
					<?php echo $lang['forum']; ?>
				</td>
				<?php
				if($forumVariables['adminInlogged'] || (isset($moderatorInlogged) && $page = "threads.php")) {
				?>
				<td class="menuHeadingAdmin" onmouseover="menuHeadingBoxChangeColorIn(this, 'forumAdminMenu');" onmouseout="menuHeadingBoxChangeColorOut(this,'forumAdminMenu');" onclick="toggleMenu(this,'forumAdminMenu');">
					<?php if($forumVariables['adminInlogged']) echo $lang['forumAdmin']; else echo $lang['moderator']; ?>
				</td>
				<td class="menuHeadingGeneralAdmin" onmouseover="menuHeadingBoxChangeColorIn(this, 'generalAdminMenu');" onmouseout="menuHeadingBoxChangeColorOut(this,'generalAdminMenu');" onclick="toggleMenu(this,'generalAdminMenu');">
					<?php echo $lang['generalAdmin']; ?>
				</td>
				<?php
				}
				?>
				<td class="menuLogin" onclick="menuReset();">
					<?php
					if($forumVariables['inlogged']) {
						echo "<a href=\"".$page."?logOut=true";
						if(isset($_GET['id']))
							echo "&amp;id=".$_GET['id'];
						echo "\"><b>".$lang['logout']."</b></a> <i>".$forumVariables['inloggedUserName']."</i>";
					}
					else {
					?>
					<form id="login" action="<?php echo $page; if(isset($_GET['id'])) echo "?id=".$_GET['id']; ?>" method="post"><div><?php echo $lang['username']; ?>: <input type="text" name="loginUsername" onkeyup="onEnter(event);" value="<?php if(isset($_COOKIE['forumLastUser'])) echo $_COOKIE['forumLastUser']; ?>" class="menuLoginTextfeilds"/> <?php echo $lang['password']; ?>: <input type="password" name="loginPassword" onkeyup="onEnter(event);"  class="menuLoginTextfeilds"/> <a href="javascript:document.getElementById('login').submit();" title="<?php echo $lang['login']; ?>"><b><?php echo $lang['login']; ?> &gt;&gt;</b></a>&nbsp;&nbsp;<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript: popup('addMember.php',800,600);<?php } else { ?>addMember.php<?php } ?>"><i><?php echo $lang['register']; ?></i></a></div></form>
					<?php
					}
					?>
				</td>
				<td id="menuTitle" class="menuTitle" onclick="menuReset();">
					<i>&copy; Forum Senaculum</i>
				</td>
			</tr>
		</table>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" onclick="menuReset();">
			<tr>
				<td valign="top" class="menuForumLogo" title="<?php echo $forumSettings['forumSlogan']; ?>">
					<?php
					if($forumVariables['inlogged'] && $page != "PMs.php") {
						$newPM = $PM->checkNew();
						if($newPM) {
					?>
					<table width="100%" cellpadding="3" cellspacing="0" border="0" onclick="menuReset();">
						<tr>
							<td>
								&nbsp;
							</td>
							<td class="menuPM" valign="top" id="PM"<?php if(preg_match("�MSIE�i", $_SERVER['HTTP_USER_AGENT'])) echo " style=\"filter: Alpha(opacity=100);\""; ?>>
								<a href="PMs.php" class="link" onmouseover="fadeIn(70,100,'PM');" onmouseout="fadeOut(100,70,'PM');">
								<?php
								if($newPM == 1)
									echo $lang['haveGotNewPM1']." ".$newPM." ".$lang['haveGotNewPM2']."</a>";
								else
									echo $lang['haveGotNewPM1']." ".$newPM." ".$lang['haveGotNewPM3']."</a>";
								?>
							</td>
						</tr>
					</table>
					<script type="text/javascript">
						fadeIn(0,70,'PM');
					</script>
					<?php
						}
					}
					if($forumVariables['inlogged']) {
						require_once("./classes/postHandler.php");
						$posts = new postHandler;
						$newPosts = $posts->newPostReplyCount();
						if($newPosts) {
					?>
					<table width="100%" cellpadding="3" cellspacing="0" border="0" onclick="menuReset();">
						<tr>
							<td>
								&nbsp;
							</td>
							<td class="menuPM" valign="top" id="newPosts"<?php if(preg_match("�MSIE�i", $_SERVER['HTTP_USER_AGENT'])) echo " style=\"filter: Alpha(opacity=100);\""; ?>>
								<a href="search.php?keyword=&mode=new" class="link" onmouseover="fadeIn(70,100,'newPosts');" onmouseout="fadeOut(100,70,'newPosts');">
								<?php
								if($newPosts == 1)
									echo $lang['haveGotNewPostReplies1']." ".$newPosts." ".$lang['haveGotNewPostReplies2']."</a>";
								else
									echo $lang['haveGotNewPostReplies1']." ".$newPosts." ".$lang['haveGotNewPostReplies3']."</a>";
								?>
							</td>
						</tr>
					</table>
					<script type="text/javascript">
						fadeIn(0,70,'newPosts');
					</script>
					<?php
						}
					}
					?>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td valign="top">
