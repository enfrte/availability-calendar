<?php defined('BASEPATH') OR exit('No direct script access allowed');

// a base controller for admin and public controllers.
class MY_Controller extends CI_Controller
{
  protected $data = array();

  function __construct()
  {
    parent::__construct();
    //echo '<pre>Debug: ' ; print_r($_SESSION); echo '</pre>';
    //$this->lang->load('auth');
    //$this->load->database();
    //$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>'); // from form validation library

    $this->data['before_head'] = ''; // inject page specific js or styles
    $this->data['before_body'] = '';
    $this->data['current_user_menu'] = ''; // define a user specific menu
    $this->data['page_title'] = 'Set a page title!'; // appears if you forgot ;)
    $this->data['calendar_days'] = ''; // n-day appearing in a clanendar month where n = Mon, Tues...
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
class Admin_Controller extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
    // allow further site access only to those logged in, or refuse further
    // access if a session has expired
    if(!$this->ion_auth->logged_in()) { redirect('login/login', 'refresh'); }

    // user_name and group_name is used in the navbar to display the current user and group menu
    if(isset($this->ion_auth->user()->row()->first_name)) $this->data['user_name'] = $this->ion_auth->user()->row()->first_name;
    if(isset($this->ion_auth->get_users_groups()->row()->name)) $this->data['group_name'] = $this->ion_auth->get_users_groups()->row()->name;

    // user is logged in. Get their details for use with UI filtering
    $this->data['current_user']['id'] = $this->ion_auth->user()->row()->id;

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
