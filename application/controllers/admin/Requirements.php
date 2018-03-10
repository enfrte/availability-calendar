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
    $this->data['before_body'] .= '<script src="'.site_url('assets/js/acal/confirmation.js').'"></script>';
    $this->data['page_title'] = 'Project requirements';
    $this->data['requirements'] = $this->requirements_model->get_requirements();
    $this->render('requirements/list_view');
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
      $this->render('requirements/create_view'); 
    }
    else
    {
      $this->requirements_model->create_requirements();
      redirect('admin/requirements', 'refresh');
    }

  }


  public function update($requirement_id)
  {
    $this->data['page_title'] = 'Edit project requirements';

    $validate = $this->requirements_model->validate['create_requirements']; 

    $this->form_validation->set_rules($validate); // validation rules are in the model
    // validate form submission or form has loaded for the first time
    if($this->form_validation->run() === FALSE)
    {
      $this->data['requirement'] = $this->requirements_model->get_requirement($requirement_id); // current requirement info
      $this->render('requirements/update_requirement_view'); 
    }
    else
    {
      $this->requirements_model->update($requirement_id);
      $this->session->set_flashdata('message', "Updated");
      redirect('admin/requirements', 'refresh');
    }

  }


  public function delete($requirement_id)
  {
    $this->securityAccess('super_admin');
    $this->requirements_model->delete($requirement_id);
    redirect('admin/requirements', 'refresh');
  }


}