<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orderform extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index($id=0){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}

        $this->load->view('atmin/admin/head',["menu"=>28]);
        $this->load->view('atmin/orderform/index',["id"=>$id]);
        $this->load->view('atmin/admin/foot');
    }
	public function edit($id=0,$produk=0){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}

        if($id > 0 || (intval($id) == 0 AND intval($produk) > 0)){
            $this->load->view('atmin/admin/head',["menu"=>28]);
            $this->load->view('atmin/orderform/form',["id"=>$id,"produk"=>$produk]);
            $this->load->view('atmin/admin/foot');
        }else{
			redirect($this->func->admurl()."/orderform");
		}
    }
	public function data(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/orderform/data","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			redirect($this->func->admurl()."/orderform");
		}
	}
	public function tambah(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
            //print_r($_POST["fieldactive"]);exit;
			//$_POST["id"] = intval(["id"]);
			$data["tgl"]	= date("Y-m-d H:i:s");
			if(isset($_FILES['header']) AND $_FILES['header']['size'] != 0 && $_FILES['header']['error'] == 0){
				$testi = (isset($_POST["id"])) ? intval($_POST["id"]) : 0;
				$this->db->where("id",$testi);
				$db = $this->db->get("formorder");
				foreach($db->result() as $res){
					if(file_exists("cdn/uploads/".$res->header) AND $res->header != ""){
						unlink("cdn/uploads/".$res->header);
					}
				}

				$config['upload_path'] = './cdn/uploads/';
				$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
				$config['file_name'] = "form_".date("YmdHis");;
		
				$this->load->library('upload', $config);
				if(!$this->upload->do_upload('header')){
					$error = $this->upload->display_errors();
					echo json_encode(array("success"=>false,"msg"=>$error));
					exit;
				}else{
					$upload_data = $this->upload->data();			
					$data["header"] = $upload_data["file_name"];
				}
			}

            $data["nama"]   = $_POST["nama"];
            $data["url"]   = $this->func->cleanURL($_POST["nama"]);
            $data["title"]   = $_POST["title"];
            $data["tagline"]   = $_POST["tagline"];
            $data["showproduct"]   = isset($_POST["showproduct"]) ? $_POST["showproduct"] : 0;
            $data["garansi1"]   = isset($_POST["garansi1"]) ? $_POST["garansi1"] : 0;
            $data["garansi2"]   = isset($_POST["garansi2"]) ? $_POST["garansi2"] : 0;
            $data["dropship"]   = isset($_POST["dropship"]) ? $_POST["dropship"] : 0;
            $data["summary"]   = isset($_POST["summary"]) ? $_POST["summary"] : 0;
            $data["button_tema"]   = $_POST["tema"];
            $data["button_warna"]   = $_POST["temawarna"];
            $data["button_text"]   = $_POST["button_text"];
            $data["deskripsi"]   = $_POST["deskripsi"];
            $data["pembayaran"]   = isset($_POST["pembayaran"]) ? implode("|",$_POST["pembayaran"]) : "";
			
			if($_POST["id"] > 0){
                $order = $this->func->getOrder($_POST["id"],"semua");
                $prod = $this->func->getProduk($order->idproduk,"semua");
                $data["digital"]   = $prod->digital;
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("formorder",$data);

                $this->db->where("formid",intval($_POST["id"]));
                $this->db->delete("formorder_bullet");
                //$this->db->where("formid",intval($_POST["id"]));
                //$this->db->delete("formorder_detail");

                // CUSTOM FIELDS
                foreach($_POST["fieldid"] as $k => $v){
                    $detail = array(
                        "formid"    => intval($_POST["id"]),
                        "tgl"	    => date("Y-m-d H:i:s"),
                        "sambung"   => $_POST["fieldsambung"][$k],
                        "model"     => $_POST["fieldmodel"][$k],
                        "type"      => $_POST["fieldtype"][$k],
                        "field"     => $_POST["field"][$k],
                        "required"  => $_POST["fieldrequired"][$k],
                        "opsi"      => $_POST["fieldopsi"][$k],
                        "label"     => $_POST["fieldlabel"][$k],
                        "placeholder"=> $_POST["fieldplaceholder"][$k],
                        "status"    => $_POST["fieldactive"][$k],
                        "urutan"    => $k
                    );
                    if($v > 0){
                        $this->db->where("id",$v);
                        $this->db->update("formorder_detail",$detail);
                    }else{
                        $this->db->insert("formorder_detail",$detail);
                    }
                }
                if(isset($_POST["hapusfield"])){
                    foreach($_POST["hapusfield"] as $k => $v){
                        $this->db->where("id",$v);
                        $this->db->delete("formorder_detail");
                    }
                }

                // BULLET
                foreach($_POST["bullet"] as $k => $v){
                    $detail = array(
                        "formid"=> intval($_POST["id"]),
                        "tgl"	=> date("Y-m-d H:i:s"),
                        "isi"   => $v
                    );
                    $this->db->insert("formorder_bullet",$detail);
                }

                redirect($this->func->admurl()."/orderform/index/".$prod->id);
			}elseif($_POST["id"] == 0){
                $data["idproduk"]   = $_POST["idproduk"];
                $prod = $this->func->getProduk($_POST["idproduk"],"semua");
                $data["digital"]   = $prod->digital;
				$this->db->insert("formorder",$data);
                $formid = $this->db->insert_id();

                // CUSTOM FIELDS
                foreach($_POST["fieldid"] as $k => $v){
                    $detail = array(
                        "formid"    => $formid,
                        "tgl"	    => date("Y-m-d H:i:s"),
                        "sambung"   => $_POST["fieldsambung"][$k],
                        "model"     => $_POST["fieldmodel"][$k],
                        "type"      => $_POST["fieldtype"][$k],
                        "field"     => $_POST["field"][$k],
                        "required"  => $_POST["fieldrequired"][$k],
                        "opsi"      => $_POST["fieldopsi"][$k],
                        "label"     => $_POST["fieldlabel"][$k],
                        "placeholder"=> $_POST["fieldplaceholder"][$k],
                        "status"    => $_POST["fieldactive"][$k],
                        "urutan"    => $k
                    );
                    $this->db->insert("formorder_detail",$detail);
                }

                // BULLET
                foreach($_POST["bullet"] as $k => $v){
                    $detail = array(
                        "formid"=> $formid,
                        "tgl"	=> date("Y-m-d H:i:s"),
                        "isi"   => $v
                    );
                    $this->db->insert("formorder_bullet",$detail);
                }
                redirect($this->func->admurl()."/orderform/index/".$prod->id);
			}else{
                redirect($this->func->admurl()."/orderform");
			}
		}else{
			redirect($this->func->admurl()."/orderform");
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
			$this->db->delete("formorder");
			$this->db->where("formid",intval($_POST["id"]));
			$this->db->delete("formorder_detail");
			$this->db->where("formid",intval($_POST["id"]));
			$this->db->delete("formorder_bullet");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapusfield(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		if(isset($_POST["id"])){
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("formorder_detail");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
}