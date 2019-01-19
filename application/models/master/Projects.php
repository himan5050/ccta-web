<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file
 * Model of Master Project Info.
 */
class Projects extends CI_Model {

  /**
   * Get rows of projects.
   *
   * @param  int $id
   *  The project id.
   *
   * @return array
   *  List of Projects.
   */
  public function getRows($id = "") {
    if (!empty($id)) {
      $query = $this->db->get_where('master_project', array('project_id' => $id));
      return $query->row_array();
    } else {
      $query = $this->db->get('master_project');
      return $query->result_array();
    }
  }

  /**
   * Add Project.
   *
   * @param array $data
   *  Array containing project info.
   *
   * @return boolean
   *  TRUE if added else FALSE.
   */
  public function add($data = []) {
    $insert = $this->db->insert('master_project', $data);
    if ($insert) {
      return $this->db->insert_id();
    } else {
      return FALSE;
    }
  }

  /**
   * Create revision of project.
   *
   * @param array $data
   *   Array containing project info.
   *
   * @return boolean
   *  TRUE if created else FALSE.
   */
  public function addVersionControl($data = []) {
    $insert = $this->db->insert('master_project_version_control', $data);
    if ($insert) {
      return $this->db->insert_id();
    } else {
      return FALSE;
    }
  }

  /**
   * Update project info.
   * @param  array $data
   *  Array containing project info.
   * @param  int $id
   *  The project id.
   *
   * @return boolean
   *  TRUE if updated else FALSE.
   */
  public function update($data, $id) {
    if (!empty($data) && !empty($id)) {
      $update = $this->db->update('master_project', $data, ['project_id' => $id]);
      return $update ? TRUE : FALSE;
    } else {
      return FALSE;
    }
  }

  /**
   * Clean up process.
   *
   * @param  int $id
   *  The project id.
   *
   * @return boolean
   *  TRUE if deleted else FALSE.
   */
  public function delete($id) {
    $delete = $this->db->delete('master_project',array('project_id'=>$id));
    $delete = $this->db->delete('master_project_version_control',array('project_id'=>$id));
    $delete = $this->db->delete('financial_plan',array('project_id'=>$id));
    $delete = $this->db->delete('financial_plan_to_years',array('project_id'=>$id));
    //  $delete = $this->db->delete('financial_plan_to_years_version_control_',array('project_id'=>$id));
    $delete = $this->db->delete('financial_plan_version_control',array('project_id'=>$id));
    $delete = $this->db->delete('map_project_phase_contract',array('project_id'=>$id));
    $delete = $this->db->delete('map_project_phase_contract_version_control',array('project_id'=>$id));
    $delete = $this->db->delete('master_invoice',array('project'=>$id));
    $delete = $this->db->delete('master_invoice_version_control',array('project'=>$id));
    $delete = $this->db->delete('master_project_fund_source',array('project_id'=>$id));
    $delete = $this->db->delete('master_project_fund_source_version_control',array('project_id'=>$id));

    return $delete?true:false;
  }

  /**
   * [deleteFinancialPlan description]
   *
   * @param  [type] $id [description]
   * @return [type]     [description]
   */
  public function deleteFinancialPlan($id) {
    $delete = $this->db->delete('financial_plan',array('project_id'=>$id));

    return $delete?true:false;
  }

  /**
   * [check description]
   *
   * @param  [type] $project [description]
   * @return [type]          [description]
   */
  public function check($project) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM master_project WHERE TRIM(project_name) = '". $project ."'");
    foreach ($query->result() as $row) {
      return $row->total;
    }
  }

  /**
   * [history description]
   *
   * @param  [type] $project_id [description]
   * @return [type]             [description]
   */
  public function history($project_id) {
    if (!empty($project_id)) {
      $query = $this->db->get_where('master_project_version_control', array('project_id' => $project_id));
      return $query->result_array();
    }
  }
}
