<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_reminder_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

public function get_email_reminder()
{
  $dt = new DateTime();
  $dt->add(new DateInterval("P1D")); // plus 1 day
  $date = $dt->format('Y-m-d');
  $result = [];

  // get list of user email addresses signed up for tomorrow's projects
  $attendee_list = $this->db->query("
  select distinct users.email from users_positions as up
  left join users
  on up.user_id = users.id
  where up.calendar_date = '$date'
  ");
  $result['attendee_list'] = $attendee_list->result();

  // get list of projects/positions running tomorrow and the attendees attending
  $position_list = $this->db->query("
  select
  	pro.title as project_title,
  	pos.title as position_title,
      pos.description,
      users.email,
      up.calendar_date
  from positions as pos

  left join projects as pro
  on pos.project_id = pro.id

  left join users_positions as up
  on pos.id = up.position_id

  left join users
  on up.user_id = users.id

  where up.calendar_date = '$date'
  ");
  $result['position_list'] = $position_list->result();

  return $result;
}

}
