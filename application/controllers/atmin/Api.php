<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Api extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		redirect();
	}

	// TESTING MODE
	function tambahmember(){
		if(isset($_POST)){
			$this->db->where("username",$_POST["email"]);
			$this->db->or_where("nohp",$_POST["nohp"]);
			$db = $this->db->get("userdata");

			if($db->num_rows() == 0){
				$datar = array(
					"username"	=> $_POST["email"],
					"nama"	=> $_POST['nama'],
					"nohp"	=> $_POST['nohp'],
					"password"	=> $this->admfunc->encode($_POST['pass']),
					"level"	=> $_POST['level'],
					"status"=> 1,
					"upline"=> 0
				);
				$this->db->insert("userdata",$datar);
				$usrid = $this->db->insert_id();
				$datar = array(
					"usrid"	=> $usrid,
					"nohp"	=> $_POST['nohp'],
					"nama"	=> $_POST['nama'],
					"kelamin"=> $_POST['kelamin'],
					"foto"	=> "no-image.png"
				);
				$this->db->insert("profil",$datar);
				$datar = array(
					"usrid"	=> $usrid,
					"apdet"	=> date("Y-m-d H:i:s"),
					"saldo"	=> $_POST['saldo']
				);
				$this->db->insert("saldo",$datar);

				echo json_encode(array("success"=>true,"msg"=>"Berhasil mengupdate profil","token"=> $this->security->get_csrf_hash()));
			}else{
				echo json_encode(array("success"=>false,"msg"=>"Email atau No Handphone sudah terdaftar!","token"=> $this->security->get_csrf_hash()));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Forbidden!","token"=> $this->security->get_csrf_hash()));
		}
	}

	/*

			if($dbs->num_rows() > 0){
				$this->db->where("id",$r->id);
				$this->db->update("produk",["stok"=>$stok]);
			}
		}
	}
	function ubahkurir(){
		$db = $this->db->get("transaksi");
		foreach($db->result() as $r){
			$kurir = $this->admfunc->getKurir($r->kurir,"id","rajaongkir");
			$paket = $this->admfunc->getPaket($r->paket,"id","rajaongkir");
			$this->db->where("id",$r->id);
			$this->db->update("transaksi",["kurir"=>$kurir,"paket"=>$paket]);
		}
	}
	*/
	
	// CETAK MENCETAK
	function cetakInvoice(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["type"]) AND $_GET["type"] = "thermal"){
			$this->load->view("atmin/print/invoice-thermal");
		}else{
			$this->load->view("atmin/print/invoice");
		}
	}
	function cetakLabel(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/print/label");
	}
	function cekupdatenotif(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$jmlpesanan = $this->admfunc->getJmlPesanan();
		$jmlpesan = $this->admfunc->getJmlPesan();
		$jmltopup = $this->admfunc->getJmlTopup();
		$jmltarik = $this->admfunc->getJmlTarik();
		echo json_encode(["jmlpesanan"=>$jmlpesanan,"jmlpesan"=>$jmlpesan,"jmltopup"=>$jmltopup,"jmltarik"=>$jmltarik]); //,"token"=> $this->security->get_csrf_hash()
	}
	function tescoba(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->db->where("status = 2 AND resi != ''");
		$trx = $this->db->get("transaksi");
		foreach($trx->result() as $r){
			$tgl1 = new DateTime($r->kirim);
			$tgl2 = new DateTime(date("Y-m-d"));
			$d = $tgl2->diff($tgl1)->days + 1;
			echo $d." hari<br/>";
			if($d > 5){
				$this->db->where("id",$r->id);
				$this->db->update("transaksi",["status"=>3,"tglupdate"=>date("Y-m-d H:i:s"),"selesai"=>date("Y-m-d H:i:s")]);
			}
		}
	}
	
	// KURIR
	function aktifkankurir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["push"]) && $_POST["push"] != '10'){ // disable JET karena error
			$toko = $this->admfunc->globalset("kurir");
			$kurir = explode("|",$toko);
			$kurir[] = $this->security->xss_clean($_POST["push"]);
			$push = implode("|",$kurir);
			
			$this->db->where("field","kurir");
			$this->db->update("setting",array("value"=>$push,"tgl"=>date("Y-m-d H:i:s")));

			echo json_encode(array("success"=>true,"msg"=>"Berhasil mengupdate kurir","token"=> $this->security->get_csrf_hash()));
		}else{
			if($_POST["push"] == '10'){
				echo json_encode(array("success"=>false,"msg"=>"<i class='text-danger'>Kurir sedang gangguan, tidak dapat diaktifkan</i>"));
			}else{
				echo json_encode(array("success"=>false,"msg"=>"<i class='text-danger'>Kurir tidak tersedia</i>"));
			}
		}
	}
	function nonaktifkankurir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["push"])){
			$toko = $this->admfunc->globalset("kurir");
			$kurir = explode("|",$toko);
			for($i=0; $i<count($kurir); $i++){
				if($kurir[$i] == $_POST["push"]){
					unset($kurir[$i]);
				}
			}
			array_values($kurir);
			$push = implode("|",$kurir);
				
			$this->db->where("field","kurir");
			$this->db->update("setting",array("value"=>$push,"tgl"=>date("Y-m-d H:i:s")));

			echo json_encode(array("success"=>true,"msg"=>"Berhasil mengupdate profil","token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Forbidden!"));
		}
	}
	public function lacakiriman(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["orderid"])){
			$set = $this->admfunc->globalset("rajaongkir");
			$trx = $this->admfunc->getTransaksi($_GET["orderid"],"semua","orderid");

			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://pro.rajaongkir.com/api/waybill",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "waybill=".$trx->resi."&courier=".$this->admfunc->getKurir($trx->kurir,"rajaongkir"),
			CURLOPT_HTTPHEADER => array(
				"content-type: application/x-www-form-urlencoded",
				"key: ".$set
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				echo "<span class='cl1'>terjadi kendala saat menghubungi pihak ekspedisi, cobalah beberapa saat lagi</span>";
			}else{
				$response = json_decode($response);
				//print_r($response);
				if($response->rajaongkir->status->code == "200"){
					$respon = $response->rajaongkir->result->manifest;
					$respon = !empty($respon) ? $respon : [];
					echo "
						<div class='m-b-12'>
							Nomor Resi: <b class='text-primary'>".$trx->resi."</b>
						</div>
					";
					if($response->rajaongkir->result->delivered == true){
						if($trx->status < 3){
							$this->db->where("id",$trx->id);
							$this->db->update("transaksi",["status"=>3,"tglupdate"=>date("Y-m-d H:i:s"),"selesai"=>date("Y-m-d H:i:s")]);

							$this->selesaiPesanan($trx->id);
						}
						echo "
							<div class='m-b-30'>
								Status: <b style='color:#28a745;'>PAKET TELAH DITERIMA</b><br/>
								Penerima: <b>".strtoupper(strtolower($response->rajaongkir->result->delivery_status->pod_receiver))."</b><br/>
								Tgl diterima: ".$this->admfunc->ubahTgl("d M Y H:i",$response->rajaongkir->result->delivery_status->pod_date." ".$response->rajaongkir->result->delivery_status->pod_time)." WIB
							</div>
						";
					}else{
						echo "<div class='m-b-30'>Status: <b style='color:#c0392b;'>PAKET SEDANG DIKIRIM</b></div>";
					}

					echo "
						<div class='row p-tb-10' style='border-bottom: 1px solid #ccc;font-weight:bold;'>
							<div class='col-md-3'>TANGGAL</div>
							<div class='col-md-9'>STATUS</div>
						</div>
					";

					if($response->rajaongkir->result->delivered == true AND $response->rajaongkir->query->courier != "jne"){
						echo "
							<div class='row p-tb-10' style='border-bottom: 1px dashed #ccc;'>
								<div class='col-md-3'>
									<i>".$this->admfunc->ubahTgl("d/m/Y H:i",$response->rajaongkir->result->delivery_status->pod_date." ".$response->rajaongkir->result->delivery_status->pod_time)."WIB</i>
								</div>
								<div class='col-md-9'>
									<i>Diterima oleh ".strtoupper(strtolower($response->rajaongkir->result->delivery_status->pod_receiver))."</i>
								</div>
							</div>
						";
					}

					for($i=0; $i<count($respon); $i++){
						//print_r($respon[$i])."<p/>";
						echo "
							<div class='row p-tb-10' style='border-bottom: 1px dashed #ccc;'>
								<div class='col-md-3'>
									<i>".$this->admfunc->ubahTgl("d/m/Y H:i",$respon[$i]->manifest_date." ".$respon[$i]->manifest_time)." WIB</i>
								</div>
								<div class='col-md-9'>
									<i>".$respon[$i]->manifest_description."</i>
									<i>".$respon[$i]->city_name."</i>
								</div>
							</div>
						";
					}
				}else{
					echo "
						<div class='p-tb-10' style='border-bottom: 1px dashed #ccc;'>
							<div class='m-b-12'>
								Nomor Resi: <b class='text-danger'>".$trx->resi."</b>
							</div>
							<div class='m-b-12'>
								Nomor Resi tidak ditemukan, coba ulangi beberapa jam lagi sampai resi sudah update di sistem pihak ekspedisi.
							</div>
						</div>
					";
				}
			}
		}else{
			echo "<span class='label label-red'><i class='fa fa-exclamation-triangle'></i> terjadi kesalahan sistem, silahkan ualngi beberapa saat lagi.</span>";
		}
	}

	// IMPORT PRODUK
	public function import(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			$config['upload_path'] = './cdn/import/';
			$config['allowed_types'] = 'xls|xlsx|csv';
			$config['file_name'] = "import_".date("YmdHis");

			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('fileupload')){
				$error = $this->upload->display_errors();
				echo json_encode(array("success"=>false,"msg"=>$error,"token"=> $this->security->get_csrf_hash()));
			}else{
				$uploadData = $this->upload->data();
				$inputFileName = FCPATH."cdn/import/".$uploadData["file_name"];

				if(!file_exists($inputFileName)){
					echo json_encode(array("success"=>false,"msg"=>"file tidak ditemukan"));
					exit;
				}

				//$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				//if(mime_content_type($inputFileName) !== null && in_array(mime_content_type($inputFileName), $file_mimes)) {
					$extension = pathinfo($inputFileName, PATHINFO_EXTENSION);
				
					if('csv' == $extension) {
						$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
					} else {
						$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
					}
				
					$spreadsheet = $reader->load($inputFileName);
					
					$sheetData = $spreadsheet->getActiveSheet()->toArray();
					if(count($sheetData) > 1){
						/*
						print_r($sheetData);
						*/
						for($i=1; $i<count($sheetData); $i++){
							if($sheetData[$i]['0'] != null){
								$string = $this->clean($sheetData[$i]['1']);
								$sheetData[$i]['1'] = $sheetData[$i]['1'] != null ? $sheetData[$i]['1'] : "";
								$sheetData[$i]['2'] = $sheetData[$i]['2'] != null ? $sheetData[$i]['2'] : "";
								$sheetData[$i]['3'] = $sheetData[$i]['3'] != null ? $sheetData[$i]['3'] : 0;
								$sheetData[$i]['4'] = $sheetData[$i]['4'] != null ? $sheetData[$i]['4'] : 0;
								$sheetData[$i]['5'] = $sheetData[$i]['5'] != null ? $sheetData[$i]['5'] : 0;
								$sheetData[$i]['6'] = $sheetData[$i]['6'] != null ? $sheetData[$i]['6'] : 0;
								$sheetData[$i]['7'] = $sheetData[$i]['7'] != null ? $sheetData[$i]['7'] : 0;
								$sheetData[$i]['8'] = $sheetData[$i]['8'] != null ? $sheetData[$i]['8'] : 0;
								$sheetData[$i]['9'] = $sheetData[$i]['9'] != null ? $sheetData[$i]['9'] : 0;
								$sheetData[$i]['10'] = $sheetData[$i]['10'] != null ? $sheetData[$i]['10'] : 0;
								$sheetData[$i]['11'] = $sheetData[$i]['11'] != null ? $sheetData[$i]['11'] : 0;
								$sheetData[$i]['12'] = $sheetData[$i]['12'] != null ? $sheetData[$i]['12'] : 0;
								$sheetData[$i]['13'] = $sheetData[$i]['13'] != null ? $sheetData[$i]['13'] : 0;
								$data = array(
									"tglbuat"	=> date("Y-m-d H:i:s"),
									"tglupdate"	=> date("Y-m-d H:i:s"),
									"nama"		=> $sheetData[$i]['0'],
									"url"		=> $string."-".date("His"),
									"kode"		=> $sheetData[$i]['1'],
									"deskripsi"	=> $sheetData[$i]['2'],
									"idcat"		=> $sheetData[$i]['3'],
									"berat"		=> $sheetData[$i]['4'],
									"harga"		=> $sheetData[$i]['5'],
									"hargareseller"	=> $sheetData[$i]['6'],
									"hargaagen"		=> $sheetData[$i]['7'],
									"hargaagensp"	=> $sheetData[$i]['8'],
									"hargadistri"	=> $sheetData[$i]['9'],
									"minorder"	=> $sheetData[$i]['10'],
									"variasi"	=> $sheetData[$i]['11'],
									"subvariasi"=> $sheetData[$i]['12'],
									"status"	=> $sheetData[$i]['13']
								);
								$this->db->insert("produk",$data);
								$idpro = $this->db->insert_id();

								for($a=1; $a<=4; $a++){
									$e = ($a-1 > 0) ? 3 * ($a - 1) : 0;
									$nom = 14 + $e;
									if($sheetData[$i][$nom] != ""){
										$var = array(
											"idproduk"	=> $idpro,
											"tgl"		=> date("Y-m-d H:i:s"),
											"warna"		=> $sheetData[$i][$nom],
											"size"		=> $sheetData[$i][$nom+1],
											"stok"		=> $sheetData[$i][$nom+2]
										);
										$this->db->insert("produkvariasi",$var);
									} 
								}
							}
						}
						echo json_encode(array("success"=>true,"msg"=>"berhasil mengimpor produk","token"=> $this->security->get_csrf_hash()));
					}else{
						echo json_encode(array("success"=>false,"msg"=>"file tidak sesuai atau kosong","token"=> $this->security->get_csrf_hash()));
					}
				//}else{
				//	echo json_encode(array("success"=>false,"msg"=>"file tidak sesuai: ".mime_content_type($inputFileName." | ".$inputFileName)));
				//}
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	
	// TAMBAH UBAH PRODUK
	public function hapusProduk(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",$_POST["id"]);
			$this->db->delete("produk");

			$this->db->where("idproduk",$_POST["id"]);
			$this->db->delete("produkvariasi");
			$this->db->where("idproduk",$_POST["id"]);
			$this->db->delete("upload");
			$this->db->where("idproduk",$_POST["id"]);
			$this->db->delete("wishlist");
			/*$this->db->where("idproduk",$_POST["id"]);
			$this->db->delete("sundul");*/

			echo json_encode(array("success"=>true,"msg"=>"berhasil menghapus","token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	public function tambahProduk(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			if($_SESSION["uploadedPhotos"] != 0){
				$tgl = date("Y-m-d H:i:s");
				$string = $this->clean($_POST["nama"]);

				$customlabel = [];
				if(isset($_POST["labeltext"])){
					foreach($_POST["labeltext"] as $k => $v){
						$customlabel[] = [
							"text"	=> $v,
							"warna"	=> $_POST["labelwarna"][$k],
							"background"	=> $_POST["labelbackground"][$k],
						];
					}
					unset($_POST["labeltext"]);
					unset($_POST["labelwarna"]);
					unset($_POST["labelbackground"]);
				}
				$_POST['customlabel'] = json_encode($customlabel);

				if(isset($_POST["grosir_id"])){
					$grosirid = $_POST["grosir_id"];
					$grosirmin = $_POST["grosir_min"];
					$grosirharga = $_POST["grosir_harga"];
					unset($_POST["grosir_id"]);
					unset($_POST["grosir_min"]);
					unset($_POST["grosir_harga"]);
				}

				$arr = $_POST;
				$arr2 = array(
					"tglbuat"	=> $tgl,
					"tglupdate"	=> $tgl,
					"url"		=> $string."-".date("His")
				);
				$data = array_merge($arr,$arr2);
				$this->db->insert("produk",$data);
				$insertid = $this->db->insert_id();

				if(isset($_SESSION["fotoProduk"]) AND count($_SESSION["fotoProduk"]) > 0){
					for($i=0; $i<count($_SESSION["fotoProduk"]); $i++){
						$this->db->where("id",$_SESSION["fotoProduk"][$i]);
						$this->db->update("upload",array("idproduk"=>$insertid));
					}
					$this->session->unset_userdata("fotoProduk");
				}

				if(isset($_SESSION["fotoCopy"]) AND count($_SESSION["fotoCopy"]) > 0){
					foreach($_SESSION["fotoCopy"] as $key => $val){
						$up = $this->admfunc->getUpload($val,"semua");
						$this->db->insert("upload",array("idproduk"=>$insertid,"jenis"=>$up->jenis,"nama"=>$up->nama,"tgl"=>date("Y-m-d H:i:s")));
					}
					$this->session->unset_userdata("fotoCopy");
				}

				if(isset($grosirid)){
					for($i=0; $i<count($grosirid); $i++){
						if(intval($grosirid[$i]) > 0){
							$this->db->where("id",$grosirid[$i]);
							$this->db->update("produkgrosir",["tgl"=>$tgl,"idproduk"=>$insertid,"minimal"=>$grosirmin[$i],"harga"=>$grosirharga[$i]]);
						}else{
							$this->db->insert("produkgrosir",["tgl"=>$tgl,"idproduk"=>$insertid,"minimal"=>$grosirmin[$i],"harga"=>$grosirharga[$i]]);
						}
					}
				}

				echo json_encode(array("success"=>true,"msg"=>"berhasil","id"=>$insertid,"token"=> $this->security->get_csrf_hash()));
			}else{
				echo json_encode(array("success"=>false,"msg"=>"foto wajib di isi: ".$_SESSION["uploadedPhotos"],"token"=> $this->security->get_csrf_hash()));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!","token"=> $this->security->get_csrf_hash()));
		}
	}
	public function updateproduk(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = $this->clean($_POST["id"]);
			$tgl = date("Y-m-d H:i:s");

			$customlabel = [];
			if(isset($_POST["labeltext"])){
				foreach($_POST["labeltext"] as $k => $v){
					$customlabel[] = [
						"text"	=> $v,
						"warna"	=> $_POST["labelwarna"][$k],
						"background"	=> $_POST["labelbackground"][$k],
					];
				}
				unset($_POST["labeltext"]);
				unset($_POST["labelwarna"]);
				unset($_POST["labelbackground"]);
			}
			$_POST['customlabel'] = json_encode($customlabel);

			if(isset($_POST["grosir_id"])){
				for($i=0; $i<count($_POST["grosir_id"]); $i++){
					if(intval($_POST["grosir_id"][$i]) > 0){
						$this->db->where("id",$_POST["grosir_id"][$i]);
						$this->db->update("produkgrosir",["tgl"=>$tgl,"idproduk"=>$_POST["id"],"minimal"=>$_POST["grosir_min"][$i],"harga"=>$_POST["grosir_harga"][$i]]);
					}else{
						$this->db->insert("produkgrosir",["tgl"=>$tgl,"idproduk"=>$_POST["id"],"minimal"=>$_POST["grosir_min"][$i],"harga"=>$_POST["grosir_harga"][$i]]);
					}
				}
				unset($_POST["grosir_id"]);
				unset($_POST["grosir_min"]);
				unset($_POST["grosir_harga"]);
			}
			if(isset($_POST["deskripsi"])){
				$html = $_POST["deskripsi"];
				unset($_POST["deskripsi"]);
				/*$dom = new DOMDocument();
				$dom->loadHTML($html);
				$script = $dom->getElementsByTagName('script');
				$remove = [];
				foreach($script as $item){
					$remove[] = $item;
				}
				foreach ($remove as $item){
					$item->parentNode->removeChild($item); 
				}
				$html = $dom->saveHTML();*/
				$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
				$_POST["deskripsi"] = $html;
			}
			if(isset($_POST["files"])){
				unset($_POST["files"]);
			}

			$arr = $_POST;
			//$string = $this->clean($_POST["nama"]);
			$arr2 = array("tglupdate"=> $tgl); //,"url"=>$string."-".date("His"));
			$data = array_merge($arr,$arr2);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->update("produk",$data);

			echo json_encode(array("success"=>true,"msg"=>"berhasil","id"=>$_POST["id"],"token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!","token"=> $this->security->get_csrf_hash()));
		}
	}
	function hapusgrosir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			$this->db->where("id",$_POST["id"]);
			$this->db->delete("produkgrosir");

			echo json_encode(array("success"=>true,"msg"=>"berhasil","token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!","token"=> $this->security->get_csrf_hash()));
		}
	}
	public function varianadd(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			$data = [
				"nama"	=> $_POST["nama"],
				"tgl"	=> date("Y-m-d H:i:s")
			];
			$this->db->insert("variasiwarna",$data);
			$insertid = $this->db->insert_id();

			$this->db->where("idproduk",$_POST["produk"]);
			$this->db->group_by("size");
			$db = $this->db->get("produkvariasi");
			$produk = $this->admfunc->getProduk($_POST["produk"],"semua");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					if($r->size > 0){
						$data = [
							"idproduk"	=>$_POST["produk"],
							"harga"		=>$produk->harga,
							"hargaagen"	=>$produk->hargaagen,
							"hargaagensp"	=>$produk->hargaagensp,
							"hargareseller"	=>$produk->hargareseller,
							"hargadistri"	=>$produk->hargadistri,
							"warna"	=>$insertid,
							"size"	=>$r->size,
							"tgl"	=>date("Y-m-d H:i:s")
						];
						$this->db->insert("produkvariasi",$data);
					}else{
						$data = [
							"idproduk"=>$_POST["produk"],
							"harga"=>$produk->harga,
							"hargadistri"=>$produk->hargadistri,
							"hargaagen"=>$produk->hargaagen,
							"hargaagensp"=>$produk->hargaagensp,
							"hargareseller"=>$produk->hargareseller,
							"warna"=>$insertid,
							"tgl"=>date("Y-m-d H:i:s")
						];
						$this->db->insert("produkvariasi",$data);
					}
				}
			}else{
				$data = [
					"idproduk"=>$_POST["produk"],
					"harga"=>$produk->harga,
					"hargadistri"=>$produk->hargadistri,
					"hargaagen"=>$produk->hargaagen,
					"hargaagensp"=>$produk->hargaagensp,
					"hargareseller"=>$produk->hargareseller,
					"warna"=>$insertid,
					"tgl"=>date("Y-m-d H:i:s")
				];
				$this->db->insert("produkvariasi",$data);
			}

			echo json_encode(array("success"=>true,"msg"=>"berhasil","id"=>$insertid,"token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	public function subvarianadd(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			$this->db->where("idproduk",$_POST["produk"]);
			$this->db->group_by("warna");
			$db = $this->db->get("produkvariasi");
			$produk = $this->admfunc->getProduk($_POST["produk"],"semua");
			if($db->num_rows() > 0){
				$data = [
					"nama"	=> $_POST["nama"],
					"tgl"	=> date("Y-m-d H:i:s")
				];
				$this->db->insert("variasisize",$data);
				$insertid = $this->db->insert_id();

				foreach($db->result() as $r){
					$data = [
						"idproduk"=>$_POST["produk"],
						"harga"=>$produk->harga,
						"hargadistri"=>$produk->hargadistri,
						"hargaagen"=>$produk->hargaagen,
						"hargaagensp"=>$produk->hargaagensp,
						"hargareseller"=>$produk->hargareseller,
						"size"=>$insertid,
						"warna"=>$r->warna,
						"tgl"=>date("Y-m-d H:i:s")
					];
					$this->db->insert("produkvariasi",$data);

					$this->db->where("idproduk",$_POST["produk"]);
					$this->db->where("warna",$r->warna);
					$this->db->where("size",0);
					$this->db->delete("produkvariasi");
				}

				echo json_encode(array("success"=>true,"msg"=>"berhasil","id"=>$insertid,"token"=> $this->security->get_csrf_hash()));
			}else{
				echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	function varianhapus(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			$this->db->where("idproduk",$_POST["produk"]);
			$this->db->where("warna",$_POST["id"]);
			$this->db->delete("produkvariasi");

			echo json_encode(array("success"=>true,"msg"=>"berhasil","token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	function subvarianhapus(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			$this->db->where("idproduk",$_POST["produk"]);
			$this->db->where("size",$_POST["id"]);
			$this->db->delete("produkvariasi");

			echo json_encode(array("success"=>true,"msg"=>"berhasil","token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	function variansave($id=0){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["harga"])){
			$stok = 0;
			foreach($_POST["harga"] as $ids => $harga){
				$stok += intval($_POST["stok"][$ids]);
				$data = [
					"stok"	=> $_POST["stok"][$ids],
					"kuota"	=> $_POST["stok"][$ids],
					"harga"	=> $harga,
					"hargareseller"	=> $_POST["hargareseller"][$ids],
					"hargaagen"		=> $_POST["hargaagen"][$ids],
					"hargaagensp"	=> $_POST["hargaagensp"][$ids],
					"hargadistri"	=> $_POST["hargadistri"][$ids],
					"tgl"	=> date("Y-m-d H:i:s")
				];
				$this->db->where("id",$ids);
				$this->db->update("produkvariasi",$data);
				
				/*
				if($_POST["id"][$i] != 0){
					if($this->admfunc->getProduk($id,"preorder") > 0){
						$this->db->where("variasi",$_POST["id"][$i]);
						$t = $this->db->get("preorder");
						$tot = 0;
						foreach($t->result() as $r){
							$tot += $r->jumlah;
						}
						
						if($_POST["stok"][$i] < $tot){
							echo json_encode(array("success"=>false,"msg"=>"stok variasi harus lebih dari jumlah pre order masuk [jumlah pre order masuk: $tot]","token"=> $this->security->get_csrf_hash()));
							exit;
						}
					}
				
				}else{
					$this->db->insert("produkvariasi",$data);
				}
				*/
			}
			$this->db->where("id",$id);
			$this->db->update("produk",["stok"=>$stok]);
			
			echo json_encode(array("success"=>true,"msg"=>"berhasil","stok"=>$stok,"token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	/*
	function simpanvariasi($id=0){
		if(isset($_POST["id"])){
			$stok = 0;
			for($i=0; $i<count($_POST["id"]); $i++){
				$stok += intval($_POST["stok"][$i]);
				$data = [
					"idproduk"	=> $id,
					"warna"	=> $_POST["warna"][$i],
					"size"	=> $_POST["size"][$i],
					"stok"	=> $_POST["stok"][$i],
					"kuota"	=> $_POST["stok"][$i],
					"harga"	=> $_POST["harga"][$i],
					"hargareseller"	=> $_POST["hargareseller"][$i],
					"hargaagen"		=> $_POST["hargaagen"][$i],
					"hargaagensp"	=> $_POST["hargaagensp"][$i],
					"hargadistri"	=> $_POST["hargadistri"][$i],
					"tgl"	=> date("Y-m-d H:i:s")
				];
				
				if($_POST["id"][$i] != 0){
					if($this->admfunc->getProduk($id,"preorder") > 0){
						$this->db->where("variasi",$_POST["id"][$i]);
						$t = $this->db->get("preorder");
						$tot = 0;
						foreach($t->result() as $r){
							$tot += $r->jumlah;
						}
						
						if($_POST["stok"][$i] < $tot){
							echo json_encode(array("success"=>false,"msg"=>"stok variasi harus lebih dari jumlah pre order masuk [jumlah pre order masuk: $tot]","token"=> $this->security->get_csrf_hash()));
							exit;
						}
					}
				
					$this->db->where("id",$_POST["id"][$i]);
					$this->db->update("produkvariasi",$data);
				}else{
					$this->db->insert("produkvariasi",$data);
				}
			}
			$this->db->where("id",$id);
			$this->db->update("produk",["stok"=>$stok]);
			
			echo json_encode(array("success"=>true,"msg"=>"berhasil","token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	*/
	private function clean($string) {
		$string = str_replace(' ', '-', $string);
		$string = preg_replace('/[^A-Za-z0-9\-]/', '-', $string);

		return preg_replace('/-+/', '-', $string);
	}

	// HALAMAN STATIS
	function halaman(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$this->db->where("id",intval($_POST["formid"]));
			$db = $this->db->get("page");
			$data = array();
			foreach($db->result() as $r){
				$data = array(
					"nama"	=> $r->nama,
					"konten"=> $r->konten
				);
			}
			
			echo json_encode(array("success"=>true,"data"=>$data,"token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false));
		}
	}
	function updatehalaman(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			$_POST["usrid"] = $_SESSION["admusrid"];
			$_POST["tgl"] = date("Y-m-d H:i:s");
			$_POST["slug"] = $this->admfunc->cleanURL($_POST["nama"]);
			$_POST["status"] = 1;

			if($_POST["id"] != 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("page",$_POST);
			}else{
				$this->db->insert("page",$_POST);
			}
			
			echo json_encode(array("success"=>true,"token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"token"=> $this->security->get_csrf_hash()));
		}
	}
	public function hapushalaman(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("page");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	
	// PESANAN
	function detailpesanan(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["theid"])){
			$this->load->view("atmin/admin/pesanandetail");
		}else{
			echo "404 - Request Not Found";
		}
	}
	function updatepreorder(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			
			$this->db->where("id",intval($_POST["id"]));
			$this->db->update("preorder",$_POST);
			
			echo json_encode(array("success"=>true,"token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"token"=> $this->security->get_csrf_hash()));
		}
	}
	function updatepesanan(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			$status = isset($_POST["status"]) ? $_POST["status"] : 1;
			$trx = $this->admfunc->getTransaksi(intval($_POST["id"]),"semua","idbayar");
			
			if(isset($_POST["statusbayar"])){
				if($_POST["statusbayar"] == 1){
					$this->admfunc->notifsukses($_POST["id"]);
				}
				
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("pembayaran",["status"=>intval($_POST["statusbayar"]),"tglupdate"=>date("Y-m-d H:i:s")]);
			}

			if($status >= 3){
				$data = ["status"=>$status,"tglupdate"=>date("Y-m-d H:i:s"),"selesai"=>date("Y-m-d H:i:s")];
			}elseif($status == 1){
				$status = ($trx->digital == 1) ? 3 : $status;
				$data = ["status"=>$status,"tglupdate"=>date("Y-m-d H:i:s")];
			}else{
				$data = ["status"=>$status,"tglupdate"=>date("Y-m-d H:i:s")];
			}
			
			$this->db->where("idbayar",intval($_POST["id"]));
			$this->db->update("transaksi",$data);

			if($status == 3){
				$this->selesaiPesanan($trx->id);
			}elseif($status == 1){
				$this->db->where("idtransaksi",$trx->id);
				$this->db->update("afiliasi",["status"=>1]);
			}
			
			echo json_encode(array("success"=>true,"token"=>$this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"token"=>$this->security->get_csrf_hash()));
		}
	}
	private function selesaiPesanan($id){
		$trx = $this->admfunc->getTransaksi($id,"semua");

		// KOIN CASHBACK
		$this->db->where("idtransaksi",$trx->id);
		$trxp = $this->db->get("transaksiproduk");
		$koin = $this->admfunc->getSaldo($trx->usrid,"koin","usrid");
		$kointambah = 0;
		foreach($trxp->result() as $t){
			$kointambah += $t->koin * $t->jumlah;
		}
		$koin = $koin + $kointambah;
		$this->db->where("usrid",$trx->usrid);
		$this->db->update("saldo",["koin"=>$koin]);

		// AFILIASI
		$this->db->where("idtransaksi",$trx->id);
		$db = $this->db->get("afiliasi");
		foreach($db->result() as $r){
			$saldo = $this->admfunc->getSaldo($r->usrid,"semua");
			$saldototal = $saldo->saldo + $r->jumlah;
			$tgl = date("Y-m-d H:i:s");
			$data = [
				"usrid"	=> $r->usrid,
				"trxid"	=> "TOP_".$r->usrid.date("YmdHis"),
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
	}
	function batalkanpesanan($by=null){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->admfunc->notifbatal($_POST["id"],1);
			$trx = $this->admfunc->getTransaksi(intval($_POST["id"]),"semua","idbayar");

			$this->db->where("idtransaksi",$trx->id);
			$this->db->update("afiliasi",["status"=>3]);

			$variasi = [];
			$this->db->where("idtransaksi",$trx->id);
			$db = $this->db->get("transaksiproduk");
			foreach($db->result() as $r){
				if($r->variasi > 0){
					$var = $this->admfunc->getVariasi($r->variasi,"semua","id");
					if(isset($var->stok)){
						$stok = $var->stok + $r->jumlah;
						$variasi[] = $r->variasi;
						$stock[] = $stok;
						$stokawal[] = $var->stok;
						$jml[] = $r->jumlah;
					}
				}else{
					$pro = $this->admfunc->getProduk($r->idproduk,"semua");
					if(is_object($pro)){
						$stok = $pro->stok + $r->jumlah;
						$this->db->where("id",$r->idproduk);
						$this->db->update("produk",["stok"=>$stok,"tglupdate"=>date("Y-m-d H:i:s")]);

						$data = array(
							"usrid"	=> $trx->usrid,
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
			}
			for($i=0; $i<count($variasi); $i++){
				$this->db->where("id",$variasi[$i]);
				$this->db->update("produkvariasi",["stok"=>$stock[$i],"tgl"=>date("Y-m-d H:i:s")]);
				
				$data = array(
					"usrid"	=> $trx->usrid,
					"stokawal" => $stokawal[$i],
					"stokakhir" => $stock[$i],
					"variasi" => $variasi[$i],
					"jumlah" => $jml[$i],
					"tgl"	=> date("Y-m-d H:i:s"),
					"idtransaksi" => $trx->id
				);
				$this->db->insert("historystok",$data);
			}
			
			$this->db->where("id",intval($_POST["id"]));
			$this->db->update("pembayaran",["status"=>3,"tglupdate"=>date("Y-m-d H:i:s")]);
			
			$this->db->where("idbayar",intval($_POST["id"]));
			$this->db->update("transaksi",["status"=>4,"tglupdate"=>date("Y-m-d H:i:s"),"selesai"=>date("Y-m-d H:i:s")]);

			// TOTAL SALDO
			if($by == null){
				$byr = $this->admfunc->getBayar($_POST["id"],"semua");
				$saldojml = $byr->saldo;
				$koinjml = $byr->koin;
			}else{
				$gby = $this->admfunc->getBayar($_POST["id"],"semua");
				$saldojml = $gby->saldo;
				$saldojml += ($gby->metode_bayar != 1) ? $gby->transfer : 0;
				$koinjml = $gby->koin;
			}
			$sal = $this->admfunc->getSaldo($trx->usrid,"semua","usrid");
			$saldoawal = $sal->saldo;
			$koinawal = $sal->koin;

			// UPDATE KOIN
			if($koinjml > 0){
				$koin = $koinawal + $koinjml;
				$this->db->where("usrid",$trx->usrid);
				$this->db->update("saldo",array("koin"=>$koin));
			}

			if($saldojml > 0){
				// UPDATE SALDO
				$saldo = $saldoawal + $saldojml;
				$this->db->where("usrid",$trx->usrid);
				$this->db->update("saldo",array("saldo"=>$saldo));

				// SALDO TARIK
				$tgl = date("Y-m-d H:i:s");
				$data = [
					"usrid"	=> $trx->usrid,
					"trxid"	=> "TOP_".$trx->usrid.date("YmdHis"),
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
			
			echo json_encode(array("success"=>true,"token"=>$this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"token"=>$this->security->get_csrf_hash()));
		}
	}
	function inputresi(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["theid"])){
			//$_POST["theid"] = intval(["theid"]);
			$trx = $this->admfunc->getTransaksi(intval($_POST["theid"]),"semua");
			$usrid = $this->admfunc->getUserdata($trx->usrid,"semua");

			$status = $trx->kurir != "cod" ? 2 : 3;
			$resi = (isset($_POST["resi"])) ? $_POST["resi"] : "";
			$data = array(
				"resi" => $resi,
				"tglupdate"=>date("Y-m-d H:i:s"),
				"kirim" => date("Y-m-d H:i:s"),
				"status" => $status
			);
			if($status == 3){
				$data["selesai"] = date("Y-m-d H:i:s");
			}

			$this->db->where("id",intval($_POST["theid"]));
			$this->db->update("transaksi",$data);

			$namatoko = $this->admfunc->globalset("nama");

			$pesan = "
				Berikut resi pengiriman untuk pesanan anda di <b>".$namatoko."</b><br/>
				Resi: <b style='font-size:120%'>".$resi."</b><br/>&nbsp;<br/>&nbsp;<br/>
				Lacak pengirimannya langsung di menu <b>pesananku</b><br/>
				<a href='".site_url('manage/pesanan')."'>Klik Disini</a>
			";
			$this->admfunc->sendEmail($usrid->username,$namatoko." - Resi Pengiriman Pesanan",$pesan,"Resi Pengiriman");
			$pesan = "
				Berikut resi pengiriman untuk pesanan anda di *".$namatoko."* \n".
				"Resi: *".$resi."* \n \n".
				"Lacak pengirimannya langsung di menu *pesananku* \n ".
				site_url('manage/pesanan')."
			";
			$this->admfunc->sendWA($this->admfunc->getProfil($trx->usrid,"nohp","usrid"),$pesan);
			$this->admfunc->notifMobile("Resi Pesanan #".$trx->orderid,"admin telah mengirimkan pesananmu, cek status pengirimannya langsung di aplikasi ".$namatoko,"",$trx->usrid);
			
			echo json_encode(array("success"=>true,"token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"token"=> $this->security->get_csrf_hash()));
		}
	}

	// FORM VARIASI PRODUK
	public function variasiform($idpro=0){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$data = $this->load->view("atmin/admin/produkformvariasi",array("id"=>$idpro),true);
		echo json_encode(array("success"=>true,"data"=>$data,"token"=> $this->security->get_csrf_hash()));
	}

	// FILE UPLOAD
	public function jadikanFotoUtama($id){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$idproduk = (isset($_POST["idproduk"])) ? intval($_POST["idproduk"]) : 0;
		$this->db->where("idproduk",$idproduk);
		$this->db->where("jenis",1);
		$this->db->update("upload",array("jenis"=>0));

		$this->db->where("id",$id);
		$this->db->update("upload",array("jenis"=>1));

		echo json_encode(array("success"=>true,"token"=> $this->security->get_csrf_hash()));
	}
	public function hapusFotoProduk($id){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if($id == "all"){
			$idproduk = (isset($_POST["idproduk"])) ? intval($_POST["idproduk"]) : 0;
			if(isset($_SESSION["fotoCopy"]) AND is_array($_SESSION["fotoCopy"]) AND count($_SESSION["fotoCopy"]) > 0){
				$this->db->where_not_in("id",$_SESSION["fotoCopy"]);
			}
			$this->db->where("idproduk",$idproduk);
			$db = $this->db->get("upload");
			foreach($db->result() as $res){
				$this->db->select("id");
				$this->db->where("nama",$res->nama);
				$this->db->where("id !=",$res->id);
				$kmb = $this->db->get("upload");

				if($kmb->num_rows() == 0){
					$url = "cdn/uploads/".$res->nama;
					if(file_exists($url)){
						unlink($url);
					}
				}
			}

			$_SESSION["uploadedPhotos"] = 0;
			$this->session->unset_userdata("fotoProduk");
			if(isset($_SESSION["fotoCopy"]) AND is_array($_SESSION["fotoCopy"]) AND count($_SESSION["fotoCopy"]) > 0){
				$this->db->where_not_in("id",$_SESSION["fotoCopy"]);
				$this->session->unset_userdata("fotoCopy");
			}
			$this->db->where("idproduk",$idproduk);
			$this->db->delete("upload");
		}else{
			$are = (isset($_SESSION["fotoCopy"]) AND is_array($_SESSION["fotoCopy"]) AND count($_SESSION["fotoCopy"]) > 0) ? $_SESSION["fotoCopy"] : array(0);
			if(!in_array($id,$are)){
				$nama = $this->admfunc->getUpload($id,"nama");
				$this->db->select("id");
				$this->db->where("nama",$nama);
				$this->db->where("id !=",$id);
				$kmb = $this->db->get("upload");

				if($kmb->num_rows() == 0){
					$url = "cdn/uploads/".$nama;
					if(file_exists($url)){
						unlink($url);
					}
				}

				$this->db->where("id",$id);
				$this->db->delete("upload");
				if(($key = array_search($id, $_SESSION["fotoProduk"])) == true) {
					unset($_SESSION["fotoProduk"][$key]);
				}
			}else{
				if(($key = array_search($id, $_SESSION["fotoCopy"])) == true) {
					unset($_SESSION["fotoCopy"][$key]);
				}
			}
			$_SESSION["uploadedPhotos"] = $_SESSION["uploadedPhotos"]-1;
		}

		echo json_encode(array("success"=>true,"token"=> $this->security->get_csrf_hash()));
	}
	public function uploadFotoResult($idpro=0){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$data = $this->load->view("atmin/admin/produkuploadfoto",array("idproduk"=>$idpro),true);
		echo json_encode(array("success"=>true,"data"=>$data,"token"=> $this->security->get_csrf_hash()));
	}
	public function uploadFotoProduk(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			//$_POST["idproduk"] = intval(["idproduk"]);
			$this->db->where("idproduk",$_POST["idproduk"]);
			$db = $this->db->get("upload");
			$jenis = $db->num_rows() > 0 ? 0 : 1;

			$config['upload_path'] = './cdn/uploads/';
			$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
			$config['file_name'] = $_SESSION["admusrid"].date("YmdHis");

			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('fotoProduk')){
				echo json_encode(array("success"=>false,"msg"=>"error: ".$this->upload->display_errors(),"token"=> $this->security->get_csrf_hash()));
			}else{
				$uploadData = $this->upload->data();
				/*
				$this->load->library('image_lib');
				$config_resize['image_library'] = 'gd2';
				$config_resize['maintain_ratio'] = TRUE;
				$config_resize['master_dim'] = 'height';
				$config_resize['quality'] = "100%";
				$config_resize['source_image'] = $config['upload_path'].$uploadData["file_name"];

				$config_resize['width'] = 1024;
				$config_resize['height'] = 720;
				$this->image_lib->initialize($config_resize);
				$this->image_lib->resize();
				*/

				$data = array(
					"idproduk"=> $_POST["idproduk"],
					"jenis"=> $jenis,
					"nama"=> $uploadData["file_name"],
					"tgl"=> date("Y-m-d H:i:s")
				);
				$this->db->insert("upload",$data);
				$_SESSION["fotoProduk"][] = $this->db->insert_id();

				if($_POST["idproduk"] == 0){
					$upl = (isset($_SESSION["uploadedPhotos"])) ? $_SESSION["uploadedPhotos"] : 0;
					$_SESSION["uploadedPhotos"] = $upl+1;
				}

				echo json_encode(array("success"=>true,"token"=> $this->security->get_csrf_hash()));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"File tidak ditemukan","token"=> $this->security->get_csrf_hash()));
		}
	}
	public function uploadLogo($tipe=1){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			$type = $tipe == 1 ? "logo" : "favicon";
			$foto = $this->admfunc->globalset($type);
			if(file_exists("./assets/images/".$foto)){
				unlink("./assets/images/".$foto);
			}

			$config['upload_path'] = './assets/images/';
			$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
			$config['file_name'] = $type."-".date("YmdHis");

			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('logo')){
				$error = array('error' => $this->upload->display_errors());
			}else{
				$uploadData = $this->upload->data();
				$data = array(
					"value"=> $uploadData["file_name"],
					"tgl"=> date("Y-m-d H:i:s")
				);
				$this->db->where("field",$type);
				$this->db->update("setting",$data);
				echo json_encode(array("success"=>true,"filename"=>$uploadData["file_name"],"token"=> $this->security->get_csrf_hash()));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"File tidak ditemukan","token"=> $this->security->get_csrf_hash()));
		}
	}

	// BLOG
	public function hapusblog(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$img = $this->admfunc->getBlog($_POST["id"],"img");
			if($img != "no-image.png"){
				unlink("cdn/uploads/".$img);
			}
			
			$this->db->where("id",$_POST["id"]);
			$this->db->delete("blog");

			echo json_encode(array("success"=>true,"msg"=>"berhasil menghapus","token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"form not submitted!"));
		}
	}
	public function uploadblog($id=0){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST)){
			if(isset($_SESSION["fotoPage"])){
				unlink("cdn/uploads/".$_SESSION["fotoPage"]);
				$this->session->unset_userdata("fotoPage");
			}

			$config['upload_path'] = './cdn/uploads/';
			$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
			$config['file_name'] = "blogpost_".$_SESSION["admusrid"].date("YmdHis");

			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('foto')){
				echo json_encode(array("success"=>false,"msg"=>$this->upload->display_errors(),"token"=> $this->security->get_csrf_hash()));
			}else{
				$uploadData = $this->upload->data();
				/*
				$this->load->library('image_lib');
				$config_resize['image_library'] = 'gd2';
				$config_resize['maintain_ratio'] = TRUE;
				$config_resize['master_dim'] = 'height';
				$config_resize['quality'] = "100%";
				$config_resize['source_image'] = $config['upload_path'].$uploadData["file_name"];

				$config_resize['width'] = 1024;
				$config_resize['height'] = 720;
				$this->image_lib->initialize($config_resize);
				$this->image_lib->resize();
				*/

				if($id == 0){
					$_SESSION["fotoPage"] = $uploadData["file_name"];
				}else{
					$img = $this->admfunc->getBlog($id,"img");
					$url = "cdn/uploads/".$img;
					if($img != "no-image.png" AND file_exists($url)){
						unlink($url);
					}
					$this->db->where("id",$id);
					$this->db->update("blog",["img"=>$uploadData["file_name"],"tgl"=>date("Y-m-d H:i:s")]);
				}

				echo json_encode(array("success"=>true,"filename"=>$uploadData["file_name"],"token"=> $this->security->get_csrf_hash()));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"File tidak ditemukan","token"=> $this->security->get_csrf_hash()));
		}
	}
	
	
	// PESAN KOTAK MASUK
	public function pesanmasuk($usrid){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($usrid)){
			$this->db->where("(dari = 0 AND tujuan = ".$usrid.") OR (dari = ".$usrid." AND tujuan = 0)");
			$this->db->limit(100);
			$db = $this->db->get("pesan");
			
			$this->db->where("dari",$usrid);
			$this->db->where("baca",0);
			$this->db->update("pesan",array("baca"=>1));
							
			$currdate = false;
			if($db->num_rows() > 0){
				$noe = 1;
				foreach($db->result() as $r){
					$centang = ($r->baca == 0) ? "<i class='fas fa-check'></i>" : "<i class='fas fa-check-double'></i>";
					$centang = ($r->tujuan != 0) ? $centang : "";
					$loc = ($r->tujuan == 0) ? "left" : "right";
					$tgl = '<br/><small>'.$this->admfunc->ubahTgl("d/m H:i",$r->tgl).' &nbsp'.$centang.'</small>';
				
					if($this->admfunc->ubahTgl("d-m-Y",$r->tgl) != $currdate){
						echo '<div class="pesanwrap center">
								<div class="isipesan">'.$this->admfunc->ubahTgl("d M Y",$r->tgl).'</div>
							</div>';
						$currdate = $this->admfunc->ubahTgl("d-m-Y",$r->tgl);
					}
						
					if($r->idproduk > 0){
						$prod = $this->admfunc->getProduk($r->idproduk,"semua");
						if($r->tujuan == 0){
							$usr = $this->admfunc->getUserdata($r->dari,"level");
						}else{
							$usr = $this->admfunc->getUserdata($r->tujuan,"level");
						}
						$level = isset($usr) ? $usr : 0;
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
						echo '
							<div class="pesanwrap '.$loc.'">
								<div class="isipesan cursor-pointer" onclick="window.open(\''.site_url("produk/".$prod->url).'\')">
									<div class="row">
										<div class="col-3 text-left">
											<img src="'.$this->admfunc->getFoto($r->idproduk,"utama").'" style="max-width:100%;max-height:60px;border-radius:8px;" />
										</div>
										<div class="col-9 text-left">
											<div class="font-medium" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'.$prod->nama.'</div>
											<div class="" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Rp. '.$this->admfunc->formUang($harga).' | '.$prod->stok.' tersedia</div>
										</div>
									</div>
								</div>
							</div>
						';
					}else{
						echo '<div class="pesanwrap '.$loc.'">
								<div class="isipesan"><b>'.$this->admfunc->clean($r->isipesan)."</b>".$tgl.'</div>
							</div>';
					}
					$noe++;
				}
			}else{
				echo '
					<div class="pesanwrap center">
						<div class="isipesan">belum ada pesan</div>
					</div>';
			}
		}else{
			echo '
				<div class="pesanwrap center">
					<div class="isipesan">belum ada pesan</div>
				</div>';
		}
	}
	public function kirimpesan(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST['isipesan']) AND intval($_POST["tujuan"]) > 0){
			$idproduk = (isset($_POST["idproduk"])) ? intval($_POST["idproduk"]) : 0;
			$isipesan = (isset($_POST["isipesan"])) ? $this->admfunc->clean($_POST["isipesan"]) : "";
			if($idproduk > 0 AND $isipesan != ""){
				$data = array(
					"isipesan"	=> "",
					"idproduk"	=> $idproduk,
					"tujuan"	=> $_POST["tujuan"],
					"baca"		=> 0,
					"dari"		=> 0,
					"tgl"		=> date("Y-m-d H:i:s")
				);
				$this->db->insert("pesan",$data);
				$data = array(
					"isipesan"	=> $isipesan,
					"idproduk"	=> 0,
					"tujuan"	=> $_POST["tujuan"],
					"baca"		=> 0,
					"dari"		=> 0,
					"tgl"		=> date("Y-m-d H:i:s")
				);
				$this->db->insert("pesan",$data);
			}else{
				$data = array(
					"isipesan"	=> $isipesan,
					"idproduk"	=> $idproduk,
					"tujuan"	=> $_POST["tujuan"],
					"baca"		=> 0,
					"dari"		=> 0,
					"tgl"		=> date("Y-m-d H:i:s")
				);
				$this->db->insert("pesan",$data);
			}
			$this->admfunc->notifMobile("Pesan baru dari admin",$this->admfunc->potong($this->admfunc->clean($isipesan),40,"..."),"/chat",$_POST["tujuan"]);

			echo json_encode(array("success"=>true,"token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"token"=> $this->security->get_csrf_hash()));
		}
	}
	
	/* RESELLER AGEN */
	public function tambahagen(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$data = array(
				"level"	=> $_POST["level"],
				"tgl"	=> date("Y-m-d H:i:s")
			);
			if($_POST["id"] > 0){
				if($_POST["level"] == 5){
					$head = "Distributor";
				}elseif($_POST["level"] == 4){
					$head = "Reseller";
				}elseif($_POST["level"] == 3){
					$head = "Agen";
				}elseif($_POST["level"] == 2){
					$head = "Agen Premium";
				}else{
					$head = "User Normal";
				}

				// NOTIFIKASI KE USER/PENGGUNA
				$set = $this->admfunc->globalset("semua");
				$usr = $this->admfunc->getUserdata($_POST["id"],"semua");
				$profil = $this->admfunc->getProfil($_POST["id"],"semua","usrid");
				if($usr->username != ""){
					$pesan = "
						Halo <b>".$profil->nama."</b><br/>
						Kami informasikan bahwa mulai saat ini status level pengguna kamu telah berubah menjadi <b>".$head."</b><br/>
						Agar perubahan levelmu bisa bisa segera aktif, silahkan logout terlebih dahulu dari website & aplikasi ".$set->nama."
					";
					$this->admfunc->sendEmail($usr->username,$set->nama." - Update Level Pengguna",$pesan,"Update Pengguna");
				}
				if($usr->nohp != ""){
					$pesan = "
						Halo *".$profil->nama."*\n".
						"Kami informasikan bahwa mulai saat ini status level pengguna kamu telah berubah menjadi *".$head."*\n".
						"Agar perubahan levelmu bisa bisa segera aktif, silahkan logout terlebih dahulu dari website & aplikasi ".$set->nama."
						"; 
					$this->admfunc->sendWA($usr->nohp,$pesan);
				}

				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("userdata",$data);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("userdata",$data);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			$res = $this->load->view("atmin/admin/agenform","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}
	}
	public function hapusagen(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}

		if(isset($_POST)){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",$_POST["id"]);
			$this->db->delete("userdata");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("alamat");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("profil");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("transaksi");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("preorder");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("transaksiproduk");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("pembayaran");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("rekening");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("wishlist");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("saldo");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("saldohistory");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("saldotarik");
			$this->db->where("usrid",$_POST["id"]);
			$this->db->delete("token");
			$this->db->where("dari",$_POST["id"]);
			$this->db->or_where("tujuan",$_POST["id"]);
			$this->db->delete("pesan");

			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
		}
	}
	function getusrid(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["level"])){
			//$_POST["level"] = intval(["level"]);
			$this->db->where("level",$_GET["level"]);
			$this->db->order_by("nama ASC, id DESC");
			$db = $this->db->get("userdata");

			$opt = '<option value="">== Pilih Pengguna ==</option>';
			foreach($db->result() as $r){
				$opt .= "<option value='".$r->id."'>".strtoupper(strtolower($this->admfunc->clean($r->nama)))." - ".$r->username." - ".$r->nohp."</option>";
			}
			echo json_encode(["success"=>true,"load"=>$opt,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
		}
	}
	
	/* VOUCHER */
	public function voucher(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/admin/voucherlist","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}elseif(isset($_POST["formid"])){
			$_POST["formid"] = $_POST["formid"];
			$this->db->where("id",intval($_POST["formid"]));
			$db = $this->db->get("voucher");
			$data = [];
			foreach($db->result() as $r){
				$data = [
					"id"		=> $_POST["formid"],
					"digital"	=> $r->digital,
					"usrid"		=> $r->usrid,
					"nama"		=> $r->nama,
					"deskripsi"	=> $r->deskripsi,
					"kode"		=> strtoupper(strtolower($r->kode)),
					"public"	=> $r->public,
					"mulai"		=> $r->mulai,
					"selesai"	=> $r->selesai,
					"jenis"		=> $r->jenis,
					"tipe"		=> $r->tipe,
					"idkab"		=> $r->idkab,
					"idproduk"	=> $r->idproduk,
					"potongan"	=> $r->potongan,
					"potonganmin"	=> $r->potonganmin,
					"potonganmaks"	=> $r->potonganmaks,
					"peruser"	=> $r->peruser,
					"token"		=> $this->security->get_csrf_hash()
				];
			}
			echo json_encode($data);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambahvoucher(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			$_POST["usrid"]	= $_SESSION["admusrid"];
			$_POST["kode"]	= strtoupper(strtolower($_POST["kode"]));
			if(isset($_POST["tipe"]) AND $_POST["tipe"] == 1){
				if(intval($_POST["potongan"]) > 100){
					echo json_encode(["success"=>false,"msg"=>"nilai potongan persen tidak boleh lebih dari 100%","token"=> $this->security->get_csrf_hash()]);
					exit;
				}
			}
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("voucher",$_POST);
				echo json_encode(["success"=>true,"msg"=>"","token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("voucher",$_POST);
				echo json_encode(["success"=>true,"msg"=>"","token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"msg"=>"","token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapusvoucher(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("voucher");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	
	/* TESTIMONI */
	public function testimoni(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/admin/testimonilist","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}elseif(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$this->db->where("id",intval($_POST["formid"]));
			$db = $this->db->get("testimoni");
			$data = [];
			foreach($db->result() as $r){
			$data = [
				"id"	=> $_POST["formid"],
				"nama"	=> $r->nama,
				"foto"	=> $r->foto,
				"jabatan"	=> $r->jabatan,
				"komentar"	=> $r->komentar,
				"token"	=> $this->security->get_csrf_hash()
			];
			}
			echo json_encode($data);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambahtestimoni(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			if(isset($_FILES['foto']) AND $_FILES['foto']['size'] != 0 && $_FILES['foto']['error'] == 0){
				$testi = (isset($_POST["id"])) ? intval($_POST["id"]) : 0;
				$this->db->where("id",$testi);
				$db = $this->db->get("testimoni");
				foreach($db->result() as $res){
					if(file_exists("cdn/uploads/".$res->foto) AND $res->foto != ""){
						unlink("cdn/uploads/".$res->foto);
					}
				}

				$config['upload_path'] = './cdn/uploads/';
				$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
				$config['file_name'] = "testimoni_".date("YmdHis");;
		
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('foto')){
					$error = $this->upload->display_errors();
					echo json_encode(array("success"=>false,"msg"=>$error,"token"=> $this->security->get_csrf_hash()));
					exit;
				}else{
					$upload_data = $this->upload->data();			
					$_POST["foto"] = $upload_data["file_name"];
				}
			}else{
				unset($_FILES["foto"]);
				unset($_POST["foto"]);
			}
			$_POST["tgl"] = date("Y-m-d H:i:s");
			$_POST["status"] = 1;
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("testimoni",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("testimoni",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapustestimoni(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		if(isset($_POST["id"])){
			$testi = (isset($_POST["id"])) ? intval($_POST["id"]) : 0;
			$this->db->where("id",$testi);
			$db = $this->db->get("testimoni");
			foreach($db->result() as $res){
				if(file_exists("cdn/uploads/".$res->foto) AND $res->foto != ""){
					unlink("cdn/uploads/".$res->foto);
				}
			}

			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("testimoni");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	
	/* BROADCAST */
	public function broadcast(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/admin/broadcastlist",["jenis"=>2],true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambahbroadcast($id=null){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		$tgl = date("Y-m-d H:i:s");
		$gambar = "";
		$rilis = $_POST["tgl"]." ".$_POST["jam"];
		
		if(isset($_FILES['gambar']) AND $_FILES['gambar']['size'] != 0 && $_FILES['gambar']['error'] == 0){
			$config['upload_path'] = './cdn/promo/';
			$config['allowed_types'] = 'gif|jpeg|jpg|png|webp';
			$config['file_name'] = "BC_".date("YmdHis");
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('gambar')){
				echo json_encode(array("success"=>false,"msg"=>"error: ".$this->upload->display_errors(),"token"=> $this->security->get_csrf_hash()));
				exit;
			}else{
				$uploadData = $this->upload->data();
				$gambar = $uploadData['file_name'];
			}
		}
		$data = [
			"tgl"	=> $tgl,
			"judul"	=> $_POST["judul"],
			"isi"	=> $_POST["isi"],
			"rilis"	=> $rilis,
			"gambar"=> $gambar,
			"jenis"	=> $_POST["jenis"],
			"tujuan"=> $_POST["tujuan"]
		];
		if($id == null){
			$this->db->insert("broadcast",$data);
		}else{
			$this->db->where("id",$id);
			$this->db->update("broadcast",$data);
		}
		echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
	}
	public function updatebroadcast(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}

		//unset($_POST)
		if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
		$this->db->where("id",$_POST["id"]);
		$this->db->update("broadcast",$_POST);
		echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
	}
	public function hapusbroadcast(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		$this->db->where("id",$_POST["id"]);
		$this->db->delete("broadcast");
		echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
	}
	
	/* SALDO */
	public function topup(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/admin/saldolist",["jenis"=>2],true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambahtopup(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$saldo = $this->admfunc->getSaldo($_POST["usrid"],"semua");
		$saldototal = $saldo->saldo + intval($_POST["total"]);
		$tgl = date("Y-m-d H:i:s");
		$data = [
			"usrid"	=> $_POST["usrid"],
			"trxid"	=> "TOP_".$_POST["usrid"].date("YmdHis"),
			"jenis"	=> 2,
			"status"=> 1,
			"selesai"	=> $tgl,
			"tgl"	=> $tgl,
			"total"	=> $_POST["total"],
			"metode"=> 1,
			"keterangan"=> $_POST["keterangan"]
		];
		$this->db->insert("saldotarik",$data);
		$topup = $this->db->insert_id();

		$data2 = [
			"tgl"	=> $tgl,
			"usrid"	=> $_POST["usrid"],
			"jenis"	=> 1,
			"jumlah"=> $_POST["total"],
			"darike"=> 1,
			"sambung"	=> $topup,
			"saldoawal"	=> $saldo->saldo,
			"saldoakhir"=> $saldototal
		];
		$this->db->insert("saldohistory",$data2);

		if($saldo->id == 0){
			$this->db->insert("saldo",["usrid"=>$_POST["usrid"],"apdet"=>$tgl,"saldo"=>$saldototal]);
		}else{
			$this->db->where("id",$saldo->id);
			$this->db->update("saldo",["apdet"=>$tgl,"saldo"=>$saldototal]);
		}
						
		$this->admfunc->notifTopup($topup);

		echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
	}
	public function updatetopup(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$st = $this->admfunc->getSaldotarik($_POST["id"],"semua");
		$sal = $this->admfunc->getSaldo($st->usrid,"semua");
		if(!is_object($sal)){
			$this->db->insert("saldo",["usrid"=>$st->usrid,"saldo"=>0,"apdet"=>date("Y-m-d H:i:s")]);
			$sal = $this->admfunc->getSaldo($this->db->insert_id(),"semua","id");
		}
		$tgl = date("Y-m-d H:i:s");
		$total = $st->total + $sal->saldo;

		$data2 = [
			"tgl"	=> $tgl,
			"usrid"	=> $st->usrid,
			"jenis"	=> 1,
			"jumlah"=> $st->total,
			"darike"=> 1,
			"sambung"	=> $st->id,
			"saldoawal"	=> $sal->saldo,
			"saldoakhir"=> $total
		];
		$this->db->insert("saldohistory",$data2);

		$this->db->where("id",$st->id);
		$this->db->update("saldotarik",["status"=>1,"selesai"=>$tgl]);

		if($sal->id > 0){
			$this->db->where("id",$sal->id);
			$this->db->update("saldo",["apdet"=>$tgl,"saldo"=>$total]);
		}else{
			$this->db->insert("saldo",["usrid"=>$st->usrid,"saldo"=>$total,"apdet"=>date("Y-m-d H:i:s")]);
		}
						
		$this->admfunc->notifTopup($st->id);

		echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
	}
	public function bataltopup(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}

		$tgl = date("Y-m-d H:i:s");
		$this->db->where("id",$_POST["id"]);
		$this->db->update("saldotarik",["status"=>2,"selesai"=>$tgl]);

		echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
	}
	public function tarik(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/admin/saldolist",["jenis"=>1],true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}else{
			redirect("ngadimin");
		}
	}
	public function updatetarik(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		$tgl = date("Y-m-d H:i:s");

		$this->db->where("id",$_POST["id"]);
		$this->db->update("saldotarik",["status"=>1,"selesai"=>$tgl]);

		echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
	}
	public function bataltarik(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$st = $this->admfunc->getSaldotarik($_POST["id"],"semua");
		$sal = $this->admfunc->getSaldo($st->usrid,"semua");
		$tgl = date("Y-m-d H:i:s");
		$total = $st->total + $sal->saldo;
		$data = [
			"usrid"	=> $st->usrid,
			"trxid"	=> "TOP_".$st->usrid.date("YmdHis"),
			"jenis"	=> 2,
			"status"=> 1,
			"selesai"	=> $tgl,
			"tgl"	=> $tgl,
			"total"	=> $total,
			"metode"=> 1,
			"keterangan"=> "pengembalian dana karena penarikan ditolak oleh admin"
		];
		$this->db->insert("saldotarik",$data);
		$topup = $this->db->insert_id();

		$data2 = [
			"tgl"	=> $tgl,
			"usrid"	=> $st->usrid,
			"jenis"	=> 1,
			"jumlah"=> $st->total,
			"darike"=> 3,
			"sambung"	=> $topup,
			"saldoawal"	=> $sal->saldo,
			"saldoakhir"=> $total
		];
		$this->db->insert("saldohistory",$data2);

		$this->db->where("id",$st->id);
		$this->db->update("saldotarik",["status"=>2,"selesai"=>$tgl]);

		$this->db->where("id",$sal->id);
		$this->db->update("saldo",["apdet"=>$tgl,"saldo"=>$total]);

		echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
	}
	
	/* SALES BOSTER */
	public function booster(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/admin/boosterlist","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}elseif(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$this->db->where("id",intval($_POST["formid"]));
			$db = $this->db->get("booster");
			$data = [];
			foreach($db->result() as $r){
			$data = [
				"id"		=> $_POST["formid"],
				"usrid"		=> $r->usrid,
				"nama"		=> $r->nama,
				"idproduk"	=> $r->idproduk,
				"token"		=> $this->security->get_csrf_hash()
			];
			}
			echo json_encode($data);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambahbooster(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			$_POST["tgl"]	= date("Y-m-d H:i:s");
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("booster",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("booster",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapusbooster(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("booster");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	
	/* REKENING */
	public function rekening(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$this->load->view("atmin/admin/rekeninglist");
		}elseif(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$rek = $this->admfunc->getRekening(intval($_POST["formid"]),"semua");
			$data = [
				"id"		=> $_POST["formid"],
				"pass"		=> $this->admfunc->decode($rek->pass),
				"userid"	=> $rek->userid,
				"atasnama"	=> $rek->atasnama,
				"idbank"	=> $rek->idbank,
				"norek"		=> $rek->norek,
				"kcp"		=> $rek->kcp,
				"token"		=> $this->security->get_csrf_hash()
			];
			echo json_encode($data);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambahrekening(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			$_POST["usrid"]	= 0;
			$_POST["tgl"]	= date("Y-m-d H:i:s");
			if(isset($_POST["pass"])){
				$_POST["pass"]	=  $this->admfunc->encode($_POST["pass"]);
			};
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("rekening",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("rekening",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapusrekening(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		if(isset($_POST["id"])){
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("rekening");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	
	/* KURIR */
	public function kurir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/admin/paketkurirlist");
	}
	public function getkurir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$rek = $this->admfunc->getKurir(intval($_POST["id"]),"semua");
		$data = [
			"id"		=> $_POST["id"],
			"nama"		=> $rek->nama,
			"namalengkap"	=> $rek->namalengkap,
			"token"		=> $this->security->get_csrf_hash()
		];
		echo json_encode($data);
	}
	public function kurirsave(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			$_POST["jenis"]	= 2;
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("kurir",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("kurir",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapuskurir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		if(isset($_POST["id"])){
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("kurir");
			$this->db->where("idkurir",intval($_POST["id"]));
			$this->db->delete("paket");
			$this->db->where("kurir",intval($_POST["id"]));
			$this->db->delete("kurircustom");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}

	/* PAKET */
	public function getpaket(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$rek = $this->admfunc->getPaket(intval($_POST["id"]),"semua");
		$data = [
			"id"		=> $_POST["id"],
			"nama"		=> $rek->nama,
			"cod"		=> $rek->cod,
			"idkurir"	=> $rek->idkurir,
			"kurirjenis"=> $this->admfunc->getKurir($rek->idkurir,"jenis"),
			"keterangan"=> $rek->keterangan,
			"token"		=> $this->security->get_csrf_hash()
		];
		echo json_encode($data);
	}
	public function paketsave(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("paket",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("paket",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapuspaket(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("paket");
			$this->db->where("paket",intval($_POST["id"]));
			$this->db->delete("kurircustom");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}

	// PESANAN
	public function terimapesanan(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			$this->db->where("id",$_POST["id"]);
			if($this->db->update("transaksi",array("status"=>3,"tglupdate"=>date("Y-m-d H:i:s"),"selesai"=>date("Y-m-d H:i:s")))){
				$trx = $this->admfunc->getTransaksi($_POST["id"],"semua");
				$this->selesaiPesanan($trx->id);

				echo json_encode(array("success"=>true,"message"=>"Success!"));
			}else{
				echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
			}
		}else{
			echo json_encode(array("success"=>false,"message"=>"Forbidden Access"));
		}
	}
	
	// AMBIL DATA DOMISILI
	public function getkab($selec=null){
		$id = (isset($_POST["id"])) ? $_POST["id"] : 0;
		$this->db->where("idprov",$id);
		$this->db->order_by("tipe","DESC");
		$db = $this->db->get("kab");
		if($id == 0){
			$result = "<option value='0'>Nasional / Seluruh Indonesia</option>/n";
		}else{
			$result = "<option value=''>- Pilih Kabupaten/Kota -</option>/n";
		}
		foreach($db->result() as $res){
			$select = $selec == $res->id ? "selected" : "";
			$result .= "<option  value='".$res->id."' ".$select.">".$res->tipe." ".$res->nama."</option>/n";
		}
		echo json_encode(array("html"=>$result,"token"=> $this->security->get_csrf_hash()));
	}
	public function getkec($selec=null){
		$id = (isset($_POST["id"])) ? $_POST["id"] : 0;
		$this->db->where("idkab",$id);
		$db = $this->db->get("kec");
			$result = "<option value=''>- Pilih Kecamatan -</option>/n";
		foreach($db->result() as $res){
			$select = $selec == $res->id ? "selected" : "";
			$result .= "<option value='".$res->id."' ".$select.">".$res->nama."</option>/n";
		}
		echo json_encode(array("html"=>$result,"token"=> $this->security->get_csrf_hash()));
	}
	public function getpaketdrop($selec=null){
		$id = (isset($_POST["id"])) ? $_POST["id"] : 0;
		$this->db->where("idkurir",$id);
		$db = $this->db->get("paket");
			$result = "<option value=''>- Pilih Paket -</option>/n";
		foreach($db->result() as $res){
			$select = $selec == $res->id ? "selected" : "";
			$result .= "<option value='".$res->id."' ".$select.">".$res->nama."</option>/n";
		}
		echo json_encode(array("html"=>$result,"token"=> $this->security->get_csrf_hash()));
	}

	/* ONGKIR CUSTOM */
	public function ongkir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/admin/paketkurirongkir");
	}
	public function getongkir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$rek = $this->admfunc->getOngkir(intval($_POST["id"]),"semua");
		$data = [
			"id"	=> $_POST["id"],
			"kurir"	=> $rek->kurir,
			"paket"	=> $rek->paket,
			"prov"	=> $this->admfunc->getKab($rek->idkab,"idprov"),
			"kab"	=> $rek->idkab,
			"jenis"	=> $rek->jenis,
			"harga"	=> $rek->harga,
			"hargakm"	=> $rek->hargakm,
			"estimasi"	=> $rek->estimasi,
			"token"		=> $this->security->get_csrf_hash()
		];
		echo json_encode($data);
	}
	public function ongkirsave(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			$_POST["tgl"] = date("Y-m-d H:i:s");			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("kurircustom",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("kurircustom",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapusongkir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		if(isset($_POST["id"])){
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("kurircustom");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	
	/* WHATSAPP */
	public function wasap(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$this->load->view("atmin/admin/wasaplist");
		}elseif(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$rek = $this->admfunc->getWasap(intval($_POST["formid"]),"semua");
			$data = [
				"id"	=> $_POST["formid"],
				"nama"	=> $rek->nama,
				"wasap"	=> $rek->wasap,
				"token"	=> $this->security->get_csrf_hash()
			];
			echo json_encode($data);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambahwasap(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$_POST["tgl"]	= time();
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("wasap",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("wasap",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapuswasap(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}

		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("wasap");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	
	/* USER MANAJER */
	public function usermanajer(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if($this->admfunc->demo() == true){
			echo json_encode(["result"=>"maaf, fitur tidak tersedia untuk mode demo aplikasi","token"=>$this->security->get_csrf_hash()]);
		}else{
			if(isset($_POST["formid"])){
				//$_POST["formid"] = intval(["formid"]);
				$rek = $this->admfunc->getUser(intval($_POST["formid"]),"semua");
				$data = [
					"id"		=> $_POST["formid"],
					"pass"		=> $this->admfunc->decode($rek->password),
					"username"	=> $rek->username,
					"nama"		=> $rek->nama,
					"level"		=> $rek->level,
					"token"		=> $this->security->get_csrf_hash()
				];
				echo json_encode($data);
			}else{
				$res = $this->load->view("atmin/admin/userlist","",true);
				echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
			}
		}
	}
	public function hapususer(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}

		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("admin");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function tambahuser(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			if(isset($_POST[$this->security->get_csrf_token_name()])){ unset($_POST[$this->security->get_csrf_token_name()]); }
			//$_POST["id"] = intval(["id"]);
			if(isset($_POST["pass"])){
				$_POST["password"]	=  $this->admfunc->encode($_POST["pass"]);
				unset($_POST["pass"]);
			};
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("admin",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("admin",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}elseif(isset($_POST["gantipass"])){
			$set["password"] = $this->admfunc->encode($_POST["gantipass"]);
			
			$this->db->where("id",$_SESSION["admusrid"]);
			$this->db->update("admin",$set);
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	
	/* PENGATURAN */
	public function savesetting($hal=null){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_POST) AND $this->admfunc->demo() == false){
			foreach($_POST as $key=>$value){
				$key = $key;
				$this->db->where("field",$key);
				$this->db->update("setting",["value"=>$value]);
			}
			
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false,"msg"=>"not allowed"]);
		}
	}
	public function setting(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/admin/pengaturanform");
	}
	public function settingkurir(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/admin/pengaturankurir");
	}
	public function settingserver(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/admin/pengaturanserver");
	}
	public function settingpayment(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		$this->load->view("atmin/admin/pengaturanpayment");
	}
	
	/* PESANAN */
	public function pesanan(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
		if(isset($_GET['load']) AND $_GET['load'] == "bayar"){
			$res = $this->load->view("atmin/admin/pesananbayar","",true);
		}elseif(isset($_GET['load']) AND $_GET['load'] == "dikemas"){
			$res = $this->load->view("atmin/admin/pesanandikemas","",true);
		}elseif(isset($_GET['load']) AND $_GET['load'] == "dikirim"){
			$res = $this->load->view("atmin/admin/pesanandikirim","",true);
		}elseif(isset($_GET['load']) AND $_GET['load'] == "selesai"){
			$res = $this->load->view("atmin/admin/pesananselesai","",true);
		}elseif(isset($_GET['load']) AND $_GET['load'] == "batal"){
			$res = $this->load->view("atmin/admin/pesananbatal","",true);
		}else{
			redirect($this->func->admurl()."/manage");exit;
		}
		echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
	}
	
	function resetoken(){
		echo $this->security->get_csrf_hash();
	}
}
