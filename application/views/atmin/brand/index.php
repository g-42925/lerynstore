
<h4 class="page-title m-b-20">Data Brand</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row align-items-center">
			<div class="card-title col-md-8 m-b-10">
				<a href="javascript:tambah()" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Tambah Brand</a>
			</div>
			<div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control" onchange="load(1)" placeholder="cari data" id="cari" />
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info w-full" onclick="load(1)"><i class="fas fa-search"></i></button>
                    </div>
                </div>
			</div>
		</div>
		<div class="card-body" id="load">
			<i class="fas fa-spin fa-spinner"></i> Loading data...
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		load(1);
		
		$("#sbform").on("submit",function(e){
			e.preventDefault();
			swal.fire({
				text: "pastikan lagi data yang anda masukkan sudah sesuai",
				title: "Validasi data",
				type: "warning",
				showCancelButton: true,
				cancelButtonText: "Cek Lagi"
			}).then((vals)=>{
				if(vals.value){
					var formData = new FormData();
					$(".progress").show();
					$("#sbforms").hide();
					formData.append("foto", $("#imgInp").get(0).files[0]);
					formData.append("id", $("#id").val());
					formData.append("nama", $("#nama").val());
					$.ajax({
						url        : '<?php echo site_url($this->func->admurl()."/brand/tambah"); ?>',
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
									$(".progress-bar").css("width", percentComplete+"%");
									$(".progress-bar").attr("aria-valuenow", percentComplete);
								}
							}, false );
							return jqXHR;
						},
						success    : function ( data )
						{
							$("#sbforms").show("slow");
							$(".progress").hide();
							var res = eval("("+data+")");
							updateToken(res.token);
							$("#modal").modal("hide");
							swal.fire("Berhasil","Berhasil menyimpan data","success");
							load(1);
						}
					});
				}
			});
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
	});

    // FOTO UPLOAD
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
	
	function load(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/brand/data?load=true&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function edit(id){
        $(".modal-title").html("Edit Data Brand");
		$.post("<?=site_url($this->func->admurl()."/brand/data")?>",{"formid":id,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
            //$("#loadproduk").hide();
			$("#id").val(id);
			$("#status").val(data.status);
			$("#nama").val(data.nama);
		    //$('#sbform #foto').attr("required",false);
		    //$('#sbform #file').hide();
			$("#blah").attr("src","<?=base_url()?>cdn/brand/"+data.foto);
			$("#blah").show();
			$(".delete").show();
			$(".text").hide()
			
			$("#modal").modal();
		});
	}
	function tambah(){
        $(".modal-title").html("Tambah Brand");
        
		//$('#sbform #file').show();
		$('#sbform input').val("");
		$('#sbform #id').val("0");
		//$('#sbform #foto').attr("required",true);
		$("#imgInp").val("");
		$("#blah").hide();
        $(".delete").hide();
        $(".text").show();
		
		$("#modal").modal();
	}
	function hapus(id){
		swal.fire({
			text: "data yang sudah dihapus tidak dapat dikembalikan lagi",
			title: "Yakin menghapus data ini?",
			type: "warning",
			showCancelButton: true,
			cancelButtonColor: "#ff646d",
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url($this->func->admurl()."/brand/hapus")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						load(1);
						swal.fire("Berhasil","data sudah dihapus","success");
					}else{
						swal.fire("Gagal!","gagal menghapus data, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
	}
</script>

<div class="modal fade" id="modal" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">Tambah Brand</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="sbform">
					<input type="hidden" name="id" id="id" value="0" />
					<div class="form-group" id="file">
						<div id="inputfile">
							<input type='file' accept="image/*" name="icon" id="imgInp" />
							<div class="imgInpPreview pointer">
								<div class="text" onclick="selectIMG()">Pilih gambar</div>
								<img id="blah" class="imgpreview" src="#" alt="gambar" />
								<div class="delete">
									<a href="javascript:void(0)" onclick="clearIMG()"><i class="la la-times"></i> ganti gambar</a>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>Nama Brand</label>
						<input type="text" id="nama" name="nama" class="form-control" required />
					</div>
					<div class="form-group m-tb-10">
						<button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal" ><i class="fas fa-times"></i> Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>