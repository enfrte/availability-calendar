<?php defined('BASEPATH') OR exit('No direct script access allowed');

// this will run as a cron job
if ( !is_cli() ) { die("<h1>You can only call this from the commandline</h1>"); } 

// cron executed class - you can make file permissions 600
class Email_reminder extends Admin_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('email_reminder_model');
  }

  function index()
  {
    // get email content
    $email_reminder_info = $this->email_reminder_model->get_email_reminder();

    $email_list = [];
    // organise all positions under each attendee email address
    foreach ($email_reminder_info['attendee_list'] as $attendee) {
      foreach ($email_reminder_info['position_list'] as $position) {
        // if current attendee email maches one in the position attendee list
        if($attendee->email == $position->email) {
          $position_info = "<p>For $position->project_title, you have marked attendence for $position->position_title";
          if(!empty($position->description)) {
            $position_info .= "<br>Description: $position->description</p>";
          }
          else {
            $position_info .= "</p>";
          }
          $email_list[$attendee->email][] = $position_info;
        }
      }
    }

    // send the email
    $server_email = $this->config->item('server_email'); 
    $this->email->from($server_email, $server_email);
    $this->email->reply_to($this->config->item('reply_to_email'), $this->config->item('reply_to_name'));
    $this->email->subject('['.$this->config->item('cms_title').'] '.' Reminder service');
    $email_content = "<h4>Here is your reminder for tomorrow.</h4>";
    // to each email address in the list
    foreach ($email_list as $attendee_email => $position_info) {
      $this->email->to($attendee_email);
      foreach ($position_info as $info) {
        $email_content .= $info;
      }
      $email_content .= "<p>See you then.</p>";
      $this->email->message($email_content);
      if(!$this->email->send()){
        echo "Send error";
  		}
    }

  }

}
