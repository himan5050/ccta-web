<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_report extends CI_Model{

	/*-------------------------------------------------------------Report 1 Start--------------------------------------------------*/

	public function getDistinctProjectFunds($id)
	{
		if($id)
		{
			$query = $this->db->query("SELECT DISTINCT fund_id FROM financial_plan WHERE project_id = '". $id ."'");
			return $query->result_array();
		}
	}


	public function getamount2($project_id, $project_phase_contract_id, $fund_id)
	{
		if($project_id && $project_phase_contract_id)
		{
			$query = $this->db->query("SELECT SUM(eac) as total FROM financial_plan WHERE project_phase_contract_id = '". $project_phase_contract_id ."' AND project_id = '". $project_id ."' AND fund_id = '". $fund_id ."'");

			foreach ($query->result() as $row)
			{
				return $row->total;
			}
		}
	}


	/*-------------------------------------------------------------Report 1 End----------------------------------------------------*/

	/*-------------------------------------------------------------Report 2 Start--------------------------------------------------*/

	public function getDistinctYears($id = '') {
		if($id) {
			$query = $this->db->query("SELECT DISTINCT financial_years FROM financial_plan_to_years WHERE project_id = '". $id ."' ORDER BY financial_years ASC ");
			return $query->result_array();
		} else {
			$year = ( date('m') > 6) ? date('Y') + 1 : date('Y');
			$query = $this->db->query("SELECT DISTINCT financial_years FROM financial_plan_to_years WHERE financial_years > '" . $year . "' ORDER BY financial_years");
			return $query->result_array();
		}
	}

	public function getProjectPhaseContractId1($Phase_id, $project_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $Phase_id ."'");

		$financial_ids = $query->result_array();

		if($financial_ids)
		{
			$data['financials'] = [];
			foreach ($financial_ids as $financial_id)
			{
				$finances = $this->getfinancialid($financial_id['project_phase_contract_id']);
				if (!empty($finances)) {
					foreach($finances as $finance) {
						$data['financials'][] = array( 'financial_id' => $finance);
					}
				}

				// $data['financials'][] = array( 'financial_id' => $this->getfinancialid($financial_id['project_phase_contract_id']));
			}

			return $data['financials'];
		}
		else
		{
			$data['financials'][0] = array( 'financial_id' => "" );

			return $data['financials'];
		}
	}

	public function getProjectPhaseContractId2($Phase_id, $project_id, $fund_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $Phase_id ."'");

		$financial_ids = $query->result_array();

		if($financial_ids)
		{
			foreach ($financial_ids as $financial_id)
			{
				$data['financials'][] = array( 'financial_id' => $this->getfinancialid2($financial_id['project_phase_contract_id'], $fund_id));
			}
			return $data['financials'];
		}
		else
		{
			$data['financials'][0] = array( 'financial_id' => "" );

			return $data['financials'];
		}

	}

	public function getProjectFundFinancialIDs($project_id, $fund_id) {
		$query = $this->db->query("SELECT financial_id as finance FROM financial_plan WHERE project_id = '". $project_id ."' AND fund_id = '". $fund_id ."'");
		$financial_ids = $query->result_array();
		if($financial_ids) {
			foreach ($financial_ids as $financial_id)
			{
				$data['financials'][] = array( 'financial_id' => $financial_id);
			}
			return $data['financials'];
		}
		else {
			$data['financials'][0] = array( 'financial_id' => "" );
			return $data['financials'];
		}
	}

	public function getfinancialid($id)
	{
		$query = $this->db->query("SELECT financial_id as finance FROM financial_plan WHERE project_phase_contract_id = '". $id ."'");

		$finances = [];
		foreach ($query->result() as $row)
		{
			$finances[] =  $row->finance;
		}

		return $finances;
	}

	public function getfinancialid2($id, $fund_id)
	{
		$query = $this->db->query("SELECT financial_id as finance FROM financial_plan WHERE project_phase_contract_id = '". $id ."' AND fund_id = '". $fund_id ."'");

		foreach ($query->result() as $row)
		{
			return $row->finance;
		}
	}

	public function getamount3($project_id, $financial_id, $financial_year) {
		if($project_id && $financial_id && $financial_year) {
			$query = $this->db->query("SELECT SUM(amount) as total FROM financial_plan_to_years WHERE financial_id = '". $financial_id ."' AND project_id = '". $project_id ."' AND financial_years = '". $financial_year ."'");
			foreach ($query->result() as $row) {
				return $row->total;
			}
		}
	}

	public function getFundsList()
	{
		$query = $this->db->query("SELECT fund_id, fund_name FROM master_fund_source");
		return $query->result_array();
	}


	public function getPropbaseAmount($phase_id, $project_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $phase_id ."'");

		$financial_ids = $query->result_array();

		$financials = 0;

		foreach ($financial_ids as $financial_id)
		{
			$financials = $financials +  $this->PropbaseAmount($financial_id['project_phase_contract_id']);
		}

		return $financials;
	}

	public function PropbaseAmount($id)
	{
		$query = $this->db->query("SELECT SUM(prop_base) as prop FROM financial_plan WHERE project_phase_contract_id = '". $id ."'");

		foreach ($query->result() as $row)
		{
			return $row->prop;
		}
	}

	public function getPriorAmount($phase_id, $project_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $phase_id ."'");

		$financial_ids = $query->result_array();

		$financials = 0;

		foreach ($financial_ids as $financial_id)
		{
			$financials = $financials +  $this->priorSum($financial_id['project_phase_contract_id']);
		}

		return $financials;
	}

	public function getmasterPriorAmount($project_id, $fund_id) {
    	$query = $this->db->query("SELECT SUM(priorfy) as prior FROM `financial_plan` WHERE project_id = '". $project_id ."' AND fund_id = '". $fund_id ."'");
    	foreach ($query->result() as $row) {
			return $row->prior;
		}
		return 0;
	}

	public function getmasterexpendedAmount($project_id, $fund_id) {
    	$query = $this->db->query("SELECT SUM(fytodate) as fytodate FROM `financial_plan` WHERE project_id = '". $project_id ."' AND fund_id = '". $fund_id ."'");
    	foreach ($query->result() as $row) {
			return $row->fytodate;
		}
		return 0;
	}

	public function getmasterremainingAmount($project_id, $fund_id) {
    	$query = $this->db->query("SELECT SUM(fybalance) as fybalance FROM `financial_plan` WHERE project_id = '". $project_id ."' AND fund_id = '". $fund_id ."'");
    	foreach ($query->result() as $row) {
			return $row->fybalance;
		}
		return 0;
	}

	public function getmasterfund($project_id, $fund_id) {
    	$query = $this->db->query("SELECT SUM(amount) as amount FROM `financial_plan_to_years` WHERE project_id = '". $project_id ."' AND fund_id = '". $fund_id ."'");
    	foreach ($query->result() as $row) {
			return $row->fybalance;
		}
		return 0;
	}

	public function priorSum($id)
	{
		$query = $this->db->query("SELECT SUM(priorfy) as prior FROM financial_plan WHERE project_phase_contract_id = '". $id ."'");

		foreach ($query->result() as $row)
		{
			return $row->prior;
		}
	}

	public function getexpendedAmount($phase_id, $project_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $phase_id ."'");

		$financial_ids = $query->result_array();

		$financials = 0;

		foreach ($financial_ids as $financial_id)
		{
			$financials = $financials +  $this->expendedSum($financial_id['project_phase_contract_id']);
		}

		return $financials;
	}

	public function expendedSum($id)
	{
		$query = $this->db->query("SELECT SUM(fytodate) as todate FROM financial_plan WHERE project_phase_contract_id = '". $id ."'");

		foreach ($query->result() as $row)
		{
			return $row->todate;
		}
	}

	public function getremainingAmount($phase_id, $project_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $phase_id ."'");

		$financial_ids = $query->result_array();

		$financials = 0;

		foreach ($financial_ids as $financial_id)
		{
			$financials = $financials +  $this->remainingSum($financial_id['project_phase_contract_id']);
		}

		return $financials;
	}

	public function remainingSum($id)
	{
		$query = $this->db->query("SELECT SUM(fybalance) as balance FROM financial_plan WHERE project_phase_contract_id = '". $id ."'");

		foreach ($query->result() as $row)
		{
			return $row->balance;
		}
	}

	public function getPropbaseAmount1($phase_id, $project_id, $fund_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $phase_id ."'");

		$financial_ids = $query->result_array();

		$financials = 0;

		foreach ($financial_ids as $financial_id)
		{
			$financials = $financials +  $this->PropbaseSum1($financial_id['project_phase_contract_id'], $fund_id);
		}

		return $financials;
	}

	public function PropbaseSum1($id, $fund_id)
	{
		$query = $this->db->query("SELECT SUM(prop_base) as prop FROM financial_plan WHERE project_phase_contract_id = '". $id ."' AND fund_id = '". $fund_id ."' ");

		foreach ($query->result() as $row)
		{
			return $row->prop;
		}
	}

	public function getPriorAmount1($phase_id, $project_id, $fund_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $phase_id ."'");

		$financial_ids = $query->result_array();

		$financials = 0;

		foreach ($financial_ids as $financial_id)
		{
			$financials = $financials +  $this->priorSum1($financial_id['project_phase_contract_id'], $fund_id);
		}

		return $financials;
	}

	public function priorSum1($id, $fund_id)
	{
		$query = $this->db->query("SELECT SUM(priorfy) as prior FROM financial_plan WHERE project_phase_contract_id = '". $id ."' AND fund_id = '". $fund_id ."' ");

		foreach ($query->result() as $row)
		{
			return $row->prior;
		}
	}

	public function getexpendedAmount1($phase_id, $project_id, $fund_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $phase_id ."'");

		$financial_ids = $query->result_array();

		$financials = 0;

		foreach ($financial_ids as $financial_id)
		{
			$financials = $financials +  $this->expendedSum1($financial_id['project_phase_contract_id'], $fund_id);
		}

		return $financials;
	}

	public function expendedSum1($id, $fund_id)
	{
		$query = $this->db->query("SELECT SUM(fytodate) as todate FROM financial_plan WHERE project_phase_contract_id = '". $id ."' AND fund_id = '". $fund_id ."' ");

		foreach ($query->result() as $row)
		{
			return $row->todate;
		}
	}

	public function getremainingAmount1($phase_id, $project_id, $fund_id)
	{
		$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $phase_id ."'");

		$financial_ids = $query->result_array();

		$financials = 0;

		foreach ($financial_ids as $financial_id)
		{
			$financials = $financials +  $this->remainingSum1($financial_id['project_phase_contract_id'], $fund_id);
		}

		return $financials;
	}

	public function remainingSum1($id, $fund_id)
	{
		$query = $this->db->query("SELECT SUM(fybalance) as balance FROM financial_plan WHERE project_phase_contract_id = '". $id ."' AND fund_id = '". $fund_id ."'");

		foreach ($query->result() as $row)
		{
			return $row->balance;
		}
	}


	/*-------------------------------------------------------------Report 2 End----------------------------------------------------*/

	/*-------------------------------------------------------------Report 3 Start--------------------------------------------------*/

	public function getDistinctProjectPhases($id)
	{
		if($id)
		{
			$query = $this->db->query("SELECT DISTINCT Phase_id FROM map_project_phase_contract WHERE project_id = '". $id ."'");
			return $query->result_array();
		}
	}

	public function getPhaseList() {
		$query = $this->db->query("SELECT Phase_id FROM master_phase");
		return $query->result_array();
	}

	public function getProjectList() {
		$query = $this->db->query("SELECT Project_id FROM master_project");
		return $query->result_array();
	}

	public function getDistinctFunds($id)
	{
		if($id)
		{
			$query = $this->db->query("SELECT DISTINCT fund_id FROM financial_plan WHERE project_id = '". $id ."'");
			return $query->result_array();
		}
	}

	public function getFundList()
	{
		$query = $this->db->query("SELECT fund_id FROM master_fund_source");
		return $query->result_array();
	}

	public function getFundListByProject($project_id) {
		$query = $this->db->query("SELECT DISTINCT fund_id FROM financial_plan WHERE project_id = $project_id ORDER BY fund_id ASC");
		return $query->result_array();
	}

	public function getProjectPhaseContractId($Phase_id, $project_id)
	{
		if($Phase_id && $project_id)
		{
			$query = $this->db->query("SELECT project_phase_contract_id FROM map_project_phase_contract WHERE project_id = '". $project_id ."' AND phase_id = '". $Phase_id ."'");
			return $query->result_array();
		}
	}


	public function getEstimateCost_Phase_Project($Phase_id, $project_id)
	{
		if($Phase_id && $project_id)
		{
			$query = $this->db->query("SELECT DISTINCT Phase_id FROM map_project_phase_contract WHERE project_id = '". $id ."'");
			return $query->result_array();
		}
	}

	public function getamount($project_id, $project_phase_contract_id) {
		if($project_id && $project_phase_contract_id) {
			$query = $this->db->query("SELECT SUM(eac) as total FROM financial_plan WHERE project_phase_contract_id = '". $project_phase_contract_id ."' AND project_id = '". $project_id ."'");
			foreach ($query->result() as $row) {
				return $row->total;
			}
		}
	}

	public function getphasewise_startdate($project_id, $project_phase_contract_id)
	{
		if($project_id && $project_phase_contract_id)
		{
			$query = $this->db->query("SELECT start_date as dates FROM financial_plan WHERE project_phase_contract_id = '". $project_phase_contract_id ."' AND project_id = '". $project_id ."'");

			return $query->result_array();
		}
	}

	public function getphasewise_enddate($project_id, $project_phase_contract_id)
	{
		if($project_id && $project_phase_contract_id)
		{
			$query = $this->db->query("SELECT end_date as dates FROM financial_plan WHERE project_phase_contract_id = '". $project_phase_contract_id ."' AND project_id = '". $project_id ."'");

			return $query->result_array();
		}
	}

	public function getFundTotal($fund_id, $project_id)
	{
		if($project_id && $fund_id)
		{
			$query = $this->db->query("SELECT SUM(eac) as total FROM financial_plan WHERE fund_id = '". $fund_id ."' AND project_id = '". $project_id ."'");

			foreach ($query->result() as $row)
			{
				return $row->total;
			}
		}
	}

	/*-------------------------------------------------------------Report 3 End--------------------------------------------------*/

}
