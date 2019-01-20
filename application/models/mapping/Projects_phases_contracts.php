<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

class Projects_Phases_Contracts extends CI_Model
{

    public function getRows($id = "")
    {
        if(!empty($id)) {
            $query = $this->db->get_where('map_project_phase_contract', array('project_phase_contract_id' => $id));
            return $query->row_array();
        }
        else
        {
            if($this->session->userdata('project_id')) {
                $project_id = $this->session->userdata('project_id');
                $query = $this->db->get_where('map_project_phase_contract', array('project_id' => $project_id));
                return $query->result_array();
            }

        }
    }

    public function getRowsByNamePhase($name = "", $phase = "")
    {
        if(!empty($name)) {
            $query = $this->db->get_where('map_project_phase_contract', array('contract' => $name, 'phase_id' => $phase));
            return $query->row_array();
        }
    }

    public function getRowsByPhase($phase = "")
    {
        if(!empty($phase)) {
            $query = $this->db->get_where('map_project_phase_contract', array('phase_id' => $phase));
            return $query->result_array();
        }
    }

    public function getRowsByPhaseProject($phase = "", $project_id="")
    {
        if(!empty($phase) && !empty($project_id)) {
            $query = $this->db->get_where('map_project_phase_contract', array('phase_id' => $phase, 'project_id' => $project_id));
            return $query->result_array();
        }
    }

    public function add($data = array())
    {
        $insert = $this->db->insert('map_project_phase_contract', $data);
        if($insert) {
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    public function addVersionControl($data = array())
    {
        $insert = $this->db->insert('map_project_phase_contract_version_control', $data);
    }

    public function update($data, $id)
    {
        if(!empty($data) && !empty($id)) {
            $update = $this->db->update('map_project_phase_contract', $data, array('project_phase_contract_id'=>$id));
            return $update?true:false;
        }else{
            return false;
        }
    }

    public function delete($id)
    {
        $delete = $this->db->delete('map_project_phase_contract', array('project_phase_contract_id'=>$id));
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

    public function getPhaseList()
    {
        $query = $this->db->get('master_phase');
        return $query->result_array();
    }

    public function check($contract, $phase_id, $project_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM map_project_phase_contract WHERE contract = '". TRIM($contract) ."' AND phase_id ='". $phase_id ."' AND project_id = '". $project_id ."'");
        foreach ($query->result() as $row) {
            return $row->total;
        }
        return false;
    }

    public function checkedit($contract, $phase_id, $id, $project_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM map_project_phase_contract WHERE TRIM(contract) = '". $contract ."' AND phase_id ='". $phase_id ."' AND project_phase_contract_id != '". $id ."' AND project_id = '". $project_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->total;
        }
    }

    public function getTotalAmount($project_id)
    {
        $query = $this->db->query("SELECT SUM(amount) AS total FROM map_project_phase_contract WHERE project_id = '". $project_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->total;
        }
    }

    public function history($phase_contract_id)
    {
        if(!empty($phase_contract_id)) {
            $query = $this->db->get_where('map_project_phase_contract_version_control', array('project_phase_contract_id' => $phase_contract_id));
            return $query->result_array();
        }
    }

    /**
     * [getSumRows description]
     *
     * @param  [type] $fund_id    [description]
     * @param  [type] $project_id [description]
     * @return [type]             [description]
     */
    public function getSumRowsByProject($project_id)
    {
        $amount = 0;
        $query = $this->db->query("SELECT SUM(amount) AS amount FROM `map_project_phase_contract` WHERE project_id = '" . $project_id . "'");
        foreach ($query->result() as $row) {
            $amount = isset($row->amount) ? $row->amount : 0;
        }

        return $amount;
    }

    /**
     * Filters by Phase, Project, Contracts and Amount.
     */
    public function getRowsByProjectPhaseContractAmount($project, $phase, $contract, $amount)
    {
        $results = [];
        if(!empty($project) && !empty($phase) && !empty($contract) && !empty($amount)) {
            $query = $this->db->get_where(
                'map_project_phase_contract', [
                'project_id' => $project,
                'phase_id' => $phase,
                'contract' => $contract,
                'amount' => $amount
                ]
            );
            $results = $query->result_array();
        }

        return $results;
    }

     /**
      * Filters by Phase, Project, Contracts.
      */
    public function getRowsByProjectPhaseContract($project, $phase, $contract)
    {
        $results = [];
        if(!empty($project) && !empty($phase) && !empty($contract)) {
            $query = $this->db->get_where(
                'map_project_phase_contract', [
                'project_id' => $project,
                'phase_id' => $phase,
                'contract' => $contract,
                ]
            );
            $results = $query->result_array();
        }

        return $results;
    }

      /**
       * Filters by Phase, Project, Contracts.
       */
    public function getRowsByProjectAmount($project, $amount)
    {
        $results = [];
        if(!empty($project) && !empty($amount)) {
            $query = $this->db->get_where(
                'map_project_phase_contract', [
                'project_id' => $project,
                'amount' => $amount,
                ]
            );
            $results = $query->result_array();
        }

        return $results;
    }

      /**
       * Filters by Phase, Project, Contracts.
       */
    public function getRowsByProjectPhase($project, $phase)
    {
        $results = [];
        if(!empty($project) && !empty($phase)) {
            $query = $this->db->get_where(
                'map_project_phase_contract', [
                'project_id' => $project,
                'phase_id' => $phase,
                ]
            );
            $results = $query->result_array();
        }

        return $results;
    }

      /**
       * Filters by Phase, Project, Contracts.
       */
    public function getRowsByProjectPhaseAmount($project, $phase, $amount)
    {
        $results = [];
        if(!empty($project) && !empty($phase) && !empty($amount)) {
            $query = $this->db->get_where(
                'map_project_phase_contract', [
                'project_id' => $project,
                'phase_id' => $phase,
                'amount' => $amount,
                ]
            );
            $results = $query->result_array();
        }

        return $results;
    }
}
