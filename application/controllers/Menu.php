<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Select the project from the project menu (this is not the User menu)
class Menu extends Admin_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('menu_model');
  }

  // When the user clicks a project in the project menu, set it as the last selected project in the users db table
  // This will be set like a database cookie and the project will be loaded next time the user logs in.
  // It also serves to set/redirect the selected project to the calendar view. 
  public function set_project($id)
  {
    $_SESSION['selected_project_name'] = $this->menu_model->get_project_name($id); // currently selected project in the menu bar
    $_SESSION['selected_project_id'] = $id;

    $this->menu_model->save_previous_project_view($_SESSION['selected_project_id']);

    redirect('calendar/calendar/home/'.$id);
  }

  // set a day the project runs on (called from the select day view menu)
  // I think this is redundant now, as days are not selectable
  /*
  public function set_menu_view($day = NULL)
  {
    $_SESSION['selected_project_day'] = $day;
    $this->menu_model->save_previous_project_view($_SESSION['selected_project_id'], $_SESSION['selected_project_day']);
    redirect('calendar/calendar/home/'.$_SESSION['selected_project_id'].'/'.$_SESSION['selected_project_day']);
  }
  */
}
