<?php defined('BASEPATH') OR exit('No direct script access allowed');

// admin other user accounts
class Users extends Admin_Controller
{
  function __construct()
  {
    parent::__construct();
    // this can be moved to the admin controller
    if(!$this->ion_auth->in_group('admin') && !$this->ion_auth->in_group('super_admin'))
    {
      $this->session->set_flashdata('message','You are not allowed to visit that page.');
      redirect('login/login','refresh');
    }
  }

  // list the users in a user table (don't forget to include index in the call when filtering by group).
  public function index($group_id = NULL)
  {
    $this->data['page_title'] = 'Users';

    $this->data['users'] = $this->ion_auth->users($group_id)->result(); // get all the users (optional parameter - by group).
    foreach ($this->data['users'] as $k => $user)
    {
      $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result(); // will hold the groups the user belongs to
    }

    $this->render('admin/users/list_users_view');
  }

  // register new users
  public function create()
  {
    $this->data['page_title'] = 'Create user';

    // if the current user is an admin, don't allow them access to the group
    if(!$this->ion_auth->in_group('admin'))
    {
      $this->form_validation->set_rules('groups[]', 'Set user rights', 'required|integer');
    }
    $this->form_validation->set_rules('first_name','First name','trim|required');
    $this->form_validation->set_rules('last_name','Last name','trim|required');
    $this->form_validation->set_rules('phone','Phone','trim');
    $this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[users.email]');

    if($this->form_validation->run()===FALSE)
    {
      $this->load->helper('form');
      $this->render('admin/users/create_user_view');
    }
    else
    {
      $username = $this->input->post('username');
      $email = $this->input->post('email');
      $password = NULL;
      // if the current user is a regular admin, don't allow them access to the group
      if(!$this->ion_auth->in_group('admin'))
      {
        $group_ids = $this->input->post('groups[]');
      }
      else
      {
        $group_ids = ''; // if no group id is passed, members group is used by default
      }

      $additional_data = array(
        'first_name' => $this->input->post('first_name'),
        'last_name' => $this->input->post('last_name'),
        'phone' => $this->input->post('phone')
      );

      // submit the data
      $this->db->trans_start(); // http://www.codeigniter.com/user_guide/database/transactions.html
      $this->ion_auth->register($username, $password, $email, $additional_data, $group_ids);
      // more query data like user settings, admin settings
      $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE)
      {
        $this->session->set_flashdata('message',$this->ion_auth->messages());
      }
      else
      {
        $this->session->set_flashdata('message',$this->ion_auth->messages());
      }

      redirect('admin/users','refresh');
    }
  }

  public function edit($user_id = NULL)
  {
    // get the id of the user passed in the edit() method call
    $user_id = $this->input->post('user_id') ? $this->input->post('user_id') : $user_id;

    // get the group of the user that is being edited.
    $user_group = $this->ion_auth->get_users_groups($user_id)->result();
    $user_group = $user_group[0]->name;
    $this->data['user_group'] = $user_group;

    $this->data['page_title'] = 'Edit user';

    // if the user being edited is a super admin, don't allow the group to be edited.
    if($user_group !== 'super_admin')
    {
      // if the current user is an admin, don't allow them access to the group
      if(!$this->ion_auth->in_group('admin'))
      {
        $this->form_validation->set_rules('groups[]', 'Set user rights', 'required|integer');
      }
    }
    $this->form_validation->set_rules('first_name','First name','trim|required');
    $this->form_validation->set_rules('last_name','Last name','trim|required');
    $this->form_validation->set_rules('phone','Phone','trim');
    $this->form_validation->set_rules('email','Email','trim|valid_email|is_unique[users.email]');
    $this->form_validation->set_rules('user_id','User ID','trim|integer|required');

    if($this->form_validation->run() === FALSE)
    {
      if($user = $this->ion_auth->user((int) $user_id)->row())
      {
        $this->data['user'] = $user;
      }
      else
      {
        $this->session->set_flashdata('message', 'The user doesn\'t exist.');
        redirect('admin/users', 'refresh');
      }

      $this->data['usergroups'] = array(); // will hold the groups the user belongs to
      // insert all groups the user belongs to into the usergroups array
      if($usergroups = $this->ion_auth->get_users_groups($user->id)->result())
      {
        foreach($usergroups as $group)
        {
          $this->data['usergroups'][] = $group->id;
        }
      }

      $this->load->helper('form');
      $this->render('admin/users/edit_user_view');
    }
    else
    {
      $user_id = $this->input->post('user_id');
      // these details are prefilled in the form and are always updated.
      $new_data = array(
        //'email' => $this->input->post('email'),
        'first_name' => $this->input->post('first_name'),
        'last_name' => $this->input->post('last_name'),
        'phone' => $this->input->post('phone')
      );
      // Include email to $new_data update only if a valid email is present
      if($this->input->post('email')){ $new_data['email'] = $this->input->post('email'); }

      $this->ion_auth->update($user_id, $new_data);

      // Update the groups user belongs to (exclude if user is super_admin)
      if($user_group !== 'super_admin')
      {
        // if the current user is an admin, don't allow them access to the group
        if(!$this->ion_auth->in_group('admin'))
        {
          $groups = $this->input->post('groups');
          if (isset($groups) && !empty($groups))
          {
            $this->ion_auth->remove_from_group('', $user_id);
            foreach ($groups as $group)
            {
              $this->ion_auth->add_to_group($group, $user_id);
            }
          }
        }
      }
      $this->session->set_flashdata('message',$this->ion_auth->messages());
      redirect('admin/users','refresh');
    }
  }

  public function delete($user_id = NULL)
  {
    if(is_null($user_id))
    {
      $this->session->set_flashdata('message','There\'s no user to delete');
    }
    else
    {
      $this->ion_auth->delete_user($user_id);
      $this->session->set_flashdata('message',$this->ion_auth->messages());
    }
    redirect('admin/users','refresh');
  }

  // admin resets a user's password
  public function reset_password($user_id = NULL)
  {
    if($user_id == NULL) { return show_error('No user id specified.'); }
    else
    {
      // get the user's details
      $user_id = (int) $user_id; // typecast as int
      $user_row = $this->ion_auth->user($user_id)->row();
      // run the forgotten password method to email an activation code to the user
      $email_set_password_link = $this->ion_auth->forgotten_password($user_row->email);

      if ($email_set_password_link)
      {
        // if there were no errors
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect('admin/users','refresh');
      }
      else
      {
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        redirect('admin/users','refresh');
      }

    }


  }

}
