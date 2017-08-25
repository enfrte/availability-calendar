<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects_model extends CI_Model
{
  public $current_user_id; // current user's id

  public function __construct()
  {
    parent::__construct();
    $this->current_user_id = $this->ion_auth->user()->row()->id; // get their id.
  }

  // all the rules of all the project forms. This keeps the controller light.
  // Reference: https://www.codeigniter.com/userguide3/libraries/form_validation.html?highlight=validator#rule-reference
  public $validate = array(
    'create_project'=>
      array(
        'title' => array('field'=>'project_title','label'=>'Project name','rules'=>'trim|required|max_length[99]|is_unique[projects.title]')
      )
  );

  // get a list of project titles and their owners
  public function get_projects()
  {
    $query = $this->db->get('projects'); // select the table with ActiveRecord get() same as SELECT * FROM test_table
    return $query->result(); // return the rows selected
  }

  // set new a project title and their owner
  public function create_project_title()
  {
    $data = array(
      'title' => $this->input->post('project_title'),
      'owner_id' => $this->current_user_id,
    );
    return $this->db->insert('projects', $data);
  }


  public function update_project_title($project_id = NULL, $owner_id = NULL)
  {
    // prevent modifing something without an ID OR if the user id doesn't match the owner id (hack attempt)
    if( is_numeric($project_id) && is_numeric($owner_id) && ($this->current_user_id == $owner_id || $this->ion_auth->in_group('super_admin')) )
    {
      $data = array(
        'title' => $this->input->post('project_title')
      );
      //https://www.codeigniter.com/userguide3/database/query_builder.html#updating-data
      return $this->db->update('projects', $data, array('id' => $project_id)); // 3rd arg is the where clause
    }
    $this->session->set_flashdata('message', "<p>1 ID check failed: $project_id / $owner_id </p>");
    redirect('calendar/projects', 'refresh');
  }

  // get the name of a project via its id
  public function get_project_name($project_id = NULL)
  {
    if($project_id === NULL || !is_numeric($project_id)){ redirect('calendar/projects', 'refresh'); }
    // pull one record from the db
    $row = $this->db->get_where('projects', array('id' => $project_id))->row();
    return $row->title;
  }

  public function delete_project($project_id = NULL, $owner_id = NULL )
  {
    if( is_numeric($project_id) && is_numeric($owner_id) && ($this->current_user_id == $owner_id || $this->ion_auth->in_group('super_admin')) )
    {
      return $this->db->delete('projects', array('id' => $project_id));
    }
      redirect('calendar/projects', 'refresh');
  }

}
