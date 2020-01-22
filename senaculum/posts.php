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

require_once('classes/menuHandler.php');
require_once('classes/postHandler.php');
require_once('classes/threadHandler.php');
require_once('classes/errorHandler.php');
require_once('classes/logInOutHandler.php');
require_once('classes/permissionHandler.php');
require_once('classes/other.php');
require_once('classes/moderatorHandler.php');

$menu = new menuHandler;
$post = new postHandler;
$thread = new threadHandler;
$error = new errorHandler;
$auth = new logInOutHandler;
$permission = new permissionHandler;
$other = new other;
$moderator = new moderatorHandler;

if(isset($_GET['bookmark'])) {
	require_once('classes/bookmarkHandler.php');
	$bookmark = new bookmarkHandler;
	$bookmark->add($_GET['id']);
	global $alert;
	$alert = $lang['bookmarkAdded'];
}

if(empty($_GET['page']))
	$pageNum = 1;
else
	$pageNum = $_GET['page'];
$limit = $forumSettings['postsPerPage'];
$startRow = ($pageNum - 1) * $limit;

//Get sort
if(empty($_GET['sort']))
	$sort=null;
else
	$sort=$_GET['sort'];

if(isset($_GET['id'])) {
	if(!empty($_POST['pollOptionVote'])) {
		$thread->pollVote($_GET['id'], $_POST['pollOptionVote']);
	}

	$currentThread = $thread->getOne($_GET['id'], false);
	if(empty($currentThread))
	{
		$error->error($lang['threadNotExist1'],$lang['threadNotExist2']);
	}
	if(isset($_GET['lock']) && ($auth->moderator($currentThread['forumID'],"edit") || $forumVariables['adminInlogged'])) {
		$thread->lock($_GET['id']);
		header("location: posts.php?id=".$_GET['id']."&page=".$pageNum."&alert=".$lang['threadIsLocked']);
	}
	if(isset($_GET['unlock']) && ($auth->moderator($currentThread['forumID'],"edit") || $forumVariables['adminInlogged'])) {
		$thread->unlock($_GET['id']);
		header("location: posts.php?id=".$_GET['id']."&page=".$pageNum."&alert=".$lang['threadIsUnlocked']);
	}
	if(isset($_POST['moveThreadAction']) && !empty($_POST['moveThread'])) {
		if(isset($_POST['moveShadow']))
			$shadow = true;
		else
			$shadow = false;
		$thread->move($_GET['id'], $currentThread['forumID'], $_POST['moveThread'], $shadow);
		header("location: posts.php?id=".$_GET['id']."&page=".$pageNum."&alert=".$lang['threadIsMoved']);
	}
	if(isset($_POST['splitThreadAction'])) {
		if(!empty($_POST['newThreadHeadline']) && !empty($_POST['splitThreadTo']) && !empty($_POST['splitFrom'])) {
			$thread->split($_POST['splitThreadTo'], $currentThread['forumID'], $currentThread['threadID'], $_POST['splitFrom'], $_POST['newThreadHeadline'], $sort);
			header("location: posts.php?id=".$_GET['id']."&page=".$pageNum."&alert=".$lang['threadIsSplitted']);
		}
		else
			header("location: posts.php?id=".$_GET['id']."&page=".$pageNum."&alert=".$lang['splitThreadFillFields']);
	}

	if(isset($_GET['sort']))
	{
		$posts = $post->getAll($_GET['id'], $_GET['sort'], $startRow, $limit);
	}
	else
	{
		if(!empty($_GET['pid']))
			$posts = $post->getAll($_GET['id'], 1, $startRow, $limit, $_GET['pid']);
		else
			$posts = $post->getAll($_GET['id'], 1, $startRow, $limit);
	}
}
else
	$error->error($lang['wrongURL'],$lang['URLChanged']);

//Paginate
if(!empty($posts[0]['page']))
	$pageNum = $posts[0]['page'];
$numRows = $currentThread['posts'];
$getVariables[0]['name'] = "id";
$getVariables[0]['value'] = $_GET['id'];
$getVariables[1]['name'] = "sort";
$getVariables[1]['value'] = $sort;
if(!empty($_GET['highlight'])) {
	$getVariables[2]['name'] = "highlight";
	$getVariables[2]['value'] = $_GET['highlight'];
}
$paginate = $other->paginate($pageNum, $numRows, $limit, "posts.php", $getVariables);
if($numRows == 0)
	$numOfPages = 1;
else
	$numOfPages = ceil($numRows / $limit);

$permissionRead = $permission->permission($currentThread['forumID'],"read");
if(!$permissionRead) {
	$currentThread['forumName'] = "Forum Name";
	$currentThread['headline'] = "Thread Headline";
}
$permissionEdit = $permission->permission($currentThread['forumID'],"edit");

