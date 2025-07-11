<?php
    $this->db->where("id",$id);
    $db = $this->db->get("blog");

    if(isset($_SESSION["fotoPage"])){
        unlink("cdn/uploads/".$_SESSION["fotoPage"]);
        $this->session->unset_userdata("fotoPage");
    }

    if($db->num_rows() > 0){
        foreach($db->result() as $r){
            $judul = $r->judul;
            $foto  = base_url("cdn/uploads/".$r->img);
            $konten = $r->konten;
        }
    }else{
        $judul = "";
        $foto  = base_url("cdn/uploads/no-image.png");
        $konten = "";
    }
?>
<div class="m-b-60">
	<div class="card">
		<div class="card-body" id="load">
            <input type="file" name="logo" id="logoUpload" style="display:none;" accept="image/x-png,image/gif,image/jpeg" ></input>
            <form id="pengaturan" method="POST" action="<?=site_url($this->func->admurl()."/manage/editblog/".$id)?>">
                <input type="hidden" name="<?=$this->security->get_csrf_token_name()?>" class="tokens" value="<?=$this->security->get_csrf_hash();?>" />
                <div class="row">
                    <div class="col-md-12 m-b-20">
                        <div class="form-group m-b-20">
                            <label>Judul Postingan</label>
                            <input type="text" name="nama" class="form-control" value="<?=$judul?>" />
                        </div>
                        <div class="form-group logoset col-md-6">
                            <div class="logo">
                                <img id="logo" src="<?=$foto?>" />
                                <button type="button" class="btn btn-secondary btn-block logouploadbtn" onclick="$('#logoUpload').trigger('click')"><i class="fas fa-sync"></i> ganti thumbnail</button>
                                <div class="progress progreslogo" style="display:none;">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Konten Blog</label>
                            <textarea class="form-control" id="summernote" name="konten"><?=$konten?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 m-b-20">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan</button>
                            <button type="reset" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Reset</button>
                            <button type="button" class="btn btn-danger" onclick="history.back()"><i class="fas fa-times"></i> Batal</button>
                        </div>
                    </div>
                </div>
            </form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		loadText();
		$("#logoUpload").change(function(){
			var formData = new FormData();
			$(".progreslogo").show();
			$(".logouploadbtn").hide();
			formData.append("foto", $(this).get(0).files[0]);
			formData.append($("#names").val(), $("#tokens").val());
			$.ajax( {
                url        : '<?php echo site_url($this->func->admurl()."/api/uploadblog/".$id); ?>',
                type       : 'POST',
                contentType: false,
                cache      : false,
                processData: false,
                data       : formData,
                xhr        : function ()
                {
                    var jqXHR = null;
                    if ( window.ActiveXObject ){
                        jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );
                    }else{
                        jqXHR = new window.XMLHttpRequest();
                    }
                    jqXHR.upload.addEventListener( "progress", function ( evt ){
                        if ( evt.lengthComputable ){
                            var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
                            $(".progreslogo .progress-bar").css("width", percentComplete+"%");
                            $(".progreslogo .progress-bar").attr("aria-valuenow", percentComplete);
                        }
                    }, false );
                    return jqXHR;
                },
                success    : function ( data )
                {
					$(".logouploadbtn").show("slow");
					$(".progreslogo").hide();
					var res = eval("("+data+")");
                    updateToken(res.token);
					if(res.success == true){
						$("#logo").attr("src","<?=base_url('cdn/uploads/')?>"+res.filename);
					}
                }
            } );
		});
	});

	function loadText(){
		$('textarea#summernote').summernote('destroy');
		$('textarea#summernote').summernote({
			height: "40vh",
			callbacks: {
				onImageUpload: function(image) {
					uploadImage(image[0]);
				},
				onMediaDelete : function(target) {
					deleteImage(target[0].src);
				}
			}
		});
	}
	function uploadImage(image) {
		var data = new FormData();
		data.append("image", image);
		$.ajax({
			url: "<?php echo site_url($this->func->admurl().'/editor/uploadimage')?>",
			cache: false,
			contentType: false,
			processData: false,
			data: data,
			type: "POST",
			success: function(url) {
				$('textarea#summernote').summernote("insertImage", url);
			},
			error: function(data) {
				console.log(data);
			}
		});
	}
	function deleteImage(src) {
		$.ajax({
			data: {src : src},
			type: "POST",
			url: "<?php echo site_url($this->func->admurl().'/editor/deleteimage')?>",
			cache: false,
			success: function(response) {
				console.log(response);
			}
		});
	}
</script>