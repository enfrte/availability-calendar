<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<div class="container">
<div class="alert alert-info" role="alert">To change your name, phone-number, or email, use the <a href="<?php echo site_url('contact/contact');?>" style="color:green;">contact admin form</a> and state the changes you want them to make.</div>
</div>

<div class="row">

  <div class="col-lg-4 col-lg-offset-4">

    <h1 style="margin-bottom:35px;">Profile page</h1>
    <?php
      echo $this->session->flashdata('message'); // output any flashdata session messages
    ?>

    <dl style="font-size:large;">
      <dt>Name:</dt>
      <dd><?php echo $user->first_name . ' ' . $user->last_name; ?></dd>
      <dt>Phone numer:</dt>
      <dd><?php if(empty($user->phone)) {echo "Not provided.";} else {echo $user->phone;} ?></dd>
      <dt>Username/Email:</dt>
      <dd><?php echo $user->email; ?></dd>
    </dl>

    <a href="<?php echo site_url('contact/contact');?>" class="btn btn-primary btn-lg btn-block">Contact admin</a>
    <?php //echo form_submit('submit', 'Save profile', 'class="btn btn-primary btn-lg btn-block"');?>

  </div>
</div>
