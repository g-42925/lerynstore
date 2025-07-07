<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Review extends CI_Controller {

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
    
	//REVIEW
	public function tambahreview(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					/*$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));*/
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				
				if(isset($input)){
					//print_r($input);
					for($i=0; $i<count($input['data']); $i++){
						if(!empty($input["data"][$i]["review"])){
							$res = array(
								"usrid"	=> $r->usrid,
								"idtransaksi"	=> $input["id"],
								"idproduk"	=> $input["data"][$i]["idproduk"],
								"nilai"	=> $input["data"][$i]["review"],
								"keterangan"=> $input["data"][$i]["komeng"],
								"tgl"	=> date("Y-m-d H:i:s")
							);
							$this->db->insert("review",$res);
						}
					}
					echo json_encode(array("success"=>true));
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
	public function getreview($id){
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

				$this->db->where("idtransaksi",$id);
				$pro = $this->db->get('transaksiproduk');
		
				$review = array();
				foreach($pro->result() as $r){
					$produk = $this->func->getProduk($r->idproduk,"semua");
					$revies = 0;
					$komentar = "";
					$tgl = $this->func->ubahTgl("d M Y H:i",date("Y-m-d H:i:s"));
					$this->db->where("idproduk",$r->idproduk);
					$this->db->where("idtransaksi",$id);
					$rev = $this->db->get("review");
					foreach($rev->result() as $res){
						$revies = $res->nilai;
						$komentar = $res->keterangan;
						$tgl = $this->func->ubahTgl("d M Y H:i",$res->tgl);
					}
					$review[] = array(
						"id"	=> $r->id,
						"variasi"	=> $r->variasi,
						"idproduk"	=> $r->idproduk,
						"nama"	=> $produk->nama,
						"tgl"	=> $tgl,
						"foto"	=> $this->func->getFoto($r->idproduk),
						"review"=> $revies,
						"komeng"=> $komentar
					);
				}
				
				echo json_encode(array("success"=>true,"data"=>$review));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
    
	public function laporkanreview(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}
				
				echo json_encode(array("success"=>true,"msg"=>"Laporan berhasil disimpan"));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function review(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}
				$page = (isset($_GET["page"]) AND intval($_GET["page"]) > 0) ? $_GET["page"] : 1;
				
				$this->db->select("id");
				$this->db->where("keterangan !=","");
				$rows = $this->db->get("review");
				$rows = $rows->num_rows();

				$this->db->where("keterangan !=","");
				$this->db->order_by("tgl","DESC");
				//$this->db->limit(8);
				$this->db->limit(8,($page-1)*8);
				$db = $this->db->get("review");

				$maxPage = ceil($rows/8);
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						//$img = (file_exists(FCPATH."cdn/uploads/".$r->img)) ? base_url("cdn/uploads/".$r->img) : base_url("cdn/uploads/no-image.png");
						$usr = $this->func->getProfil($r->usrid,"semua");
						$prod = $this->func->getProduk($r->idproduk,"semua");
						$rev = $this->func->getReviewProduk($r->idproduk);
						$produk = array(
							"nama"	=> $prod->nama,
							"foto"	=> $this->func->getFoto($prod->id),
							"harga"	=> $prod->harga,
							"rating"=> $rev["nilai"]
						);
						$data[] = array(
							"user"	=> ($r->jenis == 1) ? $r->nama : $usr->nama,
							"produk"=> $produk,
							"rating"=> $r->nilai,
							"tgl"	=> $this->func->elapsed($r->tgl),
							"konten"=> $this->func->potong($this->func->clean(strip_tags($r->keterangan)),120,"..."),
							"id"	=> $r->id
						);
					}
					echo json_encode(array("success"=>true,"maxPage"=>$maxPage,"page"=>$page,"result"=>$data));
				}else{
					echo json_encode(array("success"=>true,"sesihabis"=>false,"result"=>[]));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}

}