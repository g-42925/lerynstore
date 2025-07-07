<div class="p-all-12" id="form">
    <input type="hidden" id="brand" value="" />
    <div class="form-group">
        <label>Produk Top Up</label>
        <select id="kategori" class="form-control" required>
            <option value="" disabled <?=($id == null) ? "selected" : ""?>>Pilih Produk</option>
            <?php
                $this->db->where("tipe",1);
                $db = $this->db->get("ppob_kategori");
                foreach($db->result() as $r){
                    $selec = ($id == $r->id) ? "selected" : "";
                    echo '<option value="'.$r->kode.'" '.$selec.'>'.$r->nama.'</option>';
                }
            ?>
        </select>
    </div>
    <div class="form-group m-b-20" id="games" style="display:none">
        <label>Pilih Game</label>
        <select id="brandgames" class="form-control">
            <option value="" selected>- Pilih -</option>
            <?php
                $this->db->where("tipe",1);
                $this->db->where("kategori","Games");
                $this->db->group_by("brand");
                $db = $this->db->get("ppob");
                foreach($db->result() as $r){
                    $selec = ($id == $r->id) ? "selected" : "";
                    echo '<option value="'.$r->brand.'" '.$selec.'>'.$r->brand.'</option>';
                }
            ?>
        </select>
    </div>
    <div class="form-group m-b-20" id="emoney" style="display:none">
        <label>Pilih E-Money</label>
        <select id="brandemoney" class="form-control">
            <option value="" selected>- Pilih -</option>
            <?php
                $this->db->where("tipe",1);
                $this->db->where("kategori","E-Money");
                $this->db->group_by("brand");
                $db = $this->db->get("ppob");
                foreach($db->result() as $r){
                    $selec = ($id == $r->id) ? "selected" : "";
                    echo '<option value="'.$r->brand.'" '.$selec.'>'.$r->brand.'</option>';
                }
            ?>
        </select>
    </div>
    <div class="form-group m-b-20" id="cek" style="display:none">
        <label>Masukkan Nomor / ID Tujuan</label>
        <input type="text" id="nomor" class="form-control" />
        <span class="text-danger" style="display:none" id="notiferor">masukkan Nomor/ID terlebih dahulu!</span>
    </div>
    <div id="produk">

    </div>
</div>
<div id="loading" class="p-tb-40 text-center" style="display:none">
    <i class="fas fa-spin fa-compact-disc fs-48 text-primary m-b-12"></i><br/>memproses pembelian, mohon tunggu sebentar...
</div>
<script type="text/javascript">
    $(function(){
        setKategori($("#kategori").val());
        
        $("#kategori").change(function(){
            setKategori($(this).val());
        });

        $("#brandgames").change(function(){
            $("#brand").val($(this).val());
            setTimeout(() => {
                getProduk();
            }, 500);
        });

        $("#brandemoney").change(function(){
            $("#brand").val($(this).val());
            setTimeout(() => {
                getProduk();
            }, 500);
        });

        $("#nomor").on("keyup keypress change click",function(){
            var nomor = $(this).val();
            if($.inArray($("#kategori").val(), ['Pulsa','Data']) >= 0){
                var catpulsa = [
                    [1, "Telkomsel", "telkomsel", "telkomsel.png", ["0811", "0812", "0813", "0821", "0822", "0823", "0852", "0853", "0851"]],
                    [2, "Indosat", "indosat", "indosat.png", ["0814", "0815", "0816", "0855", "0856", "0857", "0858"]],
                    [3, "XL", "xl", "xl.png", ["0817", "0818", "0819", "0859", "0877", "0878"]],
                    [4, "Tri", "tri", "tri.png", ["0895", "0896", "0897", "0898", "0899"]],
                    [5, "Axis", "axis", "axis.png", ["0838", "0831", "0832", "0833"]],
                    [6, "Smartfren", "smartfren", "smartfren.png", ["0881", "0882", "0883", "0884", "0885", "0886", "0887", "0888", "0889"]],
                    [7, "Bolt", "bolt", "bolt.png", ["9991", "9992", "9993", "9994", "9995", "9996", "9997", "9998"]]
                ];
                console.log('Pulsa dan Paket Data');
                if (catpulsa.length > 0 && nomor.length < 4){
                    $("#produk").html("");
                }
                if (catpulsa.length > 0 && nomor.length >= 4){
                    var nomors = nomor.substring(0,4);
                    $.each(catpulsa, function(ckey, cvalue) {
                        if ($.inArray(nomors, cvalue[4]) > -1) {
                            $("#produk").html('<div class="text-center"><i class="fas fa-spin fa-spinner"></i> Loading data...</div>');
                            $("#brand").val(cvalue[2]);
                            getProduk();
                        }
                    });
                }
            }
        })
    });

    function pilihProduk(id){
        if($("#kategori").val() != "Streaming"){
            if($("#nomor").val() == "" && $("#nomor").val().length <= 5){
                $("#notiferor").show();
                $("#nomor").focus();
                setTimeout(() => {
                    $("#notiferor").hide('slow');
                }, 4000);
                return false;
            }
        }

        $("#form").slideUp();
        $("#loading").show();
        //console.log("Produk kepilih");
        //swal.fire("Error","gagal memproses pesanan, silahkan hubungi admin atas kendala ini. kode error: 123","error");
        $.post("<?=site_url("ppob/prosestopup")?>",{"produk": id,"nomer":$("#nomor").val()},function(msg){
            var data = eval("("+msg+")");
            if(data.success){
                setTimeout(() => {
                    bayarPPOB(data.result);
                }, 4000);
            }else{
                $("#loading").slideUp();
                $("#form").show();
                swal.fire('Gagal memproses pembelian',data.msg,'error');
            }
        });
    }
    function setKategori(val){
        $("#brand").val("");
        $("#produk").html("");
        if(val == "Streaming"){
            $("#cek").hide();
            getProduk();
        }else{
            $("#cek").show();
        }

        if(val == "E-Money"){
            $("#emoney").show();
        }else{
            $("#emoney").hide();
        }

        if(val == "Games"){
            $("#games").show();
        }else{
            $("#games").hide();
        }

        if(val == "PLN"){
            getProduk();
        }
    }
    function getProduk(){
        $.post("<?=site_url("ppob/getproduk")?>",{"id":$("#kategori").val(),"brand":$("#brand").val()},function(msg){
            $("#produk").html(msg);
        });
    }
</script>