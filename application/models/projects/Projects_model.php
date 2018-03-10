<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects_model extends CI_Model
{
  public $current_user_id; 

  public function __construct()
  {
    parent::__construct();
    $this->current_user_id = $this->ion_auth->user()->row()->id; 
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
    $query = $this->db->get('projects'); 
    return $query->result(); 
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


  public function update_project_title($project_id)
  {
    // prevent modifing something without an ID OR if the user id doesn't match the owner id (hack attempt)
    if( $this->check_project_owner($project_id) )
    {
      $data = array(
        'title' => $this->input->post('project_title')
      );
      //https://www.codeigniter.com/userguide3/database/query_builder.html#updating-data
      return $this->db->update('projects', $data, array('id' => $project_id)); // 3rd arg is the where clause
    }
    $this->session->set_flashdata('danger', "Owner check failed");
    redirect('calendar/projects', 'refresh');
  }

  // get the name of a project via its id
  public function get_project_name($project_id)
  {
    $row = $this->db->get_where('projects', array('id' => $project_id))->row();
    return $row->title; // pull one record from the db
  }

  public function delete_project($project_id)
  {
    if( $this->check_project_owner($project_id) ) {
      return $this->db->delete('projects', array('id' => $project_id));
    }
  }

  // check if the current user owns the project they are trying to modify
  public function check_project_owner($project_id)
  {
    if ($this->ion_auth->in_group('super_admin')) {
      return true; // super_admin has required privileges
    }

    $query = $this->db->get_where('projects', array('id' => $project_id));
    $project_owner_id = $query->row()->owner_id;

    // check current user match
    if($this->current_user_id !== $project_owner_id) { 
      //$this->session->set_flashdata('danger', 'The system has detected you are not the project owner');
      $this->ion_auth->logout();      
      redirect('login/login', 'refresh'); // serious offence: kick out user
    }    
    return true; // everything looks ok. 
  }
  

}
