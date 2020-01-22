<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

class menuHandler
	{
		function menuHandler(){
			$this->forumMenuElements = false;
			$this->forumAdminMenuElements = false;
			$this->generalAdminMenuElements = false;
		}

		function getTop()
		{
			require_once("./menu/default/menuTop.php");
		}

		function getBottom() {
			require_once("./menu/default/menuBottom.php");
		}

		function getMenuElement($name) {
			if(!$this->forumMenuElements)
				$this->generateForumMenu();
			if(isset($this->forumMenuElements[$name]))
				return $this->forumMenuElements[$name];

			if(!$this->forumAdminMenuElements)
				$this->generateForumAdminMenu();
			if(isset($this->forumAdminMenuElements[$name]))
				return $this->forumAdminMenuElements[$name];

			if(!$this->generalAdminMenuElements)
				$this->generateGeneralAdminMenu();
			if(isset($this->generalAdminMenuElements[$name]))
				return $this->generalAdminMenuElements[$name];

			return false;
		}
		function generateForumMenu() {
			global $lang;
			global $forumVariables;
			global $forumSettings;
			$page = substr(strrchr($_SERVER['SCRIPT_NAME'],'/'),1);
			$menuElements = array();

			switch($page) {
			case "threads.php":
				if($forumSettings['guidesInPopups']) {
					$onClick = "popup('addThread.php?id=".$_GET['id']."',800,600);";
					$link = "javascript: ".$onClick;
				}
				else {
					$onClick = "window.location = 'addThread.php?id=".$_GET['id']."';";
					$link = "addThread.php?id=".$_GET['id'];
				}
				$menuItem = $lang['newThread'];
				$showMenuItem = true;
				break;
			case "posts.php":
				if($forumSettings['guidesInPopups'])  {
					$onClick = "popup('addPost.php?id=".$_GET['id']."',800,600);";
					$link = "javascript: ".$onClick;
				}
				else {
					$onClick = "window.location = 'addPost.php?id=".$_GET['id']."';";
					$link = "addPost.php?id=".$_GET['id'];
				}
				$menuItem = $lang['newPost'];
				$showMenuItem = true;
				break;
			default:
				$showMenuItem = false;
			}
			if($showMenuItem) {
				$menuElements['addPost']['name'] = $menuItem;
				$menuElements['addPost']['onClick'] = $onClick;
				$menuElements['addPost']['link'] = $link;
			}

			$menuElements['forumIndex']['name'] = $lang['forumIndex'];
			$menuElements['forumIndex']['onClick'] = "window.location='index.php'";
			$menuElements['forumIndex']['link'] = "index.php";

			$menuElements['members']['name'] = $lang['members'];
			$menuElements['members']['onClick'] = "window.location='memberlist.php'";
			$menuElements['members']['link'] = "memberlist.php";

			if($forumSettings['activateOnline']) {
				$menuElements['membersOnline']['name'] = $lang['membersOnline'];
				$menuElements['membersOnline']['onClick'] = "window.location='memberlist.php?viewOnline=1'";
				$menuElements['membersOnline']['link'] = "memberlist.php?viewOnline=1";
			}

			$menuElements['search']['name'] = $lang['search'];
			$menuElements['search']['onClick'] = "window.location='search.php'";
			$menuElements['search']['link'] = "search.php";

			if($forumVariables['inlogged']) {
				$menuElements['pm']['name'] = $lang['pm'];
				$menuElements['pm']['onClick'] = "window.location='PMs.php'";
				$menuElements['pm']['link'] = "PMs.php";

				$menuElements['bookmarks']['name'] = $lang['bookmarks'];
				$menuElements['bookmarks']['onClick'] = "window.location='bookmarks.php'";
				$menuElements['bookmarks']['link'] = "bookmarks.php";

				$menuElements['viewNewPosts']['name'] = $lang['viewNewPosts'];
				$menuElements['viewNewPosts']['onClick'] = "window.location='search.php?keyword=&mode=new'";
				$menuElements['viewNewPosts']['link'] = "search.php?keyword=&mode=new";

				$menuElements['viewMyPosts']['name'] = $lang['viewMyPosts'];
				$menuElements['viewMyPosts']['onClick'] = "window.location='search.php?keyword=&mode=my'";
				$menuElements['viewMyPosts']['link'] = "search.php?keyword=&mode=my";
			}

			$menuElements['viewUnansweredPosts']['name'] = $lang['viewUnansweredPosts'];
			$menuElements['viewUnansweredPosts']['onClick'] = "window.location='search.php?keyword=&mode=unanswered'";
			$menuElements['viewUnansweredPosts']['link'] = "search.php?keyword=&mode=unanswered";

			if($forumVariables['inlogged']) {
				$menuElements['editMyProfile']['name'] = $lang['editMyProfile'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['editMyProfile']['onClick'] = "popup('editProfile.php',800,600);";
					$menuElements['editMyProfile']['link'] = "javascript: popup('editProfile.php',800,600);";
				}
				else {
					$menuElements['editMyProfile']['onClick'] = "window.location = 'editProfile.php';";
					$menuElements['editMyProfile']['link'] = "editProfile.php";
				}

				$menuElements['viewMyProfile']['name'] = $lang['viewMyProfile'];
				$menuElements['viewMyProfile']['onClick'] = "window.location='profile.php?id=".$forumVariables['inloggedMemberID']."';";
				$menuElements['viewMyProfile']['link'] = "profile.php?id=".$forumVariables['inloggedMemberID'];

				$menuElements['usergroups']['name'] = $lang['usergroups'];
				$menuElements['usergroups']['onClick'] = "window.location='memberGroupList.php';";
				$menuElements['usergroups']['link'] = "memberGroupList.php";

				$menuElements['logout']['name'] = $lang['logout'];
				$menuElements['logout']['onClick'] = "window.location='".$page."?logOut=true";
				$menuElements['logout']['link'] = $page."?logOut=true";
				if(!empty($_GET['id'])) {
					$menuElements['logout']['onClick'] .= "?id=".$_GET['id'];
					$menuElements['logout']['link'] .= "?id=".$_GET['id'];
				}
				$menuElements['logout']['onClick'] .= "';";

			}
			else {
				$menuElements['register']['name'] = $lang['register'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['register']['onClick'] = "popup('addMember.php',800,600);";
					$menuElements['register']['link'] = "javascript: popup('addMember.php',800,600);";
				}
				else {
					$menuElements['register']['onClick'] = "window.location = 'addMember.php';";
					$menuElements['register']['link'] = "addMember.php";
				}
			}
			$this->forumMenuElements = $menuElements;
			return $menuElements;
		}

		function generateForumAdminMenu() {
			global $lang;
			global $forumVariables;
			global $forumSettings;
			$page = substr(strrchr($_SERVER['SCRIPT_NAME'],'/'),1);
			if($page == "threads.php") {
				require_once('./classes/logInOutHandler.php');
				$login = new logInOutHandler;
				if($login->moderator($_GET['id'],"all"))
					$moderatorInlogged = true;
			}
			$menuElements = array();

			if($forumVariables['adminInlogged'] || (isset($moderatorInlogged) && $page == "threads.php")) {
				if($forumVariables['adminInlogged']) {
					$menuElements['addForum']['name'] = $lang['addForum'];
					if($forumSettings['guidesInPopups']) {
						$menuElements['addForum']['onClick'] = "popup('addForum.php',800,600);";
						$menuElements['addForum']['link'] = "javascript: popup('addForum.php',800,600);";
					}
					else {
						$menuElements['addForum']['onClick'] = "window.location = 'addForum.php';";
						$menuElements['addForum']['link'] = "addForum.php";
					}
				}

				if($page == "threads.php") {
					$menuElements['editForum']['name'] = $lang['editForum'];
					if($forumSettings['guidesInPopups']) {
						$menuElements['editForum']['onClick'] = "popup('editForum.php?id=".$_GET['id']."',800,600);";
						$menuElements['editForum']['link'] = "javascript: popup('editForum.php?id=".$_GET['id']."',800,600);";
					}
					else {
						$menuElements['editForum']['onClick'] = "window.location = 'editForum.php?id=".$_GET['id']."';";
						$menuElements['editForum']['link'] = "editForum.php?id=".$_GET['id'];
					}

					if($forumVariables['adminInlogged']) {
						$menuElements['deleteForum']['name'] = $lang['deleteForum'];
						$menuElements['deleteForum']['onClick'] = "confirmProcess('".$lang['deleteForumConfirm']."','index.php?delete=".$_GET['id']."');";
						$menuElements['deleteForum']['link'] = "javascript: confirmProcess('".$lang['deleteForumConfirm']."','index.php?delete=".$_GET['id']."');";
					}
				}

				if($forumVariables['adminInlogged']) {
					$menuElements['addForumGroup']['name'] = $lang['addForumGroup'];
					if($forumSettings['guidesInPopups']) {
						$menuElements['addForumGroup']['onClick'] = "popup('addForumGroup.php',800,600);";
						$menuElements['addForumGroup']['link'] = "javascript: popup('addForumGroup.php',800,600);";
					}
					else {
						$menuElements['addForumGroup']['onClick'] = "window.location = 'addForumGroup.php';";
						$menuElements['addForumGroup']['link'] = "addForumGroup.php";
					}

					$menuElements['editForumGroup']['name'] = $lang['editForumGroup'];
					if($forumSettings['guidesInPopups']) {
						$menuElements['editForumGroup']['onClick'] = "popup('editForumGroup.php',800,600);";
						$menuElements['editForumGroup']['link'] = "javascript: popup('editForumGroup.php',800,600);";
					}
					else {
						$menuElements['editForumGroup']['onClick'] = "window.location = 'editForumGroup.php';";
						$menuElements['editForumGroup']['link'] = "editForumGroup.php";
					}

					$menuElements['forumPermissions']['name'] = $lang['forumPermissions'];
					if($forumSettings['guidesInPopups']) {
						$menuElements['forumPermissions']['onClick'] = "popup('forumPermissions.php',950,600);";
						$menuElements['forumPermissions']['link'] = "javascript: popup('forumPermissions.php',950,600);";
					}
					else {
						$menuElements['forumPermissions']['onClick'] = "window.location = 'forumPermissions.php';";
						$menuElements['forumPermissions']['link'] = "forumPermissions.php";
					}

					$menuElements['forumManagement']['name'] = $lang['forumManagement'];
					$menuElements['forumManagement']['onClick'] = "window.location = 'forumManagement.php';";
					$menuElements['forumManagement']['link'] = "forumManagement.php";
				}
			}

			if(!empty($menuElements)) {
				$this->forumAdminMenuElements = $menuElements;
				return $menuElements;
			}
			else {
				$this->forumAdminMenuElements = false;
				return false;
			}
		}

		function generateGeneralAdminMenu() {
			global $lang;
			global $forumVariables;
			global $forumSettings;

			$menuElements = array();

			if($forumVariables['adminInlogged']) {
				$menuElements['addUsergroup']['name'] = $lang['addUsergroup'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['addUsergroup']['onClick'] = "popup('addMemberGroup.php',800,600);";
					$menuElements['addUsergroup']['link'] = "javascript: popup('addMemberGroup.php',800,600);";
				}
				else {
					$menuElements['addUsergroup']['onClick'] = "window.location = 'addMemberGroup.php';";
					$menuElements['addUsergroup']['link'] = "addMemberGroup.php";
				}

				$menuElements['editUsergroup']['name'] = $lang['editUsergroup'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['editUsergroup']['onClick'] = "popup('editMemberGroup.php',800,600);";
					$menuElements['editUsergroup']['link'] = "javascript: popup('editMemberGroup.php',800,600);";
				}
				else {
					$menuElements['editUsergroup']['onClick'] = "window.location = 'editMemberGroup.php';";
					$menuElements['editUsergroup']['link'] = "editMemberGroup.php";
				}

				$menuElements['addBBCode']['name'] = $lang['addBBCode'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['addBBCode']['onClick'] = "popup('addBBCode.php',800,600);";
					$menuElements['addBBCode']['link'] = "javascript: popup('addBBCode.php',800,600);";
				}
				else {
					$menuElements['addBBCode']['onClick'] = "window.location = 'addBBCode.php';";
					$menuElements['addBBCode']['onClick'] = "addBBCode.php";
				}

				$menuElements['BBCodeManagement']['name'] = $lang['BBCodeManagement'];
				$menuElements['BBCodeManagement']['onClick'] = "window.location = 'BBCodeManagement.php';";
				$menuElements['BBCodeManagement']['link'] = "BBCodeManagement.php";

				$menuElements['addSmilie']['name'] = $lang['addSmilie'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['addSmilie']['onClick'] = "popup('addSmilie.php',800,600);";
					$menuElements['addSmilie']['link'] = "javascript: popup('addSmilie.php',800,600);";
				}
				else {
					$menuElements['addSmilie']['onClick'] = "window.location = 'addSmilie.php';";
					$menuElements['addSmilie']['link'] = "addSmilie.php";
				}

				$menuElements['addSmilieFolder']['name'] = $lang['addSmilieFolder'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['addSmilieFolder']['onClick'] = "popup('addFolderSmilie.php',800,600);";
					$menuElements['addSmilieFolder']['link'] = "javascript: popup('addFolderSmilie.php',800,600);";
				}
				else {
					$menuElements['addSmilieFolder']['onClick'] = "window.location = 'addFolderSmilie.php';";
					$menuElements['addSmilieFolder']['link'] = "addFolderSmilie.php";
				}

				$menuElements['smilieManagement']['name'] = $lang['smilieManagement'];
				$menuElements['smilieManagement']['onClick'] = "window.location = 'smilieManagement.php';";
				$menuElements['smilieManagement']['link'] = "smilieManagement.php";

				$menuElements['addAvatar']['name'] = $lang['addAvatar'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['addAvatar']['onClick'] = "popup('addAvatar.php',800,600);";
					$menuElements['addAvatar']['link'] = "javascript: popup('addAvatar.php',800,600);";
				}
				else {
					$menuElements['addAvatar']['onClick'] = "window.location = 'addAvatar.php';";
					$menuElements['addAvatar']['link'] = "addAvatar.php";
				}

				$menuElements['addAvatarFolder']['name'] = $lang['addAvatarFolder'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['addAvatarFolder']['onClick'] = "popup('addFolderAvatar.php',800,600);";
					$menuElements['addAvatarFolder']['link'] = "javascript: popup('addFolderAvatar.php',800,600);";
				}
				else {
					$menuElements['addAvatarFolder']['onClick'] = "window.location = 'addFolderAvatar.php';";
					$menuElements['addAvatarFolder']['link'] = "addFolderAvatar.php";
				}

				$menuElements['avatarManagement']['name'] = $lang['avatarManagement'];
				$menuElements['avatarManagement']['onClick'] = "window.location = 'avatarManagement.php';";
				$menuElements['avatarManagement']['link'] = "avatarManagement.php";

				$menuElements['addCensor']['name'] = $lang['addCensor'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['addCensor']['onClick'] = "popup('addCensur.php',800,600);";
					$menuElements['addCensor']['link'] = "javascript: popup('addCensur.php',800,600);";
				}
				else {
					$menuElements['addCensor']['onClick'] = "window.location = 'addCensur.php';";
					$menuElements['addCensor']['link'] = "addCensur.php";
				}

				$menuElements['censorManagement']['name'] = $lang['censorManagement'];
				$menuElements['censorManagement']['onClick'] = "window.location = 'censurManagement.php';";
				$menuElements['censorManagement']['link'] = "censurManagement.php";

				$menuElements['memberPermissions']['name'] = $lang['memberPermissions'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['memberPermissions']['onClick'] = "popup('memberPermissions.php',950,600);";
					$menuElements['memberPermissions']['link'] = "javascript: popup('memberPermissions.php',950,600);";
				}
				else {
					$menuElements['memberPermissions']['onClick'] = "window.location = 'memberPermissions.php';";
					$menuElements['memberPermissions']['link'] = "memberPermissions.php";
				}

				$menuElements['usergroupPermissions']['name'] = $lang['usergroupPermissions'];
				if($forumSettings['guidesInPopups']) {
					$menuElements['usergroupPermissions']['onClick'] = "popup('memberGroupPermissions.php',950,600);";
					$menuElements['usergroupPermissions']['link'] = "javascript: popup('memberGroupPermissions.php',950,600);";
				}
				else {
					$menuElements['usergroupPermissions']['onClick'] = "window.location = 'memberGroupPermissions.php';";
					$menuElements['usergroupPermissions']['link'] = "memberGroupPermissions.php";
				}

				$menuElements['forumSettings']['name'] = $lang['forumSettings'];
				$menuElements['forumSettings']['onClick'] = "window.location = 'settings.php';";
				$menuElements['forumSettings']['link'] = "settings.php";

				$this->generalAdminMenuElements = $menuElements;
				return $menuElements;
			}
			else {
				$this->generalAdminMenuElements = false;
				return false;
			}
		}
	}
?>
