<?php defined('BASEPATH') OR exit('No direct script access allowed');

// I was going to use this but have moved the methods to the relavent models
class MY_Model extends CI_Model
{
  private $current_user_id; 

  function __construct()
  {
    parent::__construct();
    $this->current_user_id = $this->ion_auth->user()->row()->id;
  }

}