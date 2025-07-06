<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}else{
            redirect($this->func->admurl()."/manage");
        }
	}
	function sundul(){
		if(!isset($_SESSION["isMasok"])){
            echo json_encode(array("success"=>false,"msg"=>"Forbidden!"));
			//redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			$this->db->where("id",$_POST["id"]);
			$this->db->update("produk",array("tglupdate"=>date("Y-m-d H:i:s")));

			echo json_encode(array("success"=>true,"msg"=>"Berhasil menyundul"));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Forbidden!"));
		}
	}

}