<?php defined('BASEPATH') OR exit('No direct script access allowed');
// Page that allow you to edit project positions
?>

<div class="row">
  <div class="col-lg-8 col-lg-offset-2">

    <h1 style="padding-bottom:10px">Project positions</h1>
    <?php echo $this->session->flashdata('updated');?>
    <?php echo validation_errors(); ?>

    <!-- FORM -->
    <?php
      $hidden = array('running_day'=>$day,'project_id'=>$project_id);
      echo form_open('', array('role'=>'form'), $hidden);
    ?>

    <div class="form-inline pos-control-menu cal-card-hover">
      <h2><?php echo $project_name;?></h2>
      <div class="form-group">
        <?php
        // removed days[] from select because day is set by the controller.
        // this select is used to call other day results.
        echo form_label('Select day', '', array('style'=>'margin-right:10px;'));
        echo form_error(''); // see set_rules($field_name[, $field_label[, $rules]])
        $day = array( '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '0' => 'Sunday' );
        echo form_dropdown('', $day, $this->uri->segment(6), 'id="select_day" style="margin-right:10px;" class="form-control input-lg"'); // http://stackoverflow.com/questions/11133540/adding-class-and-id-in-form-dropdown
        ?>
      </div>
      <!-- Action buttons -->
      <button name="submit" type="submit" value="draft" class="btn btn-primary btn-lg acal-confirm" title="Do not make available publically">Save draft</button>
      <button name="submit" type="submit" value="publish" style="margin-left:10px;" class="btn btn-primary btn-lg acal-confirm" title="Save and publish the positions publically">Publish</button>
      <button name="submit" type="submit" value="delete" style="margin-left:10px;" class="btn btn-primary btn-lg acal-confirm" title="Delete the positions for this day">Delete day</button>
    </div>

    <!-- POSITION CARD CONTAINER -->
    <div class="acal-position-container">
    <?php
    if(count($project_positions) == 0)
    { // display default form
    ?>
    <div class="acal-card-container">

      <input type="hidden" name="id[]" value="">
      <div class="acal-card cal-card-hover">
        <div style="text-align:right;margin-bottom:10px;">
          <a href="javascript:;" class="remove-position-card"><strong>Remove position </strong><span class="glyphicon glyphicon-remove"></span></a>
        </div>
        <div class="row">
          <div class="form-group col-sm-6">
            <?php
            echo form_label('Title (required)','title[]');
            echo form_error('title[]');
            echo form_input('title[]','','class="form-control" required');
            ?>
          </div>
          <div class="form-group col-sm-6">
            <?php
            echo form_label('Maximum participants needed','max_vol[]');
            echo form_error('max_vol[]');
            $max_vol = array('name'=>'max_vol[]', 'type'=>'number', 'min'=>'1', 'max'=>'999', 'class'=>'form-control');
            echo form_input($max_vol);
            ?>
          </div>
        </div>
        <div class="form-group">
          <?php
          echo form_label('Description','description[]');
          echo form_error('description[]');
          echo form_textarea('description[]','','style="height:100px;" class="form-control"'); // works just like form_input
          ?>
        </div>
      </div>

    </div>
    <?php
  } else { // populate the positions with the existing data
    $max_vol = array('name'=>'max_vol[]', 'type'=>'number', 'min'=>'1', 'max'=>'999', 'value'=>'', 'class'=>'form-control');

    foreach ($project_positions as $p)
    {
    ?>
    <div class="acal-card-container">

      <input type="hidden" name="id[]" value="<?php echo $p->id; ?>">
      <div class="acal-card cal-card-hover">
        <div style="text-align:right;margin-bottom:10px;">
          <a href="javascript:;" class="remove-position-card"><strong>Remove position </strong><span class="glyphicon glyphicon-remove"></span></a>
        </div>
        <div class="row">
          <div class="form-group col-sm-6">
            <?php
            echo form_label('Title (required)','title[]');
            echo form_error('title[]');
            echo form_input('title[]',$p->title,'class="form-control" required');
            ?>
          </div>
          <div class="form-group col-sm-6">
            <?php
            echo form_label('Maximum participants needed','max_vol[]');
            echo form_error('max_vol[]');
            $max_vol['value'] = $p->max_vol; // update the value key
            echo form_input($max_vol);
            //echo form_input('max_vol[]',$p->max_vol,'class="form-control"');
            ?>
          </div>
        </div>
        <div class="form-group">
          <?php
          echo form_label('Description','description[]');
          echo form_error('description[]');
          echo form_textarea('description[]',$p->description,'style="height:100px;" class="form-control"'); // works just like form_input
          ?>
        </div>
      </div>

    </div>
    <?php
    }
  } // end form generation
    ?>
  </div>

    <input type="button" id="add_position_card" style="margin-top:15px;" class="btn btn-success btn-lg btn-block" value="Add position">
    <?php //echo form_submit('submit', 'Add position', 'style="margin-top:15px;" id="btAdd" class="btn btn-success btn-lg btn-block"');?>
    <?php echo form_close();?>
  </div>
</div>

<?php
  // create the controller uri base for the day select element argument to append to
  $controller_uri = base_url().$this->uri->slash_segment(1).$this->uri->slash_segment(2).$this->uri->slash_segment(3).$this->uri->slash_segment(4).$this->uri->slash_segment(5);
  // session variable for uri -- if the is a problem with the user action (actions draft, publish or delete), send the user back to the right place with args intact
  //$this->session->set_userdata( 'controller_uri_session', $controller_uri.$this->uri->segment(6) );
?>
<script>
  // These need to be set globally since we can't set php in linked js files
  // it's then used by the linked js file project_positions.js
  var controller_uri = "<?php echo $controller_uri ?>";
  // get the day of the select dropdown
  var select_day = document.getElementById('select_day'); // select_day.value
</script>
