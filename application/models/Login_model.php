<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('menu_model');
  }

  // get the user's previous calendar project and day view
  public function get_previous_view()
  {
    $previous_project_id = $this->ion_auth->user()->row()->previous_project;

    if(!is_null($previous_project_id))
    {
      // Checks to see if the user still meets the requirements to see the requested project.
      $required_projects = $this->menu_model->get_projects();
      $projects = [];
      foreach ($required_projects as $project) {
        $projects[] = $project->id; // copy just the project ids to an array 
      }
      // check if the previous user stored project id against current user requirement project ids
      if( !in_array($previous_project_id, $projects) ){
        return false; // user does not have requirement access
      } 

      // bug fix: if admin has changed status of project from published to draft, the draft project still gets called because the project id was still getting called directly from the user's saved project entry in the database
      // check if the project is still published
      $this->db->select('is_draft'); 
      $this->db->from('positions');
      $this->db->where('project_id', $previous_project_id );
      $this->db->where('is_draft', '0' );
      $query = $this->db->get();

      if( count( $query->result() ) < 1 ) {
        // the user's previously saved project view is no longer public
        return FALSE;
      }

      $_SESSION['previous_project'] = $previous_project_id;
      return TRUE;
    }
    else { return FALSE; }
  }


  // set the user's previous calendar project and day view
  public function set_previous_view()
  {
    // check if data exists in users table
    if($this->get_previous_view())
    {
      // check if project_id and ~~day still~~ exist in the positions table (it might have been deleted since last visit)
      // SELECT * FROM positions WHERE project_id = ? AND day = ?
      $this->db->get_where('positions', array('project_id' => $_SESSION['previous_project']));
      $query_length = $this->db->count_all_results(); // returns int

      if($query_length > 0){
        // the project still exists
        $_SESSION['selected_project'] = $_SESSION['previous_project'];
        $_SESSION['selected_project_id'] = $_SESSION['previous_project'];
        $_SESSION['selected_project_name'] = $this->menu_model->get_project_name($_SESSION['previous_project']);
        //$this->menu_model->set_project_views();
        unset($_SESSION['previous_project']);
        redirect('calendar/calendar/home'.'/'.$_SESSION['selected_project_id'].'/', 'refresh');
      }

      unset($_SESSION['previous_project'], $_SESSION['previous_project_day']);
    }

    return;
  }


}
