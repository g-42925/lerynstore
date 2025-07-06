<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Akun extends CI_Controller {

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
	
	// SALDO
	public function saldo(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					/*$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));*/
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				$data = [];
				$this->db->where("usrid",$usr->id);
				$this->db->where("status",0);
				$this->db->order_by("tgl DESC");
				$this->db->limit(30);
				$db = $this->db->get("saldotarik");
				foreach($db->result() as $r){
					$rek = $this->func->getRekening($r->idrek,"semua","id",true);
					$darike = $r->jenis == 2 ? "Topup Saldo" : "Penarikan ke Rek: ".$rek->atasnama." - ".$rek->norek;
					$data[] = [
						"tgl"	=> $this->func->ubahTgl("d M Y",$r->tgl),
						"id"	=> $r->id,
						"jenis"	=> $r->jenis,
						"status"=> $r->status,
						"jumlah"=> $r->total,
						"darike"=> $darike
					];
				}

				$this->db->where("usrid",$usr->id);
				$this->db->order_by("tgl DESC");
				$this->db->limit(30);
				$db = $this->db->get("saldohistory");
				foreach($db->result() as $r){
					$id = 0;
					$status = 1;
					$darike = "";
					if($r->darike <= 2){
						$st = $this->func->getSaldoTarik($r->sambung,'semua','id',true);
						$id = $st->id;
						$status = $st->status;
						$rek = $this->func->getRekening($st->idrek,"semua","id",true);
						$darike = ($st->jenis == 2) ? "Topup Saldo" : "Penarikan ke Rek: ".$rek->atasnama." - ".$rek->norek;
					}
					if($r->darike == 3 || $r->darike == 4){
						$trx = $this->func->getBayar($r->sambung,'invoice');
						$darike = $this->func->getSaldodarike($r->darike,"keterangan");
						$darike = str_replace('[invoice]',$trx,$darike);
					}
					if($r->darike >= 5){
						$trx = $this->func->getTransaksiPPOB($r->sambung,'invoice');
						$darike = $this->func->getSaldodarike($r->darike,"keterangan");
						$darike = str_replace('[invoice]',$trx,$darike);
					}
					$jenis = ($r->jenis == 1) ? 2 : 1;
					$data[] = [
						"tgl"	=> $this->func->ubahTgl("d M Y",$r->tgl),
						"id"	=> $id,
						"jenis"	=> $jenis,
						"status"=> $status,
						"jumlah"=> $r->jumlah,
						"darike"=> $darike
					];
				}

				$result = array(
					"success"	=> true,
					"nama"		=> $this->func->getProfil($usr->id,"nama","usrid"),
					"saldo"		=> $this->func->getSaldo($usr->id,"saldo","usrid",true),
					"result"	=> $data
				);
				echo json_encode($result);
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function konfirmasitopup(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				
				$config['upload_path'] = './cdn/konfirmasi/';
				$config['allowed_types'] = 'gif|jpg|jpeg|png';
				$config['file_name'] = "TOPUP_".$r->usrid.date("YmdHis");

				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('bukti')){
					$error = $this->upload->display_errors();
					json_encode(["success"=>false,"error"=>$error]);
					//redirect("404_notfound");
				}else{
					$upload_data = $this->upload->data();

					$filename = $upload_data['file_name'];
					$data = array(
						"bukti"		=> $filename
					);
					$this->db->where("id",$_GET['id']);
					$this->db->update("saldotarik",$data);

					//redirect("manage/pesanan");
					echo json_encode(array("success"=>true,"sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function bayarsaldo($id="0"){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				$result = ["success"=>false,"sesihabis"=>false,"total"=>0,"kadaluarsa"=>date("Y-m-d H:i:s")];
				$this->db->where("id",$id);
				$this->db->order_by("tgl DESC,selesai DESC");
				$db = $this->db->get("saldotarik");
				foreach($db->result() as $r){
					$bukti = ($r->bukti != "") ? base_url("cdn/konfirmasi/".$r->bukti) : "";
					$result = [
						"success"	=> true,
						"tgl"	=> $this->func->ubahTgl("d M Y",$r->tgl),
						"kadaluarsa"=> $this->func->ubahTgl("d M Y H:i",date('Y-m-d H:i:s', strtotime( $r->tgl . " +1 days")))." WIB",
						"total"	=> $r->total,
						"bukti"	=> $bukti,
						"status"=> $r->status
					];
				}
				
				$this->db->where("usrid",0);
				$rek = $this->db->get("rekening");
				$result['rekening'] = [];
				foreach($rek->result() as $rx){
					$result['rekening'][] = array(
						"norek"	=> $rx->norek,
						"atasnama"	=> $rx->atasnama,
						"kcp"	=> $rx->kcp,
						"bank"	=> $this->func->getBank($rx->idbank,"nama")
					);
				}

				echo json_encode($result);
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	function topupsaldo(){
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

				if(isset($input["jumlah"])){
					$idbayar = "TOPUP_".$usr->id.date("YmdHis");
					$data = array(
						"status"=> 0,
						"jenis"	=> 2,
						"usrid"	=> $usr->id,
						"total"	=> $input["jumlah"],
						"tgl"	=> date("Y-m-d H:i:s"),
						"trxid"	=> $idbayar
					);
					$this->db->insert("saldotarik",$data);
					$idbayar = $this->db->insert_id();

					//$idbayar = $this->func->arrEnc(array("trxid"=>$idbayar),"encode");
					echo json_encode(array("success"=>true,"idbayar"=>$idbayar));
				}else{
					echo json_encode(array("success"=>false,"message"=>"forbidden"));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	function bataltopup(){
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

				if(isset($input["id"])){
					$st = $this->func->getSaldoTarik($input["id"],"semua","id",true);
					$this->db->where("id",$input["id"]);
					$this->db->update("saldotarik",["selesai"=>date("Y-m-d H:i:s"),"status"=>2]);

					// SEND NOTIFICATION MOBILE
					$this->func->notifMobile("Pembatalan #".$st->Trxid,"Topup saldo telah dibatalkan","",$usr->id);

					echo json_encode(array("success"=>true));
				}else{
					echo json_encode(array("success"=>false,"message"=>"forbidden"));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	function tariksaldo(){
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
				}
				
				$keterangan = (isset($input["keterangan"])) ? $input["keterangan"] : "";
				$idbayar = $r->usrid.date("YmdHis");
				$saldoawal = $this->func->getSaldo($r->usrid,"saldo","usrid",true);
				if($saldoawal >= intval($input["jumlah"])){
					$saldoakhir = $saldoawal - intval($input["jumlah"]);
					$data = array(
						"status"	=> 0,
						"jenis"		=> 1,
						"trxid"		=> $idbayar,
						"usrid"		=> $r->usrid,
						"idrek"		=> $input["idrek"],
						"total"		=> $input["jumlah"],
						"tgl"		=> date("Y-m-d H:i:s"),
						"keterangan"=> $keterangan
					);
					$this->db->insert("saldotarik",$data);
					$idtarik = $this->db->insert_id();

					$data = array(
						"tgl"		=> date("Y-m-d H:i:s"),
						"usrid"		=> $r->usrid,
						"jenis"		=> 2,
						"jumlah"	=> $input["jumlah"],
						"darike"	=> 2,
						"saldoawal"	=> $saldoawal,
						"saldoakhir"=> $saldoakhir,
						"sambung"	=> $idtarik
					);
					$this->db->insert("saldohistory",$data);

					$this->db->where("usrid",$r->usrid);
					$this->db->update("saldo",array("saldo"=>$saldoakhir,"apdet"=>date("Y-m-d H:i:s")));

					echo json_encode(array("success"=>true));
				}else{
					echo json_encode(array("success"=>false,"msg"=>"saldo tidak mencukupi, saldo saat ini Rp. ".$this->func->formUang($saldoawal)));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false,"msg"=>""));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true,"msg"=>""));
		}
	}

	
	// Rekening
	public function rekening(){
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
				$page = (isset($_GET["page"]) AND intval($_GET["page"]) > 0) ? $_GET["page"] : 1;
				$perpage = (isset($_GET["perpage"]) AND intval($_GET["perpage"]) > 0) ? $_GET["perpage"] : 6;
				
				$rows = $this->db->get("rekening");
				$this->db->where("usrid",$r->usrid);
				$rows = $rows->num_rows();

				$this->db->from('rekening');
				$this->db->where("usrid",$r->usrid);
				$this->db->order_by("id DESC");
				$this->db->limit($perpage,($page-1)*$perpage);
				$pro = $this->db->get();
				
				$maxPage = ceil($rows/$perpage);
		
				$alamat = array();
				foreach($pro->result() as $r){
					$bank = $this->func->getBank($r->idbank,"nama");
					$alamat[] = array(
						"id"	=> $r->id,
						"atasnama"	=> $r->atasnama,
						"idbank"	=> $r->idbank,
						"bank"	=> $bank,
						"norek"	=> $r->norek,
						"kcp"	=> $r->kcp,
					);
				}
				
				echo json_encode(array("success"=>true,"maxPage"=>$maxPage,"page"=>$page,"data"=>$alamat));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	public function getrekening($id){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $rx){}
				$this->db->where("id",$id);
				$this->db->where("usrid",$rx->usrid);
				$db = $this->db->get("rekening");
				$reg = 0;
				
				$alamat = array();
				foreach($db->result() as $r){
					$bank = $this->func->getBank($r->idbank,"nama");
					$alamat = array(
						"id"	=> $r->id,
						"atasnama"	=> $r->atasnama,
						"idbank"	=> $r->idbank,
						"bank"	=> $bank,
						"norek"	=> $r->norek,
						"kcp"	=> $r->kcp
					);
				}
				
				echo json_encode($alamat);
			}else{
				echo json_encode(array(
						"atasnama"	=> "",
						"idbank"	=> "",
						"bank"	=> "",
						"norek"	=> "",
						"kcp"	=> ""
					));
			}
		}else{
			echo json_encode(array(
					"atasnama"	=> "",
					"idbank"	=> "",
					"bank"	=> "",
					"norek"	=> "",
					"kcp"	=> ""
				));
		}
	}
	public function tambahrekening(){
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
					$dt = $input["data"];
					$data = array(
						"usrid"	=> $r->usrid,
						"idbank"=> $dt['idbank'],
						"atasnama"	=> $dt['atasnama'],
						"norek"	=> $dt['norek'],
						"kcp"	=> $dt['kcp'],
						"tgl"	=> date("Y-m-d H:i:s")
					);
					
					if($input['id'] > 0){
						$this->db->where("id",$input['id']);
						$this->db->update("rekening",$data);
					}else{
						$this->db->insert("rekening",$data);
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
	public function hapusrekening($id=0){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				
				$this->db->where("id",intval($input['pid']));
				$this->db->delete("rekening");
				
				echo json_encode(array("success"=>true));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	
	// ALAMAT
	public function getkec(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				
				$id = (isset($input['id'])) ? $input['id'] : null;
				$cari = (isset($input['cari'])) ? $input['cari'] : null;

				$arr = array();
				if(!empty($cari)){
					$this->db->select("id");
					$this->db->like("nama",$cari);
					$al = $this->db->get("kab");
					foreach($al->result() as $l){
						$arr[] = $l->id;
					}
					//print_r($arr);
				}
		
				if(empty($id)){
					if(count($arr) > 0){
						$this->db->where_in('idkab',$arr);
						$this->db->or_like('nama',$cari);
					}else{
						$this->db->like('nama',$cari);
					}
					$this->db->limit(30);
				}else{
					$this->db->where('id',$id);
					$this->db->limit(1);
				}
				$data = $this->db->get("kec");
		
				$datas = [];
				foreach($data->result() as $res){
					$kab = $this->func->getKab($res->idkab,'semua');
					$datas[] = ["id"=>$res->id,"nama"=>strtoupper(strtolower($res->nama.", ".$kab->nama.", ".$this->func->getProv($kab->idprov,'nama')))];
				}
				echo json_encode(array("success"=>true,"data"=>$datas,"cari"=>$cari,"id"=>$id));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	public function alamat(){
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
				$page = (isset($_GET["page"]) AND intval($_GET["page"]) > 0) ? $_GET["page"] : 1;
				$perpage = (isset($_GET["perpage"]) AND intval($_GET["perpage"]) > 0) ? $_GET["perpage"] : 6;
				
				$rows = $this->db->get("alamat");
				$this->db->where("usrid",$r->usrid);
				$rows = $rows->num_rows();

				$this->db->from('alamat');
				$this->db->where("usrid",$r->usrid);
				$this->db->order_by("status DESC");
				$this->db->limit($perpage,($page-1)*$perpage);
				$pro = $this->db->get();
				
				$maxPage = ceil($rows/$perpage);
		
				$alamat = array();
				foreach($pro->result() as $r){
					$kec = $this->func->getKec($r->idkec,"semua");
					$kab = $this->func->getKab($kec->idkab,"nama");
					$alamat[] = array(
						"kab"	=>	$kab,
						"kec"	=>	$kec->nama,
						"judul"	=> $r->judul,
						"alamat"	=> $r->alamat,
						"kodepos"	=> $r->kodepos,
						"nama"	=> $r->nama,
						"nohp"	=> $r->nohp,
						"id"	=> $r->id,
						"status"	=> $r->status,
						"dari"	=> $this->func->globalset("kota")
					);
				}
				
				echo json_encode(array("success"=>true,"maxPage"=>$maxPage,"page"=>$page,"data"=>$alamat));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	public function getalamat($gudang,$id,$berat=1000){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				$seting = $this->func->globalset("semua");
				$dari = ($gudang > 0) ? $this->func->getGudang($gudang,'idkab') : $seting->kota;
				foreach($db->result() as $rx){}
				if($id != "utama"){
					$this->db->where("id",$id);
				}else{
					$this->db->where("status",1);
				}
				$this->db->where("usrid",$rx->usrid);
				$db = $this->db->get("alamat");
				$reg = 0;
				$alamat = array();
				$berat = ($berat > 0) ? $berat : 1000;
				foreach($db->result() as $r){
					
					//$paketkurir[] = "cod - cod";
					//$paketkurir[] = "toko - toko";
					//$hasil[] = $this->cekOngkir($seting->kota,$berat,$r->idkec,"cod","cod");
					//$hasil[] = $this->cekOngkir($seting->kota,$berat,$r->idkec,"toko","toko");
					//print_r($hasil);
					$hasil = array();
					$paketkurir = array();
					$kurirs = array();
					$kurirarr = array();
					
					//ob_start();
					$kurir = explode("|",$seting->kurir);
					$this->db->where_in("id",$kurir);
					$this->db->order_by("id","ASC");
					$dbs = $this->db->get("kurir");
					
					foreach($dbs->result() as $rs){
						if($rs->jenis == 1){
							$kurirs[$rs->rajaongkir] = $rs->id;
							$kurirarr[] = $rs->rajaongkir;
						}else{
							$this->db->where("idkurir",$rs->id);
							$x = $this->db->get("paket");
							foreach($x->result() as $re){
								$res = $this->func->cekOngkir($dari,$berat,$r->idkec,$rs->id,$re->id);
								//if($rs->rajaongkir == "jne" AND $re->rajaongkir == "REG"){ $reg = $res['harga']; }
								if(isset($res['success']) AND $res['success'] == true){
									$paketkurir[] = $rs->rajaongkir." - ".$re->rajaongkir;
									$hasil[] = $res;
								}
							}
						}
					}
					/*
					$this->db->where("idkurir",$rs->id);
					$x = $this->db->get("paket");
					foreach($x->result() as $re){
						$res = $this->func->cekOngkir($dari,$berat,$r->idkec,$rs->id,$re->id);
						//if($rs->rajaongkir == "jne" AND $re->rajaongkir == "REG"){ $reg = $res['harga']; }
						if(isset($res['success']) AND $res['success'] == true){
							$paketkurir[] = $rs->rajaongkir." - ".$re->rajaongkir;
							$res['kurir'] = strtoupper($res['kurir']);
							$hasil[] = $res;
						}
					}*/

					$ongkir = $this->rajaongkir->getOngkir($dari,$r->idkec,$berat,$kurirarr);
					if($ongkir->success){
						foreach($ongkir->data as $ong){
							//print_r($ong->kurir);exit;
            				$ong->kurir = ($ong->kurir == "J&T") ? "jnt" : $ong->kurir;
							$this->db->where("idkurir",$kurirs[$ong->kurir]);
							$this->db->where("rajaongkir",$ong->service);
							$this->db->limit(1);
							$dbk = $this->db->get("paket");
							if($dbk->num_rows() > 0){
								foreach($dbk->result() as $paket){
									$paketkurir[] = $ong->kurir." - ".$ong->service;
									$hasil[] = [
										"kuririd"   => $kurirs[$ong->kurir],
										"kurir"     => strtoupper(strtolower($ong->kurir)),
										"harga"     => $ong->harga,
										"etd"     => $ong->etd,
										"serviceid" => $paket->id,
										"service" => $paket->nama,
										"cod"       => $paket->cod
									];
								}
							}
						}
					}
					
					//ob_end_clean();
					$kec = $this->func->getKec($r->idkec,"semua");
					$kab = $this->func->getKab($kec->idkab,"semua");
					$prov = $this->func->getProv($kab->idprov,"nama");
				
					$alamat = array(
						"idkec"	=>	$r->idkec,
						"idprov"=>	$kab->idprov,
						"idkab"	=>	$kab->id,
						"judul"	=> $r->judul,
						"alamat"	=> ucwords($r->alamat.", ".$kec->nama.", ".$kab->tipe." ".$kab->nama.", ".$prov),
						"kodepos"	=> $r->kodepos,
						"nama"	=> $r->nama,
						"nohp"	=> $r->nohp,
						"id"	=> $r->id,
						"dari"	=> $dari,
						"ongkir"=> $hasil,
						"paku"=> $paketkurir,
						"reg"	=> $reg
					);
				}
				
				echo json_encode($alamat);
			}else{
				echo json_encode(array(
						"idkec"	=>	0,
						"idprov"=>	0,
						"idkab"	=>	0,
						"judul"	=> "Tidak Ditemukan",
						"alamat"	=> "",
						"kodepos"	=> 0,
						"nama"	=> "",
						"nohp"	=> "",
						"id"	=> 0,
						"dari"	=> $dari,
						"ongkir"=> false,
						"reg"	=> 0
					));
			}
		}else{
			echo json_encode(array(
					"idkec"	=>	0,
					"idprov"=>	0,
					"idkab"	=>	0,
					"judul"	=> "Tidak Ditemukan",
					"alamat"	=> "",
					"kodepos"	=> 0,
					"nama"	=> "",
					"nohp"	=> "",
					"id"	=> 0,
					"dari"	=> $dari
				));
		}
	}
	public function alamatsingle($id){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $rx){
					if($id != "utama"){
						$this->db->where("id",$id);
					}else{
						$this->db->where("status",1);
					}
					$this->db->where("usrid",$rx->usrid);
					$db = $this->db->get("alamat");
					$alamat = array();
					foreach($db->result() as $r){
						$kec = $this->func->getKec($r->idkec,"semua");
						$kab = $this->func->getKab($kec->idkab,"semua");
						$prov = $this->func->getProv($kab->idprov,"nama");
					
						$alamat = array(
							"idkec"	=>	$r->idkec,
							"judul"	=> $r->judul,
							"alamat"	=> $r->alamat,
							"kab"	=> $kab->nama,
							"kecamatan"	=> strtoupper(strtolower($kec->nama.", ".$kab->tipe." ".$kab->nama.", ".$prov)),
							"kodepos"	=> $r->kodepos,
							"nama"	=> $r->nama,
							"nohp"	=> $r->nohp,
							"id"	=> $r->id,
						);
					}
					
					echo json_encode($alamat);
				}
			}else{
				echo json_encode(array(
						"idkec"	=>	0,
						"kab"	=> "jakarta",
						"judul"	=> "Tidak Ditemukan",
						"alamat"	=> "",
						"kecamatan"	=> "",
						"kodepos"	=> 0,
						"nama"	=> "",
						"nohp"	=> "",
						"id"	=> 0,
					));
			}
		}else{
			echo json_encode(array(
					"idkec"	=>	0,
					"kab"	=> "jakarta",
					"judul"	=> "Tidak Ditemukan",
					"alamat"	=> "",
					"kecamatan"	=> "",
					"kodepos"	=> 0,
					"nama"	=> "",
					"nohp"	=> "",
					"id"	=> 0,
				));
		}
	}
	public function pilihanongkir(){
		$kurir = $this->func->globalset("kurir");
		
		$db = $this->db->get("kurir");
		foreach($db->result() as $r){
			$res = $this->func->cekOngkir($_GET["dari"],$_GET["berat"],$_GET['tujuan'],$r->rajaongkir,"");
			//$cek = json_decode($res);
			//if($cek['success'] == true){
				$hasil[] = $res;
			//}
		}
		print("<pre>".print_r($hasil,true)."</pre>");
	}
	public function tambahalamat($ide=0){
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
					$dt = $input["data"];
					if($ide != 0){
						$data = array(
							"status"=>1
						);
						$this->db->where("id !=",$input['id']);
						$this->db->where("usrid",$usr->id);
						$this->db->where("status",1);
						$this->db->update("alamat",["status"=>0]);
					}else{
						$data = array(
							"usrid"	=> $r->usrid,
							"idkec"	=> $dt['idkec'],
							"judul"	=> $dt['judul'],
							"alamat"	=> $dt['alamat'],
							"nama"	=> $dt['nama'],
							"kodepos"	=> $dt['kodepos'],
							"nohp"	=> $dt['nohp']
						);
					}
					
					if($input['id'] > 0){
						$this->db->where("id",$input['id']);
						$this->db->update("alamat",$data);
					}else{
						$this->db->insert("alamat",$data);
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
	public function hapusalamat($id=0){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				
				$this->db->where("id",intval($input['pid']));
				$this->db->delete("alamat");
				
				echo json_encode(array("success"=>true));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
    
	
	// PROFIL
	public function userdetail(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					/*$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));*/
					$usr = $this->func->getUser($r->usrid,"semua");
					$result = array(
						"success"	=>true,
						"usrid"		=>$r->usrid,
						"level"		=>$usr->level,
						"nama"		=>$this->func->getProfil($r->usrid,"nama","usrid"),
						"saldo"		=>$this->func->getSaldo($r->usrid,"saldo","usrid",true),
						"token"		=>$r->token
					);
					echo json_encode($result);
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function profil(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					/*$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));*/
					$usr = $this->func->getUser($r->usrid,"semua");
				}
				
				$this->db->where("usrid",$usr->id);
				$db = $this->db->get("profil");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$data = array(
							"id"=> $r->id,
							"nohp"=> $r->nohp,
							"kelamin"=> $r->kelamin,
							"nama"=> $r->nama,
							"email"=> $this->func->getUser($usr->id,"username")
						);
					}
					echo json_encode(array("success"=>true,"data"=>$data));
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
	public function simpanprofil(){
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
					$nohp = intval($input["nohp"]);
					$no1 = substr($nohp,0,2) != "62" ? "62".$nohp : $nohp;
					$no2 = substr($nohp,0,2) != "62" ? "0".$nohp : "0".substr($nohp,2);

					$this->db->select("id");
					$this->db->where("id != ".$r->usrid." AND (nohp IN('".$no1."','".$no2."') OR username = '".$input["email"]."')");
					$db = $this->db->get("userdata");

					if($db->num_rows() == 0){
						$this->db->where("usrid",$r->usrid);
						$this->db->update("profil",array("nama"=>$input['nama'],"nohp"=>$input['nohp'],"kelamin"=>$input['kelamin']));
						$this->db->where("id",$r->usrid);
						$this->db->update("userdata",array("nohp"=>$input['nohp'],"username"=>$input['email']));

						echo json_encode(array("success"=>true));
					}else{
						echo json_encode(array("success"=>false,"sesihabis"=>false));
					}
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function simpanpassword(){
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
					
					$this->db->where("id",$r->usrid);
					$this->db->update("userdata",array("password"=>$this->func->encode($input['password'])));
				}
				
				echo json_encode(array("success"=>true));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}

}