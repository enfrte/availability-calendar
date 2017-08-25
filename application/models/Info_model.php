<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Info_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public $validate = array(
        'organisation_info'=>
        array(
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required|max_length[255]'),
            'body' => array('field'=>'body','label'=>'Content','rules'=>'trim|required')
        )
    );

    // get the info page contents
    public function read()
    {
        $this->db->select('title, body'); // the false arg here forces non-escaping of values and identifiers
        $this->db->from('info');
        $query = $this->db->get();
        return $query->row(); 
    }

    // get the info page contents
    public function update($title, $body)
    {
        $posted = $this->input->post(NULL, FALSE);

        $data = array(
            'id' => '0',
            'title'  => $posted['title'],
            'body'  => $posted['body']
        );

        $this->db->replace('info', $data);
    }  

}
