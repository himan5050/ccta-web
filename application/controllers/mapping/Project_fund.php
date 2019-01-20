<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

/**
 * @file
 * Project Fund class.
 */
class Project_Fund extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('mapping/projectfund');
        $this->load->model('master/funds');
        $this->load->model('mapping/project_funds');
        $this->load->model('master/projects');
        $this->load->model('mapping/financial_plans');
        $this->load->model('mapping/projects_phases_contracts');
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

        if(!$this->session->userdata('project_id')) {
            redirect('/project');
        } else {
            $data["project_name"] = $this->projects_phases_contracts->getProjectName($this->session->userdata('project_id'));
        }

        if($this->input->post('postSubmit')) {
            for ($i=0; $i < count($this->input->post('fund_id')); $i++) {
                $postData = array(
                    'fund_id'                => $this->input->post('fund_id')[$i],
                 'project_id'            => $this->session->userdata('project_id'),
                    'allocated_amount'      => $this->input->post('allocated_amount')[$i],
                 'available_amount'      => $this->input->post('allocated_amount')[$i],
                    'programming_action'    => $this->input->post('programming_action')[$i],
                    'notes'                 => $this->input->post('notes')[$i],
                    'is_active'             => $this->input->post('is_active')[$i],
                    'date_added'            => date("m/d/Y G:i:s"),
                    'date_modified'         => date("m/d/Y G:i:s")
                );

                if (!$this->project_funds->check($this->input->post('fund_id')[$i], $this->session->userdata('project_id'))) {
                       $delta = $this->validateProjectFundAllocation($postData);
                    if (!$delta) {
                        $insert = $this->project_funds->add($postData);
                        $postVersionControlData = array(
                           'project_fund_id'       => $insert,
                        'fund_id'                => $this->input->post('fund_id')[$i],
                           'project_id'            => $this->session->userdata('project_id'),
                              'allocated_amount'      => $this->input->post('allocated_amount')[$i],
                           'available_amount'      => $this->input->post('allocated_amount')[$i],
                              'programming_action'    => $this->input->post('programming_action')[$i],
                              'notes'                 => $this->input->post('notes')[$i],
                              'date_added'            => date("m/d/Y G:i:s")
                           );

                           $insertVersionControl = $this->project_funds->addVersionControl($postVersionControlData);

                        if($insert) {
                                        $this->updateFundMaster($postData);
                                        $this->session->set_userdata('success_msg', 'Fund has been added successfully.');
                                        redirect('/project_funds');
                        }else{
                                  $data['error_msg'] = 'Some problems occurred, please try again.';
                        }
                    } else {
                        //    $data['error_msg'] = 'Fund allocation failed due to minimal project cost, Please increase total project cost upto '.$delta;
                        $data['error_msg'] = 'Funds exceed project cost. Please increase project cost to ' . $delta . ' before you enter this funding';
                    }
                } else {
                    $data['error_msg'] = 'Fund already exists';
                }
            }
        }

        $fund_source_lists = $this->project_funds->getFundSourceList();

        foreach ($fund_source_lists as $fund_source_list) {
            $data['fund_sources'][] = array(
                'fund_id'      => $fund_source_list['fund_id'],
                'fund_name'    => $fund_source_list['fund_name']
            );
        }


        $data['fund_lists'] = [];
        $fund_lists = $this->project_funds->getRowsByProject($this->session->userdata('project_id'));
        // Parsed all ids into name.
        if (!empty($fund_lists)) {
            $data['fund_lists'] = $this->parseFundLists($fund_lists);
        }

        $data['total_project_cost'] = 0.0;
        $project_data = $this->projects->getRows($this->session->userdata('project_id'));
        if (!empty($project_data)) {
            $data['total_project_cost'] = $project_data['total_project_cost'];
        }

        $data['budget'] = 0.0;
        $budget = $this->project_funds->budget($this->session->userdata('project_id'));
        if (!empty($budget) && $budget > 0) {
            $data['budget'] = $budget;
        }

        $data['unfunded'] = $data['total_project_cost'] - $data['budget'];
        $data['title'] = 'Fund Master';

        //load the list page view
        $this->load->view('common/header', $data);
        $this->load->view('mapping/project_funds/add', $data);
        $this->load->view('common/footer');
    }

    protected function parseFundLists($fund_lists)
    {
        $parsedFundLists = [];
        foreach ($fund_lists as $fund_list) {
            if (!empty($fund_list)) {
                foreach ($fund_list as $key => $value) {
                    $parser[$key] = $value;
                    if ($key == 'fund_id') {
                        $fund = $this->funds->getRows($value);
                        if(!empty($fund)) {
                            $parser['fund_code'] = isset($fund['fund_code']) ? $fund['fund_code'] : '';
                            $parser['fund_name'] = isset($fund['fund_name']) ? $fund['fund_name'] : '';
                        }
                    }
                }

                $parsedFundLists[] = $parser;
            }
        }

        return $parsedFundLists;
    }

    /**
     * Edit handler of Project Fund.
     *
     * @param int $id
     *  Project Fund Id.
     */
    public function edit($id)
    {
        $data = [];
        // set session values.
        if(!$this->session->userdata('project_id')) {
            redirect('/project');
        } else {
            $data["project_name"] = $this->projects_phases_contracts->getProjectName($this->session->userdata('project_id'));
        }

        $postData = $this->project_funds->getRows($id);
        $postData['fund_name'] = $this->funds->getRows($postData['fund_id'])['fund_name'];

        $fund_lists = $this->project_funds->getRowsByProject($this->session->userdata('project_id'));
        // Parsed all ids into name.
        if (!empty($fund_lists)) {
            $data['fund_lists'] = $this->parseFundLists($fund_lists);
        }

        $data['title'] = 'Fund Master Edit';

        if ($this->input->post('postSubmit')) {
            if (!($error_msg = validate_edit_project_fund($this->session->userdata('project_id'), $id, $this->input->post(), $this->project_funds, $this->funds, $this->financial_plans))) {
                $old_allocated = $postData['allocated_amount'];
                $old_available = $postData['available_amount'];
                $new_allocated = $this->input->post('allocated_amount');
                $delta = $new_allocated - $old_allocated;
                $new_available = $old_available + $delta;

                if ($new_available >= 0) {
                    $postUpdate = array(
                        'allocated_amount'      => $this->input->post('allocated_amount'),
                    'available_amount'      => $new_available,
                        'programming_action'    => $this->input->post('programming_action'),
                        'notes'                 => $this->input->post('notes'),
                        'is_active'             => $this->input->post('is_active'),
                        'date_modified'         => date("m/d/Y G:i:s")
                       );

                       $update = $this->project_funds->update($postUpdate, $id);

                       $postVersionControlData = array(
                          'project_fund_id'       => $id,
                             'fund_id'               => $postData['fund_id'],
                        'project_id'            => $this->session->userdata('project_id'),
                             'allocated_amount'      => $this->input->post('allocated_amount'),
                       'available_amount'      => $new_available,
                             'programming_action'    => $this->input->post('programming_action'),
                             'notes'                 => $this->input->post('notes'),
                             'date_added'            => date("m/d/Y G:i:s")
                       );

                       $insertVersionControl = $this->project_funds->addVersionControl($postVersionControlData);

                    if($update) {
                          $this->updateFundMasterEdit($delta, $postData['fund_id']);
                          $this->session->set_userdata('success_msg', 'Funds has been updated successfully.');
                          redirect('/project_funds');
                    }else{
                        $data['error_msg'] = 'Some problems occurred, please try again.';
                    }
                } else {
                    $data['error_msg'] = 'Available funds can not be negative.';
                }
            } else {
                $data['error_msg'] = $error_msg;
            }
        }

        $fund_source_lists = $this->project_funds->getFundSourceList();

        foreach ($fund_source_lists as $fund_source_list) {
            $data['fund_sources'][] = array(
                'fund_id'      => $fund_source_list['fund_id'],
                'fund_name'    => $fund_source_list['fund_name']
            );
        }


        // Parsed all ids into name.
        if (!empty($fund_lists)) {
            $data['fund_lists'] = $this->parseFundLists($fund_lists);
        }

        $data['total_project_cost'] = 0.0;
        $project_data = $this->projects->getRows($this->session->userdata('project_id'));
        if (!empty($project_data)) {
            $data['total_project_cost'] = $project_data['total_project_cost'];
        }

        $data['budget'] = 0.0;
        $budget = $this->project_funds->budget($this->session->userdata('project_id'));
        if (!empty($budget) && $budget > 0) {
            $data['budget'] = $budget;
        }

        $data['unfunded'] = $data['total_project_cost'] - $data['budget'];
        $data['title'] = 'Fund Master';

        $data['post'] = $postData;

        //load the edit page view
        $this->load->view('common/header', $data);
        $this->load->view('mapping/project_funds/edit', $data);
        $this->load->view('common/footer');
    }

    public function delete($id)
    {
        if($id) {
            $delete = false;
            $row = $this->project_funds->getRows($id);
            if (!$this->financial_plans->getRowsByFundAllocation($row['fund_id'], $row['project_id'])) {
                $delete = $this->project_funds->delete($id);
                $this->updateMasterFund($row);
                if($delete) {
                    $this->session->set_userdata('success_msg', 'Funds has been removed successfully.');
                }else{
                    $this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
                }
            } else {
                $this->session->set_userdata('error_msg', 'Fund is not allowed to delete, Please reallocate fund from Projects');
            }
        }

        redirect('/project_funds');
    }

    public function updateMasterFund($row)
    {
        //get master fund.
        $master_fund = $this->funds->getRows($row['fund_id']);
        if (!empty($master_fund)) {
            $updated_allocation = $master_fund['allocated_amount'] - $row['allocated_amount'];
            $updated_available = $master_fund['available_amount'] - $row['allocated_amount'];
            if (($updated_available >= 0) && ($updated_allocation >= 0)) {
                $update = [
                 'allocated_amount' => $updated_allocation,
                 'available_amount' => $updated_available,
                ];

                $result = $this->funds->update($update, $row['fund_id']);
                if ($result) {
                    $revision = [
                    'fund_id' => $master_fund['fund_id'],
                    'fund_code' => $master_fund['fund_code'],
                    'fund_name' => $master_fund['fund_name'],
                    'allocated_amount' => $updated_allocation,
                    'available_amount' => $updated_available,
                    'date_added' => date("m/d/Y G:i:s")
                    ];

                    $this->funds->addVersionControl($revision);
                    return true;
                }
            }
        }

        return false;
    }

    public function check()
    {
        $fund_id = $this->input->post('fund_id');
        $result = $this->project_funds->check($fund_id, $this->session->userdata('project_id'));
        echo json_encode($result);
    }

    public function history()
    {
        $project_fund_id = $this->input->post('project_fund_id');
        $result = $this->project_funds->history($project_fund_id);
        foreach ($result as $res) {
            $fund_name = $this->funds->getRows($res['fund_id'])['fund_name'];
            if ($fund_name) {
                $res['fund_name'] = $fund_name;
            }
            $final_result[] = $res;
        }
        echo json_encode($final_result);
    }

    protected function updateFundMaster($postData)
    {
        $fund = $this->funds->getRows($postData['fund_id']);
        $allocated = $fund['allocated_amount'] + $postData['allocated_amount'];
        $available = $fund['available_amount'] + $postData['available_amount'];

        $postUpdate = array(
                'allocated_amount'   => $allocated,
                'available_amount'   => $available,
        );

        $update = $this->funds->update($postUpdate, $postData['fund_id']);
        $master_update = $this->funds->getRows($postData['fund_id']);

        $postVersionControlData = array(
                'fund_id'               => $postData['fund_id'],
                                'fund_code'               => $master_update['fund_code'],
                'fund_name'             => strtoupper($fund['fund_name']),
                'allocated_amount'      => $allocated,
                'available_amount'      => $available,
                'date_added'            => date("m/d/Y G:i:s")
        );

        $insertVersionControl = $this->funds->addVersionControl($postVersionControlData);

    }

    protected function updateFundMasterEdit($delta, $fund_id)
    {
        $fund = $this->funds->getRows($fund_id);
        $allocated = $fund['allocated_amount'] + $delta;
        $available = $fund['available_amount'] + $delta;

        $postData = array(
                'allocated_amount'   => $allocated,
                'available_amount'   => $available,
        );

        $update = $this->funds->update($postData, $fund_id);

        $postVersionControlData = array(
                'fund_id'               => $fund_id,
                                'fund_code'               => $fund['fund_code'],
                'fund_name'             => strtoupper($fund['fund_name']),
                'allocated_amount'      => $allocated,
                'available_amount'      => $available,
                'date_added'            => date("m/d/Y G:i:s")
        );

        $insertVersionControl = $this->funds->addVersionControl($postVersionControlData);

    }

    protected function validateProjectFundAllocation($postData)
    {
        $total_project_cost = 0.0;
        if ($this->projects->getRows($postData['project_id'])['total_project_cost']) {
            $total_project_cost = $this->projects->getRows($postData['project_id'])['total_project_cost'];
        }

        $budget = 0.0;
        if ($this->project_funds->budget($postData['project_id'])) {
            $budget = $this->project_funds->budget($postData['project_id']);
        }
        $delta = $total_project_cost - ($budget + $postData['allocated_amount']);
        $diff = $budget + $postData['allocated_amount'];
        if ($delta < 0) {
            return $diff;
        }

        return false;
    }
}
