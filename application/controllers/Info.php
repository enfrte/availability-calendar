<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Info / news page created by the super admin
class Info extends Member_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->data['page_title'] = "Info page";
    $this->load->model('info_model');

    // default (prefilled) values
    $this->data['title_content'] = "Page not created";
    $this->data['body_content'] = "<p><strong>This page has not been created yet.</strong></p>
<p>Super admin can create content for the information page.</p>
<img src=\"http://www.w3schools.com/tags/smiley.gif\" alt=\"Smiley face\">";

    $this->data['info_data'] = $this->info_model->read();
    // if data exists, write over prefilled values with user values from database
    if( count($this->data['info_data']) > 0 ) { 
      $this->data['title_content'] = $this->data['info_data']->title;
      $this->data['body_content'] = $this->data['info_data']->body;
     }

  }


  public function index()
  {
    $this->render('/info/info_view');
  }

  // edit the info page. As data is added by super admin, it is somewhat trusted and doesn't have xss sanitation
  public function edit() 
  { 
    $this->securityAccess('super_admin');
    // form validation and redirection
    $validate = $this->info_model->validate['organisation_info'];
    $this->form_validation->set_rules($validate);
    // handle form submission
    if($this->form_validation->run() === FALSE) {
      $this->render('info/edit_info_view');
    } else {
      $this->info_model->update($this->input->post('title'), $this->input->post('body'));
      redirect('info');
    }

  }

}
