<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<footer class="footer acal-footer">
    <div class="container">
        <p class="text-muted acal-text-muted">Page rendered in <strong>{elapsed_time}</strong> seconds.</p>
    </div>
</footer>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> -->
<script src="<?php echo site_url('assets/js/jquery-1.12.4.js');?>"></script>
<script src="<?php echo site_url('assets/js/jquery-ui-1.12.0.custom/jquery-ui.js');?>"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo site_url('assets/js/bootstrap.min.js');?>"></script>

<?php
  // optional js
  echo $before_body;
?>

</body>
</html>
