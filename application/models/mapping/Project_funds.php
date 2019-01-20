<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

class Project_Funds extends CI_Model
{

    public function getRows($id = "")
    {
        if(!empty($id)) {
            $query = $this->db->get_where('master_project_fund_source', array('project_fund_id' => $id));
            return $query->row_array();
        }
        else
        {
            $query = $this->db->get('master_project_fund_source');
            return $query->result_array();
        }
    }

    public function getRowsByProject($project_id = "")
    {
        if(!empty($project_id)) {
            $query = $this->db->get_where('master_project_fund_source', array('project_id' => $project_id));
            return $query->result_array();
        }
        else
        {
            $query = $this->db->get('master_project_fund_source');
            return $query->result_array();
        }
    }

    public function getRowsByProjectFund($project_id = "", $fund_id = "")
    {
        if(!empty($project_id) && !empty($fund_id)) {
            $query = $this->db->get_where('master_project_fund_source', array('project_id' => $project_id, 'fund_id' => $fund_id));
            return $query->row_array();
        }
        else {
            $query = $this->db->get('master_project_fund_source');
            return $query->row_array();
        }
    }

    public function add($data = array())
    {
        $insert = $this->db->insert('master_project_fund_source', $data);

        if($insert) {
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    public function addVersionControl($data = array())
    {
        $insert = $this->db->insert('master_project_fund_source_version_control', $data);
    }

    public function update($data, $id)
    {
        if(!empty($data) && !empty($id)) {
            $update = $this->db->update('master_project_fund_source', $data, array('project_fund_id'=>$id));
            return $update?true:false;
        }else{
            return false;
        }
    }

    public function delete($id)
    {
        $delete = $this->db->delete('master_project_fund_source', array('project_fund_id'=>$id));
        return $delete?true:false;
    }

    public function budget($project_id = "")
    {
        if (!empty($project_id)) {
            $this->db->select_sum('allocated_amount');
            $query = $this->db->get_where('master_project_fund_source', array('project_id' => $project_id));

            foreach ($query->result() as $row) {
                if ($row->allocated_amount) {
                    return $row->allocated_amount;
                } else {
                    return 0;
                }
            }
        }

        return 0.0;
    }

    public function getunfunded($project_id = "")
    {
        if (!empty($project_id)) {
            $query = $this->db->get_where('master_project_fund_source', array('fund_id' => 1, 'project_id' => $project_id));
            if(!empty($query->row_array())) {
                return $query->row_array();
            } else {
                return [];
            }
        }

        return [];
    }

    public function history($project_fund_id)
    {
        if(!empty($project_fund_id)) {
            $query = $this->db->get_where('master_project_fund_source_version_control', array('project_fund_id' => $project_fund_id));
            return $query->result_array();
        }
    }

    public function getFundSourceList()
    {
        $query = $this->db->get('master_fund_source');
        return $query->result_array();
    }

    public function check($fund_id, $project_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM master_project_fund_source WHERE TRIM(fund_id) = '". $fund_id ."' AND TRIM(project_id) = '". $project_id ."'");

        foreach ($query->result() as $row)
        {
            return $row->total;
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
        $query = $this->db->query("SELECT SUM(allocated_amount) AS amount FROM `master_project_fund_source` WHERE project_id = '" . $project_id . "'");
        foreach ($query->result() as $row) {
            $amount = isset($row->amount) ? $row->amount : 0;
        }

        return $amount;
    }

}
