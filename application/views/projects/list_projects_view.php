<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-12">
    <h1>Projects view</h1>
    <?php echo $this->session->flashdata('message'); ?>
    <a href="<?php echo site_url('calendar/projects/create');?>" class="btn btn-primary btn-lg">Create new project</a>
  </div>
</div>

<div class="row">
  <div class="col-lg-12" style="margin-top: 10px;">
  <?php
  if(!empty($projects))
  {
    echo '<table class="table table-hover table-bordered table-condensed">';
    echo '<tr><th>Name</th><th>Owner</th><th>Actions</th></tr>';
    foreach($projects as $project)
    {
      echo '<tr>';
      $project_user = $this->ion_auth->user($project->owner_id)->row();
      // in case the project owner is deleted 
      if(isset($project_user)) { 
        $owner = "$project_user->first_name $project_user->last_name ($project_user->email)";
      } 
      else { 
        $owner = "Owner not found.";
      }

      echo '<td>'.$project->title.'</td><td>'.$owner.'</td><td>';
      /*
        Members are not allowed to access this page so no need to check for them.
        If the user is in the group admin, they can only modify their own projects.
        If the user is a super admin, they have all access rights on projects.
      */
      // user is super admin
      if($this->ion_auth->in_group('super_admin'))
      {
        echo anchor('calendar/project_positions/update_position/'.$project->id.'/'.$project->owner_id,'Edit positions').' | '.anchor('calendar/projects/update_title/'.$project->id.'/'.$project->owner_id,'Edit title').' | '.anchor('calendar/projects/delete_project/'.$project->id.'/'.$project->owner_id,'Delete project');
      }
      // user is admin && this list's user is not admin (ie. a member)
      else if($this->ion_auth->in_group('admin') && $current_user->id == $project->owner_id)
      {
        echo anchor('calendar/project_positions/update_position/'.$project->id.'/'.$project->owner_id,'Edit positions').' | '.anchor('calendar/projects/update_title/'.$project->id.'/'.$project->owner_id,'Edit title').' | '.anchor('calendar/projects/delete_project/'.$project->id.'/'.$project->owner_id,'Delete project');
      }
      else
      {
        echo 'Access not granted.'; // required for bootstrap table data
      }
      echo '</td>';
      echo '</tr>';
    }
    echo '</table>';
  } else { echo '<div class="alert alert-info"><h3>No projects found :(</h3></div>'; }
  ?>
  </div>
</div>
