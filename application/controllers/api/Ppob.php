<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Ppob extends CI_Controller {

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
	
	// PEMBELIAN
	public function prosestopup(){
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
				
				$this->db->where("id",$input["produk"]);
				$this->db->limit(1);
				$db = $this->db->get("ppob");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$this->db->where("idproduk",$r->id);
						$this->db->where("status <",3);
						$this->db->where("tgl >=",date('Y-m-d H:i:s', strtotime("-10 minutes")));
						$pob = $this->db->get("transaksi_ppob");
	
						if($pob->num_rows() == 0){
							$trx = "TRXPPOB.".date("YmdHis");
							$data = [
								"tgl"	=> date("Y-m-d H:i:s"),
								"usrid"	=> $usr->id,
								"invoice"	=> $trx,
								"idproduk"	=> $r->id,
								"nomer"	=> $input["nomer"],
								"total"	=> $r->harga_jual,
								"bayar"	=> $r->harga_jual,
								"voucher"	=> "",
								"status"=> 0,
								"kadaluarsa"=> date('Y-m-d H:i:s', strtotime("+30 minutes"))
							];
							$this->db->insert("transaksi_ppob",$data);
							$id = $this->db->insert_id();
							$this->dgf->cekDetail($id);
							echo json_encode(array("success"=>true,"result"=>$trx));
						}else{
							echo json_encode(array("success"=>false,"msg"=>"Anda tidak dapat membuat pesanan dgn produk yg sama secara bersamaan, silahkan menunggu 10 menit sebelum membuat transaksi baru","sesihabis"=>false));
						}
					}
				}else{
					echo json_encode(array("success"=>false,"msg"=>"Produk sedang tidak tersedia, silahkan pilih produk atau nominal yg lain","sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"msg"=>"Token Invalid!","sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Unauthorized access!","sesihabis"=>false));
		}
	}
	public function prosestagihan(){
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
				
				$this->db->where("kode",$input["produk"]);
				$this->db->limit(1);
				$db = $this->db->get("ppob");
				$tag = $this->dgf->cekTagihan($input["produk"],$input["nomer"]);
				if($db->num_rows() > 0 && $tag){
					foreach($db->result() as $r){
						$this->db->where("idproduk",$r->id);
						$this->db->where("status <",3);
						$this->db->where("tgl >=",date('Y-m-d H:i:s', strtotime("-10 minutes")));
						$pob = $this->db->get("transaksi_ppob");
	
						if($pob->num_rows() == 0){
							$trx = (isset($tag->ref_id)) ? $tag->ref_id : "TRXPPOB.".date("YmdHis");
							$data = [
								"tgl"	=> date("Y-m-d H:i:s"),
								"usrid"	=> $usr->id,
								"invoice"	=> $trx,
								"idproduk"	=> $r->id,
								"nomer"	=> $input["nomer"],
								"total"	=> $tag->selling_price,
								"bayar"	=> $tag->selling_price,
								"voucher"	=> "",
								"status"=> 0,
								"kadaluarsa"=> date('Y-m-d H:i:s', strtotime("+30 minutes"))
							];
							$this->db->insert("transaksi_ppob",$data);
							$id = $this->db->insert_id();
							echo json_encode(array("success"=>true,"result"=>$trx));
						}else{
							echo json_encode(array("success"=>false,"msg"=>"Anda tidak dapat membuat pesanan dgn produk yg sama secara bersamaan, silahkan menunggu 10 menit sebelum membuat transaksi baru","sesihabis"=>false));
						}
					}
				}else{
					echo json_encode(array("success"=>false,"msg"=>"Produk sedang tidak tersedia, silahkan ulangi beberapa menit lagi atau hubungi admin untuk kendala ini","sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"msg"=>"Token Invalid!","sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Unauthorized access!","sesihabis"=>false));
		}
	}
	public function prosesbayar(){
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
				
				$this->db->where("invoice",$input["trx"]);
				$this->db->where("status",0);
				$this->db->limit(1);
				$db = $this->db->get("transaksi_ppob");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$tgl = date("Y-m-d H:i:s");
						$koin = 0;
						$saldos = 0;
						$bayar = $r->bayar;
						$saldo = $this->func->getSaldo($usr->id,"semua","usrid",true);
						if($input["koin"] > 0 && $saldo->koin > 0){
							if($saldo->koin >= $bayar){
								$koin = $bayar;
								$bayar = 0;
							}else{
								$koin = $saldo->koin;
								$bayar = $bayar - $saldo->koin;
							}
						}
						if($bayar > 0){
							if($saldo->saldo >= $bayar){
								$saldos = $bayar;
								$bayar = 0;
							}else{
								echo json_encode(array("success"=>false,"msg"=>"Saldo Anda tidak mencukupi untuk membayar transaksi ini, silahkan top up terlebih dahulu!"));
								exit;
							}
						}

						$saldoakhir = $saldo->saldo - $saldos;
						$koinakhir = $saldo->koin - $koin;
						$this->db->where("id",$saldo->id);
						$this->db->update("saldo",["saldo"=>$saldoakhir,"koin"=>$koinakhir,"apdet"=>$tgl]);
							
						// SALDO DARI KE
						$data = array(
							"tgl"	=> $tgl,
							"usrid"	=> $usr->id,
							"jenis"	=> 2,
							"jumlah"	=> $saldos,
							"darike"	=> 5,
							"saldoawal"	=> $saldo->saldo,
							"saldoakhir"=> $saldoakhir,
							"sambung"	=> $r->id
						);
						$this->db->insert("saldohistory",$data);

						$this->db->where("id",$r->id);
						$this->db->update("transaksi_ppob",["saldo"=>$saldos,"koin"=>$koin,"status"=>1,"selesai"=>date("Y-m-d H:i:s")]);

						// PROSES API DIGIFLAZZ
						$this->dgf->prosesPesanan($r->id);

						// NOTIFIKASI
						$this->func->notifPPOB($r->id);

						echo json_encode(array("success"=>true,"result"=>$r->id));
					}
				}else{
					echo json_encode(array("success"=>false,"msg"=>"Produk sedang tidak tersedia, silahkan pilih produk atau nominal yg lain","sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"msg"=>"Token Invalid!","sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Unauthorized access!","sesihabis"=>false));
		}
	}

	// CEK TAGIHAN
	public function tagihan(){
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
				
				$tag = $this->dgf->cekTagihan($input['kode'],$input['nomer']);
				if($tag){
					$data = array();
					$data[] = array(
						"id"	=> $tag->ref_id,
						"nama"	=> $tag->customer_name,
						"nomer"	=> $tag->customer_no,
						"harga"	=> $tag->selling_price,
						"detail"	=> isset($tag->desc->detail) ? (array)$tag->desc->detail : []
					);
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

	// GET DATA
	public function transaksi(){
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
				
				$this->db->where("invoice",$input["trx"]);
				$this->db->limit(1);
				$db = $this->db->get("transaksi_ppob");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$prod = $this->func->getPPOB($r->idproduk,'semua');
						$produk = (array)$prod;
						unset($produk['id']);
						unset($produk['tgl']);
						unset($produk['apdet']);
						unset($produk['harga_beli']);
						$kat = $this->func->getPPOBKategori($prod->kategori_id,'semua');
						$iconkat = ($kat->icon) ? $kat->icon : 'default.png';
						$iconp = ($prod->icon) ? $prod->icon : 'default.png';
						$icon = ($prod->tipe == 1) ? $iconkat : $iconp;
						$kategori = ($prod->tipe == 1) ? $kat->nama : $prod->brand;
						$kode = ($prod->tipe == 1) ? "/ppobtopup/".$kat->kode : "/ppobtagihan/".$prod->kode;
						$detail = "";
						if($r->detail){
							$dt = json_decode($r->detail,true);
							foreach($dt as $k=>$v){
								$detail .= ($detail != "") ? "<br/>".$k.": ".$v : $k.": ".$v;
							}
						}
						$data = [
							"invoice"	=> $r->invoice,
							"total"	=> $r->total,
							"diskon"=> $r->diskon,
							"bayar"	=> $r->bayar,
							"nomer"	=> substr_replace($r->nomer,'******',6),
							"icon"	=> base_url('cdn/ppob/'.$icon),
							"status"	=> $r->status,
							"selesai"	=> $r->selesai,
							"kadaluarsa"=> $r->kadaluarsa,
							"produk"	=> $produk,
							"kategori"	=> $kategori,
							"kode"	=> $kode,
							"detail"=> $detail
						];

						echo json_encode(array("success"=>true,"result"=>$data));
					}
				}else{
					echo json_encode(array("success"=>false,"msg"=>"Produk sedang tidak tersedia, silahkan pilih produk atau nominal yg lain","sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"msg"=>"Token Invalid!","sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Unauthorized access!","sesihabis"=>false));
		}
	}
	public function transaksilist(){
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
				
				$page = (isset($_GET["page"]) AND intval($_GET["page"]) > 0) ? $_GET["page"] : 1;
				$perpage = (isset($_GET["perpage"]) AND intval($_GET["perpage"]) > 0) ? $_GET["perpage"] : 10;
				
				$this->db->where("usrid",$r->usrid);
				$rows = $this->db->get("transaksi_ppob");
				$rows = $rows->num_rows();

				$this->db->from('transaksi_ppob');
				$this->db->where("usrid",$r->usrid);
				$this->db->order_by("status ASC,tgl DESC");
				$this->db->limit($perpage,($page-1)*$perpage);
				$db = $this->db->get();
				
				$maxPage = ceil($rows/$perpage);
		
				$hasil = array();
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$prod = $this->func->getPPOB($r->idproduk,'semua');
						$produk = (array)$prod;
						unset($produk['id']);
						unset($produk['tgl']);
						unset($produk['apdet']);
						unset($produk['harga_beli']);
						$kat = $this->func->getPPOBKategori($prod->kategori_id,'semua');
						$iconkat = ($kat->icon) ? $kat->icon : 'default.png';
						$iconp = ($prod->icon) ? $prod->icon : 'default.png';
						$icon = ($prod->tipe == 1) ? $iconkat : $iconp;
						$kategori = ($prod->tipe == 1) ? $kat->nama : $prod->brand;
						$kode = ($prod->tipe == 1) ? "/ppobtopup/".$kat->kode : "/ppobtagihan/".$prod->kode;
						$hasil[] = [
							"invoice"	=> $r->invoice,
							"tgl"	=> $this->func->ubahTgl("d M Y",$r->tgl),
							"total"	=> $r->total,
							"diskon"=> $r->diskon,
							"bayar"	=> $r->bayar,
							"nomer"	=> substr_replace($r->nomer,'******',6),
							"icon"	=> base_url('cdn/ppob/'.$icon),
							"status"	=> $r->status,
							"selesai"	=> $r->selesai,
							"kadaluarsa"=> $r->kadaluarsa,
							"produk"	=> $produk,
							"kategori"	=> $kategori,
							"kode"	=> $kode
						];
					}

					echo json_encode(array("success"=>true,"maxPage"=>$maxPage,"page"=>$page,"data"=>$hasil));
				}else{
					echo json_encode(array("success"=>false,"msg"=>"Produk sedang tidak tersedia, silahkan pilih produk atau nominal yg lain","sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"msg"=>"Token Invalid!","sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Unauthorized access!","sesihabis"=>false));
		}
	}
	
	// PRODUK
	public function produk(){
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
				
				$this->db->where("kategori",$input["kategori"]);
				$this->db->where("brand",$input["brand"]);
				$this->db->order_by("harga_jual","ASC");
				$db = $this->db->get("ppob");
				if($db->num_rows() > 0){
					$data = array();
					foreach($db->result() as $r){
                        $data[] = array(
                            "id"	=> $r->id,
                            "nama"	=> $r->nama,
                            "deskripsi"	=> $r->deskripsi,
                            "harga"	=> $r->harga_jual,
                            "kode"	=> $r->kode
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
	public function produkbrand(){
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
				
				$this->db->where("kategori",$input["kategori"]);
				$this->db->group_by("brand");
				$db = $this->db->get("ppob");
				if($db->num_rows() > 0){
					$data = array();
					foreach($db->result() as $r){
                        $data[] = $r->brand;
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

}