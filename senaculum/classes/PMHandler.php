<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once('dbHandler.php');
class PMHandler {
	function PMHandler () {}
	
	function add($subject, $text, $sender, $reciver, $disableSmilies=false, $disableBBCode=false, $attachSign=false) {
		$db = new dbHandler;
		$subject = $db->SQLsecure($subject);
		$text = $db->SQLsecure($text);
		$sender = $db->SQLsecure($sender);
		$reciver = $db->SQLsecure($reciver);
		
		if($disableBBCode)
			$disableBBCode = 1;
		else
			$disableBBCode = 0;
		if($disableSmilies)
			$disableSmilies = 1;
		else
			$disableSmilies = 0;
		if($attachSign)
			$attachSign = 1;
		else
			$attachSign = 0;	
		
		$time = time();
		$sql = "INSERT INTO _'pfx'_PM(subject,text,date,sender,reciver,`read`,senderRemoved,reciverRemoved,disableBBCode,disableSmilies,attachSign) VALUES('".$subject."','".$text."','".time()."','".$sender."','".$reciver."','0','0','0','".$disableSmilies."','".$disableSmilies."','".$attachSign."')";
		$db->runSQL($sql);
		
		//Check if the reciver will get a notification e-mail and send an e-mail if so
		$sql = "SELECT notifyNewPM, email FROM _'pfx'_members WHERE memberID = '".$reciver."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchArray($result);
		if($row['notifyNewPM']) {
			require_once("./classes/mail.php");
			$mail = new mail;
			global $forumSettings;
			global $lang;
			$subject = $lang['newPMMailSubject'];
			$message = $lang['newPMMailMessage1'].$forumSettings['forumName']." ( http://".$forumSettings['forumDomainName']." )".$lang['newPMMailMessage2']."http://".$forumSettings['forumDomainName'].$forumSettings['forumScriptPath'].$lang['newPMMailMessage3'];
			$mail->send($row['email'],$subject,$message);
		}
	}
	
