<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
global $lang;

if(!$forumSettings['guidesInPopups']) {
	$backAction = "\"history.back()\"";
	$backName = "\"&lt;&lt; Back\"";
}
if(!empty($nextAction)) {
	if($nextAction == "close") 
		$nextAction = "self.close();";
	//else
		//$nextAction = "window.location = '".$nextAction."';"
}
if(!isset($viewButtons) || !is_bool($viewButtons))
{
	$viewButtons = true;
}
if(!isset($viewPreviewButton))
	$viewPreviewButton = false;
if($forumSettings['guidesInPopups']) {
?>
<div style="position:fixed; bottom:0px;" class="guideActionArea">
<?php
}
else {
?>
<div class="guideActionArea">
<?php
}
?>
	<table width="100%" border="0">
		<tr>
			<?php
			if($viewButtons)
			{
			?>
			<td align="left" valign="bottom">
				<input onclick=<?php echo $backAction; ?> name="back" type="button" value=<?php echo $backName; ?> class="guideButton"/><br/>
			</td>
			<?php
			}
			else
			{
			?>
			<td align="left" valign="bottom">
				&nbsp;
			</td>
			<?php
			}
			if($forumSettings['guidesInPopups'] && ($page == "addForum.php" || $page == "editForum.php" || $page == "addPost.php" || $page == "editPost.php" || $page == "addThread.php" || $page == "editThread.php")) {
			?>
			<td align="center" valign="bottom" class="guideInputs">
				<?php echo $lang['reloadOpener']; ?>: <input type="checkbox" name="updateOpener"<?php if(isset($_COOKIE['updateOpener'])) if($_COOKIE['updateOpener']) echo " checked=\"checked\"";?>/>
			</td>
			<?php
			}
			if(empty($nextAction) && $viewButtons) {
			?>
			<td align="right" valign="bottom">
				<?php
				if($viewPreviewButton) {
				?>
				<input type="submit" name="preview" value="<?php echo $lang['preview']; ?>" class="guideButton"/>
				<?php
				}
				?>
				<input name="submit" type="submit" value=<?php echo $nextName; ?> class="guideButton"/><br/>
			</td>
			<?php
			}
			else if($viewButtons) {
			?>
			<td align="right" valign="bottom">
				<input onclick="<?php echo $nextAction; ?>" name="next" type="button" value=<?php echo $nextName; ?> class="guideButton"/><br/>
			</td>
			<?php
			}
			else
			{
			?>
			<td align="right" valign="bottom">
				&nbsp;
			</td>
			<?php
			}
			?>
		</tr>
	</table>
<?php
if($viewButtons && empty($nextAction)) {
?>	
</form>
<?php
}
if(!$forumSettings['guidesInPopups']) {
	echo "</div>";
	$menu->getBottom($page);
}
else {
?>		</div>
	</body>
</html>
<?php
}
?>