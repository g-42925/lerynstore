<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ppob extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	// BELI PRODUK
	public function topup($id=null){
        if($this->func->cekLogin() == true){
            $this->load->view("ppob/topup",["id"=>$id]);
        }else{
            redirect("home/signin");
        }
	}
	public function getproduk(){
        if($this->func->cekLogin() == true){
            $id = (isset($_POST['id'])) ? $_POST['id'] : "Pulsa";
            $brand = (isset($_POST['brand'])) ? $_POST['brand'] : null;
            $this->load->view("ppob/produk",["id"=>$id,"brand"=>$brand]);
        }else{
            redirect("home/signin");
        }
	}
	public function tagihan($id){
        if($this->func->cekLogin() == true){
            $this->load->view("ppob/tagihan",["id"=>$id]);
        }else{
            redirect("home/signin");
        }
	}
	public function getagihan(){
        if($this->func->cekLogin() == true){
            $id = (isset($_POST['id'])) ? $_POST['id'] : "INDIHOME";
            $nomor = (isset($_POST['nomor'])) ? $_POST['nomor'] : "146528604875";
            $data = $this->dgf->cekTagihan($id,$nomor);
            $this->load->view("ppob/tagihandata",["data"=>$data]);
        }else{
            redirect("home/signin");
        }
	}

	// BELI PROSES
	public function bayarpesanan($invoice){
        if($this->func->cekLogin() == true){
            $data = $this->func->getTransaksiPPOB($invoice,'semua','invoice');
            $this->load->view("ppob/bayar",["data"=>$data]);
        }else{
            redirect("home/signin");
        }
	}
	public function prosestopup(){
        if($this->func->cekLogin() == true){
            $this->db->where("kode",$_POST["produk"]);
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
                            "usrid"	=> $_SESSION['usrid'],
                            "invoice"	=> $trx,
                            "idproduk"	=> $r->id,
                            "nomer"	=> $_POST["nomer"],
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
            redirect("home/signin");
        }
	}
	public function prosestagihan(){
        if($this->func->cekLogin() == true){
            $this->db->where("kode",$_POST["produk"]);
            $this->db->limit(1);
            $db = $this->db->get("ppob");
            $tag = $this->dgf->cekTagihan($_POST["produk"],$_POST["nomer"]);
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
                            "usrid"	=> $_SESSION['usrid'],
                            "invoice"	=> $trx,
                            "idproduk"	=> $r->id,
                            "nomer"	=> $_POST["nomer"],
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
                echo json_encode(array("success"=>false,"msg"=>"Produk sedang tidak tersedia, silahkan hubungi admin untuk kendala ini","sesihabis"=>false));
            }
        }else{
            redirect("home/signin");
        }
	}
	public function prosesbayar(){
        if($this->func->cekLogin() == true){
            $this->db->where("invoice",$_POST["invoice"]);
            $this->db->where("status",0);
            $this->db->limit(1);
            $db = $this->db->get("transaksi_ppob");
            if($db->num_rows() > 0){
                foreach($db->result() as $r){
                    $tgl = date("Y-m-d H:i:s");
                    $koin = 0;
                    $saldos = 0;
                    $bayar = $r->bayar;
                    $saldo = $this->func->getSaldo($_SESSION['usrid'],"semua","usrid",true);
                    if($_POST["koin"] > 0 && $saldo->koin > 0){
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
                        "usrid"	=> $_SESSION['usrid'],
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
            redirect("home/signin");
        }
	}

    // WEBHOOK
	function webhook(){
		$json = file_get_contents("php://input");
		$set = $this->func->globalset("semua");
		
		$callbackSignature = isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ? $_SERVER['HTTP_X_HUB_SIGNATURE'] : '';
		$signature = hash_hmac('sha1', $json, $set->digiflazz_secret);
        print_r($callbackSignature);echo "\n";
        print_r($signature);echo "\n";

        $jsons = $json;
        //$jsons .= "{callback: ".$callbackSignature."}";
        //$jsons .= "{signature: ".$signature."}";
        $this->db->insert("teshook",["tgl"=>date("Y-m-d H:i:s"),"raw"=>$jsons]);

		if( $callbackSignature != 'sha1='.$signature ) {
			echo json_encode(array("success"=>false,"msg"=>"Forbidden Access"));
			exit();
		}

		$data = json_decode($json);
        if(isset($data->data)){
            $res = $data->data;
            $tag = $this->func->getTransaksiPPOB($res->ref_id,'semua','invoice');
            $hasil = array();
            $hasil['raw_notif'] = $json;
            if($res->status == "Sukses"){
                $hasil['status'] = 2;
                $hasil['harga_beli'] = $res->price;
                $hasil['selesai'] = date("Y-m-d H:i:s");
            }elseif($res->status == "Gagal"){
                $sal = $this->func->getSaldo($tag->usrid,'semua','usrid',true);
                $saldo = $sal->saldo + $tag->saldo;
                $koin = $sal->koin + $tag->koin;
                $this->db->where("id",$sal->id);
                $this->db->update("saldo",["saldo"=>$saldo,"koin"=>$koin]);
                
                $this->db->where("sambung",$tag->id);
                $this->db->where("darike",5);
                $this->db->delete("saldohistory");

                $this->func->notifBatalPPOB($tag->id,"*produk sedang gangguan* dan saldo Anda telah kami kembalikan");

                $hasil['status'] = 3;
                $hasil['harga_beli'] = isset($res->price) ? $res->price : $tag->harga_beli;
                $hasil['keterangan'] = $res->message."<br/>Telegram: ".$res->tele."<br/>WA: ".$res->wa;
                $hasil['selesai'] = date("Y-m-d H:i:s");
            }

            if(isset($res->sn) && !empty($res->sn)){
                $prod = $this->func->getPPOB($tag->idproduk,'semua');
                $usr = $this->func->getUser($tag->usrid,'semua');
                $detail = array(
                    "Status"    => $res->status,
                    "SN"   => $res->sn
                );
                // JIKA PLN
                if($prod->kategori == "PLN" && $prod->cek == 0){
                    $sn = explode("/",$res->sn);
                    $detail = array(
                        "token" => $sn[0],
                        "nama"  => $sn[1],
                        "tipe meter"=> $sn[2]."/".$sn[3],
                        "kwh"   => $sn[4]
                    );

                    $pesan = "Halo *".trim($usr->nama)."*\n";
                    $pesan .= "Pembelian token listrik Anda telah berhasil diproses, berikut kode tokennya:\n";
                    $pesan .= "*".trim($sn[0])."*";
                    $this->func->sendWA($usr->nohp,$pesan);
                }
                $hasil['detail'] = json_encode($detail);
            }

            $this->db->where("id",$tag->id);
            $this->db->update("transaksi_ppob",$hasil);

            echo json_encode(array("success"=>true,"msg"=>"data kami terima"));
        }else{
            echo json_encode(array("success"=>false,"msg"=>"Invalid parameter"));
        }
    }

    function cekPLN($no='14265265091'){
        //print_r($this->dgf->reqCurl('transaction','pln-subscribe',null,['customer_no'=>$no]));
        $inv = date("YmdHis");
        $data = [
            "buyer_sku_code"=> "PLN123",
            "customer_no"   => $no,
            "ref_id"        => $inv,
            "testing"       => false
        ];
        print_r($this->dgf->reqCurl('transaction',null,$inv,$data));
    }

}