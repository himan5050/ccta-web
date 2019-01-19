<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fy_Report extends CI_Controller {
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
		$resultyears = $this->master_report->getDistinctYears();
		$data['years'][] = array( 'financial_years' => '');
		if (!empty($resultyears)) {
			unset($data['years']);
			foreach ($resultyears as $resultyear) {
				if ($resultyear['financial_years']) {
					$data['years'][] = array( 'financial_years' => $resultyear['financial_years']);
				}
			}
		}
		$resultfunds = $this->master_report->getFundsList();
		$resultphases = $this->master_report->getProjectList();
		$data["fundLists"] = $resultfunds;

		if($this->input->post('postSubmit')) {
			$total = array();
			// Intialize total array.
			foreach ($resultfunds as $resultfund) {
				$total[$resultfund['fund_name']]['total'] = 0.0;
			}
			$fy = $this->input->post('fy');
			$financial_year = $this->returnFYName($fy);
			$data['financial_year'] = $financial_year;
			foreach ($resultphases as $resultphase) {
				$temp['project_name'] = $this->financial_plans->getProjectName($resultphase['Project_id']);
				$total_sum = 0.0;
				foreach ($resultfunds as $resultfund) {
					$temp[$resultfund['fund_name']] = $this->getProjectFundAmount($resultfund['fund_id'], $resultphase['Project_id'], $fy);
					$total_sum = $total_sum + $temp[$resultfund['fund_name']];

					//Total fund by fund source in projects.
					$total[$resultfund['fund_name']]['total'] = $temp[$resultfund['fund_name']] + $total[$resultfund['fund_name']]['total'];
				}
				$temp['sum'] = $total_sum;
				$temp['flag'] = 0;
				$data['project_wise_costs'][] = $temp;
			}
			$data['fund_total'] = $total;
			$data['grand_total'] = $this->calculateGrandTotal($total);
		}

		$data['title'] = 'Fund Source Report';

		//load the list page view
		$this->load->view('common/header',$data);
		$this->load->view('reports/master_reports/fy_report', $data);
		$this->load->view('common/footer');
	}

	public function returnFYName($fy) {
		switch($fy) {
			case 'priorfy':
				return 'Prior';
			case 'fytodate':
				return 'FY18 Expended to Date';
			case 'fybalance':
				return 'FY18 Remaining';
			default:
				return 'Financial Year - '.$fy;
		}
	}

	public function getProjectFundAmount($fund_id, $project_id, $fy) {
		if (in_array($fy, array('priorfy', 'fytodate', 'fybalance'))) {
			$sum =  $this->financial_plans->getTotalFundFinancialPlans($fund_id, $project_id, $fy);
		} else {
			$sum = $this->financial_plans->getTotalFundFY($fund_id, $project_id, $fy);
		}

		return isset($sum) ? $sum : 0.0;
	}

	protected function calculateGrandTotal($total) {
		$grand_total = 0.0;
		foreach ($total as $key => $value) {
			$grand_total = $grand_total + $value['total'];
		}
		return $grand_total;
	}
}
