<?php defined('BASEPATH') OR exit('No direct script access allowed');

// user can enter a new password
class Reset_password extends Public_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->data['page_title'] = 'Reset password';
    $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
  }

  // reset password - final step for forgotten password
  public function reset($code = NULL)
  {
    if (!$code)
    {
      show_404();
    }

    $user = $this->ion_auth->forgotten_password_check($code);

    if ($user)
    {
      // if the code is valid then display the password reset form
      $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
      $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

      if ($this->form_validation->run() == false)
      {
        // set the flash data error message if there is one
        $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

        // display the form
        $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
        $this->data['new_password'] = array(
          'name' => 'new',
          'id'   => 'new',
          'type' => 'password',
          'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
        );
        $this->data['new_password_confirm'] = array(
          'name'    => 'new_confirm',
          'id'      => 'new_confirm',
          'type'    => 'password',
          'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
        );
        $this->data['user_id'] = array(
          'name'  => 'user_id',
          'id'    => 'user_id',
          'type'  => 'hidden',
          'value' => $user->id,
        );
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['code'] = $code;

        $this->render('admin/passwords/reset_password_view');
      }
      else
      {
        // do we have a valid request?
        if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
        {
          // something fishy might be up
          $this->ion_auth->clear_forgotten_password_code($code);
          show_error($this->lang->line('error_csrf'));
        }
        else
        {
          // finally change the password
          $identity = $user->{$this->config->item('identity', 'ion_auth')};
          $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

          if ($change)
          {
            // password was successfully changed
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("login/login", 'refresh');
          }
          else
          {
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect('admin/reset_password/' . $code, 'refresh');
          }
        }
      }
    }
    else
    {
      // if the code is invalid then send them back to the forgot password page
      $this->session->set_flashdata('message', $this->ion_auth->errors());
      redirect("admin/forgot_password", 'refresh');
    }
  }

  // a function from the ion_auth Auth controller class
  function _get_csrf_nonce()
  {
    $this->load->helper('string');
    $key   = random_string('alnum', 8);
    $value = random_string('alnum', 20);
    $this->session->set_flashdata('csrfkey', $key);
    $this->session->set_flashdata('csrfvalue', $value);

    return array($key => $value);
  }

  // a function from the ion_auth Auth controller class
  function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

}
