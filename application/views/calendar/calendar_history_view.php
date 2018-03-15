<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php echo form_open('', 'class="form-inline"'); ?>
  <div class="form-group">
    <select id="year" name="year" class="form-control"></select>
  </div>
  <div class="form-group">
    <select id="month" name="month" class="form-control"></select>
  </div>
  <button type="submit" name="submit" class="btn btn-success">Select</button>
<?php echo form_close(); ?>

<!-- DATE VIEW -->
<?php echo '<h3>Overview for ' . $_SESSION['selected_project_name'] . ' - ' . date_format(date_create($date), "M Y") . '</h3>'; ?>
<?php if (!empty($summary)): ?><!-- if summary is empty, no projects were conceived during this date. therefore there is no history to show -->
    <?php foreach ($history as $h): ?>
        <div class="panel panel-primary">
            <div class="panel-heading"><h3><?php echo date_format(date_create($h['date']), "D jS M Y"); ?></h3></div>
            <div class="panel-body">

              <?php if (!empty($h['position_info'])): ?>
                  <?php $at_least_one_attendence = null; ?>
                  <?php foreach ($h['position_info'] as $pi): ?>
                      <?php if (count($pi->attendees) > 0): ?><!-- Hide if no attendees -->
                        <h3><?php echo $pi->title;?> </h3>
                        <p><?php echo $pi->description;?></p>
                        <p><strong>Attendee limit: </strong><?php echo $pi->max_vol;?></p>
                        <p><strong>Attendees: </strong><?php foreach ($pi->attendees as $attendee) { echo "$attendee->full_name<br>"; } ?></p>
                        
                        <?php $at_least_one_attendence = true; ?>
                      <?php endif; ?>
                  <?php endforeach; ?>
                  <?php echo (!isset($at_least_one_attendence)) ? "<p>Nobody attendening on this day.</p>" : ''; ?>
                  <hr>
                  <p><strong>Last updated: </strong><?php echo $pi->last_update;?></p>
              <?php else: ?>
                  <p>No data for this day.</p>
              <?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
	<div class="alert alert-info">
		No data for this month.
	</div>
<?php endif; ?>

<!-- SUMMARY VIEW -->
<?php echo '<h3>Edit history summary for ' . $_SESSION['selected_project_name'] . ' - ' . date_format(date_create($date), "M Y") . '</h3>'; ?>
<?php if (!empty($summary)): ?>

    <table class="table table-striped table-hover" style="margin-top:30px;">
        <tr><th>Day</th><th>Title</th><th>Description</th><th>Attendee limit</th><th>Last updated</th><th>Saved status</th></tr>
        <?php foreach ($summary as $s): ?>
        <tr>
            <td><?php echo date('l', strtotime("Sunday +{$s->running_day} days")); ?></td>
            <td><?php echo $s->title; ?></td>
            <td><?php echo $s->description; ?></td>
            <td><?php echo $s->max_vol; ?></td>
            <td><?php echo $s->last_update; ?></td>
            <td><?php echo ($s->is_draft == '0' ? 'Published' : 'Drafted'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

<?php else: ?>

	<div class="alert alert-info">
		No data for this month.
	</div>

<?php endif; ?>

<script>
<?php
if (empty($date)) {
  echo 'var currentDate = new Date();';
}
else {
  echo "var currentDate = new Date(\"$date\");";
}
?>
var year = currentDate.getFullYear();

var yearSelect = document.getElementById("year");
var monthSelect = document.getElementById("month");

for(var i = year; i > year - 5; i--){
    var yearOption = document.createElement("option");
    yearOption.text = i;
    yearOption.value = i;
    yearSelect.add(yearOption);
}

var months = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
var currentMonth = currentDate.getMonth();

for(var i = 0; i < months.length; i++){
    var monthOption = document.createElement("option");
    monthOption.text = months[i];
    monthOption.value = i + 1;
    monthSelect.add(monthOption);
}
document.getElementById("month").selectedIndex = currentMonth;
</script>