$permissionDelete = $permission->permission($currentThread['forumID'],"delete");
$permissionVote = $permission->permission($currentThread['forumID'],"vote");
$permissionPost = $permission->permission($currentThread['forumID'],"post");

if(isset($_POST['send']) && !empty($_POST['fastAnswer']) && $permissionPost) {
	if($currentThread['status'] != 1 || $forumVariables['adminInlogged'] || $auth->moderator($forumID,"edit")) {
		require_once("classes/control.php");
		$control = new control;
		$errorText=$control->text($_POST['fastAnswer'], 1, 8000);
		$errorTimeLimit=$control->postTimeLimit($_GET['id']);
		if(empty($errorText)&&empty($errorTimeLimit)) {
			if($forumVariables['alwaysAllowBBCode'])
				$disableBBCode = false;
			else
				$disableBBCode = true;
			if($forumVariables['alwaysAllowSmilies'])
				$disableSmilies = false;
			else
				$disableSmilies = true;
			$notifyWhenReply = $forumVariables['alwaysNotifyOnReply'];
			$attachSign = $forumVariables['alwaysDisplaySign'];

			$newPostId = $post->add("", $_POST['fastAnswer'], $_GET['id'], $disableBBCode, $disableSmilies, $notifyWhenReply, $attachSign);
			header("location: posts.php?id=".$_GET['id']."&pid=".$newPostId."#".$newPostId);
		}
		else {
			$fastAnswer = $_POST['fastAnswer'];
			global $alert;
			$alert = $errorText;
		}
	}
}
elseif(isset($_POST['fastAnswerPreview'])) {
	$_SESSION['forumFastAnswerTemp'] = $_POST['fastAnswer'];
	if($forumSettings['guidesInPopups']) {
		global $runJavascript;
		$runJavascript[] = "popup('addPost.php?id=".$_GET['id']."&preview=1',800,600);";
	}
	else {
		header("location: addPost.php?id=".$_GET['id']."&preview=1");
	}
}

$menu->getTop();

$forumID = $currentThread['forumID'];

