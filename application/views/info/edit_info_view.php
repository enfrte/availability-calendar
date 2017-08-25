<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-10 col-lg-offset-1">

    <h1>Create / Edit</h1>
    <h3>Create/Edit the information page</h3>

    <?php echo $this->session->flashdata('message');?>
    <?php echo validation_errors(); ?>

    <?php echo form_open('',array('role'=>'form'));?>

    <div class="form-group">
      <?php
      //if(isset($info_data->title)) { $title = $info_data->title; } else { $title = ""; }
      echo form_label('Title','title');
      echo form_error('title');
      echo form_input('title', $title_content, 'class="form-control" required');      
      ?>
    </div>

    <div class="form-group">
      <p>The text-area below supports markup (tables, headers, links, images etc...), but do not add document headers or meta tags.</p>
      <?php
      echo form_label('Content','body');
      echo form_error('body');
      echo form_textarea('body', $body_content, 'style="height:200px;" class="form-control" required'); // works just like form_input
      ?>
    </div>

    <div class="row">
        <div class="col-xs-6">
          <?php echo form_submit('submit', 'Save', 'class="btn btn-primary btn-lg btn-block"');?>
        </div>        
        <div class="col-xs-6">   
          <a href="<?php echo site_url('info');?>" class="btn btn-danger btn-lg btn-block">Cancel</a>
        </div>
    </div>

    <?php echo form_close();?>

  </div>
</div>
