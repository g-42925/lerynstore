<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Produk extends CI_Controller {

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
	
	// PRODUK
	public function produk(){
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
				
				$page = (isset($_GET["page"]) AND intval($_GET["page"]) > 0) ? $_GET["page"] : 1;
				$perpage = (isset($_GET["perpage"]) AND intval($_GET["perpage"]) > 0) ? $_GET["perpage"] : 10;

				$kategori = "";
				if(isset($_GET["catid"]) AND $_GET["catid"] > 0){
					$kategori = $this->func->getKategori($_GET["catid"],"nama");
					$this->db->where("idcat",$_GET["catid"]);
				}
				$this->db->select("id");
				$dba = $this->db->get("produk");
				$maxPage = ceil($dba->num_rows()/$perpage);
				
				if(isset($_GET["catid"]) AND $_GET["catid"] > 0){
					$this->db->where("idcat",$_GET["catid"]);
				}
				$this->db->order_by("id DESC");
				$this->db->limit($perpage,($page-1)*$perpage);
				$db = $this->db->get("produk");
				if($db->num_rows() > 0){
					$data = array();
					foreach($db->result() as $r){
						$this->db->where("idproduk",$r->id);
						$dba = $this->db->get("produkvariasi");
						$stok = 0;
						if($dba->num_rows() == 0){ $stok = $r->stok; }
						foreach($dba->result() as $rs){
							$stok += $rs->stok;
						}
						
						if(is_object($usr)){
							if($usr->level == 5){
								$harga = $r->hargadistri;
							}else
							if($usr->level == 4){
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
						if($dba->num_rows() == 0){ $stok = $r->stok; }
						foreach($dba->result() as $rs){
							$stok += $rs->stok;
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
						//if($stok > 0){
						$terjual = $this->func->getTerjual($r->id);
						$terjual = ($terjual >= 10000) ? 10000 : $terjual;
						$terjual = ($terjual >= 1000) ? round(($terjual/1000),1)."rb+" : $terjual;
							$hargacoret = $r->hargacoret != 0 ? "Rp ".$this->func->formUang($r->hargacoret) : null;
							$diskons = ($r->hargacoret > 0) ? round(($r->hargacoret-$harga)/$r->hargacoret*100,0) : null;
							$data[] = array(
								"foto"	=> $this->func->getFoto($r->id,"utama"),
								"hargadiskon"	=> $hargacoret,
								"diskon"	=> $diskons,
								"harga"	=> "Rp ".$this->func->formUang($harga),
								"nama"	=> ucwords($r->nama),
								"id"	=> $r->id,
								"stok"	=> $stok,
								"po"	=> $r->preorder,
								"digital"	=> $r->digital,
								"ulasan"=> $ulasan["ulasan"],
								"nilai"	=> $ulasan["nilai"],
								"terjual"=> $terjual
							);
						//}
					}
					echo json_encode(array("success"=>true,"kategori"=>$kategori,"maxPage"=>$maxPage,"page"=>$page,"result"=>$data));
				}else{
					echo json_encode(array("success"=>false,"kategori"=>$kategori,"sesihabis"=>false));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function cariproduk(){
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
				$cari = (isset($input["cari"])) ? $input["cari"] : "";
				$where = "nama LIKE '%".$cari."%' OR kode LIKE '%".$cari."%' OR url LIKE '%".$cari."%' OR deskripsi LIKE '%".$cari."%' OR berat LIKE '%".$cari."%' OR harga LIKE '%".$cari."%' OR hargareseller LIKE '%".$cari."%' OR hargaagen LIKE '%".$cari."%' OR hargaagensp LIKE '%".$cari."%' OR hargadistri LIKE '%".$cari."%' OR stok LIKE '%".$cari."%'";
				
				$page = (isset($_GET["page"]) AND intval($_GET["page"]) > 0) ? $_GET["page"] : 1;
				$perpage = (isset($_GET["perpage"]) AND intval($_GET["perpage"]) > 0) ? $_GET["perpage"] : 10;
				
				$this->db->select("id");
				$this->db->where($where);
				$dba = $this->db->get("produk");
				$maxPage = ceil($dba->num_rows()/$perpage);
				
				$this->db->where($where);
				$this->db->order_by("preorder ASC, id DESC");
				$this->db->limit($perpage,($page-1)*$perpage);
				$db = $this->db->get("produk");
				if($db->num_rows() > 0){
					$data = array();
					foreach($db->result() as $r){
						$this->db->where("idproduk",$r->id);
						$dba = $this->db->get("produkvariasi");
						$stok = 0;
						if($dba->num_rows() == 0){ $stok = $r->stok; }
						foreach($dba->result() as $rs){
							$stok += $rs->stok;
						}
						
						if(is_object($usr)){
							if($usr->level == 5){
								$harga = $r->hargadistri;
							}else
							if($usr->level == 4){
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
						if($dba->num_rows() == 0){ $stok = $r->stok; }
						foreach($dba->result() as $rs){
							$stok += $rs->stok;
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
						//if($stok > 0){
						$terjual = $this->func->getTerjual($r->id);
						$terjual = ($terjual >= 10000) ? 10000 : $terjual;
						$terjual = ($terjual >= 1000) ? round(($terjual/1000),1)."rb+" : $terjual;
							$hargacoret = $r->hargacoret != 0 ? "Rp ".$this->func->formUang($r->hargacoret) : null;
							$diskons = ($r->hargacoret > 0) ? round(($r->hargacoret-$harga)/$r->hargacoret*100,0) : null;
							$data[] = array(
								"foto"	=> $this->func->getFoto($r->id,"utama"),
								"hargadiskon"	=> $hargacoret,
								"diskon"	=> $diskons,
								"harga"	=> "Rp ".$this->func->formUang($harga),
								"nama"	=> ucwords($r->nama),
								"id"	=> $r->id,
								"stok"	=> $stok,
								"po"	=> $r->preorder,
								"digital"	=> $r->digital,
								"ulasan"=> $ulasan["ulasan"],
								"nilai"	=> $ulasan["nilai"],
								"terjual"=> $terjual
							);
						//}
					}
					echo json_encode(array("success"=>true,"maxPage"=>$maxPage,"page"=>$page,"result"=>$data));
				}else{
					echo json_encode(array("success"=>true,"maxPage"=>1,"page"=>1,"result"=>[]));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function produkterbaru(){
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
				$data = array();
				
				$this->db->order_by("digital ASC, tglupdate DESC");
				$this->db->where("stok>",0);
				$this->db->limit(12);
				$db = $this->db->get("produk");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
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
						//if($stok > 0){
						$terjual = $this->func->getTerjual($r->id);
						$terjual = ($terjual >= 10000) ? 10000 : $terjual;
						$terjual = ($terjual >= 1000) ? round(($terjual/1000),1)."rb+" : $terjual;
							$diskon = ($r->hargacoret > 0) ? "Rp ".$this->func->formUang($r->hargacoret) : null;
							$diskons = ($r->hargacoret > 0) ? round(($r->hargacoret-$harga)/$r->hargacoret*100,0) : null;
							$data[] = array(
								"foto"	=> $this->func->getFoto($r->id,"utama"),
								"kategori"	=> $this->func->getKategori($r->idcat,"nama"),
								"hargadiskon"	=> $diskon,
								"diskon"	=> $diskons,
								"harga"	=> "Rp ".$this->func->formUang($harga),
								"nama"	=> ucwords($this->func->potong($r->nama,32,"...")),
								"id"	=> $r->id,
								"stok"	=> $stok,
								"po"	=> $r->preorder,
								"digital"	=> $r->digital,
								"ulasan"=> $ulasan["ulasan"],
								"nilai"	=> $ulasan["nilai"],
								"terjual"=> $terjual
							);
						//}
					}
					echo json_encode(array("success"=>true,"result"=>$data));
				}else{
					echo json_encode(array("success"=>true,"result"=>[]));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function produkdigital(){
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
				$data = array();
				
				$this->db->order_by("tglupdate DESC");
				$this->db->where("stok>",0);
				$this->db->where("digital",1);
				$this->db->limit(12);
				$db = $this->db->get("produk");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
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
						//if($stok > 0){
						$terjual = $this->func->getTerjual($r->id);
						$terjual = ($terjual >= 10000) ? 10000 : $terjual;
						$terjual = ($terjual >= 1000) ? round(($terjual/1000),1)."rb+" : $terjual;
							$diskon = ($r->hargacoret > 0) ? "Rp ".$this->func->formUang($r->hargacoret) : null;
							$diskons = ($r->hargacoret > 0) ? round(($r->hargacoret-$harga)/$r->hargacoret*100,0) : null;
							$data[] = array(
								"foto"	=> $this->func->getFoto($r->id,"utama"),
								"kategori"	=> $this->func->getKategori($r->idcat,"nama"),
								"hargadiskon"	=> $diskon,
								"diskon"	=> $diskons,
								"harga"	=> "Rp ".$this->func->formUang($harga),
								"nama"	=> ucwords($this->func->potong($r->nama,32,"...")),
								"id"	=> $r->id,
								"stok"	=> $stok,
								"po"	=> $r->preorder,
								"pohari"	=> $r->pohari,
								"digital"	=> $r->digital,
								"ulasan"=> $ulasan["ulasan"],
								"nilai"	=> $ulasan["nilai"],
								"terjual"=> $terjual
							);
						//}
					}
					echo json_encode(array("success"=>true,"result"=>$data));
				}else{
					echo json_encode(array("success"=>true,"result"=>[]));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function produkpreorder(){
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
				$data = array();
				
				$this->db->order_by("tglupdate DESC");
				$this->db->where("stok>",0);
				$this->db->where("preorder",1);
				$this->db->limit(12);
				$db = $this->db->get("produk");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
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
						//if($stok > 0){
						$terjual = $this->func->getTerjual($r->id);
						$terjual = ($terjual >= 10000) ? 10000 : $terjual;
						$terjual = ($terjual >= 1000) ? round(($terjual/1000),1)."rb+" : $terjual;
						$diskon = ($r->hargacoret > 0) ? "Rp ".$this->func->formUang($r->hargacoret) : null;
						$diskons = ($r->hargacoret > 0) ? round(($r->hargacoret-$harga)/$r->hargacoret*100,0) : null;
						$data[] = array(
							"foto"	=> $this->func->getFoto($r->id,"utama"),
							"kategori"	=> $this->func->getKategori($r->idcat,"nama"),
							"hargadiskon"	=> $diskon,
							"diskon"	=> $diskons,
							"harga"	=> "Rp ".$this->func->formUang($harga),
							"nama"	=> ucwords($this->func->potong($r->nama,32,"...")),
							"id"	=> $r->id,
							"stok"	=> $stok,
							"po"	=> $r->preorder,
							"pohari"	=> $r->pohari,
							"digital"	=> $r->digital,
							"ulasan"=> $ulasan["ulasan"],
							"nilai"	=> $ulasan["nilai"],
							"terjual"	=> $terjual
						);
						//}
					}
					echo json_encode(array("success"=>true,"result"=>$data));
				}else{
					echo json_encode(array("success"=>true,"result"=>[]));
				}
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function produksingle(){
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
				
				
				$this->db->where("id",$_GET["pid"]);
				$db = $this->db->get("produk");
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
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
						$this->db->where("idproduk",$_GET["pid"]);
						$this->db->order_by("jenis","DESC");
						$dbs = $this->db->get("upload");
						$foto = array();
						foreach($dbs->result() as $rs){
							$foto[]["foto"] = base_url("cdn/uploads/".$rs->nama);
						}
						$this->db->where("idproduk",$_GET["pid"]);
						//$this->db->group_by("warna");
						$dbs = $this->db->get("produkvariasi");
						$warnafix = array();
						$stoky = $r->stok;
						$variasiproduk = 0; 
						$hargos = 0;
						$hargo = array();
						if($dbs->num_rows() > 0){
							$warna = array();
							$stoky = 0;
							foreach($dbs->result() as $rs){
								$variasiproduk = 1;
								$stoky += $rs->stok;
								
								//$warna[] = $this->func->getWarna($rs->warna,"nama");
								$warnaid[] = $rs->warna;
								$variasi[$rs->warna][] = $rs->id;
								$sizeid[$rs->warna][] = $rs->size;
								$har[$rs->warna][$rs->size] = $rs->harga;
								$harreseller[$rs->warna][$rs->size] = $rs->hargareseller;
								$haragen[$rs->warna][$rs->size] = $rs->hargaagen;
								$haragensp[$rs->warna][$rs->size] = $rs->hargaagensp;
								$hardistri[$rs->warna][$rs->size] = $rs->hargadistri;
								if(isset($stoks[$rs->warna])){
									$stoks[$rs->warna] += $rs->stok;
								}else{
									$stoks[$rs->warna] = $rs->stok;
								}
								$stok[$rs->warna][] = $rs->stok;
								//$size[$rs->warna][] = $this->func->getSize($rs->size,"nama");
							}
							$warnaid = array_unique($warnaid);
							$warnaid = array_values($warnaid);
							for($i=0; $i<count($warnaid); $i++){
								if($stoks[$warnaid[$i]] > 0){
									$warnafix[] = array(
										"id"	=> $warnaid[$i],
										"nama" 	=> $this->func->getWarna($warnaid[$i],"nama")
									);
									
									for($a=0; $a<count($sizeid[$warnaid[$i]]); $a++){
										if(is_object($usr)){
											if($usr->level == 5){
												$hargo[] = intval($hardistri[$warnaid[$i]][$sizeid[$warnaid[$i]][$a]]);
											}elseif($usr->level == 4){
												$hargo[] = intval($haragensp[$warnaid[$i]][$sizeid[$warnaid[$i]][$a]]);
											}elseif($usr->level == 3){
												$hargo[] = intval($haragen[$warnaid[$i]][$sizeid[$warnaid[$i]][$a]]);
											}elseif($usr->level == 2){
												$hargo[] = intval($harreseller[$warnaid[$i]][$sizeid[$warnaid[$i]][$a]]);
											}else{
												$hargo[] = intval($har[$warnaid[$i]][$sizeid[$warnaid[$i]][$a]]);
											}
										}else{
											$hargo[] = intval($har[$warnaid[$i]][$sizeid[$warnaid[$i]][$a]]);
										}
										$hargos += intval($har[$warnaid[$i]][$sizeid[$warnaid[$i]][$a]]);
									}
								}
							}
						}
						$this->db->where("idproduk",$_GET["pid"]);
						$rev = $this->db->get("review");
						$ulasan = [];
						$nilai = 0;
						foreach($rev->result() as $u){
							$ulasan[] = array(
								"nama"	=> ($u->jenis == 1) ? $u->nama : $this->func->getProfil($u->usrid,"nama","usrid"),
								"tgl"	=> $this->func->ubahTgl("d M Y H:i",$u->tgl)." WIB",
								"keterangan"=> $u->keterangan,
								"nilai"	=> $u->nilai
							);
							$nilai += $u->nilai;
						}
						$nilai = $nilai != 0 ? round($nilai/$rev->num_rows(),1) : 0;
						//echo "<h1>".min($hargo)."</h1>";
						$harga = ($hargos > 0) ? max($hargo) : $harga;
						$harga = ($hargos > 0 AND min($hargo) != max($hargo)) ? "Rp. ".$this->func->formUang(min($hargo))." - ".$this->func->formUang(max($hargo)) : "Rp. ".$this->func->formUang($harga);

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
						$terjual = $this->func->getTerjual($r->id);
						$terjual = ($terjual >= 10000) ? 10000 : $terjual;
						$terjual = ($terjual >= 1000) ? round(($terjual/1000),1)."rb+" : $terjual;

						$data = array(
							"success"=>true,
							"warna"	=> $warnafix,
							"stok"	=> $stoky,
							"foto"	=> $foto,
							"harga"	=> $harga,
							"hargacoret"	=> $this->func->formUang($r->hargacoret),
							"nama"	=> ucwords($r->nama),
							"deskripsi"	=> $r->deskripsi,
							"id"	=> $r->id,
							"variasiproduk"	=> $variasiproduk,
							"po"	=> $r->preorder,
							"pohari"	=> $r->pohari,
							"digital"	=> $r->digital,
							"totulasan"=> $rev->num_rows(),
							"ulasan"=> $ulasan,
							"nilai"=> $nilai,
							"variasi"=> $r->variasi,
							"minorder"=> $r->minorder,
							"berat"=> $r->berat,
							"kategori"=> $this->func->getKategori($r->idcat,"nama"),
							"subvariasi"=> $r->subvariasi,
							"customlabel"	=> $cuslab,
							"terjual"	=> $terjual
						);
					}
					echo json_encode($data);
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
	public function size(){
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
				
				
				$this->db->where("idproduk",$_GET["proid"]);
				$this->db->where("warna",$_GET["pid"]);
				$db = $this->db->get("produkvariasi");
				$subvar = 0;
				$varid = 0;
				if($db->num_rows() > 0){
					foreach($db->result() as $r){
						if($r->stok > 0){
							$size[] = array(
								"id"=> $r->id,
								"stok"=> $r->stok,
								"nama"=> $this->func->getSize($r->size,"nama")
							);
							$subvar += $r->size;
							$varid = $r->id;
						}
					}
					echo json_encode(array("success"=>true,"size"=>$size,"subvar"=>$subvar,"varid"=>$varid));
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