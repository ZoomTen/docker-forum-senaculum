<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
class errorHandler {

	function errorHandler(){}

	function error($headLine, $text)
	{
		global $lang;
		
		header("Content-type: text/html; charset=iso-8859-1");
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
		<html>
			<head>
				<title>
					<?php $lang['error']; ?>, <?php echo $headLine; ?>
				</title>
				<link rel="stylesheet" type="text/css" href="style.css"/>
			</head>
			<body class="guideBody">
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td valign="top" class="errorHeading">
							<?php $lang['error']; ?>, <?php echo $headLine;?>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<table cellspacing="0" cellpadding="5">
								<tr>
									<td valign="top" class="errorText">
										<?php echo $text;?><br/>
									</td>	
								</tr>
							</table>	
						</td>
					</tr>
				</table>				
			</body>
		</html>
		<?php
		die;
	}
}
?>