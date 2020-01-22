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

require_once('classes/menuHandler.php');
require_once('classes/searchHandler.php');
require_once('classes/other.php');

global $lang;

$menu = new menuHandler;
$search = new searchHandler;
$other = new other;

if(empty($_GET['page']))
	$pageNum = 1;
else
	$pageNum = $_GET['page'];
$limit = $forumSettings['threadsPerPage'];
$startRow = ($pageNum - 1) * $limit;

$menu->getTop();
$keyword = "";
if(!empty($_POST['keyword'])) {
	$searchs = $search->search($_POST['keyword'],$_POST['mode'],$startRow,$limit);
	$keyword = $_POST['keyword'];
	$mode = $_POST['mode'];
}
elseif(isset($_GET['keyword']) && !empty($_GET['mode'])) {
	$alowedSearch = true;
	if(!$forumVariables['inlogged'] && ($_GET['mode'] == "my" || $_GET['mode'] == "new"))
		$alowedSearch = false;
	if($alowedSearch)
		$searchs = $search->search($_GET['keyword'],$_GET['mode'],$startRow,$limit);
	$keyword = $_GET['keyword'];
	$mode = $_GET['mode'];
}
if(empty($mode))
	$mode = "";

//Paginate
if(empty($searchs[0]['numRows']))
	$numRows = 0;
else
	$numRows = $searchs[0]['numRows'];

$getVariables[0]['name'] = "keyword";
$getVariables[0]['value'] = $keyword;
$getVariables[1]['name'] = "mode";
$getVariables[1]['value'] = $mode;
$paginate = $other->paginate($pageNum, $numRows, $limit, "search.php", $getVariables);
if($numRows == 0)
	$numOfPages = 1;
else
	$numOfPages = ceil($numRows / $limit);
?>

<table style="width:100%;" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="searchHeadingThread">
			<?php echo $lang['thread']; ?>:
		</td>
		<td class="searchHeadingForum">
			<?php echo $lang['forum']; ?>:
		</td>
		<td class="searchHeadingReplies">
			<?php echo $lang['replies']; ?>:
		</td>
		<td class="searchHeadingOwner">
			<?php echo $lang['threadOwner']; ?>:
		</td>
		<td class="searchHeadingLastPost">
			<?php echo $lang['lastPost']; ?>:
		</td>
	</tr>
</table>
<table style="width:100%;" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td class="searchHeadingSearch">
			<?php
			if(empty($searchs)) {
				if($mode == "new")
					echo $lang['noNewPosts'];
				elseif($mode == "my")
					echo $lang['youHaveNoPosts'];
				elseif($mode == "unanswered")
					echo $lang['noUnansweredPosts'];
				else
					echo $lang['searchLookingFor'];
			}
			else
				echo $lang['XThreadsWasFound1'].$numRows.$lang['XThreadsWasFound2'];
			?>
		</td>
	</tr>
