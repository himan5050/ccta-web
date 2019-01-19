<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Default controller.
$route['default_controller'] = 'dashboard';

// Master project module.
$route['project/view/(:num)'] = 'master/project/view/$1';
$route['project/add'] = 'master/project/add';
$route['project/edit/(:num)'] = 'master/project/edit/$1';
$route['project/delete/(:num)'] = 'master/project/delete/$1';
$route['project/check'] = 'master/project/check';
$route['project/history'] = 'master/project/history';
$route['project'] = 'master/project';

// Master phase module.
$route['phase/view/(:num)'] = 'master/phase/view/$1';
$route['phase/add'] = 'master/phase/add';
$route['phase/edit/(:num)'] = 'master/phase/edit/$1';
$route['phase/delete/(:num)'] = 'master/phase/delete/$1';
$route['phase/check'] = 'master/phase/check';
$route['phase'] = 'master/phase';

// Master fund module.
$route['fund/view/(:num)'] = 'master/fund/view/$1';
$route['fund/add'] = 'master/fund/add';
$route['fund/edit/(:num)'] = 'master/fund/edit/$1';
$route['fund/delete/(:num)'] = 'master/fund/delete/$1';
$route['fund/check'] = 'master/fund/check';
$route['fund/history'] = 'master/fund/history';
$route['fund'] = 'master/fund';

// Invoice module.
$route['invoice'] = 'master/invoice';
$route['invoice/recall/(:num)'] = 'master/invoice/edit/$1';
$route['invoice/discard/(:num)'] = 'master/invoice/discard/$1';
$route['invoice/history'] = 'master/invoice/history';

// Project fund module.
$route['project_funds'] = 'mapping/project_fund';
$route['project_funds/edit/(:num)'] = 'mapping/project_fund/edit/$1';
$route['project_funds/delete/(:num)'] = 'mapping/project_fund/delete/$1';
$route['project_funds/history'] = 'mapping/project_fund/history';

// Project phase contract module.
$route['project_phase_contract/view/(:num)'] = 'mapping/project_phase_contract/view/$1';
$route['project_phase_contract/edit/(:num)'] = 'mapping/project_phase_contract/edit/$1';
$route['project_phase_contract/delete/(:num)'] = 'mapping/project_phase_contract/delete/$1';
$route['project_phase_contract/disable/(:num)'] = 'mapping/project_phase_contract/disable/$1';
$route['project_phase_contract/add'] = 'mapping/project_phase_contract/add';
$route['project_phase_contract/check'] = 'mapping/project_phase_contract/check';
$route['project_phase_contract/history'] = 'mapping/project_phase_contract/history';
$route['project_phase_contract/checkedit'] = 'mapping/project_phase_contract/checkedit';
$route['project_phase_contract'] = 'mapping/project_phase_contract';

// Finance plan module.
$route['financial_plan/view/(:num)'] = 'mapping/financial_plan/view/$1';
$route['financial_plan/edit/(:num)'] = 'mapping/financial_plan/edit/$1';
$route['financial_plan/delete/(:num)'] = 'mapping/financial_plan/delete/$1';
$route['financial_plan/add'] = 'mapping/financial_plan/add';
$route['financial_plan'] = 'mapping/financial_plan';
$route['financial_plan/contractlist'] = 'mapping/financial_plan/contractlist';
$route['financial_plan/check'] = 'mapping/financial_plan/check';
$route['financial_plan/verify'] = 'mapping/financial_plan/verify';
$route['financial_plan/history'] = 'mapping/financial_plan/history';
$route['financial_plan/financial_year'] = 'mapping/financial_plan/financial_year';
$route['financial_plan/cron'] = 'mapping/financial_plan/cron';


// Project reports module.
$route['report1'] = 'reports/report1';
$route['export/report1'] = 'reports/report1/export';
$route['report2'] = 'reports/report2';
$route['export/report2'] = 'reports/report2/export';
$route['report3'] = 'reports/report3';
$route['export/report3'] = 'reports/report3/export';

// Master reports module.
$route['report'] = 'reports/master_reports/report';
$route['report/fy_report'] = 'reports/master_reports/fy_report';
