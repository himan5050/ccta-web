<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phases extends CI_Model{

    public function getRows($id = ""){
        if(!empty($id))
        {
            $query = $this->db->get_where('master_phase', array('phase_id' => $id));
            return $query->row_array();
        }
        else
        {
            $query = $this->db->get('master_phase');
            return $query->result_array();
        }
    }
    
    public function getRowsByCode($code = ""){
        if(!empty($code))
        {
            $query = $this->db->get_where('master_phase', array('phase_code' => $code));
            return $query->row_array();
        }
    }
    
    public function add($data = array()) {
        $insert = $this->db->insert('master_phase', $data);
        if($insert){
            return $this->db->insert_id();
        }else{
            return false;
        }
    }
    
    public function update($data, $id) {
        if(!empty($data) && !empty($id)){
            $update = $this->db->update('master_phase', $data, array('phase_id'=>$id));
            return $update?true:false;
        }else{
            return false;
        }
    }

    public function delete($id){
        $delete = $this->db->delete('master_phase',array('phase_id'=>$id));
        return $delete?true:false;
    }

    public function check($phase) { 
        $query = $this->db->query("SELECT COUNT(*) AS total FROM master_phase WHERE TRIM(phase_name) = '". $phase ."'");

        foreach ($query->result() as $row)
        {
            return $row->total;
        }
    }
}