<?php
	$set = $this->func->globalset("semua");
	$read = ($this->func->demo() == true) ? "readonly" : "";
    //rehuxegxNe9D
    //dev-6c794e90-681c-11ee-98de-4333c4c2bce0
?>
<form id="pengaturan">
    <div class="m-b-20">
        <div class="form-group titel" style="font-weight: bold;">
            FITUR PPOB DIGIFLAZZ
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group g-otp col-12 m-lr-0 form-group m-b-10 col-md-8 m-b-30" role="group">
                    <?php 
                        $setaktif = ($set->digiflazz == 1) ? "btn-success" : "btn-light";
                        $setnonaktif = ($set->digiflazz == 0) ? "btn-danger" : "btn-light";
                    ?>
                    <button id="aktifotp" onclick="saveDigi(1)" type="button" style="border: 1px solid #bbb;" class="col-6 btn btn-sm <?=$setaktif?>"><b>AKTIF</b></button>
                    <button id="aktifmanual" onclick="saveDigi(0)" type="button" style="border: 1px solid #bbb;" class="col-6 btn btn-sm <?=$setnonaktif?>"><b>NONAKTIF</b></button>
                </div>
            </div>
            <div class="col-md-6">
                <button onclick="cekSaldo()" type="button" class="btn btn-success"><i class="fas fa-wallet"></i> &nbsp;Cek Saldo</button>
                <button id="sinkron" type="button" class="btn btn-primary"><i class="fas fa-sync-alt"></i> &nbsp;Sinkronisasi Produk</button>
                <div class="w-full"><i class="text-danger">lakukan sinkronisasi secara berkala untuk mengupdate data produk dari digiflazz</i></div>
            </div>
        </div>
        <div class="form-group titel m-t-20" style="font-weight: bold;">
            PENGATURAN API
        </div>
        <div class="form-group">
            <label>Username</label>
            <?php if($this->func->demo() == true){ ?>
            <input type="text" class="form-control" value="abcdefghijklmnopqrstuvwxyz" <?=$read?> />
            <?php }else{ ?>
            <input type="text" name="digiflazz_username" class="form-control" value="<?=$set->digiflazz_username?>" />
            <?php } ?>
        </div>
        <div class="form-group">
            <label>API Key</label>
            <?php if($this->func->demo() == true){ ?>
            <input type="text" class="form-control" value="abcdefghijklmnopqrstuvwxyz1234567890" <?=$read?> />
            <?php }else{ ?>
            <input type="text" name="digiflazz_apikey" class="form-control" value="<?=$set->digiflazz_apikey?>" />
            <?php } ?>
        </div>
        <div class="form-group">
            <label>Webhook Secret Code</label>
            <?php if($this->func->demo() == true){ ?>
            <input type="text" class="form-control" value="abcdefghijklmnopqrstuvwxyz1234567890" <?=$read?> />
            <?php }else{ ?>
            <input type="text" name="digiflazz_secret" class="form-control" value="<?=$set->digiflazz_secret?>" />
            <?php } ?>
        </div>
        <div class="form-group">
            <label>Penambahan di Harga Jual</label>
            <?php if($this->func->demo() == true){ ?>
            <input type="text" class="form-control col-md-4" value="500" <?=$read?> />
            <?php }else{ ?>
            <input type="text" name="digiflazz_untung" class="form-control col-md-4" value="<?=$set->digiflazz_untung?>" />
            <?php } ?>
        </div>
        <div class="form-group titel m-t-20" style="font-weight: bold;">
            ID/NAME/DETAIL CHECKER
        </div>
        <div class="p-tb-8 p-lr-12"><i>pengaturan produk khusus untuk cek detail topup (nama/username/detail lainnya)</i></div>
        <div class="row">
            <div class="col-md-4 p-tb-6">
                <div class="form-group">
                    <label>Cek Pelanggan PLN</label>
                    <select name="digiflazz_cekpln" class="form-control">
                        <option value='' disabled <?php if($set->digiflazz_cekpln == 0){ echo 'selected'; } ?>>Belum diatur</option>
                        <?php
                            $this->db->where("brand","PLN");
                            $this->db->where("tipe",1);
                            $db = $this->db->get("ppob");
                            foreach($db->result() as $r){
                                $selec = ($set->digiflazz_cekpln == $r->id) ? "selected" : "";
                                echo "<option value='".$r->id."' ".$selec.">".$r->kode." - ".$r->nama."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 p-tb-6">
                <div class="form-group">
                    <label>Cek Pengguna DANA</label>
                    <select name="digiflazz_cekdana" class="form-control">
                        <option value='' disabled <?php if($set->digiflazz_cekdana == 0){ echo 'selected'; } ?>>Belum diatur</option>
                        <?php
                            $this->db->where("brand","DANA");
                            $this->db->where("tipe",1);
                            $db = $this->db->get("ppob");
                            foreach($db->result() as $r){
                                $selec = ($set->digiflazz_cekdana == $r->id) ? "selected" : "";
                                echo "<option value='".$r->id."' ".$selec.">".$r->kode." - ".$r->nama."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 p-tb-6">
                <div class="form-group">
                    <label>Cek Pengguna OVO</label>
                    <select name="digiflazz_cekovo" class="form-control">
                        <option value='' disabled <?php if($set->digiflazz_cekovo == 0){ echo 'selected'; } ?>>Belum diatur</option>
                        <?php
                            $this->db->where("brand","OVO");
                            $this->db->where("tipe",1);
                            $db = $this->db->get("ppob");
                            foreach($db->result() as $r){
                                $selec = ($set->digiflazz_cekovo == $r->id) ? "selected" : "";
                                echo "<option value='".$r->id."' ".$selec.">".$r->kode." - ".$r->nama."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 p-tb-6">
                <div class="form-group">
                    <label>Cek Pengguna GOPAY</label>
                    <select name="digiflazz_cekgopay" class="form-control">
                        <option value='' disabled <?php if($set->digiflazz_cekgopay == 0){ echo 'selected'; } ?>>Belum diatur</option>
                        <?php
                            $this->db->where("brand","GO PAY");
                            $this->db->where("tipe",1);
                            $db = $this->db->get("ppob");
                            foreach($db->result() as $r){
                                $selec = ($set->digiflazz_cekgopay == $r->id) ? "selected" : "";
                                echo "<option value='".$r->id."' ".$selec.">".$r->kode." - ".$r->nama."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 p-tb-6">
                <div class="form-group">
                    <label>Cek Pengguna SHOPEEPAY</label>
                    <select name="digiflazz_cekshopeepay" class="form-control">
                        <option value='' disabled <?php if($set->digiflazz_cekshopeepay == 0){ echo 'selected'; } ?>>Belum diatur</option>
                        <?php
                            $this->db->where("brand","SHOPEE PAY");
                            $this->db->where("tipe",1);
                            $db = $this->db->get("ppob");
                            foreach($db->result() as $r){
                                $selec = ($set->digiflazz_cekshopeepay == $r->id) ? "selected" : "";
                                echo "<option value='".$r->id."' ".$selec.">".$r->kode." - ".$r->nama."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 p-tb-6">
                <div class="form-group">
                    <label>Cek Pengguna LINKAJA</label>
                    <select name="digiflazz_ceklinkaja" class="form-control">
                        <option value='' disabled <?php if($set->digiflazz_ceklinkaja == 0){ echo 'selected'; } ?>>Belum diatur</option>
                        <?php
                            $this->db->where("brand","LinkAja");
                            $this->db->where("tipe",1);
                            $db = $this->db->get("ppob");
                            foreach($db->result() as $r){
                                $selec = ($set->digiflazz_ceklinkaja == $r->id) ? "selected" : "";
                                echo "<option value='".$r->id."' ".$selec.">".$r->kode." - ".$r->nama."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 p-tb-6">
                <div class="form-group">
                    <label>Cek Pengguna I.SAKU</label>
                    <select name="digiflazz_cekisaku" class="form-control">
                        <option value='' disabled <?php if($set->digiflazz_cekisaku == 0){ echo 'selected'; } ?>>Belum diatur</option>
                        <?php
                            $this->db->where("brand","I.SAKU");
                            $this->db->where("tipe",1);
                            $db = $this->db->get("ppob");
                            foreach($db->result() as $r){
                                $selec = ($set->digiflazz_cekisaku == $r->id) ? "selected" : "";
                                echo "<option value='".$r->id."' ".$selec.">".$r->kode." - ".$r->nama."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 p-tb-6">
                <div class="form-group">
                    <label>Cek FREE FIRE</label>
                    <select name="digiflazz_cekfreefire" class="form-control">
                        <option value='' disabled <?php if($set->digiflazz_cekfreefire == 0){ echo 'selected'; } ?>>Belum diatur</option>
                        <?php
                            $this->db->where("brand","FREE FIRE");
                            $this->db->where("tipe",1);
                            $db = $this->db->get("ppob");
                            foreach($db->result() as $r){
                                $selec = ($set->digiflazz_cekfreefire == $r->id) ? "selected" : "";
                                echo "<option value='".$r->id."' ".$selec.">".$r->kode." - ".$r->nama."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
	</div>
    <div class="m-b-20">
        <div class="form-group">
            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan</button>
            <button type="reset" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Reset</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(function(){
        $("#pengaturan").on("submit",function(e){
            e.preventDefault();
            <?php
                if($this->func->demo() == true){
                    echo 'swal.fire("Mode Demo Terbatas","maaf, fitur tidak tersedia untuk mode demo","error");';
                }else{
                    echo '
                    var datar = $(this).serialize();
                    datar = datar +  "&" + $("#names").val() + "=" + $("#tokens").val();
                    $.post("'.site_url($this->func->admurl()."/api/savesetting").'",datar,function(msg){
                        var data = eval("("+msg+")");
                        updateToken(data.token);
                        if(data.success == true){
                            swal.fire("Berhasil","berhasil menyimpan pengaturan umum","success").then((val)=>{
                                loadSettingServer();
                            });
                        }else{
                            swal.fire("Gagal","gagal menyimpan pengaturan","error");
                        }
                    });';
                }
            ?>
        });
        
        $("#sinkron").click(function(){
            $("#modal").modal();
            $("#dgf").html("Menghubungi server...<br/>Get data produk prabayar...");
            //$("#dgf-result").load("<?=site_url($this->func->admurl()."/ppob/ceksaldo")?>");
            //if($(this).siblings().not('.fa-spin')){
                //$('.fas',this).addClass('fa-spin');
                //$(this).attr('disabled',true);
            //}
            sinkronPrabayar();
        });
    });
	function saveDigi(val){
        $(".g-otp button").removeClass("btn-success");
        $(".g-otp button").removeClass("btn-danger");
        $(".g-otp button").removeClass("btn-light");
        $.post("<?=site_url($this->func->admurl()."/api/savesetting")?>",{"digiflazz":val},function(ev){
            var data = eval("("+ev+")");
            updateToken(data.token);
            if(val == 1){
                $("#aktifotp").addClass("btn-success");
                $("#aktifmanual").addClass("btn-light");
            }else{
                $("#aktifotp").addClass("btn-light");
                $("#aktifmanual").addClass("btn-danger");
            }
        });
	}
    function cekSaldo(){
        $("#modal").modal();
        $("#dgf").html("Mengecek saldo Anda...");
        $("#dgf-result").load("<?=site_url($this->func->admurl().'/ppob/ceksaldo')?>");
    }
    function sinkronPrabayar(){
        //$("#dgf").html("Mengecek saldo Anda...");
        $("#dgf-result").html("");
        $.post("<?=site_url($this->func->admurl().'/ppob/sinkronprabayar')?>",{"digiflazz":"val"},function(ev){
            var datak = eval("("+ev+")");
            if(datak.success == true){
                $("#dgf-result").html("Berhasil mendapatkan data produk prabayar...<br/>");
                setTimeout(() => {
                    $("#dgf-result").html($("#dgf-result").html()+"Total produk: "+datak.produk+"<br/>");
                    setTimeout(() => {
                        $("#dgf-result").html($("#dgf-result").html()+"Total kategori produk: "+datak.kategori+"<br/>");
                        setTimeout(() => {
                            $("#dgf-result").html($("#dgf-result").html()+"Mengupdate database..."+"<br/>");
                            setTimeout(() => {
                                $("#dgf-result").html($("#dgf-result").html()+"Get data produk pascabayar..."+"<br/>");
                                sinkronPascabayar();
                            }, 1000);
                        }, 1000);
                    }, 1000);
                }, 1000);
            }else{
                $("#dgf-result").html("Gagal mendapatkan data produk prabayar. Status: Produk Kosong!... [END]<br/>");
            }
        });
    }
    function sinkronPascabayar(){
        //$("#dgf").html("Mengecek saldo Anda...");
        //$("#dgf-result").load("");
        $.post("<?=site_url($this->func->admurl().'/ppob/sinkronpasca')?>",{"digiflazz":"val"},function(ev){
            var datak = eval("("+ev+")");
            if(datak.success == true){
                $("#dgf-result").html($("#dgf-result").html()+"Berhasil mendapatkan data produk pascabayar...<br/>");
                setTimeout(() => {
                    $("#dgf-result").html($("#dgf-result").html()+"Total produk: "+datak.produk+"<br/>");
                    setTimeout(() => {
                        $("#dgf-result").html($("#dgf-result").html()+"Mengupdate database...<br/>");
                        setTimeout(() => {
                            $("#dgf-result").html($("#dgf-result").html()+"Sinkronisasi berhasil... [END]"+"<br/>");
                        }, 1000);
                    }, 1000);
                }, 1000);
            }else{
                $("#dgf-result").html($("#dgf-result").html()+"Gagal mendapatkan data produk pascabayar. Status: Produk Kosong!... [END]<br/>");
            }
        });
    }
</script>

<!-- scan Modal-->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Digiflazz API Console</h5>
            </div>
            <div class="modal-body">
                <div class="bg-dark text-white p-all-12">
                    <div class="bg-dark text-white" id="dgf"></div>
                    <div class="bg-dark text-white" id="dgf-result"></div>
                </div>
            </div>
        </div>
    </div>
</div>