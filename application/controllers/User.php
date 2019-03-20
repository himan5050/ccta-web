<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User controller.
 */
class User extends CI_Controller {

  protected $calledClass ;
  protected $calledMethod;
  protected $isAuthException;
  
  /**
   * Login handler.
   */
  public function login() {
    $data = [];
    $data['title'] = 'Ficus Dashboard';

    if(!$this->session->userdata('username')) {
      //redirect();
    } else {
        $data['username'] = $this->session->userdata('username');
        redirect('dashboard');
    }

    if ($this->input->post('userLogin')) {
      $this->form_validation->set_rules('username','User Name','trim|required');
      $this->form_validation->set_rules('password','Password','trim|required|callback_check_existuser');
      if ($this->form_validation->run() == FALSE) {
      } else {
        $this->load->model('Users');
        $valid = $this->Users->valid_user();
        $this->load->helper('user');
        if($this->check_existuser()) {
          redirect('dashboard');
        }
      }
    }

    $this->load->view('common/header', $data);
    $this->load->view('user/login');
    $this->load->view('common/footer');
  }

  public function check_existuser() {
    $this->load->model('Users');
    $valid = $this->Users->valid_user();
    if ($valid) {
      $userdata = [
        'username' => $this->input->post('username'),
        'is_loged_in' => 1,
        'role' => $valid
      ];

      $this->session->set_userdata($userdata);

      return TRUE;
    } else {
      $this->form_validation->set_message("password", "Invalid user");
    }
  }

  public function logout() {
    $user_data = $this->session->all_userdata();
    foreach ($user_data as $key => $value) {
      $this->session->unset_userdata($key);
    }

    $this->session->sess_destroy();
    redirect('user/login');
  }
}
