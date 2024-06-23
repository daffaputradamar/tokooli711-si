<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin_model');
		$this->load->model('Karyawan_model');
		session_start();
	}
	public function logout(){
		// $this->session->sess_destroy();
		session_destroy();
		redirect('login','refresh');
	}

}