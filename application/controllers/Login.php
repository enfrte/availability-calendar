<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Public_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('login_model');
    $this->data['page_title'] = 'Login';
  }

  public function login()
  {
    // in case the root domain is entered and the user is already logged in, this will prevent the login screen appearing in the view
    if($this->ion_auth->logged_in()) { redirect('calendar/calendar/home', 'refresh'); }

    // user attemps to log in
    if($this->input->post())
    {
      $this->form_validation->set_rules('identity', 'Identity', 'required|valid_email');
      $this->form_validation->set_rules('password', 'Password', 'required');
      $this->form_validation->set_rules('remember','Remember me','integer');

      if($this->form_validation->run()===TRUE)
      {
        $remember = (bool) $this->input->post('remember'); // remember me checkbox

        if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
        {
          $this->login_model->set_previous_view();
          redirect('calendar/calendar/home', 'refresh');
        }
        else
        {
          $this->session->set_flashdata('ion_auth', $this->ion_auth->errors());
          redirect('login/login', 'refresh');
        }
      }
    }
    // display default login page
    $this->render('admin/login_view');
  }


}
