<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Create requirements rules for projects 
class Requirements extends Admin_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('projects/requirements_model');
  }

  // list the created visibilities 
  public function index()
  {
    $this->data['page_title'] = 'Project requirements';
    $this->data['requirements'] = $this->requirements_model->get_requirements();
    $this->render('projects/list_requirements_view');
  }

  // create a new requirements 
  public function create()
  {
    $this->data['page_title'] = 'New project requirements';

    $validate = $this->requirements_model->validate['create_requirements']; 
    $this->form_validation->set_rules($validate); // validation rules are in the model
    // validate form submission or form has loaded for the first time
    if($this->form_validation->run() === FALSE)
    {
      $this->render('projects/create_requirements_view'); 
    }
    else
    {
      $this->requirements_model->create_requirements();
      redirect('admin/requirements', 'refresh');
    }

  }


  public function update($requirement_id)
  {
    echo "Coming soon...";exit;
  }


  public function delete($requirement_id)
  {
    echo "Coming soon...";exit;
  }
}