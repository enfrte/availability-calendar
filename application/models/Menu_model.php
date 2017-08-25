<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  // Initiate the projects view with project named menu items (called in MY_Controller)
  public function get_projects()
  {
    // NEW REQUIREMENTS
    // RETURN ONLY PROJECTS THAT HAVE DAYS/POSITIONS - YOU CAN DO THIS IN MYSQL
    // RETURN ONLY PROJECTS THAT HAVE AT LEAST ONE DAY SET TO PUBLISHED
    /* THIS WORKS
    SELECT DISTINCT
    	projects.id, projects.title
    FROM
    	projects
    INNER JOIN
    	positions
    ON
    	(projects.id = positions.project_id)
    WHERE (positions.is_draft = '0')
    */

    $this->db->distinct();
    $this->db->select('projects.id, projects.title');
    // NOTE! If you are having problems with this query, check into the use of the distinct method
    // OR $this->db->select('DISTINCT(projects.id), projects.title');
    $this->db->from('projects');
    $this->db->join('positions', 'projects.id = positions.project_id', 'inner');
    $this->db->where('positions.is_draft', '0');
    $query = $this->db->get();

    return $query->result(); // return the rows selected
  }


  // get the name of a project via its id
  public function get_project_name($project_id = NULL)
  {
    if($project_id === NULL || !is_numeric($project_id)){ redirect('calendar/projects', 'refresh'); }
    // pull one record from the db
    $row = $this->db->get_where('projects', array('id' => $project_id))->row();
    return $row->title;
  }


  // get the days a project runs on
  public function get_project_views($project_id)
  {
    $this->db->distinct();
    $this->db->select('running_day');
    $this->db->from('positions');
    $this->db->where(array('project_id' => $project_id));
    $this->db->where('is_draft', '0');
    $this->db->order_by('running_day', 'ASC');
    $query = $this->db->get();

    return $query->result();
  }


  public function set_project_views()
  {
    $project_views = $this->get_project_views($_SESSION['selected_project_id']);
    // after getting the project name, populate the days it runs on
    $project_days = array();
    foreach ($project_views as $key) {
      array_push($project_days, $key->running_day);
    }
    $_SESSION['menu_views'] = array_unique($project_days); // no duplicates - array_unique not needed now since no duplicates are handled by the sql query
    return;
  }


  // update users table with current selected project and day
  public function save_previous_project_view($project_id)
  {
    $data = array('previous_project' => $project_id);
    $this->db->where('id', $_SESSION['user_id']);
    $this->db->update('users', $data);
  }

}
