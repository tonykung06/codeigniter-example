<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Magazine extends CI_Controller {
	public function index() {
		$this->load->model('Publication');
		$this->Publication->publication_name = 'Tony Kung';
		$this->Publication->save();
		echo '<tt><pre>' . var_export($this->Publication, true) . '</pre></tt>';

		$this->load->model('Issue');
		$issue = new Issue();
		$issue->publication_id = $this->Publication->publication_id;
		$issue->issue_number = 2;
		$issue->issue_date_publication = date('2017-02-12');
		$issue->save();
		echo '<tt><pre>' . var_export($issue, true) . '</pre></tt>';

		$this->load->view('magazines');
	}
}
