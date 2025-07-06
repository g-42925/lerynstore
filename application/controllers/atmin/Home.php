<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}
		
        $set = $this->func->globalset('semua');
        $tipe = [
            1   => "Slider",
            2   => "Promo Iklan",
            3   => "Topup & Tagihan (PPOB)",
            4   => "Tombol Playstore",
            5   => "Flashsale",
            6   => "Grid Produk",
            7   => "Testimoni Pembeli",
            8   => "Blog",
            9   => "Brand List",
            10   => "Kategori List",
            11   => "Slider + PPOB",
        ];
		$this->load->view('atmin/admin/head',["menu"=>33]);
		$this->load->view('atmin/homepage/index',["set"=>$set,"tipe"=>$tipe]);
		$this->load->view('atmin/admin/foot');
	}
	
	function simpan($id=null){
		if(!isset($_SESSION["isMasok"])){
			redirect($this->func->admurl()."/manage/login");
			exit;
		}

        if($this->func->demo()){
			echo json_encode(array("success"=>false,"msg"=>"Mode demo terbatas, Anda tidak dapat mengubah pengaturan ini"));
            exit;
        }
		
		if(isset($_POST["elemen"])){
            $data = [];
            foreach($_POST["elemen"] as $val){
                $data[] = [
                    "warna" => (isset($_POST['warna'][$val])) ? $_POST['warna'][$val] : "#ffffff",
                    "judul" => (isset($_POST['judul'][$val])) ? $_POST['judul'][$val] : "",
                    "tipe"  => (isset($_POST['tipe'][$val])) ? $_POST['tipe'][$val] : 1,
                    "tags"  => (isset($_POST['tags'][$val])) ? $_POST['tags'][$val] : "",
                    "produk"=> (isset($_POST['produk'][$val])) ? $_POST['produk'][$val] : "",
                    "jenis" => (isset($_POST['jenis'][$val])) ? $_POST['jenis'][$val] : 1,
                    "kategori"=> (isset($_POST['kategori'][$val])) ? $_POST['kategori'][$val] : 0,
                    "brand" => (isset($_POST['brand'][$val])) ? $_POST['brand'][$val] : 0,
                    "jumlah" => (isset($_POST['jumlah'][$val])) ? $_POST['jumlah'][$val] : 0
                ];
            }
			$this->db->where("field","homepage");
			$this->db->update("setting",array("value"=>json_encode($data),"tgl"=>date("Y-m-d H:i:s")));

			echo json_encode(array("success"=>true,"msg"=>"Berhasil mengupdate kurir","token"=> $this->security->get_csrf_hash()));
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Forbidden!"));
		}
	}
}