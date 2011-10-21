<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Bacon Framework - It's tasty!</title>
    <?php echo $html->favicon("favicon.ico") ?>
    <?php echo $html->stylesheet("style/style.css") ?>
</head>
<body>
	<div class="middle">
		<?php echo $html->image("bacon_logo.jpg")."\n"?>
	    <?php echo $view; ?>
	    <h2>System Information</h2>
		<table align="center">
		    <tbody>
		        <tr>
		            <th><strong>Running</strong></th>
		            <td>Yes</td>
		        </tr>
		        <tr>
		            <th><strong>Bacon Framework</strong></th>
		            <td><?php echo BACON_VERSION ?></td>
		        </tr>
		        <tr>
		            <th><strong>PHP</strong></th>
		            <td><?php echo phpversion() ?></td>
		        </tr>
				<?php if(function_exists("apache_get_version")): ?>
		        <tr>
		            <th><strong>Server</strong></th>
		            <td><?php echo apache_get_version() ?></td>
		        </tr>
				<?php endif ?>		
		    </tbody>
		</table>
		<h2>Notifications</h2>
		<?php echo $html->notifications() ?>
	</div>
</body>
</html>