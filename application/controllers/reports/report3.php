<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report3 extends CI_Controller {

    private $root_upload_import_path = "uploads/report3/";

    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->model('mapping/financial_plans');
        $this->load->model('reports/master_report');
    }

    public function index() {
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

        if(!$this->session->userdata('project_id')) {
            redirect('/project');
        } else {
            $data["project_name"] = $this->financial_plans->getProjectName($this->session->userdata('project_id'));
        }

        /*-------------------Table 1------------------------*/

        /*$resultphases = $this->master_report->getDistinctProjectPhases($this->session->userdata('project_id')); */

        $resultphases = $this->master_report->getPhaseList();
        $data['phaseTotal'] = 0;
        $data['fundTotal'] = 0;
        $data['phase_estimated_costs'] = [];
        $data['fund_costs'] = [];
        $data['phase_schedules'] = [];

        foreach ($resultphases as $resultphase) {
            $data['phase_estimated_costs'][] = array(
                'phase_name'                    => $this->financial_plans->getPhaseName($resultphase['Phase_id']),
                'amount'                        => $this->getestimatecost($resultphase['Phase_id'], $this->session->userdata('project_id'))
            );
        }

        if (!empty($data['phase_estimated_costs'])) {
          for($i = 0; $i<count($data['phase_estimated_costs']); $i++) {
              $data['phaseTotal'] = $data['phaseTotal'] + $data['phase_estimated_costs'][$i]['amount'];
          }
        }


        /*--------------------Table 2------------------------*/

        /*$resultfunds = $this->master_report->getDistinctFunds($this->session->userdata('project_id'));*/
        $resultfunds = $this->master_report->getFundList();

        if (!empty($resultfunds)) {
          foreach ($resultfunds as $resultfund) {
                  $data['fund_costs'][] = array(
                      'fund_name'                    => $this->financial_plans->getFundName($resultfund['fund_id']),
                      'amount'                       => $this->master_report->getFundTotal($resultfund['fund_id'], $this->session->userdata('project_id'))
                  );
          }
        }

        if (!empty($data['fund_costs'])) {
          for($i=0;$i<count($data['fund_costs']);$i++) {
            $data['fundTotal'] = $data['fundTotal'] + $data['fund_costs'][$i]['amount'];
          }
        }

        /*--------------------Table 3------------------------*/

        /*$resultSchedules = $this->master_report->getDistinctProjectPhases($this->session->userdata('project_id')); */

        $resultSchedules = $this->master_report->getPhaseList();

        if (!empty($resultSchedules)) {
          foreach ($resultSchedules as $resultSchedule)
              {
                  $data['phase_schedules'][] = array(
                      'phase_name'                    => $this->financial_plans->getPhaseName($resultSchedule['Phase_id']),
                      'start_date'                    => $this->getstart_time($resultSchedule['Phase_id'], $this->session->userdata('project_id')),
                      'end_date'                      => $this->getend_time($resultSchedule['Phase_id'], $this->session->userdata('project_id')),
                  );
              }
        }

        $data['title'] = 'Report 3';

        //echo '<PRE>';
        //print_r($data); exit;

        //load the list page view
        $this->load->view('common/header',$data);
        $this->load->view('reports/report3', $data);
        $this->load->view('common/footer');
    }

    public function export () {

      /*-------------------Table 1------------------------*/
      $resultphases = $this->master_report->getPhaseList();
      $export['phaseTotal'] = 0;
      $export['fundTotal'] = 0;
      $export['phase_estimated_costs'] = [];
      $export['fund_costs'] = [];
      $export['phase_schedules'] = [];

      foreach ($resultphases as $resultphase) {
          $export['phase_estimated_costs'][] = array(
              'phase_name'                    => $this->financial_plans->getPhaseName($resultphase['Phase_id']),
              'amount'                        => $this->getestimatecost($resultphase['Phase_id'], $this->session->userdata('project_id'))
          );
      }

      if (!empty($export['phase_estimated_costs'])) {
        for($i = 0; $i<count($export['phase_estimated_costs']); $i++) {
            $export['phaseTotal'] = $export['phaseTotal'] + $export['phase_estimated_costs'][$i]['amount'];
        }
      }


      /*--------------------Table 2------------------------*/
      $resultfunds = $this->master_report->getFundList();

      if (!empty($resultfunds)) {
        foreach ($resultfunds as $resultfund) {
                $export['fund_costs'][] = array(
                    'fund_name'                    => $this->financial_plans->getFundName($resultfund['fund_id']),
                    'amount'                       => $this->master_report->getFundTotal($resultfund['fund_id'], $this->session->userdata('project_id'))
                );
        }
      }

      if (!empty($export['fund_costs'])) {
        for($i=0;$i<count($export['fund_costs']);$i++) {
          $export['fundTotal'] = $export['fundTotal'] + $export['fund_costs'][$i]['amount'];
        }
      }

      /*--------------------Table 3------------------------*/

      $resultSchedules = $this->master_report->getPhaseList();

      if (!empty($resultSchedules)) {
        foreach ($resultSchedules as $resultSchedule)
            {
                $export['phase_schedules'][] = array(
                    'phase_name'                    => $this->financial_plans->getPhaseName($resultSchedule['Phase_id']),
                    'start_date'                    => $this->getstart_time($resultSchedule['Phase_id'], $this->session->userdata('project_id')),
                    'end_date'                      => $this->getend_time($resultSchedule['Phase_id'], $this->session->userdata('project_id')),
                );
            }
      }

      // create file name
      $fileName = 'report3-'.time().'.xlsx';
      // load excel library
      $this->load->library('excel');
      //$empInfo = $this->export->employeeList();
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->setActiveSheetIndex(0);

      /*-------------------Table 1------------------------*/
      // set Header
      $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Estimated Cost');
      $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Project Phases');
      $objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Amount');
      // set Row.
      $rowCount = 3;
      if (!empty($export['phase_estimated_costs'])) {
        foreach ($export['phase_estimated_costs'] as $estimated_cost) {
          if ($estimated_cost['amount']) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $estimated_cost['phase_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $estimated_cost['amount'])));
            $rowCount++;
          }
        }
      }
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'TOTAL');
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format(preg_replace('/\s+/', '', $export['phaseTotal'])));

      /*-------------------Table 2------------------------*/
      // set Header
      $rowCount = $rowCount + 2;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'PROJECT FUND SOURCES');
      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'Fund Source');
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, 'Amount');
      // set Row.
      $rowCount++;
      if (!empty($export['fund_costs'])) {
        foreach ($export['fund_costs'] as $estimated_cost) {
          if ($estimated_cost['amount']) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $estimated_cost['fund_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, '$ ' . number_format(preg_replace('/\s+/', '', $estimated_cost['amount'])));
            $rowCount++;
          }
        }
      }
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'TOTAL');
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format(preg_replace('/\s+/', '', $export['fundTotal'])));

      /*-------------------Table 3------------------------*/
      // set Header
      $rowCount = $rowCount + 2;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'PROJECT SCHEDULES');
      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'Project Phases');
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, 'Start Date');
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, 'End Date');
      // set Row.
      $rowCount++;
      if (!empty($export['phase_schedules'])) {
        foreach ($export['phase_schedules'] as $estimated_cost) {
          if ($estimated_cost['start_date']) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $estimated_cost['phase_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $estimated_cost['start_date']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $estimated_cost['end_date']);
            $rowCount++;
          }
        }
      }

      $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      $path = $this->root_upload_import_path;
      $http_path = base_url("uploads/report3/");
      $objWriter->save($path . $fileName);
      // download file
      header("Content-Type: application/vnd.ms-excel");
      redirect($http_path . $fileName);
    }

    function getestimatecost($phase_id, $project_id)
    {
        $total_cost = 0;
        $phase_contract_ids = $this->master_report->getProjectPhaseContractId($phase_id, $project_id);

        foreach ($phase_contract_ids as $phase_contract_id)
        {
            $total_cost = $total_cost + ($this->master_report->getamount($project_id, $phase_contract_id['project_phase_contract_id']));
        }
        return $total_cost;
    }

    function getstart_time($phase_id, $project_id)
    {
        $start_date = '';
        $phase_contract_ids = $this->master_report->getProjectPhaseContractId($phase_id, $project_id);

        foreach ($phase_contract_ids as $phase_contract_id)
        {
            $results = $this->master_report->getphasewise_startdate($project_id, $phase_contract_id['project_phase_contract_id']);

            for($i = 0; $i<count($results); $i++)
            {
                $x = $results[$i]['dates'];

                if($start_date)
                {
                    $covDate1 = str_replace('/', '-', $start_date);
                    $covDate1 = date('Y-m-d', strtotime($covDate1));

                    $covDate2 = str_replace('/', '-', $x);
                    $covDate2 = date('Y-m-d', strtotime($covDate2));

                    $a = strtotime($covDate1);
                    $b = strtotime($covDate2);

                    if($a > $b)
                    {
                        $start_date = $x;
                    }
                }
                else
                {
                    $start_date = $x;
                }
            }
        }

        return $start_date;
    }

    function getend_time($phase_id, $project_id)
    {
        $end_date = '';
        $phase_contract_ids = $this->master_report->getProjectPhaseContractId($phase_id, $project_id);

        foreach ($phase_contract_ids as $phase_contract_id)
        {
            $results = $this->master_report->getphasewise_enddate($project_id, $phase_contract_id['project_phase_contract_id']);

            for($i = 0; $i<count($results); $i++)
            {
                $x = $results[$i]['dates'];

                if($end_date)
                {
                    $covDate1 = str_replace('/', '-', $end_date);
                    $covDate1 = date('Y-m-d', strtotime($covDate1));

                    $covDate2 = str_replace('/', '-', $x);
                    $covDate2 = date('Y-m-d', strtotime($covDate2));

                    $a = strtotime($covDate1);
                    $b = strtotime($covDate2);

                    if($a < $b)
                    {
                        $end_date = $x;
                    }
                }
                else
                {
                    $end_date = $x;
                }
            }
        }
        return $end_date;
    }


}
