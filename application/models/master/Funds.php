<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Funds extends CI_Model{

    public function getRows($id = ""){
        if(!empty($id))
        {
            $query = $this->db->get_where('master_fund_source', array('fund_id' => $id));
            return $query->row_array();
        }
        else
        {
            $query = $this->db->get('master_fund_source');
            return $query->result_array();
        }
    }

    public function getRowsByCode($code = ""){
        if(!empty($code))
        {
            $query = $this->db->get_where('master_fund_source', array('fund_code' => $code));
            return $query->row_array();
        }
    }

    public function add($data = array()) {
        $insert = $this->db->insert('master_fund_source', $data);

        if($insert){
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    public function addVersionControl($data = array()) {
        $insert = $this->db->insert('master_fund_source_version_control', $data);
    }

    public function update($data, $id) {
        if(!empty($data) && !empty($id)){
            $update = $this->db->update('master_fund_source', $data, array('fund_id'=>$id));
            return $update?true:false;
        }else{
            return false;
        }
    }

    public function delete($id){
        $delete = $this->db->delete('master_fund_source',array('fund_id'=>$id));
        return $delete?true:false;
    }

    public function total_allocated_amount() {

        $this->db->select_sum('allocated_amount');
        $query = $this->db->get('master_fund_source');

        foreach ($query->result() as $row)
        {
        	if ($row->allocated_amount) {
        	  return $row->allocated_amount;
        	} else {
        	  return 0;
        	}
        }
    }

    public function total_available_amount() {

        $this->db->select_sum('available_amount');
        $query = $this->db->get('master_fund_source');

        foreach ($query->result() as $row)
        {
           if ($row->available_amount) {
        	  return $row->available_amount;
        	} else {
        	  return 0;
        	}
        }
    }

    public function check($fund_code, $fund_name) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM master_fund_source WHERE TRIM(fund_code) = '". $fund_code ."' OR TRIM(fund_name) = '". $fund_name ."'");
        foreach ($query->result() as $row) {
            return $row->total;
        }
    }

    public function history($fund_id){
        if(!empty($fund_id))
        {
            $query = $this->db->get_where('master_fund_source_version_control', array('fund_id' => $fund_id));
            return $query->result_array();
        }
    }

}
