Add changes for the git push here...

Created: 19-02-2018 

Updated parts of the application to Codeigniter 3.1.7 including:
* Updated index file in the root (/index.php)
* Updated composer.json

Created a requirements feature. This feature acts like user groups, but is designed to filter certain users from viewing certain projects. 

Commented out the following code because it was left over from the initial calendar navigation system where there were projects and project days. If the app runs fine after some testing, delete it completly. 

	MY_Controller => if(isset($_SESSION['selected_project_id'])) {$this->menu_model->set_project_views();}
	Login_model => $this->menu_model->set_project_views();
	Menu_model => public function get_project_views($project_id)
	Menu_model => public function set_project_views()

