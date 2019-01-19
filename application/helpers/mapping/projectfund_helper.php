<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file
 * Helper functions for Project Fund Controller.
 * @var [type]
 */
if (!function_exists('validate_edit_project')) {
  /**
   * Validate edit handler of project info.
   *
   * @param  array  $postData
   *  Input data poster by user.
   *
   * @return boolean
   *  Return false if validation passes,
   *  else return a message string.
   */
  function validate_edit_project_fund($project_id, $project_fund_id, $inputData = [], $modelProjectFunds, $modelFunds, $modelFinancialPlans) {

    $projectFunds = $modelProjectFunds->getRows($project_fund_id);
    $projectFunds['fund_name'] = $modelFunds->getRows($projectFunds['fund_id']);
    $new_total_project_fund = isset($inputData) ? $inputData['allocated_amount'] : 0;
    // Get total amount of financial plans.
    $financial_plan_sum = $modelFinancialPlans->getFundSubTotal($projectFunds['fund_id'], $project_id);

    if (($new_total_project_fund < $financial_plan_sum)) {
        $error_msg = 'Total project cost is not valid.';
        return $error_msg;
    }

    return FALSE;
  }
}

if (!function_exists('cleanup_process')) {
  /**
   * Clean up data before deleting project.
   *
   * @param  array  $project_id
   *  The project id.
   *
   * @return boolean
   *  Return false if fails.
   *  else return true.
   */
  function cleanup_process($project_id, $modelProjectFunds, $modelFunds) {
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
