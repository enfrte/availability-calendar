<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Project_positions extends Admin_Controller
{

public function __construct()
{
  parent::__construct();
  $this->load->model('projects/positions_model');
  $this->load->model('projects/projects_model');
  $this->data['page_title'] = "Project positions";
  $this->data['before_body'] = '<script src="'.site_url('assets/js/acal/project_positions.js').'"></script>';  
}

// populates the admin's project position view.
// also works with the select day form element to pull the positions for a selected day
public function update_position($project_id, $day = NULL)
{
  if ( $this->projects_model->check_project_owner($project_id) !== true ) {
    $this->session->set_flashdata('danger', 'Only the project owner or super admin can do that.');
    redirect('calendar/projects', 'refresh');
  }

  if($day == NULL){ $day = 1; } // set day to 1 == Monday (default view)
  $this->data['project_id'] = $project_id;
  $this->data['day'] = $day;
  $this->data['project_positions'] = $this->positions_model->get_positions($project_id, $day); // all positions by project_id
  $this->data['project_name'] = $this->projects_model->get_project_name($project_id);

  // form validation
  $validate = $this->positions_model->validate['update_position'];
  $this->form_validation->set_rules($validate);

  if($this->form_validation->run() === FALSE) {
    $this->render('projects/project_positions_view');
  }
  else {
    // check which action the user requested (see UI buttons -> Draft, Publish, Delete)
    //$_POST['submit_type'] holds the string draft, submit, or delete;
    if($this->input->post('submit_type') == 'draft' || $this->input->post('submit_type') == 'publish') {
      $this->positions_model->update_position();
    }
    else if ($this->input->post('submit_type') == 'delete') {
      $this->positions_model->delete_position();
    }
    else {
      $this->session->set_flashdata('danger', '<strong>Error! </strong>No action detected.<br>Please use one of the form actions to submit the form data (Save draft, Publish, or Delete)');
    }

    redirect('calendar/project_positions/update_position/'."$project_id/$day");
  }
}

}
