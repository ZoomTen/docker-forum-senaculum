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

class settingHandler {

	function settingHandler() {}

	function getOne($setting) {
		global $forumSettings;
		if(!isset($forumSettings)) {
			$db = new dbHandler;
			$sql = "SELECT * FROM _'pfx'_settings";
			$result = $db->runSQL($sql);
			while($row = $db->fetchArray($result)) {
				$forumSettings[$row['settingName']] = $row['settingValue'];
			}
			return $forumSettings[$setting];
		}
		else {
			return $forumSettings[$setting];
		}
	}
	
	function getAll() {
		global $forumSettings;
		if(!isset($forumSettings)) {
			$db = new dbHandler;
			$sql = "SELECT * FROM _'pfx'_settings";
			$result = $db->runSQL($sql);
			while($row = $db->fetchArray($result)) {
				$forumSettings[$row['settingName']] = $row['settingValue'];
			}
			return $forumSettings;
		}
		else {
			return $forumSettings;
		}
	}
	function edit($setting, $value) {
		$db = new dbHandler;
		$setting = $db->SQLsecure($setting);
		$value = $db->SQLsecure($value);
		
		$sql = "UPDATE _'pfx'_settings SET settingValue = '".$value."' WHERE settingName = '".$setting."'";
		$db->runSQL($sql);
	}
}
?>