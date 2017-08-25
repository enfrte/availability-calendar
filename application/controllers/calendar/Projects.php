<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends Admin_Controller
{
public function __construct()
{
  parent::__construct();
  $this->load->model('projects/projects_model');
}

// list all projects
public function index()
{
  $this->data['page_title'] = 'Projects page';
  $this->data['projects'] = $this->projects_model->get_projects();
  $this->render('projects/list_projects_view');
}

// create a new project (title)
public function create()
{
  $validate = $this->projects_model->validate['create_project']; 
  $this->form_validation->set_rules($validate); // validation rules are in the model
  // validate form submission or form has loaded for the first time
  if($this->form_validation->run() === FALSE)
  {
    $this->render('projects/create_project_view'); // return the user
  }
  else
  {
    $this->projects_model->create_project_title();
    redirect('calendar/projects', 'refresh');
  }

}

// edit the title of the project
public function update_title($project_id = NULL, $owner_id = NULL)
{
  // use the same validation rules as create project title
  $validate = $this->projects_model->validate['create_project'];
  $this->form_validation->set_rules($validate);

  if($this->form_validation->run() === FALSE)
  {
    $this->data['project_name'] = $this->projects_model->get_project_name($project_id);
    $this->render('projects/update_project_title_view'); // return the user
  }
  else
  {
    $this->projects_model->update_project_title($project_id, $owner_id);
    //Redirect to positions page
    redirect('calendar/projects', 'refresh');
  }
}

public function delete_project($project_id = NULL, $owner_id = NULL)
{
  $this->projects_model->delete_project($project_id, $owner_id);
  redirect('calendar/projects', 'refresh');
}


}
