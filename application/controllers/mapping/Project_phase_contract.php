<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

/**
 * @file
 * Projects Phases Contracts Module Class.
 */
class Project_Phase_Contract extends CI_Controller
{

    /**
     * Constructor of Class.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('mapping/projectphasecontract');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model('mapping/projects_phases_contracts');
        $this->load->model('mapping/financial_plans');
        $this->load->model('master/projects');
    }

    /**
     * Index call.
     */
    public function index()
    {
        $data = [];
        //get messages from the session
        if ($this->session->userdata('success_msg')) {
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }
        if ($this->session->userdata('error_msg')) {
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }
        // check project id.
        if (!$this->session->userdata('project_id')) {
            redirect('/project');
        } else {
            $data["project_name"] = $this->projects_phases_contracts->getProjectName($this->session->userdata('project_id'));
        }

        if ($this->input->post('postSubmit')) {
            $data = $this->input->post();
            for ($i=0; $i < count($data['contract']); $i++) {
                if ($_FILES['attachment']['size'][$i] != 0) {
                    $_FILES['userFile']['name'] = $_FILES['attachment']['name'][$i];
                    $_FILES['userFile']['type'] = $_FILES['attachment']['type'][$i];
                    $_FILES['userFile']['tmp_name'] = $_FILES['attachment']['tmp_name'][$i];
                    $_FILES['userFile']['error'] = $_FILES['attachment']['error'][$i];
                    $_FILES['userFile']['size'] = $_FILES['attachment']['size'][$i];
                    $uploadPath = 'uploads/';
                    $config['upload_path'] = $uploadPath;
                    $config['allowed_types'] = 'pdf|jpg|jpeg|JPG|PNG|png';
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('userFile')) {
                        $fileData = $this->upload->data();
                        $data[$i]['file_name'] = $fileData['file_name'];
                    }
                }
                if (!isset($data[$i]['file_name'])) {
                    $data[$i]['file_name'] = "";
                }

                if (!is_numeric($state = set_phase_contract_state($data['start_date'][$i], $data['end_date'][$i]))) {
                    $this->session->set_userdata('error_msg', $val);
                    redirect('project_phase_contract');
                }

                $postData = [
                'project_id' => $this->session->userdata('project_id'),
                'phase_id' => $data['phase_id'][$i],
                'contract' => $data['contract'][$i],
                'amount' => $data['amount'][$i],
                'contract_start_date' => $data['start_date'][$i],
                'contract_end_date' => $data['end_date'][$i],
                'description' => $data['description'][$i],
                'attachment' => $data[$i]['file_name'],
                'status' => $state,
                'modified_date' => date("m/d/Y G:i:s")
                ];

                if ($val = validate_project_phase_contract($this->session->userdata('project_id'), $data, 'add', $this->projects_phases_contracts, $this->projects, $this->financial_plans)) {
                    if ($val == 2) { // Start date is before project start date.
                        $this->session->set_userdata('error_msg', 'Contract Start date can not before Project start date.');
                        redirect('/project_phase_contract');
                    }
                    if ($val == 3) { // End date is beyond project end date.
                          $this->session->set_userdata('error_msg', 'Contract End date can not beyond project end date.');
                          redirect('/project_phase_contract');
                    }
                    if (!$this->projects_phases_contracts->check($data['contract'][$i], $data['phase_id'][$i], $this->session->userdata('project_id'))) {
                          $insert = $this->projects_phases_contracts->add($postData);
                        if ($insert) {
                            $postDataVersionControl = [
                            'project_id' => $this->session->userdata('project_id'),
                            'project_phase_contract_id' => $insert,
                            'phase_id' => $data['phase_id'][$i],
                            'contract' => $data['contract'][$i],
                            'amount' => $data['amount'][$i],
                            'contract_start_date' => $data['start_date'][$i],
                            'contract_end_date' => $data['end_date'][$i],
                            'description' => $data['description'][$i],
                            'attachment' => $data[$i]['file_name'],
                            'status' => $state,
                            'date_added' => date("m/d/Y G:i:s")
                            ];
                            // Create revision.
                            $insertvc = $this->projects_phases_contracts->addversioncontrol($postDataVersionControl);

                            $this->session->set_userdata('success_msg', 'Phase to Contract mapping has been added successfully.');
                            redirect('/project_phase_contract');
                        } else {
                            $data['error_msg'] = 'Some problems occurred, please try again.';
                        }
                    } else {
                              // check project id.
                        if (!$this->session->userdata('project_id')) {
                            redirect('/project');
                        } else {
                            $data["project_name"] = $this->projects_phases_contracts->getProjectName($this->session->userdata('project_id'));
                        }
                        $data['error_msg'] = 'Contract Already Exists.';
                    }
                } else {
                    // check project id.
                    if (!$this->session->userdata('project_id')) {
                        redirect('/project');
                    } else {
                        $data["project_name"] = $this->projects_phases_contracts->getProjectName($this->session->userdata('project_id'));
                    }
                    $data['error_msg'] = 'Total amount can not exceed project total cost.';
                }
            }
        }

