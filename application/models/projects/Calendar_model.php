<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// calendar related methods
class Calendar_model extends CI_Model
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
        'attendee_checkbox' => array('field'=>'attendee_checkbox[]','label'=>'','rules'=>'trim|integer')
      )
  );


  // Display a list of *published* *project* *positions* for a position *day* and *date*.
  // If no date is specified, the current date is used.
  public function get_positions($project_id, $date)
  {
    $week_day = date('w', strtotime($date));
    /*
      NEW QUERY uses a left join to return all the rows from the positions table,
      even if they don't match the table you're joining with. If there's no
      match, the values in the users_positions table will be NULL, and you can
      test for that when showing whether the position has a user reservation.
    */

    // Watch out for last_update as we shouldn't need it anymore, but I haven't removed it because IIABDFI
    // Step 1: write inner most subquery
    $this->db->select('MAX(`last_update`)')
        ->from('positions')
        ->where('project_id', $project_id)
        ->where('running_day', $week_day);
    $subQuery = $this->db->get_compiled_select();

    // Step 2: write next subquery and inject the inner most subquery
    $this->db->select('*')
        ->from('positions')
        ->where("`last_update` = ($subQuery)", NULL, FALSE);
    $subQuery = $this->db->get_compiled_select();

    // Step 3: write the outer query and inject the previous query, which should also contain its own subquery
    $this->db->select('up.id AS up_id, p.id AS pos_id, title, description, max_vol, IF(up.id IS NULL, "FALSE", "TRUE") volunteered')
    ->from("($subQuery) AS p")
    ->join('users_positions AS up', "p.id = up.position_id AND up.user_id = \"$this->current_user_id\" AND up.calendar_date = \"$date\"", 'left')
    ->where('p.running_day', $week_day)
    ->where('p.project_id', $project_id)
    ->where('p.is_draft', '0');

    $query = $this->db->get();
    return $query->result(); 
  }

public function get_positions_of_other_users($project_id = NULL, $date)
{
  /*
  -- this only returns users coloums for volunteered positions
  SELECT
  	users_positions.id AS users_positions_id,
      positions.id AS positions_id,
      first_name,
      last_name,
      user_id,
      IF(users_positions.id IS NULL, '0', '1') volunteered
  FROM
      positions AS positions
          LEFT JOIN
      users_positions AS users_positions ON positions.id = users_positions.position_id
          LEFT JOIN
      users AS users ON users_positions.user_id = users.id
  WHERE
      positions.day = 0
          AND positions.project_id = 1
          AND users_positions.calendar_date = '2016-08-29'
  */

  $week_day = date('w', strtotime($date));

  $this->db->select('up.id AS up_id, p.id AS pos_id, first_name, last_name, user_id, IF(up.id IS NULL, "FALSE", "TRUE") volunteered');
  $this->db->from('positions AS p');
  $this->db->join('users_positions AS up', "p.id = up.position_id", 'left');
  $this->db->join('users AS users', "up.user_id = users.id", 'left');
  $this->db->where('p.running_day', $week_day);
  $this->db->where('p.project_id', $project_id);
  $this->db->where('up.calendar_date', $date);

  $query = $this->db->get();
  return $query->result(); // return the rows selected
}

