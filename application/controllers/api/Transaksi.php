<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Transaksi extends CI_Controller {

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
	
	// PESANAN
	public function pesanan(){
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
				$status = (isset($_GET["status"]) AND intval($_GET["status"]) > 0) ? $_GET["status"] : 0;
				$page = (isset($_GET["page"]) AND intval($_GET["page"]) > 0) ? $_GET["page"] : 1;
				$perpage = (isset($_GET["perpage"]) AND intval($_GET["perpage"]) > 0) ? $_GET["perpage"] : 6;
				
				$this->db->where("usrid",$r->usrid);
				if($status != 1){
					$this->db->where("status",$status);
				}else{
					$this->db->where("status >=",1);
					$this->db->where("status <=",2);
				}
				$rows = $this->db->get("transaksi");
				$rows = $rows->num_rows();

				$this->db->from('transaksi');
				$this->db->where("usrid",$r->usrid);
				if($status != 1){
					$this->db->where("status",$status);
					$this->db->order_by("status ASC,tgl DESC");
				}else{
					$this->db->where("status >=",1);
					$this->db->where("status <=",2);
					$this->db->order_by("status DESC,tgl DESC");
				}
				$this->db->limit($perpage,($page-1)*$perpage);
				$pro = $this->db->get();
				
				$maxPage = ceil($rows/$perpage);
		
				$hasil = array();
				foreach($pro->result() as $r){
					$bayar = $this->func->getBayar($r->idbayar,"semua");
					$trxproduk = $this->func->getTransaksiProduk($r->id,"semua","idtransaksi");
					$produk = $this->func->getProduk($trxproduk->idproduk,"semua");
					$variasi = $this->func->getVariasi($trxproduk->variasi,"semua");
					//$variasinama = ($trxproduk->variasi != 0) ? $variasi->nama : "";
					$stok = (isset($produk->stok)) ? $produk->stok : 0;
					$variasistok = (is_object($variasi) AND isset($variasi->stok)) ? $variasi->stok : $stok;
					//print_r($variasi); exit;
					$total = $bayar->total - $bayar->kodebayar;
					$review = 0;
					$this->db->where("idtransaksi",$r->id);
					$rev = $this->db->get("review");
					if($rev->num_rows() > 0){
						foreach($rev->result() as $rv){
							$review += $rv->nilai;
						}
						$review = $review > 0 ? round($review/$rev->num_rows(),0) : 0;
					}
					
					if(is_object($produk)){
						//print_r($produk);
						$hasil[] = array(
							"id"	=> $r->id,
							"idbayar"	=> $r->idbayar,
							"orderid"	=> $r->orderid,
							"tgl"	=> $this->func->ubahTgl("d-m-Y H:i",$r->tgl),
							"digital"=> $r->digital,
							"po"=> $r->po,
							"status"=> $r->status,
							"stok"	=> $variasistok,
							"total"	=> $this->func->formUang($total),
							"foto"	=> $this->func->getFoto($trxproduk->idproduk,"utama"),
							"nama"	=> $produk->nama,
							//"variasi"	=> $variasinama,
							"jml"	=> $trxproduk->jumlah,
							"harga"	=> $this->func->formUang($trxproduk->harga),
							"review"=> $review
						);
					}else{
						if($r->status == 0){
							//$this->db->where("usrid",$usr->id);
							$this->db->where("id",$trxproduk->id);
							$this->db->delete("transaksiproduk");
							
							$this->db->where("id",$bayar->id);
							$this->db->delete("pembayaran");
							
							$this->db->where("id",$r->id);
							$this->db->delete("transaksi");
						}
					}
				}
				
				echo json_encode(array("success"=>true,"maxPage"=>$maxPage,"page"=>$page,"data"=>$hasil));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	public function pesanansingle($id=null){
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
				
				$trx = $this->func->getTransaksi($id,"semua");
				if($id != null AND $trx != null){
					$data['digital'] = $trx->digital;
					$data['status'] = $trx->status;
					$data['kadaluarsa'] = $this->func->ubahTgl("D, d M Y H:i",$this->func->getBayar($trx->idbayar,"kadaluarsa"))." WIB";
					if($trx->digital == 0){
						$data['paket'] = $this->func->getPaket($trx->paket,"nama");
						$data['kurir'] = $this->func->getKurir($trx->kurir,"nama");
						$data['ongkir'] = $trx->ongkir;
						
						$this->db->where("id",$trx->alamat);
						$pro = $this->db->get("alamat");
						foreach($pro->result() as $r){
							$kec = $this->func->getKec($r->idkec,"semua");
							$kab = $this->func->getKab($kec->idkab,"nama");
							$data['alamat'][] = array(
								"kab"	=>	$kab,
								"kec"	=>	$kec->nama,
								"judul"	=> $r->judul,
								"alamat"	=> $r->alamat,
								"kodepos"	=> $r->kodepos,
								"nama"	=> $r->nama,
								"nohp"	=> $r->nohp,
								"id"	=> $r->id
							);
						}
					}
					
					$this->db->where("idtransaksi",$id);
					$pro = $this->db->get("transaksiproduk");
					$data['harga'] = 0;
					foreach($pro->result() as $rs){
						$prod = $this->func->getProduk($rs->idproduk,"semua");
						$var = $this->func->getVariasi($rs->variasi,"semua");
						$link = ($trx->status > 0) ? $prod->akses : "belum bayar";
						if($rs->variasi > 0){
							$war = $this->func->getWarna($var->warna,"nama");
							$zar = $this->func->getSize($var->size,"nama");
							$variasea = ($rs->variasi != 0) ? $prod->variasi." ".$war." ".$prod->subvariasi." ".$zar : "";
						}else{
							$variasea = "";
						}
						
						$produk[] = array(
							"foto"	=> $this->func->getFoto($rs->idproduk,"utama"),
							"harga"	=> "Rp ".$this->func->formUang($rs->harga),
							"nama"	=> $prod->nama,
							"jumlah"=> $rs->jumlah,
							"po"	=> $rs->idpo,
							"variasi"	=> $variasea,
							"link"	=> $link
						);
						$data['harga'] = $data['harga'] + ($rs->harga*$rs->jumlah);
					}
					$harga = $data['harga'];
					$data['harga'] = $this->func->formUang($data['harga']);
					if($trx->digital == 0){
						$total = $harga + $data['ongkir'];
						$data['total'] = $this->func->formUang($total);
						$data['ongkir'] = $this->func->formUang($data['ongkir']);
					}else{
						$data['total'] = $data['harga'];
					}
					
					echo json_encode(array("success"=>true,"data"=>$data,"produk"=>$produk));
				}else{
					echo json_encode(array("success"=>false,"sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	public function hapuspesanan($id=0){
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
				
				$trx = $this->func->getTransaksi(intval($input['pid']),"semua");
				
				if(is_object($trx)){
					if($trx->status == 0){
						$this->func->notifbatal($trx->idbayar,2);

						$variasi = [];
						$this->db->where("idtransaksi",$trx->id);
						$db = $this->db->get("transaksiproduk");
						foreach($db->result() as $r){
							if($r->variasi > 0){
								$var = $this->func->getVariasi($r->variasi,"semua","id");
								if(isset($var->stok)){
									$stok = $var->stok + $r->jumlah;
									$variasi[] = $r->variasi;
									$stock[] = $stok;
									$stokawal[] = $var->stok;
									$jml[] = $r->jumlah;
								}
								$pro = $this->func->getProduk($r->idproduk,"semua");
								$stok = $pro->stok + $r->jumlah;
								$this->db->where("id",$r->idproduk);
								$this->db->update("produk",["stok"=>$stok,"tglupdate"=>date("Y-m-d H:i:s")]);
							}else{
								$pro = $this->func->getProduk($r->idproduk,"semua");
								$stok = $pro->stok + $r->jumlah;
								$this->db->where("id",$r->idproduk);
								$this->db->update("produk",["stok"=>$stok,"tglupdate"=>date("Y-m-d H:i:s")]);

								$data = array(
									"usrid"	=> $usr->id,
									"stokawal" => $pro->stok,
									"stokakhir" => $stok,
									"variasi" => 0,
									"jumlah" => $r->jumlah,
									"tgl"	=> date("Y-m-d H:i:s"),
									"idtransaksi" => $trx->id
								);
								$this->db->insert("historystok",$data);
							}
						}
						for($i=0; $i<count($variasi); $i++){
							$this->db->where("id",$variasi[$i]);
							$this->db->update("produkvariasi",["stok"=>$stock[$i],"tgl"=>date("Y-m-d H:i:s")]);
							
							$data = array(
								"usrid"	=> $usr->id,
								"stokawal" => $stokawal[$i],
								"stokakhir" => $stock[$i],
								"variasi" => $variasi[$i],
								"jumlah" => $jml[$i],
								"tgl"	=> date("Y-m-d H:i:s"),
								"idtransaksi" => $trx->id
							);
							$this->db->insert("historystok",$data);
						}
						
						$this->db->where("id",$trx->idbayar);
						$this->db->update("pembayaran",["status"=>3,"tglupdate"=>date("Y-m-d H:i:s")]);
					
						$this->db->where("id",intval($input['pid']));
						$this->db->update("transaksi",["status"=>4]);

						// TOTAL SALDO
						$saldojml = $this->func->getBayar($trx->idbayar,"saldo");
						$saldoawal = $this->func->getSaldo($trx->usrid,"saldo","usrid",true);
		
						if($saldojml > 0){
							// UPDATE SALDO
							$saldo = $saldoawal + $saldojml;
							$this->db->where("usrid",$trx->usrid);
							$this->db->update("saldo",array("saldo"=>$saldo));
		
							// SALDO TARIK
							$tgl = date("Y-m-d H:i:s");
							$data = [
								"usrid"	=> $trx->usrid,
								"trxid"	=> "TOPUP_".$trx->usrid.date("YmdHis"),
								"jenis"	=> 2,
								"status"=> 1,
								"selesai"	=> $tgl,
								"tgl"	=> $tgl,
								"total"	=> $saldojml,
								"metode"=> 1,
								"keterangan"=> "Pengembalian dana dari pembatalan #".$trx->orderid
							];
							$this->db->insert("saldotarik",$data);
							$topup = $this->db->insert_id();
							
							// SALDO DARI KE
							$data = array(
								"tgl"	=> $tgl,
								"usrid"	=> $trx->usrid,
								"jenis"	=> 1,
								"jumlah"	=> $saldojml,
								"darike"	=> 4,
								"saldoawal"	=> $saldoawal,
								"saldoakhir"=> $saldo,
								"sambung"	=> $topup
							);
							$this->db->insert("saldohistory",$data);
						}
					}else{
						$this->db->where("id",$trx->idbayar);
						$this->db->delete("pembayaran");

						$this->db->where("idtransaksi",intval($input['pid']));
						$this->db->delete("transaksiproduk");
						
						$this->db->where("id",intval($input['pid']));
						$this->db->delete("transaksi");
					}
				
					echo json_encode(array("success"=>true));
				}else{
					echo json_encode(array("success"=>false,"sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	public function pembayaran($id=0){
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

				$set = $this->func->globalset("semua");
				$this->db->from('pembayaran');
				$this->db->where("id",$id);
				$this->db->limit(1);
				$pro = $this->db->get();
				$tripay_channel = [];
				$tripays = $this->tripay->metode("semua");
				$channel = $this->tripay->metode();
				if(is_array($channel)){
					foreach($channel as $key => $val){
						if($val["active"] == true){
							$tripay_channel[] = $val;
						}
					}
				}
				$tripay = [];
				$hasil = array();
				foreach($pro->result() as $r){
					$hasilcek = ($r->midtrans_id != "") ? $this->cekmidtrans($r->id) : ["data"=>null,"status"=>0];
					$tripay = $this->tripay->getTripay($r->tripay_ref,"semua","reference");
					$tripay_metode = (in_array($r->tripay_metode,["QRIS","QRISC","QRISOP","QRISCOP"])) ? "QRIS" : $r->tripay_metode;
					$hasil = array(
						"id"	=> $r->id,
						"tgl"	=> $this->func->ubahTgl("d-m-Y H:i",$r->tgl),
						"kadaluarsa"  => $this->func->ubahTgl("D, d M Y H:i",$r->kadaluarsa)." WIB",
						"metode"=> $r->metode_bayar,
						"status"=> $r->status,
						"total"	=> $r->transfer+$r->kodebayar,
						"tripay" => $tripay,
						"tripay_ref" => $r->tripay_ref,
						"tripay_metode" => $tripay_metode,
						"tripay_channel" => $tripay_channel,
						"tripay_pilih_metode" => $tripays,
						"payment_transfer"	=> $set->payment_transfer,
						"payment_ipaymu"	=> 0,
						"payment_midtrans"	=> $set->payment_midtrans,
						"midtrans_id"	=> $r->midtrans_id,
						"midtrans_cek"	=> $hasilcek
					);
				}
				
				$this->db->where("usrid",0);
				$rek = $this->db->get("rekening");
				foreach($rek->result() as $rx){
					$hasil['rekening'][] = array(
						"norek"	=> $rx->norek,
						"atasnama"	=> $rx->atasnama,
						"kcp"	=> $rx->kcp,
						"bank"	=> $this->func->getBank($rx->idbank,"nama")
					);
				}
				
				$hasil['konfirmasi'] = "";
				$this->db->where("idbayar",$id);
				$this->db->limit(1);
				$this->db->order_by("id","DESC");
				$rek = $this->db->get("konfirmasi");
				foreach($rek->result() as $rx){
					$hasil['konfirmasi'] = base_url("cdn/konfirmasi/".$rx->bukti);
				}
				
				echo json_encode(array("success"=>true,"data"=>$hasil));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	public function bayartripay(){
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
					$user = $this->func->getUser($r->usrid,"semua");
					$profil = $this->func->getProfil($r->usrid,"semua","usrid");
					$trx = $input["id"];
					$byr = $this->func->getBayar($trx,"semua");
					$trs = $this->func->getTransaksi($trx,"semua","idbayar");
					$this->db->where("idtransaksi",$trs->id);
					$db = $this->db->get("transaksiproduk");
					$produk = [['sku'=>$byr->invoice,'name'=>"Pembayaran Invoice #".$byr->invoice,'price'=> $byr->transfer,'quantity'=>1]];
					$email = ($user->username != "") ? $user->username : "afdkstore@gmail.com";
					$pembeli = ['nama'=>$profil->nama,'email'=>$email,'nohp'=>$user->nohp];

					/*
					$inv = $_SESSION["usrid"].date("YmdHis");
					$data = array(
						"invoice"	=> $inv,
						"idproduk"	=> $prod->id,
						"tgl"	=> date("Y-m-d H:i:s"),
						"apdet"	=> date("Y-m-d H:i:s"),
						"status"	=> 0,
						"tripay_metode"	=> $_POST["metode"],
						"usrid"	=> $_SESSION["usrid"],
						"jenis"	=> $_POST["tipe"],
						"total"	=> $prod->harga
					);
					$this->db->insert("transaksi",$data);
					$trx = $this->db->insert_id();
					*/

					$res = $this->tripay->createPayment($trx,$input["metode"],$byr->transfer,$pembeli,$produk);

					if($res->success == true){
						echo json_encode(array("success"=>true,"msg"=>"Success"));
					}else{
						echo json_encode(array("success"=>false,"msg"=>"Gagal memproses pembayaran"));
					}
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>true));
		}
	}
	
	// KERANJANG
	public function keranjang(){
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
				
				$this->db->where("idtransaksi",0);
				$this->db->where("usrid",$r->usrid);
				$this->db->order_by("digital,id","DESC");
				$this->db->limit(10);
				$db = $this->db->get("transaksiproduk");
				if($db->num_rows() > 0){
					$totalfisik = 0;
					$totaldigital = 0;
					$fisik = [];
					$digital = [];
					$gudangtot = [];
					foreach($db->result() as $r){
						$produk = $this->func->getProduk($r->idproduk,"semua");
						$var = $this->func->getVariasi($r->variasi,"semua");
						$stok = ($r->variasi != 0) ? $var->stok : $produk->stok;

						if($stok > 0){
							$harga = $r->harga*$r->jumlah;
							if($stok < $r->jumlah){
								$this->db->where("id",$r->id);
								$this->db->update("transaksiproduk",["jumlah"=>$stok]);
								$jumlah = $stok;
							}else{
								$jumlah = $r->jumlah;
							}

							if($var != null){
								$war = $this->func->getWarna($var->warna,"nama");
								$zar = $this->func->getSize($var->size,"nama");
								$variasea = ($r->variasi != 0) ? $produk->variasi." ".$war." ".$produk->subvariasi." ".$zar : "";
							}else{
								$variasea = "";
							}

							$gudang = ($produk->gudang > 0) ? $this->func->getGudang($produk->gudang,'semua') : null;
							$gudang = ($produk->gudang > 0) ? $gudang->nama." (".$this->func->getKab($gudang->idkab,'tipe')." ".$this->func->getKab($gudang->idkab,'nama').")" : "Pusat";
							if(!in_array($produk->gudang,$gudangtot) && $produk->digital == 0){
								$gudangtot[] = $produk->gudang;
							}
							$data = array(
								"foto"	=> $this->func->getFoto($r->idproduk,"utama"),
								"harga"	=> "Rp ".$this->func->formUang($harga),
								"nama"	=> $produk->nama,
								"jumlah"=> intval($jumlah),
								"id"	=> $r->id,
								"idproduk"	=> $r->idproduk,
								"po"	=> $r->idpo,
								"stok"	=> intval($stok),
								"variasi"	=> $variasea,
								"gudang"=> $gudang
							);

							if($produk->digital == 1){
								$digital[] = $data;
								$totaldigital += $r->harga*$r->jumlah;
							}else{
								$fisik[] = $data;
								$totalfisik += $r->harga*$r->jumlah;
							}
						}else{
							$this->db->where("id",$r->id);
							$this->db->delete("transaksiproduk");
						}
					}
					$totalfisik = $this->func->formUang($totalfisik);
					$totaldigital = $this->func->formUang($totaldigital);
					echo json_encode(array("success"=>true,"fisik"=>$fisik,"digital"=>$digital,"totalfisik"=>$totalfisik,"totaldigital"=>$totaldigital,"gudangtot"=> count($gudangtot)));
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
	public function hapuskeranjang(){
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
				
				$this->db->where("id",$input['pid']);
				$this->db->delete("transaksiproduk");
				
				echo json_encode(array("success"=>true));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function tambahkeranjang(){
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
					$prod = $this->func->getProduk($input["idproduk"],"semua");
					$level = isset($usr->level) ? $usr->level : 0;
					if($level == 5){
						$harga = $prod->hargadistri;
					}elseif($level == 4){
						$harga = $prod->hargaagensp;
					}elseif($level == 3){
						$harga = $prod->hargaagen;
					}elseif($level == 2){
						$harga = $prod->hargareseller;
					}else{
						$harga = $prod->harga;
					}

					if($input["jumlah"] < $prod->minorder){
						echo json_encode(array("success"=>false,"msg"=>"Jumlah order kurang dari batas minimal order produk, minimal pembelian: ".$prod->minorder." pcs"));
						exit;
					}

					$keterangan = (isset($input["keterangan"])) ? $input["keterangan"] : "";
					$variasi = (isset($input["variasi"])) ? $input["variasi"] : 0;
					$update = false;
					$this->db->select("id");
					$this->db->where("idproduk",$prod->id);
					$dbs = $this->db->get("produkvariasi");
					if($dbs->num_rows() > 0 AND $variasi == 0){
						echo json_encode(array("success"=>false,"msg"=>"Pilih variasi terlebih dahulu sebelum menambahkan produk ke keranjang belanja"));
						exit;
					}

					// CEK KERANJANG
					$this->db->where("idproduk",$prod->id);
					$this->db->where("variasi",$variasi);
					$this->db->where("idtransaksi",0);
					$this->db->where("usrid",$r->usrid);
					$db = $this->db->get("transaksiproduk");

					if($variasi != 0){
						$var = $this->func->getVariasi($variasi,"semua");
						if($level == 5){
							$harga = $var->hargadistri;
						}elseif($level == 4){
							$harga = $var->hargaagensp;
						}elseif($level == 3){
							$harga = $var->hargaagen;
						}elseif($level == 2){
							$harga = $var->hargareseller;
						}else{
							$harga = $var->harga;
						}

						if(intval($input["jumlah"]) > $var->stok){
							echo json_encode(array("success"=>false,"msg"=>"Stok tidak mencukupi, stok tersedia hanya ".$var->stok." pcs"));
							exit;
						}

						foreach($db->result() as $rs){
							$jumlah = intval($input["jumlah"]) + $rs->jumlah;
							if($jumlah > $var->stok){
								echo json_encode(array("success"=>false,"msg"=>"Stok tidak mencukupi, stok tersedia hanya ".$var->stok." pcs, di keranjang belanja Anda sudah ada produk yang sama, setelah dijumlahkan melebihi stok yg tersedia saat ini"));
								exit;
							}else{
								$update = true;
								$id = $rs->id;
							}
						}
					}else{
						if(intval($input["jumlah"]) > $prod->stok){
							echo json_encode(array("success"=>false,"msg"=>"Stok tidak mencukupi, stok tersedia hanya ".$prod->stok." pcs"));
							exit;
						}
		
						foreach($db->result() as $rs){
							$jumlah = intval($input["jumlah"]) + $rs->jumlah;
							if($jumlah > $prod->stok){
								echo json_encode(array("success"=>false,"msg"=>"Stok tidak mencukupi, stok tersedia hanya ".$prod->stok." pcs, di keranjang belanja Anda sudah ada produk yang sama, setelah dijumlahkan melebihi stok yg tersedia saat ini"));
								exit;
							}else{
								$update = true;
								$id = $rs->id;
							}
						}
					}
					
					if($update == false){
						$data = array(
							"usrid"		=> $r->usrid,
							"digital"	=> $prod->digital,
							"idproduk"	=> $input["idproduk"],
							"tgl"		=> date("Y-m-d H:i:s"),
							"jumlah"	=> $input["jumlah"],
							"harga"		=> $harga,
							"hargabeli"	=> $prod->hargabeli,
							"margin"	=> $harga - $prod->hargabeli,
							"keterangan"=> $keterangan,
							"variasi"	=> $variasi,
							"idtransaksi"	=> 0
						);
						if($this->db->insert("transaksiproduk",$data)){
							echo json_encode(array("success"=>true,"result"=>$data));
						}else{
							echo json_encode(array("success"=>false,"msg"=>"terjadi kesalahan saat memproses pesanan, mohon diulangi beberapa menit kemudian"));
						}
					}else{
						$this->db->where("id",$id);
						$this->db->update("transaksiproduk",["jumlah"=>$jumlah,"harga"=>$harga,"tgl"=>date("Y-m-d H:i:s"),"keterangan"=> $keterangan."\n".$rs->keterangan]);
						echo json_encode(array("success"=>true));
					}
				}else{
					echo json_encode(array("success"=>false,"sesihabis"=>true,"msg"=>"terjadi kesalahan saat memproses pesanan, mohon diulangi beberapa menit kemudian"));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true,"msg"=>"terjadi kesalahan saat memproses pesanan, mohon diulangi beberapa menit kemudian"));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false,"msg"=>"terjadi kesalahan saat memproses pesanan, mohon diulangi beberapa menit kemudian"));
		}
	}
	public function updatekeranjang(){
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

				if(isset($input["update"]) AND $input["update"] > 0){
					$id = $input["update"];
					unset($input["update"]);
		
					$trx = $this->func->getTransaksiProduk($id,"semua");
					$stok = ($trx->variasi > 0) ? $this->func->getVariasi($trx->variasi,"stok") : $this->func->getProduk($trx->idproduk,"stok");
					if($stok >= intval($input["jumlah"])){
						$this->db->where("id",$id);
						$this->db->update("transaksiproduk",$input);
		
						echo json_encode(array("success"=>true,"stok"=>$stok,"token"=>$this->security->get_csrf_hash()));
					}else{
						$this->db->where("id",$id);
						$this->db->update("transaksiproduk",["jumlah"=>$stok]);
		
						echo json_encode(array("success"=>false,"stok"=>$stok,"msg"=>"Stok produk tidak mencukupi, maksimal pemesanan ".$stok."pcs","token"=>$this->security->get_csrf_hash()));
					}
				}else{
					echo json_encode(array("success"=>false,"msg"=>"Produk tidak tersedia","token"=>$this->security->get_csrf_hash()));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}

	public function bayarpesanan(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					/*$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));*/
					$usrid = $r->usrid;
				}
				
				$this->db->where("idtransaksi",0);
				$this->db->where("digital",0);
				$this->db->where("usrid",$r->usrid);
				$this->db->order_by("id","DESC");
				//$this->db->limit(10);
				$db = $this->db->get("transaksiproduk");
				
				$totalharga = 0;
				$berat = 0;
				$cod = 0;
				$gudang = "none";
				$produk = [];
				
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$pro = $this->func->getProduk($r->idproduk,"semua");
						$var = $this->func->getVariasi($r->variasi,"semua");
						$stok = ($r->variasi != 0) ? $var->stok : $pro->stok;
						if($var != null){
							$war = $this->func->getWarna($var->warna,"nama");
							$zar = $this->func->getSize($var->size,"nama");
							$variasea = ($r->variasi != 0) ? $pro->variasi." ".$war." ".$pro->subvariasi." ".$zar : "";
						}else{
							$variasea = "";
						}
						
						$gudang = ($gudang == "none") ? $pro->gudang : $gudang;
						if($stok >= $r->jumlah AND $pro->digital == 0 AND $pro->gudang == $gudang){
							$harga = $r->harga;
							$totalharga += $harga*$r->jumlah;
							$berat += $pro->berat * $r->jumlah;
							$produk[] = array(
								"foto"	=> $this->func->getFoto($r->idproduk,"utama"),
								"harga"	=> "Rp ".$this->func->formUang($harga),
								"nama"	=> $pro->nama,
								"jumlah"=> $r->jumlah,
								"id"	=> $r->id,
								"po"	=> $r->idpo,
								"variasi"	=> $variasea
							);
						}
					}
					
					if(count($produk) > 0){
						$set = $this->func->globalset("semua");
						$biayacod = $set->biaya_cod <= 0 ? 0 : $totalharga * ($set->biaya_cod/100);
						$biayacod = $set->biaya_cod > 100 ? $set->biaya_cod : $biayacod;
						echo json_encode(
							array(
								"success"	=> true,
								"payment_cod"	=> $set->payment_cod,
								"biaya_cod"	=> $biayacod,
								"payment_transfer"	=> $set->payment_transfer,
								"payment_tripay"	=> $set->payment_tripay,
								"payment_ipaymu"=> 0,
								"payment_midtrans"	=> $set->payment_midtrans,
								"produk"	=> $produk,
								"totalharga"=> $totalharga,
								"berat"	=> $berat,
								"gudang"=> $gudang,
								"saldo"	=> $this->func->getSaldo($usrid,"saldo","usrid",true)
							)
						);
					}else{
						echo json_encode(array("success"=>false,"sesihabis"=>false));
					}
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
	public function bayarpesanandigital(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					/*$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));*/
					$usrid = $r->usrid;
				}
				
				
				$this->db->where("idtransaksi",0);
				$this->db->where("digital",1);
				$this->db->where("usrid",$r->usrid);
				$this->db->order_by("id","DESC");
				//$this->db->limit(10);
				$db = $this->db->get("transaksiproduk");
				
				$totalharga = 0;
				$berat = 0;
				$cod = 0;
				
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$harga = $r->harga;
						$totalharga += $harga*$r->jumlah;
						$pro = $this->func->getProduk($r->idproduk,"semua");
						$var = $this->func->getVariasi($r->variasi,"semua");
						$stok = ($r->variasi != 0) ? $var->stok : $pro->stok;
						$berat += $pro->berat * $r->jumlah;
						if($var != null){
							$war = $this->func->getWarna($var->warna,"nama");
							$zar = $this->func->getSize($var->size,"nama");
							$variasea = ($r->variasi != 0) ? $pro->variasi." ".$war." ".$pro->subvariasi." ".$zar : "";
						}else{
							$variasea = "";
						}
						
						if($stok >= $r->jumlah AND $pro->digital == 1){
							$produk[] = array(
								"foto"	=> $this->func->getFoto($r->idproduk,"utama"),
								"harga"	=> "Rp ".$this->func->formUang($harga),
								"nama"	=> $pro->nama,
								"jumlah"=> $r->jumlah,
								"id"	=> $r->id,
								"po"	=> $r->idpo,
								"variasi"	=> $variasea
							);
						}
					}
					
					if(count($produk) > 0){
						$set = $this->func->globalset("semua");
						$biayacod = $set->biaya_cod <= 0 ? 0 : $totalharga * ($set->biaya_cod/100);
						$biayacod = $set->biaya_cod > 100 ? $set->biaya_cod : $biayacod;
						echo json_encode(
							array(
								"success"=>true,
								"payment_cod"=>$cod,
								"biaya_cod"=>$biayacod,
								"payment_transfer"=>$set->payment_transfer,
								"payment_tripay"=>$set->payment_tripay,
								"payment_ipaymu"=>0,
								"payment_midtrans"=>$set->payment_midtrans,
								"produk"=>$produk,
								"totalharga"=>$totalharga,
								"saldo"=>$this->func->getSaldo($usrid,"saldo","usrid",true)
							)
						);
					}else{
						echo json_encode(array("success"=>false,"sesihabis"=>false));
					}
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
	function terimapesanan(){
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

				$this->db->where("id",$input["id"]);
				if($this->db->update("transaksi",array("status"=>3,"selesai"=>date("Y-m-d H:i:s")))){
					$trx = $this->func->getTransaksi($input["id"],"semua");
					
					$this->db->where("idtransaksi",$trx->id);
					$db = $this->db->get("afiliasi");
					foreach($db->result() as $r){
						$saldo = $this->func->getSaldo($r->usrid,"semua","usrid",true);
						$saldototal = $saldo->saldo + $r->jumlah;
						$tgl = date("Y-m-d H:i:s");
						$data = [
							"usrid"	=> $r->usrid,
							"trxid"	=> "TOPUP_".$r->usrid.date("YmdHis"),
							"jenis"	=> 2,
							"status"=> 1,
							"selesai"	=> $tgl,
							"tgl"	=> $tgl,
							"total"	=> $r->jumlah,
							"metode"=> 1,
							"keterangan"=> "Pencairan komisi dari transaksi #".$trx->orderid
						];
						$this->db->insert("saldotarik",$data);
						$topup = $this->db->insert_id();

						$data2 = [
							"tgl"	=> $tgl,
							"usrid"	=> $r->usrid,
							"jenis"	=> 1,
							"jumlah"=> $r->jumlah,
							"darike"=> 1,
							"sambung"	=> $topup,
							"saldoawal"	=> $saldo->saldo,
							"saldoakhir"=> $saldototal
						];
						$this->db->insert("saldohistory",$data2);

						$this->db->where("id",$saldo->id);
						$this->db->update("saldo",["apdet"=>$tgl,"saldo"=>$saldototal]);
						
						$this->db->where("id",$r->id);
						$this->db->update("afiliasi",["status"=>2,"cair"=>date("Y-m-d H:i:s"),"saldotarik"=>$topup]);
					}

					echo json_encode(array("success"=>true,"message"=>"Success!"));
				}else{
					echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
				}
			}
		}
	}
	function cekvoucher(){
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
				$voc = $this->func->getVoucher($input["kode"],"semua","kode");
				
				if(is_object($voc) AND $voc->digital == $input["digital"]){
					$this->db->select("id");
					$this->db->where("voucher",$voc->id);
					$this->db->where("usrid",$usr->id);
					$this->db->where("status <",2);
					$sudah = $this->db->get("pembayaran");
	
					$tgla = $this->func->ubahTgl("YmdHis",$voc->mulai);
					$tglb = $this->func->ubahTgl("YmdHis",$voc->selesai);
					if($tgla <= date("YmdHis") AND $tglb >= date("YmdHis") AND $sudah->num_rows() < $voc->peruser){
						$harga = isset($input["harga"]) ? intval($input["harga"]) : 0;
						$ongkir = isset($input["ongkir"]) ? intval($input["ongkir"]) : 0;
						if($voc->jenis == 1){
							if($voc->tipe == 2){
								$diskon = ($harga >= $voc->potonganmin) ? $voc->potongan : 0;
							}else{
								$diskon = ($harga >= $voc->potonganmin) ? $harga * ($voc->potongan/100) : 0;
							}
							$diskonmax = $diskon;
							$diskon = ($harga >= $diskon) ? $diskon : $harga;
						}elseif($voc->jenis == 2){
							if($voc->tipe == 2){
								$diskon = ($harga >= $voc->potonganmin) ? $voc->potongan : 0;
							}else{
								$diskon = ($harga >= $voc->potonganmin) ? $ongkir * ($voc->potongan/100) : 0;
							}
							$diskonmax = $diskon;
							$diskon = ($ongkir >= $diskon) ? $diskon : $ongkir;
						}else{
							$diskon = 0;
							$diskonmax = 0;
						}
						if($voc->potonganmaks != 0){
							$diskon = ($diskon < $voc->potonganmaks) ? $diskon : $voc->potonganmaks;
						}
						echo json_encode(["success"=>true,"diskon"=>$diskon,"diskonmax"=>$diskonmax,"nama"=>$voc->nama,"token"=>$this->security->get_csrf_hash()]);
					}else{
						echo json_encode(["success"=>false,"token"=>$this->security->get_csrf_hash(),"msg"=>"masa berlaku habis, atau kuota penggunaan sudah penuh, Anda sudah menggunakan voucher ini ".$sudah->num_rows()." kali"]);
					}
				}else{
					echo json_encode(["success"=>false,"token"=>$this->security->get_csrf_hash(),"msg"=>"voucher tidak ditemukan, atau tidak sesuai dengan jenis produk yang akan Anda beli"]);
				}

			}else{
				echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
			}
		}else{
			echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
		}
	}
	function getvoucher(){
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

                $this->db->where("mulai <=",date("Y-m-d"));
                $this->db->where("selesai >=",date("Y-m-d"));
                $this->db->where("public",1);
                $voc = $this->db->get("voucher");
				$digital = (isset($_GET["digital"])) ? $_GET["digital"] : 0;
				if($voc->num_rows() > 0){
					$data = [];
					foreach($voc->result() as $v){
						if($digital == $v->digital){
							$pot = $this->func->formUang($v->potongan);
							$potongan = ($v->tipe == 2) ? "<div class=\"font-bold fs-24 text-success text-center p-tb-12\">Rp ".$pot."</div>" : '<div class="font-bold fs-38 text-success text-center p-tb-0">'.$pot."%</div>";
							$jenis = ($v->jenis == 1) ? "Harga" : "Ongkir";
							$data[] = [
								"nama"	=> $v->nama,
								"deskripsi"	=> $v->deskripsi,
								"jenis"	=> $v->jenis,
								"potongan"	=> $v->potongan,
								"potonganmin"	=> $v->potonganmin,
								"tipe"	=> $v->tipe,
								"kode"	=> $v->kode,
								"digital"	=> $v->digital
							];
						}
					}

					echo json_encode(["success"=>true,"result"=>$data]);
				}else{
					echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
				}
			}else{
				echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
			}
		}else{
			echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
		}
	}
	function cekout(){
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
				
				$text = "";
				$produkwa = "";
				$hrgwatotal = 0;
				$idbayar = 0;
				$kodebayar = rand(100,999);
				$toko = $this->func->globalset("semua");
				$wa = (isset($_GET["wa"])) ? $_GET["wa"] : null;
				//$input["total"] = intval($input["total"]) - intval($input["diskon"]);
				$transfer = intval($input["total"]) - intval($input["saldo"]);
				if($input["metodebayar"] == 2){
					$total = $kodebayar + intval($input["total"]);
				}else{
					$total = $input["total"];
					$kodebayar = 0;
				}

				$input["metodebayar"] = ($input["metodebayar"] == null) ? 0 : $input["metodebayar"];
				$bcod = ($input["metodebayar"] == 1) ? $toko->biaya_cod : 0;
				$status = 0;
				$seli = intval($input["saldo"])-intval($input["total"]);
				$status = ($seli >= 0) ? 1 : 0;
				$status = ($input["metodebayar"] == 1) ? 1 : $status;
				//$status = (strtolower($input["kurir"]) == "bayar") ? 1 : $status;
				$idalamat = $input["alamat"];

				$voucher = $this->func->getVoucher($input["voucher"],"id","kode");
				$bayar = array(
					"usrid"	=> $r->usrid,
					"tgl"	=> date("Y-m-d H:i:s"),
					"total"	=> $total,
					"saldo"	=> $input["saldo"],
					"kodebayar"	=> $kodebayar,
					"transfer"	=> $transfer,
					"voucher"	=> $voucher,
					"metode"	=> $input["metode"],
					"metode_bayar"	=> $input["metodebayar"],
					"biaya_cod"	=> $bcod,
					"diskon"	=> $input["diskon"],
					"status"	=> $status,
					"kadaluarsa"	=> date('Y-m-d H:i:s', strtotime("+2 days"))
				);
				$this->db->insert("pembayaran",$bayar);
				$idbayar = $this->db->insert_id();

				if($input["metode"] == 2){
					$saldoawal = $this->func->getSaldo($r->usrid,"saldo","usrid",true);
					$saldoakhir = $saldoawal - intval($input["saldo"]);
					$this->db->where("usrid",$r->usrid);
					$this->db->update("saldo",array("saldo"=>$saldoakhir,"apdet"=>date("Y-m-d H:i:s")));

					$sh = array(
						"tgl"	=> date("Y-m-d H:i:s"),
						"usrid"	=> $r->usrid,
						"jenis"	=> 2,
						"jumlah"	=> $input["saldo"],
						"darike"	=> 3,
						"sambung"	=> $idbayar,
						"saldoawal"	=> $saldoawal,
						"saldoakhir"	=> $saldoakhir
					);
					$this->db->insert("saldohistory",$sh);
				}

				$this->db->where("id",$idbayar);
				$this->db->update("pembayaran",array("invoice"=>date("Ymd").$idbayar.$kodebayar));
				$invoice = "#".date("Ymd").$idbayar.$kodebayar;

				$transaksi = array(
					"orderid"	=> "TRX".date("YmdHis"),
					"tgl"	=> date("Y-m-d H:i:s"),
					"kadaluarsa"	=> date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' + 2 days')),
					"usrid"	=> $r->usrid,
					"alamat"	=> $idalamat,
					"berat"	=> $input["berat"],
					"ongkir"	=> $input["ongkir"],
					"kurir"	=> strtolower($input["kurir"]),
					"paket"	=> $input["paket"],
					"dari"	=> $input["dari"],
					"tujuan"	=> $input["tujuan"],
					"status"	=> $status,
					"biaya_cod"	=> $bcod,
					"idbayar"	=> $idbayar
				);
				if($status == 1){
					$transaksi["tglupdate"] = date("Y-m-d H:i:s");
				}
				if($input["dropship"] != ""){
					$transaksi["dropship"] = $input["dropship"];
					$transaksi["dropshipnomer"] = $input["dropshipnomer"];
					$transaksi["dropshipalamat"] = $input["dropshipalamat"];
				}
				$this->db->insert("transaksi",$transaksi);
				$idtransaksi = $this->db->insert_id();
				
				$idproduk = explode("|",$input['idproduk']);
				for($i=0; $i<count($idproduk); $i++){
					$this->db->where("id",$idproduk[$i]);
					$this->db->update("transaksiproduk",array("idtransaksi"=>$idtransaksi));
				}
					
				// UPDATE STOK PRODUK
				$this->db->where("idtransaksi",$idtransaksi);
				$db = $this->db->get("transaksiproduk");
				$nos = 1;
				$po = 0;
				$afiliasi = 0;
				if($db->num_rows() == 0){ $produkwa = "TIDAK ADA PRODUK\n\n"; }
				foreach($db->result() as $r){
					$pro = $this->func->getProduk($r->idproduk,"semua");
					$afiliasi += $pro->afiliasi * $r->jumlah;
					$po = ($pro->preorder > 0 AND $pro->pohari > $po) ? $pro->pohari : $po;
					if($r->variasi != 0){
						$var = $this->func->getVariasi($r->variasi,"semua","id");
						if($r->jumlah > $var->stok){
							echo json_encode(array("success"=>false,"message"=>"stok produk tidak mencukupi"));
							$stok = 0;
							exit;
						}

						$stok = $var->stok - $r->jumlah;
						$prostok = $pro->stok - $r->jumlah;
						$this->db->where("id",$r->idproduk);
						$this->db->update("produk",["stok"=>$prostok,"tglupdate"=>date("Y-m-d H:i:s")]);
							
						$variasi[] = $r->variasi;
						$stock[] = $stok;
						$stokawal[] = $var->stok;
						$jml[] = $r->jumlah;

						for($i=0; $i<count($variasi); $i++){
							$this->db->where("id",$variasi[$i]);
							$this->db->update("produkvariasi",["stok"=>$stock[$i],"tgl"=>date("Y-m-d H:i:s")]);
							
							$data = array(
								"usrid"	=> $r->usrid,
								"stokawal" => $stokawal[$i],
								"stokakhir" => $stock[$i],
								"variasi" => $variasi[$i],
								"jumlah" => $jml[$i],
								"tgl"	=> date("Y-m-d H:i:s"),
								"idtransaksi" => $idtransaksi
							);
							$this->db->insert("historystok",$data);
						}
					}else{
						if($r->jumlah > $pro->stok){
							echo json_encode(array("success"=>false,"message"=>"stok produk tidak mencukupi"));
							$stok = 0;
							exit;
						}
						$stok = $pro->stok - $r->jumlah;
						$this->db->where("id",$r->idproduk);
						$this->db->update("produk",["stok"=>$stok,"tglupdate"=>date("Y-m-d H:i:s")]);

						$data = array(
							"usrid"	=> $usr->id,
							"stokawal" => $pro->stok,
							"stokakhir" => $stok,
							"variasi" => 0,
							"jumlah" => $r->jumlah,
							"tgl"	=> date("Y-m-d H:i:s"),
							"idtransaksi" => $idtransaksi
						);
						$this->db->insert("historystok",$data);
					}

					if($wa != null){
						$variasee = $this->func->getVariasi($r->variasi,"semua");
						$hargawa = $pro->harga;
						if(isset($variasee->harga)){
							$hargawa = $variasee->harga;
							if($usr->level == 5){
								$hargawa = $variasee->hargadistri;
							}elseif($usr->level == 4){
								$hargawa = $variasee->hargaagensp;
							}elseif($usr->level == 3){
								$hargawa = $variasee->hargaagen;
							}elseif($usr->level == 2){
								$hargawa = $variasee->hargareseller;
							}
						}else{
							if($usr->level == 5){
								$hargawa = $pro->hargadistri;
							}elseif($usr->level == 4){
								$hargawa = $pro->hargaagensp;
							}elseif($usr->level == 3){
								$hargawa = $pro->hargaagen;
							}elseif($usr->level == 2){
								$hargawa = $pro->hargareseller;
							}
						}
						$hargawatotal = $hargawa*$r->jumlah;
						$hrgwatotal += $hargawatotal;
						$variaksi = ($r->variasi != 0 AND $variasee != null) ? $this->func->getWarna($variasee->warna,"nama")." ".$pro->subvariasi." ".$this->func->getSize($variasee->size,"nama") : "";
						$produkwa .= "*".$nos.". ".$pro->nama."*\n";
						$produkwa .= ($r->variasi != 0 AND $variasee != null) ? "    Varian : ".$variaksi."\n" : "";
						$produkwa .= "    Jumlah : ".$r->jumlah."\n";
						$produkwa .= "    Harga (@) : Rp ".$this->func->formUang($hargawa)."\n";
						$produkwa .= "    Harga Total : Rp ".$this->func->formUang($hargawatotal)."\n \n";
						$nos++;
					}
				}
				$this->db->where("id",$idtransaksi);
				$this->db->update("transaksi",['po'=>$po]);
				
				// AFILIASI
				if($usr->upline > 0 AND $afiliasi > 0){
					$affs = array(
						"tgl"	=> date("Y-m-d H:i:s"),
						"usrid"	=> $usr->upline,
						"idtransaksi"	=> $idtransaksi,
						"status"=> $status,
						"jumlah"=> $afiliasi
					);
					$this->db->insert("afiliasi",$affs);
				}

				$idbayaran = $idbayar;
				//$idbayar = $this->func->arrEnc(array("idbayar"=>$idbayar),"encode");

				$usrid = $this->func->getUser($r->usrid,"semua");
				$profil = $this->func->getProfil($r->usrid,"semua","usrid");
				$alamat = $this->func->getAlamat($idalamat,"semua","id",true);
				$kec = $this->func->getKec($alamat->idkec,"semua");
				$kab = $this->func->getKab($kec->idkab,"nama");
				$alamatz = $alamat->alamat.", ".$kec->nama.", ".$kab." - ".$alamat->kodepos;
				$diskon = $input["diskon"] != 0 ? "Diskon: <b>Rp ".$this->func->formUang(intval($input["diskon"]))."</b><br/>" : "";
				$diskonwa = $input["diskon"] != 0 ? "Diskon: *Rp ".$this->func->formUang(intval($input["diskon"]))."*\n" : "";
				$kurir = $this->func->getKurir($input["kurir"],"nama");
				$paket = $this->func->getPaket($input["paket"],"nama");

				$text = "Halo kak admin ".$this->func->globalset("nama").", saya mau order produk berikut dong\n\n";
				$text .= $produkwa;
				$text .= "Subtotal : *Rp ".$this->func->formUang($hrgwatotal)."*\n";
				$text .= "Ongkos Kirim : *Rp ".$this->func->formUang($input["ongkir"])."*\n";
				$text .= "Diskon : *Rp ".$this->func->formUang($input["diskon"])."*\n";
				$text .= "Total : *Rp ".$this->func->formUang($input["total"])."*\n";
				$text .= "------------------------------\n\n";
				$text .= "*Nama Penerima*\n";
				$text .= $alamat->nama." (".$alamat->nohp.")\n\n";
				$text .= "*Alamat Pengiriman*\n";
				$text .= $alamatz."\n\n";
				$text .= "*Jasa Kurir*\n";
				$text .= strtoupper($kurir." ".$paket);

				if($wa == null){
					$pesan = "
						Halo <b>".$profil->nama."</b><br/>".
						"Terimakasih sudah membeli produk kami.<br/>".
						"Saat ini kami sedang menunggu pembayaran darimu sebelum kami memprosesnya. Sebagai informasi, berikut detail pesananmu <br/>".
						"No Invoice: <b>".$invoice."</b><br/> <br/>".
						"Total Pesanan: <b>Rp ".$this->func->formUang($total)."</b><br/>";
					$pesan .= "Ongkos Kirim: <b>Rp ".$this->func->formUang(intval($input["ongkir"]))."</b><br/>".$diskon.
						"Kurir Pengiriman: <b>".strtoupper($kurir)."</b><br/> <br/>".
						"Detail Pengiriman <br/>".
						"Penerima: <b>".$alamat->nama."</b> <br/>".
						"No HP: <b>".$alamat->nohp."</b> <br/>".
						"Alamat: <b>".$alamatz."</b>".
						"<br/> <br/>";
					if($input["metodebayar"] == 2){
						$pesan .= "Berikut informasi rekening untuk pembayaran pesanan<br/>";
						$this->db->where("usrid",0);
						$rek = $this->db->get("rekening");
						foreach($rek->result() as $re){
							$pesan .= "<b style='font-size:120%'>".$this->func->getBank($re->idbank,"nama")." ".$re->norek."</b><br/>";
							$pesan .= "a/n ".$re->atasnama."<br/> <br/>";
						}
						$pesan .= "Untuk konfirmasi pembayaran silahkan langsung klik link berikut:<br/>".
							"<a href='".site_url("manage/pesanan")."?konfirmasi=".$idbayar."'>Bayar Pesanan Sekarang &raquo;</a>
						";
					}else{
						$pesan .= "Untuk pembayaran silahkan langsung klik link berikut:<br/>".
							"<a href='".site_url("home/invoice")."?inv=".$idbayar."'>Bayar Pesanan Sekarang &raquo;</a>
						";
					}
					$this->func->sendEmail($usrid->username,$toko->nama." - Pesanan",$pesan,"Pesanan");
					$pesan = "
						Halo *".$profil->nama."*\n".
						"Terimakasih sudah membeli produk kami.\n".
						"Saat ini kami sedang menunggu pembayaran darimu sebelum kami memprosesnya. Sebagai informasi, berikut detail pesananmu \n \n".
						"No Invoice: *".$invoice."*\n".
						"Total Pesanan: *Rp ".$this->func->formUang($total)."*\n";
					$pesan .= "Ongkos Kirim: *Rp ".$this->func->formUang(intval($input["ongkir"]))."*\n".$diskonwa.
						"Kurir Pengiriman: *".strtoupper($kurir)."*\n \n".
						"Detail Pengiriman \n".
						"Penerima: *".$alamat->nama."*\n".
						"No HP: *".$alamat->nohp."*\n".
						"Alamat: *".$alamatz."*\n \n";
					if($input["metodebayar"] == 2){
						$pesan .= "Berikut informasi rekening untuk pembayaran pesanan \n";
						foreach($rek->result() as $re){
							$pesan .= "*".$this->func->getBank($re->idbank,"nama")." ".$re->norek."* \n";
							$pesan .= "a/n ".$re->atasnama."\n \n";
						}
						$pesan .= "Untuk konfirmasi pembayaran silahkan langsung klik link berikut\n".site_url("manage/pesanan")."?konfirmasi=".$idbayar;
					}else{
						$pesan .= "Untuk pembayaran silahkan langsung klik link berikut\n".site_url("home/invoice")."?inv=".$idbayar;
					}
					$this->func->sendWA($profil->nohp,$pesan);

					// SEND NOTIFICATION MOBILE
					$this->func->notifMobile("Pesanan ".$invoice,"Segera lakukan pembayaran agar pesananmu juga segera diproses","",$usrid->id);
					
					$pesan = "
						<h3>Pesanan Baru</h3><br/>
						<b>".strtoupper(strtolower($profil->nama))."</b> telah membuat pesanan baru dengan total pembayaran 
						<b>Rp. ".$this->func->formUang($total)."</b> Invoice ID: <b>".$invoice."</b>
						<br/>&nbsp;<br/>&nbsp;<br/>
						Cek Pesanan Pembeli di Dashboard Admin ".$toko->nama."<br/>
						<a href='".site_url("cdn")."'>Klik Disini</a>
					";
					$this->func->sendEmail($toko->email,$toko->nama." - Pesanan Baru",$pesan,"Pesanan Baru di ".$toko->nama);
					$pesan = "
						*Pesanan Baru*\n".
						"*".strtoupper(strtolower($profil->nama))."* telah membuat pesanan baru dengan detail:\n".
						"Total Pembayaran: *Rp. ".$this->func->formUang($total)."*\n".
						"Invoice ID: *".$invoice."*".
						"\n \n".
						"Cek Pesanan Pembeli di *Dashboard Admin ".$toko->nama."*
						"; 
					$this->func->sendWA($toko->wasap,$pesan);
				}

				//$url = $status == 0 ? site_url("home/invoice")."?inv=".$idbayar : site_url("manage/pesanan");
				echo json_encode(array("success"=>true,"status"=>$status,"inv"=>$idbayaran,"text"=>$text));
			/*}else{
				echo json_encode(array("success"=>false,"idbayar"=>0));
			}*/
			}else{
				echo json_encode(array("success"=>false,"message"=>"forbidden"));
			}
		}else{
			echo json_encode(array("success"=>false,"message"=>"forbidden"));
		}
	}
	function cekoutdigital(){
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
				
				$text = "";
				$produkwa = "";
				$hrgwatotal = 0;
				$idbayar = 0;
				$kodebayar = rand(100,999);
				$toko = $this->func->globalset("semua");
				$wa = (isset($_GET["wa"])) ? $_GET["wa"] : null;
				$transfer = intval($input["total"]) - intval($input["saldo"]);
				if($input["metodebayar"] == 2){
					$total = $kodebayar + intval($input["total"]);
				}else{
					$total = $input["total"];
					$kodebayar = 0;
				}

				$input["metodebayar"] = ($input["metodebayar"] == null) ? 0 : $input["metodebayar"];
				$bcod = ($input["metodebayar"] == 1) ? $toko->biaya_cod : 0;
				$status = 0;
				$seli = intval($input["saldo"])-intval($input["total"]);
				$status = ($seli >= 0) ? 1 : 0;
				$status = ($input["metodebayar"] == 1) ? 1 : $status;

				$voucher = $this->func->getVoucher($input["voucher"],"id","kode");
				$bayar = array(
					"usrid"	=> $r->usrid,
					"tgl"	=> date("Y-m-d H:i:s"),
					"total"	=> $total,
					"saldo"	=> $input["saldo"],
					"digital"=> 1,
					"kodebayar"	=> $kodebayar,
					"transfer"	=> $transfer,
					"voucher"	=> $voucher,
					"metode"	=> $input["metode"],
					"metode_bayar"	=> $input["metodebayar"],
					"diskon"	=> $input["diskon"],
					"status"	=> $status,
					"kadaluarsa"	=> date('Y-m-d H:i:s', strtotime("+2 days"))
				);
				$this->db->insert("pembayaran",$bayar);
				$idbayar = $this->db->insert_id();

				if($input["metode"] == 2){
					$saldoawal = $this->func->getSaldo($r->usrid,"saldo","usrid",true);
					$saldoakhir = $saldoawal - intval($input["saldo"]);
					$this->db->where("usrid",$r->usrid);
					$this->db->update("saldo",array("saldo"=>$saldoakhir,"apdet"=>date("Y-m-d H:i:s")));

					$sh = array(
						"tgl"	=> date("Y-m-d H:i:s"),
						"usrid"	=> $r->usrid,
						"jenis"	=> 2,
						"jumlah"	=> $input["saldo"],
						"darike"	=> 3,
						"sambung"	=> $idbayar,
						"saldoawal"	=> $saldoawal,
						"saldoakhir"	=> $saldoakhir
					);
					$this->db->insert("saldohistory",$sh);
				}

				$this->db->where("id",$idbayar);
				$this->db->update("pembayaran",array("invoice"=>date("Ymd").$idbayar.$kodebayar));
				$invoice = "#".date("Ymd").$idbayar.$kodebayar;

				$transaksi = array(
					"orderid"	=> "TRX".date("YmdHis"),
					"tgl"	=> date("Y-m-d H:i:s"),
					"kadaluarsa"	=> date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' + 2 days')),
					"usrid"	=> $r->usrid,
					"status"	=> $status,
					"idbayar"	=> $idbayar,
					"digital"	=> 1
				);
				if($status == 1){
					$transaksi["tglupdate"] = date("Y-m-d H:i:s");
				}
				$this->db->insert("transaksi",$transaksi);
				$idtransaksi = $this->db->insert_id();
				
				$idproduk = explode("|",$input['idproduk']);
				for($i=0; $i<count($idproduk); $i++){
					$this->db->where("id",$idproduk[$i]);
					$this->db->update("transaksiproduk",array("idtransaksi"=>$idtransaksi));
				}
					
				// UPDATE STOK PRODUK
				$this->db->where("idtransaksi",$idtransaksi);
				$db = $this->db->get("transaksiproduk");
				$nos = 1;
				$po = 0;
				$afiliasi = 0;
				if($db->num_rows() == 0){ $produkwa = "TIDAK ADA PRODUK\n\n"; }
				foreach($db->result() as $r){
					$pro = $this->func->getProduk($r->idproduk,"semua");
					$afiliasi += $pro->afiliasi * $r->jumlah;
					$po = ($pro->preorder > 0 AND $pro->pohari > $po) ? $pro->pohari : $po;
					if($r->variasi != 0){
						$var = $this->func->getVariasi($r->variasi,"semua","id");
						if($r->jumlah > $var->stok){
							echo json_encode(array("success"=>false,"message"=>"stok produk tidak mencukupi"));
							$stok = 0;
							exit;
						}

						$stok = $var->stok - $r->jumlah;
						$prostok = $pro->stok - $r->jumlah;
						$this->db->where("id",$r->idproduk);
						$this->db->update("produk",["stok"=>$prostok,"tglupdate"=>date("Y-m-d H:i:s")]);
							
						$variasi[] = $r->variasi;
						$stock[] = $stok;
						$stokawal[] = $var->stok;
						$jml[] = $r->jumlah;

						for($i=0; $i<count($variasi); $i++){
							$this->db->where("id",$variasi[$i]);
							$this->db->update("produkvariasi",["stok"=>$stock[$i],"tgl"=>date("Y-m-d H:i:s")]);
							
							$data = array(
								"usrid"	=> $r->usrid,
								"stokawal" => $stokawal[$i],
								"stokakhir" => $stock[$i],
								"variasi" => $variasi[$i],
								"jumlah" => $jml[$i],
								"tgl"	=> date("Y-m-d H:i:s"),
								"idtransaksi" => $idtransaksi
							);
							$this->db->insert("historystok",$data);
						}
					}else{
						if($r->jumlah > $pro->stok){
							echo json_encode(array("success"=>false,"message"=>"stok produk tidak mencukupi"));
							$stok = 0;
							exit;
						}
						$stok = $pro->stok - $r->jumlah;
						$this->db->where("id",$r->idproduk);
						$this->db->update("produk",["stok"=>$stok,"tglupdate"=>date("Y-m-d H:i:s")]);

						$data = array(
							"usrid"	=> $usr->id,
							"stokawal" => $pro->stok,
							"stokakhir" => $stok,
							"variasi" => 0,
							"jumlah" => $r->jumlah,
							"tgl"	=> date("Y-m-d H:i:s"),
							"idtransaksi" => $idtransaksi
						);
						$this->db->insert("historystok",$data);
					}

					if($wa != null){
						$variasee = $this->func->getVariasi($r->variasi,"semua");
						if(isset($variasee->harga)){
							if($usr->level == 5){
								$hargawa = $variasee->hargadistri;
							}elseif($usr->level == 4){
								$hargawa = $variasee->hargaagensp;
							}elseif($usr->level == 3){
								$hargawa = $variasee->hargaagen;
							}elseif($usr->level == 2){
								$hargawa = $variasee->hargareseller;
							}else{
								$hargawa = $variasee->harga;
							}
						}else{
							if($usr->level == 5){
								$hargawa = $pro->hargadistri;
							}elseif($usr->level == 4){
								$hargawa = $pro->hargaagensp;
							}elseif($usr->level == 3){
								$hargawa = $pro->hargaagen;
							}elseif($usr->level == 2){
								$hargawa = $pro->hargareseller;
							}else{
								$hargawa = $pro->harga;
							}
						}
						$hargawatotal = $hargawa*$r->jumlah;
						$hrgwatotal += $hargawatotal;
						$variaksi = ($r->variasi != 0 AND $variasee != null) ? $this->func->getWarna($variasee->warna,"nama")." ".$pro->subvariasi." ".$this->func->getSize($variasee->size,"nama") : "";
						$produkwa .= "*".$nos.". ".$pro->nama."*\n";
						$produkwa .= ($r->variasi != 0 AND $variasee != null) ? "    Varian : ".$variaksi."\n" : "";
						$produkwa .= "    Jumlah : ".$r->jumlah."\n";
						$produkwa .= "    Harga (@) : Rp ".$this->func->formUang($hargawa)."\n";
						$produkwa .= "    Harga Total : Rp ".$this->func->formUang($hargawatotal)."\n \n";
						$nos++;
					}
				}
				$this->db->where("id",$idtransaksi);
				$this->db->update("transaksi",['po'=>$po]);
				
				// AFILIASI
				if($usr->upline > 0 AND $afiliasi > 0){
					$affs = array(
						"tgl"	=> date("Y-m-d H:i:s"),
						"usrid"	=> $usr->upline,
						"idtransaksi"	=> $idtransaksi,
						"status"=> $status,
						"jumlah"=> $afiliasi
					);
					$this->db->insert("afiliasi",$affs);
				}

				$idbayaran = $idbayar;
				//$idbayar = $this->func->arrEnc(array("idbayar"=>$idbayar),"encode");

				$usrid = $this->func->getUser($r->usrid,"semua");
				$profil = $this->func->getProfil($r->usrid,"semua","usrid");
				$diskon = $input["diskon"] != 0 ? "Diskon: <b>Rp ".$this->func->formUang(intval($input["diskon"]))."</b><br/>" : "";
				$diskonwa = $input["diskon"] != 0 ? "Diskon: *Rp ".$this->func->formUang(intval($input["diskon"]))."*\n" : "";

				$text = "Halo kak admin ".$this->func->globalset("nama").", saya mau order produk berikut dong\n\n";
				$text .= $produkwa;
				$text .= "Subtotal : *Rp ".$this->func->formUang($hrgwatotal)."*\n";
				$text .= "Diskon : *Rp ".$this->func->formUang($input["diskon"])."*\n";
				$text .= "Total : *Rp ".$this->func->formUang($input["total"])."*\n";
				$text .= "------------------------------\n\n";

				if($wa == null){
					$pesan = "
						Halo <b>".$profil->nama."</b><br/>".
						"Terimakasih sudah membeli produk kami.<br/>".
						"Saat ini kami sedang menunggu pembayaran darimu sebelum kami memprosesnya. Sebagai informasi, berikut detail pesananmu <br/>".
						"No Invoice: <b>".$invoice."</b><br/> <br/>".
						"Total Pesanan: <b>Rp ".$this->func->formUang($total)."</b><br/>";
					if($input["metodebayar"] == 2){
						$pesan .= "Berikut informasi rekening untuk pembayaran pesanan<br/>";
						$this->db->where("usrid",0);
						$rek = $this->db->get("rekening");
						foreach($rek->result() as $re){
							$pesan .= "<b style='font-size:120%'>".$this->func->getBank($re->idbank,"nama")." ".$re->norek."</b><br/>";
							$pesan .= "a/n ".$re->atasnama."<br/> <br/>";
						}
						$pesan .= "Untuk konfirmasi pembayaran silahkan langsung klik link berikut:<br/>".
							"<a href='".site_url("manage/pesanan")."?konfirmasi=".$idbayar."'>Bayar Pesanan Sekarang &raquo;</a>
						";
					}else{
						$pesan .= "Untuk pembayaran silahkan langsung klik link berikut:<br/>".
							"<a href='".site_url("home/invoice")."?inv=".$idbayar."'>Bayar Pesanan Sekarang &raquo;</a>
						";
					}
					$this->func->sendEmail($usrid->username,$toko->nama." - Pesanan",$pesan,"Pesanan");
					$pesan = "
						Halo *".$profil->nama."*\n".
						"Terimakasih sudah membeli produk kami.\n".
						"Saat ini kami sedang menunggu pembayaran darimu sebelum kami memprosesnya. Sebagai informasi, berikut detail pesananmu \n \n".
						"No Invoice: *".$invoice."*\n".
						"Total Pesanan: *Rp ".$this->func->formUang($total)."*\n";
					if($input["metodebayar"] == 2){
						$pesan .= "Berikut informasi rekening untuk pembayaran pesanan \n";
						foreach($rek->result() as $re){
							$pesan .= "*".$this->func->getBank($re->idbank,"nama")." ".$re->norek."* \n";
							$pesan .= "a/n ".$re->atasnama."\n \n";
						}
						$pesan .= "Untuk konfirmasi pembayaran silahkan langsung klik link berikut\n".site_url("manage/pesanan")."?konfirmasi=".$idbayar;
					}else{
						$pesan .= "Untuk pembayaran silahkan langsung klik link berikut\n".site_url("home/invoice")."?inv=".$idbayar;
					}
					$this->func->sendWA($profil->nohp,$pesan);

					// SEND NOTIFICATION MOBILE
					$this->func->notifMobile("Pesanan ".$invoice,"Segera lakukan pembayaran agar pesananmu juga segera diproses","",$usrid->id);
					
					$pesan = "
						<h3>Pesanan Baru</h3><br/>
						<b>".strtoupper(strtolower($profil->nama))."</b> telah membuat pesanan baru dengan total pembayaran 
						<b>Rp. ".$this->func->formUang($total)."</b> Invoice ID: <b>".$invoice."</b>
						<br/>&nbsp;<br/>&nbsp;<br/>
						Cek Pesanan Pembeli di Dashboard Admin ".$toko->nama."<br/>
						<a href='".site_url("cdn")."'>Klik Disini</a>
					";
					$this->func->sendEmail($toko->email,$toko->nama." - Pesanan Baru",$pesan,"Pesanan Baru di ".$toko->nama);
					$pesan = "
						*Pesanan Baru*\n".
						"*".strtoupper(strtolower($profil->nama))."* telah membuat pesanan baru dengan detail:\n".
						"Total Pembayaran: *Rp. ".$this->func->formUang($total)."*\n".
						"Invoice ID: *".$invoice."*".
						"\n \n".
						"Cek Pesanan Pembeli di *Dashboard Admin ".$toko->nama."*
						"; 
					$this->func->sendWA($toko->wasap,$pesan);
				}

				//$url = $status == 0 ? site_url("home/invoice")."?inv=".$idbayar : site_url("manage/pesanan");
				echo json_encode(array("success"=>true,"status"=>$status,"inv"=>$idbayaran,"text"=>$text));
			/*}else{
				echo json_encode(array("success"=>false,"idbayar"=>0));
			}*/
			}else{
				echo json_encode(array("success"=>false,"message"=>"forbidden"));
			}
		}else{
			echo json_encode(array("success"=>false,"message"=>"forbidden"));
		}
	}
	public function konfirmasipesanan(){
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
				$config['file_name'] = $r->usrid.date("YmdHis");

				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('bukti')){
					$error = $this->upload->display_errors();
					json_encode(["success"=>false,"error"=>$error]);
					//redirect("404_notfound");
				}else{
					$upload_data = $this->upload->data();

					$filename = $upload_data['file_name'];
					$data = array(
						"tgl"	=> date("Y-m-d H:i:s"),
						"idbayar"	=> $_GET['id'],
						"bukti"		=> $filename
					);
					$this->db->insert("konfirmasi",$data);

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
    
	// LACAK KIRIMAN
	public function lacakiriman(){
		if(isset($_GET["trx"])){
			$trx = $this->func->getTransaksi($_GET["trx"],"semua");
			$set = $this->func->globalset("semua");

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://pro.rajaongkir.com/api/waybill",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => "waybill=".$trx->resi."&courier=".$this->func->getKurir($trx->kurir,"rajaongkir"),
				CURLOPT_HTTPHEADER => array(
				"content-type: application/x-www-form-urlencoded",
				"key: ".$set->rajaongkir
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				echo "<span class='cl1'>terjadi kendala saat menghubungi pihak ekspedisi, cobalah beberapa saat lagi</span>";
			}else{
				$response = json_decode($response);
				//print_r();
				if($response->rajaongkir->status->code == "200"){
					$respon = $response->rajaongkir->result->manifest;
					if($response->rajaongkir->result->delivered == true){
						$paket = array(
							"penerima" 	=> strtoupper(strtolower($response->rajaongkir->result->delivery_status->pod_receiver)),
							"tgl"		=> $this->func->ubahTgl("d M Y H:i",$response->rajaongkir->result->delivery_status->pod_date." ".$response->rajaongkir->result->delivery_status->pod_time),
							"status"	=> 2,
							"resi"		=> $trx->resi
						);
					}else{
						$paket = array(
							"penerima" 	=> "",
							"tgl"		=> $this->func->ubahTgl("d M Y H:i",date("Y-m-d H:i:s")),
							"status"	=> 1,
							"resi"		=> $trx->resi
						);
					}
					if($response->rajaongkir->result->delivered == true AND $response->rajaongkir->query->courier != "jne"){
						$proses[] = array(
							"tgl" 	=> $this->func->ubahTgl("d/m/Y H:i",$response->rajaongkir->result->delivery_status->pod_date." ".$response->rajaongkir->result->delivery_status->pod_time),
							"desc"	=> "Diterima oleh ".strtoupper(strtolower($response->rajaongkir->result->delivery_status->pod_receiver)),
							"status"=> 2
						);
					}

					for($i=0; $i<count($respon); $i++){
						//print_r($respon[$i])."<p/>";
						$proses[] = array(
							"tgl" 	=> $this->func->ubahTgl("d/m/Y H:i",$respon[$i]->manifest_date." ".$respon[$i]->manifest_time),
							"desc"	=> $respon[$i]->manifest_description,
							"city"	=> $respon[$i]->city_name,
							"status"=> 1
						);
					}
					
					$paket["success"] = true;
					$paket["proses"] = $proses;
					echo json_encode($paket);
				}else{
					echo json_encode(
						array(
							"success"	=> false,
							"tgl"		=> $this->func->ubahTgl("d M Y H:i",date("Y-m-d H:i:s")),
							"msg"		=> "Nomor Resi tidak ditemukan, coba ulangi beberapa jam lagi sampai resi sudah update di sistem pihak ekspedisi",
							"resi"		=> $trx->resi
						)
					);
				}
			}
		}else{
			echo json_encode(array("success"=>false,"tgl"=>$this->func->ubahTgl("d M Y H:i",date("Y-m-d H:i:s")),"msg"=>"terjadi kesalahan sistem, silahkan ualngi beberapa saat lagi"));
		}
	}

}