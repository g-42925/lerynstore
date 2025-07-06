<?php
    $tipeco = (isset($_SESSION["usrid"])) ? 0 : 1;
    if($tipeco == 0){
        $this->db->where("usrid",$_SESSION["usrid"]);
    }else{
        $this->db->where("usrid_temp",$_SESSION["usrid_temp"]);
    }
    $this->db->order_by("status","DESC");
    $row = $this->db->get("alamat");
?>
<form id="alamat">
    <div class="row">
        <div class="col-md-8">
            <div class="section p-all-24 m-b-20">
                <input type="hidden" id="tujuan" value="" name="tujuan" />
                <div class="m-b-12 alamatform">
                <b>Alamat Pengiriman</b>
                </div>
                <div class="rs1-select2 rs2-select2 m-b-12 alamatform">
                <select class="js-select2 form-control" name="alamat" id="idalamat" required>
                    <option value="">- Pilih Alamat Tujuan -</option>
                    <option value="0">+ Tambah Alamat Baru</option>
                    <?php
                    foreach($row->result() as $al){
                        //RAJAONGKIR
                        $kec = $this->func->getKec($al->idkec,"semua");
                        $idkab = $kec->idkab;
                        $keckab = $kec->nama.", ".$this->func->getKab($idkab,"nama");
                        echo '<option value="'.$al->id.'" data-tujuan="'.$al->idkec.'">'.strtoupper(strtolower($al->judul.' - '.$al->nama)).' ('.$keckab.')</option>';
                    }
                    ?>
                </select>
                <div class="dropDownSelect2"></div>
                </div>
                <div class="m-b-12">
                <?php
                    foreach($row->result() as $als){
                    $kec = $this->func->getKec($al->idkec,"semua");
                    $idkab = $kec->idkab;
                    $kec = $kec->nama;
                    $kab = $this->func->getKab($idkab,"nama");
                    echo "
                        <div class='alamat section bg-foot p-tb-20 p-lr-24 m-t-20' id='alamat_".$als->id."' data-tujuan='".$al->idkec."' style='display:none;'>
                        <b class='text-info'>Nama Penerima:</b><br/>".strtoupper(strtolower($als->nama))."<br/>
                        <b class='text-info'>No HP:</b><br/>".$als->nohp."<br/>
                        <b class='text-info'>Alamat Lengkap:</b><br/>".strtoupper(strtolower($als->alamat."<br/>".$kec.", ".$kab))."<br/>KODEPOS ".$als->kodepos."
                        </div>
                    ";
                    }
                ?>
                </div>
                <div class="m-b-12 tambahalamat" style="display:none;">
                <b>Tambah Alamat Pengiriman</b>
                </div>
                <div class="tambahalamat" style="display:none;">
                    <div class="m-b-12 col-md-10 p-lr-0">
                        <label>Simpan Sebagai? ex: Alamat Rumah, Alamat Kantor, Dll</label>
                        <input class="form-control" type="text" name="judul" placeholder="">
                    </div>
                    <div class="m-b-12 col-md-8 p-lr-0">
                        <label>Nama Penerima</label>
                        <input class="form-control" type="text" name="nama" placeholder="">
                    </div>
                    <div class="m-b-12 col-md-6 p-lr-0">
                        <label>No Handphone Penerima</label>
                        <input class="form-control" type="text" name="nohp" placeholder="">
                    </div>
                    <div class="m-b-12">
                        <label>Alamat lengkap</label>
                        <textarea class="form-control" name="alamatbaru" placeholder=""></textarea>
                    </div>
                    <div class="m-b-12" id="kecparent">
                        <label>Kecamatan</label>
                        <select class="form-control" id="kec" name="idkec"></select>
                    </div>
                    <div class="m-b-12">
                        <div class="mb-3">
                            <input type="hidden" class="form-control btn-light" name="gl" placeholder="Garis Lintang" required="" readonly="" id="latitude_gl" />
                            <input type="hidden" class="form-control btn-light" name="gb" placeholder="Garis Bujur" required="" readonly="" id="longitude_gb" />
                            <input type="hidden" class="form-control btn-light" name="glgbkm" placeholder="Garis Bujur" required="" readonly="" id="jarak_km" />
                            <input id="addressmaploc" type="text" class="form-control" placeholder="Cari lokasi disini..." />
                        </div>
                        <div id="googleMap" style="width:100%;height:400px;"></div>
                    </div>
                    <div class="m-b-12 p-lr-0">
                        <label>Kode POS</label>
                        <input class="form-control col-md-4" type="number" name="kodepos" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="section p-all-24">
                <b>Dropship</b>
                <div class="dropship">
                    <div class="m-t-10">
                        <button type="button" id="nodrop" class="btn btn-primary m-r-10"><i class="fa fa-check-square"></i> Tidak Dropship</button>
                        <div class="showsmall m-t-10"></div>
                        <button type="button" id="yesdrop" class="btn btn-outline-primary"><i class="fa fa-check-square" style="display:none"></i> Dropship</button>
                    </div>
                    <div class="p-t-20" id="dropform" style="display:none;">
                        <div class="m-b-12">
                            <label class="m-b-4">Nama Pengirim</label>
                            <input type="text" name="dropship" class="form-control" placeholder="" />
                        </div>
                        <div class="m-b-12">
                            <label class="m-b-4">No Telepon</label>
                            <input type="text" name="dropshipnomer" class="form-control col-md-8" placeholder="" />
                        </div>
                        <div class="m-b-12">
                            <label class="m-b-4">Alamat</label>
                            <input type="text" name="dropshipalamat" class="form-control" placeholder="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center p-t-32">
        <button type="submit" class="btn btn-lg btn-primary">SELANJUTNYA &nbsp;<i class="fas fa-chevron-right"></i></button>
    </div>
