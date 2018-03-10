<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-8 col-lg-offset-2">

    <div class="row">
      <div class="col-lg-12">
        
        <h1>Requirements view</h1>
        <p>Requirements rules allow you to restrict requirements of projects to certain users. After creating a new requirement, activate it in the project settings (Progect page > Edit positions) and configure the users (User list > Edit user) allowed to participate.</p>
        
        <?php echo $showMessages; ?>

        <a href="<?php echo site_url('admin/requirements/create');?>" class="btn btn-block btn-primary btn-lg">Create new requirements</a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12" style="margin-top: 10px;">
      <?php
      if(!empty($requirements))
      {
        echo '<table class="table table-hover table-bordered table-condensed">';
        echo '<tr><th>Title and Description</th><th>Actions</th></tr>';
        foreach($requirements as $visibile)
        {
          echo '<tr>';
          echo '<td><h4>'.$visibile->title.'</h4><p>'.$visibile->description.'</p></td><td>';
          /*
            Members are not allowed to access this page so no need to check for them.
            If the user is in the group admin, they can only modify their own projects.
            If the user is a super admin, they have all access rights on projects.
          */
          // user is super admin
          if($this->ion_auth->in_group('super_admin'))
          {
            echo anchor('admin/requirements/update/'.$visibile->id, 'Edit requirements').'<br>'
                .anchor('admin/requirements/delete/'.$visibile->id, 'Delete requirements', 'data-confirm="deleteRequirement"');
          }
          else
          {
            echo 'Access not granted.'; // required for bootstrap table data
          }
          echo '</td>';
          echo '</tr>';
        }
        echo '</table>';
      } else { echo '<div class="alert alert-info"><h3>No requirements rules found :(</h3></div>'; }
      ?>
      </div>
    </div>

  </div>
</div>
