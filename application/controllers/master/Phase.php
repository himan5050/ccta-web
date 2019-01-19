<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phase extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->model('master/phases');
        $this->load->model('mapping/projects_phases_contracts');
    }

    public function index(){
        $data = array();
        $postData = array();

        //get messages from the session
        if($this->session->userdata('success_msg')){
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }

        if($this->session->userdata('error_msg')){
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }

        if($this->input->post('postSubmit')) {
            for ($i=0; $i < count($this->input->post('phase_name')); $i++) {
                $postData = array(
                'phase_code' => $this->input->post('phase_code')[$i],
                'phase_name' => $this->input->post('phase_name')[$i],
                'phase_description' => $this->input->post('phase_description')[$i],
                'weight' => $this->input->post('weight')[$i],
                'date_added' => date("m/d/Y G:i:s"),
                'date_modified' => date("m/d/Y G:i:s")
                );

                $insert = $this->phases->add($postData);

            }

            if($insert){
                $this->session->set_userdata('success_msg', 'Phase has been added successfully.');
                redirect('/phase');
            }else{
                $data['error_msg'] = 'Some problems occurred, please try again.';
            }
        }

        $data['phase_lists'] = $this->phases->getRows();
        $data['title'] = 'Project Phase Master';

        //load the list page view
        $this->load->view('common/header',$data);
        $this->load->view('master/phase/add', $data);
        $this->load->view('common/footer');
    }

    public function view($id) {
        $data = array();

        if(!empty($id))
        {
            $data['view'] = $this->phases->getRows($id);

            $data['phase_code'] = $data['view']['phase_code'];
            $data['phase_name'] = $data['view']['phase_name'];
            $data['phase_description'] = $data['view']['phase_description'];
            $data['date_added'] = $data['view']['date_added'];
            $data['date_modified'] = $data['view']['date_modified'];

            $data['phase_lists'] = $this->phases->getRows();

            $data['title'] = 'Project Phase Master';

            //load the details page view
            $this->load->view('common/header',$data);
            $this->load->view('master/phase/view', $data);
            $this->load->view('common/footer');
        }
        else
        {
            redirect('/phase');
        }
    }

    public function edit($id){
        $data = array();
        $postData = $this->phases->getRows($id);
        $data['phase_lists'] = $this->phases->getRows();
        $data['title'] = 'Project Phase Master Edit';

        if($this->input->post('postSubmit')){

            $postData = array(
                'phase_name' => $this->input->post('phase_name'),
                'phase_description' => $this->input->post('phase_description'),
            	'weight' => $this->input->post('weight'),
                'date_modified' => date("m/d/Y G:i:s")
            );

            $update = $this->phases->update($postData, $id);

            if($update){
                $this->session->set_userdata('success_msg', 'Project has been updated successfully.');
                redirect('/phase');
            }else{
                $data['error_msg'] = 'Some problems occurred, please try again.';
            }
        }

        $data['post'] = $postData;

        //load the edit page view
        $this->load->view('common/header', $data);
        $this->load->view('master/phase/edit', $data);
        $this->load->view('common/footer');
    }

    public function delete($id){
        if($id) {
        	if (!$this->projects_phases_contracts->getRowsByPhase($id)) {
        		$delete = $this->phases->delete($id);
        		if($delete){
                	$this->session->set_userdata('success_msg', 'Phase has been removed successfully.');
            	}else{
                	$this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
            	}
        	} else {
        		$this->session->set_userdata('error_msg', 'Phase is used by projects');
        	}
        }
        redirect('/phase');
    }

    public function check() {
        $phase_name = $this->input->post('phase_name');
        $result = $this->phases->check($phase_name);
        echo json_encode($result);
    }
}
