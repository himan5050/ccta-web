<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

	public function index()
	{
		$data = array();

        $data['title'] = 'Ficus Dashboard';

        $this->load->view('common/header', $data);
        $this->load->view('dashboard');
        $this->load->view('common/footer');
	}
}
