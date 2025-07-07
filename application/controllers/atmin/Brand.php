<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view('atmin/admin/head',["menu"=>28]);
		$this->load->view('atmin/brand/index');
		$this->load->view('atmin/admin/foot');
	}
	public function data(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/brand/data","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}elseif(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$this->db->where("id",intval($_POST["formid"]));
			$db = $this->db->get("brand");
			$data = [];
			foreach($db->result() as $r){
                $data = [
                    "id"		=> $_POST["formid"],
                    "nama"      => $r->nama,
                    "foto"      => $r->icon,
                    "token"		=> $this->security->get_csrf_hash()
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
			$_POST["tgl"]	= date("Y-m-d H:i:s");
			if(isset($_FILES['foto']) AND $_FILES['foto']['size'] != 0 && $_FILES['foto']['error'] == 0){
				$config['upload_path'] = './cdn/brand/';
				$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
				$config['file_name'] = "brand_".date("YmdHis");;
		
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('foto')){
					$error = $this->upload->display_errors();
					echo json_encode(array("success"=>false,"msg"=>$error,"token"=> $this->security->get_csrf_hash()));
					exit;
				}else{
					$upload_data = $this->upload->data();			
					$_POST["icon"] = $upload_data["file_name"];
				}
			}else{
				unset($_FILES["foto"]);
				unset($_POST["foto"]);
			}
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("brand",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("brand",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
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
			$this->db->delete("brand");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}

}