<?php defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('templates/_parts/admin_master_header_view'); ?>

<div class="container" style="padding-bottom:15px;">
  <div class="main-content">
    <?php echo $the_view_content; ?>
  </div>
</div>

<?php $this->load->view('templates/_parts/admin_master_footer_view');?>
