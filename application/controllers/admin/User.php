<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// control some standard user interactions
class User extends Member_Controller
{
  function __construct()
  {
    parent::__construct();
  }

  public function logout()
  {
    $this->ion_auth->logout();
    redirect('login/login', 'refresh');
  }

  // User's own profile page. User can only edit their password here.
  public function profile()
  {
    $this->data['page_title'] = 'User Profile';
    $user = $this->ion_auth->user()->row();
    $this->data['user'] = $user;
    $this->render('admin/user/profile_view','admin_master');
  }

  // allows the user to change their own password 
  public function change_password()
  {
    $this->data['page_title'] = 'Change password';
    $user = $this->ion_auth->user()->row();
    $this->data['user'] = $user; // user data (the id) is still needed to perform update

    $this->form_validation->set_rules('password','Password','trim|min_length[8]|max_length[30]|required');
    $this->form_validation->set_rules('password_confirm','Confirm password','trim|matches[password]|required');

    // process the change password request
    if($this->form_validation->run()===FALSE)
    {
      $this->render('admin/user/change_password_view','admin_master'); // reload the page
    }
    else
    {
      $new_data = array( 'password' => $this->input->post('password') );
      $this->ion_auth->update($user->id, $new_data);
      $this->session->set_flashdata('message', $this->ion_auth->messages());
      redirect('admin/user/change_password','refresh');
    }
  }

  // user can change their settings, like subscribing to email reminders...
  // !currently not in use!
  public function user_settings()
  {
    $this->data['page_title'] = 'User settings';
    $user = $this->ion_auth->user()->row();
    $this->data['user'] = $user; // user data (the id) is still needed to perform update

    $this->render('admin/user/user_settings_view','admin_master'); // reload the page
  }

}