// user submits or edits attendence. if editing, all old attendence is wiped before adding updated attendence.
public function update_users_positions($date)
{
  $posted = $this->input->post(NULL, FALSE);
  $update_notification = array('failed_updated_positions' => 0, 'past_attendence_error' => false );

  if(isset($posted['up_id'])) {
    // delete previous rows from users_positions table
    foreach ($posted['up_id'] as $users_positions_id) {
      if(!empty($users_positions_id)) {
        // delete all post variables with an db id
        $this->db->delete('users_positions', array('id' => $users_positions_id));
      }
    }
  }

  // if attendee_checkbox is empty, then no checkboxes were selected
  if(isset($posted['attendee_checkbox'])) {
    // there are checkbox values. update with latest data
    foreach ($posted['attendee_checkbox'] as $checkbox_value) {
      // the position has not reached the maximum attendee limit OR the current user attendence is already marked (already attending)
      $attendee_stats = $this->check_attendee_limit($checkbox_value, $date);
      //if($attendee_stats['total_attendees'] < $attendee_stats['attendee_limit'] || $attendee_stats['user_present'] == "1") {
      if($attendee_stats['total_attendees'] < $attendee_stats['attendee_limit'] || $attendee_stats['attendee_limit'] === null) {
        $data = array(
          'user_id' => $this->current_user_id,
          'position_id' => $checkbox_value,
          'calendar_date' => $date
        );
        $this->db->insert('users_positions', $data);
      }
      else {
        $update_notification['failed_updated_positions'] += 1;
      }
    }
  }

  // set return notifications
  if($update_notification['failed_updated_positions'] > 0) {
      $this->session->set_flashdata('updated', "<div class=\"alert alert-danger\" style=\"margin-top:15px; text-align:left;\"><strong>{$update_notification['failed_updated_positions']} </strong>position(s) failed to update. Perhaps the maximum attendance was reached between the time the form was opened and it was submitted. Please review your attendence.</div>");
  }
  else {
      $this->session->set_flashdata('updated', '<div class="alert alert-success" style="margin-top:15px;"><strong>Success! </strong>Positions have been updated</div>');
  }

}


// check whether the total attendees is less than the attendee limit
// used by update_users_positions()
// fetches (single row) properties [attendee_limit], [total_attendees], [result] form DB
// also checks for logged in user being part of the project position being searched.
// returns single row with true/false for result, and true/false  for user_present
private function check_attendee_limit($pos_id = NULL, $date = NULL)
{
  /*
  SELECT
      positions.max_vol AS attendee_limit,
      COUNT(users_positions.user_id) AS total_attendees
  FROM
      positions
          INNER JOIN
      users_positions ON positions.id = users_positions.position_id
  WHERE
      positions.id = 16
          AND users_positions.calendar_date = '2016-09-05'
  */
  $user_id = $_SESSION["user_id"];
  $this->db->query('LOCK TABLES users_positions WRITE, positions WRITE');
  $this->db->select("positions.max_vol AS attendee_limit, COUNT(users_positions.user_id) AS total_attendees");
  $this->db->from('positions');
  $this->db->join('users_positions', "positions.id = users_positions.position_id", 'inner');
  $this->db->where('positions.id', $pos_id);
  $this->db->where('users_positions.calendar_date', $date);
  $query = $this->db->get();

  $attendee_stats['attendee_limit'] = $query->row()->attendee_limit;
  $attendee_stats['total_attendees'] = $query->row()->total_attendees;
  /*
  SELECT
  	IF(up.user_id LIKE 1, TRUE, FALSE ) AS user_present
  FROM
  	users_positions up
  WHERE
  	up.position_id = 1
  AND
  	up.calendar_date = '2017-05-29'
  */
  /*
  Checking if the user was present was implemented for some reason,
  but then I couldn't figure out why it was originally needed.
  The method seems to work without it, so I've commented out the following...'
  $this->db->select("IF(users_positions.user_id LIKE \"$user_id\", TRUE, FALSE ) AS user_present", FALSE); // the false arg here forces non-escaping of values and identifiers
  $this->db->from('users_positions');
  $this->db->where('users_positions.position_id', $pos_id);
  $this->db->where('users_positions.calendar_date', $date);
  $query = $this->db->get();
  if(empty($query->row()->user_present)) {
    $attendee_stats['user_present'] = null;
  }
  else {
    $attendee_stats['user_present'] = $query->row()->user_present;
  }
  */
  $this->db->query('UNLOCK TABLES');
  return $attendee_stats;
}


