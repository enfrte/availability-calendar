<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-4 col-lg-offset-4">

    <h1>Contact admin</h1>

    <?php 
      echo $showMessages; 
      //echo validation_errors(); 
    ?>

    <?php echo form_open('',array('role'=>'form'));?>

    <div class="form-group">
      <?php
      echo form_label('Title','title');
      echo form_error('title');
      echo form_input('title','','class="form-control" required');
      ?>
    </div>

    <div class="form-group">
      <?php
      echo form_label('Message','message');
      echo form_error('message');
      echo form_textarea('message','','style="height:200px;" class="form-control" required'); // works just like form_input
      ?>
    </div>

    <?php echo form_submit('submit', 'Submit', 'class="btn btn-primary btn-lg btn-block"');?>
    <?php echo form_close();?>

  </div>
</div>
