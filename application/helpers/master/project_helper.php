<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

/**
 * @file
 * Helper functions for Master Project controller.
 * @var  [type]
 */
if (!function_exists('validate_edit_project')) {
    /**
     * Validate edit handler of project info.
     *
     * @param array $postData
     *  Input data poster by user.
     *
     * @return boolean
     *  Return false if validation passes,
     *  else return a message string.
     */
    function validate_edit_project($project_id, $postData = [], $modelFinancialPlans, $modelFunds, $modelProjectPhaseContract)
    {
        $new_total_project_cost = isset($postData) ? $postData['total_project_cost'] : 0;
        // Get total amount of financial plans.
        $financial_plan_sum = $modelFinancialPlans->getSumRows($project_id);
        // Get total amount of allocated funds.
        $funds_sum = $modelFunds->getSumRowsByProject($project_id);
        // Get total amount in project phase contract.
        $project_phase_contract_sum = $modelProjectPhaseContract->getSumRowsByProject($project_id);

        if (($new_total_project_cost < $financial_plan_sum) || ($new_total_project_cost < $funds_sum)
            || ($new_total_project_cost < $project_phase_contract_sum) 
        ) {
            $error_msg = 'Total project cost is not valid.';
            return $error_msg;
        }

        return false;
    }
}

if (!function_exists('cleanup_process')) {
    /**
     * Clean up data before deleting project.
     *
     * @param array $project_id
     *  The project id.
     *
     * @return boolean
     *  Return false if fails.
     *  else return true.
     */
    function cleanup_process($project_id, $modelProjectFunds, $modelFunds)
    {
        $projectfunds = $modelProjectFunds->getRowsByProject($project_id);
        if (!empty($projectfunds)) {
            foreach ($projectfunds as $projectfund) {
                $masterfund = $this->funds->getRows($projectfund['fund_id']);
                $remain_allocated_amount = $masterfund['allocated_amount'] - $projectfund['allocated_amount'];
                $remain_available_amount = $masterfund['available_amount'] - $projectfund['available_amount'];
                if (($remain_allocated_amount >= 0) && ($remain_available_amount >= 0)) {
                    $update_master = [
                    'allocated_amount' => $remain_allocated_amount,
                    'available_amount' => $remain_available_amount,
                    'date_modified' => date("m/d/Y G:i:s")
                    ];
                    $update = $modelFunds->update($update_master, $projectfund['fund_id']);
                    if (!$update) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
