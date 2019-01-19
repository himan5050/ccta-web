<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Financial_Plan extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper('form');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('mapping/financial_plans');
		$this->load->model('mapping/project_funds');
		$this->load->model('mapping/projects_phases_contracts');
		$this->load->model('master/funds');
		$this->load->model('master/projects');
	}

	public function index(){
		$data = array();
		$prop_base = 0.0;
		$eac = 0.0;
		$current_fy_year = ( date('m') > 6) ? date('Y') + 1 : date('Y');

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

		if($this->input->post('postSubmit')) {
			$data = $this->input->post(); /*print_r($data);exit();*/
			$phase_contract_id = $this->financial_plans->getPhaseContractId($data['phase'],$data['contract'], $this->session->userdata('project_id'));

			$postData = array(
                    'project_id'                        => $this->session->userdata('project_id'),
                    'project_phase_contract_id'         => $phase_contract_id,
                    'fund_id'                           => $data['fund'],
			//'curr_base'                         => ltrim($data['curr_base'], '0'),
                    'prop_base'                         => ltrim($data['prop_base'], '0'),
                    'eac'                               => ltrim($data['eac'], '0'),
                    'unfunded'                          => ltrim($data['unfunded'], '0'),
                    'priorfy'                           => ltrim($data['priorfy'], '0'),
                    'fytodate'                          => ltrim($data['fytodate'], '0'),
                    'fybalance'                         => ltrim($data['fybalance'], '0'),
                    //'start_date'                        => $data['start_date'],
                    //'end_date'                          => $data['end_date'],
                    'date_added'                        => date("m/d/Y G:i:s"),
                    'date_modified'                     => date("m/d/Y G:i:s")
			);

			if(!$postData["prop_base"]) {
				$postData["prop_base"] = 0;
			}

			if(!$postData["eac"]) {
				$postData["eac"] = 0;
			}

			if(!$postData["unfunded"]) {
				$postData["unfunded"] = $this->returnUnfundedamount($postData);
			}

			if(!$postData["priorfy"]) {
				$postData["priorfy"] = 0;
			}

			if(!$postData["fytodate"]) {
				$postData["fytodate"] = 0;
			}

			if(!$postData["fybalance"])
			{
				$postData["fybalance"] = 0;
			}
			else if (isset($data['amount'])) {
				for ($i=0; $i < count($data['amount']); $i++) {
					if ($data['year'][$i] == $current_fy_year) {
						$postData['fybalance'] += $data['amount'][$i];
					}
				}
			}


			if (!$this->validateFinancialPlan($postData)) {
				$financial_id = $this->financial_plans->add($postData);

				$postDataversion = array(
                    'financial_id'                      => $financial_id,
                    'project_id'                        => $this->session->userdata('project_id'),
                    'project_phase_contract_id'         => $phase_contract_id,
                    'fund_id'                           => $data['fund'],
				//'curr_base'                         => ltrim($data['curr_base'], '0'),
                    'prop_base'                         => ltrim($data['prop_base'], '0'),
                    'eac'                               => ltrim($data['eac'], '0'),
                    'unfunded'                          => ltrim($data['unfunded'], '0'),
                    'priorfy'                           => ltrim($data['priorfy'], '0'),
                    'fytodate'                          => ltrim($data['fytodate'], '0'),
                    'fybalance'                         => ltrim($data['fybalance'], '0'),
                  //  'start_date'                        => $data['start_date'],
                  //  'end_date'                          => $data['end_date'],
                    'date_added'                        => date("m/d/Y G:i:s")
				);

				$financial_plan_version_id = $this->financial_plans->addversion($postDataversion);

				if(isset($data['amount'])) {
					for ($i=0; $i < count($data['amount']); $i++) {
						if ($data['year'][$i] != $current_fy_year) {
							$postyear = array(
	                        	'financial_id'      => $financial_id,
	                        	'project_id'        => $this->session->userdata('project_id'),
	                        	'financial_years'   => $data['year'][$i],
	                        	'amount'            => $data['amount'][$i]
							);

							$insert = $this->financial_plans->addyear($postyear);
						}

					}
				}

				if($financial_plan_version_id){
					$this->session->set_userdata('success_msg', 'Financial Plan has been added successfully.');
					redirect('/financial_plan');
				}else{
					$data['error_msg'] = 'Some problems occurred, please try again.';
				}
			} else {
				if(!$this->session->userdata('project_id')) {
					redirect('/project');
				} else {
					$data["project_name"] = $this->financial_plans->getProjectName($this->session->userdata('project_id'));
				}

				$error_msg = $this->validateFinancialPlan($postData);
				$data['error_msg'] = $error_msg;
			}

		}

		if(!$this->input->post('postFilter')) {
			$results = $this->financial_plans->getRows();

			if (!empty($results)) {
				foreach ($results as $result) {
					$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
					$data['financial_plan_lists'][] = array(
	                    'financial_id'                  => $result['financial_id'],
	                    'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
	                    'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
	                    'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
					// 'curr_base'                     => $result['curr_base'],
	                    'prop_base'                     => $result['prop_base'],
	                    'eac'                           => $result['eac'],
	                    'unfunded'                      => $result['unfunded'],
	                    'priorfy'                       => $result['priorfy'],
	                    'fytodate'                      => $result['fytodate'],
	                    'fybalance'                     => $result['fybalance'],
	                    'start_date'                    => $result['start_date'],
	                    'end_date'                      => $result['end_date'],
											'phase_id'											=> $phase_id,
											'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
					);

					$prop_base = $prop_base + $result['prop_base'];
					$eac = $eac + $result['eac'];
				}

				$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);
			}
		}

		// $prop_base = $this->financial_plans->prop_base($this->session->userdata('project_id'));
		$data['prop_base'] = ($prop_base) ? $prop_base : 0.0;

		// $eac = $this->financial_plans->eac($this->session->userdata('project_id'));
		$data['eac'] = ($eac) ? $eac : 0.0;

		$phase_lists = $this->financial_plans->getPhaseList();

		foreach ($phase_lists as $phase_list)
		{
			$data['phases'][] = array(
                'phase_id'      => $phase_list['phase_id'],
                'phase_name'    => $phase_list['phase_name']
			);
		}

		$fund_lists = $this->financial_plans->getFundList();

		foreach ($fund_lists as $fund_list)
		{
			$data['funds'][] = array(
                'fund_id'      => $fund_list['fund_id'],
                'fund_name'    => $fund_list['fund_name']
			);
		}

		$data['title'] = 'Financial Plan';

		/*---------------------------------------------------------------------------For Filters Start----------------------------------------------------------------------------*/

		if($this->input->post('postFilter')) {
			$project_phase = $this->input->post('phasefilter');
			$Contract = $this->input->post('contractfilter');
			$fund_source = $this->input->post('fundfilter');

			if($project_phase && $Contract)
			{
				if($fund_source)
				{
					$project_phase_contract = $this->financial_plans->getPhaseContractId($project_phase,$Contract, $this->session->userdata('project_id'));

					$results = $this->financial_plans->getfilter1($project_phase_contract, $fund_source, $this->session->userdata('project_id'));

					$prop_base = 0.0;
					$eac = 0.0;
					foreach ($results as $result)
					{
						$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
						$data['financial_plan_lists'][] = array(
                                'financial_id'                  => $result['financial_id'],
                                'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
                                'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
                                'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
						// 'curr_base'                     => $result['curr_base'],
                                'prop_base'                     => $result['prop_base'],
                                'eac'                           => $result['eac'],
                                'unfunded'                      => $result['unfunded'],
                                'priorfy'                       => $result['priorfy'],
                                'fytodate'                      => $result['fytodate'],
                                'fybalance'                     => $result['fybalance'],
                                'start_date'                    => $result['start_date'],
                                'end_date'                      => $result['end_date'],
																'phase_id'											=> $phase_id,
																'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
						);

						$prop_base = $prop_base + $result['prop_base'];
						$eac = $eac + $result['eac'];

					}

					$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);

					$data['prop_base'] = ($prop_base) ? $prop_base : 0.0;
					$data['eac'] = ($eac) ? $eac : 0.0;

					$this->load->view('common/header', $data);
					$this->load->view('mapping/financial_plan/add', $data);
					$this->load->view('common/footer2');
				}
				else
				{
					$project_phase_contract = $this->financial_plans->getPhaseContractId($project_phase,$Contract, $this->session->userdata('project_id'));

					$results = $this->financial_plans->getfilter2($project_phase_contract, $this->session->userdata('project_id'));

					$prop_base = 0.0;
					$eac = 0.0;
					if (!empty($results)) {
						foreach ($results as $result)
						{
							$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
							$data['financial_plan_lists'][] = array(
	                                'financial_id'                  => $result['financial_id'],
	                                'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
	                                'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
	                                'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
							//'curr_base'                     => $result['curr_base'],
	                                'prop_base'                     => $result['prop_base'],
	                                'eac'                           => $result['eac'],
	                                'unfunded'                      => $result['unfunded'],
	                                'priorfy'                       => $result['priorfy'],
	                                'fytodate'                      => $result['fytodate'],
	                                'fybalance'                     => $result['fybalance'],
	                                'start_date'                    => $result['start_date'],
	                                'end_date'                      => $result['end_date'],
																	'phase_id'											=> $phase_id,
																	'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
							);

							$prop_base = $prop_base + $result['prop_base'];
							$eac = $eac + $result['eac'];
						}

						$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);

					}


					$data['prop_base'] = ($prop_base) ? $prop_base : 0.0;
					$data['eac'] = ($eac) ? $eac : 0.0;

					$this->load->view('common/header', $data);
					$this->load->view('mapping/financial_plan/add', $data);
					$this->load->view('common/footer2');
				}
			}
			else
			{
				if($fund_source)
				{
					$results = $this->financial_plans->getfilter3($fund_source, $this->session->userdata('project_id'));

					$prop_base = 0.0;
					$eac = 0.0;
					foreach ($results as $result)
					{
						$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
						$data['financial_plan_lists'][] = array(
                                'financial_id'                  => $result['financial_id'],
                                'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
                                'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
                                'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
						//'curr_base'                     => $result['curr_base'],
                                'prop_base'                     => $result['prop_base'],
                                'eac'                           => $result['eac'],
                                'unfunded'                      => $result['unfunded'],
                                'priorfy'                       => $result['priorfy'],
                                'fytodate'                      => $result['fytodate'],
                                'fybalance'                     => $result['fybalance'],
                                'start_date'                    => $result['start_date'],
                                'end_date'                      => $result['end_date'],
																'phase_id'											=> $phase_id,
																'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
						);
						$prop_base = $prop_base + $result['prop_base'];
						$eac = $eac + $result['eac'];
					}

					$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);

					$data['prop_base'] = ($prop_base) ? $prop_base : 0.0;
					$data['eac'] = ($eac) ? $eac : 0.0;

					$this->load->view('common/header', $data);
					$this->load->view('mapping/financial_plan/add', $data);
					$this->load->view('common/footer2');
				}

				if($project_phase)
				{
					$phase_to_contracts = $this->projects_phases_contracts->getRowsByPhaseProject($project_phase, $this->session->userdata('project_id'));
					foreach($phase_to_contracts as $phase_to_contract) {
						$phase_contracts[] = $phase_to_contract['project_phase_contract_id'];
					}
					$results = $this->financial_plans->getfilter4($phase_contracts, $this->session->userdata('project_id'));

					$prop_base = 0.0;
					$eac = 0.0;
					if (!empty($results)) {
						foreach ($results as $result)
						{
							$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
							$data['financial_plan_lists'][] = array(
																	'financial_id'                  => $result['financial_id'],
																	'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
																	'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
																	'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
							//'curr_base'                     => $result['curr_base'],
																	'prop_base'                     => $result['prop_base'],
																	'eac'                           => $result['eac'],
																	'unfunded'                      => $result['unfunded'],
																	'priorfy'                       => $result['priorfy'],
																	'fytodate'                      => $result['fytodate'],
																	'fybalance'                     => $result['fybalance'],
																	'start_date'                    => $result['start_date'],
																	'end_date'                      => $result['end_date'],
																	'phase_id'											=> $phase_id,
																	'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
							);
							$prop_base = $prop_base + $result['prop_base'];
							$eac = $eac + $result['eac'];
						}

						$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);
					}


					$data['prop_base'] = ($prop_base) ? $prop_base : 0.0;
					$data['eac'] = ($eac) ? $eac : 0.0;

					$this->load->view('common/header', $data);
					$this->load->view('mapping/financial_plan/add', $data);
					$this->load->view('common/footer2');
				}
			}

			if(!$project_phase && !$Contract && !$fund_source)
			{
				redirect('/financial_plan');
			}
		}

		/*----------------------------------------------------------------------------For Filters End-----------------------------------------------------------------------------*/

		//load the list page view
		if(!$this->input->post('postFilter'))
		{
			$this->load->view('common/header', $data);
			$this->load->view('mapping/financial_plan/add', $data);
			$this->load->view('common/footer2');
		}
	}

	public function edit($id) {
		$data = array();
		$current_fy_year = ( date('m') > 6) ? date('Y') + 1 : date('Y');

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

		/*------------------------------------------Submit Start ---------------------------------------------------------------------------*/

		if($this->input->post('postSubmit')) {
			$data = $this->input->post();
			$recoverdata = $this->financial_plans->getRows($id);

			$postData = array(
			// 'curr_base'                         => ltrim($data['curr_base'], '0'),
                    'prop_base'                         => ltrim($data['prop_base'], '0'),
                    'eac'                               => ltrim($data['eac'], '0'),
                    'unfunded'                          => ltrim($data['unfunded'], '0'),
                    'priorfy'                           => ltrim($data['priorfy'], '0'),
                    'fytodate'                          => ltrim($data['fytodate'], '0'),
                    'fybalance'                         => ltrim($data['fybalance'], '0'),
                  //  'start_date'                        => $data['start_date'],
                  //  'end_date'                          => $data['end_date'],
                    'date_modified'                     => date("m/d/Y G:i:s")
			);

			// if(!$postData["curr_base"])
			// {
			//     $postData["curr_base"] = 0;
			// }

			if(!$postData["prop_base"])
			{
				$postData["prop_base"] = 0;
			}

			if(!$postData["eac"])
			{
				$postData["eac"] = 0;
			}


			$postData["unfunded"] = $this->returnUnfundedamount($postData);

			if(!$postData["priorfy"])
			{
				$postData["priorfy"] = 0;
			}

			if(!$postData["fytodate"])
			{
				$postData["fytodate"] = 0;
			}

			if(!$postData["fybalance"])
			{
				$postData["fybalance"] = 0;
			}
			else if (isset($data['amount'])) {
				for ($i=0; $i < count($data['amount']); $i++) {
					if ($data['year'][$i] == $current_fy_year) {
						$postData['fybalance'] += $data['amount'][$i];
					}
				}
			}

			$postDataCheck['project_id'] = $recoverdata['project_id'];
			$postDataCheck['project_phase_contract_id'] = $recoverdata['project_phase_contract_id'];
			$postDataCheck['fund_id'] = $recoverdata['fund_id'];
			$postDataCheck = array_merge($postDataCheck, $postData);

			if (!$this->validateFinancialPlan($postDataCheck, $id)) {
				$financial_id = $this->financial_plans->update($postData,$id);

				if($recoverdata['prop_base']!=$data['prop_base'] || $recoverdata['eac']!=$data['eac'] || $recoverdata['unfunded']!=$data['unfunded'] || $recoverdata['priorfy']!=$data['priorfy'] || $recoverdata['fytodate']!=$data['fytodate'] || $recoverdata['fybalance']!=$data['fybalance'])
				//if($recoverdata['curr_base']!=$data['curr_base'] || $recoverdata['prop_base']!=$data['prop_base'] || $recoverdata['eac']!=$data['eac'] || $recoverdata['unfunded']!=$data['unfunded'] || $recoverdata['priorfy']!=$data['priorfy'] || $recoverdata['fytodate']!=$data['fytodate'] || $recoverdata['fybalance']!=$data['fybalance'] || $recoverdata['start_date']!=$data['start_date'] || $recoverdata['end_date']!=$data['end_date'])
				{
					$postDataversion = array(
                    'financial_id'                      => $id,
                    'project_id'                        => $this->session->userdata('project_id'),
                    'project_phase_contract_id'         => $recoverdata['project_phase_contract_id'],
                    'fund_id'                           => $recoverdata['fund_id'],
					// 'curr_base'                         => ltrim($data['curr_base'], '0'),
                    'prop_base'                         => ltrim($data['prop_base'], '0'),
                    'eac'                               => ltrim($data['eac'], '0'),
                    'unfunded'                          => ltrim($data['unfunded'], '0'),
                    'priorfy'                           => ltrim($data['priorfy'], '0'),
                    'fytodate'                          => ltrim($data['fytodate'], '0'),
                    'fybalance'                         => ltrim($data['fybalance'], '0'),
                  //  'start_date'                        => $data['start_date'],
                  //  'end_date'                          => $data['end_date'],
                    'date_added'                        => date("m/d/Y G:i:s")
					);

					$financial_plan_version_id = $this->financial_plans->addversion($postDataversion);
				}

				if(isset($data['amount']))
				{
					$this->financial_plans->deleteyears($id);

					for ($i=0; $i < count($data['amount']); $i++)
					{
						if ($data['year'][$i] != $current_fy_year) {
							$postyear = array(
	                        'financial_id'      => $id,
	                        'project_id'        => $this->session->userdata('project_id'),
	                        'financial_years'   => $data['year'][$i],
	                        'amount'            => $data['amount'][$i]
							);

							$insert = $this->financial_plans->addyear($postyear);
						}
					}
				}

				if($financial_id){
					$this->session->set_userdata('success_msg', 'Financial Plan has been Updated successfully.');
					redirect('/financial_plan');
				}else{
					$data['error_msg'] = 'Some problems occurred, please try again.';
				}

			} else {
				if(!$this->session->userdata('project_id')) {
					redirect('/project');
				} else {
					$data["project_name"] = $this->financial_plans->getProjectName($this->session->userdata('project_id'));
				}

				$error_msg = $this->validateFinancialPlan($postDataCheck, $id);
				$data['error_msg'] = $error_msg;
			}

		}

		/*------------------------------------------Submit End ---------------------------------------------------------------------------*/

		if(!$this->input->post('postFilter')) {
			$results = $this->financial_plans->getRows();
			foreach ($results as $result) {
				$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
				$data['financial_plan_lists'][] = array(
                    'financial_id'                  => $result['financial_id'],
                    'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
                    'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
                    'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
				//'curr_base'                     => $result['curr_base'],
                    'prop_base'                     => $result['prop_base'],
                    'eac'                           => $result['eac'],
                    'unfunded'                      => $result['unfunded'],
                    'priorfy'                       => $result['priorfy'],
                    'fytodate'                      => $result['fytodate'],
                    'fybalance'                     => $result['fybalance'],
                    'start_date'                    => $result['start_date'],
                    'end_date'                      => $result['end_date'],
										'phase_id'											=> $phase_id,
										'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
				);
			}

			$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);
		}

		$prop_base = $this->financial_plans->prop_base($this->session->userdata('project_id'));
		$data['prop_base'] = ($prop_base) ? $prop_base : 0.0;

		$eac = $this->financial_plans->eac($this->session->userdata('project_id'));
		$data['eac'] = ($eac) ? $eac : 0.0;

		$data['title'] = 'Financial Plan Edit';

		$postData = $this->financial_plans->getRows($id);
		$phase_to_contract = $this->projects_phases_contracts->getRows($postData['project_phase_contract_id']);

		$postData['project_phase'] = $this->financial_plans->getPhaseName1($postData['project_phase_contract_id']);
		$postData['contract_name'] = $this->financial_plans->getContractName($postData['project_phase_contract_id']);
		$postData['fund'] = $this->financial_plans->getFundName($postData['fund_id']);
		$postData['start_date'] = $phase_to_contract['contract_start_date'];
		$postData['end_date'] = $phase_to_contract['contract_end_date'];
		$data['post'] = $postData;

		$postYears = $this->financial_plans->getyears($id);

		$data['financial_year_sum'] = $this->financial_plans->get_year_wise_totolamount($id); /*print_r($data['financial_year_sum']);exit();*/

		if(!isset($data['financial_year_sum'])) {
			$data['financial_year_sum'] = 0;
		}

		if($postYears) {
			$data["count"] = count($postYears);

			foreach ($postYears as $postYear) {
				$data['financial_years'][] = array(
                    'years_id'          => $postYear['years_id'],
                    'financial_years'   => $postYear['financial_years'],
                    'amount'            => $postYear['amount'],
				);
			}

			$data['postyear'] = $postYear;
		}
		else {
			$data["count"] = 0;
		}

		$phase_lists = $this->financial_plans->getPhaseList();

		foreach ($phase_lists as $phase_list) {
			$data['phases'][] = array(
                'phase_id'      => $phase_list['phase_id'],
                'phase_name'    => $phase_list['phase_name']
			);
		}

		$fund_lists = $this->financial_plans->getFundList();

		foreach ($fund_lists as $fund_list) {
			$data['funds'][] = array(
                'fund_id'      => $fund_list['fund_id'],
                'fund_name'    => $fund_list['fund_name']
			);
		}

		/*---------------------------------------------------------------------------For Filters Start----------------------------------------------------------------------------*/

		if($this->input->post('postFilter')) {
			$project_phase = $this->input->post('phasefilter');
			$Contract = $this->input->post('contractfilter');
			$fund_source = $this->input->post('fundfilter');

			if($project_phase && $Contract)
			{
				if($fund_source)
				{
					$project_phase_contract = $this->financial_plans->getPhaseContractId($project_phase,$Contract, $this->session->userdata('project_id'));

					$results = $this->financial_plans->getfilter1($project_phase_contract, $fund_source);

					foreach ($results as $result)
					{
						$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
						$data['financial_plan_lists'][] = array(
                                'financial_id'                  => $result['financial_id'],
                                'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
                                'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
                                'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
						//'curr_base'                     => $result['curr_base'],
                                'prop_base'                     => $result['prop_base'],
                                'eac'                           => $result['eac'],
                                'unfunded'                      => $result['unfunded'],
                                'priorfy'                       => $result['priorfy'],
                                'fytodate'                      => $result['fytodate'],
                                'fybalance'                     => $result['fybalance'],
                                'start_date'                    => $result['start_date'],
                                'end_date'                      => $result['end_date'],
																'phase_id'											=> $phase_id,
																'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
						);
					}

					$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);

					$this->load->view('common/header', $data);
					$this->load->view('mapping/financial_plan/edit', $data);
					$this->load->view('common/footer2');
				}
				else
				{
					$project_phase_contract = $this->financial_plans->getPhaseContractId($project_phase,$Contract, $this->session->userdata('project_id'));

					$results = $this->financial_plans->getfilter2($project_phase_contract);

					foreach ($results as $result)
					{
						$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
						$data['financial_plan_lists'][] = array(
                                'financial_id'                  => $result['financial_id'],
                                'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
                                'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
                                'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
						//'curr_base'                     => $result['curr_base'],
                                'prop_base'                     => $result['prop_base'],
                                'eac'                           => $result['eac'],
                                'unfunded'                      => $result['unfunded'],
                                'priorfy'                       => $result['priorfy'],
                                'fytodate'                      => $result['fytodate'],
                                'fybalance'                     => $result['fybalance'],
                                'start_date'                    => $result['start_date'],
                                'end_date'                      => $result['end_date'],
																'phase_id'											=> $phase_id,
																'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
						);
					}

					$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);

					$this->load->view('common/header', $data);
					$this->load->view('mapping/financial_plan/edit', $data);
					$this->load->view('common/footer2');
				}
			}
			else
			{
				if($fund_source)
				{
					$results = $this->financial_plans->getfilter3($fund_source);

					foreach ($results as $result)
					{
						$phase_id = $this->financial_plans->getPhaseId($result['project_phase_contract_id']);
						$data['financial_plan_lists'][] = array(
                                'financial_id'                  => $result['financial_id'],
                                'project_phase'                 => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
                                'contract'                      => $this->financial_plans->getContractName($result['project_phase_contract_id']),
                                'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
						//'curr_base'                     => $result['curr_base'],
                                'prop_base'                     => $result['prop_base'],
                                'eac'                           => $result['eac'],
                                'unfunded'                      => $result['unfunded'],
                                'priorfy'                       => $result['priorfy'],
                                'fytodate'                      => $result['fytodate'],
                                'fybalance'                     => $result['fybalance'],
                                'start_date'                    => $result['start_date'],
                                'end_date'                      => $result['end_date'],
																'phase_id'											=> $phase_id,
																'weight'												=> $this->financial_plans->getPhaseWeight($phase_id),
						);
					}

					$data['financial_plan_lists'] = $this->sortPhaseFinancialPlans($data['financial_plan_lists']);

					$this->load->view('common/header', $data);
					$this->load->view('mapping/financial_plan/edit', $data);
					$this->load->view('common/footer2');
				}
			}

			if(!$project_phase && !$Contract && !$fund_source)
			{
				redirect('/financial_plan');
			}
		}

		/*----------------------------------------------------------------------------For Filters End-----------------------------------------------------------------------------*/

		if(!$this->input->post('postFilter'))
		{
			$this->load->view('common/header', $data);
			$this->load->view('mapping/financial_plan/edit', $data);
			$this->load->view('common/footer2');
		}
	}

	public function delete($id){
		//check whether post id is not empty
		if($id){
			$expended_amount = 0;
			$financial_plan = $this->financial_plans->getRows($id);
			if (!empty($financial_plan)) {
				$expended_amount = $financial_plan['priorfy'] + $financial_plan['fytodate'];
				if ($expended_amount) {
					// reset available amount of project fund and master fund.
					$project_fund = $this->project_funds->getRowsByProjectFund($financial_plan['project_id'], $financial_plan['fund_id']);
					$project_update = [
						'available_amount' => $project_fund['available_amount'] + $expended_amount,
					];
					$master_fund = $this->funds->getRows($financial_plan['fund_id']);
					$master_update = [
						'available_amount' => $master_fund['available_amount'] + $expended_amount,
					];
					$project_result = $this->project_funds->update($project_update, $project_fund['project_fund_id']);
					$master_result = $this->funds->update($master_update, $master_fund['fund_id']);
				}
			}
			//delete post
			$delete = $this->financial_plans->delete($id);

			$delete = $this->financial_plans->deleteyears($id);

			if($delete){
				$this->session->set_userdata('success_msg', 'Financial Plan has been removed successfully.');
			}else{
				$this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
			}
		}

		redirect('/financial_plan');
	}

	public function contractlist() {
		if(!$this->session->userdata('project_id')) {
			redirect('/project');
		} else {
			$project_id = $this->session->userdata('project_id');
		}
		$phase_id = $this->input->post('phase_id');
		$result = $this->financial_plans->contractlist($phase_id, $project_id);

		echo json_encode($result);
	}

	public function check() {
		$phase_id = $this->input->post('phase_id');
		$contract_name = $this->input->post('contract');
		$fund_id = $this->input->post('fund_id');
		$phase_contract_id = $this->financial_plans->getPhaseContractId($phase_id,$contract_name, $this->session->userdata('project_id'));
		$result = $this->financial_plans->checkPhaseContractIdToFunds($fund_id, $phase_contract_id, $this->session->userdata('project_id'));

		echo json_encode($result);
	}

	public function verify(){
		$fund_id = $this->input->post('fund_id');
		$project_id = $this->session->userdata('project_id');
		$result = $this->financial_plans->returnfundamount($fund_id, $project_id);
		echo json_encode($result);
	}

	public function history() {
		$financial_id = $this->input->post('financial_id');
		$results = $this->financial_plans->history($financial_id);
		foreach ($results as $result) {
			$data['history'][] = [
        'financial_plan_version_id'     => $result['financial_plan_version_id'],
        'financial_id'                  => $result['financial_id'],
        'project_id'                    => $result['project_id'],
        'phase_name'                    => $this->financial_plans->getPhaseName1($result['project_phase_contract_id']),
        'contract_name'                 => $this->financial_plans->getContractName($result['project_phase_contract_id']),
        'fund_name'                     => $this->financial_plans->getFundName($result['fund_id']),
        'prop_base'                     => $result['prop_base'],
        'eac'                           => $result['eac'],
        'unfunded'                      => $result['unfunded'],
        'priorfy'                       => $result['priorfy'],
        'fytodate'                      => $result['fytodate'],
        'fybalance'                     => $result['fybalance'],
        'date_added'                    => $result['date_added'],
			];
		}

		echo json_encode($data['history']);
	}

	public function financial_year()
	{
		$financial_id = $this->input->post('financial_id');

		$result = $this->financial_plans->getyears($financial_id);

		echo json_encode($result);
	}

	protected function returnUnfundedamount($data) {
		if (is_numeric($data['priorfy']) && is_numeric($data['fytodate']) && is_numeric($data['fybalance']) && is_numeric($data['prop_base'])) {
			$unfunded = ($data['priorfy'] + $data['fytodate'] + $data['fybalance']) - $data['prop_base'];
			if ($unfunded > 0) {
				return $unfunded;
			}
		}

		return 0;
	}

	protected function validateFinancialPlan($postData, $edit = FALSE) {
		$error_msg = '';
		$input = $this->input->post();
		$current_fy_year = ( date('m') > 6) ? date('Y') + 1 : date('Y');
		$fund = $this->funds->getRows($postData['fund_id']);
		if ($fund['fund_code'] == 'TBD') { // validation for unfunded resource.
			$total_project_cost = 0.0;
			$project_data = $this->projects->getRows($this->session->userdata('project_id'));
			if (!empty($project_data)) {
				$total_project_cost = $project_data['total_project_cost'];
			}

			$budget = 0.0;
			$curr_budget = $this->project_funds->budget($this->session->userdata('project_id'));
			if (!empty($curr_budget) && $curr_budget > 0) {
				$budget = $curr_budget;
			}

			$fund_allocated_to_project['allocated_amount'] = $total_project_cost - $budget;
			if (!$fund_allocated_to_project['allocated_amount']) {
				$fund_allocated_to_project['allocated_amount'] = 0.0;
			}

			$total_fund_allocated_to_contracts = $this->financial_plans->getTotalFundAllocatedToContracts($postData['project_id'], $postData['fund_id']);
			if (!$total_fund_allocated_to_contracts) {
				$total_fund_allocated_to_contracts = 0.0;
			}

			$new_allocated_to_contracts = $total_fund_allocated_to_contracts + $postData['eac'];
		}
		else { //Validation for fund source.
			//Level 01 validation : Fund allocation to project should greater than distributed amount to contracts.
			$fund_allocated_to_project = $this->project_funds->getRowsByProjectFund($postData['project_id'], $postData['fund_id']);
			$total_fund_allocated_to_contracts = $this->financial_plans->getTotalFundAllocatedToContracts($postData['project_id'], $postData['fund_id']);
			if (!$total_fund_allocated_to_contracts) {
				$total_fund_allocated_to_contracts = 0.0;
			}
			if (!$fund_allocated_to_project['allocated_amount']) {
				$fund_allocated_to_project['allocated_amount'] = 0.0;
			}
			if (!$fund_allocated_to_project['available_amount']) {
				$fund_allocated_to_project['allocated_amount'] = 0.0;
			}
			$new_allocated_to_contracts = $total_fund_allocated_to_contracts + $postData['eac'];

		}

		if ($edit) {
			$recoveredData = $this->financial_plans->getRows($edit);
			$new_allocated_to_contracts = $total_fund_allocated_to_contracts;
			if ($postData['eac'] != $recoveredData['eac']) {
				$delta = $postData['eac'] - $recoveredData['eac'];
				$new_allocated_to_contracts = $total_fund_allocated_to_contracts + $delta;
			}
		}


		// Implement validation rule.
		if ($fund_allocated_to_project['allocated_amount'] < $new_allocated_to_contracts) {
			$error_msg = "Financial plan failed due to minimal fund allocated to project, Please increase project allocation amount.";
			return $error_msg;
		} else {
			// Validation rule 02 - Allocation should be consistent.
			$yw_paid_amount = $postData['priorfy'] + $postData['fytodate'];
			$fy_future_amount = 0.0;

			if(isset($input['amount'])) {
				for ($i=0; $i < count($input['amount']); $i++) {
					if ($input['year'][$i] != $current_fy_year) {
						$fy_future_amount = $fy_future_amount + $input['amount'][$i];
					}
				}
			}
			$fy_balance_amount = $postData['fybalance'];
			$total_dist_amount = $yw_paid_amount + $fy_balance_amount + $fy_future_amount;
			if ($postData['eac'] < $total_dist_amount) {
				$error_msg = "Financial plan failed due to exceeding planned allocation amount, Please increase EAC amount.";
				return $error_msg;
			} else {
				if ($fund['fund_code'] != 'TBD') {
					$fund_new_available_to_project = $fund_allocated_to_project['available_amount'] - $yw_paid_amount;
					if (($fund_new_available_to_project < 0) && (!$edit)) {
						$error_msg = "Sufficient funds are not available, please increase project allocation amount.";
						return $error_msg;
					} else { // All validation passes, now good to proceed.
						if (!$edit) {
							$update = array(
						'available_amount' => $fund_new_available_to_project,
						'date_modified' => date("m/d/Y G:i:s"),
							);

							// Get update master array.
							$master_fund = $this->funds->getRows($fund_allocated_to_project['fund_id']);
							if (!empty($master_fund)) {
								$fund_new_available_to_master = $master_fund['available_amount'] - $yw_paid_amount;
								if (($fund_new_available_to_master < 0) && (!$edit)) {
									$error_msg = "Sufficient funds are not available, please increase project allocation amount.";
									return $error_msg;
								} else {
									$master_update = [
										'available_amount' => $fund_new_available_to_master,
										'date_modified' => date("m/d/Y G:i:s"),
									];
								}

							}
							$pf_updated = $this->project_funds->update($update, $fund_allocated_to_project['project_fund_id']);
							$f_updated = $this->funds->update($master_update, $fund_allocated_to_project['fund_id']);
						}
					}
				} else { //operation for unfunded financial plan.
					// All validation passes, now good to proceed.
					// print_r($new_allocated_to_contracts); exit;
					//if (!$edit) {
					$update = array(
							'allocated_amount' => $new_allocated_to_contracts,
							'available_amount' => $new_allocated_to_contracts,
							'date_modified' => date("m/d/Y G:i:s"),
					);
					//$pf_updated = $this->project_funds->update($update, $fund_allocated_to_project['project_fund_id']);
					$f_updated = $this->funds->update($update, $fund['fund_id']);
					//}
				}
			}
		}

		// Validate contract amount.
		$phase_to_contract_financial_amount = $input['eac'];
		$phase_to_contract_allocated_amount = $this->projects_phases_contracts->getRows($postData['project_phase_contract_id'])['amount'];
		if ($edit) {
			$phase_to_contract_financial_amount += $this->financial_plans->getRemainSumByPhaseToContract($postData['project_phase_contract_id'], $recoveredData['financial_id'])['eac'];
		} else {
			$phase_to_contract_financial_amount += $this->financial_plans->getSumByPhaseToContract($postData['project_phase_contract_id'])['eac'];
		}

		if ($phase_to_contract_allocated_amount < $phase_to_contract_financial_amount) {
			$error_msg = "Amount allocated to contracts are not sufficient, please increase contract allocation amount.";
			return $error_msg;
		}

		return FALSE;
	}

	public function sortPhaseFinancialPlans ($financial_plans) {
		$output = [];
		if (!empty($financial_plans)) {
			foreach ($financial_plans as $key => $row) {
				 $output[$key] = $row['weight'];
			}
			array_multisort($output, SORT_ASC, $financial_plans);
		}
		return $financial_plans;
	}

	public function cron() {
		$current_fy_year = ( date('m') > 6) ? date('Y') + 1 : date('Y');
		$financial_plans = $this->financial_plans->getRows();

		if (!empty($financial_plans)) {
			foreach ($financial_plans as $financial_plan) {
				$years = $this->financial_plans->getyears($financial_plan['financial_id']);

				if (!empty($years)) {
					foreach ($years as $year) {
						if ($year['financial_years'] == $current_fy_year) {
							$financial_plan['fybalance'] = $financial_plan['fybalance'] + $year['amount'];
							$this->financial_plans->deleteyearbyfy($financial_plan['financial_id'], $year['financial_years']);
						}
					}
				}

				if ($financial_plan['fytodate'] != 0) {
					$financial_plan['priorfy'] = $financial_plan['priorfy'] + $financial_plan['fytodate'];
					$financial_plan['fytodate'] = 0;
				}

				$this->financial_plans->update($financial_plan, $financial_plan['financial_id']);
				//$this->financial_plans->addversion($financial_plan);
			}
		}
	}
}
