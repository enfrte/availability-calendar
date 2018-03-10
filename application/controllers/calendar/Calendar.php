<?php defined('BASEPATH') OR exit('No direct script access allowed');

// shows calendar and user availability form 
class Calendar extends Member_Controller
{
public function __construct()
{
  parent::__construct();
  $this->data['page_title'] = 'Calendar';
  $this->load->model('menu_model');
  $this->load->model('projects/calendar_model');
  $this->data['before_body'] = '<script src="'.site_url('/assets/js/acal/calendar_home.js').'"></script>';
}


public function home($project = NULL, $date = NULL)
{
  // process the controller args for the view and get_positions()
  if($project === NULL) {
    if(isset($_SESSION['selected_project_id'])) {
      $this->data['project'] = $_SESSION['selected_project_id'];
      $project = $this->data['project']; // user will receive prompt to select a project (tested later on)
    }
    else {
      $_SESSION['selected_project_id'] = NULL;
      $_SESSION['selected_project_name'] = NULL; 
      $this->data['project'] = NULL;
    }
  }
  else {
    $this->data['project'] = $project;
  }
  // and the date arg
  $dt = new DateTime("now");
  if($date === NULL) {
    $this->data['date'] = $dt->format("Y-m-d");
    $date = $this->data['date'];
  }
  else {
    $this->data['date'] = $date;
    $parameter_date = new DateTime($date);
  }

  // if a project is selected, get its positions
  if(isset($project)) {

    // address the bug where any logged in user would hit an error page if an admin suddenly pulled the plug on a project
    if(!$this->calendar_model->is_current_project_public($project)) {
      redirect("calendar/calendar/home/");
    }

    $datepicker_month_range = $this->config->item('datepicker_month_range'); // how many months in the future to allow users to interact with
    $get_project_days = $this->calendar_model->get_project_days($project); // days of the week the project runs on

    // check to see if calendar will need to skip to the next month because there are no upcoming project days in current month
    if( !isset($parameter_date) || $dt->format('m') == $parameter_date->format('m') ) {
      // we are at the current month. run the check
      $this->data['skip_current_month'] = $this->calendar_model->select_next_month($get_project_days);
    }
    else {
      // a non current month has been selected, don't run the check, but set the variable because it is assigned in the view to JS
      $this->data['skip_current_month'] = '0';
    }

		// get all dates the project does not run on, including cancelled dates (set by admin - like public holidays)
		$this->data['non_project_dates'] = $this->calendar_model->get_non_project_dates($get_project_days, $datepicker_month_range);

    $this->data['calendar_positions'] = $this->calendar_model->get_positions($_SESSION['selected_project_id'], $date);
    $this->data['get_positions_of_other_users'] = $this->calendar_model->get_positions_of_other_users($_SESSION['selected_project_id'], $date);
  }

  // form validation and redirection
  $validate = $this->calendar_model->validate['update_position'];
  $this->form_validation->set_rules($validate);

  // handle form submission
  if($this->form_validation->run() === FALSE) {
    $this->render('calendar/calendar_home_view');
  } else {
    $this->calendar_model->update_users_positions($this->input->post('date'));
    redirect("calendar/calendar/home/$project/$date");
  }

}

}
