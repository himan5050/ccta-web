<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index()
    {
        $data = array();

        $data['title'] = 'Ficus Dashboard';

        if(!$this->session->userdata('username')) {
          redirect('user/login');
        } else {
            $data['username'] = $this->session->userdata('username');
        }

        $this->load->view('common/header', $data);
        $this->load->view('dashboard');
        $this->load->view('common/footer');
    }
}
