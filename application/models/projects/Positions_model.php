<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Used by admin to create and edit project positions
class Positions_model extends CI_Model
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
    'update_position'=>
      array(
        'title' => array('field'=>'title[]','label'=>'Title (required)','rules'=>'trim|required|max_length[160]'),
        'max_vol' => array('field'=>'max_vol[]','label'=>'Maximum participants needed','rules'=>'trim|integer'),
        'description' => array('field'=>'description[]','label'=>'Description','rules'=>'trim|max_length[9000]')
      )
  );

  //
  public function get_positions($project_id = NULL, $day = NULL)
  {
    /*
    SELECT *
    FROM
      positions
    WHERE
        last_update = (SELECT
            MAX(last_update)
          FROM
            positions
          WHERE
            project_id = $project_id AND running_day = $day)

    */

    // An example of writing sub-queries in QueryBuilder
    // source ref: https://arjunphp.com/how-to-write-subqueries-in-codeigniter-active-record/
    // Step 1: write your sub-query
    /*
    $this->db->select('MAX(`last_update`)')
      ->from('positions')
      ->where('project_id', $project_id)
      ->where('running_day', $day);
    $subQuery = $this->db->get_compiled_select();
    // Step 2: write the outer query and inject the sub-query
    $query = $this->db->select('*')
      ->from('positions')
      ->where("`last_update` = ($subQuery)", NULL, FALSE)
      ->get();
    */

    $query = $this->db->get_where('positions', array('project_id' => $project_id, 'running_day' => $day));
    return $query->result(); 
  }


  // insert/update, delete specific positions
  public function update_position()
  {
    $posted = $this->input->post(NULL, FALSE); // convert all $_POST variables to CI POST variables

    if($posted['submit_type'] == 'draft') {
      $is_draft = 1;
    }
    else if ($posted['submit_type'] == 'publish') {
      $is_draft = 0;
    }

    $position_form_data = array(); // positions form data store

    // process the form data into arrays for database operations
    foreach( $posted as $post_key=>$post_value ) {
      // ignore non-array post variables
      if( is_array( $post_value ) ) {
        foreach( $post_value as $form_key=>$form_value ) {
          if (!isset($position_form_data[$form_key])) {
            $position_form_data[$form_key] = array();
          }
          $position_form_data[$form_key][$post_key] = $form_value;
        }
      }
    }

    $last_update = date("Y-m-d H:i:s"); // create a non-unique datetime to for each insert
    // if id exists db->update else db->insert
    foreach($position_form_data as $value){
      //  data for inset and replace db operations
      $value['max_vol'] = empty($value['max_vol']) ? NULL : $value['max_vol']; // empty returns true for '' and '0'

      $data = array(
        'id' => $value['id'],
        'running_day' => $posted['running_day'],
        'title' => $value['title'],
        'description' => $value['description'],
        'max_vol' => $value['max_vol'],
        'is_draft' => $is_draft,
        'last_update' => $last_update,
        'project_id' => $posted['project_id']
      );

      if( empty($value['id']) ) {
        // this is a new entry
        $this->db->insert('positions', $data);
        // new functionality. we need to archive all edits so make a back-up of this entry in old_positions table
        // get the last insert id from the previous row
        $last_id_in_table = $this->db->insert_id();
        // prepare the positions table $data array for the old_positions table
        // add a new column
        $data['old_position_id'] = $last_id_in_table; // Note! old_position_id is appended to the array
        unset($data['id']); // unset (remove) the old key from the array
        // insert backup of the new entry position
        $this->db->insert('old_positions', $data); 
      } else {
        // OLD STATEMENT $this->db->replace('positions', $data); // replace deletes then inserts data with the same id. this triggered on delete cascade which is not desirable.
        // admin has edited an existing entry for this day. save a copy.
        // as there is no mechanism to determine if a specific entry was edited, we update all the entries with ids for the whole day
        /*
        insert into old_positions
        (old_position_id, running_day, title, description, max_vol, is_draft, last_update, project_id)
        (select
          id, running_day, title, description, max_vol, is_draft, last_update, project_id
          from positions
          where positions.id = 1
        );
        */
        // now update the positions
        $this->db->where('id', $value['id']);
        $this->db->update('positions', $data);
        // new functionality. we need to archive all edits so make a back-up of this entry in old_positions table
        // create old_position_id. set it to id
        $data['old_position_id'] = $data['id']; // Note! old_position_id is appended to the array
      	unset($data['id']); // unset (remove) the old key from the array
        $this->db->insert('old_positions', $data); // insert the backup data for archiving
      }

      // check if any positions were removed && if the current array iteration is not empty
      if ( isset($posted['removed']) && !empty($value['removed']) ) {
        // to-do: check behaviour of this. maybe we don't need to do anything. ie. remove the following line
        $this->db->delete('positions', array('id' => $value['removed']));
      }
    }

    // general update message.
    $this->session->set_flashdata('updated', '<div class="alert alert-success"><strong>Success! </strong>Positions have been updated.</div>');
  }


  // delete an entire project day
  public function delete_position()
  {
    $posted = $this->input->post(NULL, FALSE);
    $this->db->where('running_day', $posted['running_day']); 
    $this->db->where('project_id', $posted['project_id']);
    $this->db->delete('positions');

    $this->session->set_flashdata('updated', '<div class="alert alert-success"><strong>Success! </strong>Day has been deleted</div>');
  }

}
