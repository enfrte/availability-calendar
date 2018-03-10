<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Set cancelled dates to be omitted from the calendar
class Cancelled_dates extends Admin_Controller
{
  function __construct()
  {
    parent::__construct();
		$this->load->model('projects/calendar_model');
		$this->load->model('projects/projects_model');
		$this->data['page_title'] = 'Cancelled dates';
  }

  public function index()
  {
		$this->data['before_body'] .= '<script src="'.site_url('assets/js/acal/confirmation.js').'"></script>';
		$this->data['projects'] = $this->projects_model->get_projects();
		//print_r($this->data['projects']); exit;
		$this->data['cancelled_dates'] = $this->calendar_model->get_cancelled_dates();
		//print_r($this->data['cancelled_dates']); exit;

		$this->form_validation->set_rules('date','Date','trim|required');
    $this->form_validation->set_rules('project','Project','trim|required|integer');

    if($this->form_validation->run()===FALSE)
    {
			$this->render('admin/cancelled_dates');
    }
    else
    {
			$this->calendar_model->set_cancelled_date();
			redirect('admin/cancelled_dates','refresh');
    }
  }

	public function delete_date($id = NULL)
	{
		$this->calendar_model->delete_cancelled_date($id);
		redirect('admin/cancelled_dates','refresh');
	}
}