</form>

<script type="text/javascript">
    $(function(){
        $("#idalamat").change(function(){
            var idalamat = $(this).val();
            var tujuan = $("#alamat_"+idalamat).data('tujuan');

            $(".alamat").hide();
            if($(this).val() == ""){
                $(".tambahalamat").hide();
                $(".tambahalamat input,.tambahalamat textarea").prop("required",false);
            }else if($(this).val() == 0){
                $(".tambahalamat").show();
                $(".tambahalamat input,.tambahalamat textarea").prop("required",true);
                if($("#kab").val() != ""){
                    $("#tujuan").val($("#kab").val());
                }
            }else if($(this).val() > 0){
                $("#alamat_"+idalamat).show();
                $(".tambahalamat").hide();
                $(".tambahalamat input,.tambahalamat textarea").prop("required",false);
            }
        });

        $("#alamat").on("submit",function(e){
            e.preventDefault();
            $.post("<?=site_url("checkout/simpanalamat")?>",$(this).serialize(),function(msg){
                var data = eval("("+msg+")");
                if(data.success == true){
                    loadKurir();
                }else{
                    swal.fire("Gagal Menyimpan Alamat","terjadi kesalahan saat menyimpan alamat Anda. Silahkan ulangi beberapa saat lagi","warning");
                }
            });
        });
		
		$("#nodrop").click(function(){
			$("#yesdrop").removeClass("btn-primary");
			$("#yesdrop").addClass("btn-outline-primary");
			$(this).removeClass("btn-outline-primary");
			$(this).addClass("btn-primary");
			$(".fa",this).show()
			$("#yesdrop .fa").hide();
			$("#dropform").hide();
			$("#dropform input").val("");
			$("#dropform input").prop("required",false);
		});
		$("#yesdrop").click(function(){
			$("#nodrop").removeClass("btn-primary");
			$("#nodrop").addClass("btn-outline-primary");
			$(this).removeClass("btn-outline-primary");
			$(this).addClass("btn-primary");
			$("#dropform").show();
			$(".fa",this).show()
			$("#nodrop .fa").hide();
			$("#dropform input").prop("required",true);
		});

        //LOAD KABUPATEN KOTA & KECAMATAN
        $('#kec').select2({
            dropdownParent: $("#kecparent"),
            placeholder: 'Ketik nama kecamatan atau kabupaten/kota',
            ajax: {
                url: '<?=site_url("checkout/getkec")?>',
                dataType: 'json',
                method: 'POST',
                delay: 250,
                processResults: function (data) {
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.nama,
                            id: item.id
                        }
                    })
                };
                },
                cache: true
            }
        });
    });
</script>