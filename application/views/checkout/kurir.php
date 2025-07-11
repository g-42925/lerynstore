<?php
    $hasil = [];
    $pre = $this->func->getPreBayar($_SESSION["prebayar"],"semua");
    $this->db->where("id",$pre->alamat);
    if(isset($_SESSION["usrid"])){
        $this->db->where("usrid",$_SESSION["usrid"]);
    }elseif(isset($_SESSION["usrid_temp"])){
        $this->db->where("usrid_temp",$_SESSION["usrid_temp"]);
    }else{
        $this->db->where("usrid","xzact");
    }
    $this->db->Limit(1);
    $db = $this->db->get("alamat");
    $berat = ($pre->berat > 0) ? $pre->berat : 1000;
    $hasil = array();
    $paketkurir = array();
    $kurirs = array();
    $kurirarr = array();
    $jarak_km = 1;
    foreach($db->result() as $r){
        $jarak_km = $r->jarak_km;
        //print_r($pre->dari.",".$berat.",".$r->idkec);exit;
        $seting = $this->func->getSetting("semua");
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
                    $res = $this->func->cekOngkir($pre->dari,$berat,$r->idkec,$rs->id,$re->id,$r->jarak_km);
                    //if($rs->rajaongkir == "jne" AND $re->rajaongkir == "REG"){ $reg = $res['harga']; }
                    if(isset($res['success']) AND $res['success'] == true){
                        $hasil[] = $res;
                    }
                }
            }
        }
    }

    $ongkir = $this->rajaongkir->getOngkir($pre->dari,$r->idkec,$berat,$kurirarr);
    if($ongkir->success){
        foreach($ongkir->data as $ong){
            $ong->kurir = ($ong->kurir == "J&T") ? "jnt" : $ong->kurir;
            //print_r($ong->kurir);exit;
            $this->db->where("idkurir",$kurirs[$ong->kurir]);
            $this->db->where("rajaongkir",$ong->service);
            $this->db->limit(1);
            $dbk = $this->db->get("paket");
            if($dbk->num_rows() > 0){
                foreach($dbk->result() as $paket){
                    $hasil[] = [
                        "kuririd"   => $kurirs[$ong->kurir],
                        "kurir"     => $ong->kurir,
                        "harga"     => $ong->harga,
                        "etd"     => $ong->etd,
                        "serviceid" => $paket->id,
                        "cod"       => $paket->cod
                    ];
                }
            }
        }
    }
    //print_r($ongkir);exit;
    
    $kurir = []; $paket = [];
    for($i=0; $i<count($hasil); $i++){
        $kurir[$hasil[$i]["kuririd"]] = $hasil[$i]["kurir"];
        $paket[$hasil[$i]["kuririd"]][$hasil[$i]["serviceid"]] = array(
            "harga" => $hasil[$i]["harga"],
            "cod" => $hasil[$i]["cod"],
            "etd" => $hasil[$i]["etd"]
        );
    }
    //print_r($kurir);
?>
<?php if($jarak_km=='0' || $jarak_km==''){ ?>
<div class="section p-all-24 m-b-20">
    <div class="font-medium m-b-20 fs-20">Alamat kamu tidak lengkap, silahkan edit atau tambahkan alamat baru. <a href="" class="btn btn-lg btn-primary">Kembali</a></div>
</div>
<?php }else{ ?>
<div class="section p-all-24 m-b-20">
    <div class="font-medium m-b-20 fs-20">Pilih Kurir Pengiriman</div>
    <div class="row">
        <?php foreach($kurir as $key => $val){ ?>
            <div class="col-md-2 col-6 kurir-pilih-atas">
                <div class="kurir-wrap kurir-select" data-kurir="<?=$key?>">
                    <i class="fas fa-check-circle"></i>
                    <?php if(file_exists(FCPATH."assets/images/kurir/".$val.".png")){ ?>
                        <img src="<?=base_url("assets/images/kurir/".$val.".png")?>" />
                    <?php }else{ ?>
                        <div class="col-12 font-medium"><?=strtoupper(strtolower($val))?></div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="section p-all-24 m-b-20">
    <div class="font-medium m-b-20 fs-20">Pilih Paket Pengiriman</div>
    <div class="text-danger m-b-20 pilihkurir">pilih kurir dulu</div>
    <?php foreach($paket as $key => $val){ ?>
        <div class="row paket-list" id="kur_<?=$key?>" style="display:none">
            <?php
                foreach($val as $k => $v){
                    $etd = (!empty($v["etd"]) AND intval($v["etd"]) > 0) ? intval($v["etd"]) : 1;
                    $etds = $etd+3;
            ?>
                <div class="col-md-4">
                    <div class="kurir-wrap paket-select" id="paket_<?=$k?>" data-paket="<?=$k?>">
                        <i class="fas fa-check-circle"></i>
                        <div class="font-medium"><?=$this->func->getPaket($k,"nama")?></div>
                        <div class="text-success">Ongkir Rp. <?=$this->func->formUang($v["harga"])?></div>
                        <?php if($v["etd"] > 0){ ?>
                        <div class="fs-13 m-b-8">Perkiraan sampai <?="<b>".date('d-m', strtotime('+'.$etd.' days', strtotime(date("Y-m-d"))))."</b> s/d <b>".date('d-m', strtotime('+'.$etds.' days', strtotime(date("Y-m-d"))))."</b>"?></div>
                        <?php } ?>
                        <?php if($v["cod"] > 0){ ?>
                        <div class="badge badge-warning badge-sm">bisa bayar ditempat <b>(COD)</b></div>
                        <?php } ?>
                    </div>
                </div>
            <?php        
                }
            ?>
        </div>
    <?php } ?>
</div>
<form id="lanjut" style="display:none">
    <input type="hidden" id="kurir" name="kurir" />
    <input type="hidden" id="paket" name="paket" />
    <input type="hidden" id="jidcode" name="jidcode" value="<?=$jarak_km;?>" />
    <div class="text-center">
        <button type="submit" class="btn btn-lg btn-primary">SELANJUTNYA &nbsp;<i class="fas fa-chevron-right"></i></button>
    </div>
</form>
<?php } ?>

<script type="text/javascript">
    $(function(){
        $(".kurir-select").click(function(){
            $(".kurir-select").removeClass("active");
            $(".paket-select").removeClass("active");
            $(this).addClass("active");
            var kurir = $(this).data("kurir");
            $("#kurir").val(kurir);
            $("#paket").val("0");
            $("#lanjut").hide();
            $(".paket-list").hide();
            $(".pilihkurir").hide();
            $("#kur_"+kurir).show();
        });
        $(".paket-select").click(function(){
            $(".paket-select").removeClass("active");
            $(this).addClass("active");
            var paket = $(this).data("paket");
            $("#paket").val(paket);
            $("#lanjut").show();
        });
        
        $("#lanjut").on("submit",function(e){
            e.preventDefault();
            $.post("<?=site_url("checkout/simpankurir")?>",$(this).serialize(),function(msg){
                var data = eval("("+msg+")");
                if(data.success == true){
                    loadBayar();
                }else{
                    swal.fire("Gagal Menyimpan","terjadi kesalahan saat menyimpan data kurir pilihan Anda. Silahkan ulangi beberapa saat lagi","warning");
                }
            });
        });
    });
</script>