<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

class Report extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->model('mapping/financial_plans');
        $this->load->model('reports/master_report');
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

        $resultyears = $this->master_report->getDistinctYears();
        $data['years'][] = array( 'financial_years' => '');
        $total_years = [];
        if (!empty($resultyears)) {
            unset($data['years']);
            foreach ($resultyears as $resultyear) {
                if ($resultyear['financial_years']) {
                    $data['years'][] = array( 'financial_years' => $resultyear['financial_years']);
                    $total_years[] = 0.0;
                }
            }
        }

        /*-------------------Table 2 Start------------------------*/
        $resultfunds = $this->master_report->getFundsList();
        $data["fundLists"] = $resultfunds;
        if($this->input->post('postSubmit')) {
            $fund_id = $this->input->post('fund_id'); /*print_r($fund_id);exit();*/
            $data['fund_name'] = $this->financial_plans->getFundName($fund_id);
            $resultphases = $this->master_report->getProjectList();
            foreach ($resultphases as $resultphase) {
                $data['project_wise_costs'][] = array(
                    'project_name'                    => $this->financial_plans->getProjectName($resultphase['Project_id']),
                    'prior'                         => $this->master_report->getmasterPriorAmount($resultphase['Project_id'], $fund_id),
                    'expended'                      => $this->master_report->getmasterexpendedAmount($resultphase['Project_id'], $fund_id),
                    'remaining'                     => $this->master_report->getmasterremainingAmount($resultphase['Project_id'], $fund_id),
                    'funds'                         => $this->getfunds($resultphase['Project_id'], $fund_id),
                    'sum'                           => $this->getfundsum($this->master_report->getmasterPriorAmount($resultphase['Project_id'], $fund_id), $this->master_report->getmasterexpendedAmount($resultphase['Project_id'], $fund_id), $this->master_report->getmasterremainingAmount($resultphase['Project_id'], $fund_id), $this->getfunds($resultphase['Project_id'], $fund_id)),
                    'flag'                          => 0, //$this->checksum($this->master_report->getPropbaseAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->master_report->getPriorAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->master_report->getexpendedAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->master_report->getremainingAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->getfunds2($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id))
                );
            }

            $data['total_prior'] = 0.0;
            $data['total_expended'] = 0.0;
            $data['total_remaining'] = 0.0;
            $data['total_sum'] = 0.0;
            if (!empty($data['project_wise_costs'])) {
                foreach ($data['project_wise_costs'] as $project_wise_cost) {
                    $data['total_prior'] = $data['total_prior'] + $project_wise_cost['prior'];
                    $data['total_expended'] = $data['total_expended'] + $project_wise_cost['expended'];
                    $data['total_remaining'] = $data['total_remaining'] + $project_wise_cost['remaining'];
                    $data['total_sum'] = $data['total_sum'] + $project_wise_cost['sum'];
                    if (!empty($project_wise_cost['funds'])) {
                        for($i=0;$i<count($project_wise_cost['funds']);$i++) {
                            $total_years[$i] = $total_years[$i] + $project_wise_cost['funds'][$i];
                        }
                    }
                }
            } else {
                $data['error_msg'] = 'No Project Found.';
            }

            $data['total_years'] = $total_years;
        }
        /*-------------------Table 2 End--------------------------*/

        $data['title'] = 'Fund Source Report';

        //    echo '<PRE>';
        //    print_r($data); exit;



        //load the list page view
        $this->load->view('common/header', $data);
        $this->load->view('reports/master_reports/report', $data);
        $this->load->view('common/footer');
    }

    function getfunds($project_id, $fund_id)
    {
        $total_cost = [];
        $resultyears = $this->master_report->getDistinctYears();
        if (!empty($resultyears)) {
            for($i=0; $i<count($resultyears); $i++)
            {
                $total_cost[$i] = $this->getmasterfund($project_id, $resultyears[$i]['financial_years'], $fund_id);
            }
        }

        return $total_cost;
    }

    function getmasterfund($project_id, $financial_year, $fund_id)
    {
        $total = 0;
        $financial_ids = $this->master_report->getProjectFundFinancialIDs($project_id, $fund_id);
        for($i=0; $i<count($financial_ids); $i++) {
            if($financial_ids[$i]['financial_id']) {
                $total = $total + $this->master_report->getamount3($project_id, $financial_ids[$i]['financial_id']['finance'], $financial_year);
            }
        }
        return $total;
    }

    function getfundsum($prior, $expended, $remaining, $funds)
    {
        $fundtotal = 0;
        $sum = 0;
        for($i=0; $i<count($funds); $i++) {
            $fundtotal = $fundtotal + $funds[$i];
        }
        $sum = $fundtotal + $prior + $expended + $remaining;
        return $sum;
    }

    function checksum($prop_base, $prior, $expended, $remaining, $funds)
    {
        $fundtotal = 0;
        $sum = 0;
        $flag  = 0;
        for($i=0; $i<count($funds); $i++) {
            $fundtotal = $fundtotal + $funds[$i];
        }
        $sum = $fundtotal + $prior + $expended + $remaining;
        return 0;
        if ($sum > $prop_base  ) {
            return 1;
        }
        else{
            return 0;
        }
    }

}
