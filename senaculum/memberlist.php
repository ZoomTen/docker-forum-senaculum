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
global $forumSettings;
global $lang;

require_once('classes/errorHandler.php');
require_once('classes/memberHandler.php');
require_once('classes/menuHandler.php');
require_once('classes/logInOutHandler.php');
require_once('classes/other.php');

$member = new memberHandler;
$error = new errorHandler;
$menu = new menuHandler;
$auth = new logInOutHandler;
$other = new other;

if(isset($_GET['sort']))
	$sort = $_GET['sort'];
else	
	$sort = 0;
if(isset($_GET['order']))
	$order = $_GET['order'];
else		
	$order = 0;

if(empty($_GET['page']))
	$pageNum = 1;
else
	$pageNum = $_GET['page'];		
$limit = $forumSettings['membersPerPage'];		
$startRow = ($pageNum - 1) * $limit;

if(isset($_GET['viewOnline']) && $forumSettings['activateOnline'])
	$members = $member->getAllOnline($sort,$order,$startRow,$limit);
else	
	$members = $member->getAll($sort,$order,$startRow,$limit);

//Paginate
if(empty($members[0]['numRows']))
	$numRows = 0;
else
	$numRows = $members[0]['numRows'];		
$paginate = $other->paginate($pageNum, $numRows, $limit, "memberlist.php");	
if($numRows == 0)
	$numOfPages = 1;
else	
	$numOfPages = ceil($numRows / $limit);	
	
$menu->getTop();

?> 

<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="memberlistHeadingUsername">
			<?php echo $lang['username']; ?>:
		</td>
		<td class="memberlistHeadingPM">
			<?php echo $lang['privateMessage']; ?>:
		</td>
		<td class="memberlistHeadingEmail">
			<?php echo $lang['email']; ?>:
		</td>
		<td class="memberlistHeadingLocation">
			<?php echo $lang['location']; ?>:
		</td>
		<td class="memberlistHeadingHomepage">
			<?php echo $lang['website']; ?>:
		</td>
		<td class="memberlistHeadingPosts">
			<?php echo $lang['posts']; ?>:
		</td>
		<td class="memberlistHeadingAction">
			<?php echo $lang['action']; ?>:
		</td>
	</tr>
