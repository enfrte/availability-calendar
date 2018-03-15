<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Activate the user
class Activate_account extends Public_Controller
{
// user clicks email link to activate account.
// user is presented with a page to set their password.
// user sets password and account is activated.
function __construct()
{
  parent::__construct();
  $this->data['page_title'] = 'Activate account';
}

// send the user here after clicking the link
function set_password($id=false, $code=false)
{
  // check avtivation codes from in email link
  $id = $this->input->post('id') ? $this->input->post('id') : $id;
  $code = $this->input->post('code') ? $this->input->post('code') : $code;

  if ($id !== false && $code !== false)
  {
    // set the password validation rules 
    $this->form_validation->set_rules('password','Password','trim|min_length[8]|max_length[30]|required');
    $this->form_validation->set_rules('password_confirm','Password confirmation','trim|required|matches[password]');

    if($this->form_validation->run()===FALSE)
    {
      $this->render('admin/passwords/set_password_view');
    }
    else
    {
      $activation = $this->ion_auth->activate($id, $code); // activate the user
      // if activation went ok, set the password
      if ($activation)
  		{
        $new_data = array('password' => $this->input->post('password')); 
        $this->ion_auth->update($id, $new_data);
  			// redirect user to login page
  			$this->session->set_flashdata('ion_auth', $this->ion_auth->messages());
  			redirect("login/login", 'refresh');
  		}
  		else
  		{
  			// activation failed
  			$this->session->set_flashdata('ion_auth', $this->ion_auth->errors());
  			$this->render("admin/passwords/set_password_view");
  		}
    }
  }
  else { 
    die('<h3>No access to this controller without a user id and confirmation code.<h3>'); 
  }

}

}
