<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

if($this->ion_auth->in_group('super_admin')) {
?>
  <div class="row">
    <div class="col-lg-12">
      <?php echo $showMessages; ?>
      <a href="<?php echo site_url('info/edit');?>" class="btn btn-primary btn-lg">Edit information</a>
    </div>
  </div>
<?php      
}
?>
<h1>
  <?php 
      echo $title_content;
  ?>
</h1>

<?php 
    echo $body_content;
?>
