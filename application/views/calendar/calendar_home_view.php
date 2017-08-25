<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-8 col-lg-offset-2">
    <?php
    echo validation_errors();
    // prompt the user to select a project day or view type
    if(!isset($_SESSION['selected_project_id']) && !isset($project)) {
      echo '<h1 class="cal-header-row">Select a project</h1>';
      echo '<div class="alert alert-info" role="alert">Please select a project from the Project menu.</div>';
    }
    else {
      //$calendar_month_name = DateTime::createFromFormat('m', $calendar_month, $timezone)->format('F');
    ?>
    <!-- TITLE -->
    <h2 style="text-align:center;">
      <strong>
        <?php
          echo (isset($_SESSION['selected_project_name']) ? $_SESSION['selected_project_name'] : 'Select a project from the menu');
        ?>
      </strong>
    </h2>

    <!-- DAYS -->
    <div class="calendar-container">

    <!--
      jQuery DATEPICKER
      User clicks call the calendar home controller. See /assets/js/acal/calendar_home.js
    -->
    <div id="datepicker"></div>
    <?php echo $this->session->flashdata('updated'); // form confirmation messages ?>

    <div id="form-container"><!-- Show hide the form based on user month navigation -->
    <?php

    if(count($calendar_positions) < 1) {
      echo '<div class="alert alert-info" role="alert" style="margin-top:15px;">Please select a project day from the calendar.</div>';
    }
    else if (isset($date)) {
      // FORM
      $hidden = array('date'=>$date); // removed 'attendee_checkbox[]'=>''
      echo form_open('',array('role'=>'form'),$hidden);

      foreach ($calendar_positions as $cp) {
        ?>
        <input type="hidden" name="up_id[]" value="<?php echo $cp->up_id; ?>">
        <div class="cal-pos-form">
          <div class="row">

            <div class="col-xs-8 col-xs-offset-2">
              <h3><?php echo $cp->title; ?></h3>
            </div>

            <?php if(!empty($cp->description)) { ?>
            <div class="col-xs-8 col-xs-offset-2">
              <p><?php echo $cp->description; ?></p>
            </div>
            <?php } ?>

            <div class="col-xs-8 col-xs-offset-2">
              <p><strong>Attendees:</strong> <?php
                // list all the attendees for each position
                $names = array();
                foreach ($get_positions_of_other_users as $attendee_names) {
                    if($cp->pos_id === $attendee_names->pos_id){
                      $names[] = $attendee_names->first_name . " " . $attendee_names->last_name;
                    }
                  }
                  echo implode( ', ', $names );
                  if(count($names) < 1){ echo "None"; }
              ?></p>
            </div>

            <div class="col-xs-8 col-xs-offset-2">
              <div class="checkbox">
                <label>
                  <input
                    name="attendee_checkbox[]"
                    type="checkbox"
                    value="<?php echo $cp->pos_id; ?>"
                    <?php
                      if($cp->volunteered == 'TRUE'){ echo 'checked="checked"'; }
                      if(count($names) >= $cp->max_vol && $cp->volunteered == 'FALSE' && $cp->max_vol !== NULL) {
                        echo "disabled";
                      }
                    ?>
                    >
                    <?php
                      if(count($names) < $cp->max_vol || $cp->volunteered == 'TRUE' || $cp->max_vol === NULL) {
                        echo "<strong>Enroll for this position</strong>";
                      }
                      else
                      {
                        echo '<strong class="bg-primary" style="background-color:red;">Maximum attendee limit reached</strong>';
                      }
                    ?>
                </label>
                <span> (Attendee limit:
                  <?php
                    if($cp->max_vol > 0) {
                      echo $cp->max_vol;
                    }
                    else {
                      echo "None";
                    }
                  ?>)</span>
              </div>
            </div>

          </div>
        </div>

        <?php
      } // END FORM LOOP
      echo form_submit('submit', 'Submit / Update', 'class="btn btn-success btn-lg btn-block" style="margin-bottom:15px;"');
      echo form_close();
    }
    echo '</div><!-- END FORM-CONTAINER -->';
    echo '<div id="calendar-message-container" class="to-show" style="margin-top:15px"><div class="alert alert-info" role="alert">(calendar-message-container) Select a date from the calendar.</div></div>';
    echo '</div><!-- END CALENDAR-CONTINER -->';

  } // END ELSE
  ?>
  <!--
  <pre><?php
  /*
  if (isset($calendar_positions)) {
    foreach ($calendar_positions as $cp) {
      echo "Title: $cp->title | Desc.: $cp->description | Max: $cp->max_vol | Volunteeded: $cp->volunteered\n";
    }
  } else { echo "Positions not retrieved.\n"; }
  if(isset($_SESSION['selected_project_name'])) { echo "SESSION project name: ".$this->session->selected_project_name."\n"; } else { echo "Project name not set\n"; }
  if(isset($_SESSION['selected_project_id'])) { echo "SESSION project id: ".$this->session->selected_project_id."\n"; } else { echo "SESSION project id not set\n"; }
  if(isset($project)) { echo "Project: ".$project."\n"; } else { echo "Project not set\n"; }
  if(isset($day)) { echo "Day: ".$day."\n"; } else { echo "Day not set\n"; }
  if(isset($date)) { echo "Date: ".$date."\n"; } else { echo "Date not set\n"; }
  */
  ?></pre>
  -->
  </div>
</div>


<script>
//var datepicker_month_range = <?php echo "'+".$this->config->item('datepicker_month_range')."m'"; ?>;
var datepicker_month_range = <?php echo "".$this->config->item('datepicker_month_range').""; ?>;
// get days in month the project does not run on for datepicker
var non_project_dates = <?php echo $non_project_dates; ?>;
// this will be used by calendar_home to redirect to a selected calendar date
var base_project_url = <?php echo '"'.site_url("calendar/calendar/home").'/'.$_SESSION['selected_project_id'].'/'.'"'; ?>;
<?php
if($skip_current_month === '1') {
  echo 'var base_project_date = "+'.$skip_current_month.'m";';
}
else {
  echo 'var base_project_date = "'.$date.'";';
}
?>
//var base_project_date = <?php echo '"'.$date.'+'.$skip_current_month.'m"'; ?>;

</script>
