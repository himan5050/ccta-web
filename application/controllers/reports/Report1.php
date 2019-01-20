<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

class Report1 extends CI_Controller
{

    private $root_upload_import_path = "uploads/report1/";

    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('excel');
        $this->load->helper('url');
        $this->load->model('mapping/financial_plans');
        $this->load->model('reports/master_report');
    }



    public function index()
    {
        $data = array();
        $data['funds'] = [];
        $data['phase_estimated_costs'] = [];
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
            $data["project_name"] = $this->financial_plans->getProjectName($this->session->userdata('project_id'));
        }

        /*-------------------Table 1------------------------*/
        /*$resultphases = $this->master_report->getDistinctProjectPhases($this->session->userdata('project_id'));
        $resultfunds = $this->master_report->getDistinctProjectFunds($this->session->userdata('project_id'));*/

        $resultphases = $this->master_report->getPhaseList();
        $resultfunds = $this->master_report->getFundListByProject($this->session->userdata('project_id'));
        //echo '<PRE>';
        //print_r($resultfunds); exit;
        $data['phaseTotal'] = 0;
        if (!empty($resultphases)) {
            foreach ($resultphases as $resultphase) {
                $data['phase_estimated_costs'][] = array(
                  'phase_name'                    => $this->financial_plans->getPhaseName($resultphase['Phase_id']),
                  'amount'                        => $this->getestimatecost($resultphase['Phase_id'], $this->session->userdata('project_id')),
                  'funds'                         => $this->getfunds($resultphase['Phase_id'], $this->session->userdata('project_id'))
                );
            }
        }

        if (!empty($data['phase_estimated_costs'])) {
            for($i = 0; $i<count($data['phase_estimated_costs']); $i++) {
                $data['phaseTotal'] = $data['phaseTotal'] + $data['phase_estimated_costs'][$i]['amount'];
            }
        }

        if (!empty($resultfunds)) {
            foreach ($resultfunds as $resultfund) {
                $data['funds'][] = array( 'fund_name' => $this->financial_plans->getFundName($resultfund['fund_id']) );
                $data['total_funds'][] = array( 'amount' => $this->financial_plans->getFundSubTotal($resultfund['fund_id'], $this->session->userdata('project_id')));
            }
        }

        $data['title'] = 'Report 1';

        //load the list page view
        $this->load->view('common/header', $data);
        $this->load->view('reports/report1', $data);
        $this->load->view('common/footer');
    }

    public function export()
    {
        $resultphases = $this->master_report->getPhaseList();
        $resultfunds = $this->master_report->getFundListByProject($this->session->userdata('project_id'));
        $export['phaseTotal'] = 0;
        if (!empty($resultfunds)) {
            foreach ($resultfunds as $resultfund) {
                $export['funds'][] = array( 'fund_name' => $this->financial_plans->getFundName($resultfund['fund_id']) );
                $export['total_funds'][] = array( 'amount' => $this->financial_plans->getFundSubTotal($resultfund['fund_id'], $this->session->userdata('project_id')));
            }
        }

        if (!empty($resultphases)) {
            foreach ($resultphases as $resultphase) {
                $export['phase_estimated_costs'][] = [
                'phase_name'                    => $this->financial_plans->getPhaseName($resultphase['Phase_id']),
                'amount'                        => $this->getestimatecost($resultphase['Phase_id'], $this->session->userdata('project_id')),
                'funds'                         => $this->getfunds($resultphase['Phase_id'], $this->session->userdata('project_id'))
                ];
            }
        }

        if (!empty($export['phase_estimated_costs'])) {
            for($i = 0; $i<count($export['phase_estimated_costs']); $i++) {
                $export['phaseTotal'] = $export['phaseTotal'] + $export['phase_estimated_costs'][$i]['amount'];
            }
        }

        // create file name
        $fileName = 'report1-'.time().'.xlsx';
        // load excel library
        $this->load->library('excel');
        //$empInfo = $this->export->employeeList();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Project Component');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Costs');
        for ($i=0;$i<count($export['funds']);$i++) {
            $char = $this->getChar($i);
            $objPHPExcel->getActiveSheet()->SetCellValue($char.'1', $export['funds'][$i]['fund_name']);
        }
        // set Row.
        $rowCount = 2;
        if (!empty($export['phase_estimated_costs'])) {
            foreach ($export['phase_estimated_costs'] as $estimated_cost) {
                if ($estimated_cost['amount']) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $estimated_cost['phase_name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $estimated_cost['amount'])));
                    // funds.
                    for ($i=0;$i<count($estimated_cost['funds']);$i++) {
                        $char = $this->getChar($i);
                        $objPHPExcel->getActiveSheet()->SetCellValue($char . $rowCount, '$ '. number_format(preg_replace('/\s+/', '', $estimated_cost['funds'][$i])));
                    }

                    $rowCount++;
                }
            }
        }
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'SUM');
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format(preg_replace('/\s+/', '', $export['phaseTotal'])));
        for ($i=0;$i<count($export['total_funds']);$i++) {
            $char = $this->getChar($i);
            $objPHPExcel->getActiveSheet()->SetCellValue($char. $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $export['total_funds'][$i]['amount'])));
        }

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $path = $this->root_upload_import_path;
        $http_path = base_url("uploads/report1/");
        $objWriter->save($path . $fileName);
        // download file
        header("Content-Type: application/vnd.ms-excel");
        redirect($http_path . $fileName);
    }

    function getestimatecost($phase_id, $project_id)
    {
        $total_cost = 0;
        $phase_contract_ids = $this->master_report->getProjectPhaseContractId($phase_id, $project_id);

        foreach ($phase_contract_ids as $phase_contract_id) {
            $total_cost = $total_cost + ($this->master_report->getamount($project_id, $phase_contract_id['project_phase_contract_id']));
        }
        return $total_cost;
    }

    /*----------------------table 2 Start-----------------------------*/

    function getfunds($phase_id, $project_id)
    {
        $total_cost[] = 0;
        /*$resultfunds = $this->master_report->getDistinctProjectFunds($this->session->userdata('project_id'));*/
        $resultfunds = $this->master_report->getFundListByProject($project_id);
        for($i=0; $i<count($resultfunds); $i++) {
            $total_cost[$i] = $this->getfundPhase($project_id, $phase_id, $resultfunds[$i]['fund_id']);
        }

        return $total_cost;
    }

    function getfundPhase($project_id, $phase_id, $fund_id )
    {
        $total = 0;
        $phase_contract_ids = $this->master_report->getProjectPhaseContractId($phase_id, $project_id);
        for($i=0; $i<count($phase_contract_ids); $i++) {
            $total = $total + $this->master_report->getamount2($project_id, $phase_contract_ids[$i]['project_phase_contract_id'], $fund_id);
        }

        return $total;
    }

    /*----------------------table 2 End-----------------------------*/

    function getChar($i)
    {
        switch ($i) {
        case 0:
            return 'C';
        case 1:
            return 'D';
        case 2:
            return 'E';
        case 3:
            return 'F';
        case 4:
            return 'G';
        case 5:
            return 'H';
        case 6:
            return 'I';
        case 7:
            return 'J';
        case 8:
            return 'K';
        default:
            return 'Z';
        }
    }
}
