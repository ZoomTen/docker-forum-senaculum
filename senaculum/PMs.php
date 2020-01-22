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
	
require_once('classes/menuHandler.php');
require_once('classes/PMHandler.php');
require_once('classes/errorHandler.php');
require_once('classes/other.php');

$menu = new menuHandler;
$PM = new PMHandler;
$error = new errorHandler;
$other = new other;

if(empty($_GET['type'])) {
	$type = "new";
}	
else {
	switch($_GET['type']){
		case "new": 
			$type = "new";
			break;
		case "inbox": 
			$type = "inbox";
			break;
		case "outbox":
			$type = "outbox";
			break;	
		default:
			$type = "new";
	}
}	

if(empty($_GET['sort']))
	$sort = 0;
else
	$sort = $_GET['sort'];	

if(!empty($_GET['delete'])){
	if($forumVariables['inlogged']) {
		$PM->remove($_GET['delete']);
		header("location: PMs.php?type=".$type."&amp;sort=".$sort);
	}		
}
$menu->getTop();
if(empty($_GET['id']))
	$PMs = $PM->getAll($type,$sort);
else {
	$PMs = $PM->getOne($_GET['id']);
	$id = $_GET['id'];
}		
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<table width="100%" cellpadding="2" cellspacing="0">
				<tr>
					<td class="PMListHeadingPM">
						<?php echo $lang['PM']; ?>:
					</td>
					<td class="PMListHeadingFrom">
						<?php 
							if($type == "outbox") 
								echo $lang['to'].":";
							else 
								echo $lang['from'].":"; 
						?>
					</td>
				</tr>
			</table>
		</td>			
	</tr>	
	<tr>
		<td class="PMListActionbox">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle" class="PMListChoseType">
						<a href="PMs.php?type=new&amp;sort=<?php echo $sort; ?>" <?php if($type=="new") echo "class=\"PMListChoseTypeActive\""; ?>><?php echo $lang['new']; ?></a> |
						<a href="PMs.php?type=inbox&amp;sort=<?php echo $sort; ?>" <?php if($type=="inbox") echo "class=\"PMListChoseTypeActive\""; ?>><?php echo $lang['inbox']; ?></a> |
						<a href="PMs.php?type=outbox&amp;sort=<?php echo $sort; ?>" <?php if($type=="outbox") echo "class=\"PMListChoseTypeActive\""; ?>><?php echo $lang['outbox']; ?></a>
					</td>
					<td class="PMListSort">
					<?php
						if(empty($_GET['sort']))
						{
							$sort=null;
						}
						else
						{
							$sort=$_GET['sort'];
						}
					?>
						<select class="postListThreadHeadingSortDropdown1" onchange="window.location='PMs.php?type=<?php echo $type;?>&amp;sort='+this.options[this.selectedIndex].value;">
							<option value="1" class="PMListSortDropdown1" <?php if($sort==1){echo "selected";} ?>><?php echo $lang['newest']; ?></option>
							<option value="2" class="PMListSortDropdown2" <?php if($sort==2){echo "selected";} ?>><?php echo $lang['oldest']; ?></option>
						</select>
					</td>
					<td class="PMListNewPM">
						<a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPM.php',800,600);<?php } else {?>addPM.php<?php } ?>" class="actionLink"><?php echo $lang['newPM']; ?></a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
	if(!$forumVariables['inlogged']) {
	?>
	<tr>
		<td align="center">
			<?php echo $lang['notLoggedInPleaseLogin']; ?>
		</td>
	</tr>
	<?php
	$menu->getBottom();
	die();
	}
	?>
	<tr>
			<td>
				<table width="100%" cellspacing="0" cellpadding="3">
	<?php
	/*if(isset($_GET['sort'])) {
		if($_GET['sort'] == 2)
			$i = count($PMs) - 1;
		else
			$i = 0;
	}
	else*/ 
	$i=0;
	if(!empty($PMs)) {
		if(!empty($id)) {
	?>	
					<tr>
						<td class="PMListHeading1">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left" valign="middle">
										<span class="PMListHeadingSubject"><?php echo $PMs['subject']; ?></span> <span class="PMListHeadingSent"><?php echo $lang['sent']; ?>: <i><?php echo $other->dateParse($forumVariables['dateFormat'],$PMs['date']); ?></i></span>
									</td>
									<td class="PMListHeadingAnswer" align="right" valign="middle">
										<?php if($type != "outbox" && $PMs['senderMemberID'] != 2) { ?><a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPM.php?id=<?php echo $PMs['sender']; ?>&amp;answer=<?php echo $PMs['PMID']; ?>&amp;quote=<?php echo $PMs['PMID']; ?>',800,600);<?php } else {?>addPM.php?id=<?php echo $PMs['sender']; ?>&amp;answer=<?php echo $PMs['PMID']; ?>&amp;quote=<?php echo $PMs['PMID']; } ?>" class="actionLink2"><?php echo $lang['quote1']; ?></a> | <a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPM.php?id=<?php echo $PMs['sender']; ?>&amp;answer=<?php echo $PMs['PMID']; ?>',800,600);<?php } else {?>addPM.php?id=<?php echo $PMs['sender']; ?>&amp;answer=<?php echo $PMs['PMID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['reply']; ?></a><?php } ?><?php if($type!="new") { if($type != "outbox") echo " | "; ?><a href="javascript:confirmProcess('<?php echo $lang['deletePMConfirm']; ?>','PMs.php?type=<?php echo $type; ?>&amp;delete=<?php echo $PMs['PMID']; ?>&amp;sort=<?php echo $sort; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a><?php } ?>
									</td>
								</tr>
							</table>
						</td>
						<td valign="top" class="PMListFrom1" rowspan="3">
							<?php
								if($type == "outbox") {
									if($PMs['reciverMemberID'] == 2) {
							?>
							<b><span class="postListDeletedUser"><?php echo $lang['deletedUser']; ?></span></b><br/>
							<?php
									}
									else {
							?>
							<b><a href="profile.php?id=<?php echo $PMs['reciverMemberID']; ?>"><?php echo $PMs['reciverUserName']; ?></a></b><br/>
							<?php
									}
							?>
							<?php if(!empty($PMs['reciverAvatar'])) echo "<img src=\"images/avatars/".$PMs['reciverAvatar']."\" alt=\"".$lang['avatar']."\"/>"; ?><br/><br/>
							<?php if(!empty($PMs['reciverLocation'])) echo $lang['location'].": ".$PMs['reciverLocation']."<br/>"; ?>
							<?php
								}
								else {
									if($PMs['senderMemberID'] == 2) {
							?>
							<b><span class="postListDeletedUser"><?php echo $lang['deletedUser']; ?></span></b><br/>
							<?php
									}
									else {
							?>
							<b><a href="profile.php?id=<?php echo $PMs['senderMemberID']; ?>"><?php echo $PMs['senderUserName']; ?></a></b><br/>
							<?php
									}
							?>
							<?php if(!empty($PMs['senderAvatar'])) echo "<img src=\"images/avatars/".$PMs['senderAvatar']."\" alt=\"".$lang['avatar']."\"/>"; ?><br/><br/>
							<?php if(!empty($PMs['senderLocation'])) echo $lang['location'].": ".$PMs['senderLocation']."<br/>"; ?>
							<?php
								}
							?>	
						</td>
					</tr>
					<tr>
						<td valign="top" class="PMListMessage1">
							<?php echo $PMs['text']; ?><br/>
							<br/>
						</td>
					</tr>
					<tr>	
						<td valign="bottom" class="PMListSignature1">
							<?php
							if($type == "outbox") {
								if(!empty($PMs['reciverSignature']) && $PMs['reciverAttachSign'])
									echo "_______________________<br/>\n".$PMs['reciverSignature'];
							}
							else {
								if(!empty($PMs['senderSignature']) && $PMs['senderAttachSign'])
									echo "_______________________<br/>\n".$PMs['senderSignature'];
							}
							?>
						</td>
					</tr>
	<?php	
		}	
		else {
			$i=0;
			foreach($PMs as $currentPMValue) {
				if($i % 2 == 0) {
	?>	
					<tr>
						<td class="PMListHeading1">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left" valign="middle">
										<span class="PMListHeadingSubject"><a href="PMs.php?id=<?php echo $currentPMValue['PMID']; ?>&amp;type=<?php echo $type; ?>&amp;sort=<?php echo $sort; ?>"><?php echo $currentPMValue['subject']; ?></a></span>
									</td>
									<td class="PMListHeadingAnswer" align="right" valign="middle">
										<?php if($type != "outbox" && $currentPMValue['senderMemberID'] != 2) { ?><a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPM.php?id=<?php echo $currentPMValue['sender']; ?>&amp;answer=<?php echo $currentPMValue['PMID']; ?>',800,600);<?php } else {?>addPM.php?id=<?php echo $currentPMValue['sender']; ?>&amp;answer=<?php echo $currentPMValue['PMID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['reply']; ?></a><?php } ?><?php if($type!="new") { if($type != "outbox") echo " | "; ?><a href="javascript:confirmProcess('<?php echo $lang['deletePMConfirm']; ?>','PMs.php?type=<?php echo $type; ?>&amp;delete=<?php echo $currentPMValue['PMID']; ?>&amp;sort=<?php echo $sort; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a><?php } ?>
									</td>
								</tr>
							</table>
						</td>
						<td valign="top" class="PMListFrom1">
							<?php
							if($currentPMValue['senderMemberID'] == 2) {
							?>
							<b><span class="postListDeletedUser"><?php echo $lang['deletedUser']; ?></span></b><br/>
							<?php
							}
							else {
							?>
							<b><a href="profile.php?id=<?php echo $currentPMValue['senderMemberID']; ?>"><?php echo $currentPMValue['senderUserName']; ?></a></b>
							<?php
							}
							?>
						</td>
					</tr>
	<?php
				}
				else {
	?>		
					<tr>
						<td class="PMListHeading2">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left" valign="middle">
										<span class="PMListHeadingSubject"><a href="PMs.php?id=<?php echo $currentPMValue['PMID']; ?>&amp;type=<?php echo $type; ?>&amp;sort=<?php echo $sort; ?>"><?php echo $currentPMValue['subject']; ?></a></span>
									</td>
									<td class="PMListHeadingAnswer" align="right" valign="middle">
										<?php if($type != "outbox") { ?><a href="<?php if($forumSettings['guidesInPopups']) { ?>javascript:popup('addPM.php?id=<?php echo $currentPMValue['sender']; ?>&amp;answer=<?php echo $currentPMValue['PMID']; ?>',800,600);<?php } else {?>addPM.php?id=<?php echo $currentPMValue['sender']; ?>&amp;answer=<?php echo $currentPMValue['PMID']; ?><?php } ?>" class="actionLink2"><?php echo $lang['reply']; ?></a><?php } ?><?php if($type!="new") { if($type != "outbox") echo " | "; ?><a href="javascript:confirmProcess('<?php echo $lang['deletePMConfirm']; ?>','PMs.php?type=<?php echo $type; ?>&amp;delete=<?php echo $currentPMValue['PMID']; ?>&amp;sort=<?php echo $sort; ?>');" class="actionLink2"><?php echo $lang['delete']; ?></a><?php } ?>
									</td>
								</tr>
							</table>
						</td>
						<td valign="top" class="PMListFrom2">
							<?php
							if($currentPMValue['senderMemberID'] == 2) {
							?>
							<b><span class="postListDeletedUser"><?php echo $lang['deletedUser']; ?></span></b><br/>
							<?php
							}
							else {
							?>
							<b><a href="profile.php?id=<?php echo $currentPMValue['senderMemberID']; ?>"><?php echo $currentPMValue['senderUserName']; ?></a></b><br/>
							<?php
							}
							?>
						</td>
					</tr>
	<?php
				}
				$i++;	
			}
		}
	}	
	else {
	?>
					<tr>
						<td align="center" colspan="4">
							<?php echo $lang['noPMs']; ?>
						</td>
					</tr>
	<?php
	}
	?>	

				</table>
			</td>
		</tr>																			
</table>
	
<?php
$menu->getBottom();
//echo $masterCount;
?>  