if(!empty($_GET['highlight'])) { //Highlight text when search
	$i=0;
	foreach($posts as $element) {
		$highlights = preg_replace("§[\+\-\(\)\"]§i"," ",$_GET['highlight']); 	//Erase search chars
		$highlights = str_replace("  "," ",$highlights);		//Erase gaps
		$highlights = explode(" ",$highlights);
		$element['headline'] = " ".$element['headline']." ";
		foreach($highlights as $highlight) {
			if(preg_match("§\*§i",$highlight)) {
				$highlightstar = str_replace("*","",$highlight);
				$element['headline'] = preg_replace("§".$highlightstar."([^[]+?) §i","<span style=\"background-color:#00FF00;\">".$highlightstar."$1</span> ",$element['headline']);
			}
			else
				$element['headline'] = preg_replace("§ ".$highlight."([^a-zA-Z0-9]?) §i"," <span style=\"background-color:#00FF00;\">".$highlight."</span>$1 ",$element['headline']);
		}
		$textLength = strlen($element['headline']);
		$element['headline'] = substr_replace($element['headline'],"",0,0); //Delete the inserted whitespaces
		$element['headline'] = substr_replace($element['headline'],"",$textLength-1,0);
		$posts[$i]['headline'] = $element['headline'];
		$element['text'] = " ".$element['text']." ";
		foreach($highlights as $highlight) {
			if(preg_match("§\*§i",$highlight)) {
				$highlightstar = str_replace("*","",$highlight);
				$element['text'] = preg_replace("§".$highlightstar."([^[]?) §i","<span style=\"background-color:#00FF00;\">".$highlightstar."$1</span> ",$element['text']);
			}
			else
				$element['text'] = preg_replace("§ ".$highlight."([^a-zA-Z0-9]?) §i"," <span style=\"background-color:#00FF00;\">".$highlight."</span>$1 ",$element['text']);
		}
		$textLength = strlen($element['text']);
		$element['text'] = substr_replace($element['text'],"",0,0); //Delete the inserted whitespaces
		$element['text'] = substr_replace($element['text'],"",$textLength-1,0);
		$posts[$i]['text'] = $element['text'];
		$i++;
	}
}
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<table width="100%" cellpadding="2" cellspacing="0">
				<tr>
					<td class="postListHeadingPost">
						<?php echo $lang['post']; ?>:
					</td>
					<td class="postListHeadingAuthor">
						<?php echo $lang['author']; ?>:
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="middle" class="postListThreadHeading">
			<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" valign="middle">
						<span class="postListThreadHeadingName"><?php echo "<a href=\"index.php\">".$forumSettings['forumName']."</a> &gt; <a href=\"forumGroup.php?id=".$currentThread['forumGroupID']."\">".$currentThread['forumGroupName']."</a> &gt; <a href=\"threads.php?id=".$currentThread['forumID']."\">".$currentThread['forumName']."</a> &gt; <a href=\"posts.php?id=".$currentThread['threadID']."\">".$currentThread['headline']."</a>"; ?></span>
						<span class="postListThreadHeadingOwner"><?php echo $lang['owner']; ?>: <?php if($currentThread['memberID'] != 2) {?><a href="profile.php?id=<?php echo $currentThread['memberID']; ?>"><?php } ?><?php echo $currentThread['memberName']; ?><?php if($currentThread['memberID'] != 2) { ?></a><?php } ?></span>
					</td>
					<td class="postListThreadHeadingNewPost">
						<?php
						if($forumVariables['inlogged']) {
						?>
						<a href="posts.php?id=<?php echo $_GET['id']; ?>&amp;sort=<?php echo $sort; ?>&amp;bookmark=1" class="actionLink"><?php echo $lang['addBookmark']; ?></a> |
						<?php
						}
						if($currentThread['status'] != 1) {
						?>
						<a href="javascript:<?php if($forumSettings['guidesInPopups']) { ?>popup('addPost.php?id=<?php echo $currentThread['threadID']; ?>',800,600);<?php } else {?> window.location = 'addPost.php?id=<?php echo $currentThread['threadID']; ?>';<?php } ?>" class="actionLink"><?php echo $lang['newPost']; ?></a>
						<?php
						}
						elseif($forumVariables['adminInlogged'] || $auth->moderator($forumID,"edit")) {
						?>
						<a href="javascript:<?php if($forumSettings['guidesInPopups']) { ?>popup('addPost.php?id=<?php echo $currentThread['threadID']; ?>',800,600);<?php } else {?> window.location = 'addPost.php?id=<?php echo $currentThread['threadID']; ?>';<?php } ?>" class="actionLink"><?php echo $lang['newPostLocked']; ?></a>
						<?php
						}
						else {
						?>
						<a href="javascript:alert('<?php echo $lang['replyLockedMessage']; ?>');" class="actionLink"><?php echo $lang['newPostLocked']; ?></a>
						<?php
						}
						if($forumVariables['adminInlogged'] || $auth->moderator($forumID,"delete")) {
						?>
						| <a href="javascript:confirmProcess('<?php echo $lang['confirmDeleteThread']; ?>','posts.php?id=<?php echo $currentThread['threadID']."&amp;dt=".$currentThread['threadID']; ?>');" class="actionLink"><?php echo $lang['deleteThread']; ?></a>
						<?php
						}
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
	if(!$permissionRead) {
	?>
	<tr>
		<td align="center">
			<?php echo $lang['noPermissionReadForum']; ?>
		</td>
	</tr>
	<?php
	$menu->getBottom();
	die();
	}
	?>
	<tr>
		<td class="pageViewAreaTop">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="pageView">
						<?php echo $lang['pageOf1']." ".$pageNum." ".$lang['pageOf2']." ".$numOfPages ?>
					</td>
					<td class="pageSort">
						<?php echo $lang['sort']; ?>:
						<select class="pageDropDown" onchange="window.location='posts.php?id=<?php echo $_GET['id'];?>&amp;sort='+this.options[this.selectedIndex].value;">
							<option value="1" class="pageDropDownOption1" <?php if($sort==1){echo "selected";} ?>><?php echo $lang['oldest']; ?></option>
							<option value="2" class="pageDropDownOption2" <?php if($sort==2){echo "selected";} ?>><?php echo $lang['newest']; ?></option>
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
	<?php
	if($currentThread['poll']) {
	?>
	<tr>
		<td class="postListPollArea">
			<?php
			if(!$currentThread['pollVoted'] && $permissionVote)
				echo "<form action=\"posts.php?id=".$_GET['id']."&amp;sort=".$sort."&amp;page=".$pageNum."\" method=\"post\">\n";
			echo "<b>".$currentThread['pollQuestion']."</b><br/><br/>";
			$totalPollVotes = 0;
			foreach($currentThread['pollOptions'] as $pollOption) {
				$totalPollVotes += $pollOption['votes'];
			}
			?>
			<div align="center">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="postListPollOptions">
			<?php
			foreach($currentThread['pollOptions'] as $pollOption) {
				if($totalPollVotes == 0)
					$optionProcent = 0;
				else
					$optionProcent = round(($pollOption['votes'] / $totalPollVotes)* 100);
				if(!$currentThread['pollVoted'] && $permissionVote)
					echo "<input type=\"radio\" name=\"pollOptionVote\" value=\"".$pollOption['optionID']."\"/> ";
				echo $pollOption['option']." - <b>".$optionProcent."%</b> ( ".$pollOption['votes']." )<br/>\n";
			}
			?>
						</td>
					</tr>
				</table>
			</div>
			<?php
			echo "<br/><b>".$lang['totalVotes'].": ".$totalPollVotes."</b>\n";
			if(!$currentThread['pollVoted'] && $permissionVote) {
				echo "<br/><br/><input type=\"submit\" class=\"guideButton\" value=\"".$lang['vote']."\"/>\n";
				echo "</form>\n";
			}
			?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td>
			<table width="100%" cellspacing="0" cellpadding="3">
<?php
if($sort == 2)
	$i = count($posts) - 1;
else
	$i = 0;

if(!empty($posts)) {
	foreach($posts as $currentPostValue) {
		if($i % 2 == 0) {
?>
				<tr>
					<td class="postListHeading1">
						<a name="<?php echo $currentPostValue['postID']?>"></a>
						<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" valign="middle">
									<span class="postListHeadingSubject"><?php echo $currentPostValue['headline']; ?></span> <span class="postListHeadingPosted"><?php echo $lang['posted']; ?>: <i><?php echo $other->dateParse($forumVariables['dateFormat'], $currentPostValue['date']); ?></i><?php if($currentPostValue['date'] != $currentPostValue['lastEdit']) { ?>&nbsp;&nbsp;(<?php echo $lang['lastEdited']; ?>: <i><?php echo $other->dateParse($forumVariables['dateFormat'], $currentPostValue['lastEdit']); ?></i>)<?php } ?></span>
								</td>
								<td class="postListHeadingAnswer" align="right" valign="middle">
									<?php
									if($currentThread['status'] != 1) {
									?>
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']; ?>',800,600);<?php } else {?>addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['reply']; ?></a> |
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']."&amp;quote=".$currentPostValue['postID']; ?>',800,600);<?php } else {?>addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']."&amp;quote=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['quote1']; ?></a>
									<?php
									}
									elseif($forumVariables['inlogged'] && ($forumVariables['adminInlogged'] || $auth->moderator($forumID,"post"))) {
									?>
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']; ?>',800,600);<?php } else {?>addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['replyLocked']; ?></a> |
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']."&amp;quote=".$currentPostValue['postID']; ?>',800,600);<?php } else {?>addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']."&amp;quote=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['quote1']; ?></a>
									<?php
									}
									else {
									?>
									<a href="javascript:alert('<?php echo $lang['replyLockedMessage']; ?>');" class="actionLink2"><?php echo $lang['replyLocked']; ?></a>
									<?php
									}
									if($forumVariables['inlogged'] && (($currentPostValue['authorID'] == $forumVariables['inloggedMemberID'] && $currentThread['status'] != 1) || $forumVariables['adminInlogged'] || $auth->moderator($forumID,"edit") || $auth->moderator($forumID,"delete"))) {
										if($i==0 && (($pageNum == 1 && ($sort == 1 || empty($sort))) || ($pageNum == $numOfPages && $sort == 2)) && $permissionEdit) {
									?>
									| <a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editThread.php?id=<?php echo $currentThread['threadID']."&amp;pid=".$currentPostValue['postID']; ?>',800,600);<?php } else { ?>editThread.php?id=<?php echo $currentThread['threadID']."&amp;pid=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['editThread']; ?></a>
									<?php
									}
										elseif($permissionEdit) {
									?>
									| <a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editPost.php?id=<?php echo $currentPostValue['postID']; ?>',800,600);<?php } else { ?>editPost.php?id=<?php echo $currentPostValue['postID']; ?>&amp;tid=<?php echo $currentThread['threadID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
									<?php
										}
										if($permissionDelete) {
											if($i==0 && (($pageNum == 1 && ($sort == 1 || empty($sort))) || ($pageNum == $numOfPages && $sort == 2))) {
												if($forumVariables['adminInlogged'] || $auth->moderator($forumID,"delete")) {
									?>
									| <a href="javascript:confirmProcess('<?php echo $lang['confirmDeleteThread']; ?>','posts.php?id=<?php echo $currentThread['threadID']."&amp;dt=".$currentThread['threadID']; ?>');" class="actionLink2"><?php echo $lang['deleteThread']; ?></a>
									<?php
												}
											}
											else {
									?>
									| <a href="javascript:confirmProcess('<?php echo $lang['confirmDeletePost']; ?>','posts.php?id=<?php echo $currentThread['threadID']."&amp;dp=".$currentPostValue['postID']; ?>');" class="actionLink2"><?php echo $lang['deletePost']; ?></a>
									<?php
											}
										}
									}
									?>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top" class="postListAuthor1" rowspan="3">
						<?php
						if($currentPostValue['authorID'] != 2) {
						?>
						<b><a href="profile.php?id=<?php echo $currentPostValue['authorID']; ?>"><?php echo $currentPostValue['authorName']; ?></a></b><br/>
						<?php
						}
						else {
						?>
						<?php if(!empty($currentPostValue['guestName'])) echo "<b>".$currentPostValue['guestName']."</b><br/>"; ?>
						<?php if($currentPostValue['deletedUser']) echo "<span class=\"postListDeletedUser\">".$lang['deletedUser']; else echo "<span class=\"postListGuest\">".$lang['guest']; ?></span><br/>
						<?php
						}
						if($currentPostValue['authorID'] != 2) {
							if($currentPostValue['admin']) {
						?>
						<span class="postListAdmin"><?php echo $lang['administrator']; ?></span><br/>
						<?php
							}
							elseif($moderator->checkModerator($currentPostValue['authorID'],$currentThread['forumID'])) {
						?>
								<span class="postListModerator"><?php echo $lang['moderator']; ?> </span><br/>
						<?php
							}
						}
						?>
						<br/>
						<?php if(!empty($currentPostValue['avatar'])) echo "<img src=\"images/avatars/".$currentPostValue['avatar']."\" alt=\"".$lang['avatar']."\"/>"; ?><br/><br/>
						<?php if($currentPostValue['authorID'] != 2) echo $lang['memberIn1'].floor((time() - $currentPostValue['authorRegisterDate']) / 86400).$lang['memberIn2']."<br/>"; ?>
						<?php if(!empty($currentPostValue['location'])) echo $lang['location'].": ".$currentPostValue['location']."<br/>"; ?>
						<?php
							if($forumSettings['activateOnline'] && $currentPostValue['authorID'] != 2) {
								echo $lang['status'].": ";
								if($currentPostValue['authorLastActive'] > time() - $forumSettings['onlineViewExpire'])
									echo $lang['online']."<br/>";
								else
									echo $lang['offline']."<br/>";
							}
						?>
						<div class="splitFromSelects" style="display:none;"><input type="radio" name="splitFromSelect" value="<?php echo $currentPostValue['postID']; ?>" onclick="document.getElementById('splitFrom').value = this.value;"/></div>
					</td>
				</tr>
				<tr>
					<td valign="top" class="postListMessage1">
						<?php echo $currentPostValue['text']; ?><br/>
						<br/>
					</td>
				</tr>
				<tr>
					<td valign="bottom" class="postListSignature1">
						<?php
						if(!empty($currentPostValue['signature']))
							echo "_______________________<br/>\n".$currentPostValue['signature'];
						?>
						<?php
							if($forumSettings['attachmentsActivated']) {
								require_once("classes/attachmentHandler.php");
								$attachment = new attachmentHandler;
								if($files = $attachment->get($currentPostValue['postID'])) {
						?>
						<br/>
						<div class="postListAttachmentArea">
							<table cellpadding="0" cellspacing="0" class="postListAttachmentTable">
								<tr>
									<td class="postListAttchmentLabel">
										<?php echo $lang['attachments'].":<br/>\n"; ?>
									</td>
								<tr>
							<?php
										$k = 0;
										foreach($files as $file) {
											if($filesize = @filesize("attachments/".$currentPostValue['postID']."/".$file)) {
												$filesize = round($filesize / 1024);
											}
											else
												$filesize = "";
											if($k % 2 == 0) {
							?>
								<tr>
									<td class="postListAttachmentList1">
							<?php
											}
											else {
							?>
								<tr>
									<td class="postListAttachmentList2">
							<?php
											}
							?>
										<a href="attachments/<?php echo $currentPostValue['postID']."/".$file; ?>" class="link"><?php echo $file ?></a> (<?php echo $filesize." ".$lang['KB']; ?>)<br/>
									</td>
								</tr>
							<?php
											$k++;
										}
							?>
							</table>
						</div>
						<?php
									}
							}
						?>
					</td>
				</tr>