</table>
<table width="100%" cellpadding="2" cellspacing="0" border="0">		
	<tr>
		<td class="memberlistCountMembersHeading">
			<?php 
				if(isset($_GET['viewOnline']) && $forumSettings['activateOnline'])
					echo $lang['forumHaveXMembersOnline1'].$numRows.$lang['forumHaveXMembersOnline2'];
				else
					echo $lang['forumHaveXMembers1'].$numRows.$lang['forumHaveXMembers2']; 
			?>
		</td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">	
	<tr>
		<td class="pageViewAreaTop">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="pageView">
						<?php echo $lang['pageOf1']." ".$pageNum." ".$lang['pageOf2']." ".$numOfPages ?>
					</td>
					<td class="pageSort">
						<?php echo $lang['sort']; ?>:
						<select name="sort" class="pageDropDown" onchange="window.location = 'memberlist.php?sort='+this.options[this.selectedIndex].value+'&amp;order=<?php echo $order; if(isset($_GET['viewOnline'])) echo "&amp;viewOnline=1"; ?>'">
							<option value="1" class="pageDropDownOption1" <?php if($sort == 1) echo "selected"; ?>><?php echo $lang['registered']; ?></option>
							<option value="2" class="pageDropDownOption2" <?php if($sort == 2) echo "selected"; ?>><?php echo $lang['username']; ?></option>
							<option value="3" class="pageDropDownOption1" <?php if($sort == 3) echo "selected"; ?>><?php echo $lang['location']; ?></option>
							<option value="4" class="pageDropDownOption2" <?php if($sort == 4) echo "selected"; ?>><?php echo $lang['totalPosts']; ?></option>
							<option value="5" class="pageDropDownOption1" <?php if($sort == 5) echo "selected"; ?>><?php echo $lang['email']; ?></option>
							<option value="6" class="pageDropDownOption2" <?php if($sort == 6) echo "selected"; ?>><?php echo $lang['website']; ?></option>
							<option value="7" class="pageDropDownOption1" <?php if($sort == 7) echo "selected"; ?>><?php echo $lang['topTenPosters']; ?></option>
						</select>
					</td>
					<td class="pageSort">
						<?php echo $lang['order']; ?>:
						<select name="sort" class="pageDropDown" onchange="window.location = 'memberlist.php?sort=<?php echo $sort; if(isset($_GET['viewOnline'])) echo "&amp;viewOnline=1"; ?>&amp;order='+this.options[this.selectedIndex].value">
							<option value="1" class="pageDropDownOption1" <?php if($order == 1) echo "selected"; ?>><?php echo $lang['ascending']; ?></option>
							<option value="2" class="pageDropDownOption2" <?php if($order == 2) echo "selected"; ?>><?php echo $lang['descending']; ?></option>
						</select>
					</td>
					<td class="pageGoto">
						<?php 
						if(!empty($paginate))
							echo "<b>".$lang['gotoPage'].":</b> ".$paginate;
						else
							echo "&nbsp;";	 
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>		
	<?php
	if($members) {
	?>
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<?php
		$i = 0;
		foreach($members as $currentMemberValue) {
			if($i % 2 == 0) {
	?>
	<tr>
		<td class="memberlistItem1Username">
			<a href="profile.php?id=<?php echo $currentMemberValue['memberID'] ?>" class="bigLink"><?php echo $currentMemberValue['userName']; ?></a>
		</td>
		<td class="memberlistItem1PM">
			<a href="<?php if($forumSettings['guidesInPopups']) echo "javascript:popup('addPM.php?id=".$currentMemberValue['memberID']."',800,600);"; else echo "addPM.php?id=".$currentMemberValue['memberID']; ?>" class="actionLink2"><?php echo $lang['PM']; ?></a>
		</td>
		<td class="memberlistItem1Email">
			<?php 
				if(!empty($currentMemberValue['email'])) { 
			?>
			<a href="mailto:<?php echo $currentMemberValue['email'] ?>" class="link"><?php echo $currentMemberValue['email']; ?></a>
			<?php
				}
				else
					echo "&nbsp;";
			?>
		</td>
		<td class="memberlistItem1Location">
			<?php echo $currentMemberValue['location'] ?>
		</td>
		<td class="memberlistItem1Homepage">
			<?php 
				if(!empty($currentMemberValue['homepage'])) { 
			?>
			<a href="<?php echo $currentMemberValue['homepage'] ?>" target="_blank" class="link"><?php echo $currentMemberValue['homepage']; ?></a>
			<?php
				}
				else
					echo "&nbsp;";
			?>
		</td>
		<td class="memberlistItem1Posts">
			<?php echo $currentMemberValue['posts']; ?>
		</td>
		<?php 
				if(!$forumVariables['inlogged'])
				{
		?>
		<td class="memberlistItem1Action">&nbsp;
		</td>
		<?php
				}
				elseif($forumVariables['superAdminInlogged'] || ($forumVariables['adminInlogged'] && $currentMemberValue['memberID']!=1 && !$currentMemberValue['admin'])){ ?>
		<td class="memberlistItem1Action">
			<a href="javascript:<?php if($forumSettings['guidesInPopups']) { ?>popup('editProfile.php?id=<?php echo $currentMemberValue['memberID'] ?>',800,600);<?php } else { ?>window.location = 'editProfile.php?id=<?php echo $currentMemberValue['memberID'] ?>';<?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
			<!--<a href="javascript:confirmProcess('Do you really want to delete this user?','memberlist.php?id=<?php// echo $currentMemberValue['memberID']; ?>');" class="actionLink2">Delete</a>-->
		</td>
		<?php 
				}
				else { ?>
		<td class="memberlistItem1Action">&nbsp;
		</td>
		<?php 
				} 
		?>
	</tr>
	<?php
			}
			else {
	?>	
	<tr>
		<td class="memberlistItem2Username">
			<a href="profile.php?id=<?php echo $currentMemberValue['memberID'] ?>" class="bigLink"><?php echo $currentMemberValue['userName']; ?></a>
		</td>
		<td class="memberlistItem2PM">
			<a href="<?php if($forumSettings['guidesInPopups']) echo "javascript:popup('addPM.php?id=".$currentMemberValue['memberID']."',800,600);"; else echo "addPM.php?id=".$currentMemberValue['memberID']; ?>" class="actionLink2"><?php echo $lang['PM']; ?></a>
		</td>
		<td class="memberlistItem2Email">
			<?php 
				if(!empty($currentMemberValue['email'])) { 
			?>
			<a href="mailto:<?php echo $currentMemberValue['email'] ?>" class="link"><?php echo $currentMemberValue['email']; ?></a>
			<?php
				}
				else
					echo "&nbsp;";
			?>
		</td>
		<td class="memberlistItem2Location">
			<?php echo $currentMemberValue['location'] ?>
		</td>
		<td class="memberlistItem2Homepage">
			<?php 
				if(!empty($currentMemberValue['homepage'])) { 
			?>
			<a href="<?php echo $currentMemberValue['homepage'] ?>" target="_blank" class="link"><?php echo $currentMemberValue['homepage']; ?></a>
			<?php
				}
				else
					echo "&nbsp;";
			?>
		</td>
		<td class="memberlistItem2Posts">
			<?php echo $currentMemberValue['posts']; ?>
		</td>
		<?php
			if(!$forumVariables['inlogged'])
			{
		?>
		<td class="memberlistItem2Action">&nbsp;
		</td>
		<?php
			}
			elseif($forumVariables['superAdminInlogged'] || ($forumVariables['adminInlogged'] && $currentMemberValue['memberID']!=1 && !$currentMemberValue['admin']) || ($currentMemberValue['memberID'] == $forumVariables['inloggedMemberID'])){ ?>
			<td class="memberlistItem2Action">
			<a href="javascript:<?php if($forumSettings['guidesInPopups']) { ?>popup('editProfile.php?id=<?php echo $currentMemberValue['memberID'] ?>',800,600);<?php } else { ?>window.location = 'editProfile.php?id=<?php echo $currentMemberValue['memberID'] ?>';<?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
			<!--<a href="javascript:confirmProcess('Do you really want to delete this user?','memberlist.php?id=<?php// echo $currentMemberValue['memberID']; ?>');" class="actionLink2">Delete</a>-->
			</td>
		<?php 
			}
			else { ?>
			<td class="memberlistItem2Action">&nbsp;
			</td>
			<?php
			}
	?>
	</tr>
	<?php
			}
			$i++;
		}
?>
</table>
<?php		
	}
	else {	
	?> 
<div align="center"><?php echo $lang['noMembers']; ?></div>	
	<?php
	}
	?>		
