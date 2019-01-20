<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

class Report2 extends CI_Controller
{

    private $root_upload_import_path = "uploads/report2/";

    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('excel');
        $this->load->model('mapping/financial_plans');
        $this->load->model('reports/master_report');
    }

    public function index()
    {
        $data = array();
        $data['phase_estimated_costs'] = [];
        $postData = array();

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
            $data["project_name"] = $this->financial_plans->getProjectName($this->session->userdata('project_id'));
        }

        /*-------------------Table 1 start------------------------*/
        $resultyears = $this->master_report->getDistinctYears($this->session->userdata('project_id'));
        $resultphases = $this->master_report->getPhaseList();
        foreach ($resultphases as $resultphase) {
            $data['phase_estimated_costs'][] = array(
                'phase_name'                    => $this->financial_plans->getPhaseName($resultphase['Phase_id']),
                'prop_base'                     => $this->master_report->getPropbaseAmount($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'prior'                         => $this->master_report->getPriorAmount($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'expended'                      => $this->master_report->getexpendedAmount($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'remaining'                     => $this->master_report->getremainingAmount($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'funds'                         => $this->getfunds($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'sum'                           => $this->getfundsum($this->master_report->getPriorAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getexpendedAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getremainingAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->getfunds($resultphase['Phase_id'], $this->session->userdata('project_id'))),
                'flag'                          => $this->checksum($this->master_report->getPropbaseAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getPriorAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getexpendedAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getremainingAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->getfunds($resultphase['Phase_id'], $this->session->userdata('project_id')))
            );
        }

        //print_r($data['phase_estimated_costs']);exit();
        $data['years'][] = array( 'financial_years' => '');
        if (!empty($resultyears)) {
            unset($data['years']);
            foreach ($resultyears as $resultyear) {
                if ($resultyear['financial_years']) {
                    $data['years'][] = array( 'financial_years' => $resultyear['financial_years']);
                }
            }
        }

        // Get subtotal of each column.
        $data['totals'] = $this->getSubtotal($data);

        /*-------------------Table 1 end--------------------------*/

        /*-------------------Table 2 Start------------------------*/

        $resultfunds = $this->master_report->getFundsList();

        $data["fundLists"] = $resultfunds;

        if($this->input->post('postSubmit')) {
            $fund_id = $this->input->post('fund_id'); /*print_r($fund_id);exit();*/

            $data['fund_name'] = $this->financial_plans->getFundName($fund_id);

            $resultphases = $this->master_report->getPhaseList();

            foreach ($resultphases as $resultphase) {
                $data['phase_wise_costs'][] = array(
                    'phase_name'                    => $this->financial_plans->getPhaseName($resultphase['Phase_id']),
                    'prop_base'                     => $this->master_report->getPropbaseAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id),
                    'prior'                         => $this->master_report->getPriorAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id),
                    'expended'                      => $this->master_report->getexpendedAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id),
                    'remaining'                     => $this->master_report->getremainingAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id),
                    'funds'                         => $this->getfunds2($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id),
                    'sum'                           => $this->getfundsum($this->master_report->getPriorAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->master_report->getexpendedAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->master_report->getremainingAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->getfunds2($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id)),
                    'flag'                          => $this->checksum($this->master_report->getPropbaseAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->master_report->getPriorAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->master_report->getexpendedAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->master_report->getremainingAmount1($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id), $this->getfunds2($resultphase['Phase_id'], $this->session->userdata('project_id'), $fund_id))
                );
            }

            // Get subtotal of each column.
            $data['fund_totals'] = $this->getFundSubtotal($data);
        }


        /*-------------------Table 2 End--------------------------*/

        $data['title'] = 'Report 2';

        //load the list page view
        $this->load->view('common/header', $data);
        $this->load->view('reports/report2', $data);
        $this->load->view('common/footer');
    }


    public function export()
    {
        $resultyears = $this->master_report->getDistinctYears($this->session->userdata('project_id'));
        $resultphases = $this->master_report->getPhaseList();
        foreach ($resultphases as $resultphase) {
            $export['phase_estimated_costs'][] = array(
                'phase_name'                    => $this->financial_plans->getPhaseName($resultphase['Phase_id']),
                'prop_base'                     => $this->master_report->getPropbaseAmount($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'prior'                         => $this->master_report->getPriorAmount($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'expended'                      => $this->master_report->getexpendedAmount($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'remaining'                     => $this->master_report->getremainingAmount($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'funds'                         => $this->getfunds($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'sum'                           => $this->getfundsum($this->master_report->getPriorAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getexpendedAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getremainingAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->getfunds($resultphase['Phase_id'], $this->session->userdata('project_id'))),
                'flag'                          => $this->checksum($this->master_report->getPropbaseAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getPriorAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getexpendedAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->master_report->getremainingAmount($resultphase['Phase_id'], $this->session->userdata('project_id')), $this->getfunds($resultphase['Phase_id'], $this->session->userdata('project_id')))
            );
        }

        //print_r($data['phase_estimated_costs']);exit();
        $export['years'][] = array( 'financial_years' => '');
        if (!empty($resultyears)) {
            unset($export['years']);
            foreach ($resultyears as $resultyear) {
                if ($resultyear['financial_years']) {
                    $export['years'][] = array( 'financial_years' => $resultyear['financial_years']);
                }
            }
        }

        // Get subtotal of each column.
        $export['totals'] = $this->getSubtotal($export);

        // create file name
        $fileName = 'report2-'.time().'.xlsx';
        // load excel library
        $this->load->library('excel');
        //$empInfo = $this->export->employeeList();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Project Component');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Prior');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'FY18 expended to date');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'FY18 remaining');
        for ($i=0;$i<count($export['years']);$i++) {
            $char = $this->getChar($i);
            $objPHPExcel->getActiveSheet()->SetCellValue($char.'1', $export['years'][$i]['financial_years']);
        }
        $char = $this->getChar($i);
        $objPHPExcel->getActiveSheet()->SetCellValue($char.'1', 'SUM');
        // set Row.
        $rowCount = 2;
        if (!empty($export['phase_estimated_costs'])) {
            foreach ($export['phase_estimated_costs'] as $estimated_cost) {
                if ($estimated_cost['sum']) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $estimated_cost['phase_name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $estimated_cost['prior'])));
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $estimated_cost['expended'])));
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $estimated_cost['remaining'])));
                    // funds.
                    for ($i=0;$i<count($estimated_cost['funds']);$i++) {
                        $char = $this->getChar($i);
                        $objPHPExcel->getActiveSheet()->SetCellValue($char . $rowCount, '$ '. number_format(preg_replace('/\s+/', '', $estimated_cost['funds'][$i])));
                    }
                    $char = $this->getChar($i);
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $estimated_cost['sum'])));

                    $rowCount++;
                }
            }
        }
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'SUM');
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format(preg_replace('/\s+/', '', $export['totals']['prior'])));
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format(preg_replace('/\s+/', '', $export['totals']['expended'])));
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format(preg_replace('/\s+/', '', $export['totals']['remaining'])));
        for ($i=0;$i<count($export['totals']['funds']);$i++) {
            $char = $this->getChar($i);
            $objPHPExcel->getActiveSheet()->SetCellValue($char. $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $export['totals']['funds'][$i])));
        }
        $char = $this->getChar($i);
        $objPHPExcel->getActiveSheet()->SetCellValue($char . $rowCount, number_format(preg_replace('/\s+/', '', $export['totals']['prop_base'])));

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $path = $this->root_upload_import_path;
        $http_path = base_url("uploads/report2/");
        $objWriter->save($path . $fileName);
        // download file
        header("Content-Type: application/vnd.ms-excel");
        redirect($http_path . $fileName);
    }

    function getfunds($phase_id, $project_id)
    {
        $total_cost = [];
        $resultyears = $this->master_report->getDistinctYears($this->session->userdata('project_id'));
        if (!empty($resultyears)) {
            for($i=0; $i<count($resultyears); $i++)
            {
                $total_cost[$i] = $this->getfundPhase($project_id, $phase_id, $resultyears[$i]['financial_years']);
            }
        }

        return $total_cost;
    }

    function getfundPhase($project_id, $phase_id, $financial_year)
    {
        $total = 0;

        $financial_ids = $this->master_report->getProjectPhaseContractId1($phase_id, $project_id);

        for($i=0; $i<count($financial_ids); $i++)
        {
            $total = $total + $this->master_report->getamount3($project_id, $financial_ids[$i]['financial_id'], $financial_year);
        }

        return $total;
    }

    function getfundsum($prior, $expended, $remaining, $funds)
    {
        $fundtotal = 0;
        $sum = 0;

        for($i=0; $i<count($funds); $i++)
        {
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

        for($i=0; $i<count($funds); $i++)
        {
            $fundtotal = $fundtotal + $funds[$i];
        }

        $sum = $fundtotal + $prior + $expended + $remaining;

        if ($sum > $prop_base  ) {
            return 1;
        }
        else
        {
            return 0;
        }

    }


    function getfunds2($phase_id, $project_id, $fund_id)
    {
        $total_cost = [];
        $resultyears = $this->master_report->getDistinctYears($this->session->userdata('project_id'));

        if (!empty($resultyears)) {
            for($i=0; $i<count($resultyears); $i++)
            {
                $total_cost[$i] = $this->getfundPhase2($project_id, $phase_id, $resultyears[$i]['financial_years'], $fund_id);
            }
        }

        return $total_cost;
    }

    function getfundPhase2($project_id, $phase_id, $financial_year, $fund_id)
    {
        $total = 0;

        $financial_ids = $this->master_report->getProjectPhaseContractId2($phase_id, $project_id, $fund_id);

        for($i=0; $i<count($financial_ids); $i++)
        {
            if($financial_ids[$i]['financial_id']) {
                $total = $total + $this->master_report->getamount3($project_id, $financial_ids[$i]['financial_id'], $financial_year);
            }
        }
        return $total;
    }

    function getSubtotal($data)
    {
        $total = $this->initializeSubtotal($data);
        if (isset($data['phase_estimated_costs']) && !empty($data['phase_estimated_costs'])) {
            foreach ($data['phase_estimated_costs'] as $phase_estimated_costs) {
                $total['prop_base'] = $total['prop_base'] + $phase_estimated_costs['prop_base'];
                $total['prior'] = $total['prior'] + $phase_estimated_costs['prior'];
                $total['expended'] = $total['expended'] + $phase_estimated_costs['expended'];
                $total['remaining'] = $total['remaining'] + $phase_estimated_costs['remaining'];
                for ($i=0;$i<count($phase_estimated_costs['funds']);$i++) {
                    $total['funds'][$i] = $total['funds'][$i] + $phase_estimated_costs['funds'][$i];
                }
            }
        }

        return $total;
    }

    function initializeSubtotal($data)
    {
        $total['prop_base'] = 0;
        $total['prior'] = 0;
        $total['expended'] = 0;
        $total['remaining'] = 0;
        $total['sum'] = 0;

        for ($i=0;$i<count($data['years']);$i++) {
            $total['funds'][$i] = 0;
        }
        return $total;
    }

    function getFundSubtotal($data)
    {
        $fund_total = $this->initializeSubtotal($data);
        if (isset($data['phase_wise_costs']) && !empty($data['phase_wise_costs'])) {
            foreach ($data['phase_wise_costs'] as $phase_estimated_costs) {
                $fund_total['prop_base'] = $fund_total['prop_base'] + $phase_estimated_costs['prop_base'];
                $fund_total['prior'] = $fund_total['prior'] + $phase_estimated_costs['prior'];
                $fund_total['expended'] = $fund_total['expended'] + $phase_estimated_costs['expended'];
                $fund_total['remaining'] = $fund_total['remaining'] + $phase_estimated_costs['remaining'];
                for ($i=0;$i<count($phase_estimated_costs['funds']);$i++) {
                    $fund_total['funds'][$i] = $fund_total['funds'][$i] + $phase_estimated_costs['funds'][$i];
                }
                $fund_total['sum'] = $fund_total['sum'] + $phase_estimated_costs['sum'];
            }
        }

        return $fund_total;
    }

    function getChar($i)
    {
        switch ($i) {
        case 0:
            return 'E';
        case 1:
            return 'F';
        case 2:
            return 'G';
        case 3:
            return 'H';
        case 4:
            return 'I';
        case 5:
            return 'J';
        case 6:
            return 'K';
        case 7:
            return 'L';
        case 8:
            return 'M';
        default:
            return 'Z';
        }
    }

}
