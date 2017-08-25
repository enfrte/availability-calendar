<?php defined('BASEPATH') OR exit('No direct script access allowed');

// forgotten password - user supplies email for reset
class Forgot_password extends Public_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->data['page_title'] = 'Forgot password';
    $this->load->database();
    $this->load->library(array('ion_auth','form_validation'));
    $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
  }

  function index()
  {
    $this->data['identity_label'] = 'email';
    $this->form_validation->set_rules('identity', 'Email', 'trim|required|valid_email');

    if ($this->form_validation->run() == false)
    {
      // set any errors and display the form
      $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
      $this->render('admin/passwords/forgot_password_view');
    }
    else
    {
      $identity = $this->ion_auth->where('email', $this->input->post('identity'))->users()->row(); // change if ion_auth identity !== email

      if(empty($identity)) {
        // No record of that email address exception
        $this->ion_auth->set_error('forgot_password_email_not_found');
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        redirect("admin/forgot_password", 'refresh');
      }

      // run the forgotten password method to email an activation code to the user
      $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

      if ($forgotten)
      {
        // if there were no errors
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect("admin/forgot_password", 'refresh'); 
      }
      else
      {
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        redirect("admin/forgot_password", 'refresh');
      }
    }
  }

}