	function getAll($type,$sort) {
		global $forumVariables;
		$db = new dbHandler;
		
		if($type == "outbox")
			$sql = "SELECT * FROM _'pfx'_PM, _'pfx'_members WHERE _'pfx'_PM.reciver = _'pfx'_members.memberID AND _'pfx'_PM.sender = '".$db->SQLsecure($forumVariables['inloggedMemberID'])."' AND _'pfx'_PM.senderRemoved = 0";
		if($type == "inbox")
			$sql = "SELECT * FROM _'pfx'_PM, _'pfx'_members WHERE _'pfx'_PM.sender = _'pfx'_members.memberID AND _'pfx'_PM.reciver = '".$db->SQLsecure($forumVariables['inloggedMemberID'])."' AND _'pfx'_PM.reciverRemoved = 0 AND _'pfx'_PM.read = 1";
		if($type == "new")
			$sql = "SELECT * FROM _'pfx'_PM, _'pfx'_members WHERE _'pfx'_PM.sender = _'pfx'_members.memberID AND _'pfx'_PM.reciver = '".$db->SQLsecure($forumVariables['inloggedMemberID'])."' AND _'pfx'_PM.reciverRemoved = 0 AND _'pfx'_PM.read = 0";
		if($sort == 2) 
			$sql .= " ORDER BY _'pfx'_PM.date ASC";
		else
			$sql .= " ORDER BY _'pfx'_PM.date DESC";		
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$i = 0;
		while($row = $db->fetchArray($result)) {
			$PM[$i]['PMID'] = $row['PMID'];
			$PM[$i]['subject'] = $row['subject'];
			$PM[$i]['text'] = $row['text'];
			$PM[$i]['date'] = $row['date'];
			$PM[$i]['sender'] = $row['sender'];
			$PM[$i]['reciver'] = $row['reciver'];
			$PM[$i]['read'] = $row['read'];
			$PM[$i]['senderRemoved'] = $row['senderRemoved'];
			$PM[$i]['reciverRemoved'] = $row['reciverRemoved'];
			$PM[$i]['senderMemberID'] = $row['memberID'];	
			$PM[$i]['senderUserName'] = $row['userName'];
			$PM[$i]['senderAdmin'] = $row['admin'];
			$PM[$i]['senderSignature'] = $row['signature'];
			//$PM[$i]['senderLoginDate1'] = $row['loginDate1'];
			//$PM[$i]['senderLoginDate2'] = $row['loginDate2'];
			$PM[$i]['senderLocation'] = $row['location'];
			$PM[$i]['senderAvatar'] = $row['avatar'];
			$PM[$i]['disableSmilies'] = $row['disableSmilies'];
			$PM[$i]['disableBBCode'] = $row['disableBBCode'];
			$PM[$i]['attachSign'] = $row['attachSign'];
			
			if($PM[$i]['disableSmilies'] || $PM[$i]['disableBBCode']) {
				$processText[$i]['text'] = $row['text'];
				$processText[$i]['disableSmilies'] = $PM[$i]['disableSmilies'];
				$processText[$i]['disableBBCode'] = $PM[$i]['disableBBCode'];
			}
			else
				$processText[$i] = $row['text'];
			$processSubject[$i] = $row['subject'];
			$processSignature[$i] = $row['signature'];
			$i++;
		}
		
		require_once("process.php");
		$process = new process;
		$processText = $process->text($processText);
		$processSubject = $process->headline($processSubject);
		$processSignature = $process->text($processSignature);
		$i = 0;
		foreach($PM as $element) {
			$PM[$i]['text'] = $processText[$i];
			$PM[$i]['subject'] = $processSubject[$i];
			$PM[$i]['senderSignature'] = $processSignature[$i];
			$i++;
		}
			
		return $PM;
	}
	function getOne($PMID, $raw=false) {
		$db = new dbHandler;
		$PMID = $db->SQLsecure($PMID);
		require_once("process.php");
		$process = new process;
		
		$sql = "SELECT * FROM _'pfx'_PM, _'pfx'_members WHERE _'pfx'_PM.sender = _'pfx'_members.memberID AND _'pfx'_PM.PMID = '".$PMID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$PM['PMID'] = $row->PMID;
		if(!$raw) {
			$PM['subject'] = $process->headline($row->subject);
			if($row->disableSmilies || $row->disableBBCode) {
				$processText['text'] = $row->text;
				$processText['disableSmilies'] = $row->disableSmilies;
				$processText['disableBBCode'] = $row->disableBBCode;
				$PM['text'] = $process->text($processText);
			}
			else
				$PM['text'] = $process->text($row->text);
		}
		else {
			$PM['subject'] = $row->subject;
			$PM['text'] = $row->text;
		}	
		$PM['date'] = $row->date;
		$PM['sender'] = $row->sender;
		$PM['reciver'] = $row->reciver;
		$PM['read'] = $row->read;
		$PM['senderRemoved'] = $row->senderRemoved;
		$PM['reciverRemoved'] = $row->reciverRemoved;
		$PM['subject'] = $row->subject;
		$PM['senderMemberID'] = $row->memberID;
		$PM['senderUserName'] = $row->userName;
		$PM['senderAdmin'] = $row->admin;
		$PM['senderLoginDate1'] = $row->loginDate1;
		$PM['senderLoginDate2'] = $row->loginDate2;
		$PM['senderLocation'] = $row->location;
		$PM['senderAvatar'] = $row->avatar;
		$PM['senderSignature'] = $process->text($row->signature);
		$PM['senderDisableSmilies'] = $row->disableSmilies;
		$PM['senderDisableBBCode'] = $row->disableBBCode;
		$PM['senderAttachSign'] = $row->attachSign;
		
		$sql = "SELECT * FROM _'pfx'_PM, _'pfx'_members WHERE _'pfx'_PM.reciver = _'pfx'_members.memberID AND _'pfx'_PM.PMID = '".$PMID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$PM['reciverMemberID'] = $row->memberID;
		$PM['reciverUserName'] = $row->userName;
		$PM['reciverAdmin'] = $row->admin;
		$PM['reciverLoginDate1'] = $row->loginDate1;
		$PM['reciverLoginDate2'] = $row->loginDate2;
		$PM['reciverLocation'] = $row->location;
		$PM['reciverAvatar'] = $row->avatar;
		$PM['reciverSignature'] = $process->text($row->signature);
		$PM['reciverDisableSmilies'] = $row->disableSmilies;
		$PM['reciverDisableBBBCode'] = $row->disableBBCode;
		$PM['reciverSignature'] = $row->signature;
		
		if($PM['read'] == 0) {
			$sql = "UPDATE _'pfx'_PM SET `read` = 1 WHERE PMID = '".$PMID."'";
			$db->runSQL($sql);
		}
		
		return $PM;
	}	
	function checkNew() {
		global $forumVariables;
		$db = new dbHandler;
		$sql = "SELECT COUNT(PMID) AS newPM FROM _'pfx'_PM WHERE reciver = '".$forumVariables['inloggedMemberID']."' AND `read` = 0";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$row = $db->fetchObject($result);
		return $row->newPM; 
	}
	function remove($PMID) {
		global $forumVariables;
		$db = new dbHandler;
		$PMID = $db->SQLsecure($PMID);
		$sql = "SELECT reciver, sender, senderRemoved, reciverRemoved FROM _'pfx'_PM WHERE PMID = '".$PMID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		if($row->reciver == $forumVariables['inloggedMemberID']) {
			if($row->senderRemoved) {
				$sql = "DELETE FROM _'pfx'_PM WHERE PMID = '".$PMID."'";
				$db->runSQL($sql);
			}
			else {
				$sql = "UPDATE _'pfx'_PM SET reciverRemoved = 1 WHERE PMID = '".$PMID."'";
				$db->runSQL($sql);
			}	
		}
		if($row->sender == $forumVariables['inloggedMemberID']) {
			if($row->reciverRemoved) {
				$sql = "DELETE FROM _'pfx'_PM WHERE PMID = '".$PMID."'";
				$db->runSQL($sql);
			}
			else {
				$sql = "UPDATE _'pfx'_PM SET senderRemoved = 1 WHERE PMID = '".$PMID."'";
				$db->runSQL($sql);
			}	
		}
	}
}
?>