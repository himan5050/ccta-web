<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

/**
 * @file
 * Helper functions for Project Phase Contract Controller.
 * @var  [type]
 */
if (!function_exists('validate_project_phase_contract')) {
    /**
     * Validate project phase contract.
     *
     * @param array $data
     *  Input data poster by user.
     * @param int   $id
     *  The project phase contract id.
     *
     * @return boolean
     *  Return false if validation passes,
     *  else return a message string.
     */
    function validate_project_phase_contract($project_id, $data, $id, $modelProjectPhaseContract, $modelProjects, $modelFinancialPlans)
    {
        $input_amount = 0;
        $total_amount = 0;
        $new_total_amount = 0;
        $total_amount = $modelProjectPhaseContract->getTotalAmount($project_id);
        $project = $modelProjects->getRows($project_id);
        $total_project_cost = isset($project['total_project_cost']) ? $project['total_project_cost'] : 0;
        if ($id == 'add') {
            for ($i=0; $i < count($data['contract']); $i++) {
                // validatation on start and end dates.
                $start_diff = strtotime($data['start_date'][$i]) - strtotime($project['start_date']);
                $end_diff = strtotime($project['end_date']) - strtotime($data['end_date'][$i]);
                if ($start_diff <= 0) { // start date is before project start date.
                    return 2;
                }
                if ($end_diff <= 0) { // End date is beyong project end date.
                    return 3;
                }
                $input_amount = $input_amount + $data['amount'][$i];
            }
            $new_total_amount = $total_amount + $input_amount;
        } else {
            // validatation on start and end dates.
            $start_diff = strtotime($data['start_date']) - strtotime($project['start_date']);
            $end_diff = strtotime($project['end_date']) - strtotime($data['end_date']);
            if ($start_diff <= 0) { // start date is before project start date.
                return 2;
            }
            if ($end_diff <= 0) { // End date is beyong project end date.
                return 3;
            }
            $recoverdata = $modelProjectPhaseContract->getRows($id);
            if ($data['amount'] != $recoverdata['amount']) {
                $diff = $data['amount'] - $recoverdata['amount'];
                $new_total_amount = $total_amount + $diff;

                // check with financial plans.
                $financial_plans = $modelFinancialPlans->getfilter2($id, $project_id);
                $contract_allocated_plans = 0;
                if (!empty($financial_plans)) {
                    foreach ($financial_plans as $financial_plan) {
                        $contract_allocated_plans = $contract_allocated_plans + $financial_plan['prop_base'];
                    }
                }
                //validation..
                if ($contract_allocated_plans > $data['amount']) {
                    return 4;
                }
            } else {
                return 1;
            }
        }

        if ($new_total_amount <= $total_project_cost) {
            return 1;
        }

        return 0;
    }
}

/**
 * [if description]
 *
 * @var [type]
 */
if (!function_exists('get_phase_contract_state')) {
    /**
     * [get_phase_contract_state description]
     *
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    function get_phase_contract_state($status)
    {
        switch ($status) {
        case 0:
            return 'Closed';
        case 1:
            return 'Expiring';
        case 2:
            return 'Expired';
        case 3:
            return 'Active';
        default:
            return 'Closed';
        }
    }
}

/**
 * [if description]
 *
 * @var [type]
 */
if (!function_exists('sort_project_phase_contract_list')) {
    /**
     * [get_phase_contract_state description]
     *
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    function sort_project_phase_contract_list($projects_phases_contracts)
    {
        $output = [];
        if (!empty($projects_phases_contracts)) {
            foreach ($projects_phases_contracts as $key => $row) {
                 $output[$key] = $row['weight'];
            }
            array_multisort($output, SORT_ASC, $projects_phases_contracts);
        }
        return $projects_phases_contracts;
    }
}

/**
 * [if description]
 *
 * @var [type]
 */
if (!function_exists('set_phase_contract_state')) {
    /**
     * [get_phase_contract_state description]
     *
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    function set_phase_contract_state($start_date, $end_date, $renew = 'false')
    {
        if (is_validate_dates($start_date, $end_date)) {
            $now = date_create(date('m/d/Y'));
            $end = date_create($end_date);
            //print_r($now); exit;
            if (is_validate_dates(date('m/d/Y'), $end_date)) {
                $remaining_days = date_diff($now, $end)->days;
                if ($remaining_days > 90) {
                    return 3; // Is Active.
                } else if (($remaining_days <= 90) && ($remaining_days > 0)) {
                    return 1; // Expiring;
                } else if ($remaining_days <= 0) {
                    return 2; // Expired.
                } else {
                    return 2; // Expired.
                }
            } else {
                return 2; //Expired.
            }
        } else {
            $error_msg = 'Please enter valid dates, End date can not behind Start date.';
            return $error_msg;
        }
    }
}

/**
 * [if description]
 *
 * @var [type]
 */
if (!function_exists('is_validate_dates')) {
    /**
     * [get_phase_contract_state description]
     *
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    function is_validate_dates($start_date, $end_date)
    {
        $start = date('Y-m-d', strtotime($start_date));
        $end = date('Y-m-d', strtotime($end_date));
        if ($start > $end) {
            return false;
        }
        return true;
    }
}
