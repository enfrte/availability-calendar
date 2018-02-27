<?php defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="row">
  <div class="col-lg-4 col-lg-offset-4">

    <h1 style="margin-bottom:35px;">Create requirements</h1>
    <?php echo $this->session->flashdata('message');?>
    <?php echo form_open('',array('role'=>'form'));?>

    <div class="form-group">
      <?php
      echo form_label('Requirement name','requirements_title');
      echo form_error('requirements_title');
      echo form_input('requirements_title',set_value('requirements_title'),'class="form-control" required');
      ?>
    </div>

    <div class="form-group">
      <?php
      echo form_label('Requirement description (optional)','requirements_description');
      echo form_error('requirements_description');
      echo form_textarea('requirements_description',set_value('requirements_description'),'class="form-control" style="height:200px;"');
      ?>
    </div>


    <?php echo form_submit('submit', 'Create requirement', 'class="btn btn-primary btn-lg btn-block"');?>
    <?php echo form_close();?>

  </div>
</div>