// get non-selectable days for datepicker to make unselectable
public function get_non_project_dates(array $project_days, $datepicker_month_range)
{
  // create an array of dates for a number of months specified by the user. 0 is used for this month
  for ($month = 0; $month <= $datepicker_month_range; $month++) {
    $dt_dates = new DateTime();
    $month_beginning = $dt_dates->format('Y-m-01');
    $dt_dates = new DateTime($month_beginning); // rollback the date to the first so we can increment months safely
    $dt_dates->add(new DateInterval("P{$month}M")); // P1M == plus 1 month
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $dt_dates->format('m'), $dt_dates->format('Y'));
    //echo $days_in_month." days in ".$dt_dates->format('m').", ";
    for ($day = 1; $day <= $days_in_month; $day++) {
      $date = $dt_dates->format('Y')."-".$dt_dates->format('m')."-".sprintf('%02d', $day); // leading zeros 05-..
      $month_days[] = $date; // holds dates for datepicker month ranges
    }
  }

	// get all the days in the calendar time span, which correspond to the project's days
  foreach ($project_days as $project_day) {
    // for each day in the project day array (mon, tue, etc...)
    for ($month = 0; $month <= $datepicker_month_range; $month++) {
      $dt_project_dates = new DateTime();
      $month_beginning = $dt_project_dates->format('Y-m-01');
      $dt_project_dates = new DateTime($month_beginning); // rollback the date to the first so we can increment months safely
      $dt_project_dates->add(new DateInterval("P{$month}M")); // P1M == plus 1 month
      $days_in_month = cal_days_in_month(CAL_GREGORIAN, $dt_project_dates->format('m'), $dt_project_dates->format('Y'));

      for ($day = 1; $day <= $days_in_month; $day++) {
        // for every day in the month
        $date = $dt_project_dates->format('Y')."-".$dt_project_dates->format('m')."-".sprintf('%02d', $day);
        $week_day = date('w', strtotime($date));
        if($week_day == $project_day){
          $project_days_this_month[] = $date; // holds project dates for a specific month
        }
      }
    }

  }

	// get all cancelled dates (set by admin - like public holidays)
	$cancelled_dates_obj = $this->get_cancelled_dates();
	if(!empty($cancelled_dates_obj)) {
    foreach ($cancelled_dates_obj as $value) {
      $cancelled_dates[] = $value->date; // prepare array
  	}
  }
  else {
    $cancelled_dates = []; // no cancelled dates have been submitted
  }

  // prepare the single level array to be passed to js array for js based datepicker
	// array_diff - subtract project dates from all dates
	// array_merge - add cancelled days to the result of non project dates
	// return all the *non project dates*. This is for the datapicker
	return '["' . implode('", "', array_merge( array_diff($month_days, $project_days_this_month), $cancelled_dates) ) . '"]';
}


// days of the week the project runs on
public function get_project_days($project)
{
  // SELECT DISTINCT day FROM positions WHERE project_id = 1 ORDER BY day ASC
  $this->db->distinct();
  $this->db->select('running_day');
  $this->db->from('positions');
  $this->db->where('project_id', $project);
  $this->db->where('is_draft', '0');
  $this->db->order_by('running_day', 'ASC');
  $query = $this->db->get();
  //print_r($query->result()); exit;
  $get_project_days = [];
  // convert the $query->result() object array to numeric array ie [1, 3, 4...]
  foreach ($query->result() as $value) {
    $get_project_days[] = $value->running_day;
  }
  return $get_project_days;
}

// check whether to skip month. if the array is empty, there are no remaining project days in the month
// return the number of months to skip. zero to stay on current month.
public function select_next_month(array $days)
{
  $project_days = $days;
  $remaining_days = [];
  // http://php.net/manual/en/class.datetime.php
  $dt = new DateTime("now");
  $d =  $dt->format('d');
  $m =  $dt->format('m');
  $y =  $dt->format('Y');
  $days_in_month = cal_days_in_month(CAL_GREGORIAN, $m,$y);
  $month_days_remaining = $days_in_month - $d;

  foreach($project_days as $day) {
    for($i = 0; $i <= $month_days_remaining; $i++) {
      $date = clone $dt; // copy the start date
      $date->modify("+{$i} day"); // add num of days to start date
      $date_day =  $date->format('w'); // get the day of the week 0-6 from the date
      if($date_day == $day) {
        //echo "Weekday: " . $date_day . " falls on " . $date->format('Y-m-d');
        $remaining_days[] = $date_day;
      }
    }
  }

  // check whether to skip month. if the array is empty, there are no remaining project days in the month
  // return the number of months to skip. zero to stay on current month.
  if( empty($remaining_days) ) {
    return '1'; // skip to the next month
  }
  else {
    return '0'; // stay on the current month
  }
}


