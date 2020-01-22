<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
class attachmentHandler {
	
	function attachmentHandler() {}
	
	function upload($file,$attachNumber,$tempName="") {
		if(is_writable("./attachments/temp/")) {
			$this->removeOldTempfiles(); 
			if(empty($tempName)) {
				$time = time();
				while(file_exists("./attachments/temp/".$time."_1.tmp" || "./attachments/temp/".$time."_2.tmp" || "./attachments/temp/".$time."_3.tmp"))
					$time = time();
				$tempName = $time;
			}
			if(!move_uploaded_file($file['tmp_name'],"./attachments/temp/".$tempName."_".$attachNumber.".tmp"))
				return false;
				
			return $tempName;
		}
		else
			return false;
	}
	
	function unload($attachNumber,$tempName) {
		if(is_writeable("./attachments/temp/")) {
			if(!unlink("./attachments/temp/".$tempName."_".$attachNumber.".tmp"))
				return false;
			else 
				return true;	
		}
		else
			return false;
	}
	
	function add($postID,$attachmentNumber,$tempName,$filename) {
		if(is_writable("./attachments/") && file_exists("./attachments/temp/".$tempName."_".$attachmentNumber.".tmp")) {
			if(!is_dir("./attachments/".$postID))
				mkdir("./attachments/".$postID);
			copy("./attachments/temp/".$tempName."_".$attachmentNumber.".tmp","./attachments/".$postID."/".$filename);
			unlink("./attachments/temp/".$tempName."_".$attachmentNumber.".tmp");	
			return true;
		}
		else 
			return false;
	}
	
	function get($postID) {
		if(is_readable("./attachments/".$postID."/")) {
			$dirHandle = opendir("./attachments/".$postID."/");
			while(($file = readdir($dirHandle)) !== false) {
				if ($file!="." && $file!="..") {
					$currentFile = "./attachments/".$postID."/".$file;
					if(is_file($currentFile)) {
						$files[] = $file;
					}	
				}
			}	
			closedir($dirHandle);
			if(empty($files))
				return false;
			else
				return $files;	
		}
		else
			return false;
	}
	
	function remove($postID,$filename){
		if(is_writable("./attachments/".$postID."/")) {
			if(@unlink("./attachments/".$postID."/".$filename))
				return true;
			else
				return false;	
		}
		else
			return false;
	}
	
	function removeAll($postID) {
		if(file_exists("./attachments/".$postID."/") && is_writable("./attachments/".$postID."/")) {
			if($files = $this->get($postID)) {
				foreach($files as $file) {
					@unlink("./attachments/".$postID."/".$file);
				}
				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	
	function removeOldTempfiles() {
		if(is_readable("./attachments/temp/") && is_writable("./attachments/temp/")) {
			$dirHandle = opendir("./attachments/temp/");
			while(($file = readdir($dirHandle)) !== false) {
				if ($file!="." && $file!="..") {
					$currentFile = "./attachments/temp/".$file;
					if(is_file("./attachments/temp/".$file)) {
						$explodedFile = explode("_",$file);
						if($explodedFile[0] < time()-3600) {
							@unlink($currentFile);
						}
					}
				}
			}
			closedir($dirHandle);	
			if(empty($files))
				return false;
			else
				return true;	
		}
		else
			return false;
	}
}
?>