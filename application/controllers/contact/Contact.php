<?php defined('BASEPATH') OR exit('No direct script access allowed');

// use the contact form to send email to the organiser from the user
class Contact extends Member_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->data['page_title'] = 'Contact';
    $sender_email = $this->ion_auth->user()->row()->email;
    $sender_name = $this->ion_auth->user()->row()->first_name.' '.$this->ion_auth->user()->row()->last_name;
    $organiser = $this->config->item('organiser');
    $server_email = $this->config->item('server_email');
    $message_introduction = 'Message from: '.$sender_name.'\n\n';
  }

  function index()
  {
    // validation rules
    $this->form_validation->set_rules('title', 'Title', 'trim|required');
    $this->form_validation->set_rules('message', 'Message', 'trim|required');

    if ($this->form_validation->run() === FALSE) {
      // set any errors and display the form
      $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
      $this->render('contact/contact_view');
    }
    else {
      $this->email->from($server_email, '');
  		$this->email->to($organiser);
      $this->email->reply_to($sender_email, $sender_name);
  		$this->email->subject('['.$this->config->item('cms_title').'] '.$this->input->post('title'));
  		$this->email->message($message_introduction . $this->input->post('message'));

      if($this->email->send()){
        $this->session->set_flashdata('message', '<div class="alert alert-success"><strong>Mail sent. </strong>A reply will be sent to your user email address.</div>');
      }
  		else {
        $this->session->set_flashdata('updated', '<div class="alert alert-success"><strong>Sorry, </strong>unable to send email.</div>');
  		}

      $this->render('contact/contact_view');
    }
  }

}
