<?php
if(!defined('BASEPATH')) exit('Hacking Attempt : Keluar dari sistem !! ');

class Rajaongkir_kurir extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    function getOngkir($dari,$tujuan,$berat,$kurir){
        $set = $this->func->globalset('semua');
        //$berat = ($berat >= 1000) ? round(($berat/1000),0) : 1;
        $kurir = implode(":",$kurir);
        $dari = $this->func->getKab($dari,'rajaongkir');
        $tujuan = $this->func->getKec($tujuan,'rajaongkir');
        
		$beratkg = round(intval($berat) / 1000,0,PHP_ROUND_HALF_DOWN);
		if($kurir == "jne"){
			$selisih = $berat - ($beratkg * 1000);
			if($selisih > 300){
				$beratkg = $beratkg + 1;
			}
		}elseif($kurir == "pos"){
			$selisih = $berat - ($beratkg * 1000);
			if($selisih > 200){
				$beratkg = $beratkg + 1;
			}
		}elseif($kurir == "tiki"){
			$selisih = $berat - ($beratkg * 1000);
			if($selisih > 299){
				$beratkg = $beratkg + 1;
			}
		}else{
			$selisih = $berat - ($beratkg * 1000);
			if($selisih > 0){
				$beratkg = $beratkg + 1;
			}
		}
		$beratkg = ($beratkg < 1) ? 1 : $beratkg;
        //print_r("origin=".$dari."&originType=city&destination=".$tujuan."&destinationType=subdistrict&weight=".$berat."&courier=".$kurir);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=".$dari."&originType=city&destination=".$tujuan."&destinationType=subdistrict&weight=".$berat."&courier=".$kurir,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: ".$set->rajaongkir
            ),
        ));

        $response = curl_exec($curl);
        //print_r($response);exit;
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
			$hasil = array("success"=>false,"response"=>"Error","err"=>$err);
        } else {
			$arr = json_decode($response);
            $has = [];
			$hasil = array("success"=>false,"response"=>"Error","err"=>$err);
            //print_r($response);exit;
			if(isset($arr->rajaongkir->status->code) AND $arr->rajaongkir->status->code == "200"){
				foreach($arr->rajaongkir->results as $r){
                    foreach($r->costs as $res){
                        $hargakg = $res->cost[0]->value / $beratkg;
                        $service = $res->service;
                        $etd = $res->cost[0]->etd;
                        $etd = ($etd != "") ? $etd : "0";
                        $has[] = (object)array(
                            "dari"		=> $dari,
                            "tujuan"	=> $tujuan,
                            "kurir"		=> $r->code,
                            "service"	=> $service,
                            "harga"		=> $res->cost[0]->value,
                            "harga_perkg" => $hargakg,
                            "etd"		=> $etd,
                            "update"	=> date("Y-m-d H:i:s")
                        );
                    }
                }
			    $hasil = array("success"=>true,"data"=>$has);
            }
        }
        return (object)$hasil;
    }
}