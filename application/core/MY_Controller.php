<?php defined('BASEPATH') OR exit('No direct script access allowed');

// a base controller for admin and public controllers.
class MY_Controller extends CI_Controller
{
  protected $data = array();

  function __construct()
  {
    parent::__construct();
    //echo '<pre>Debug: ' ; print_r($_SESSION); echo '</pre>';
    $this->data['before_head'] = ''; // inject page specific js or styles
    $this->data['before_body'] = '';
    $this->data['current_user_menu'] = ''; // define a user specific menu
    $this->data['page_title'] = 'Set a page title!'; // appears if you forgot ;)
    if ($this->ion_auth->logged_in()) {
      $this->data['current_user']['id'] = $this->ion_auth->user()->row()->id; 
    } else { 
      $this->data['current_user']['id'] = NULL;
    }
    //$this->data['calendar_days'] = ''; // n-day appearing in a clanendar month where n = Mon, Tues...
    $this->data['showMessages'] = $this->showMessages();
  }

  // Handle the security access of the site using ion_auth library.
  // Default security, set at the class level, can be overwritten by calling the method again in a controller
  protected function securityAccess($access_level = NULL) 
  {
    //if($this->router->class === 'login' || $this->router->class === 'forgot_password' || $this->router->class === 'reset_password' ) { return; } // not needed for the publically accessable Login controller

    if($access_level === NULL) { show_error('securityAccess requires an argument.', '', 'An Error Was Encountered'); }

    // this should be called for every controller apart from the Account controller (this is currently done in the MY_Controller construct)
    if($access_level === 'login') {
      // user accesses the login page, but is already logged in
      if(($this->router->method === 'login') && ($this->ion_auth->logged_in() === TRUE)) {
          $this->ion_auth->logout(); // log them out
          redirect('login/login', 'refresh');
      }
      // Require user to be logged in before accessing any page
      if( $this->ion_auth->logged_in() === FALSE ) {
        $this->session->set_flashdata('error', 'You must be logged in to access that page.');
        redirect('login/login', 'refresh');
      }
    }

    // the access level requires admin, but the user is not in the admin or super_admin group
    if ( $access_level === 'admin' && ($this->ion_auth->in_group('admin') === FALSE && $this->ion_auth->in_group('super_admin') === FALSE) ) {
      $this->session->set_flashdata('error', 'You must be logged-in as admin to do that.');
      $this->ion_auth->logout();
      redirect('login/login', 'refresh');
    }

    // the access level requires admin, but the user is not in the admin group
    if ( $access_level === 'super_admin' && $this->ion_auth->in_group('super_admin') === FALSE ) {
      $this->session->set_flashdata('error', 'You must be logged-in as super admin to do that.');
      $this->ion_auth->logout();
      redirect('login/login', 'refresh');
    }
    
  }

  /* 
    A function to show stylised flash data messages. 
    Put $this->data['showMessages'] = $this->showMessages(); in the controller __construct.
    Call errors in the view like echo $showMessages;
    Create errors like this $this->session->set_flashdata('info', 'Example message.');
    Remember to call the database query as the model's return parameter, and then test 
    model's method for true in the controller when setting the flashdata message. 
  */
  protected function showMessages()
  {
    $messages = '';
    if(!empty( $this->session->flashdata() )) { 
      foreach ($this->session->flashdata() as $key => $value) {
        // output the style (color) of the error based on whether there is a keyword in the flashdata key-name
        if (strpos($key, 'error') !== false) { 
          $messages .= '<div class="alert alert-danger" role="alert">'.$this->session->flashdata($key).'</div>';
        } 
        else if (strpos($key, 'success') !== false) { 
          $messages .= '<div class="alert alert-success" role="alert">'.$this->session->flashdata($key).'</div>';
        } 
        else if (strpos($key, 'info') !== false) { 
          $messages .= '<div class="alert alert-info" role="alert">'.$this->session->flashdata($key).'</div>';
        } 
        else if (strpos($key, 'warning') !== false) { 
          $messages .= '<div class="alert alert-warning" role="alert">'.$this->session->flashdata($key).'</div>';
        } 
        else if ( strpos($key, 'csrfkey' ) !== false || strpos($key, 'csrfvalue') !== false ) { 
          // prevent outputting csrf data used by ion auth and set_flashdata() 
          return;
        } 
        else { 
          // ion auth stylises its own messages, when calling for example $this->ion_auth->errors(). See app/config/ion_auth.php
          $messages .= $this->session->flashdata($key);
        } 
      }
    }
    return $messages;
  }


  // set page templates and pass construct data when loading the view
  protected function render($the_view = NULL, $template = 'master')
  {
    if(is_null($the_view)) {
      $this->data['the_view_content'] = '';
    }
    else {
      $this->data['the_view_content'] = $this->load->view($the_view, $this->data, TRUE);
    }

    $this->load->view('templates/'.$template.'_view', $this->data);
  }

}

// the area accessable by logged in users
class Member_Controller extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    // allow further site access only to those logged in, or refuse further access if a session has expired
    $this->securityAccess('login');
    //if(!$this->ion_auth->logged_in()) { redirect('login/login', 'refresh'); }

    // user_name and group_name is used in the navbar to display the current user and group menu
    if(isset($this->ion_auth->user()->row()->first_name)) $this->data['user_name'] = $this->ion_auth->user()->row()->first_name;
    if(isset($this->ion_auth->get_users_groups()->row()->name)) $this->data['group_name'] = $this->ion_auth->get_users_groups()->row()->name;

    // add the admin menu if they belong to admin or super_admin groups
    if($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('super_admin'))
    {
      // load these menu items if user is an admin
      $this->data['current_user_menu'] = $this->load->view('templates/_parts/user_menu_admin_view.php', NULL, TRUE);
    }

    // Initiate the projects view with project named menu items
    $this->load->model('menu_model');
    $this->data['menu_projects'] = $this->menu_model->get_projects();
    //if(isset($_SESSION['selected_project_id'])) {$this->menu_model->set_project_views();}
  }
  

  protected function render($the_view = NULL, $template = 'admin_master')
  {
    parent::render($the_view, $template);
  }

}

// for admin and super_admin users (currently no need for a SuperAdmin_Controller)
class Admin_Controller extends Member_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->securityAccess('admin'); // includes super_admin
  }
}

// the area accessable by the public
class Public_Controller extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
    // if user is already logged in and tries to access a public class,
    // log them out before continuing
    if($this->ion_auth->logged_in()) { $this->ion_auth->logout(); }
  }

}