<table width="100%" cellpadding="0" cellspacing="0" border="0">	
	<tr>
		<td class="pageViewAreaTop">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="pageView">
						<?php echo $lang['pageOf1']." ".$pageNum." ".$lang['pageOf2']." ".$numOfPages ?>
					</td>
					<td class="pageSort">
						<?php echo $lang['sort']; ?>:
						<select name="sort" class="pageDropDown" onchange="window.location = 'memberlist.php?sort='+this.options[this.selectedIndex].value+'&amp;order=<?php echo $order; if(isset($_GET['viewOnline'])) echo "&amp;viewOnline=1"; ?>'">
							<option value="1" class="pageDropDownOption1" <?php if($sort == 1) echo "selected"; ?>><?php echo $lang['registered']; ?></option>
							<option value="2" class="pageDropDownOption2" <?php if($sort == 2) echo "selected"; ?>><?php echo $lang['username']; ?></option>
							<option value="3" class="pageDropDownOption1" <?php if($sort == 3) echo "selected"; ?>><?php echo $lang['location']; ?></option>
							<option value="4" class="pageDropDownOption2" <?php if($sort == 4) echo "selected"; ?>><?php echo $lang['totalPosts']; ?></option>
							<option value="5" class="pageDropDownOption1" <?php if($sort == 5) echo "selected"; ?>><?php echo $lang['email']; ?></option>
							<option value="6" class="pageDropDownOption2" <?php if($sort == 6) echo "selected"; ?>><?php echo $lang['website']; ?></option>
							<option value="7" class="pageDropDownOption1" <?php if($sort == 7) echo "selected"; ?>><?php echo $lang['topTenPosters']; ?></option>
						</select>
					</td>
					<td class="pageSort">
						<?php echo $lang['order']; ?>:
						<select name="sort" class="pageDropDown" onchange="window.location = 'memberlist.php?sort=<?php echo $sort; if(isset($_GET['viewOnline'])) echo "&amp;viewOnline=1"; ?>&amp;order='+this.options[this.selectedIndex].value">
							<option value="1" class="pageDropDownOption1" <?php if($order == 1) echo "selected"; ?>><?php echo $lang['ascending']; ?></option>
							<option value="2" class="pageDropDownOption2" <?php if($order == 2) echo "selected"; ?>><?php echo $lang['descending']; ?></option>
						</select>
					</td>
					<td class="pageGoto">
						<?php 
						if(!empty($paginate))
							echo "<b>".$lang['gotoPage'].":</b> ".$paginate;
						else
							echo "&nbsp;";	 
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>	

<?php
$menu->getBottom();
//echo $masterCount;
?> 