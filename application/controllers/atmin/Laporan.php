<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}else{
		}
	}
	
	/* LAPORAN */
	public function labarugi(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view('atmin/laporan/labarugilist',"",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			$this->load->view('atmin/admin/head',["menu"=>32]);
			$this->load->view('atmin/laporan/labarugi');
			$this->load->view('atmin/admin/foot');
		}
	}
	public function ppob(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view('atmin/digiflazz/laporanlist',"",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			$this->load->view('atmin/admin/head',["menu"=>31]);
			$this->load->view('atmin/digiflazz/laporan');
			$this->load->view('atmin/admin/foot');
		}
	}
	public function transaksi(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view('atmin/laporan/transaksilist',"",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			$this->load->view('atmin/admin/head',["menu"=>14]);
			$this->load->view('atmin/laporan/transaksi');
			$this->load->view('atmin/admin/foot');
		}
	}
	public function produk(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view('atmin/laporan/produklist',"",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			$this->load->view('atmin/admin/head',["menu"=>19]);
			$this->load->view('atmin/laporan/produk');
			$this->load->view('atmin/admin/foot');
		}
	}
	public function user(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view('atmin/laporan/userlist',"",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			$this->load->view('atmin/admin/head',["menu"=>15]);
			$this->load->view('atmin/laporan/user');
			$this->load->view('atmin/admin/foot');
		}
	}
	public function komisi(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view('atmin/laporan/komisilist',"",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			$this->load->view('atmin/admin/head',["menu"=>26]);
			$this->load->view('atmin/laporan/komisi');
			$this->load->view('atmin/admin/foot');
		}
	}

}