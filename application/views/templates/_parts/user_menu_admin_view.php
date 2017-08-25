<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<li class="dropdown-header">Admin menu</li>

  <li><a href="<?php echo site_url('admin/users/create');?>">Create user</a></li>
  <li><a href="<?php echo site_url('admin/users'); ?>">User list</a></li>
  <li><a href="<?php echo site_url('calendar/calendar_history');?>">History view</a></li>
	<li><a href="<?php echo site_url('calendar/projects');?>">Project page</a></li>
	<li><a href="<?php echo site_url('admin/cancelled_dates'); ?>">Cancelled dates</a></li>

<li class="divider"></li>
