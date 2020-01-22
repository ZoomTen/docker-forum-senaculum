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

	require_once("classes/avatarHandler.php");
	require_once("classes/errorHandler.php");
	
	$error = new errorHandler;
	$avatar = new avatarHandler;
	
	$avatars = $avatar->getAll();
		
	$title = $lang['browseAvatars'];
	$heading = $lang['browseAvatars'];
	$help = $lang['avatarBrowserHelp'];
	
	include("include/guideTop.php");
?>
	<script type="text/javascript">
		function klick(fake,real) {
		opener.document.getElementById('member').avatarFake.value=fake;
		opener.document.getElementById('member').avatarTrue.value=real;
		window.close();
	}
	</script>
	<table cellpadding="0" cellspacing="10">
		<tr>
			<td align="left" valign="top" class="guideBoxHeading">
			<?php
			if(!$avatars)
			{
				echo $lang['noAvatars'];
			}
			else
			{
			?>
				<?php echo $lang['avatars']; ?>:<br/>
				<table cellspacing="0" cellpadding="3" class="guideInputArea">
					<tr>
					<?php
						$i = 1;
						foreach($avatars as $element)
						{
						if($i%7!=0)
						{
					?>
						<td class="guideAvatarBrowser" valign="bottom" align="center">
							<img src="<?php echo "images/avatars/public/".$element['fileName'] ?>" border="0" onClick="javascript:klick('<?php echo $element['fileName'] ?>','<?php echo $element['avatarID'] ?>');"/><br/>
							<?php
							if(!empty($element['name'])) 
								echo $element['name'] 
							?>
						</td>
					<?php
						}
						if($i%7==0)
						{
					?>
					</tr>
					<tr>
						<td class="guideAvatarBrowser" valign="bottom" align="center">
							<img src="<?php echo "images/avatars/public/".$element['fileName']?>" border="0" onClick="javascript:klick('<?php echo $element['fileName'] ?>','<?php echo $element['avatarID'] ?>');"/><br/>
							<?php
							if(!empty($element['name'])) 
								echo $element['name'] 
							?>
						</td>
					<?php
						}
					$i++;
					}
					?>
					</tr>
				</table>
				<?php
				}
				?>
			</td>
		</tr>
	</table>
<?php
$backAction = "\"self.close();\"";
$backName = "\"<< ".$lang['close']."\"";
$nextName = "\"".$lang['next']." >>\"";
$viewButtons = false;

include("include/guideBottom.php");
?>