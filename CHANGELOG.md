# Change log

## Info about changes to the app.

### Log for: 2018-03-15

* General code clean up.
* Converting reset and forgot password forms to handle the showMessages method.
* Minor improvement of the reset and forgot password forms.

### Log for: 2018-03-01

Lot's of updates and perhaps the finalisation of the app. 

* MY_Controller - Added securityAccess method to prevent direct access of certain areas. 
* MY_Controller - Added members class to represent logged in users whom are not admin. 
* MY_Controller - Added custom showMessages method. 
* assets/js/confirmation.js - Added confirmation for user form submission. Covers deletion of users, projects, requirements, cancelled dates.
* Added edit and delete methods for requirements. 
* Projects_model - added a check_project_owner method to properly check that the admin user can modify various projects and positions. As a result, various owner_id controller arguments have been removed. 

### New backlog

There is a new bug where after creating an unpublished project (with no days), it appears in the projects list. 

### Log for: 2018-02-19 

Updated parts of the application to Codeigniter 3.1.7 including:
* Updated index file in the root (/index.php)
* Updated composer.json

Created a requirements feature. This feature acts like user groups, but is designed to filter certain users from viewing certain projects. 

Commented out the following code because it was left over from the initial calendar navigation system where there were projects and project days. If the app runs fine after some testing, delete it completly. 

	MY_Controller => if(isset($_SESSION['selected_project_id'])) {$this->menu_model->set_project_views();}
	Login_model => $this->menu_model->set_project_views();
	Menu_model => public function get_project_views($project_id)
	Menu_model => public function set_project_views()

