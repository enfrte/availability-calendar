<div class="row">
  <div class="col-lg-4 col-lg-offset-4">

	<h1 style="margin-bottom:35px;"><?php echo lang('reset_password_heading');?></h1>

	<div id="infoMessage"><?php echo $message;?></div>

	<?php echo form_open('admin/reset_password/reset/' . $code);?>

		<div class="form-group">
			<label for="new_password"><?php echo sprintf(lang('reset_password_new_password_label'), $min_password_length);?></label> <br />
			<?php echo form_input($new_password, '','class="form-control"');?>
		</div>

		<div class="form-group">
			<?php echo lang('reset_password_new_password_confirm_label', 'new_password_confirm');?> <br />
			<?php echo form_input($new_password_confirm, '','class="form-control"');?>
		</div>

		<?php echo form_input($user_id);?>
		<?php echo form_hidden($csrf); ?>

		<?php echo form_submit('submit', lang('reset_password_submit_btn'), 'class="btn btn-primary btn-lg btn-block"');?>

	<?php echo form_close();?>

	</div>
</div>
