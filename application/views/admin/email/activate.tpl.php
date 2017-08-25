<html>
<body>
	<h2><?php echo sprintf(lang('email_activate_heading'), $identity);?></h2>
	<p>
		An account has been created for you at <?php echo $this->config->item('cms_title');?><br><br>
		<?php echo "Your user name is $identity";?><br><br>
		To activate the account, click the following link and create a password.
	</p>
	<p>
	<?php
		echo sprintf(lang('email_activate_subheading'), anchor('admin/activate_account/set_password/'. $id .'/'. $activation, lang('email_activate_link')));
	?>
	</p>
</body>
</html>
