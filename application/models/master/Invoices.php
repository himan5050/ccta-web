<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices extends CI_Model{

	public function getRows($id = ""){
		if(!empty($id))
		{
			$query = $this->db->get_where('master_invoice', array('invoice_id' => $id));
			return $query->row_array();
		}
		else
		{
			$query = $this->db->get('master_invoice');
			return $query->result_array();
		}
	}

	public function getRowsByStatus($status = 0){
		$query = $this->db->get_where('master_invoice', array('status' => $status));
		return $query->row_array();
	}

	
	
	

	public function add($data = array()) {
		$insert = $this->db->insert('master_invoice', $data);

		if($insert){
			return $this->db->insert_id();
		}else{
			return false;
		}
	}

	public function addVersionControl($data = array()) {
		$insert = $this->db->insert('master_invoice_version_control', $data);
	}

	public function update($data, $id) {
		if(!empty($data) && !empty($id)){
			$update = $this->db->update('master_invoice', $data, array('invoice_id'=>$id));
			return $update?true:false;
		}else{
			return false;
		}
	}

	public function discard($id){
		$data = array('status' => 2);
		$update = $this->db->update('master_invoice', $data, array('invoice_id'=>$id));
		return $update?true:false;
	}

	public function invoiceCheck($invoice) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM master_invoice WHERE TRIM(invoice_number) = '". $invoice['ID'] ."' AND TRIM(contract) = '". $invoice['Contract#'] ."' AND TRIM(project) = '". $invoice['ProjectCode'] ."' AND status = 0");

		foreach ($query->result() as $row)
		{
			return $row->total;
		}
	}
	
    public function processInvoiceCheck($tokens, $invoice) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM master_invoice WHERE invoice_number = '". $invoice['ID'] ."' AND contract = '". $tokens['project_phase_contract_id'] ."' AND project = '". $tokens['project_id'] ."' AND fund = '". $tokens['fund_id'] ."' AND status IN (1,2)");

		foreach ($query->result() as $row)
		{
			return $row->total;
		}
	}

	public function history($invoice_id){
		if(!empty($invoice_id))
		{
			$query = $this->db->get_where('master_invoice_version_control', array('invoice_id' => $invoice_id));
			return $query->result_array();
		}
	}

}