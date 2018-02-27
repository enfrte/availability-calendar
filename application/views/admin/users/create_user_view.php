<?php defined('BASEPATH') OR exit('No direct script access allowed');
//print_r($groups);
?>

<div class="row">
  <div class="col-lg-4 col-lg-offset-4">

    <!--<pre><?php //print_r($current_user);
        if($this->ion_auth->in_group('super_admin')) { echo "SUPER ADMIN"; }
        if($this->ion_auth->in_group('admin')) { echo "ADMIN"; }
      ?></pre>
    -->

    <h1>Create user</h1>
    <?php echo $this->session->flashdata('message');?>
    <?php echo form_open('',array('role'=>'form'));?>

    <?php if($this->ion_auth->in_group('super_admin')) : ?>
    <div class="form-group">
      <?php
      echo form_label('Set user rights', 'groups[]');
      echo form_error('groups[]'); // see set_rules($field_name[, $field_label[, $rules]])
      $userRights = array( '2' => 'Member', '1' => 'Administrator' );
      echo form_dropdown('groups[]', $userRights, '2', 'class="form-control"'); // http://stackoverflow.com/questions/11133540/adding-class-and-id-in-form-dropdown
      ?>
    </div>
    <?php endif; ?>

    <div class="form-group">
      <?php
      echo form_label('First name','first_name');
      echo form_error('first_name');
      echo form_input('first_name',set_value('first_name'),'class="form-control"');
      ?>
    </div>

    <div class="form-group">
      <?php
      echo form_label('Last name','last_name');
      echo form_error('last_name');
      echo form_input('last_name',set_value('last_name'),'class="form-control"');
      ?>
    </div>

    <div class="form-group">
      <?php
      echo form_label('Phone','phone');
      echo form_error('phone');
      echo form_input('phone',set_value('phone'),'class="form-control"');
      ?>
    </div>

    <div class="form-group">
      <?php
      echo form_label('Email','email');
      echo form_error('email');
      echo form_input('email','','class="form-control"');
      ?>
    </div>

    <?php if(!empty($requirements)) : ?>
      <div class="form-group">
        <fieldset>
          <legend>Assign user requirements (Optional)</legend>
          <?php foreach($requirements as $require) : ?>
            <div>
              <input type="checkbox" id="visability_<?php echo $require->id ?>" name="requirements[]" value="<?php echo $require->id ?>">
              <label for="visability_<?php echo $require->id ?>"><?php echo $require->title ?></label>
            </div>
          <?php endforeach; ?>
        </fieldset>
      </div>
    <?php endif; ?>

    <?php echo form_submit('submit', 'Create user', 'class="btn btn-primary btn-lg btn-block"');?>
    <?php echo form_close();?>

  </div>
</div>
