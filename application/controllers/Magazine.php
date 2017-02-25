<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Magazine extends CI_Controller {
	public function index() {
		// version 1
		// $this->load->model('Publication');
		// $this->Publication->publication_name = 'Tony Kung';
		// $this->Publication->save();
		// echo '<tt><pre>' . var_export($this->Publication, true) . '</pre></tt>';

		// $this->load->model('Issue');
		// $issue = new Issue();
		// $issue->publication_id = $this->Publication->publication_id;
		// $issue->issue_number = 2;
		// $issue->issue_date_publication = date('2017-02-12');
		// $issue->save();
		// echo '<tt><pre>' . var_export($issue, true) . '</pre></tt>';

		// var_export($this->Publication->get(1));


		// version 2
		// $data = [];

		// $this->load->model('Publication');
		// $publication = new Publication();
		// $publication->load(1);
		// $data['publication'] = $publication;

		// $this->load->model('Issue');
		// $issue = new Issue();
		// $issue->load(1);
		// $data['issue'] = $issue;

		// $this->load->view('magazine', $data);

		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->view('bootstrap/header');
		$this->load->library('table');
		$magazines = [];
		$this->load->model(['Issue', 'Publication']);
		$issues = $this->Issue->get();
		foreach ($issues as $issue) {
			$publication = new Publication();
			$publication->load($issue->publication_id);
			$magazines[] = [
				$publication->publication_name,
				$issue->issue_number,
				$issue->issue_date_publication,
				$issue->issue_cover ? 'Y' : 'N',
				anchor('magazine/view/' . $issue->issue_id, 'View') . ' | ' . anchor('magazine/delete/' . $issue->issue_id, 'Delete')
			];
		}
		$this->load->view('magazines', [
			'magazines' => $magazines
		]);
		$this->load->view('bootstrap/footer');
	}

	public function add() {
		$this->load->library('upload', [
			'upload_path' => 'upload',
			'allowed_types' => 'gif|jpg|png',
			'max_size' => 250,
			'max_width' => 1920,
			'max_height' => 1080
		]);
		$this->load->view('bootstrap/header');
		$this->load->model('Publication');
		$publications = $this->Publication->get();
		$publicaton_form_options = [];
		foreach ($publications as $id => $publication) {
			$publication_form_options[$id] = $publication->publication_name;
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules([
			[
				'field' => 'publication_id',
				'label' => 'Publication',
				'rules' => 'required'
			],
			[
				'field' => 'issue_number',
				'label' => 'Issue number',
				'rules' => 'required|is_numeric'
			],
			[
				'field' => 'issue_date_publication',
				'label' => 'Publication date',
				'rules' => 'required|callback_my_date_validation'
			]
		]);
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

		$check_file_upload = false;
		if (isset($_FILES['issue_cover']['error']) && $_FILES['issue_cover']['error'] != 4) {
			$check_file_upload = true;
		}
		if (!$this->form_validation->run() || ($check_file_upload && !$this->upload->do_upload('issue_cover'))) {
			$this->load->view('magazine_form', [
				'publication_form_options' => $publication_form_options
			]);
		} else {
			$this->load->model('Issue');
			$issue = new Issue();
			$issue->publication_id = $this->input->post('publication_id');
			$issue->issue_number = $this->input->post('issue_number');
			$issue->issue_date_publication = $this->input->post('issue_date_publication');
			$upload_data = $this->upload->data();
			if (isset($upload_data['file_name'])) {
				$issue->issue_cover = $upload_data['file_name'];
			}
			$issue->save();
			$this->load->view('magazine_form_success', [
				'issue' => $issue
			]);
		}
		$this->load->view('bootstrap/footer');
	}

	public function my_date_validation($input) {
		$test_date = explode('-', $input);
		if (!@checkdate($test_date[1], $test_date[2], $test_date[0])) {
			$this->form_validation->set_message('my_date_validation', 'The {field} field must be in YYYY-MM-DD format');
			return false;
		}
		return true;
	}

	public function view($issue_id) {
		$this->load->helper('html');
		$this->load->view('bootstrap/header');
		$this->load->model(['Issue', 'Publication']);
		$issue = new Issue();
		$issue->load($issue_id);
		if (!$issue->issue_id) {
			show_404();
			return;
		}
		$publication = new Publication();
		$publication->load($issue->publication_id);
		$this->load->view('magazine', [
			'issue' => $issue,
			'publication' => $publication
		]);
		$this->load->view('bootstrap/footer');
	}

	public function delete($issue_id) {
		$this->load->model('Issue');
		$this->load->view('bootstrap/header');
		$issue = new Issue();
		$issue->load($issue_id);
		if (!$issue->issue_id) {
			show_404();
			return;
		}
		$issue->delete();
		$this->load->view('magazine_deleted', [
			'issue_id' => $issue_id
		]);
		$this->load->view('bootstrap/footer');
	}
}
