<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar_history extends Admin_Controller
{

public $project_id;
public $date;

public function __construct()
{
  parent::__construct();
  $this->data['page_title'] = 'Calendar History';
  $this->load->model('menu_model');
  $this->load->model('projects/calendar_model');
}


public function index($project_id = NULL, $date = NULL) {

  $this->form_validation->set_rules('year', 'year', 'required|integer');
  $this->form_validation->set_rules('month', 'month', 'required|integer');

  // if form data has been submitted, format it to a date string
  if ($this->form_validation->run() !== FALSE)
  {
    $date = new DateTime($_POST["year"].'/'.$_POST["month"].'/01');
    $date = $date->format('Y/m/d');
  }
  // if no form data has been submitted, just carry on
  $this->check_index_args($project_id, $date); // run first

  $this->data['summary'] = $this->calendar_model->get_summary($this->project_id, $this->date);
  $this->data['history'] = $this->calendar_model->get_most_recent_edits($this->project_id, $this->date);

  $this->data['date'] = $this->date; // for the summary title in view
  $this->render('calendar/calendar_history_view');
}


// process the index controller args (keeps main controller tidy)
public function check_index_args($project_id = NULL, $date = NULL)
{
    if($project_id === NULL) {
      if(isset($_SESSION['selected_project_id'])) {
        $this->data['project_id'] = $_SESSION['selected_project_id'];
        $this->project_id = $this->data['project_id'];
      }
      else {
        $_SESSION['selected_project_id'] = NULL;
        $_SESSION['selected_project_name'] = NULL;
        $this->data['project_id'] = NULL;
      }
    }
    else {
      $this->project_id = $project_id;
    }

    $dt = new DateTime("now");
    if($date === NULL) {
      $this->data['date'] = $dt->format("Y/m/d");
      $this->date = $this->data['date'];
    }
    else {
      $this->date = $date;
      $this->data['date'] = $this->date;
    }
}


}
