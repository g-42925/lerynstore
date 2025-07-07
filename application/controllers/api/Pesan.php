<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Pesan extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$set = $this->func->globalset("semua");
		$production = (strpos($set->midtrans_snap,"sandbox") == true) ? false : true;
		\Midtrans\Config::$serverKey = $set->midtrans_server;
		\Midtrans\Config::$isProduction = $production;
		\Midtrans\Config::$isSanitized = true;
		\Midtrans\Config::$is3ds = true;

		/*if($this->func->maintenis() == TRUE) {
			include(APPPATH.'views/maintenis.php');

			die();
		}*/
	}

	// CHAT
	public function chat(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
				
				$this->db->where("(tujuan = '".$r->usrid."' OR dari = '".$r->usrid."') AND baca = '0'");
				$this->db->update("pesan",["baca"=>1]);
				
				$this->db->where("tujuan",$r->usrid);
				$this->db->or_where("dari",$r->usrid);
				$this->db->limit(50,($page-1)*50);
				$db = $this->db->get("pesan");
				
				if($db->num_rows() > 0){
					$currdate = false;
					foreach($db->result() as $r){
						$letak = ($r->tujuan == 0) ? "kanan" : "kiri";
						$prod = $this->func->getProduk($r->idproduk,"semua");
						
						if($this->func->ubahTgl("d-m-Y",$r->tgl) != $currdate){
							$data[] = array(
								"pesan"	=> $this->func->ubahTgl("d M Y",$r->tgl),
								"letak"	=> "tengah",
								"waktu"	=> $this->func->ubahTgl("H:i",$r->tgl),
								"baca"	=> $r->baca
							);
							$currdate = $this->func->ubahTgl("d-m-Y",$r->tgl);
						}
						
						if($usr->level == 5){
							$harga = $prod->hargadistri;
						}elseif($usr->level == 4){
							$harga = $prod->hargaagensp;
						}elseif($usr->level == 3){
							$harga = $prod->hargaagen;
						}elseif($usr->level == 2){
							$harga = $prod->hargareseller;
						}else{
							$harga = $prod->harga;
						}
						$data[] = array(
							"pesan"	=> $r->isipesan,
							"letak"	=> $letak,
							"waktu"	=> $this->func->ubahTgl("H:i",$r->tgl),
							"baca"	=> $r->baca,
							"idproduk"	=> $r->idproduk,
							"produk_nama"	=> $prod->nama,
							"produk_stok"	=> $prod->stok,
							"produk_harga"	=> $this->func->formUang($harga),
							"produk_foto"	=> $this->func->getFoto($prod->id,"utama"),
						);
					}
					echo json_encode(array("success"=>true,"result"=>$data));
				}else{
					echo json_encode(array("success"=>false,"sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function kirimpesan(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					/*$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
					$usr = $this->func->getUser($r->usrid,"semua");*/
					$file = "";
					$new_name = time();
					//$new_name = str_replace(" ","_",$new_name);
					$config['upload_path'] 	 = './cdn/chat/';
					$config['allowed_types'] = '*';
					//$config['max_size']      = '2000';
					//$config['max_width']     = '2024';
					//$config['max_height']    = '2024';
					$config['file_name'] 	 = $new_name;
				
					$this->load->library('upload', $config);
					if($this->upload->do_upload('file')){
						$upload_data = $this->upload->data();
						$file = $upload_data['file_name'];
					}
					
					$produk = (isset($input['produk'])) ? $input['produk'] : 0;
					$pesan = (isset($input['pesan'])) ? $input['pesan'] : "";
					$isi = array(
						"tujuan"=> 0,
						"dari"	=> $r->usrid,
						"isipesan"	=> $pesan,
						"idproduk"	=> $produk,
						"file"	=> $file,
						"tgl"	=> date("Y-m-d H:i:s"),
						"baca"	=> 0
					);
					
					$this->db->insert("pesan",$isi);
					echo json_encode(array("success"=>true,"result"=>$isi));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}

}