<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Requirements_model extends MY_Model
{
  //public $current_user_id; // current user's id

  public function __construct()
  {
    parent::__construct();
    //$this->current_user_id = $this->ion_auth->user()->row()->id; // get their id.
  }

  // all the rules of all the project forms. This keeps the controller light.
  // Reference: https://www.codeigniter.com/userguide3/libraries/form_validation.html?highlight=validator#rule-reference
  public $validate = array(
    'create_requirements'=>
      array(
        'title' => array('field'=>'requirements_title','label'=>'Requirements name','rules'=>'trim|required|max_length[99]|is_unique[requirements.title]')
      )
  );

  // get a list of requirements titles
  public function get_requirements()
  {
    $query = $this->db->get('requirements'); 
    return $query->result(); 
  }

  // add new requirements to db
  public function create_requirements()
  {
    $data = array(
      'title' => $this->input->post('requirements_title'),
      'description' => $this->input->post('requirements_description'),
    );
    return $this->db->insert('requirements', $data);
  }
  
  // get the current state of the user's requirements
  public function get_user_requirements($user_id)
  {
    /*
    select 
      requirements.id as requirements_id,
      requirements.title as requirements_title,
        users_requirements.users_id as checked
    from requirements
    left join users_requirements 
    on users_requirements.requirements_id = requirements.id
    and users_requirements.users_id = 2
    */
    $this->db->select('requirements.id, requirements.title, users_requirements.users_id as checked');
    $this->db->from('requirements');
    $this->db->join('users_requirements', "users_requirements.requirements_id = requirements.id AND users_requirements.users_id = $user_id", 'left');
    $query = $this->db->get();
    //var_dump($query->result());exit;
    return $query->result();
  }

  // update/create a user's requirements
  public function update_user_requirements($requirements, $user_id)
  {
    // purge the old records of the user 
    $this->db->where('users_id', $user_id);
    $this->db->delete('users_requirements');
    
    // if any requirements checkboxes (form data) are checked, add them to the db
    if (!empty($requirements)) {
      $data = [];
      
      foreach ($requirements as $visible) {
        $data[] = ['users_id' => $user_id, 'requirements_id' => $visible];
      }

      $this->db->insert_batch('users_requirements', $data);
    }
    return;
  }

  // get the requirements of an individual project
  public function get_project_requirements($project_id)
  {
    $this->db->select('requirements.id, requirements.title, projects_requirements.projects_id as checked');
    $this->db->from('requirements');
    $this->db->join('projects_requirements', "projects_requirements.requirements_id = requirements.id AND projects_requirements.projects_id = $project_id", 'left');
    $query = $this->db->get();
    //var_dump($query->result());exit;
    return $query->result();
  }

  // update the requirements of an individual project
  public function update_project_requirements($requirements, $project_id)
  {
    // Note: this is not the requirement itself. See update() for that.
    // Purge the old records of the project 
    $this->db->where('projects_id', $project_id);
    $this->db->delete('projects_requirements');
    
    // If any requirements checkboxes (form data) are checked, add them to the db
    if (!empty($requirements)) {
      $data = [];
      
      foreach ($requirements as $visible) {
        $data[] = ['projects_id' => $project_id, 'requirements_id' => $visible];
      }

      return $this->db->insert_batch('projects_requirements', $data);
    }
    return; 
  }

  // get the details of a single requirement
  public function get_requirement($requirement_id)
  {
    $query = $this->db->get_where('requirements', array('id' => $requirement_id));
    //var_dump($query->result()->row());exit;
    return $query->row();
  }

  // update the requirement itself
  public function update($requirement_id)
  {
    $data = array(
      'title' => $this->input->post('requirements_title'),
      'description' => $this->input->post('requirements_description')
    );
    return $this->db->update('requirements', $data, array('id' => $requirement_id));     
  }

  // delete the requirement itself
  public function delete($requirement_id)
  {
    $this->securityModelAccess('super_admin'); 
    $this->db->delete('requirements', array('id' => $requirement_id));
    return;
  }

}