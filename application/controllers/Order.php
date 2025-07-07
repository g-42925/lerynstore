<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
        $this->load->view('headv2',array("titel"=>"Akun Saya"));
        // /$this->load->view('admin/afiliasi');
        $this->load->view('footv2');
	}

}