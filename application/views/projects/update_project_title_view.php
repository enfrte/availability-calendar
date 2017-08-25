<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-4 col-lg-offset-4">

    <h1 style="margin-bottom:35px;">Edit project name</h1>
    <?php echo $this->session->flashdata('message');?>
    <?php echo form_open('',array('role'=>'form'));?>

    <div class="form-group">
      <?php
      echo form_label('New name for: '.$project_name,'project_title');
      echo form_error('project_title');
      echo form_input('project_title',set_value('project_title'),'class="form-control"');
      ?>
    </div>

    <?php echo form_submit('submit', 'Edit project name', 'class="btn btn-primary btn-lg btn-block"');?>
    <?php echo form_close();?>

  </div>
</div>
