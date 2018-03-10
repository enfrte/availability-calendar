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
  $this->data['before_body'] .= '<script src="'.site_url('assets/js/acal/confirmation.js').'"></script>';
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
    if ($this->projects_model->create_project_title()) { $this->session->set_flashdata('success', "Project created"); }
    redirect('calendar/projects', 'refresh');
  }

}

// edit the title of the project
public function update_title($project_id = NULL)
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
    if ($this->projects_model->update_project_title($project_id)) { $this->session->set_flashdata('success', "Project updated"); }
    //Redirect to positions page
    redirect('calendar/projects', 'refresh');
  }
}

public function delete_project($project_id)
{
  if ($this->projects_model->delete_project($project_id)) { $this->session->set_flashdata('success', "Project deleted"); }
  redirect('calendar/projects', 'refresh');
}


// ouput an editable list of current project requirements and current saved status
public function update_requirements($project_id)
{
  $this->load->model('projects/requirements_model');

  // Note: no validation is being checked for this form, so no $this->form_validation->run() === FALSE
  if(empty($_POST))
  {
    //var_dump($this->input->post());
    $this->data['requirements'] = $this->requirements_model->get_project_requirements($project_id);
    $this->data['project_name'] = $this->projects_model->get_project_name($project_id);
    $this->render('projects/update_project_requirements_view'); 
  }
  else
  {
    //var_dump($this->input->post());exit;
    if ($this->requirements_model->update_project_requirements($this->input->post('requirements'), $project_id)) { $this->session->set_flashdata('success', "Project requirements updated"); }
    redirect('calendar/projects', 'refresh');
  }

}



}
