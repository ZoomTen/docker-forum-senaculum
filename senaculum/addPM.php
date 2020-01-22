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
	require_once("classes/PMHandler.php");
	require_once("classes/errorHandler.php");
	require_once("classes/control.php");
	require_once("classes/process.php");
	require_once("classes/permissionHandler.php");
	
	$error = new errorHandler;
	$auth = new logInOutHandler;
	$PM = new PMHandler;
	$control = new control;
	$process = new process;
	$permission = new permissionHandler;
	
	$errorSubject = "";
	$errorText = "";
	
	$subject = "";
	$text = "";
	if($forumVariables['alwaysAllowBBCode'])
		$disableBBCode = false;
	else
		$disableBBCode = true;
	if($forumVariables['alwaysAllowSmilies'])
		$disableSmilies = false;
	else
		$disableSmilies = true;
	$attachSign = $forumVariables['alwaysDisplaySign'];
	$preview = false;
	
	if(empty($_GET['id']))
	{
		
		$reciver = "";
		$errorReciver = "";
		if(isset($_POST['submit'])) {
			if($_POST['submit'] != $lang['find']) {
				require_once("classes/memberHandler.php");
				$member = new memberHandler;
				$memberID = $member->getMemberID($_POST['reciver']);
				if(empty($memberID)) {
					$reciver = $_POST['reciver'];
					$errorReciver = $lang['userNotExist'];
				}	
				else 
					header("location: addPM.php?id=".$memberID);
			}
			if($_POST['submit'] == $lang['find']) {
				require_once("classes/searchHandler.php");
				$search = new searchHandler;
				$users = $search->user($_POST['reciver']);
				if(empty($users))
					$reciver = $_POST['reciver'];
				else
					$reciver = $users[0]['userName'];	
			}		
		}
			
		$title = $lang['sendAPM'];
		$heading = $lang['sendAPM'];
		$help = $lang['addPMPage1Help'];
	
		include("include/guideTop.php");
	
?>
<form action="addPM.php" name="userSelect" method="post">
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['sendTo']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['to']; ?>: <?php echo $lang['username']; ?> <span class="errorText"><?php if(!empty($errorReciver)) echo $errorReciver; else echo "&nbsp;"; ?></span><br/>
							<input type="text" name="reciver" size="40" value="<?php echo $reciver; ?>" maxlength="50" class="guideTextFields"/>
							<input type="submit" name="submit" value="<?php echo $lang['find']; ?>" class="guideButton"/>
						</td>
					</tr>
					<?php
					if(!empty($users)) {
					?>		
					<tr>
						<td class="guideInputs">
							<?php echo $lang['findResult']; ?>:<br/>
							<select name="users" class="guideDropDown" onChange="userSelect.reciver.value = this.options[this.selectedIndex].value;">
					<?php			
						foreach($users as $user) {
					?>
								<option value="<?php echo $user['userName']; ?>"><?php echo $user['userName']; ?></option>
					<?php
						}
					?>
							</select>
						</td>
					</tr>
					<?php			
					}	
					?>
				</table>
			</td>
			<td align="left" valign="top" class="guideBoxHeading">
				<?php echo $lang['help']; ?>:<br/>
				<table class="guideEHelp" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<?php echo $lang['addPMPage1Helptext']; ?>
						</td>
					</tr>
				</table>
			</td>	
		</tr>
	</table>				
<?php		
		$backAction = "\"self.close();\"";
		$backName = "\"<< ".$lang['close']."\"";
		$nextName = "\"".$lang['next']." >>\"";

		include("include/guideBottom.php");
		die();
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
	
	if(!$forumVariables['inlogged'])
	{
		$error->guide($lang['notLoggedIn'], $lang['notLoggedInPleaseLogin'], true);
	}

if(isset($_GET['answer'])) {
	$answer = $PM->getOne($_GET['answer'], true);
	$subject = $answer['subject'];
	if(substr($subject,0,strlen($lang['replyShortening'])) != $lang['replyShortening'] && $subject != "")
		$subject = $lang['replyShortening']." ".$subject;
	if(isset($_GET['quote']))	
		$text = "[quote=".$answer['senderUserName']."]".$answer['text']."[/quote]";	
}
	if(isset($_POST['preview'])) {
		$subject = $_POST['subject'];
		$text = $_POST['text'];
		if(isset($_POST['disableBBCode']))
			$disableBBCode = true;
		else
			$disableBBCode = false;
		if(isset($_POST['disableSmilies']))
			$disableSmilies = true;
		else
			$disableSmilies = false;
		if(isset($_POST['attachSign']))		
			$attachSign = true;
		else
			$attachSign = false;
		$preview = true;
	}	
	elseif(isset($_POST['subject']))
	{
		$errorSubject=$control->text($_POST['subject'], 1, 50);
		$errorText=$control->text($_POST['text'], 1, 8000);
		
		if(isset($_POST['disableBBCode']))
			$disableBBCode = true;
		else
			$disableBBCode = false;
		if(isset($_POST['disableSmilies']))
			$disableSmilies = true;
		else
			$disableSmilies = false;
		if(isset($_POST['attachSign']))		
			$attachSign = true;
		else
			$attachSign = false;
		
		if(!empty($errorSubject)||!empty($errorText))
		{
			$subject = $_POST['subject'];
			$text = $_POST['text'];
		}
		
		if(empty($errorSubject)&&empty($errorText))
		{
			$PM->add($_POST['subject'], $_POST['text'], $forumVariables['inloggedMemberID'], $_GET['id'], $disableSmilies, $disableBBCode, $attachSign);
			$nextAction = "index.php";
			$error->done($lang['PMSent1'],$lang['PMSent2'],$nextAction);
		}
	}
	
	require_once("classes/memberHandler.php");
	$member = new memberHandler;
	$memberName = $member->getOne($_GET['id'], false);
	$memberName = $memberName['userName'];
	
	require_once("classes/BBCodeHandler.php");
	require_once("classes/smilieHandler.php");
	$BBCode = new BBCodeHandler;
	$smilie = new smilieHandler;
	$BBCodes = $BBCode->getAll();
	$smilies = $smilie->getAll();
	
	$title = $lang['sendAPMTo1'].$memberName.$lang['sendAPMTo2'];
	$heading = $lang['sendAPMTo1'].$memberName.$lang['sendAPMTo2'];
	$help = $lang['addPMPage2Help1'].$memberName.$lang['addPMPage2Help2'];
	
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
<form action="addPM.php?id=<?php echo $_GET['id']?>" method="post">
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
									<td class="PMListHeading1">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td align="left" valign="middle">
													<span class="PMListHeadingSubject"><?php echo $process->headline($subject); ?></span> <span class="PMListHeadingSent"><?php echo $lang['sent']; ?>: <i><?php echo $other->dateParse($forumVariables['dateFormat'],time()); ?></i></span>
												</td>
												<td class="PMListHeadingAnswer" align="right" valign="middle">
													&nbsp;
												</td>
											</tr>
										</table>
									</td>
									<td valign="top" class="PMListFrom1" rowspan="3">
										<b><?php echo $member['userName']; ?></b><br/>
										<?php if(!empty($member['avatar'])) echo "<img src=\"images/avatars/".$member['avatar']."\" alt=\"".$lang['avatar']."\"/>"; ?><br/><br/>
										<?php if(!empty($member['location'])) echo $lang['location'].": ".$member['location']."<br/>"; ?>
									</td>
								</tr>
								<tr>
									<td valign="top" class="PMListMessage1">
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
									<td valign="bottom" class="PMListSignature1">
										<?php
										if(!empty($member['signature']) && $attachSign)
											echo "_______________________<br/>\n".$process->text($member['signature']);
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
				<?php echo $lang['input']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
						<td class="guideInputs">
							<?php echo $lang['subject']; ?>: <span class="errorText"><?php if(!empty($errorSubject)) echo $errorSubject; else echo "&nbsp;"; ?></span><br/>
							<input name="subject" type="text" size="40" value="<?php echo $subject; ?>" maxlength="50" class="guideTextFields"/>
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
										<input type="checkbox" id="enableUndo" name="enableUndo" checked="checked" onclick="totalResetUndo();"/><span class="normalText"><?php echo $lang['enableUndo']; ?></span>
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