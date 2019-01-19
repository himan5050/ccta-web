<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends CI_Controller {

	protected $non_processed;
	protected $processed;

	function __construct() {
		parent::__construct();
		$this->non_processed = 0;
		$this->processed = 0;
		$this->load->helper('form');
		$this->load->helper(array('form', 'url'));
		$this->load->helper('file');
		$this->load->library('form_validation');
		$this->load->library('csvreader');
		$this->load->library('session');
		$this->load->model('master/invoices');
		$this->load->model('mapping/projects_phases_contracts');
		$this->load->model('master/phases');
		$this->load->model('master/funds');
		$this->load->model('mapping/financial_plans');
	}

	protected function setInvoiceProcessedCounter() {
		$this->processed++;
	}

	protected function setInvoiceNonProcessedCounter() {
		$this->non_processed++;
	}

	protected function getInvoiceProcessedCounter() {
		return $this->processed;
	}

	protected function getInvoiceNonProcessedCounter() {
		return $this->non_processed;
	}

	public function index(){
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

		if($this->input->post('postSubmit')) {
			$config['upload_path'] = 'uploads/invoices/queue/';
			$config['allowed_types'] = 'csv';

			$this->load->library('upload', $config);
			delete_files($config['upload_path']);

			if (!$this->upload->do_upload('attachment')) {
				$this->session->set_userdata(['error_msg' => $this->upload->display_errors()]);
				redirect('/invoice');
			} else {
				$config['upload_data'] = $this->upload->data();
				$this->processInvoices($config);
				redirect('/invoice');
			}
		}

		// Fetch all non processed invoices that required action.
		$invoices = $this->invoices->getRows();
		// Parsed all ids into name.
		if (!empty($invoices)) {
			$data['invoices_list'] = $this->parseNonProcessInvoices($invoices);
		}

		$data['title'] = 'Non Processed Invoices Pending for Action';

		//load the list page view
		$this->load->view('common/header',$data);
		$this->load->view('master/invoice/add', $data);
		$this->load->view('common/footer');
	}

	protected function processInvoices($config = array()) {
		$non_processed = 0;
		$processed = 0;
		$filepath = $config['upload_data']['full_path'];
		$rows = $this->csvreader->parse_file($filepath); //path to csv file
		if (!empty($rows)) {
			foreach ($rows as $row) {
				if ($this->isInvoiceValidProcess($row)) {
					// get tokens (Phase Code, Fund resouce code, contract id)
					if (!empty($this->parseInvoiceProjectCode($row))) {
						$tokens = $this->parseInvoiceProjectCode($row);
						$this->processInvoice($tokens, $row);
					} else {
						$this->setInvoiceNonProcessedCounter(); // ERROR: Invalid contract and project phase detail.
						$this->saveInvoicesProcessRawData($row);
					}
				}
			}
			if ($this->validateInvoicesProcessCount()) {
				$this->session->set_userdata(['success_msg' => $this->getInvoiceProcessedCounter().' invoice(s) processed and ' . $this->getInvoiceNonProcessedCounter().' invoice(s) not processed']);
			}
		} else {
			$this->session->set_userdata(['error_msg' => 'No valid invoice found to process.']);
		}
	}

	protected function isInvoiceValidProcess($row) {
		if (isset($row['Status']) && isset($row['ProjectCode']) && isset($row['Contract#']) && isset($row['ID'])) {
		  if ($row['Status'] != 'Paid') {
			return FALSE;
		  }

		  return TRUE;
		} else {
			$this->session->set_userdata(['error_msg' => 'Unexpected error encountered, Please upload valid CSV file.']); // Invalid CSV.
			redirect('/invoice');
		}
	}

	protected function parseInvoiceProjectCode($row) {
		$tokens = [];
		$projectCode = explode('.', $row['ProjectCode']);
		$contract = [];
		$phase = [];
		$fund = [];
		foreach ($projectCode as $code) {
			$code = preg_replace('/\d+/u', '', $code);
			$code = trim(str_replace(' ','', $code));
			if (strlen($code) >= 3) { //valid code.
			  $ph = isset($code) ? $this->phases->getRowsByCode($code) : 0;
			  $fu = isset($code) ? $this->funds->getRowsByCode($code) : 0;
			  if (isset($ph) && ($ph['phase_id'])) {
			  	$phase = $ph;
			  	$contract = $this->projects_phases_contracts->getRowsByNamePhase($row['Contract#'], $phase['phase_id']);
			  }
			  if (isset($fu) && ($fu['fund_id'])) {
			  	$fund = $fu;
			  }
			}
		}

		if (!empty(($contract)) && !empty(($phase)) && !empty(($fund))) {
		if ((is_numeric($contract['project_id'])) && (is_numeric($contract['project_phase_contract_id'])) && (is_numeric($fund['fund_id']))) {
			$tokens['project_id'] = $contract['project_id'];
			$tokens['project_phase_contract_id'] = $contract['project_phase_contract_id'];
			$tokens['fund_id'] = $fund['fund_id'];
		}
		}

		return $tokens;
	}

	protected function processInvoice($tokens = array(), $row = array(), $id = NULL) {
		if ($this->financial_plans->getRowsByTokens($tokens)) {
			$financial = $this->financial_plans->getRowsByTokens($tokens);
			if ($this->invoiceProcessValidate($financial, $row)) {
				$invoice_amount = isset($row['Amount']) ? $row['Amount'] : 0;
				$fybalance = $financial['fybalance'] - $invoice_amount;
				$fytodate = $financial['fytodate'] + $invoice_amount;
				if (!$this->alreadyProcessedInvoice($tokens, $row)) {
					$updateInvoice = $this->financial_plans->updateInvoiceFYData($financial['financial_id'], $fybalance, $fytodate);
					$available_amount = $this->financial_plans->returnfundamount($tokens['fund_id'], $tokens['project_id']); //get available amount.
					$financial['fybalance'] = $fybalance;
					$financial['fytodate'] = $fytodate;
					$remaining_amount = $available_amount - $invoice_amount;
					$this->financial_plans->updateavailableamount($tokens['fund_id'], $remaining_amount, $tokens['project_id']);
					unset($financial['date_modified']); // unset field for version control.
					// add finacial_plans version.
					$financial_plan_version_id = $this->financial_plans->addversion($financial);
					$this->saveInvoicesProcessed($tokens, $row, $id); //invoice is processed.
					$this->setInvoiceProcessedCounter(); // SUCCESS: Invoice has been processed successfully.
					if ($updateInvoice) {
						$code['msg'] = 'Invoice Recalled and Processed Successfully.';
						$code['status'] = 1;
						return $code;
					}
				} else {
					$this->setInvoiceNonProcessedCounter(); //ERROR: Invoice is already processed in system.
					$code['msg'] = 'Invoice not processed for reason : Invoice is already processed to the system, No action required.';
					$code['status'] = 0;
					return $code;
				}
			} else {
				$this->setInvoiceNonProcessedCounter(); //ERROR: Invoice amount exceeds FY balance amount.
				$this->saveInvoicesProcessRawData($row, $id);
				$code['msg'] = 'Invoice not processed for reason : Invoice amount exceeds FY balance amount.';
				$code['status'] = 0;
				return $code;
			}
		} else {
			$this->setInvoiceNonProcessedCounter(); // ERROR: Contract does not exists for given project phase.
			$this->saveInvoicesProcessRawData($row, $id);
			$code['msg'] = 'Invoice not processed for reason : Contract does not map with project phase.';
			$code['status'] = 0;
			return $code;
		}
	}

	protected function invoiceProcessValidate($financial = array(), $row = array()) {
		$invoice_amount = isset($row['Amount']) ? $row['Amount'] : 0;
		$balance_amount = $financial['eac'] - ($financial['priorfy'] + $financial['fytodate']);
		if ($balance_amount > 0) {
		  if ($invoice_amount <= $financial['fybalance']) {
			return TRUE;
		  }
		}

		return FALSE;
	}

	protected function alreadyProcessedInvoice($tokens = array(), $row = array()) {
		return $this->invoices->processInvoiceCheck($tokens, $row);
	}

	protected function saveInvoicesProcessed($tokens = array(), $row = array(), $id = NULL) {
		$invoice = [];
		if (!$this->invoices->processInvoiceCheck($tokens, $row)) {
			$invoice['invoice_number'] = isset($row['ID']) ? $row['ID']: '';
			$invoice['project'] = isset($tokens['project_id']) ? $tokens['project_id'] : 0;
			$invoice['contract'] = isset($tokens['project_phase_contract_id']) ? $tokens['project_phase_contract_id'] : 0;
			$invoice['fund'] = isset($tokens['fund_id']) ? $tokens['fund_id'] : 0;
			$invoice['description'] = isset($row['Description']) ? $row['Description'] : 0;
			$invoice['gl_date'] = isset($row['GLDate']) ? $row['GLDate'] : '';
			$invoice['gl_account'] = isset($row['GLAccount']) ? $row['GLAccount'] : '';
			$invoice['amount'] = isset($row['Amount']) ? $row['Amount'] : 0;
			$invoice['status'] = 1; // Invoice is processed now.
			if (isset($id)) {
				$invoice['date_recalled'] = time();
				$this->invoices->update($invoice, $id);
			} else {
				$invoice['date_processed'] = time();
				$invoice['date_recalled'] = '';
				$id = $this->invoices->add($invoice);
			}
			$version = array_merge(['invoice_id' => $id], $invoice);
			$this->invoices->addVersionControl($version);
		}
	}

	protected function saveInvoicesProcessRawData($row = array(), $id = NULL) {
		$invoice = [];
		if (!$this->invoices->invoiceCheck($row) && !isset($id)) {
			$invoice['invoice_number'] = isset($row['ID']) ? $row['ID']: '';
			$invoice['project'] = isset($row['ProjectCode']) ? $row['ProjectCode'] : 0;
			$invoice['contract'] = isset($row['Contract#']) ? $row['Contract#'] : 0;
			$invoice['fund'] = isset($row['ProjectCode']) ? $row['ProjectCode'] : 0;
			$invoice['description'] = isset($row['Description']) ? $row['Description'] : 0;
			$invoice['gl_date'] = isset($row['GLDate']) ? $row['GLDate'] : '';
			$invoice['gl_account'] = isset($row['GLAccount']) ? $row['GLAccount'] : '';
			$invoice['amount'] = isset($row['Amount']) ? $row['Amount'] : 0;
			$invoice['date_processed'] = time();
			$invoice['date_recalled'] = '';
			$invoice['status'] = 0; // By default all invoices are non processed.

			$insert = $this->invoices->add($invoice);
			$version = array_merge(['invoice_id' => $insert], $invoice);
			$this->invoices->addVersionControl($version);
		}
	}

	protected function parseNonProcessInvoices($invoices) {
		$parsedInvoices = [];
		foreach ($invoices as $invoice) {
			if (!empty($invoice) && ($invoice['status'] == 0)) {
				foreach ($invoice as $key => $value) {
					$parser[$key] = $value;
					if ($key == 'contract') {
						$contract = $this->projects_phases_contracts->getRows($value);
						$parser['contract_name'] = $contract['contract_name'];
					}
					if ($key == 'status') {
						switch ($key) {
							case 0:
								$parser['status'] = 'Non Processed';
								break;
							case 1:
								$parser['status'] = 'Processed';
								break;
							case 2:
								$parser['status'] = 'Discarded';
								break;
						}
					}
					if ($key == 'date_processed') {
						$parser['date_processed'] = date('m/d/Y:hh:mm:ss', $value);
					}
				}

				$parsedInvoices[] = $parser;
			}
		}

		return $parsedInvoices;
	}

	protected function validateInvoicesProcessCount() {
		if (!$this->getInvoiceProcessedCounter()) {
			$this->session->set_userdata(['error_msg' => 'Please check CSV File, Either invoices are already processed or no valid invoice found.']);
			return FALSE;
		}

		return TRUE;
	}

	public function edit($id){
		$data = array();

		$postData = $this->invoices->getRows($id);
		// Fetch all non processed invoices that required action.
		$invoices = $this->invoices->getRows();
		// Parsed all ids into name.
		if (!empty($invoices)) {
			$data['invoices_list'] = $this->parseNonProcessInvoices($invoices);
		}


		$data['title'] = 'Invoice Process Recall';

		if($this->input->post('postSubmit')){
			// Prepare row to process invoice.
			$row = array(
			  'ID' => $postData['invoice_number'],
			  'GLDate' => $postData['gl_date'],
			  'Contract#' => $this->input->post('contract'),
			  'Amount' => $this->input->post('amount'),
			  'GLAccount' => $postData['gl_account'],
			  'ProjectCode' => $this->input->post('project')
			);

			$postData = array(
                'invoice_number' => $this->input->post('invoice_number'),
                'project'      => $this->input->post('project'),
                'contract'      => $this->input->post('contract'),
                'amount'  => $this->input->post('amount'),
			    'date_recalled' => time(),
			);

			$update = $this->invoices->update($postData, $id);

			$postVersionControlData = array(
                'invoice_id'               => $id,
                'invoice_number'             => $this->input->post('invoice_number'),
                'project'      => $this->input->post('project'),
                'contract'      => $this->input->post('contract'),
                'amount'                 => $this->input->post('amount'),
                'date_processed'            => date("m/d/Y G:i:s"),
			    'date_recalled' => time()
			);

			$insertVersionControl = $this->invoices->addVersionControl($postVersionControlData);

			if($update){
				// Process Invoice recall.
				if (!empty($this->parseInvoiceProjectCode($row))) {
					$tokens = $this->parseInvoiceProjectCode($row);
					$code = $this->processInvoice($tokens, $row, $id);
					if ($code['status']) {
						$this->session->set_userdata('success_msg', $code['msg']);
					} else {
						$this->session->set_userdata('error_msg', $code['msg']);
					}
				} else {
					$this->session->set_userdata('error_msg', 'Invoice not processed for reason : Invalid contract found with respect to project phase.');
				}
				$this->session->set_userdata('success_msg', 'Invoice Updated Successfully.');
				redirect('/invoice');
			}else{
				$data['error_msg'] = 'Some problems occurred, please try again.';
			}
		}

		$data['post'] = $postData;

		//load the edit page view
		$this->load->view('common/header', $data);
		$this->load->view('master/invoice/recall', $data);
		$this->load->view('common/footer');
	}


	public function discard($id){
		//check whether post id is not empty
		if($id){
			//delete post
			$discard = $this->invoices->discard($id);

			if($discard){
				$this->session->set_userdata('success_msg', 'Invoice has been discarded successfully.');
			}else{
				$this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
			}
		}

		redirect('/invoice');
	}



	public function history() {
		$invoice_id = $this->input->post('invoice_id');
		$results = $this->invoices->history($invoice_id);

		foreach ($results as $result) {
			$data['history'][] = array(
                'invoice_version_id'     => $result['invoice_version_id'],
                'invoice_id'                  => $result['invoice_id'],
                'invoice_number'                    => $result['invoice_number'],
                'project'                    => $result['project'],
                'contract'                 => $result['contract'],
				'amount'	=> $result['amount'],
                'date_processed'                       => $result['date_processed'],
			);
		}

		echo json_encode($data['history']);
	}
}
