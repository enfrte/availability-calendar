<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<!--<pre><?php // print_r($users);
    if($this->ion_auth->in_group('super_admin')) { echo "Group: SUPER ADMIN"; }
    if($this->ion_auth->in_group('admin')) { echo "Group: ADMIN"; }
?></pre>-->

<div class="row">
  <div class="col-lg-12">
    <h1>Users view</h1>
    <?php echo $showMessages; ?>
    <a href="<?php echo site_url('admin/users/create');?>" class="btn btn-primary btn-lg">Create new user</a>
  </div>
</div>
<div class="row">
  <div class="col-lg-12" style="margin-top: 10px;">
  <?php
  if(!empty($users))
  {
    echo '<table class="table table-hover table-bordered table-condensed">';
    echo '<tr><th>Name</th><th>Email</th><th>Phone</th><th>Group</th><th>Actions</th></tr>';
    foreach($users as $user)
    {
      echo '<tr>';
      echo '<td>'.$user->first_name.' '.$user->last_name.'</td><td>'.$user->email.'</td><td>'.$user->phone.'</td><td>'.str_replace("_", " ", ucfirst($user->groups[0]->name)).'</td><td>';
      /*
        If the user is in the group admin, they can only edit/delete members.
        If the user is a super admin, they have all access rights, but can't delete their own account.
      */
      // user is super admin
      if($this->ion_auth->in_group('super_admin'))
      {
        if($current_user['id'] != $user->id)
        {
          // allow full options
          echo anchor('admin/users/edit/'.$user->id, 'Edit user').'<br>'
              .anchor('admin/users/delete/'.$user->id, 'Delete user', 'data-confirm="deleteUser"').'<br>';
              //.anchor('admin/users/reset_password/'.$user->id,'Reset password');
        }
        else
        {
          // current list's user is super admin - don't allow user to delete their own account (only edit)
          echo anchor('admin/users/edit/'.$user->id, 'Edit user');
        }
      }
      // user is admin 
      else if($this->ion_auth->in_group('admin') && $user->groups[0]->name !== 'admin' && $user->groups[0]->name !== 'super_admin')
      {
        echo anchor('admin/users/edit/'.$user->id, 'Edit user').'<br>'
            .anchor('admin/users/delete/'.$user->id, 'Delete user', 'data-confirm="deleteUser"').'<br>';
            //.anchor('admin/users/reset_password/'.$user->id,'Reset password');
      }
      else
      {
        echo '&nbsp;'; // required for bootstrap table data
      }
      echo '</td>';
      echo '</tr>';
    }
    echo '</table>';
  } else { echo '<h1>No users found.</h1>'; }
  ?>
  </div>
</div>
