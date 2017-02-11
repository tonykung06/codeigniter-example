<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publication extends MY_Model {
	const DB_TABLE = 'publications';
	const DB_TABLE_PK = 'publication_id';

	public $publication_id;
	public $publication_name;
}
