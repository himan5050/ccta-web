<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

class Fund extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->model('master/funds');
    }

    public function index()
    {
        $data = array();
        $postData = array();

        //get messages from the session
        if($this->session->userdata('success_msg')) {
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }

        if($this->session->userdata('error_msg')) {
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }

        if(!$this->session->userdata('username')) {
          redirect('user/login');
        } else {
            $data['username'] = $this->session->userdata('username');
        }


        if($this->input->post('postSubmit')) {
            for ($i=0; $i < count($this->input->post('fund_name')); $i++)
            {
                $postData = array(
                    'fund_code'                => strtoupper($this->input->post('fund_code')[$i]),
                    'fund_name'             => strtoupper($this->input->post('fund_name')[$i]),
                    'allocated_amount'      => 0.0,
                    'available_amount'      => 0.0,
                    'is_active'             => $this->input->post('is_active')[$i],
                    'date_added'            => date("m/d/Y G:i:s"),
                    'date_modified'         => date("m/d/Y G:i:s")
                );

                if (!$this->funds->check($postData['fund_code'], $postData['fund_name'])) {
                       $insert = $this->funds->add($postData);

                       $postVersionControlData = array(
                                'fund_id'               => $insert,
                             'fund_code'                => strtoupper($this->input->post('fund_code')[$i]),
                                'fund_name'             => strtoupper($this->input->post('fund_name')[$i]),
                                'allocated_amount'      => 0.0,
                                'available_amount'      => 0.0,
                                'date_added'            => date("m/d/Y G:i:s")
                          );

                          $insertVersionControl = $this->funds->addVersionControl($postVersionControlData);

                    if($insert) {
                           $this->session->set_userdata('success_msg', 'Fund has been added successfully.');
                           redirect('/fund');
                    }else{
                        $data['error_msg'] = 'Some problems occurred, please try again.';
                    }
                } else {
                    $data['error_msg'] = 'Fund already exists.';
                }

            }
        }

        $data['total_allocated_amount'] = $this->funds->total_allocated_amount();
        $data['total_available_amount'] = $this->funds->total_available_amount();
        $data['fund_lists'] = $this->funds->getRows();
        $data['title'] = 'Fund Master';

        //load the list page view
        $this->load->view('common/header', $data);
        $this->load->view('master/fund/add', $data);
        $this->load->view('common/footer');
    }

    public function edit($id)
    {
        $data = array();

        $postData = $this->funds->getRows($id);
        $data['fund_lists'] = $this->funds->getRows();

        if(!$this->session->userdata('username')) {
          redirect('user/login');
        } else {
            $data['username'] = $this->session->userdata('username');
        }


        $data['title'] = 'Edit Fund Master';

        if($this->input->post('postSubmit')) {
            $postData = array(
                'fund_name'             => strtoupper($this->input->post('fund_name')),
                'is_active'             => $this->input->post('is_active'),
                'date_modified'         => date("m/d/Y G:i:s")
            );

            $update = $this->funds->update($postData, $id);
            $master_fund = $this->funds->getRows($id);

            $postVersionControlData = array(
                'fund_id'               => $id,
              'fund_code'                => strtoupper($master_fund['fund_code']),
                'fund_name'             => strtoupper($this->input->post('fund_name')),
                'allocated_amount'      => $this->funds->getRows($id)['allocated_amount'],
                'available_amount'      => $this->funds->getRows($id)['available_amount'],
                'date_added'            => date("m/d/Y G:i:s")
            );

            $insertVersionControl = $this->funds->addVersionControl($postVersionControlData);

            if($update) {
                   $this->session->set_userdata('success_msg', 'Funds has been updated successfully.');
                   redirect('/fund');
            }else{
                $data['error_msg'] = 'Some problems occurred, please try again.';
            }
        }

        $data['total_allocated_amount'] = $this->funds->total_allocated_amount();
        $data['total_available_amount'] = $this->funds->total_available_amount();
        $data['post'] = $postData;

        //load the edit page view
        $this->load->view('common/header', $data);
        $this->load->view('master/fund/edit', $data);
        $this->load->view('common/footer');
    }

    public function delete($id)
    {
        if($id) {
            $delete = false;
            $row = $this->funds->getRows($id);
            if (!$row['allocated_amount']) {
                if($row['fund_id'] != 1) {
                    $delete = $this->funds->delete($id);
                    if($delete) {
                        $this->session->set_userdata('success_msg', 'Funds has been removed successfully.');
                    }else{
                        $this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
                    }
                } else {
                    $this->session->set_userdata('error_msg', 'Unfunded resouce is not allowed to delete.');
                }

            } else {
                $this->session->set_userdata('error_msg', 'Fund cannot be deleted, since allocated to projects');
            }
        }
        redirect('/fund');
    }

    public function check()
    {
        $fund_name = $this->input->post('fund_name');
        $result = $this->funds->check($fund_name);
        echo json_encode($result);
    }

    public function history()
    {
        $fund = $this->input->post('fund_id');
        $result = $this->funds->history($fund);
        echo json_encode($result);
    }
}
