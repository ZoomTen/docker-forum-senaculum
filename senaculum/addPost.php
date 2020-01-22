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

	require_once("classes/logInOutHandler.php");
	require_once("classes/postHandler.php");
	require_once("classes/threadHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/process.php");
	require_once("classes/permissionHandler.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$post = new postHandler;
	$thread = new threadHandler;
	$control = new control;
	$process = new process;
	$permission = new permissionHandler;
	
	$errorGuestName = "";
	$errorHeadline = "";
	$errorText = "";
	$errorUploads = "";
	$errorTimeLimit = "";
	
	$guestName = "";
	$headline = "";
	$text = "";
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
	$enableAttachments = false;
	$attachmentUploads = "";
	$attachTempname = "";
	$attachmentNumberCount = 1;
	$attachmentFiles = "";
	$preview = false;
	$notify = false;
	$notifyTo = false;
	
	if(isset($_GET['notify']))
		$notify = true;
	if(isset($_GET['notifyTo']))
		$notifyTo = $_GET['notifyTo'];	
	
	if(isset($_GET['preview'])) {
		if(!empty($_SESSION['forumFastAnswerTemp'])) {
			$text = $_SESSION['forumFastAnswerTemp'];
			$preview = true;
			$_SESSION['forumFastAnswerTemp2'] = "";
		}
	}
	
	if(empty($_GET['id']))
	{
		$error->guide($lang['incorrectURL1'], $lang['incorrectURL2'], false);
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

	
	$forumID = $post->forumIDFromThreadID($_GET['id']);
	$threads = $thread->getOne($_GET['id'],true);
	if($threads['status'] == 1 && !$forumVariables['adminInlogged'] && !$auth->moderator($forumID,"post"))
		$error->guide($lang['threadIsLocked'],$lang['threadIsLocked'],false);
	if(!$permission->permission($forumID,"post")) {
		if(!$forumVariables['inlogged'])
			$error->guide($lang['notLoggedIn'], $lang['notLoggedInPleaseLogin'], true);
		else	
			$error->guide($lang['noPermissionDoThis1'], $lang['noPermissionDoThis2'], false);
	}
	if($forumSettings['attachmentsActivated'])
		$attach = $permission->permission($forumID,"attach");
	else
		$attach = false;

if(isset($_GET['post'])) {
	$posts = $post->getOne($_GET['post'], true);
	$headline = $posts['headline'];		
	if(substr($headline,0,3) != $lang['replyShortening'] && $headline != "")
		$headline = $lang['replyShortening']." ".$headline;
	if(isset($_GET['quote']))	
		$text = "[quote=".$posts['authorName']."]".$posts['text']."[/quote]";
	if($posts['notifyWhenReply'] && $posts['authorID'] != 2) {
		$notify = true;
		$notifyTo = $posts['authorID'];
	}	
}

	if(isset($_POST['preview'])) {
		$headline = $_POST['headline'];
		$text = $_POST['text'];
		if(isset($_POST['disableBBCode']))
			$disableBBCode = true;
		else
			$disableBBCode = false;
		if(isset($_POST['disableSmilies']))
			$disableSmilies = true;
		else
			$disableSmilies = false;
		if(isset($_POST['notifyWhenReply']))	
			$notifyWhenReply = true;
		else
			$notifyWhenReply = false;
		if(isset($_POST['attachSign']))		
			$attachSign = true;
		else
			$attachSign = false;	
		if(isset($_POST['guestName']))
			$guestName = $_POST['guestName'];
		if(isset($_POST['enableAttachments']) && $attach) {
			$enableAttachments = true;
			$attachmentNumberCount = $_POST['attachmentNumberCount'];
			if(!empty($_POST['attachmentFilename']) && is_array($_POST['attachmentFilename']) && !empty($_POST['attachmentNumber']) && is_array($_POST['attachmentNumber'])) {
				$i = 0;
				foreach($_POST['attachmentFilename'] as $upload) {
					$attachmentUploads[$i]['filename'] = $_POST['attachmentFilename'][$i];
					$attachmentUploads[$i]['attachmentNumber'] = $_POST['attachmentNumber'][$i];
					$i++;
				}
				if(!empty($_POST['attachTempname']))
					$attachTempname = $_POST['attachTempname'];
			}
		}
		$preview = true;			
	}
	elseif(isset($_POST['headline']))
	{
		if(isset($_POST['fileUploadSubmit']) && !empty($_POST['attachmentNumberCount'])) {
			if(!empty($_FILES['fileUpload']['name'])) {
				$headline = $_POST['headline'];
				$text = $_POST['text'];
				if(isset($_POST['disableBBCode']))
					$disableBBCode = true;
				else
					$disableBBCode = false;
				if(isset($_POST['disableSmilies']))
					$disableSmilies = true;
				else
					$disableSmilies = false;
				if(isset($_POST['notifyWhenReply']))	
					$notifyWhenReply = true;
				else
					$notifyWhenReply = false;
				if(isset($_POST['attachSign']))		
					$attachSign = true;
				else
					$attachSign = false;
				if(isset($_POST['guestName']))
					$guestName = $_POST['guestName'];
				$enableAttachments = true;
				$attachmentNumberCount = $_POST['attachmentNumberCount'];
				
				$errorUploads = $control->maxFilesize($_FILES['fileUpload'],$forumSettings['maxAttachmentUploadSize']);
				if(!$forumSettings['disallowedAttachmentExtensionsAdd'] || empty($forumSettings['disallowedAttachmentExtensionsAddThis'])) {
					if($forumSettings['checkAllowedDisallowedAttachmentExtensions'])
						$errorUploads = $control->allowedExtensions($_FILES['fileUpload']['name'],$forumSettings['allowedDisallowedAttachmentExtensions']);
					else	
						$errorUploads = $control->disallowedExtensions($_FILES['fileUpload']['name'],$forumSettings['allowedDisallowedAttachmentExtensions']);
				}	
				
				if(!empty($_POST['attachmentFilename']) && is_array($_POST['attachmentFilename']) && !empty($_POST['attachmentNumber']) && is_array($_POST['attachmentNumber'])) {
					$i = 0;
					foreach($_POST['attachmentFilename'] as $upload) {
						$attachmentUploads[$i]['filename'] = $_POST['attachmentFilename'][$i];
						$attachmentUploads[$i]['attachmentNumber'] = $_POST['attachmentNumber'][$i];
						$i++;
					}
					if(!empty($_POST['attachTempname']))
						$attachTempname = $_POST['attachTempname'];
				}
				
				if(empty($errorUploads)) {
					require_once("classes/attachmentHandler.php");
					$attachment = new attachmentHandler;
					if(empty($attachTempname)) {
						$attachTempname = $attachment->upload($_FILES['fileUpload'],$attachmentNumberCount);
					}	
					else
						$attachment->upload($_FILES['fileUpload'],$attachmentNumberCount,$attachTempname);		
					$filename = $_FILES['fileUpload']['name'];	
					
					if(is_array($attachmentUploads)) {
						foreach($attachmentUploads as $upload) 
							$attachmentFiles[] = $upload['filename'];
					}		
					
					$filename = $_FILES['fileUpload']['name'];
					
					if($forumSettings['disallowedAttachmentExtensionsAdd'] || !empty($forumSettings['disallowedAttachmentExtensionsAddThis'])) {
						if($forumSettings['checkAllowedDisallowedAttachmentExtensions']) {
							if($control->allowedExtensions($filename,$forumSettings['allowedDisallowedAttachmentExtensions']))
								$filename = $filename.".".$forumSettings['disallowedAttachmentExtensionsAddThis'];
						}	
						else {
							if($control->disallowedExtensions($filename,$forumSettings['allowedDisallowedAttachmentExtensions']))
								$filename = $filename.".".$forumSettings['disallowedAttachmentExtensionsAddThis'];
						}	
					}	
					
					if(is_array($attachmentFiles) && in_array($filename,$attachmentFiles)) {
						if(!$sufix = strrchr($filename, "."))
							$sufix = "";
						$prefix = substr($filename,0,-strlen($sufix));
							
						$i = 1;
						while(in_array($prefix."(".$i.")".$sufix, $attachmentFiles))
							$i++;
						$filename = $prefix."(".$i.")".$sufix;	
					}	
					
					$noElements = count($attachmentUploads);
					$attachmentUploads[$noElements]['filename'] = $filename;	
					$attachmentUploads[$noElements]['attachmentNumber'] = $attachmentNumberCount;
					$attachmentNumberCount++;		
				}
			}	
			else {
				$headline = $_POST['headline'];
				$text = $_POST['text'];
				if(isset($_POST['disableBBCode']))
					$disableBBCode = true;
				else
					$disableBBCode = false;
				if(isset($_POST['disableSmilies']))
					$disableSmilies = true;
				else
					$disableSmilies = false;
				if(isset($_POST['notifyWhenReply']))	
					$notifyWhenReply = true;
				else
					$notifyWhenReply = false;
				if(isset($_POST['attachSign']))		
					$attachSign = true;
				else
					$attachSign = false;
				if(isset($_POST['guestName']))
					$guestName = $_POST['guestName'];
				$enableAttachments = true;
				$attachmentNumberCount = $_POST['attachmentNumberCount'];
				if(!empty($_POST['attachmentFilename']) && is_array($_POST['attachmentFilename']) && !empty($_POST['attachmentNumber']) && is_array($_POST['attachmentNumber'])) {
					$i = 0;
					foreach($_POST['attachmentFilename'] as $upload) {
						$attachmentUploads[$i]['filename'] = $_POST['attachmentFilename'][$i];
						$attachmentUploads[$i]['attachmentNumber'] = $_POST['attachmentNumber'][$i];
						$i++;
					}
					if(!empty($_POST['attachTempname']))
						$attachTempname = $_POST['attachTempname'];
				}
			}
		}
		elseif(isset($_POST['deleteAttachment'])) {
			$headline = $_POST['headline'];
			$text = $_POST['text'];
			if(isset($_POST['disableBBCode']))
				$disableBBCode = true;
			else
				$disableBBCode = false;
			if(isset($_POST['disableSmilies']))
				$disableSmilies = true;
			else
				$disableSmilies = false;
			if(isset($_POST['notifyWhenReply']))	
				$notifyWhenReply = true;
			else
				$notifyWhenReply = false;
			if(isset($_POST['attachSign']))		
				$attachSign = true;
			else
				$attachSign = false;
			if(isset($_POST['guestName']))
				$guestName = $_POST['guestName'];
			$enableAttachments = true;
			$attachmentNumberCount = $_POST['attachmentNumberCount'];
			if(!empty($_POST['attachmentFilename']) && is_array($_POST['attachmentFilename']) && !empty($_POST['attachmentNumber']) && is_array($_POST['attachmentNumber'])) {
				$i = 0;
				foreach($_POST['attachmentFilename'] as $upload) {
					$attachmentUploads[$i]['filename'] = $_POST['attachmentFilename'][$i];
					$attachmentUploads[$i]['attachmentNumber'] = $_POST['attachmentNumber'][$i];
					$i++;
				}
				if(!empty($_POST['attachTempname']))
					$attachTempname = $_POST['attachTempname'];
			}

			if(is_array($attachmentUploads) && is_array($_POST['deleteAttachmentSelect']) && !empty($attachTempname)) {
				require_once("classes/attachmentHandler.php");
				$attachment = new attachmentHandler;
				$attachmentUploads2 = "";
				$i = 0;
				foreach($attachmentUploads as $upload) {
					if(!in_array($i,$_POST['deleteAttachmentSelect'])) {
						$attachmentUploads2[$i]['filename'] = $upload['filename'];
						$attachmentUploads2[$i]['attachmentNumber'] = $upload['attachmentNumber'];
					}
					else
						$attachment->unload($upload['attachmentNumber'],$attachTempname);
					$i++;
				}
				$attachmentUploads = $attachmentUploads2;
			}
		}
		else {
			$errorHeadline=$control->text($_POST['headline'], 0, 50);
			$errorText=$control->text($_POST['text'], 1, 8000);
			$errorTimeLimit = $control->postTimeLimit($_GET['id']);
			if(!empty($_POST['guestName']))
				$errorGuestName = $control->text($_POST['guestName'],0,20);
			if(!empty($errorHeadline)||!empty($errorText)||!empty($errorGuestName)||!empty($errorTimeLimit))
			{
				$headline = $_POST['headline'];
				$text = $_POST['text'];
				if(isset($_POST['disableBBCode']))
					$disableBBCode = true;
				else
					$disableBBCode = false;
				if(isset($_POST['disableSmilies']))
					$disableSmilies = true;
				else
					$disableSmilies = false;
				if(isset($_POST['notifyWhenReply']))	
					$notifyWhenReply = true;
				else
					$notifyWhenReply = false;
				if(isset($_POST['attachSign']))		
					$attachSign = true;
				else
					$attachSign = false;
				if(isset($_POST['guestName']))
					$guestName = $_POST['guestName'];
				if(isset($_POST['enableAttachments']) && $attach) {
					$enableAttachments = true;
					$attachmentNumberCount = $_POST['attachmentNumberCount'];
					if(!empty($_POST['attachmentFilename']) && is_array($_POST['attachmentFilename']) && !empty($_POST['attachmentNumber']) && is_array($_POST['attachmentNumber'])) {
						$i = 0;
						foreach($_POST['attachmentFilename'] as $upload) {
							$attachmentUploads[$i]['filename'] = $_POST['attachmentFilename'][$i];
							$attachmentUploads[$i]['attachmentNumber'] = $_POST['attachmentNumber'][$i];
							$i++;
						}
						if(!empty($_POST['attachTempname']))
							$attachTempname = $_POST['attachTempname'];
					}
				}	
			}
			
			if(empty($errorHeadline)&&empty($errorText)&&empty($errorGuestName)&&empty($errorTimeLimit))
			{
				if(isset($_POST['disableBBCode']))
					$disableBBCode = true;
				else
					$disableBBCode = false;
				if(isset($_POST['disableSmilies']))
					$disableSmilies = true;
				else
					$disableSmilies = false;
				if(isset($_POST['notifyWhenReply']))	
					$notifyWhenReply = true;
				else
					$notifyWhenReply = false;
				if(isset($_POST['attachSign']))		
					$attachSign = true;
				else
					$attachSign = false;
				if(isset($_POST['guestName']))
					$guestName = $_POST['guestName'];
				if($attach && isset($_POST['enableAttachments']) && !empty($_POST['attachmentNumber']) && is_array($_POST['attachmentFilename']) && !empty($_POST['attachTempname'])) {
					$i = 0;
					foreach($_POST['attachmentFilename'] as $upload) {
						if($forumSettings['checkAllowedDisallowedAttachmentExtensions']) {
							if($control->allowedExtensions($upload,$forumSettings['allowedDisallowedAttachmentExtensions']))
								continue;
						}	
						else {
							if($control->disallowedExtensions($upload,$forumSettings['allowedDisallowedAttachmentExtensions']))
								continue;
						}	
						$attachments[$i]['filename'] = $_POST['attachmentFilename'][$i];
						$attachments[$i]['attachmentNumber'] = $_POST['attachmentNumber'][$i];
						$attachments[$i]['tempName'] = $_POST['attachTempname'];
						if($forumSettings['maxNumberOfAttachments'] <= $i)
							break;
						$i++;
					}
					$id = $post->add($_POST['headline'], $_POST['text'], $_GET['id'], $disableBBCode, $disableSmilies, $notifyWhenReply, $attachSign, $guestName, $attachments);
				}
				else
					$id = $post->add($_POST['headline'], $_POST['text'], $_GET['id'], $disableBBCode, $disableSmilies, $notifyWhenReply, $attachSign, $guestName);
					
				//Send a notify e-mail
				if($notify && !empty($notifyTo) && $notifyTo != $forumVariables['inloggedMemberID']) {
					require_once("classes/mail.php");
					$mail = new mail;
					$subject = $lang['notifyReplyMailSubject'];
					$message = $lang['notifyReplyMailMessage1'].$forumSettings['forumName']." ( http://".$forumSettings['forumDomainName'].$forumSettings['forumScriptPath']." )".$lang['notifyReplyMailMessage2']."http://".$forumSettings['forumDomainName'].$forumSettings['forumScriptPath']."posts.php?id=".$_GET['id']."&pid=".$id."#".$id.$lang['notifyReplyMailMessage3'];
					require_once("classes/dbHandler.php");
					$db = new dbHandler;
					$notifyTo = $db->SQLsecure($notifyTo);
					$sql = "SELECT email FROM _'pfx'_members WHERE memberID = '".$notifyTo."'";
					$result = $db->runSQL($sql);
					if($db->numRows($result) > 0) {
						$row = $db->fetchArray($result);
						$sendTo = $row['email'];
						$mail->send($sendTo,$subject,$message);
					}
				}
					
				$nextAction = "posts.php?id=".$_GET['id']."&pid=".$id."#".$id;
				$error->done($lang['postSent1'],$lang['postSent2'],$nextAction);
			}
		}
	}
	
	require_once("classes/BBCodeHandler.php");
	require_once("classes/smilieHandler.php");
	$BBCode = new BBCodeHandler;
	$smilie = new smilieHandler;
	$BBCodes = $BBCode->getAll();
	$smilies = $smilie->getAll();
	
	$title = $lang['createNewPost'];
	$heading = $lang['createNewPost'];
	$help = $lang['addPostHelp'];
	
	include("include/guideTop.php");
	
?>
<script type="text/javascript">
<!--	
	//Undo variables
	var nrOfUndos = 50;						//How many undos you can make
	var undoText = new Array(nrOfUndos);	//Old texts
	for(i=0;i<nrOfUndos;i++)				//Set all undoTexts to empty
		if(i == 0)
			undoText[i] = "&sect;empty&sect;";
	var	undoIndex = 0;						//How many undos you have made
	
	<?php
	if(!empty($BBCodes)) {
	?>
	//BBcode variables
	var BBCodes = new Array(<?php echo count($BBCodes); ?>);			//BBCode codes
	var BBCodesStart = new Array(<?php echo count($BBCodes); ?>);		//BBCode start tag
	var BBCodesEnd = new Array(<?php echo count($BBCodes); ?>);			//BBCode end tag
	var keyDown = 0, previousKeys = new Array(10);						//typed key and old typed keys
	var previousText = '';												//The text that it looked like before the key was pressed
	var autoBBCodeEndTag = '';											//Current BBCode end tag for autoBBCode function
	for(i=0;i<10;i++)				//Set all keys to empty
		previousKeys[i] = "";
	<?php
	$i = 0;
	foreach($BBCodes as $element) {							//Get all BBCode codes
		echo "BBCodes[".$i."] = '".$element['code']."';\n";
		$i++;
	}
	?>
	//Split BBCode code to get start and end tag
	var BBCodesSplit;
	for(i=0; i<BBCodes.length; i++) {
		BBCodesSplit = BBCodes[i].split('§');
		BBCodesStart[i] = '';
		for(j=0; j<BBCodesSplit.length-1; j++) {
			/*if(j == BBCodesSplit.length-2)
				BBCodesStart[i] = BBCodesStart[i] + BBCodesSplit[j];
			else
				BBCodesStart[i] = BBCodesStart[i] + BBCodesSplit[j] + "§";*/
			BBCodesStart[i] = BBCodesStart[i] + BBCodesSplit[j];	
		}
		BBCodesEnd[i] =  BBCodesSplit[BBCodesSplit.length-1];
	}
	
	function messageFieldChangePress(event) {				//When onkeypressed on the message area
		if(event.keyCode && document.getElementById("enableAutoBBCode").checked) {					//For IE, store witch key that was pressed
			keyDown = event.keyCode;
			autoBBCode();
		}
	}
	<?php
	}
	?>
	function messageFieldChangeUp() {
		<?php
		if(!empty($BBCodes)) {
		?>
		if(document.getElementById("enableAutoBBCode").checked) {
			if(typeof document.getElementById("text").selectionStart=="number") { //For Mozilla and more to store key pressed in message area
				var caretPos = document.getElementById("text").selectionStart;
				var text = document.getElementById("text").value;
				if(text != previousText) {
					keyDown = text.charCodeAt(caretPos-1);
					if(autoBBCode())
						autoBBCodeAdd();		//Add the end tag for BBCode for the autoBBCode function if a starttag was found
					previousText = text;		//Store the previous text
				}	
				else {
					previousText = text; 		//Store the previous text
					return false;	
				}	
			}
			if(navigator.appName == "Microsoft Internet Explorer")
				autoBBCodeAdd(); //If the the browser is IE add the end tag for BBCode for the autoBBCode function
		}
		<?php
		}
		?>
		storeUndo();	//Store the current text in the message field for the undo-function	
	}
	<?php
	if(!empty($BBCodes)) {
	?>
	function autoBBCodeAdd() {			//Insert BBCode end tag for the autoBBCode function
		if(autoBBCodeEndTag != '') {
			if(document.selection && document.getElementById("text").createTextRange) {		//For IE
				var theCaret = document.selection.createRange().duplicate();
				theCaret.text = autoBBCodeEndTag;						//Insert BBCode end tag at the caret
				//Put the caret in the right position
				theCaret.moveEnd('character',-autoBBCodeEndTag.length);
				theCaret.moveStart('character',0);
				theCaret.select();
			}
			else {					//For mozilla and more
				var text = document.getElementById("text").value;
				var caretPos = document.getElementById("text").selectionStart;			//Get the caret position
				document.getElementById("text").value = text.substr(0,caretPos) + autoBBCodeEndTag + text.substr(caretPos,text.length-1); //Insert BBCode end tag at the caret
				//Put the caret in the right position
				document.getElementById("text").selectionStart = caretPos;
				document.getElementById("text").selectionEnd = caretPos;
			}	
			autoBBCodeEndTag = '';
		}
	}
	function autoBBCode() {			//Looks for entered BBCodes
		var text = document.getElementById("text").value;
		var previousKeys2 = new Array(10);
		var keys = '';
		
		//Update the previousKeys variable with new keys
		for(i=1;i<10;i++)			
			previousKeys2[i] = previousKeys[i-1];
		previousKeys = previousKeys2;	
		previousKeys[0] = String.fromCharCode(keyDown);
		
		//Look for entered BBCodes
		for(j=0;j<10;j++) {
			for(i=0;i<BBCodesStart.length;i++) {
				for(k=0;k<=j;k++) {
					keys = previousKeys[k] + keys;
				}
				if(keys == BBCodesStart[i]) {
					autoBBCodeEndTag = BBCodesEnd[i];
					return true;		//BBCode found
				}
				keys = '';	
			}
		}	
		
		return false;	//BBCode not found
	}

	//Add BBCode to the message
	function addBBCode(BBCode) {
		resetUndo();
		document.getElementById("text").focus(); //Set focus on the messagefield
		
		//Do the work
		if(typeof document.getElementById("text").selectionStart=="number") {//For Mozilla and more
			var caretPos;							//Stores the position of the caret in the textarea
			if(document.getElementById("text").selectionStart != document.getElementById("text").selectionEnd) {	//Check if a part of the text is selected
				BBCodeSplit = BBCode.split('§');		//Split in parts
				
				//If the BBCode code contains more than one §, store the length to the first § in the BBCode code
				var putCaretTo;
				if(BBCodeSplit.length > 2)
					putCaretTo = BBCodeSplit[0].length;
				else
					putCaretTo = false;	
					
				var BBCode1 = "";						//The beginning of the BBCode
				for(i=0;i<BBCodeSplit.length-1;i++)		//Put the beginning of the BBCode all together. If the BBCode code conatains more than 2 §
					BBCode1 = BBCode1 + BBCodeSplit[i];
				var BBCode2 = BBCodeSplit[BBCodeSplit.length-1];	//The end of the BBCode
				
				var selectionStart = document.getElementById("text").selectionStart;								//The postion where the selection starts
				var selectionEnd = document.getElementById("text").selectionEnd;									//The position where the selection ends
				var selectedText = document.getElementById("text").value.substring(selectionStart, selectionEnd);	//The selected text
				
				//Insert the BBCode
				document.getElementById("text").value = document.getElementById("text").value.substring(0, selectionStart) + BBCode1 + selectedText + BBCode2 + document.getElementById("text").value.substring(selectionEnd);
				
				//Put the caret in the right position
				if(putCaretTo) {		//If the BBCode code contains more than one §, store the length to the first § in the BBCode code
					var insertedText = document.getElementById("text").value.substring(0, selectionStart);
					putCaretTo = insertedText.length + putCaretTo;
				}	
				else {	
					var insertedText = document.getElementById("text").value.substring(0, selectionStart) + BBCode1 + selectedText + BBCode2;	//The text before there the caret will be
					putCaretTo = insertedText.length;							//Position where the caret will be put to
				}	
				document.getElementById("text").selectionStart = putCaretTo;	
				document.getElementById("text").selectionEnd = putCaretTo;
			}
			else {	
				caretPos = document.getElementById("text").selectionStart;			//Get the caret position
				
				var putCaretTo = BBCode.lastIndexOf('§') + caretPos;				//Get the position of the last occurens of § in the BBCode
				BBCodeSplit = BBCode.split('§');
				putCaretTo = putCaretTo -(BBCodeSplit.length - 2);					//Erase extra length for §
				//Erase all §
				while(BBCode.match(/§/i))
					BBCode = BBCode.replace(/§/i,'');
						
				//Insert the BBCode		
				var text = document.getElementById("text").value;
				var length = text.length; 
				document.getElementById("text").value = text.substr(0,caretPos) + BBCode + text.substr(caretPos,length-1);
				
				//Put the caret between the tags
				document.getElementById("text").selectionStart = putCaretTo;
				document.getElementById("text").selectionEnd = putCaretTo;
			}
		}	
		else if(document.selection && document.getElementById("text").createTextRange) { //For IE
			var text = document.getElementById("text").value;
			var i = text.length + 1, theCaret = document.selection.createRange().duplicate();
			BBCodeSplit = BBCode.split('§');		//Split in parts
			var BBCode1 = "";						//The beginning of the BBCode
			for(i=0;i<BBCodeSplit.length-1;i++)		//Put the beginning of the BBCode all together. If the BBCode code conatains more than 2 §
				BBCode1 = BBCode1 + BBCodeSplit[i];
			var BBCode2 = BBCodeSplit[BBCodeSplit.length-1];	//The end of the BBCode
			
			var caretTextLength = theCaret.text.length;			//Length of the selected text
			
			//Insert the BBCode
			var insertText = BBCode1 + theCaret.text + BBCode2;
			theCaret.text = insertText;
			
			//Put the caret in right possition
			if(BBCodeSplit.length > 2) {		//If the BBCode code contains more than one § put the caret at the first §
				if(caretTextLength > 0) {		//Check if a part of the text is selected
					theCaret.moveStart('character',-(insertText.length - BBCodeSplit[0].length));
					theCaret.moveEnd('character',-(insertText.length - BBCodeSplit[0].length));
				}
				else {
					theCaret.moveStart('character',-BBCodeSplit[BBCodeSplit.length-1].length);
					theCaret.moveEnd('character',-BBCodeSplit[BBCodeSplit.length-1].length);
				}	
			}
			else if(caretTextLength == 0) {
				theCaret.moveStart('character',-BBCodeSplit[BBCodeSplit.length-1].length);
				theCaret.moveEnd('character',-BBCodeSplit[BBCodeSplit.length-1].length);
			}	
			theCaret.select();		
		}
		storeUndo();		
	}
	
	//Shows BBCode info
	function showBBCodeInfo(info) {
		if(navigator.appName == "Microsoft Internet Explorer")
			document.getElementById("BBCodeInfo").innerText = info;		//For IE
		else	
			document.getElementById("BBCodeInfo").innerHTML = info;		//For mozilla and more
	}
	
	//Hides BBCode info
	function hideBBCodeInfo() {
		if(navigator.appName == "Microsoft Internet Explorer")
			document.getElementById("BBCodeInfo").innerText = '';		//For IE
		else	
			document.getElementById("BBCodeInfo").innerHTML = '';		//For mozilla and more
	}			
	<?php
	}
	if(!empty($smilies)) {
	?>
	//Add smilie to the message
	function addSmilie(smilie) {
		resetUndo();
		
		document.getElementById("text").focus(); //Set focus on the messagefield

		//Do the work
		if(typeof document.getElementById("text").selectionStart=="number") {//For Mozilla and more
			if(document.getElementById("text").selectionStart != document.getElementById("text").selectionEnd)	//If a text is selected, do nothing
				return false;
				
			var caretPos;							//Stores the position of the caret in the textarea	
			caretPos = document.getElementById("text").selectionStart;
			
			var putCaretTo = smilie.length + caretPos;				//Find the position to set the caret after inserted smilie
				
			//Insert the smilie		
			var text = document.getElementById("text").value;
			var length = text.length;
			document.getElementById("text").value = text.substr(0,caretPos) + smilie + text.substr(caretPos,length-1);
			
			//Put the caret in the right position
			document.getElementById("text").selectionStart = putCaretTo;
			document.getElementById("text").selectionEnd = putCaretTo;
		}	
		else if(document.selection && document.getElementById("text").createTextRange) { //For IE
			var theCaret = document.selection.createRange().duplicate();
			if(theCaret.text.length > 0)				//If a part of the text is selected, do nothing
				return false;
			theCaret.text = smilie;						//Insert the smilie at the caret
			theCaret.select();							//Put the caret in right position
		}	
		storeUndo();
	}
	<?php
	}
	?>
	//Undo functions
	
	function resetUndo() {		//Reset the undo function
		if(undoIndex != 0) {
			for(i=0;i<nrOfUndos;i++) {
				if(i == 0)
					undoText[i] = undoText[undoIndex];
				else	
					undoText[i] = "&sect;empty&sect;";
			}	
			undoIndex = 0;
		}
	}
	function totalResetUndo() {		//Reset the undo function
		for(i=0;i<nrOfUndos;i++) {
			if(i == 0)
				undoText[i] = undoText[undoIndex];
			else	
				undoText[i] = "&sect;empty&sect;";
		}	
		undoIndex = 0;
	}
	function storeUndo() {		//Store old texts for the undo function
		if(undoIndex != 0) {
			resetUndo();		//If a undo has been made, reset the undo function
		}
		if(document.getElementById("enableUndo").checked) {		
			if(undoText[0] != document.getElementById("text").value) {
				var undoText2 = new Array(nrOfUndos);
				for(i=1;i<nrOfUndos;i++)
					undoText2[i] = undoText[i-1];
				undoText = undoText2;	
				undoText[0] = document.getElementById("text").value;
				undoIndex = 0;
			}	
		}	
	}
	function textUndo() {		//Do the undo
		if(document.getElementById("enableUndo").checked) {
			if(undoText[undoIndex] != "&sect;empty&sect;") {	//Check if the undo exists
				if(undoIndex < nrOfUndos-1) {
					if(undoText[undoIndex+1] != "&sect;empty&sect;") //Check if the next undo exists
						undoIndex++;	//Move to next undo
				}			
				document.getElementById("text").value = undoText[undoIndex]; //Do the undo
			}	
		}
		document.getElementById("text").focus();
	}
	function textRedo() {		//Do the redo
		if(document.getElementById("enableUndo").checked) {
			if(undoIndex != 0) {
				if(undoText[undoIndex] != "&sect;empty&sect;") {	//Check if the redo exists
					undoIndex--;			//Move to next redo
					document.getElementById("text").value = undoText[undoIndex]; //Do the redo
				}	
			}
		}
		document.getElementById("text").focus();		//Set focus to the messagefield
	}
//-->
</script>
<form action="addPost.php?id=<?php echo $_GET['id']?><?php if($notify) echo "&amp;notify=1"; if(!empty($notifyTo)) echo "&amp;notifyTo=".$notifyTo; ?>" method="post" enctype="multipart/form-data">
	<?php 
	if($preview) {
		if($forumVariables['inlogged']) {
			require_once("classes/memberHandler.php");
			$members = new memberHandler;
			$member = $members->getOne($forumVariables['inloggedMemberID']);
		}	
		require_once("classes/other.php");
		$other = new other;
		require_once("classes/process.php");
		$process = new process;
	?>
	<table cellpadding="0" cellspacing="10" style="width:100%;">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['preview']; ?>:
				<table cellspacing="0" cellpadding="3" class="guideInputArea" style="width:100%;">
					<tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="3">
								<tr>
									<td class="postListHeading1">
										<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0">
											<tr>
												<td align="left" valign="middle">
													<span class="postListHeadingSubject"><?php echo $process->headline($headline); ?></span> <span class="postListHeadingPosted"><?php echo $lang['posted']; ?>: <i><?php echo $other->dateParse($forumVariables['dateFormat'], time()); ?></i>&nbsp;&nbsp;(<?php echo $lang['lastEdited']; ?>: <i><?php echo $other->dateParse($forumVariables['dateFormat'], time()); ?></i>)</span>
												</td>
												<td class="postListHeadingAnswer" align="right" valign="middle">
													&nbsp;
												</td>
											</tr>
										</table>
									</td>
									<td valign="top" class="postListAuthor1" rowspan="3">
										<?php 
										if($forumVariables['inlogged']) {
										?>
										<b><a href="profile.php?id=<?php echo $forumVariables['inloggedMemberID']; ?>"><?php echo $forumVariables['inloggedUserName']; ?></a></b><br/>
										<?php
										}
										else {
										?>
										<?php if(!empty($guestName)) echo "<b>".$guestName."</b><br/>"; ?><span class="postListGuest"><?php echo $lang['guest']; ?></span><br/>
										<?php
										}
										?>
										<br/>
										<?php if(!empty($member['avatar'])) echo "<img src=\"images/avatars/".$member['avatar']."\" alt=\"".$lang['avatar']."\"/>"; ?><br/><br/>
										<?php if(!empty($member['location'])) echo $lang['location'].": ".$member['location']."<br/>"; ?>
									</td>
								</tr>
								<tr>
									<td valign="top" class="postListMessage1">
										<?php
										$processText['text'] = $text;
										$processText['disableBBCode'] = $disableBBCode;
										$processText['disableSmilies'] = $disableSmilies;
										echo $process->text($processText); 
										?><br/>
										<br/>
									</td>
								</tr>
								<tr>
									<td valign="bottom" class="postListSignature1">
										<?php
										if(!empty($member['signature']) && $attachSign)
											echo "_______________________<br/>\n".$process->text($member['signature']);
										?>
										<?php
											if($forumSettings['attachmentsActivated'] && $enableAttachments) {
												if(!empty($attachmentUploads) && is_array($attachmentUploads)) {
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
														foreach($attachmentUploads as $upload) {
															if($filesize = @filesize("attachments/temp/".$attachTempname."_".$upload['attachmentNumber'].".tmp")) {
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
														<a href="#" class="link"><?php echo $upload['filename'] ?></a> (<?php echo $filesize." ".$lang['KB']; ?>)<br/>
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
							</table>
						</td>
					</tr>
				</table>		
			</td>
		</tr>
	</table>
	<?php
	}
	?>		
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['input']; ?>: <span class="errorText"><?php if(!empty($errorTimeLimit)) echo $errorTimeLimit; else echo "&nbsp;"; ?></span><br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<?php
					if(!$forumVariables['inlogged']) {
					?>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['yourNameOptional']; ?>: <span class="errorText"><?php if(!empty($errorGuestName)) echo $errorGuestName; else echo "&nbsp;"; ?></span><br/>
							<input name="guestName" type="text" size="40" value="<?php echo $guestName; ?>" class="guideTextFields"/>
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['headlineOptional']; ?>: <span class="errorText"><?php if(!empty($errorHeadline)) echo $errorHeadline; else echo "&nbsp;"; ?></span><br/>
							<input name="headline" type="text" size="40" value="<?php echo $headline; ?>" class="guideTextFields"/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs" valign="top">
							<table style="width:582px; table-layout: fixed;" cellspacing="0" cellpadding="0">
								<tr>
									<?php
									if(!empty($BBCodes)) {
									?>
									<td valign="top" style="padding-right:20px;" class="guideInputsInset">
										<?php echo $lang['BBCode']; ?>: <span id="BBCodeInfo" class="guideBBCodeInfo"><?php echo $lang['BBCodeTip']; ?></span><br/>
										<?php
										foreach($BBCodes as $element) {
										?>
										<input type="button" value="<?php echo $element['display']; ?>" style="margin-bottom: 4px;"<?php if(!empty($element['accesskey'])) echo " accesskey=\"".$element['accesskey']."\""; ?> onclick="addBBCode('<?php echo htmlentities($element['code']); ?>');" onmouseover="showBBCodeInfo('<?php echo $element['info']; if(!empty($element['accesskey'])) echo " (alt+".$element['accesskey'].")"; ?>');" onmouseout="hideBBCodeInfo();" class="guideButton"/>
										<?php
										}
										?>
										<br/>
										<input type="checkbox" id="enableAutoBBCode" name="enableAutoBBCode" checked="checked"/><span class="normalText"><?php echo $lang['enableAutoBBCode']; ?></span>
									</td>
									<?php
									}
									?>
									<td valign="top" style="width:160px;" class="guideInputsInsetLeft">
										<?php echo $lang['undoRedo']; ?>:<br/>
										<input type="button" id="undo" name="undo" value="<?php echo $lang['undo']; ?>" class="guideButton" onclick="textUndo();"/> <input type="button" id="redo" name="redo" value="<?php echo $lang['redo']; ?>" class="guideButton" onclick="textRedo();"/><br/>
										<input type="checkbox" id="enableUndo" name="enableUndo" checked="checked" onclick="totalResetUndo();"/><span class="normalText">Enable Undo</span>
									</td>
								</tr>
							</table>
						</td>
					</tr>			
					<tr>
						<td class="guideInputs">
							<?php echo $lang['text']; ?>: <span class="errorText"><?php if(!empty($errorText)) echo $errorText; else echo "&nbsp;"; ?></span><br/>
								<textarea id="text" name="text" rows="15" cols="70" class="guideTextFields" onkeyup="messageFieldChangeUp();" onkeypress="messageFieldChangePress(event);"><?php echo $text; ?></textarea>
						</td>
					</tr>
					<?php
					if($attach) {
					?>
					<tr>
						<td class="guideInputs">
							<script type="text/javascript">
								function showHideAttachments() {
									if(document.getElementById("attachmentArea").style.display == "none") {
										document.getElementById("attachmentArea").style.display = "block";
									}	
									else {
										document.getElementById("attachmentArea").style.display = "none";
									}		
								}	
							</script>	
							<?php echo $lang['attachments']; ?> <input type="checkbox" name="enableAttachments" onclick="showHideAttachments();"<?php if($enableAttachments) echo " checked=\"checked\""; ?>/> <span class="errorText"><?php if(!empty($errorUploads)) echo $errorUploads; else echo "&nbsp;"; ?></span>
							<div id="attachmentArea" style="<?php if(!$enableAttachments) echo "display:none; "; ?>padding-left:10px;">
								<?php 
								$i = 0;
								if(!empty($attachmentUploads) && is_array($attachmentUploads)) {
									foreach($attachmentUploads as $upload) {
								?>
								<div style="float:left;"><span class="uploadAttachmentFilename"><?php echo $upload['filename']; ?></span></div>
								<div style="text-align:right;"><?php echo $lang['delete']; ?> <input type="checkbox" name="deleteAttachmentSelect[]" value="<?php echo $i ?>"/></div>
								<input type="hidden" name="attachmentFilename[]" value="<?php echo $upload['filename']; ?>" class="guideTextFields"/>
								<input type="hidden" name="attachmentNumber[]" value="<?php echo $upload['attachmentNumber']; ?>"/>
								<?php
										$i++;		
									}
								?>
								<div align="center" style="padding-bottom:5px;">
									<input type="submit" name="deleteAttachment" value="<?php echo $lang['deleteSelectedAttachments']; ?>" class="guideButton"/>
								</div>
								<?php
								}
								if($i < $forumSettings['maxNumberOfAttachments']) {
								?>
								<div style="float:left;">
									<?php echo $lang['file']; ?>: <input type="file" name="fileUpload"/>
								</div>
								<div style="text-align:right;">
									<input type="submit" name="fileUploadSubmit" value="<?php echo $lang['uploadFile'] ?>" class="guideButton"/> 
								</div>
								<?php
								}
								else 
									echo "<span class=\"errorText\">".$lang['canNotUploadMoreAttachments']."</span>";
								?>
								<input type="hidden" name="attachTempname" value="<?php echo $attachTempname; ?>"/>
								<input type="hidden" name="attachmentNumberCount" value="<?php echo $attachmentNumberCount; ?>"/>
							</div>
						</td>
					</tr>	
					<?php
					}
					?>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['disableBBCode']; ?>:<br/>
							<input name="disableBBCode" type="checkbox" value="1"<?php if($disableBBCode) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
					<tr>
						<td class="guideInputs">
							<?php echo $lang['disableSmilies']; ?>:<br/>
							<input name="disableSmilies" type="checkbox" value="1"<?php if($disableSmilies) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
					<?php
					if($forumVariables['inlogged'] && $forumSettings['emailActivated']) {
					?>
					<tr>
						<td class="guideInputs">		
							<?php echo $lang['notifyByEmailWhenReply']; ?>:<br/>
							<input name="notifyWhenReply" type="checkbox" value="1"<?php if($notifyWhenReply) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td class="guideInputs">			
							<?php echo $lang['attachSignature']; ?>:<br/>
							<input name="attachSign" type="checkbox" value="1"<?php if($attachSign) echo " checked=\"checked\""; ?>/>
						</td>
					</tr>
				</table>
				<br/><br/>
			</td>
			<?php
				if(!empty($smilies)) {
			?>	
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['smilies']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td style="width:170px;" class="guideInputs">
							<?php echo $lang['pickOne']; ?>:<br/>
							<?php
							foreach($smilies as $element) {
							?>
							<img src="images/smilies/<?php echo $element['fileName']; ?>" alt="<?php echo $element['description']; ?>" title="<?php echo $element['description']; ?>" style="cursor: pointer;" onclick="addSmilie('<?php echo $element['find']; ?>');"/>&nbsp;
							<?php
							}
							?>
						</td>
					</tr>
				</table>			
			</td>
			<?php
			}
			?>
		</tr>
	</table>	
<?php
$backAction = "\"self.close();\"";
$backName = "\"&lt;&lt; ".$lang['close']."\"";
$nextName = "\"".$lang['send']." &gt;&gt;\"";
$viewPreviewButton = true;

include("include/guideBottom.php");
?>