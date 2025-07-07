<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ppob extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view('atmin/admin/head',["menu"=>29]);
		$this->load->view('atmin/digiflazz/index');
		$this->load->view('atmin/admin/foot');
	}

	// PESANAN
	public function pesanan(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view('atmin/admin/head',["menu"=>30]);
		$this->load->view("atmin/digiflazz/pesanan");
		$this->load->view('atmin/admin/foot');
	}
	public function pesananload(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET['load']) AND $_GET['load'] == "proses"){
			$res = $this->load->view("atmin/digiflazz/pesananproses","",true);
		}elseif(isset($_GET['load']) AND $_GET['load'] == "selesai"){
			$res = $this->load->view("atmin/digiflazz/pesananselesai","",true);
		}elseif(isset($_GET['load']) AND $_GET['load'] == "batal"){
			$res = $this->load->view("atmin/digiflazz/pesananbatal","",true);
		}else{
			redirect($this->func->admurl()."/manage");exit;
		}
		echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
	}

	// PRODUK
	public function produk(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/digiflazz/produk");
	}
	public function produkpasca(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/digiflazz/produkpasca");
	}
	public function getproduk(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			$this->db->where('id',intval($_POST["id"]));
			$this->db->limit(1);
			$db = $this->db->get("ppob");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$data = [
						"id"	=> $r->id,
						"kode"	=> $r->kode,
						"tipe"	=> $r->tipe,
						"kategori"	=> $r->kategori,
						"kategori_id"=> $r->kategori_id,
						"brand"	=> $r->brand,
						"start_cutoff"	=> $r->start_cutoff,
						"end_cutoff"	=> $r->end_cutoff,
						"nama"	=> $r->nama,
						"deskripsi"	=> $r->deskripsi,
						"multi"	=> $r->multi,
						"harga_beli"	=> $r->harga_beli,
						"harga_jual"	=> $r->harga_jual,
						"biaya_admin"	=> $r->biaya_admin,
						"komisi"	=> $r->komisi,
						"status"=> $r->status
					];
					echo json_encode(['success'=>true,'data'=>$data]);
				}
			}else{
				echo json_encode(['success'=>false]);
			}
		}else{
			echo json_encode(['success'=>false]);
		}
	}
	public function saveproduk(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			$data = [];
			if(isset($_FILES['icon']) AND $_FILES['icon']['size'] != 0 && $_FILES['icon']['error'] == 0){
				$id = (isset($_POST["id"])) ? intval($_POST["id"]) : 0;
				$this->db->where("id",$id);
				$db = $this->db->get("ppob");
				foreach($db->result() as $res){
					if($res->icon != null AND file_exists("cdn/ppob/".$res->icon)){
						unlink("cdn/ppob/".$res->icon);
					}
				}

				$config['upload_path'] = './cdn/ppob/';
				$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
				$config['file_name'] = "icon-".date("YmdHis");;
		
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('icon')){
					$error = $this->upload->display_errors();
					echo json_encode(array("success"=>false,"msg"=>$error,"token"=> $this->security->get_csrf_hash()));
					exit;
				}else{
					$upload_data = $this->upload->data();			
					$icon = $upload_data["file_name"];
				}
			}else{
				$icon = null;
			}
			$data["apdet"] = date("Y-m-d H:i:s");
			if($icon != null){
				$data["icon"] = $icon;
			}
			if(isset($_POST['harga_jual'])){
				$data["harga_jual"] = $_POST['harga_jual'];
			}
			
			if(intval($_POST["id"]) > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("ppob",$data);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}

	public function kategori(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/digiflazz/kategori");
	}
	public function getkategori(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			$this->db->where('id',intval($_POST["id"]));
			$this->db->limit(1);
			$db = $this->db->get("ppob_kategori");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$data = [
						"id"	=> $r->id,
						"kode"	=> $r->kode,
						"tipe"	=> $r->tipe,
						"nama"	=> $r->nama,
						"status"=> $r->status
					];
					echo json_encode(['success'=>true,'data'=>$data]);
				}
			}else{
				echo json_encode(['success'=>false]);
			}
		}else{
			echo json_encode(['success'=>false]);
		}
	}
	public function savekategori(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			$data = [];
			if(isset($_FILES['icon']) AND $_FILES['icon']['size'] != 0 && $_FILES['icon']['error'] == 0){
				$id = (isset($_POST["id"])) ? intval($_POST["id"]) : 0;
				$this->db->where("id",$id);
				$db = $this->db->get("ppob_kategori");
				foreach($db->result() as $res){
					if($res->icon != null AND file_exists("cdn/ppob/".$res->icon)){
						unlink("cdn/ppob/".$res->icon);
					}
				}

				$config['upload_path'] = './cdn/ppob/';
				$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
				$config['file_name'] = "icon-".date("YmdHis");;
		
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('icon')){
					$error = $this->upload->display_errors();
					echo json_encode(array("success"=>false,"msg"=>$error,"token"=> $this->security->get_csrf_hash()));
					exit;
				}else{
					$upload_data = $this->upload->data();			
					$icon = $upload_data["file_name"];
				}
			}else{
				$icon = null;
			}
			$data["apdet"] = date("Y-m-d H:i:s");
			if($icon != null){
				$data["icon"] = $icon;
			}
			if(isset($_POST['nama'])){
				$data["nama"] = $_POST['nama'];
			}
			
			if(intval($_POST["id"]) > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("ppob_kategori",$data);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}

	public function sinkronprabayar(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		echo $this->dgf->sinkronPra();
		//$this->load->view("atmin/digiflazz/kategori");
		//echo json_encode(['success'=>true,'produk'=>50,'kategori'=>10]);
	}
	public function sinkronpasca(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		echo $this->dgf->sinkronPasca();
		//$this->load->view("atmin/digiflazz/kategori");
		//echo json_encode(['success'=>true,'produk'=>50,'kategori'=>10]);
	}
	public function ceksaldo(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		//$this->load->view("atmin/digiflazz/kategori");
		echo "Saldo digiflazz: ".$this->dgf->saldo();
	}
	public function setting(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/digiflazz/setting");
	}
	public function status($id){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$data = $this->dgf->statusPesanan($id);
		$this->load->view("atmin/digiflazz/status",["data"=>$data]);
	}

}