        if (!$this->input->post('postFilter')) {
            // Get Results to display list of phase to contracts.
            $results = $this->projects_phases_contracts->getRows();
            $data['project_phase_contract_lists'] = [];

            // Total amount of contracts.
            $total_contracts_amount = 0;
            $master_project = $this->projects->getRows($this->session->userdata('project_id'));

            foreach ($results as $result) {
                $data['project_phase_contract_lists'][] = [
                'project_phase_contract_id' => $result['project_phase_contract_id'],
                'project_id' => $result['project_id'],
                'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                'contract' => $result['contract'],
                'amount' => $result['amount'],
                'start_date' => $result['contract_start_date'],
                'end_date' => $result['contract_end_date'],
                'description' => $result['description'],
                'attachment' => $result['attachment'],
                'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                'status' => get_phase_contract_state($result['status'])
                ];

                // Calculate total amount of contracts.
                $total_contracts_amount = $total_contracts_amount + $result['amount'];
            }

            $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
            $phase_lists = $this->projects_phases_contracts->getPhaseList();
            if (!empty($phase_lists)) {
                foreach ($phase_lists as $phase_list) {
                    $data['phases'][] = [
                    'phase_id' => $phase_list['phase_id'],
                    'phase_name' => $phase_list['phase_name']
                    ];
                }
            }

            $data['title'] = 'Phase To Contract Mapping';
            $data['total_contracts_amount'] = $total_contracts_amount;
            $data['remaining_amount'] = $master_project['total_project_cost'] - $total_contracts_amount;

            //load the list page view
            $this->load->view('common/header', $data);
            $this->load->view('mapping/project_phase_contract/add', $data);
            $this->load->view('common/footer');
        } else {
            $project_phase = $this->input->post('phasefilter');
            $contract = $this->input->post('contractfilter');
            $amount = $this->input->post('amountfilter');

            if($project_phase && $contract) {
                if($amount) {
                    // Get Results to display list of phase to contracts.
                    $results = $this->projects_phases_contracts->getRowsByProjectPhaseContractAmount($this->session->userdata('project_id'), $project_phase, $contract, $amount);
                    // Total amount of contracts.
                    $total_contracts_amount = 0;
                    $master_project = $this->projects->getRows($this->session->userdata('project_id'));
                    if (!empty($results)) {
                        foreach ($results as $result) {
                            $data['project_phase_contract_lists'][] = [
                            'project_phase_contract_id' => $result['project_phase_contract_id'],
                            'project_id' => $result['project_id'],
                            'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                            'contract' => $result['contract'],
                            'amount' => $result['amount'],
                            'start_date' => $result['contract_start_date'],
                            'end_date' => $result['contract_end_date'],
                            'description' => $result['description'],
                            'attachment' => $result['attachment'],
                            'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                            'status' => get_phase_contract_state($result['status'])
                            ];

                            // Calculate total amount of contracts.
                            $total_contracts_amount = $total_contracts_amount + $result['amount'];
                        }

                        $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                    }

                    $phase_lists = $this->projects_phases_contracts->getPhaseList();
                    if (!empty($phase_lists)) {
                        foreach ($phase_lists as $phase_list) {
                            $data['phases'][] = [
                            'phase_id' => $phase_list['phase_id'],
                            'phase_name' => $phase_list['phase_name']
                            ];
                        }
                    }

                    $data['title'] = 'Phase To Contract Mapping';
                    $data['total_contracts_amount'] = $total_contracts_amount;
                    $data['remaining_amount'] = '-';
                } else {
                    // Get Results to display list of phase to contracts.
                    $results = $this->projects_phases_contracts->getRowsByProjectPhaseContract($this->session->userdata('project_id'), $project_phase, $contract);
                    // Total amount of contracts.
                    $total_contracts_amount = 0;
                    $master_project = $this->projects->getRows($this->session->userdata('project_id'));
                    if (!empty($results)) {
                        foreach ($results as $result) {
                              $data['project_phase_contract_lists'][] = [
                            'project_phase_contract_id' => $result['project_phase_contract_id'],
                            'project_id' => $result['project_id'],
                            'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                            'contract' => $result['contract'],
                            'amount' => $result['amount'],
                            'start_date' => $result['contract_start_date'],
                            'end_date' => $result['contract_end_date'],
                            'description' => $result['description'],
                            'attachment' => $result['attachment'],
                            'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                            'status' => get_phase_contract_state($result['status'])
                               ];

                               // Calculate total amount of contracts.
                               $total_contracts_amount = $total_contracts_amount + $result['amount'];
                        }
                        $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                    }

                    $phase_lists = $this->projects_phases_contracts->getPhaseList();
                    if (!empty($phase_lists)) {
                        foreach ($phase_lists as $phase_list) {
                              $data['phases'][] = [
                            'phase_id' => $phase_list['phase_id'],
                            'phase_name' => $phase_list['phase_name']
                              ];
                        }
                    }

                    $data['title'] = 'Phase To Contract Mapping';
                    $data['total_contracts_amount'] = $total_contracts_amount;
                    $data['remaining_amount'] = '-';
                }
            } else {
                if($project_phase && $amount) {
                      // Get Results to display list of phase to contracts.
                      $results = $this->projects_phases_contracts->getRowsByProjectPhaseAmount($this->session->userdata('project_id'), $project_phase, $amount);
                      // Total amount of contracts.
                      $total_contracts_amount = 0;

                      $master_project = $this->projects->getRows($this->session->userdata('project_id'));
                    if (!empty($results)) {
                        foreach ($results as $result) {
                               $data['project_phase_contract_lists'][] = [
                                 'project_phase_contract_id' => $result['project_phase_contract_id'],
                                 'project_id' => $result['project_id'],
                                 'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                                 'contract' => $result['contract'],
                                 'amount' => $result['amount'],
                                 'start_date' => $result['contract_start_date'],
                                 'end_date' => $result['contract_end_date'],
                                 'description' => $result['description'],
                                 'attachment' => $result['attachment'],
                                 'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                                 'status' => get_phase_contract_state($result['status'])
                                ];

                                // Calculate total amount of contracts.
                                $total_contracts_amount = $total_contracts_amount + $result['amount'];
                        }
                        $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                    }

                      $phase_lists = $this->projects_phases_contracts->getPhaseList();
                    if (!empty($phase_lists)) {
                        foreach ($phase_lists as $phase_list) {
                            $data['phases'][] = [
                            'phase_id' => $phase_list['phase_id'],
                            'phase_name' => $phase_list['phase_name']
                            ];
                        }
                    }

                         $data['title'] = 'Phase To Contract Mapping';
                         $data['total_contracts_amount'] = $total_contracts_amount;
                         $data['remaining_amount'] = '-';
                } else {
                    if($project_phase) {
                        // Get Results to display list of phase to contracts.
                        $results = $this->projects_phases_contracts->getRowsByProjectPhase($this->session->userdata('project_id'), $project_phase);
                        // Total amount of contracts.
                        $total_contracts_amount = 0;

                        $master_project = $this->projects->getRows($this->session->userdata('project_id'));
                        if (!empty($results)) {
                            foreach ($results as $result) {
                                $data['project_phase_contract_lists'][] = [
                                'project_phase_contract_id' => $result['project_phase_contract_id'],
                                'project_id' => $result['project_id'],
                                'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                                'contract' => $result['contract'],
                                'amount' => $result['amount'],
                                'start_date' => $result['contract_start_date'],
                                'end_date' => $result['contract_end_date'],
                                'description' => $result['description'],
                                'attachment' => $result['attachment'],
                                'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                                'status' => get_phase_contract_state($result['status'])
                                ];

                                // Calculate total amount of contracts.
                                $total_contracts_amount = $total_contracts_amount + $result['amount'];
                            }
                            $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                        }

                        $phase_lists = $this->projects_phases_contracts->getPhaseList();
                        if (!empty($phase_lists)) {
                            foreach ($phase_lists as $phase_list) {
                                $data['phases'][] = [
                                'phase_id' => $phase_list['phase_id'],
                                'phase_name' => $phase_list['phase_name']
                                ];
                            }
                        }

                        $data['title'] = 'Phase To Contract Mapping';
                        $data['total_contracts_amount'] = $total_contracts_amount;
                        $data['remaining_amount'] = '-';
                    }

                    if ($amount) {
                        // Get Results to display list of phase to contracts.
                        $results = $this->projects_phases_contracts->getRowsByProjectAmount($this->session->userdata('project_id'), $amount);
                        // Total amount of contracts.
                        $total_contracts_amount = 0;

                        $master_project = $this->projects->getRows($this->session->userdata('project_id'));
                        if (!empty($results)) {
                            foreach ($results as $result) {
                                $data['project_phase_contract_lists'][] = [
                                'project_phase_contract_id' => $result['project_phase_contract_id'],
                                'project_id' => $result['project_id'],
                                'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                                'contract' => $result['contract'],
                                'amount' => $result['amount'],
                                'start_date' => $result['contract_start_date'],
                                'end_date' => $result['contract_end_date'],
                                'description' => $result['description'],
                                'attachment' => $result['attachment'],
                                'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                                'status' => get_phase_contract_state($result['status'])
                                ];

                                // Calculate total amount of contracts.
                                $total_contracts_amount = $total_contracts_amount + $result['amount'];
                            }
                            $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                        }

                        $phase_lists = $this->projects_phases_contracts->getPhaseList();
                        if (!empty($phase_lists)) {
                            foreach ($phase_lists as $phase_list) {
                                $data['phases'][] = [
                                'phase_id' => $phase_list['phase_id'],
                                'phase_name' => $phase_list['phase_name']
                                ];
                            }
                        }

                        $data['title'] = 'Phase To Contract Mapping';
                        $data['total_contracts_amount'] = $total_contracts_amount;
                        $data['remaining_amount'] = '-';
                    }
                }
            }

            // No input received.
            if(!$project_phase && !$contract && !$amount) {
                redirect('/project_phase_contract');
            }

            //load the list page view
            $this->load->view('common/header', $data);
            $this->load->view('mapping/project_phase_contract/add', $data);
            $this->load->view('common/footer');
        }
    }

    /**
     * Edit handler of Project Phase Contracts.
     *
     * @param int $id
     *  Project Phase Contract ID.
     */
    public function edit($id)
    {
        $data = [];
        //get messages from the session
        if ($this->session->userdata('success_msg')) {
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }
        if ($this->session->userdata('error_msg')) {
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }

        if (!$this->session->userdata('project_id')) {
            redirect('/project');
        } else {
            $data["project_name"] = $this->projects_phases_contracts->getProjectName($this->session->userdata('project_id'));
        }
        if($this->input->post('postSubmit')) {
            $data = $this->input->post();
            $recoverdata = $this->projects_phases_contracts->getRows($id);

            if (!is_numeric($state = set_phase_contract_state($data['start_date'], $data['end_date']))) {
                $this->session->set_userdata('error_msg', $state);
                redirect('project_phase_contract/edit/' . $id);
            }

            if ($val = validate_project_phase_contract($this->session->userdata('project_id'), $data, $id, $this->projects_phases_contracts, $this->projects, $this->financial_plans)) {
                if ($val == 2) { // Start date is before project start date.
                    $this->session->set_userdata('error_msg', 'Contract Start date can not before Project start date.');
                    redirect('/project_phase_contract/edit/' . $id);
                }
                if ($val == 3) { // End date is beyond project end date.
                    $this->session->set_userdata('error_msg', 'Contract End date can not beyond project end date.');
                    redirect('/project_phase_contract/edit/' . $id);
                }

                if ($val == 4) { // Contract amount is less than already planned amount.
                    $this->session->set_userdata('error_msg', 'Contract amount is less than already allocated amount.');
                    redirect('/project_phase_contract/edit/' . $id);
                }

                if($_FILES['attachment']['size'] != 0) {
                    $_FILES['userFile']['name'] = $_FILES['attachment']['name'];
                    $_FILES['userFile']['type'] = $_FILES['attachment']['type'];
                    $_FILES['userFile']['tmp_name'] = $_FILES['attachment']['tmp_name'];
                    $_FILES['userFile']['error'] = $_FILES['attachment']['error'];
                    $_FILES['userFile']['size'] = $_FILES['attachment']['size'];
                    $uploadPath = 'uploads/';
                    $config['upload_path'] = $uploadPath;
                    $config['allowed_types'] = 'pdf|jpg|jpeg|JPG|PNG|png';
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('userFile')) {
                        $fileData = $this->upload->data();
                        $data['file_name'] = $fileData['file_name'];
                    }

                    $postData = [
                    'phase_id' => $recoverdata['phase_id'],
                    'contract' => $recoverdata['contract'],
                    'amount' => $data['amount'],
                    'contract_start_date' => $data['start_date'],
                    'contract_end_date' => $data['end_date'],
                    'description' => $data['description'],
                    'attachment' => $data['file_name'],
                    'status' => $state,
                    'modified_date' => date("m/d/Y G:i:s")
                    ];

                    $update = $this->projects_phases_contracts->update($postData, $id);
                    //if ($recoverdata['phase_id']!=$data['phase_id'] || $recoverdata['contract_name']!=$data['contract_name'] || $recoverdata['description']!=$data['description'] || $recoverdata['attachment']!=$data['file_name']) {
                      $postDataVC = [
                    'project_id' => $this->session->userdata('project_id'),
                    'project_phase_contract_id' => $id,
                    'phase_id' => $recoverdata['phase_id'],
                    'contract' => $recoverdata['contract'],
                    'amount' => $data['amount'],
                    'contract_start_date' => $data['start_date'],
                    'contract_end_date' => $data['end_date'],
                    'description' => $data['description'],
                    'attachment' => $data['file_name'],
                    'status' => $state,
                    'date_added' => date("m/d/Y G:i:s")
                      ];

                      $insertvc = $this->projects_phases_contracts->addversioncontrol($postDataVC);
                      //  }
                } else {
                    $postData = [
                    'phase_id' => $recoverdata['phase_id'],
                    'contract' => $data['contract'],
                    'amount' => $data['amount'],
                    'contract_start_date' => $data['start_date'],
                    'contract_end_date' => $data['end_date'],
                    'description' => $data['description'],
                    'status' => $state,
                    'modified_date' => date("m/d/Y G:i:s")
                    ];

                    $update = $this->projects_phases_contracts->update($postData, $id);

                    if ($update) {
                              $postDataVC = [
                                'project_id' => $this->session->userdata('project_id'),
                                'project_phase_contract_id' => $id,
                                'phase_id' => $recoverdata['phase_id'],
                                'contract' => $data['contract'],
                                'amount' => $data['amount'],
                                'contract_start_date' => $data['start_date'],
                                'contract_end_date' => $data['end_date'],
                                'description' => $data['description'],
                                'attachment' => $recoverdata['attachment'],
                                'status' => $state,
                                'date_added' => date("m/d/Y G:i:s")
                                 ];

                                 $insertvc = $this->projects_phases_contracts->addversioncontrol($postDataVC);
                    }
                }

                if ($update) {
                    $this->session->set_userdata('success_msg', 'Phase to Contract mapping has been Updated successfully.');
                    redirect('/project_phase_contract');
                } else {
                    $data['error_msg'] = 'Some problems occurred, please try again.';
                }
            } else {
                if (!$this->session->userdata('project_id')) {
                    redirect('/project');
                } else {
                    $data["project_name"] = $this->projects_phases_contracts->getProjectName($this->session->userdata('project_id'));
                }
                $data['error_msg'] = 'Total amount can not exceed project total cost.';
            }
        }

        if (!$this->input->post('postFilter')) {
            $postData = $this->projects_phases_contracts->getRows($id);
            $data['title'] = 'Project Phase Contract Edit';
            // Phase Contract List
            $results = $this->projects_phases_contracts->getRows();

            // Total contract amount.
            $total_contracts_amount = 0;
            foreach ($results as $result) {
                 $data['project_phase_contract_lists'][] = [
                   'project_phase_contract_id' => $result['project_phase_contract_id'],
                   'project_id' => $result['project_id'],
                   'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                   'contract' => $result['contract'],
                   'amount' => $result['amount'],
                   'start_date' => $result['contract_start_date'],
                   'end_date' => $result['contract_end_date'],
                   'description' => $result['description'],
                   'attachment' => $result['attachment'],
                   'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                   'status' => get_phase_contract_state($result['status'])
                 ];

                 // Calculate total amount of contracts.
                 $total_contracts_amount = $total_contracts_amount + $result['amount'];
            }

            $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
            // Phase List
            $phase_lists = $this->projects_phases_contracts->getPhaseList();

            if (!empty($phase_lists)) {
                foreach ($phase_lists as $phase_list) {
                    $data['phases'][] = [
                    'phase_id'      => $phase_list['phase_id'],
                    'phase_name'    => $phase_list['phase_name']
                    ];
                }
            }

            $data['post'] = $postData;
            $data['total_contracts_amount'] = $total_contracts_amount;

            //load the edit page view
            $this->load->view('common/header', $data);
            $this->load->view('mapping/project_phase_contract/edit', $data);
            $this->load->view('common/footer');
        } else {
            $project_phase = $this->input->post('phasefilter');
            $contract = $this->input->post('contractfilter');
            $amount = $this->input->post('amountfilter');

            if($project_phase && $contract) {
                if($amount) {
                    $postData = $this->projects_phases_contracts->getRows($id);
                    $data['title'] = 'Project Phase Contract Edit';
                    // Phase Contract List
                    $results = $this->projects_phases_contracts->getRowsByProjectPhaseContractAmount($this->session->userdata('project_id'), $project_phase, $contract, $amount);

                    // Total contract amount.
                    $total_contracts_amount = 0;
                    if (!empty($results)) {
                        foreach ($results as $result) {
                            $data['project_phase_contract_lists'][] = [
                            'project_phase_contract_id' => $result['project_phase_contract_id'],
                            'project_id' => $result['project_id'],
                            'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                            'contract' => $result['contract'],
                            'amount' => $result['amount'],
                            'start_date' => $result['contract_start_date'],
                            'end_date' => $result['contract_end_date'],
                            'description' => $result['description'],
                            'attachment' => $result['attachment'],
                            'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                            'status' => get_phase_contract_state($result['status'])
                            ];

                            // Calculate total amount of contracts.
                            $total_contracts_amount = $total_contracts_amount + $result['amount'];
                        }

                          $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                    }



                    // Phase List
                    $phase_lists = $this->projects_phases_contracts->getPhaseList();

                    if (!empty($phase_lists)) {
                        foreach ($phase_lists as $phase_list) {
                            $data['phases'][] = [
                            'phase_id'      => $phase_list['phase_id'],
                            'phase_name'    => $phase_list['phase_name']
                            ];
                        }
                    }

                    $data['post'] = $postData;
                    $data['total_contracts_amount'] = $total_contracts_amount;
                } else {
                    $postData = $this->projects_phases_contracts->getRows($id);
                    $data['title'] = 'Project Phase Contract Edit';
                    // Phase Contract List
                    $results = $this->projects_phases_contracts->getRowsByProjectPhaseContract($this->session->userdata('project_id'), $project_phase, $contract);

                    // Total contract amount.
                    $total_contracts_amount = 0;
                    if (!empty($results)) {
                        foreach ($results as $result) {
                            $data['project_phase_contract_lists'][] = [
                            'project_phase_contract_id' => $result['project_phase_contract_id'],
                            'project_id' => $result['project_id'],
                            'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                            'contract' => $result['contract'],
                            'amount' => $result['amount'],
                            'start_date' => $result['contract_start_date'],
                            'end_date' => $result['contract_end_date'],
                            'description' => $result['description'],
                            'attachment' => $result['attachment'],
                            'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                            'status' => get_phase_contract_state($result['status'])
                            ];

                            // Calculate total amount of contracts.
                            $total_contracts_amount = $total_contracts_amount + $result['amount'];
                        }

                               $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                    }

                    // Phase List
                    $phase_lists = $this->projects_phases_contracts->getPhaseList();

                    if (!empty($phase_lists)) {
                        foreach ($phase_lists as $phase_list) {
                            $data['phases'][] = [
                            'phase_id'      => $phase_list['phase_id'],
                            'phase_name'    => $phase_list['phase_name']
                            ];
                        }
                    }

                    $data['post'] = $postData;
                    $data['total_contracts_amount'] = $total_contracts_amount;
                }
            } else {
                if($project_phase && $amount) {
                    $postData = $this->projects_phases_contracts->getRows($id);
                    $data['title'] = 'Project Phase Contract Edit';
                    // Phase Contract List
                    $results = $this->projects_phases_contracts->getRowsByProjectPhaseAmount($this->session->userdata('project_id'), $project_phase, $amount);

                    // Total contract amount.
                    $total_contracts_amount = 0;
                    if (!empty($results)) {
                        foreach ($results as $result) {
                            $data['project_phase_contract_lists'][] = [
                            'project_phase_contract_id' => $result['project_phase_contract_id'],
                            'project_id' => $result['project_id'],
                            'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                            'contract' => $result['contract'],
                            'amount' => $result['amount'],
                            'start_date' => $result['contract_start_date'],
                            'end_date' => $result['contract_end_date'],
                            'description' => $result['description'],
                            'attachment' => $result['attachment'],
                            'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                            'status' => get_phase_contract_state($result['status'])
                            ];

                            // Calculate total amount of contracts.
                            $total_contracts_amount = $total_contracts_amount + $result['amount'];
                        }

                        $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                    }

                    // Phase List
                    $phase_lists = $this->projects_phases_contracts->getPhaseList();

                    if (!empty($phase_lists)) {
                        foreach ($phase_lists as $phase_list) {
                            $data['phases'][] = [
                            'phase_id'      => $phase_list['phase_id'],
                            'phase_name'    => $phase_list['phase_name']
                            ];
                        }
                    }

                    $data['post'] = $postData;
                    $data['total_contracts_amount'] = $total_contracts_amount;
                } else {
                    if($project_phase) {
                              $postData = $this->projects_phases_contracts->getRows($id);
                              $data['title'] = 'Project Phase Contract Edit';
                              // Phase Contract List
                              $results = $this->projects_phases_contracts->getRowsByProjectPhase($this->session->userdata('project_id'), $project_phase);

                              // Total contract amount.
                              $total_contracts_amount = 0;
                        if (!empty($results)) {
                            foreach ($results as $result) {
                                $data['project_phase_contract_lists'][] = [
                                 'project_phase_contract_id' => $result['project_phase_contract_id'],
                                 'project_id' => $result['project_id'],
                                 'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                                 'contract' => $result['contract'],
                                 'amount' => $result['amount'],
                                 'start_date' => $result['contract_start_date'],
                                 'end_date' => $result['contract_end_date'],
                                 'description' => $result['description'],
                                 'attachment' => $result['attachment'],
                                 'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                                 'status' => get_phase_contract_state($result['status'])
                                ];

                                // Calculate total amount of contracts.
                                $total_contracts_amount = $total_contracts_amount + $result['amount'];
                            }

                            $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                        }

                        // Phase List
                        $phase_lists = $this->projects_phases_contracts->getPhaseList();

                        if (!empty($phase_lists)) {
                            foreach ($phase_lists as $phase_list) {
                                $data['phases'][] = [
                                'phase_id'      => $phase_list['phase_id'],
                                'phase_name'    => $phase_list['phase_name']
                                ];
                            }
                        }

                        $data['post'] = $postData;
                        $data['total_contracts_amount'] = $total_contracts_amount;
                    }

                    if ($amount) {
                            $postData = $this->projects_phases_contracts->getRows($id);
                            $data['title'] = 'Project Phase Contract Edit';
                            // Phase Contract List
                            $results = $this->projects_phases_contracts->getRowsByProjectAmount($this->session->userdata('project_id'), $amount);

                            // Total contract amount.
                            $total_contracts_amount = 0;
                        if (!empty($results)) {
                            foreach ($results as $result) {
                                         $data['project_phase_contract_lists'][] = [
                                           'project_phase_contract_id' => $result['project_phase_contract_id'],
                                           'project_id' => $result['project_id'],
                                           'phase_id' => $this->projects_phases_contracts->getPhaseName($result['phase_id']),
                                           'contract' => $result['contract'],
                                           'amount' => $result['amount'],
                                           'start_date' => $result['contract_start_date'],
                                           'end_date' => $result['contract_end_date'],
                                           'description' => $result['description'],
                                           'attachment' => $result['attachment'],
                                           'weight' => $this->financial_plans->getPhaseWeight($result['phase_id']),
                                           'status' => get_phase_contract_state($result['status'])
                                         ];

                                         // Calculate total amount of contracts.
                                         $total_contracts_amount = $total_contracts_amount + $result['amount'];
                            }

                            $data['project_phase_contract_lists'] = sort_project_phase_contract_list($data['project_phase_contract_lists']);
                        }

                            // Phase List
                            $phase_lists = $this->projects_phases_contracts->getPhaseList();

                        if (!empty($phase_lists)) {
                            foreach ($phase_lists as $phase_list) {
                                $data['phases'][] = [
                                'phase_id'      => $phase_list['phase_id'],
                                'phase_name'    => $phase_list['phase_name']
                                ];
                            }
                        }

                             $data['post'] = $postData;
                             $data['total_contracts_amount'] = $total_contracts_amount;
                    }
                }
            }

                        // No input received.
            if(!$project_phase && !$contract && !$amount) {
                redirect('/project_phase_contract');
            }

                        //load the edit page view
                        $this->load->view('common/header', $data);
                        $this->load->view('mapping/project_phase_contract/edit', $data);
                        $this->load->view('common/footer');
        }
    }

    /**
     * [disable description]
     *
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function disable($id)
    {
        $projects_phases_contracts = $this->projects_phases_contracts->getRows($id);
        if (!empty($projects_phases_contracts)) {
            $status = $projects_phases_contracts['status'];
            if ($status != 0) {
                $update = [
                  'status' => 0,
                  'modified_date' => date("m/d/Y G:i:s"),
                ];

                $result = $this->projects_phases_contracts->update($update, $id);
                if ($result) {
                    $this->session->userdata('success_msg', 'Contract Closed Successfully');
                    redirect('project_phase_contract');
                } else {
                    $this->session->userdata('error_msg', 'Unexpected Error Encountered.');
                    redirect('project_phase_contract');
                }
            }
        }
    }

    public function delete($id)
    {
        //check whether post id is not empty
        if($id) {
            if (!$this->financial_plans->checkPhaseContractId($id, $this->session->userdata('project_id'))) {
                //delete post
                $delete = $this->projects_phases_contracts->delete($id);
                if($delete) {
                    $this->session->set_userdata('success_msg', 'Phase to Contract mapping has been removed successfully.');
                }
            } else {
                $this->session->set_userdata('error_msg', 'Financial Plan is already done for this contract, please deallocate funds');
            }
        }

        redirect('/project_phase_contract');
    }

    public function check()
    {
        $contract = $this->input->post('contract_name');
        $phase_id = $this->input->post('phase_id');
        $result = $this->projects_phases_contracts->check($contract, $phase_id, $this->session->userdata('project_id'));

        echo json_encode($result);
    }

    public function checkedit()
    {
        $contract_name = $this->input->post('contract_name');

        $phase_id = $this->input->post('phase_id');

        $id = $this->input->post('id');

        $result = $this->projects_phases_contracts->checkedit($contract_name, $phase_id, $id, $this->session->userdata('project_id'));

        echo json_encode($result);
    }

    public function history()
    {
        $phase_contract_id = $this->input->post('phase_contract_id');
        $results = $this->projects_phases_contracts->history($phase_contract_id);
        if($results) {
            foreach ($results as $result) {
                $data['history'][] = [
                'version_control_id' => $result['version_control_id'],
                'project_phase_contract_id' => $result['project_phase_contract_id'],
                'project_id' => $result['project_id'],
                'phase_name' => $this->financial_plans->getPhaseName($result['phase_id']),
                'contract' => $result['contract'],
                'amount' => $result['amount'],
                'description' => $result['description'],
                'attachment' => $result['attachment'],
                'date_added' => $result['date_added'],
                ];
            }
        } else {
            $data['history'] = "";
        }

        echo json_encode($data['history']);
    }
}