</table>
<?php
if($mode != "new" && $mode != "my" && $mode != "unanswered") {
?>
<script type="text/javascript">
	function viewSearchInput() {
		if(document.getElementById('searchInput').style.display == 'none')
			document.getElementById('searchInput').style.display = 'block';
		else
			document.getElementById('searchInput').style.display = 'none';
	}
</script>
<form id="search" action="search.php" method="get">
	<div class="searchInputArea" id="searchInput"<?php if(!empty($searchs)) echo " style=\"display:none;\""; ?>>
		<table cellpadding="0" cellspacing="10">
			<tr>
				<td align="left" valign="top" class="guideBoxHeading">
					<?php echo $lang['query']; ?>:<br/>
					<table cellspacing="0" cellpadding="3" class="guideInputArea">
						<tr>
							<td class="guideInputs">
								<?php echo $lang['keyword']; ?>:<br/>
								<input type="text" name="keyword" size="40" value="<?php echo htmlentities($keyword); ?>" class="guideTextFields"/><br>
							</td>
						</tr>
					</table>
				</td>
				<td align="left" valign="top" class="guideBoxHeading">
					<?php echo $lang['options']; ?>:<br/>
					<table cellspacing="0" cellpadding="3" class="guideInputArea">
						<tr>
							<td class="guideInputs">
								<b><?php echo $lang['searchFor']; ?>:</b><br/>
								<input type="radio" name="mode" value="posts"<?php if($mode != "postsAndAuthors" && $mode != "authors") echo " checked=\"checked\"" ?>/> <?php echo $lang['posts']; ?>
								<input type="radio" name="mode" value="postsAndAuthors"<?php if($mode == "postsAndAuthors") echo " checked=\"checked\""; ?>/> <?php echo $lang['postsAndAuthors']; ?>
								<input type="radio" name="mode" value="authors"<?php if($mode == "authors") echo " checked"; ?>/> <?php echo $lang['authors']; ?><br/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td class="guideButtonBar" colspan="2">
					<hr/>
					<table width="100%" border="0">
						<tr>
							<td align="center" valign="bottom">
								<input name="action" type="submit" class="guideButton" value="<?php echo $lang['search']; ?>"/><br/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</form>
<div class="searchViewHide" id="searchView">
	<a href="javascript:viewSearchInput();"><?php echo $lang['viewHideSearchForm']; ?></a>
</div>
<?php
}
if(!empty($searchs)) {
?>
<div class="pageViewAreaTop">
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td class="pageView">
				<?php echo $lang['pageOf1']." ".$pageNum." ".$lang['pageOf2']." ".$numOfPages ?>
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
</div>
<table style="width:100%;" cellpadding="2" cellspacing="0" border="0">
<?php
	$i = 0;
	foreach($searchs as $currentSearchElement) {
		if($i % 2 == 0) {
?>
	<tr>
		<td class="searchItem1Status">
			<?php
			if($currentSearchElement['newPosts'] == 0) {
				if($currentSearchElement['status'] == 1) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusLockedOwn" title="<?php echo $lang['locked']; ?>  - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusLocked" title="<?php echo $lang['locked']; ?>  - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
				}
				elseif($currentSearchElement['type'] == 0) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusOwn" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatus" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
				}
				else if($currentSearchElement['type'] == 1) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusStickyOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusSticky" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
				}
				else if($currentSearchElement['type'] == 2) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusAnnouncementOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusAnnouncement" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
				}
			}
			else {
				if($currentSearchElement['status'] == 1) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusLockedNewPostsOwn" title="<?php echo $lang['locked']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusLockedNewPosts" title="<?php echo $lang['locked']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
				}
				elseif($currentSearchElement['type'] == 0) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusNewPostsOwn" title="<?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusNewPosts" title="<?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
				}
				else if($currentSearchElement['type'] == 1) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusStickyNewPostsOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusStickyNewPosts" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
				}
				else if($currentSearchElement['type'] == 2) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusAnnouncementNewPostsOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusAnnouncementNewPosts" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
				}
			}
			?>
		</td>
		<td class="searchItem1Thread">
			<a href="posts.php?id=<?php echo $currentSearchElement['threadID']; ?>&amp;highlight=<?php echo htmlentities(urlencode($keyword)); ?>"><?php echo $currentSearchElement['threadHeadline']; ?></a>
			<?php
			if($currentSearchElement['poll'])
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"threadListPoll\">[".$lang['poll']."]</span>";
			echo "<br/>";
			if($currentSearchElement['posts'] > $forumSettings['postsPerPage']) {
				$getVariables2[0]['name'] = "id";
				$getVariables2[0]['value'] = $currentSearchElement['threadID'];
				$getVariables2[1]['name'] = "highlight";
				$getVariables2[1]['value'] = $keyword;
				echo "\n<span class=\"searchThreadGoto\">".$lang['gotoPage'].": </span><span class=\"searchThreadPaginate\">".$other->paginate2($currentSearchElement['posts'], $forumSettings['postsPerPage'], "posts.php", $getVariables2)."</span>&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if($mode != "new" && $mode != "my" && $mode != "unanswered") {
			?>
			<span class="searchThreadFirstMatchLabel"><?php echo $lang['firstMatch']; ?>: </span><span class="searchThreadFirstMatch"><a href="posts.php?id=<?php echo $currentSearchElement['threadID']; ?>&amp;pid=<?php echo $currentSearchElement['postID']; ?>&amp;highlight=<?php echo $keyword; ?>#<?php echo $currentSearchElement['postID']; ?>"><i>#<?php echo $currentSearchElement['postID']; ?></i></a></span>
			<?php
			}
			?>
		</td>
		<td class="searchItem1Forum">
			<a href="threads.php?id=<?php echo $currentSearchElement['forumID']; ?>"><?php echo $currentSearchElement['forumName']; ?></a>
		</td>
		<td class="searchItem1Replies">
			<?php echo ($currentSearchElement['posts']-1); ?>
		</td>
		<td class="searchItem1Owner">
			<?php
			if($currentSearchElement['threadOwnerID'] != 2) {
			?>
			<a href="profile.php?id=<?php echo $currentSearchElement['threadOwnerID']; ?>"><?php echo $currentSearchElement['threadOwnerUserName']; ?></a>
			<?php
			}
			elseif(!empty($currentSearchElement['threadOwnerGuestName']))
				echo $currentSearchElement['threadOwnerGuestName'];
			else
				echo $lang['guest'];
			?>
		</td>
		<td class="searchItem1LastPost">
			<?php
			if(empty($currentSearchElement['lastPost']))
				echo $lang['noPosts'];
			else {
				echo "<a href=\"posts.php?id=".$currentSearchElement['lastPostThreadID']."&amp;pid=".$currentSearchElement['lastPostID']."#".$currentSearchElement['lastPostID']."\">".$other->dateParse($forumVariables['dateFormat'], $currentSearchElement['lastPost'])."</a><br/>\n";
				if($currentSearchElement['lastPostMemberID'] != 2)
					echo "<a href=\"profile.php?id=".$currentSearchElement['lastPostMemberID']."\">".$currentSearchElement['lastPostUserName']."</a>";
				elseif(!empty($currentSearchElement['lastPostGuestName']))
					echo $currentSearchElement['lastPostGuestName'];
				else
					echo $lang['guest'];
			}
			?>
		</td>
	</tr>
	<?php
		}
		else {
	?>
	<tr>
		<td class="searchItem2Status">
			<?php
			if($currentSearchElement['newPosts'] == 0) {
				if($currentSearchElement['status'] == 1) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusLockedOwn" title="<?php echo $lang['locked']; ?>  - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusLocked" title="<?php echo $lang['locked']; ?>  - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
				}
				elseif($currentSearchElement['type'] == 0) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusOwn" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatus" title="<?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
				}
				else if($currentSearchElement['type'] == 1) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusStickyOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusSticky" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
				}
				else if($currentSearchElement['type'] == 2) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusAnnouncementOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusAnnouncement" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['noNewPosts']; ?>">&nbsp;</div>
			<?php
					}
				}
			}
			else {
				if($currentSearchElement['status'] == 1) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusLockedNewPostsOwn" title="<?php echo $lang['locked']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusLockedNewPosts" title="<?php echo $lang['locked']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
				}
				elseif($currentSearchElement['type'] == 0) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusNewPostsOwn" title="<?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusNewPosts" title="<?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
				}
				else if($currentSearchElement['type'] == 1) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusStickyNewPostsOwn" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusStickyNewPosts" title="<?php echo $lang['sticky']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
				}
				else if($currentSearchElement['type'] == 2) {
					if($currentSearchElement['ownPostsInThread']) {
			?>
			<div class="iconStatusAnnouncementNewPostsOwn" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
					else {
			?>
			<div class="iconStatusAnnouncementNewPosts" title="<?php echo $lang['announcement']; ?> - <?php echo $lang['newPosts1'].$currentSearchElement['newPosts'].$lang['newPosts2']; ?>">&nbsp;</div>
			<?php
					}
				}
			}
			?>
		</td>
		<td class="searchItem2Thread">
			<a href="posts.php?id=<?php echo $currentSearchElement['threadID']; ?>&amp;highlight=<?php echo htmlentities(urlencode($keyword)); ?>"><?php echo $currentSearchElement['threadHeadline']; ?></a>
			<?php
			if($currentSearchElement['poll'])
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"threadListPoll\">[".$lang['poll']."]</span>";
			echo "<br/>";
			if($currentSearchElement['posts'] > $forumSettings['postsPerPage']) {
				$getVariables2[0]['name'] = "id";
				$getVariables2[0]['value'] = $currentSearchElement['threadID'];
				$getVariables2[1]['name'] = "highlight";
				$getVariables2[1]['value'] = $keyword;
				echo "\n<span class=\"searchThreadGoto\">".$lang['gotoPage'].": </span><span class=\"searchThreadPaginate\">".$other->paginate2($currentSearchElement['posts'], $forumSettings['postsPerPage'], "posts.php", $getVariables2)."</span>&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if($mode != "new" && $mode != "my" && $mode != "unanswered") {
			?>
			<span class="searchThreadFirstMatchLabel"><?php echo $lang['firstMatch']; ?>: </span><span class="searchThreadFirstMatch"><a href="posts.php?id=<?php echo $currentSearchElement['threadID']; ?>&amp;pid=<?php echo $currentSearchElement['postID']; ?>&amp;highlight=<?php echo $keyword; ?>#<?php echo $currentSearchElement['postID']; ?>"><i>#<?php echo $currentSearchElement['postID']; ?></i></a></span>
			<?php
			}
			?>
		</td>
		<td class="searchItem2Forum">
			<a href="threads.php?id=<?php echo $currentSearchElement['forumID']; ?>"><?php echo $currentSearchElement['forumName']; ?></a>
		</td>
		<td class="searchItem2Replies">
			<?php echo ($currentSearchElement['posts']-1); ?>
		</td>
		<td class="searchItem2Owner">
			<?php
			if($currentSearchElement['threadOwnerID'] != 2) {
			?>
			<a href="profile.php?id=<?php echo $currentSearchElement['threadOwnerID']; ?>"><?php echo $currentSearchElement['threadOwnerUserName']; ?></a>
			<?php
			}
			elseif(!empty($currentSearchElement['threadOwnerGuestName']))
				echo $currentSearchElement['threadOwnerGuestName'];
			else
				echo $lang['guest'];
			?>
		</td>
		<td class="searchItem2LastPost">
			<?php
			if(empty($currentSearchElement['lastPost']))
				echo $lang['noPosts'];
			else {
				echo "<a href=\"posts.php?id=".$currentSearchElement['lastPostThreadID']."&amp;pid=".$currentSearchElement['lastPostID']."#".$currentSearchElement['lastPostID']."\">".$other->dateParse($forumVariables['dateFormat'], $currentSearchElement['lastPost'])."</a><br/>\n";
				if($currentSearchElement['lastPostMemberID'] != 2)
					echo "<a href=\"profile.php?id=".$currentSearchElement['lastPostMemberID']."\">".$currentSearchElement['lastPostUserName']."</a>";
				elseif(!empty($currentSearchElement['lastPostGuestName']))
					echo $currentSearchElement['lastPostGuestName'];
				else
					echo $lang['guest'];
			}
			?>
		</td>
	</tr>
	<?php
 		}
 		$i++;
 	}
	?>
</table>
<div class="pageViewAreaBottom">
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td class="pageView">
				<?php echo $lang['pageOf1']." ".$pageNum." ".$lang['pageOf2']." ".$numOfPages ?>
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
</div>
<br/>
<div align="center">
	<table cellpadding="0" cellspacing="0" style="width:50%;">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatus">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['noNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusNewPosts">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['newPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusAnnouncement">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['announcementNoNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusAnnouncementNewPosts">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['announcementNewPosts']; ?>
						</td>
					</tr>
				</table>
			</td>
			<td align="right">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusLocked">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['lockedNoNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusLockedNewPosts">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['lockedNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusSticky">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['stickyNoNewPosts']; ?>
						</td>
					</tr>
					<tr>
						<td class="iconinfoIcon">
							<div class="iconStatusStickyNewPosts">&nbsp;</div>
						</td>
						<td class="iconinfoText">
							<?php echo $lang['stickyNewPosts']; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<?php
}

$menu->getBottom();
?>