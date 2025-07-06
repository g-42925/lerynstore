<div class="p-all-12" id="form">
    <input type="hidden" id="brand" value="" />
    <div class="form-group">
        <label>Produk Top Up</label>
        <select id="kategori" class="form-control" required>
            <option value="" disabled <?=($id == null) ? "selected" : ""?>>Pilih Produk</option>
            <?php
                $this->db->where("tipe",2);
                $db = $this->db->get("ppob");
                foreach($db->result() as $r){
                    $selec = ($id == $r->id) ? "selected" : "";
                    echo '<option value="'.$r->kode.'" '.$selec.'>'.$r->nama.'</option>';
                }
            ?>
        </select>
    </div>
    <div class="form-group m-b-20" id="cek">
        <label>Masukkan Nomor / ID Tujuan</label>
        <div class="input-group mb-3">
            <input type="text" id="nomor" class="form-control" />
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" onclick="cekTagihan()">Cek Tagihan</button>
            </div>
        </div>
        <span class="text-danger" style="display:none" id="notiferor">masukkan Nomor/ID terlebih dahulu!</span>
    </div>
    <div id="produk">

    </div>
</div>
<div id="loading" class="p-tb-40 text-center" style="display:none">
    <i class="fas fa-spin fa-compact-disc fs-48 text-primary m-b-12"></i><br/>memproses pembayaran, mohon tunggu sebentar...
</div>
<script type="text/javascript">
    $(function(){
    });

    function cekTagihan(){
        if($("#nomor").val() == ""){
            $("#notiferor").show();
            $("#nomor").focus();
            setTimeout(() => {
                $("#notiferor").hide('slow');
            }, 4000);
            return false;
        }
        //swal.fire("Error","gagal memproses pesanan, silahkan hubungi admin atas kendala ini. kode error: 123","error");
        getProduk();
    }
    function getProduk(){
        $("#produk").html('<div class="p-t-24 text-center"><i class="fas fa-spin fa-compact-disc m-r-6 text-primary fs-16"></i>tunggu sebentar...</div>')
        $.post("<?=site_url("ppob/getagihan")?>",{"id":$("#kategori").val(),"nomor":$("#nomor").val()},function(msg){
            $("#produk").html(msg);
        });
    }
    function bayarTagihan(){
        $("#form").slideUp();
        $("#loading").show();
        //console.log("Produk kepilih");
        //swal.fire("Error","gagal memproses pesanan, silahkan hubungi admin atas kendala ini. kode error: 123","error");
        $.post("<?=site_url("ppob/prosestagihan")?>",{"produk": $("#kategori").val(),"nomer":$("#nomor").val()},function(msg){
            var data = eval("("+msg+")");
            if(data.success){
                bayarPPOB(data.result);
            }else{
                $("#loading").slideUp();
                $("#form").show();
                swal.fire('Gagal memproses pembelian',data.msg,'error');
            }
        });
    }
</script>