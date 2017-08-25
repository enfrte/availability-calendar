<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Select the project from the project menu (this is not the User menu)
class Menu extends Admin_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('menu_model');
  }

  // set a project from the project menu
  public function set_project($id)
  {
    $_SESSION['selected_project_name'] = $this->menu_model->get_project_name($id);
    $_SESSION['selected_project_id'] = $id;

    $this->menu_model->save_previous_project_view($_SESSION['selected_project_id']);

    redirect('calendar/calendar/home/'.$id);
  }

  // set a day the project runs on (called from the select day view menu)
  // I think this is redundant now, as days are not selectable
  public function set_menu_view($day = NULL)
  {
    $_SESSION['selected_project_day'] = $day;
    $this->menu_model->save_previous_project_view($_SESSION['selected_project_id'], $_SESSION['selected_project_day']);
    redirect('calendar/calendar/home/'.$_SESSION['selected_project_id'].'/'.$_SESSION['selected_project_day']);
  }

}
