<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form extends CI_Controller {
	public function __construct(){
		parent::__construct();

		$this->load->library('session');

		/*\Midtrans\Config::$serverKey = $this->func->getSetting("midtrans_server");
		\Midtrans\Config::$isProduction = false;
		\Midtrans\Config::$isSanitized = true;
		\Midtrans\Config::$is3ds = true;

		if($this->func->maintenis() == TRUE) {
			include(APPPATH.'views/maintenis.php');

			die();
		}*/
    }

    function index($url=0){
		$form = $this->func->getOrder($url,"semua","url");
		$prod = $this->func->getProduk($form->idproduk,"semua");
		$this->load->view("head_blank",["titel"=>$url]);
		$this->load->view("form/main",["data"=>$form,"prod"=>$prod]);
		$this->load->view("foot_blank");
    }

}