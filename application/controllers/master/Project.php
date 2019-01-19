<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file
 *
 * Master Project Controller.
 */
class Project extends CI_Controller {

  /**
   * Constructer for Project class.
   */
  function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('master/project');
        $this->load->library('form_validation');
        $this->load->model('master/projects');
        $this->load->model('master/funds');
        $this->load->model('mapping/project_funds');
        $this->load->model('mapping/financial_plans');
        $this->load->model('mapping/projects_phases_contracts');
    }

  /**
   * Main index file to render project info page.
   *
   */
  public function index() {
    $data = [];

    // get display success msg from the session.
    if ($this->session->userdata('success_msg')) {
      $data['success_msg'] = $this->session->userdata('success_msg');
      $this->session->unset_userdata('success_msg');
    }

    // get display error msg from the session.
    if ($this->session->userdata('error_msg')) {
      $data['error_msg'] = $this->session->userdata('error_msg');
      $this->session->unset_userdata('error_msg');
    }

    if ($this->input->post('project_name')) {
      $postData = [
        'project_code' => $this->input->post('project_code'),
        'project_name' => $this->input->post('project_name'),
        'description' => $this->input->post('description'),
        'total_project_cost' => $this->input->post('total_project_cost'),
        'start_date' => $this->input->post('start_date'),
        'end_date' => $this->input->post('end_date'),
        'is_active' => $this->input->post('is_active'),
        'date_added' => date("m/d/Y G:i:s"),
        'modified_date' => date("m/d/Y G:i:s")
      ];

      // Add project to database.
      $insert = $this->projects->add($postData);

      if ($insert) {
        $postVersionControlData = [
          'project_id' => $insert,
          'project_code' => $this->input->post('project_code'),
          'project_name' => $this->input->post('project_name'),
          'description' => $this->input->post('description'),
          'total_project_cost' => $this->input->post('total_project_cost'),
          'start_date' => $this->input->post('start_date'),
          'end_date' => $this->input->post('end_date'),
          'date_added' => date("m/d/Y G:i:s"),
        ];

        // create revision.
        $insertVersionControl = $this->projects->addVersionControl($postVersionControlData);

        if ($insertVersionControl) {
          $this->session->set_userdata('success_msg', 'Project has been added successfully.');
          redirect('/project');
        } else {
          $data['error_msg'] = 'Some problems occurred, please try again.';
        }
      }
    }

    $data['project_lists'] = $this->projects->getRows();
    $data['title'] = 'Project List';

    //load the list page view
    $this->load->view('common/header', $data);
    $this->load->view('master/project/add', $data);
    $this->load->view('common/footer');
  }

  /**
   * View handler for project info.
   *
   * @param  int $id
   *  The project id.
   */
  public function view($id) {
    $data = [];
    $this->session->set_userdata('project_id', $id);

    // Check if project id is not empty.
    if (!empty($id)) {
      $data['view'] = $this->projects->getRows($id);

      $data['project_code'] = $data['view']['project_code'];
      $data['project_name'] = $data['view']['project_name'];
      $data['description'] = $data['view']['description'];
      $data['total_project_cost'] = $data['view']['total_project_cost'];
      $data['start_date'] = $data['view']['start_date'];
      $data['end_date'] = $data['view']['end_date'];
      $data['is_active'] = $data['view']['is_active'];
      $data['date_added'] = $data['view']['date_added'];
      $data['modified_date'] = $data['view']['modified_date'];

      $data['project_lists'] = $this->projects->getRows();

      $data['title'] = 'Project Detailed View';

      //load the details page view
      $this->load->view('common/header',$data);
      $this->load->view('master/project/view', $data);
      $this->load->view('common/footer');
    } else {
      redirect('/project');
    }
  }

  /**
   * Edit handler for Project Info.
   *
   * @param  int $id
   *  The project id.
   */
  public function edit($id) {
    $data = [];
    $this->session->set_userdata('project_id', $id);
    $postData = $this->projects->getRows($id);
    $data['project_lists'] = $this->projects->getRows();
    $data['title'] = 'Project Edit';

    if ($this->input->post('project_name')) {
      $postData = [
        'project_code' => $this->input->post('project_code'),
        'project_name' => $this->input->post('project_name'),
        'description' => $this->input->post('description'),
        'total_project_cost' => $this->input->post('total_project_cost'),
        'start_date' => $this->input->post('start_date'),
        'end_date' => $this->input->post('end_date'),
        'is_active' => $this->input->post('is_active'),
        'modified_date' => $this->input->post('m/d/Y G:i:s')
      ];

      // Update project if validation pass.
      if (!($error_msg = validate_edit_project($id, $postData, $this->financial_plans, $this->project_funds, $this->projects_phases_contracts))) {
        $update = $this->projects->update($postData, $id);
        if ($update) {
          $postVersionControlData = [
            'project_id' => $id,
            'project_code' => $this->input->post('project_code'),
            'project_name' => $this->input->post('project_name'),
            'description' => $this->input->post('description'),
            'total_project_cost' => $this->input->post('total_project_cost'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'date_added' => date("m/d/Y G:i:s")
          ];

          // create revision.
          $insertVersionControl = $this->projects->addVersionControl($postVersionControlData);

          if ($insertVersionControl) {
            $this->session->set_userdata('success_msg', 'Project has been updated successfully.');
            redirect('/project');
          } else {
            $data['error_msg'] = 'Some problems occurred, please try again.';
          }
        } else {
            $data['error_msg'] = 'Some problems occurred, please try again.';
        }
      } else {
        $data['error_msg'] = $error_msg;
      }
    }

    // Data array.
    $data['post'] = isset($postData) ? $postData : [];

    // Load view for project edit page.
    $this->load->view('common/header', $data);
    $this->load->view('master/project/edit', $data);
    $this->load->view('common/footer');
  }

  /**
   * Delete handler of project info.
   *
   * @param  int $id
   *  The Project Id.
   *
   */
  public function delete($id) {
    //check whether post id is not empty
    if ($id) {
      $result = cleanup_process($id, $this->project_funds, $this->funds);
      if ($result) {
        //delete post
        $delete = $this->projects->delete($id);
        if ($delete) {
          $this->session->set_userdata('success_msg', 'Project has been removed successfully.');
        } else {
          $this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
        }
      } else {
          $this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
        }
    }

    redirect('/project');
  }

  /**
   * Check if project exists.
   */
  public function check() {
    $project_name = $this->input->post('project_name');
    $result = $this->projects->check($project_name);
    echo json_encode($result);
  }

  /**
   * Return history.
   */
  public function history() {
    $project = $this->input->post('project_id');
    $result = $this->projects->history($project);
    echo json_encode($result);
  }
}
