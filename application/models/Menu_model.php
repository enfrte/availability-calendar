<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  // get published projects as items for the menu (called in MY_Controller)
  public function get_projects()
  {
    /* 
    SELECT DISTINCT	projects.id, projects.title FROM projects
    INNER JOIN positions
    ON (projects.id = positions.project_id)
    WHERE (positions.is_draft = '0')

    Old code before requirement filtering was implemented
    $this->db->distinct();
    $this->db->select('projects.id, projects.title');
    // NOTE! If you are having problems with this query, check into the use of the distinct method
    // OR $this->db->select('DISTINCT(projects.id), projects.title');
    $this->db->from('projects');
    $this->db->join('positions', 'projects.id = positions.project_id', 'inner');
    $this->db->where('positions.is_draft', '0');
    $query = $this->db->get();

    return $query->result(); // return the rows selected
    */

    $sql = "SELECT * FROM projects p
      WHERE NOT EXISTS(
          SELECT 1 FROM projects_requirements pr
          WHERE pr.projects_id = p.id
              AND NOT EXISTS(
                  SELECT 1 FROM users_requirements ur
                  WHERE ur.users_id = ?
                  AND ur.requirements_id = pr.requirements_id
              )
    )";
    $query = $this->db->query($sql, array($_SESSION['user_id'])); // CI query binding example - replaces ? in query
    print_r($query->result()); 
    return $query->result();
  }


  // get the name of a project via its id (used to populate the project menu)
  public function get_project_name($project_id = NULL)
  {
    if($project_id === NULL || !is_numeric($project_id)){ redirect('calendar/projects', 'refresh'); }
    // 
    $row = $this->db->get_where('projects', array('id' => $project_id))->row();
    return $row->title;
  }

/*
  // get the projects
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

  // called in MY_Controller if isset($_SESSION['selected_project_id') is true
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
*/

  // Called when project in project menu is clicked.
  // Update users table with current selected project and day
  public function save_previous_project_view($project_id)
  {

    $data = array('previous_project' => $project_id);
    $this->db->where('id', $_SESSION['user_id']);
    $this->db->update('users', $data);
  }

}