<?php
		}
		else {
?>
				<tr>
					<td class="postListHeading2">
						<a name="<?php echo $currentPostValue['postID']?>"></a>
						<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" valign="middle">
									<span class="postListHeadingSubject"><?php echo $currentPostValue['headline']; ?></span> <span class="postListHeadingPosted"><?php echo $lang['posted']; ?>: <i><?php echo $other->dateParse($forumVariables['dateFormat'], $currentPostValue['date']); ?></i><?php if($currentPostValue['date'] != $currentPostValue['lastEdit']) { ?>&nbsp;&nbsp;(<?php echo $lang['lastEdited']; ?>: <i><?php echo $other->dateParse($forumVariables['dateFormat'], $currentPostValue['lastEdit']); ?></i>)<?php } ?></span>
								</td>
								<td class="postListHeadingAnswer" align="right" valign="middle">
									<?php
									if($currentThread['status'] != 1) {
									?>
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']; ?>',800,600);<?php } else {?>addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['reply']; ?></a> |
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']."&amp;quote=".$currentPostValue['postID']; ?>',800,600);<?php } else {?>addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']."&amp;quote=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['quote1']; ?></a>
									<?php
									}
									elseif($forumVariables['inlogged'] && ($forumVariables['adminInlogged'] || $auth->moderator($forumID,"post"))) {
									?>
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']; ?>',800,600);<?php } else {?>addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['replyLocked']; ?></a> |
									<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']."&amp;quote=".$currentPostValue['postID']; ?>',800,600);<?php } else {?>addPost.php?id=<?php echo $currentThread['threadID']."&amp;post=".$currentPostValue['postID']."&amp;quote=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['quote1']; ?></a>
									<?php
									}
									else {
									?>
									<a href="javascript:alert('<?php echo $lang['replyLockedMessage']; ?>');" class="actionLink2"><?php echo $lang['replyLocked']; ?></a>
									<?php
									}
									if($forumVariables['inlogged'] && (($currentPostValue['authorID'] == $forumVariables['inloggedMemberID'] && $currentThread['status'] != 1) || $forumVariables['adminInlogged'] || $auth->moderator($forumID,"edit") || $auth->moderator($forumID,"delete"))) {
										if($i==0 && (($pageNum == 1 && ($sort == 1 || empty($sort))) || ($pageNum == $numOfPages && $sort == 2)) && $permissionEdit) {
									?>
									| <a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editThread.php?id=<?php echo $currentThread['threadID']."&amp;pid=".$currentPostValue['postID']; ?>',800,600);<?php } else { ?>editThread.php?id=<?php echo $currentThread['threadID']."&amp;pid=".$currentPostValue['postID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['editThread']; ?></a>
									<?php
									}
										elseif($permissionEdit) {
									?>
									| <a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('editPost.php?id=<?php echo $currentPostValue['postID']; ?>',800,600);<?php } else { ?>editPost.php?id=<?php echo $currentPostValue['postID']; ?>&amp;tid=<?php echo $currentThread['threadID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['edit']; ?></a>
									<?php
										}
										if($permissionDelete) {
											if($i==0 && (($pageNum == 1 && ($sort == 1 || empty($sort))) || ($pageNum == $numOfPages && $sort == 2))) {
												if($forumVariables['adminInlogged'] || $auth->moderator($forumID,"delete")) {
									?>
									| <a href="javascript:confirmProcess('<?php echo $lang['confirmDeleteThread']; ?>','posts.php?id=<?php echo $currentThread['threadID']."&amp;dt=".$currentThread['threadID']; ?>');" class="actionLink2"><?php echo $lang['deleteThread']; ?></a>
									<?php
												}
											}
											else {
									?>
									| <a href="javascript:confirmProcess('<?php echo $lang['confirmDeletePost']; ?>','posts.php?id=<?php echo $currentThread['threadID']."&amp;dp=".$currentPostValue['postID']; ?>');" class="actionLink2"><?php echo $lang['deletePost']; ?></a>
									<?php
											}
										}
									}
									?>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top" class="postListAuthor2" rowspan="3">
						<?php
						if($currentPostValue['authorID'] != 2) {
						?>
						<b><a href="profile.php?id=<?php echo $currentPostValue['authorID']; ?>"><?php echo $currentPostValue['authorName']; ?></a></b><br/>
						<?php
						}
						else {
						?>
						<?php if(!empty($currentPostValue['guestName'])) echo "<b>".$currentPostValue['guestName']."</b><br/>"; ?>
						<?php if($currentPostValue['deletedUser']) echo "<span class=\"postListDeletedUser\">".$lang['deletedUser']; else echo "<span class=\"postListGuest\">".$lang['guest']; ?></span><br/>
						<?php
						}
						if($currentPostValue['authorID'] != 2) {
							if($currentPostValue['admin']) {
						?>
						<span class="postListAdmin"><?php echo $lang['administrator']; ?></span><br/>
						<?php
							}
							elseif($moderator->checkModerator($currentPostValue['authorID'],$currentThread['forumID'])) {
						?>
								<span class="postListModerator"><?php echo $lang['moderator']; ?> </span><br/>
						<?php
							}
						}
						?>
						<br/>
						<?php if(!empty($currentPostValue['avatar'])) echo "<img src=\"images/avatars/".$currentPostValue['avatar']."\" alt=\"".$lang['avatar']."\"/>"; ?><br/><br/>
						<?php if($currentPostValue['authorID'] != 2) echo $lang['memberIn1'].floor((time() - $currentPostValue['authorRegisterDate']) / 86400).$lang['memberIn2']."<br/>"; ?>
						<?php if(!empty($currentPostValue['location'])) echo $lang['location'].": ".$currentPostValue['location']."<br/>"; ?>
						<?php
							if($forumSettings['activateOnline'] && $currentPostValue['authorID'] != 2) {
								echo $lang['status'].": ";
								if($currentPostValue['authorLastActive'] > time() - $forumSettings['onlineViewExpire'])
									echo $lang['online']."<br/>";
								else
									echo $lang['offline']."<br/>";
							}
						?>
						<div class="splitFromSelects" style="display:none;"><input type="radio" name="splitFromSelect" value="<?php echo $currentPostValue['postID']; ?>" onclick="document.getElementById('splitFrom').value = this.value;"/></div>
					</td>
				</tr>
				<tr>
					<td valign="top" class="postListMessage2">
						<?php echo $currentPostValue['text']; ?><br/>
						<br/>
					</td>
				</tr>
				<tr>
					<td valign="bottom" class="postListSignature2">
						<?php
						if(!empty($currentPostValue['signature']))
							echo "_______________________<br/>\n".$currentPostValue['signature'];
						?>
						<?php
							if($forumSettings['attachmentsActivated']) {
								require_once("classes/attachmentHandler.php");
								$attachment = new attachmentHandler;
								if($files = $attachment->get($currentPostValue['postID'])) {
						?>
						<br/>
						<div class="postListAttachmentArea">
							<table cellpadding="0" cellspacing="0" class="postListAttachmentTable">
								<tr>
									<td class="postListAttchmentLabel">
										<?php echo $lang['attachments'].":<br/>\n"; ?>
									</td>
								<tr>
							<?php
										$k = 0;
										foreach($files as $file) {
											if($filesize = @filesize("attachments/".$currentPostValue['postID']."/".$file)) {
												$filesize = round($filesize / 1024);
											}
											else
												$filesize = "";
											if($k % 2 == 0) {
							?>
								<tr>
									<td class="postListAttachmentList1">
							<?php
											}
											else {
							?>
								<tr>
									<td class="postListAttachmentList2">
							<?php
											}
							?>
										<a href="attachments/<?php echo $currentPostValue['postID']."/".$file; ?>" class="link"><?php echo $file ?></a> (<?php echo $filesize." ".$lang['KB']; ?>)<br/>
									</td>
								</tr>
							<?php
											$k++;
										}
							?>
							</table>
						</div>
						<?php
									}
							}
						?>
					</td>
				</tr>
<?php
		}
		if($sort == 2)
			$i--;
		else
			$i++;
	}
}
else {
?>
				<tr>
					<td align="center" colspan="4">
						<?php echo $lang['noPosts']; ?>
					</td>
				</tr>
<?php
}
?>

			</table>
		</td>
	</tr>
	<tr>
		<td class="pageViewAreaBottom">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="pageView">
						<?php echo $lang['pageOf1']." ".$pageNum." ".$lang['pageOf2']." ".$numOfPages ?>
					</td>
					<td class="pageSort">
						<?php echo $lang['sort']; ?>:
						<select class="pageDropDown" onchange="window.location='posts.php?id=<?php echo $_GET['id'];?>&amp;sort='+this.options[this.selectedIndex].value;">
							<option value="1" class="pageDropDownOption1" <?php if($sort==1){echo "selected";} ?>><?php echo $lang['oldest']; ?></option>
							<option value="2" class="pageDropDownOption2" <?php if($sort==2){echo "selected";} ?>><?php echo $lang['newest']; ?></option>
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
	<?php
	if($forumVariables['adminInlogged']) {
		$delete = true;
		$edit = true;
	}
	else {
		$delete = $auth->moderator($forumID,"delete");
		$edit = $auth->moderator($forumID,"edit");
	}
	if($delete || $edit) {
	?>
	<tr>
		<td>
			<script type="text/javascript">
				function showHideElement(element) {
					radios = document.getElementsByTagName("div");

					if(document.getElementById(element).style.display == "none")
						document.getElementById(element).style.display = "block";
					else
						document.getElementById(element).style.display = "none";
					if(element == "moveThreadArea") {
						document.getElementById("splitThreadArea").style.display = "none";
						for (var i=0; i<radios.length; i++) {
							if (radios[i].className == "splitFromSelects")
								radios[i].style.display = "none";
						}
					}
					else
						document.getElementById("moveThreadArea").style.display = "none";

					if(document.getElementById("splitThreadArea").style.display == "none") {
						for (var i=0; i<radios.length; i++) {
							if (radios[i].className == "splitFromSelects")
								radios[i].style.display = "none";
						}
					}
					else {
						for (var i=0; i<radios.length; i++) {
							if (radios[i].className == "splitFromSelects")
								radios[i].style.display = "block";
						}
					}
				}

				function splitAction() {
					for(i=0;i<document.getElementById("splitFromSelect").length;i++){
						if (document.splitFromSelect[i].checked==true)
							document.getElementById("splitFrom").value = document.splitFromSelect[i].value;
					}
				}
			</script>
			<div class="postListThreadManagement">
				<?php
				if($delete) {
				?>
				<a href="javascript:confirmProcess('<?php echo $lang['confirmDeleteThread']; ?>','posts.php?id=<?php echo $currentThread['threadID']."&amp;dt=".$currentThread['threadID']; ?>');" class="actionLink2"><?php echo $lang['deleteThread']; ?></a>
				<?php
				}
				if($edit) {
				?>
				| <a href="javascript:showHideElement('moveThreadArea');" class="actionLink2"><?php echo $lang['moveThread']; ?></a>
				<?php
				if($currentThread['status'] == 1) {
				?>
				| <a href="posts.php?id=<?php echo $currentThread['threadID']; ?>&amp;page=<?php echo $pageNum; ?>&amp;unlock=" class="actionLink2"><?php echo $lang['unlockThread']; ?></a>
				<?php
				}
				else {
				?>
				| <a href="posts.php?id=<?php echo $currentThread['threadID']; ?>&amp;page=<?php echo $pageNum; ?>&amp;lock=" class="actionLink2"><?php echo $lang['lockThread']; ?></a>
				<?php
				}
				?>
				| <a href="javascript:showHideElement('splitThreadArea');" class="actionLink2"><?php echo $lang['splitThread']; ?></a>
				<?php
				}
				?>
				<div id="moveThreadArea" style="display:none;">
					<?php
					require_once("classes/forumHandler.php");
					$forum = new forumHandler;
					$forums = $forum->getAllSimple();
					?>
					<br/>
					<form action="posts.php?id=<?php echo $_GET['id']; ?>&amp;page=<?php echo $pageNum; ?>" method="post">
						<?php echo $lang['moveTo']; ?> <select name="moveThread">
							<?php
							foreach($forums as $element) {
							?>
							<option value="<?php echo $element['forumID']; ?>"><?php echo $element['name']; ?></option>
							<?php
							}
							?>
						</select><br/>
						<input type="checkbox" name="moveShadow" checked="checked"/> <?php echo $lang['leaveShadowThread']; ?><br/>
						<input type="submit" name="moveThreadAction" value="<?php echo $lang['move']; ?>"/><br/>
					</form>
				</div>
				<div id="splitThreadArea" style="display:none;">
					<?php
					require_once("classes/forumHandler.php");
					$forum = new forumHandler;
					$forums = $forum->getAllSimple();
					?>
					<br/>
					<form action="posts.php?id=<?php echo $_GET['id']; ?>&amp;page=<?php echo $pageNum; ?>" method="post">
						<?php echo $lang['newThreadHeadline']?> <input type="text" size="40" name="newThreadHeadline"/><br/>
						<div style="padding-top:2px;">
							<?php echo $lang['forumForNewThread']; ?> <select name="splitThreadTo">
								<?php
								foreach($forums as $element) {
								?>
								<option value="<?php echo $element['forumID']; ?>"><?php echo $element['name']; ?></option>
								<?php
								}
								?>
							</select>
						</div>
						<input type="hidden" id="splitFrom" name="splitFrom"/>
						<div style="padding-top:2px;">
							<input type="submit" name="splitThreadAction" value="<?php echo $lang['splitFromSelectedPost']; ?>"/>
						</div>
					</form>
				</div>
			</div>
		</td>
	</tr>
	<?php
	}
	?>
	<?php
	if($permissionPost && $currentThread['status'] != 1 || $forumVariables['adminInlogged'] || $edit) {
	?>
	<tr>
		<td class="postListFastAnswerArea">
			<form action="posts.php?id=<?php echo $_GET['id']; ?>" method="post">
				<?php echo $lang['fastAnswer']; ?>: <span class="errorText"><?php if(!empty($errorText)) echo $errorText; if(!empty($errorTimeLimit)) echo $errorTimeLimit; else echo "&nbsp;"; ?></span><br/>
				<textarea name="fastAnswer" style="width:400px; height:150px;" class="guideTextFields"><?php if(!empty($fastAnswer)) echo $fastAnswer; ?></textarea><br/>
				<br/>
				<input type="submit" name="fastAnswerPreview" value="<?php echo $lang['preview']; ?>" class="guideButton"/>
				<input type="submit" name="send" value="<?php echo $lang['send']; ?>" class="guideButton"/>
			</form>
		</td>
	</tr>
	<?php
	}
	?>
</table>
<?php
$menu->getBottom();
//echo $masterCount;
?>