public function is_current_project_public($project_id)
{
  // bug fix: if admin has changed status of project from published to draft, the draft project still gets called because the project id was still getting called directly from the user's saved project entry in the database
  // check if the project is still published
  $this->db->select('is_draft');
  $this->db->from('positions');
  $this->db->where('project_id', $project_id );
  $this->db->where('is_draft', '0' );
  $query = $this->db->get();

  if( count( $query->result() ) < 1 ) {
    // the project the user wants to access is no longer public
    // reset the project related session variables
    $_SESSION['selected_project_id'] = NULL;
    $_SESSION['selected_project_name'] = NULL; // appears on the menu
    return FALSE;
  }

  return TRUE;
}

public function get_cancelled_dates()
{
/*
	select
		cancelled_dates.cancelled_date as date,
    cancelled_dates.project_id,
    projects.title as project_name
	from cancelled_dates
	left join projects
	on cancelled_dates.project_id = projects.id
	order by cancelled_dates.cancelled_date
	*/
  //$query = $this->db->get(); print_r( $query->result() ); exit;
	$this->db->select('cancelled_dates.id, cancelled_dates.cancelled_date as date, cancelled_dates.project_id, projects.title as project_name');
	$this->db->from('cancelled_dates');
	$this->db->join('projects', "cancelled_dates.project_id = projects.id", 'left');
	$this->db->order_by('cancelled_dates.cancelled_date', 'ASC');

	return $this->db->get()->result();
}

public function set_cancelled_date()
{
	// check if date is valid
	$date = date_parse($this->input->post('date'));

  if( $date["error_count"] == 1 || !checkdate($date['month'], $date['day'], $date['year']) ) {
		$this->session->set_flashdata('info', "<strong>{$this->input->post('date')} </strong> is not a valid date.");
		return;
	}
	// format the date to YYYY-MM-DD for the database
	$date = date_create($this->input->post('date'));
	$date_yyyy_mm_dd = date_format($date,"Y-m-d H:i:s");
	// insert into database
	$data = array(
		'cancelled_date' => $date_yyyy_mm_dd,
		'project_id' => $this->input->post('project')
	);
	$this->db->insert('cancelled_dates', $data);
}

public function delete_cancelled_date($id = NULL)
{
	$this->db->delete('cancelled_dates', array('id' => $id));
}


/*
  HISTORY - Related to the Calendar_history controller
*/

public function get_summary($project_id, $date)
{
  $dt = new DateTime($date);
  $date_beginning = $dt->format("Y-m-01 H:i:s");
  $date_end = $dt->modify("+1 month");
  $date_end = $dt->format("Y-m-01 H:i:s");

  $this->db->select('running_day, title, description, max_vol, is_draft, last_update');
	$this->db->from('old_positions');
  $this->db->where('project_id', $project_id );
  $this->db->where( 'last_update BETWEEN ', "'$date_beginning' AND '$date_end'", FALSE);
	$this->db->order_by('id', 'ASC');

  return $this->db->get()->result();
}

