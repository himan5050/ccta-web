<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

/**
 * @file
 * Model Financial Plan.
 */
class Financial_Plans extends CI_Model
{
    /**
     * get Rows of financial Plans.
     *
     * @param int $id
     *  The project id.
     *
     * @return array
     *  List of financial plans.
     */
    public function getRows($id = "")
    {
        if (!empty($id)) {
            $query = $this->db->get_where('financial_plan', ['financial_id' => $id]);
            return $query->row_array();
        } else {
            if ($this->session->userdata('project_id')) {
                $project_id = $this->session->userdata('project_id');
                $query = $this->db->get_where('financial_plan', ['project_id' => $project_id]);
                return $query->result_array();
            }
        }
    }

    /**
     * [getRowsByTokens description]
     *
     * @param array $tokens
     *  Array containing project info.
     *
     * @return array
     *  List of financial plan.
     */
    public function getRowsByTokens($tokens = [])
    {
        if(!empty($tokens)) {
            $query = $this->db->get_where('financial_plan', ['project_id' => $tokens['project_id'], 'project_phase_contract_id' => $tokens['project_phase_contract_id'], 'fund_id' => $tokens['fund_id']]);
            return $query->row_array();
        }
    }

    public function getRowsByFundAllocation($fund_id, $project_id)
    {
        if(!empty($fund_id)) {
            $query = $this->db->get_where('financial_plan', array('project_id' => $project_id, 'fund_id' => $fund_id));
            return $query->row_array();
        }
    }

    public function getyears($id)
    {
        if(!empty($id)) {
            $query = $this->db->get_where('financial_plan_to_years', array('financial_id' => $id));
            return $query->result_array();
        }
    }

    public function get_year_wise_totolamount($id)
    {
        if(!empty($id)) {
            $this->db->select_sum('amount');
            $query = $this->db->get_where('financial_plan_to_years', array('financial_id' => $id));

            foreach ($query->result() as $row)
            {
                return $row->amount;
            }
        }
    }

