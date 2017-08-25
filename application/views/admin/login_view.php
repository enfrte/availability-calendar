<div class="row">
  <div class="col-lg-4 col-lg-offset-4">
    <h1 style="margin-top:45px;"><?php echo $this->config->item('cms_title');?></h1>
    <hr>
    <h1>Login</h1>
    <?php
      echo $this->session->flashdata('message'); // output any flashdata session messages
    ?>
    <?php echo form_open('',array('role'=>'form'));?>
      <div class="form-group">
        <?php echo form_label('Email','identity');?>
        <?php echo form_error('identity'); // feild specific error - like required ?>
        <?php echo form_input('identity','','class="form-control"');?>
      </div>
      <div class="form-group">
        <?php echo form_label('Password','password');?>
        <?php echo form_error('password');?>
        <?php echo form_password('password','','class="form-control"');?>
      </div>
      <div class="form-group">
        <label>
          <?php echo form_checkbox('remember','1',FALSE);?> Remember me
        </label>
      </div>
      <?php echo form_submit('submit', 'Log in', 'class="btn btn-primary btn-lg btn-block" style="margin-bottom:15px;"');?>
      <p>
        <?php echo anchor('admin/forgot_password', 'Forgot password?');?>
      </p>

    <?php echo form_close();?>
  </div>
</div>
