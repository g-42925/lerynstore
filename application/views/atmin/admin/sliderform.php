<?php
    if($id != 0){
        $this->db->where("id",intval($id));
        $db = $this->db->get("promo");
        foreach($db->result() as $r){
        }
    }
?>
<form id="saveform" method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?=intval($id)?>" />
    <input type="hidden" class="tokens" name="<?=$this->security->get_csrf_token_name()?>" value="<?=$this->security->get_csrf_hash();?>" />
    <div class="row">
        <div class="col-md-12">
            <a class="float-right btn btn-danger" href="javascript:history.back()"><i class="la la-arrow-left"></i> Kembali</a>
            <?php if($id == 0){ ?>
			<h4 class="page-title">Tambah Promo Slider</h4>
			<?php }else{ ?>
			<h4 class="page-title">Edit Promo Slider</h4>
			<?php } ?>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Detail Promo</div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Judul Promo</label>
                        <input type="text" name="caption" class="form-control" value="<?php echo (isset($r->caption)) ? $this->func->clean($r->caption) : ""; ?>" />
                    </div>
                    <div class="form-group">
                        <label>Text Promo</label>
                        <textarea name="keterangan" class="form-control" rows=5><?php echo (isset($r->keterangan)) ? $this->func->clean($r->keterangan) : ""; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Tags Promo</label>
                        <select class="form-control m-b-8" name="tags" id="tags" required >
                            <option value="null">+ Tambah Baru</option>
                            <option value="popupbanner" <?=(isset($r->tags) AND $r->tags == "popupbanner") ? " selected" : ""?>>Popup Banner</option>
                            <?php
                                $this->db->where("tags !=","popupbanner");
                                $this->db->group_by('tags');
                                $tags = $this->db->get("promo");
                                foreach($tags->result() as $res){
                                    $sel = (isset($r->tags) AND $res->tags == $r->tags) ? "selected" : "";
                            ?>
                            <option value="<?=$res->tags?>" <?=$sel?>><?=$res->tags?></option>
                            <?php } ?>
						</select>
                        <input type="text" name="tagsbaru" id="tagsbaru" class="form-control" placeholder="Ketik tags baru" value="" <?=(isset($r->id) AND $r->id > 0) ? "style='display:none'" : ""?> />
                    </div>
                    <div class="form-group">
                        <label>Link Promo</label>
                        <input type="text" name="link" class="form-control" value="<?php echo (isset($r->link)) ? strip_tags($r->link) : ""; ?>" />
                    </div>
                    <div class="form-group row m-lr-0">
                        <label class="col-12 p-lr-0">Tanggal Tayang Promo</label>
						<div class="col-md-6 p-l-0 p-r-5">
							<input type="text" name="tgl" class="form-control m-b-10 dtp" value="<?php echo (isset($r->tgl)) ? $r->tgl : ""; ?>" />
						</div>
						<div class="col-md-6 p-l-5 p-r-0">
							<input type="text" name="tgl_selesai" class="form-control m-b-10 dtp" value="<?php echo (isset($r->tgl_selesai)) ? $r->tgl_selesai : ""; ?>" />
						</div>
                    </div>
                    <div class="form-group">
                        <label>Status Promo</label>
                        <select class="form-control col-md-6" name="status" required >
							<option value="1"<?php echo (isset($r->status) AND $r->status == 1) ? " selected" : ""; ?>>AKTIF</option>
							<option value="0"<?php echo (isset($r->status) AND $r->status == 0) ? " selected" : ""; ?>>NON AKTIF</option>
						</select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Foto Display</div>
                </div>
                <div class="card-body">
                    <?php if(!isset($r->gambar)){ ?>
                        <input type='file' accept="image/*" name="gambar" id="imgInp" />
                        <a href="javascript:void(0)" class="btn btn-secondary" onclick="selectIMG()"><i class="la la-image"></i> Pilih Foto</a>
                        <div class="divider"></div>
                        <div class="imgInpPreview">
                            <div class="text">Pilih foto</div>
                            <img id="blah" class="imgpreview" src="#" alt="gambar" />
                            <div  class="delete">
                                <a href="javascript:void(0)" onclick="clearIMG()"><i class="la la-times"></i> hapus</a>
                            </div>
                        </div>
                    <?php 
                        }else{
                            echo "<img src='".base_url('cdn/promo/'.$r->gambar)."' class='imgPreview' />";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="">
        <button type="submit" class="btn btn-primary submit"><i class="la la-check-circle"></i> Simpan Promo</button>
        <button type="reset" class="btn btn-warning"><i class="la la-refresh"></i> Reset</button>
    </div>
</form>

<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
            $('#blah').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    function selectIMG(){
        $("#imgInp").trigger("click");
    }
    function clearIMG(){
        $("#imgInp").val(null).trigger("change");
    }

    $(function(){
		$(".dtp").datetimepicker({
			format: "YYYY-MM-DD HH:mm:ss"
		});
		$("#saveform").on("submit",function(){
			var btn = $(".submit").hmtl();
			$(".submit").hmtl("<i class='fas fa-spin fa-spinner'></i> Menyimpan...");
			$(".submit").prop("disabled",true);
		});
		
        $("#imgInp").change(function() {
            if($(this).val() != ""){
                readURL(this);
                $("#blah").show();
                $(".delete").show();
                $(".text").hide();
            }else{
                $("#blah").hide();
                $(".delete").hide();
                $(".text").show();
            }
        });

        $("#tagsbaru").on('input',function(){
            var val = $(this).val();
            if(val.indexOf(' ') >= 0){
                val = val.replace(/\s/g, "");
                $(this).val(val);
            }
        });

        $("#tags").change(function(){
            if($(this).val() == "null"){
                $("#tagsbaru").slideDown();
            }else{
                $("#tagsbaru").slideUp();
            }
        });
    });
</script>