    public function add($data = array())
    {
        $insert = $this->db->insert('financial_plan', $data);
        if($insert) {
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    public function addversion($data = array())
    {
        $insert = $this->db->insert('financial_plan_version_control', $data);
        if($insert) {
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    public function addyear($data = array())
    {
        $insert = $this->db->insert('financial_plan_to_years', $data);
        if($insert) {
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    public function addVersionControl($data = array())
    {
        $insert = $this->db->insert('master_project_version_control', $data);
    }

    public function update($data, $id)
    {
        if(!empty($data) && !empty($id)) {
            $update = $this->db->update('financial_plan', $data, array('financial_id'=>$id));
            return $update?true:false;
        }else{
            return false;
        }
    }

    public function delete($id)
    {
        $delete = $this->db->delete('financial_plan', array('financial_id'=>$id));
        return $delete?true:false;
    }

    public function deleteyears($id)
    {
        $delete = $this->db->delete('financial_plan_to_years', array('financial_id'=>$id));
        return $delete?true:false;
    }

    public function deleteyearbyfy($id, $year)
    {
        $delete = $this->db->delete('financial_plan_to_years', array('financial_id'=>$id, 'financial_years' => $year));
        return $delete?true:false;
    }

    public function getProjectName($project_id)
    {
        $query = $this->db->query("SELECT project_name AS project FROM master_project WHERE project_id = '". $project_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->project;
        }

    }

    public function getPhaseName($phase_id)
    {
        $query = $this->db->query("SELECT phase_name AS phase FROM master_phase WHERE phase_id = '". $phase_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->phase;
        }

    }


    public function getPhaseName1($project_phase_contract_id)
    {
        $query = $this->db->query("SELECT phase_id AS phase FROM `map_project_phase_contract` WHERE project_phase_contract_id = '". $project_phase_contract_id ."'");

        foreach ($query->result() as $row)
        {
            $phase_name = $this->getPhaseName($row->phase);
            return $phase_name;
        }

    }

    public function getPhaseId($project_phase_contract_id)
    {
        $query = $this->db->query("SELECT phase_id AS phase FROM `map_project_phase_contract` WHERE project_phase_contract_id = '". $project_phase_contract_id ."'");
        if ($query) {
            foreach ($query->result() as $row) {
                return $row->phase;
            }
        }
        return false;
    }

    public function getPhaseWeight($phase_id)
    {
        $query = $this->db->query("SELECT weight AS weight FROM `master_phase` WHERE phase_id = '" . $phase_id . "'");
        if ($query) {
            foreach ($query->result() as $row) {
                return $row->weight;
            }
        }
        return false;
    }

    public function getContractName($project_phase_contract_id)
    {
        $query = $this->db->query("SELECT contract AS contract FROM `map_project_phase_contract` WHERE project_phase_contract_id = '". $project_phase_contract_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->contract;
        }

    }

    public function getFundName($fund_id)
    {
        $query = $this->db->query("SELECT fund_name AS fund FROM `master_fund_source` WHERE fund_id = '". $fund_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->fund;
        }

    }

    public function getFundSubTotal($fund_id, $project_id)
    {
        $amount = 0;
        $query = $this->db->query("SELECT SUM(eac) AS amount FROM `financial_plan` WHERE fund_id = '". $fund_id ."' AND project_id = '" . $project_id . "'");
        foreach ($query->result() as $row) {
            $amount = isset($row->amount) ? $row->amount : 0;
        }
        return $amount;
    }

    /**
     * [getSumRows description]
     *
     * @param  [type] $fund_id    [description]
     * @param  [type] $project_id [description]
     * @return [type]             [description]
     */
    public function getSumRows($project_id)
    {
        $amount = 0;
        $query = $this->db->query("SELECT SUM(eac) AS amount FROM `financial_plan` WHERE project_id = '" . $project_id . "'");
        foreach ($query->result() as $row) {
            $amount = isset($row->amount) ? $row->amount : 0;
        }

        return $amount;
    }

    public function getPhaseList()
    {
        $query = $this->db->get('master_phase');
        return $query->result_array();
    }

    public function getFundList()
    {
        $query = $this->db->get('master_fund_source');
        return $query->result_array();
    }

    public function curr_base()
    {
        $this->db->select_sum('curr_base');
        $query = $this->db->get('financial_plan');

        foreach ($query->result() as $row)
        {
            return $row->curr_base;
        }
    }

    public function prop_base($project_id)
    {
        $this->db->select_sum('prop_base');
        $query = $this->db->get_where('financial_plan', array('project_id' => $project_id));

        foreach ($query->result() as $row)
        {
            return $row->prop_base;
        }
    }

    public function eac($project_id)
    {
        $this->db->select_sum('eac');
        $query = $this->db->get_where('financial_plan', array('project_id' => $project_id));

        foreach ($query->result() as $row)
        {
            return $row->eac;
        }
    }

    public function getTotalFundAllocatedToContracts($project_id, $fund_id)
    {
        $this->db->select_sum('eac');
        $query = $this->db->get_where('financial_plan', array('project_id' => $project_id, 'fund_id' => $fund_id));
        foreach ($query->result() as $row) {
            return $row->eac;
        }
    }

    public function contractlist($phase_id, $project_id)
    {
        //  $query = $this->db->get_where('map_project_phase_contract', array('phase_id' => $phase_id, 'project_id' => $project_id, 'status' => ));
        $query = $this->db->query("SELECT * FROM `map_project_phase_contract` WHERE phase_id = '". $phase_id ."' AND project_id = '" . $project_id . "' AND status IN (1, 3)");
        return $query->result_array();
    }

    public function getPhaseContractId($phase_id, $contract_name, $project_id)
    {
        $query = $this->db->query("SELECT project_phase_contract_id AS phase_contract_id FROM `map_project_phase_contract` WHERE phase_id = '". $phase_id ."' AND TRIM(contract) = '". $contract_name ."' AND project_id = '" . $project_id . "'");
        foreach ($query->result() as $row) {
            return $row->phase_contract_id;
        }
    }

    public function checkPhaseContractIdToFunds($fund_id, $phase_contract_id, $project_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS cnt FROM `financial_plan` WHERE fund_id = '". $fund_id ."' AND project_phase_contract_id = '". $phase_contract_id ."' AND project_id = '". $project_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->cnt;
        }
    }

    public function checkPhaseContractId($phase_contract_id, $project_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS cnt FROM `financial_plan` WHERE project_phase_contract_id = '". $phase_contract_id ."' AND project_id = '". $project_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->cnt;
        }
    }

    public function returnfundamount($fund_id, $project_id)
    {
        $query = $this->db->query("SELECT available_amount AS available FROM `master_project_fund_source` WHERE fund_id = '". $fund_id ."' AND project_id = '" . $project_id . "'");
        foreach ($query->result() as $row) {
            return $row->available;
        }
    }

    public function returnfundallocatedamount($fund_id)
    {
        $query = $this->db->query("SELECT allocated_amount AS allocated FROM `master_fund_source` WHERE fund_id = '". $fund_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->allocated;
        }
    }

    public function updateavailableamount($fund_id, $amount, $project_id)
    {
        if(!empty($fund_id) && ($amount >= 0)&& !empty($project_id)) {
            $this->db->query("UPDATE master_fund_source SET `available_amount` = '". $amount ."' WHERE `fund_id` = '". $fund_id ."'");
            $this->db->query("UPDATE master_project_fund_source SET `available_amount` = '". $amount ."' WHERE `fund_id` = '". $fund_id ."' AND `project_id` = '". $project_id ."'");
        }
    }

    public function updateallocatedamount($fund_id, $amount)
    {
        if(!empty($fund_id) && !empty($amount)) {
            $this->db->query("UPDATE master_fund_source SET `allocated_amount` = '". $amount ."' WHERE `fund_id` = '". $fund_id ."'");
        }
    }

    public function updateInvoiceFYData($financial_id, $fybalance, $fytodate)
    {
        if(!empty($financial_id)) {
            $update = $this->db->query("UPDATE financial_plan SET `fytodate` = '". $fytodate ."', `fybalance` = '". $fybalance ."' WHERE `financial_id` = '". $financial_id ."'");
            return $update?true:false;
        } else {
            return false;
        }
    }

    public function history($financial_id)
    {
        if(!empty($financial_id)) {
            $query = $this->db->get_where('financial_plan_version_control', array('financial_id' => $financial_id));
            return $query->result_array();
        }
    }

    public function getfilter1($project_phase_contract, $fund_source, $project_id)
    {
            $query = $this->db->get_where('financial_plan', array('project_phase_contract_id' => $project_phase_contract, 'fund_id' => $fund_source, 'project_id' => $project_id));
            return $query->result_array();
    }

    public function getfilter2($project_phase_contract, $project_id)
    {
            $query = $this->db->get_where('financial_plan', array('project_phase_contract_id' => $project_phase_contract, 'project_id' => $project_id));
            return $query->result_array();
    }

    public function getfilter3($fund_source, $project_id)
    {
            $query = $this->db->get_where('financial_plan', array('fund_id' => $fund_source, 'project_id' => $project_id));
            return $query->result_array();
    }

    public function getfilter4($project_phases, $project_id)
    {
        $this->db->select('*');
        $this->db->from('financial_plan');
        $this->db->where_in('project_phase_contract_id', $project_phases);
        $this->db->where('project_id', $project_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getfilter5($project_phases, $fund_source, $project_id)
    {
        $this->db->select('*');
        $this->db->from('financial_plan');
        $this->db->where_in('project_phase_contract_id', $project_phases);
        $this->db->where('project_id', $project_id);
        $this->db->where('fund_id', $fund_source);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getTotalFundFinancialPlans($fund_id, $project_id, $field_name)
    {
        $this->db->select_sum($field_name);
        $query = $this->db->get_where('financial_plan', array('project_id' => $project_id, 'fund_id' => $fund_id));
        foreach ($query->result() as $row) {
            if ($row->$field_name) {
                return $row->$field_name;
            } else {
                return 0.0;
            }

        }
    }

    public function getTotalFundFY($fund_id, $project_id, $fy)
    {
        $query = $this->db->get_where('financial_plan', array('project_id' => $project_id, 'fund_id' => $fund_id));
        $financials = $query->result_array();
        if (!empty($financials)) {
            foreach ($financials as $financial) {
                $finance[] = $financial['financial_id'];
            }
            $sum = $this->getTotalFYYear($project_id, $finance, $fy);
        } else {
            return 0.0;
        }

        return $sum['amount'];
    }

    public function getTotalFYYear($project_id, $finance, $fy)
    {
        $this->db->select_sum('amount');
        $this->db->from('financial_plan_to_years');
        $this->db->where_in('financial_id', $finance);
        $this->db->where('financial_years', $fy);
        $query = $this->db->get();
        if (!empty($query->row_array())) {
            return $query->row_array();
        }
        return ['amount' => 0.0];
    }

    /**
     * Get sum of remaining phase to contract.
     *
     * @param int $project_phase_contract_id
     *  Project phase to contract Id
     * @param int $financial_id
     *  Financial Id.
     *
     * @return int
     *  Sum of remaining phase to contract entries.
     */
    public function getRemainSumByPhaseToContract($project_phase_contract_id, $financial_id)
    {
        if ((is_numeric($project_phase_contract_id)) && (is_numeric($financial_id))) {
            $this->db->select_sum('eac')->from('financial_plan');
            $this->db->where('project_phase_contract_id', $project_phase_contract_id);
            $this->db->where('financial_id != ', $financial_id);
            $query = $this->db->get();
            if (!empty($query->row_array())) {
                return $query->row_array();
            }
        }

        return ['eac' => 0.0];
    }

    /**
     * Get sum of phase to contract.
     *
     * @param int $project_phase_contract_id
     *  Project phase to contract Id
     *
     * @return int
     *  Sum of remaining phase to contract entries.
     */
    public function getSumByPhaseToContract($project_phase_contract_id)
    {
        if ((is_numeric($project_phase_contract_id))) {
            $this->db->select_sum('eac')->from('financial_plan');
            $this->db->where('project_phase_contract_id', $project_phase_contract_id);
            $query = $this->db->get();
            if (!empty($query->row_array())) {
                return $query->row_array();
            }
        }

        return ['eac' => 0.0];
    }
}
