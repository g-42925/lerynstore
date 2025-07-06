<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ulasan extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view('atmin/admin/head',["menu"=>34]);
		$this->load->view('atmin/ulasan/index');
		$this->load->view('atmin/admin/foot');
	}
	
	/* MULTIGUDANG */
	public function data(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/ulasan/data","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}elseif(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$this->db->where("id",intval($_POST["formid"]));
			$db = $this->db->get("review");
			$data = [];
			foreach($db->result() as $r){
				$data = [
					"id"	=> $_POST["formid"],
					"usrid"	=> $r->usrid,
					"idtransaksi"=> $r->idtransaksi,
					"idproduk"	=> $r->idproduk,
					"nilai"	=> $r->nilai,
					"nama"	=> $r->nama,
					"tgl"	=> $r->tgl,
					"keterangan"=> $r->keterangan,
					"moderasi"	=> $r->moderasi,
					"jenis"	=> $r->jenis,
					"token"	=> $this->security->get_csrf_hash()
				];
			}
			echo json_encode($data);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambah(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			$_POST["tgl"] = date("Y-m-d H:i:s");
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("review",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				if(!isset($_POST['jenis'])){ $_POST['jenis'] = 1; }
				$this->db->insert("review",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapus(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("review");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false,"msg"=>"","token"=> $this->security->get_csrf_hash()]);
		}
	}

}