// edits for a specific month only with attendee bookings.
// Only output if positions have been modified that month, else don't.
public function get_most_recent_edits($project_id, $date)
{
  $dt = new DateTime($date);
  $date_beginning = $dt->format("Y-m-01 H:i:s");
  $date_end = $dt->modify("+1 month");
  $date_end = $dt->format("Y-m-01 H:i:s");

  $days = $this->get_project_days($project_id); // return array of unique project days for a given project
  $project_dates_in_month = $this->get_project_dates_in_month($days, $date);

  /*
  SELECT
  	op.running_day, op.old_position_id, op.title, op.last_update
  FROM old_positions AS op
  INNER JOIN (SELECT old_position_id, MAX(last_update) AS max_date FROM old_positions GROUP BY old_position_id)
  AS op_join ON op.old_position_id = op_join.old_position_id AND op.last_update = op_join.max_date
  WHERE op.project_id = 3
  AND op.is_draft = 0
  AND op.running_day = 1
  AND op.last_update < '2017-06-17 23:59:59'
  ORDER BY op.last_update DESC
	*/
  $result = [];
  foreach($project_dates_in_month as $key => $project_day){
    $timestamp = strtotime($project_day);
    $running_day = date('N', $timestamp);
    $this->db->select('op.old_position_id, op.running_day, op.title, op.description, op.max_vol, op.last_update');
    $this->db->from('old_positions AS op');
    $this->db->join('(SELECT old_position_id, MAX(last_update) AS max_date FROM old_positions GROUP BY old_position_id) AS op_join', 'op.old_position_id = op_join.old_position_id AND op.last_update = op_join.max_date', 'inner');
    $this->db->where('op.project_id', $project_id);
    $this->db->where('op.is_draft', '0');
    $this->db->where('op.running_day', $running_day);
    $this->db->where('op.last_update < ', "'$project_day 23:59:59'", FALSE);
    $this->db->order_by('op.last_update', 'DESC');

    $result[]['position_info'] = $this->db->get()->result();
    $result[$key]['date'] = $project_day;
  }

  // go through each result[]
  foreach($result as $result_key => $result_value){
    // if there is an old position id available add it to the current array position
    foreach($result_value['position_info'] as $value_key => $value_value){
      $project_day = $result_value['date'];
      $result[$result_key]['position_info'][$value_key]->attendees = $this->get_attendees($project_day, $value_value->old_position_id);
    }
  }

  return $result;
}


// used by calendar history via $this->get_most_recent_edits()
public function get_attendees($project_day, $old_position_id)
{
  /*
  select
    users_positions.user_id,
    concat(users.first_name, ' ', users.last_name) as full_name
  from users_positions
  left join users
  on users_positions.user_id = users.id
  where users_positions.calendar_date = '2017-05-29'
  and users_positions.position_id = 1
  */
  $this->db->select('users_positions.user_id, CONCAT(users.first_name, " ", users.last_name) as full_name');
  $this->db->from('users_positions');
  $this->db->join('users', "users_positions.user_id = users.id", 'left');
  $this->db->where('users_positions.calendar_date', $project_day);
  $this->db->where('users_positions.position_id', $old_position_id);
  return $this->db->get()->result();
}


// used by calendar_history. get an array of dates a project runs on a given month
public function get_project_dates_in_month(array $project_days, $date)
{
  $dt_dates = new DateTime($date);
  $days_in_month = cal_days_in_month(CAL_GREGORIAN, $dt_dates->format('m'), $dt_dates->format('Y'));

  for ($day = 1; $day <= $days_in_month; $day++) {
    $date = $dt_dates->format('Y')."-".$dt_dates->format('m')."-".sprintf('%02d', $day); // leading zeros 05-..
    $month_days[] = $date; // holds dates for the month range
  }

  $project_days_this_month = [];
	// get all the days in the calendar time span, which correspond to the project's days
  foreach ($project_days as $project_day) {
    // for each day in the project day array (mon, tue, etc...)
    $dt_project_dates = new DateTime($date);
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $dt_project_dates->format('m'), $dt_project_dates->format('Y'));

    for ($day = 1; $day <= $days_in_month; $day++) {
      // for every day in the month
      $date = $dt_project_dates->format('Y')."-".$dt_project_dates->format('m')."-".sprintf('%02d', $day);
      $week_day = date('w', strtotime($date));
      if($week_day == $project_day){
        $project_days_this_month[] = $date; // holds project dates for a specific month
      }
    }
  }

  return $project_days_this_month;
}


}
