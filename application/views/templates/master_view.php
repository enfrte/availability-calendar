<?php defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('templates/_parts/master_header_view'); ?>

<div class="container">
  <div class="main-content">
    <!-- 
      This is the master view: /templates/master_view.php
      The view you should see when not logged in. -->
    <?php echo $the_view_content; ?>
  </div>
</div>

<?php
  // admin foot is currently no different to the public footer
  $this->load->view('templates/_parts/admin_master_footer_view');
?>
