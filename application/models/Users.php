<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');}

class Users extends CI_Model {

  public function valid_user() {
    $this->db->select('role');
    $this->db->where('username',$this->input->post('username'));
    $this->db->where('password', md5($this->input->post('password')));
    $result = $this->db->get('users');

    if ($result->num_rows() == 1) {
      $role = $result->row('role');
      return $role;
    } else {
      return FALSE;
    }
  }
}
