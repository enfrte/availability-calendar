<div class="row">
  <div class="col-lg-4 col-lg-offset-4">
    <h1 style="margin-bottom:35px;margin-top:45px;"><?php echo lang('forgot_password_heading');?></h1>
    <div class="alert alert-info" role="alert">
      <?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?>
    </div>

    <?php echo $showMessages; ?>

    <?php echo form_open('admin/forgot_password',array('role'=>'form'));?>

    <div class="form-group">
      <?php
      echo form_label('Enter your registered email','identity');
      echo form_error('identity');
      $emailData = [
        'name' => 'identity',
        'type' => 'email', 
        'class' => 'form-control'
      ];
      echo form_input($emailData);

      ?>
    </div>

    <?php echo form_submit('submit', lang('forgot_password_submit_btn'), 'class="btn btn-primary btn-lg btn-block"');?>

    <?php echo form_close();?>

  </div>
</div>
