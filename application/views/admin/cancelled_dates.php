<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php echo validation_errors(); ?>
<?php echo $this->session->flashdata('message');?>

<?php if (!empty($projects)): ?>

	<div class="panel panel-primary" style="max-width:330px;margin:auto;margin-bottom:15px;">
		<div class="panel-heading"><h3 class="panel-title">Add new cancelled date</h3></div>
		<div class="panel-body">
		<?php echo form_open('', array('class'=>'', 'role'=>'form')); ?>
		  <div class="form-group">
				<label for="date">Add date</label>
		    <input type="date" name="date" class="form-control" placeholder="DD-MM-YYYY" required>
		  </div>
			<div class="form-group">
				<label for="project">Select project</label>
				<select class="acal-select" name="project" id="project" class="form-control" style="width:100%;">
					<?php foreach ($projects as $project): ?>
						<option value="<?php echo $project->id; ?>"><?php echo $project->title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit" class="btn btn-block btn-success">Submit</button>
		<?php echo form_close();?>
		</div>
	</div>

	<?php if (!empty($cancelled_dates)): ?>

		<table class="table table-striped table-hover" style="margin-top:30px;">
		  <tr><th>Date</th><th>Project</th><th>&nbsp;<!-- Action --></th></tr>
			<?php foreach ($cancelled_dates as $cancelled): ?>
			<tr>
				<td><?php echo $cancelled->date; ?></td>
				<td><?php echo $cancelled->project_name; ?></td>
				<td><a href="cancelled_dates/delete_date/<?php echo $cancelled->id; ?>">Delete</a></td>
			</tr>
			<?php endforeach; ?>
		</table>

	<?php else: ?>

		<div class="alert alert-info">
			No cancelled dates have been recorded.
		</div>
	</div>

	<?php endif; ?>
<?php else: ?>

	<div class="alert alert-info">
		To use this feature, at least one project must exist.
	</div>

<?php endif; ?>
