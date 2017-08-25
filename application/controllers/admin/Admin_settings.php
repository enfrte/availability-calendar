<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Currently not used
class Admin_settings extends Admin_Controller
{
function __construct()
{
  parent::__construct();
  $this->data['page_title'] = 'Contact settings';
}

public function settings()
{
  // output a list of all admin.
  // go through the admin settings db table and create a set of bool settings values for each user.
  // send the dataset to the view so that it can populate each setting with the value in the checkbox of each setting.
  //
}

}
