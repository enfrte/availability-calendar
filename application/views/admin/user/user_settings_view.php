<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<div class="row">
  <div class="col-lg-4 col-lg-offset-4">

    <h1 style="margin-bottom:35px;">My settings</h1>
    <?php
      echo $this->session->flashdata('message'); // output any flashdata session messages
    ?>

    <?php
      echo form_open('',array('role'=>'form'));
    ?>
<div class="form-group">
      <div class="checkbox">
        <label><input type="checkbox">
        <?php
        echo form_checkbox('receive_email_reminder', '1', TRUE, 'type="checkbox-inline" id="receive_email_reminder"');
        ?>
        Receive atendee reminder email?</label>
      </div></div>

      <?php echo form_submit('submit', 'Update settings', 'class="btn btn-primary btn-lg btn-block" style="margin-top:20px;"');?>
    <?php echo form_close();?>
  </div>
</div>
