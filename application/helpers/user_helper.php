<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');}

/**
 * @file
 * Helper functions for User controller.
 * @var  [type]
 */
if (!function_exists('check_existuser')) {
  function check_existuser($valid, $input, $form_validation, $session) {
    if ($valid) {
      $userdata = [
        'username' => $input->post('username'),
        'is_loged_in' => 1,
        'role' => $valid
      ];

      $session->set_userdata($userdata);

      return TRUE;
    } else {
      $form_validation->set_message("check_existuser", "Invalid user");
    }
  }
}
