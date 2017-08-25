<div class="row">
  <div class="col-lg-4 col-lg-offset-4">
    <h1 style="margin-top:45px;">Set password</h1>

    <div class="alert alert-info" role="alert">
      Before activating your account, you must set a password.
    </div>

    <?php
      echo $this->session->flashdata('message'); // output any flashdata session messages
    ?>

    <?php echo form_open('',array('role'=>'form'));?>

    <div class="form-group">
      <?php
      echo form_label('Set password','password');
      echo form_error('password');
      echo form_password('password','','class="form-control"');
      ?>
    </div>
    <div class="form-group">
      <?php
      echo form_label('Confirm password','password_confirm');
      echo form_error('password_confirm');
      echo form_password('password_confirm','','class="form-control"');
      ?>
    </div>
    <?php echo form_submit('submit', 'Set password', 'class="btn btn-primary btn-lg btn-block"');?>

    <?php echo form_close();?>

  </div>
</div>
