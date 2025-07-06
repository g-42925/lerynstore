<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Home extends CI_Controller {

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
	
	// NOTIF
	public function notif(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}
				
				$usrid = ($r->usrid == 0) ? -1 : $r->usrid;
				// CHAT
				$this->db->select("id");
				$this->db->where("tujuan",$usrid);
				$this->db->where("baca",0);
				$db = $this->db->get("pesan");
				// KERANJANG
				$this->db->select("id");
				$this->db->where("usrid",$usrid);
				$this->db->where("idtransaksi",0);
				$kr = $this->db->get("transaksiproduk");
				// SALDO
				$saldo = $this->func->getSaldo($usrid,"semua","usrid",true);
				
				echo json_encode(array("success"=>true,"chat"=>$db->num_rows(),"keranjang"=>$kr->num_rows(),"id"=>$r->usrid,"saldo"=>$saldo->saldo,"koin"=>$saldo->koin));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}

	public function beranda(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}
				
				$set = $this->func->globalset("semua");
				$segmen = json_decode($set->homepage);
				$notin = [];
				$hasil = [];
				$no = 1;
				foreach($segmen as $seg){
					$warna = ($seg->warna != "#ffffff") ? $seg->warna : "transparent";
					if($seg->tipe == 1){
						$this->db->where("tgl<=",date("Y-m-d H:i:s"));
						$this->db->where("tgl_selesai>=",date("Y-m-d H:i:s"));
						$this->db->where("tags",$seg->tags);
						$this->db->where("status",1);
						$this->db->order_by("tgl","DESC");
						$sld = $this->db->get("promo");
						if($sld->num_rows() > 0){
							$hasildata = [];
							foreach($sld->result() as $s){
								$hasildata[] = ["link"=>strip_tags($s->link),"gambar"=>base_url('cdn/promo/'.$s->gambar)];
							}
							$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe,"data"=>$hasildata];
							$no++;
						}
					}elseif($seg->tipe == 2){
						$this->db->where("tgl<=",date("Y-m-d H:i:s"));
						$this->db->where("tgl_selesai>=",date("Y-m-d H:i:s"));
						$this->db->where("tags",$seg->tags);
						$this->db->where("status",1);
						$this->db->order_by("tgl","ASC");
						$this->db->limit($seg->jumlah);
						$ikl = $this->db->get("promo");

						if($ikl->num_rows() > 0){
							$hasildata = [];
							foreach($ikl->result() as $iklan){
								$hasildata[] = [
									"link"	=> strip_tags($iklan->link),
									"gambar"=> base_url('cdn/promo/'.$iklan->gambar),
									"caption"	=> $iklan->caption,
									"keterangan"=> $iklan->keterangan
								];
							}
							$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe,"data"=>$hasildata];
							$no++;
						}
					}elseif($seg->tipe == 3){
						$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe];
						$no++;
					}elseif($seg->tipe == 4){
						// Link Playstore Gak Perlu
					}elseif($seg->tipe == 5){
						$this->db->where("mulai <=",date("Y-m-d H:i:s"));
						$this->db->where("selesai >=",date("Y-m-d H:i:s"));
						$this->db->order_by("RAND()");
						$this->db->limit(40);
						$db = $this->db->get("flashsale");
						if($db->num_rows() > 0){
							$totalproduk = 0;
							$no =  1;
							$hasildata = [];
							foreach($db->result() as $fs){
								$lolos = true;
								$r = $this->func->getProduk($fs->idproduk,"semua");
								if($seg->kategori > 0){
									$lolos = ($r->idcat != $seg->kategori) ? false : $lolos;
								}
								if($seg->brand > 0){
									$lolos = ($r->brandid != $seg->brand) ? false : $lolos;
								}
								if($seg->jenis > 0){
									$lolos = (($seg->jenis == 1 && $r->digital == 1) || ($seg->jenis == 2 && $r->digital == 0)) ? false : $lolos;
								}

								if($no <= $seg->jumlah && $lolos == true){
									$notin[] = $fs->idproduk;
									$totalstok = $fs->stok;

									$totalproduk += 1;
									$diskon = $r->hargacoret > $fs->harga ? ($r->hargacoret-$fs->harga)/$r->hargacoret*100 : 0;
									$fspersen = ($fs->terjual > 0) ? $fs->terjual / ($fs->stok + $fs->terjual) * 100 : 0;
											
									// CUSTOM LABEL
									$label = json_decode($r->customlabel);
									$cuslab = [];
									if($label){
										foreach($label as $lab){
											$cuslab[] = [
												"warna"	=> $lab->warna,
												"bg"	=> $lab->background,
												"text"	=> $lab->text
											];
										}
									}
									$hasildata[] = [
										"id"	=> $r->id,
										"nama"	=> $r->nama,
										"kategori"	=> $this->func->getKategori($r->idcat,"nama"),
										"foto"	=> $this->func->getFoto($r->id,"utama"),
										"po"	=> $r->preorder,
										"digital"	=> $r->digital,
										"hargadiskon"	=> ($r->hargacoret > $fs->harga) ? "Rp ".$this->func->formUang($r->hargacoret) : null, // hargacoret
										"diskon"	=> ($diskon > 0) ? round($diskon,0) : null, // presentase diskon
										"harga"	=> "Rp. ".$this->func->formUang($fs->harga),
										"customlabel"	=> $cuslab,
										"selesai"	=> $this->func->ubahTgl("Y-m-d H:i:s",$fs->selesai),
										"fspersen"	=> $fspersen,
										"terjual"	=> $fs->terjual
									];
									$no++;
								}
							}

							if($totalproduk > 0){
								$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe,"data"=>$hasildata];
								$no++;
							}
						}
					}elseif($seg->tipe == 6){
						if(count($notin) > 0){
							$this->db->where_not_in("id",$notin);
						}
						if($seg->jenis > 0){
							$jenis = ($seg->jenis == 1) ? 0 : 1;
							$this->db->where("digital",$jenis);
						}
						if($seg->kategori > 0){
							$this->db->where("idcat",$seg->kategori);
						}
						if($seg->brand > 0){
							$this->db->where("brandid",$seg->brand);
						}
						$this->db->where("stok >",0);
						$this->db->where("status",1);
						$this->db->limit($seg->jumlah);
						$this->db->order_by("RAND()");
						$db = $this->db->get("produk");
						$totalproduk = 0;
						if($db->num_rows() > 0){
							$hasildata = [];
							foreach($db->result() as $r){
								$level = isset($_SESSION["lvl"]) ? $_SESSION["lvl"] : 0;
								if($level == 5){
									$result = $r->hargadistri;
								}elseif($level == 4){
									$result = $r->hargaagensp;
								}elseif($level == 3){
									$result = $r->hargaagen;
								}elseif($level == 2){
									$result = $r->hargareseller;
								}else{
									$result = $r->harga;
								}
								$ulasan = $this->func->getReviewProduk($r->id);
								$nilai = ($ulasan['nilai'] > 0) ? $ulasan['nilai'] : 5;

								$this->db->where("idproduk",$r->id);
								$dbv = $this->db->get("produkvariasi");
								$totalstok = ($dbv->num_rows() > 0) ? 0 : $r->stok;
								$hargs = 0;
								$harga = array();
								foreach($dbv->result() as $rv){
									$totalstok += $rv->stok;
									if($level == 5){
										$harga[] = $rv->hargadistri;
									}elseif($level == 4){
										$harga[] = $rv->hargaagensp;
									}elseif($level == 3){
										$harga[] = $rv->hargaagen;
									}elseif($level == 2){
										$harga[] = $rv->hargareseller;
									}else{
										$harga[] = $rv->harga;
									}
									$hargs += $rv->harga;
								}

								$totalproduk += 1;
								$wishis = ($this->func->cekWishlist($r->id)) ? "active" : "";
								$hargadapat = $hargs > 0 ? min($harga) : $result;
								$diskon = $r->hargacoret > $hargadapat ? ($r->hargacoret-$hargadapat)/$r->hargacoret*100 : null;
								$terjual = $this->func->getTerjual($r->id);
								$terjual = ($terjual >= 10000) ? 10000 : $terjual;
								$terjual = ($terjual >= 1000) ? round(($terjual/1000),1)."rb+" : $terjual;
									
								// CUSTOM LABEL
								$label = json_decode($r->customlabel);
								$cuslab = [];
								if($label){
									foreach($label as $lab){
										$cuslab[] = [
											"warna"	=> $lab->warna,
											"bg"	=> $lab->background,
											"text"	=> $lab->text
										];
									}
								}
								$hasildata[] = [
									"id"	=> $r->id,
									"nama"	=> $r->nama,
									"kategori"	=> $this->func->getKategori($r->idcat,"nama"),
									"foto"	=> $this->func->getFoto($r->id,"utama"),
									"po"	=> $r->preorder,
									"digital"	=> $r->digital,
									"hargadiskon"	=> ($r->hargacoret > $hargadapat) ? "Rp ".$this->func->formUang($r->hargacoret) : null, // hargacoret
									"diskon"	=> ($diskon > 0) ? round($diskon,0) : null, // presentase diskon
									"harga"	=> "Rp. ".$this->func->formUang($hargadapat),
									"customlabel"	=> $cuslab,
									"terjual"	=> $terjual,
									"nilai"	=> $nilai
								];
							}
									
							if($totalproduk > 0){
								$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe,"data"=>$hasildata];
								$no++;
							}
						}
					}elseif($seg->tipe == 7){
						$this->db->where("status",1);
						$this->db->limit($seg->jumlah);
						$db = $this->db->get("testimoni");
						if($db->num_rows() > 0){
							$hasildata = [];
							foreach($db->result() as $r){
								$hasildata[] = [
									"komentar"	=> $r->komentar,
									"nama"	=> $r->nama,
									"jabatan"	=> $r->jabatan,
									"foto"	=> base_url("cdn/uploads/".$r->foto),
								];
							}
							$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe,"data"=>$hasildata];
							$no++;
						}
					}elseif($seg->tipe == 8){
						$this->db->limit($seg->jumlah);
						$this->db->order_by("tgl DESC");
						$db = $this->db->get("blog");
						
						if($db->num_rows() > 0){
							$hasildata = [];
							foreach($db->result() as $res){
								$img = (file_exists(FCPATH."cdn/uploads/".$res->img)) ? base_url("cdn/uploads/".$res->img) : base_url("cdn/uploads/no-image.png");
								$hasildata[] = [
									"id"	=> $res->id,
									"foto"	=> $img,
									"tgl"	=> $this->func->ubahTgl("d M Y",$res->tgl),
									"judul"	=> $this->func->potong($res->judul,80,"..."),
								];
							}
							$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe,"data"=>$hasildata];
							$no++;
						}
					}elseif($seg->tipe == 9){
						$this->db->order_by("RAND()");
						$this->db->limit(12);
						$db = $this->db->get("brand");
						if($db->num_rows() > 0){
							$hasildata = [];
							foreach($db->result() as $r){
								$hasildata[] = [
									"nama"	=> $r->nama,
									"foto"	=> base_url("cdn/brand/".$r->icon)
								];
							}
							$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe,"data"=>$hasildata];
							$no++;
						}
					}elseif($seg->tipe == 10){
						$this->db->where("parent",0);
						$this->db->limit($seg->jumlah);
						$db = $this->db->get("kategori");
						if($db->num_rows() > 0){
							$hasildata = [];
							foreach($db->result() as $r){
								$hasildata[] = [
									"id"	=> $r->id,
									"nama"	=> $r->nama,
									"foto"	=> base_url("cdn/kategori/".$r->icon),
								];
							}
							$hasil[] = ["judul"=>strtoupper(strtolower($seg->judul)),"no"=>$no,"warna"=>$warna,"tipe"=>$seg->tipe,"data"=>$hasildata];
							$no++;
						}
					}
				}
				echo json_encode(array("success"=>true,"data"=>$hasil));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}

	public function slider(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}
				
				$this->db->where("tgl <= '".date("Y-m-d H:i:s")."' AND tgl_selesai >= '".date("Y-m-d H:i:s")."' AND jenis = '1' AND status > 0");
				$this->db->order_by("id","DESC");
				$db = $this->db->get("promo");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$data[] = array(
							"foto"	=> base_url("cdn/promo/".$r->gambar),
							"link"	=> $r->link
						);
					}
					echo json_encode(array("success"=>true,"result"=>$data));
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
	
	public function promo(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}
				
				$this->db->where("tgl <= '".date("Y-m-d H:i:s")."' AND tgl_selesai >= '".date("Y-m-d H:i:s")."' AND jenis = '2' AND status > 0");
				$this->db->order_by("RAND()");
				$db = $this->db->get("promo");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$data[] = array(
							"foto"	=> base_url("cdn/promo/".$r->gambar),
							"link"	=> $r->link
						);
					}
					echo json_encode(array("success"=>true,"result"=>$data));
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
	
	public function blog(){
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
				$rows = $this->db->get("blog");
				$rows = $rows->num_rows();

				$this->db->order_by("tgl","DESC");
				//$this->db->limit(8);
				$this->db->limit(8,($page-1)*8);
				$db = $this->db->get("blog");

				$maxPage = ceil($rows/8);
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$img = (file_exists(FCPATH."cdn/uploads/".$r->img)) ? base_url("cdn/uploads/".$r->img) : base_url("cdn/uploads/no-image.png");
						$data[] = array(
							"foto"	=> $img,
							"judul"	=> $r->judul,
							"tgl"	=> $this->func->elapsed($r->tgl),
							"konten"=> $this->func->potong($this->func->clean(strip_tags($r->konten)),120,"..."),
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
    
	public function blogsingle($id=null){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}
				
				$this->db->where("id",$id);
				$db = $this->db->get("blog");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						$data = array(
							"foto"	=> base_url("cdn/uploads/".$r->img),
							"judul"	=> ucwords($r->judul),
							"konten"=> $r->konten,
							"tgl"	=> $this->func->elapsed($r->tgl),
							"id"	=> $r->id
						);
					}
					echo json_encode(array("success"=>true,"result"=>$data));
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
	
	public function kategori(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				/*foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}*/
				
				$this->db->where("parent",0);
				$this->db->order_by("nama","ASC");
				$db = $this->db->get("kategori");
				if($db->num_rows() > 0){
                    $data = array();
					foreach($db->result() as $r){
						$data[] = array(
							"foto"	=> base_url("cdn/kategori/".$r->icon),
							"url"	=> $r->url,
							"nama"	=> ucwords($r->nama),
							"id"	=> $r->id
						);
					}
                    $datappob = array();
                    $datatag = array();
                    $datatop = array();
                    $db = $this->db->get("ppob_kategori");
                    foreach($db->result() as $r){
                        $icon = ($r->icon) ? $r->icon : "default.png";
						$datatop[] = array(
							"foto"	=> base_url("cdn/ppob/".$icon),
							"nama"	=> $r->nama,
							"kode"	=> $r->kode,
                            "tipe"  => 1,
							"id"	=> $r->id
						);
						$datappob[] = array(
							"foto"	=> base_url("cdn/ppob/".$icon),
							"nama"	=> $r->nama,
							"kode"	=> $r->kode,
                            "tipe"  => 1,
							"id"	=> $r->id
						);
                    }
                    $this->db->where("tipe",2);
                    $db = $this->db->get("ppob");
                    $no = 1;
                    foreach($db->result() as $r){
                        $icon = ($r->icon) ? $r->icon : "default.png";
						$datappob[] = array(
							"foto"	=> base_url("cdn/ppob/".$icon),
							"nama"	=> $r->nama,
							"kode"	=> $r->kode,
                            "tipe"  => 2,
							"id"	=> $r->id
						);
						$datatag[] = array(
							"foto"	=> base_url("cdn/ppob/".$icon),
							"nama"	=> $r->nama,
							"kode"	=> $r->kode,
                            "tipe"  => 2,
							"id"	=> $r->id
						);
                    }
					echo json_encode(array("success"=>true,"result"=>$data,"resultppob"=>$datappob,"ppobtop"=>$datatop,"ppobtag"=>$datatag));
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
	public function kategoriproduk(){
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
				
				$this->db->where("parent",0);
				$this->db->order_by("RAND()");
				$db = $this->db->get("kategori");
				if($db->num_rows() > 0){
					foreach($db->result() as $rk){
						$this->db->where("idcat",$rk->id);
						$this->db->order_by("stok","DESC");
						$this->db->limit(6);
						$dbs = $this->db->get("produk");
						if($dbs->num_rows() > 2){
							$produk = [];
							foreach($dbs->result() as $r){
								if(is_object($usr)){
									if($usr->level == 5){
										$harga = $r->hargadistri;
									}elseif($usr->level == 4){
										$harga = $r->hargaagensp;
									}elseif($usr->level == 3){
										$harga = $r->hargaagen;
									}elseif($usr->level == 2){
										$harga = $r->hargareseller;
									}else{
										$harga = $r->harga;
									}
								}else{
									$harga = $r->harga;
								}
		
								$this->db->where("idproduk",$r->id);
								$dba = $this->db->get("produkvariasi");
								$stok = 0;
								$hargo = array();
								$stok = $r->stok;
								foreach($dba->result() as $rs){
									//$stok += $rs->stok;
									if(is_object($usr)){
										if($usr->level == 5){
											$hargo[] = $rs->hargadistri;
										}elseif($usr->level == 4){
											$hargo[] = $rs->hargaagensp;
										}elseif($usr->level == 3){
											$hargo[] = $rs->hargaagen;
										}elseif($usr->level == 2){
											$hargo[] = $rs->hargareseller;
										}else{
											$hargo[] = $rs->harga;
										}
									}else{
										$hargo[] = $rs->harga;
									}
								}
								if($dba->num_rows() > 0){ $harga = min($hargo); }
								
								$ulasan = $this->func->getReviewProduk($r->id);
								$diskon = ($r->hargacoret > 0) ? "Rp ".$this->func->formUang($r->hargacoret) : null;
								$diskons = ($r->hargacoret > 0) ? round(($r->hargacoret-$harga)/$r->hargacoret*100,0) : null;
								$produk[] = array(
									"foto"	=> $this->func->getFoto($r->id,"utama"),
									"hargadiskon"	=> $diskon,
									"diskon"	=> $diskons,
									"harga"	=> "Rp ".$this->func->formUang($harga),
									"nama"	=> ucwords($this->func->potong($r->nama,40)),
									"id"	=> $r->id,
									"stok"	=> $stok,
									"po"	=> $r->preorder,
									"pohari"	=> $r->pohari,
									"digital"	=> $r->digital,
									"ulasan"=> $ulasan["ulasan"],
									"nilai"	=> $ulasan["nilai"],
								);
							}
							$data[] = array(
								"nama"	=> ucwords($rk->nama),
								"id"	=> $rk->id,
								"produk"=> $produk
							);
						}
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
    
	// LOAD KATA PEMBELI
	function testimoni(){
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

                $this->db->where("status",1);
                $voc = $this->db->get("testimoni");
				if($voc->num_rows() > 0){
					$data = [];
					foreach($voc->result() as $v){
						$data[] = [
							"nama"	=> $v->nama,
							"foto"	=> base_url("cdn/uploads/".$v->foto),
							"komentar"	=> $v->komentar,
							"jabatan"	=> $v->jabatan
						];
					}

					echo json_encode(["success"=>true,"result"=>$data]);
				}else{
					echo json_encode(array("success"=>true,"result"=>[]));
				}
			}else{
				echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
			}
		}else{
			echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
		}
	}

	//WASAP
	function getwhatsapp(){
		echo json_encode(array("wasap"=>$this->func->getRandomWasap()));
	}
	
	// ALAMAT PROV KAB KEC
	public function getprov(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){		
				$data = array();
				$this->db->order_by("nama");
				$db = $this->db->get("prov");
				foreach($db->result() as $r){
					$data[] = array(
						"id"	=> $r->id,
						"nama"	=> $r->nama
					);
				}
				echo json_encode(array("success"=>true,"data"=>$data));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}		
	public function getkab($id=0){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){		
				$data = array();
				$this->db->where("idprov",$id);
				$this->db->order_by("tipe,nama");
				$db = $this->db->get("kab");
				foreach($db->result() as $r){
					$data[] = array(
						"id"	=> $r->id,
						"nama"	=> $r->tipe." ".$r->nama
					);
				}
				echo json_encode(array("success"=>true,"data"=>$data));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
		
	}		
	public function getkec($id=0){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){		
				$data = array();
				$this->db->where("idkab",$id);
				$this->db->order_by("nama");
				$db = $this->db->get("kec");
				foreach($db->result() as $r){
					$data[] = array(
						"id"	=> $r->id,
						"nama"	=> $r->nama
					);
				}
				echo json_encode(array("success"=>true,"data"=>$data));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
		
	}
	public function getbank(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $t){		
					$data = array();
					$this->db->order_by("nama");
					$db = $this->db->get("rekeningbank");
					foreach($db->result() as $r){
						$data[] = array(
							"id"	=> $r->id,
							"nama"	=> $r->nama
						);
					}
					echo json_encode(array("success"=>true,"data"=>$data));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
		
	}

}