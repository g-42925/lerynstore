<h4 class="page-title">Pengaturan PPOB Digiflazz</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-md-9 p-tb-6">
					<div class="tabs p-lr-15 m-b-12">
						<a href="javascript:void(0)" onclick="loadProduk(1);$('.search').show();" class="tabs-item active">
							<i class="fas fa-box"></i> &nbsp;Produk Prabayar
						</a>
						<a href="javascript:void(0)" onclick="loadProdukPasca(1);$('.search').show();" class="tabs-item">
							<i class="fas fa-clock"></i> &nbsp;Produk Pascabayar
						</a>
						<a href="javascript:void(0)" onclick="loadKategori(1);$('.search').show();" class="tabs-item">
							<i class="fas fa-list"></i> &nbsp;Kategori Produk
						</a>
						<a href="javascript:void(0)" onclick="loadPengaturan();$('.search').hide();" class="tabs-item">
							<i class="fas fa-cog"></i> &nbsp;Pengaturan
						</a>
					</div>
				</div>
				<div class="col-md-3 p-tb-6">
					<div class="input-group search">
						<input type="text" class="form-control" onchange="$('.tabs-item.active').trigger('click')" placeholder="cari data" id="cari" />
						<div class="input-group-append">
							<button class="btn btn-sm btn-info w-full" onclick="$('.tabs-item.active').trigger('click')"><i class="fas fa-search"></i></button>
						</div>
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
		loadProduk(1);
		
		$(".tabs-item").on('click',function(){
			$(".tabs-item.active").removeClass("active");
			$(this).addClass("active");
		});

		$("#proform").on("submit",function(e){
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
					$("#proform").hide();
					formData.append("icon", $("#imgInp").get(0).files[0]);
					formData.append("id", $("#proid").val());
					formData.append("harga_jual", $("#proharga").val());
					$.ajax({
						url        : '<?php echo site_url($this->func->admurl()."/ppob/saveproduk"); ?>',
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
							$("#proform").show("slow");
							$(".progress").hide();
							var res = eval("("+data+")");
                            if(res.success == true){
                                $("#modal").modal("hide");
                                swal.fire("Berhasil","Berhasil menyimpan data","success");
                                if($("#protipe").val() == 1){
                                    loadProduk(1);
                                }else{
                                    loadProdukPasca(1);
                                }
                            }else{
                                swal.fire("Gagal","Gagal menyimpan data","error");
                            }
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
	function loadProduk(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$("#load").load("<?=site_url($this->func->admurl()."/ppob/produk?page=")?>"+page+"&cari="+encodeURI($("#cari").val()));
	}
	function loadProdukPasca(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$("#load").load("<?=site_url($this->func->admurl()."/ppob/produkpasca?page=")?>"+page+"&cari="+encodeURI($("#cari").val()));
	}
	function loadKategori(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$("#load").load("<?=site_url($this->func->admurl()."/ppob/kategori?page=")?>"+page+"&cari="+encodeURI($("#cari").val()));
	}
	function loadPengaturan(){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$("#load").load("<?=site_url($this->func->admurl().'/ppob/setting')?>");
	}
	function editProduk(id){
		$.post("<?=site_url($this->func->admurl().'/ppob/getproduk')?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
            if(data.success == true){
                var datas = data.data;
                $('#proform')[0].reset();
                $('#proid').val(datas.id);
                $('#pronama').val(datas.nama);
                $('#proharga').val(datas.harga_jual);
                $('#prohb').val(datas.harga_beli);
                $('#proadm').val(datas.biaya_admin);
                $('#prokomisi').val(datas.komisi);
                $('#protipe').val(datas.tipe);
                $("#imgInp").val("");
                $("#blah").hide();
                $(".delete").hide();
                $(".text").show();
                if(datas.tipe == 1){
                    $(".pasca").hide();
                    $(".pra").show();
                }else{
                    $(".pasca").show();
                    $(".pra").hide();
                }
                
                $("#modal").modal();
            }else{
                swal.fire("Error!","gagal memuat data produk, silahkan refresh dulu halaman ini","error");
            }
		});
	}

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
</script>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">Edit Produk</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="proform">
					<input type="hidden" name="id" id="proid" value="0" />
					<input type="hidden" id="protipe" value="1" />
					<div class="form-group">
						<label>Ganti icon/logo</label>
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
						<label>Nama</label>
						<input type="text" id="pronama" class="form-control" readonly/>
					</div>
					<div class="form-group pra">
						<label>Harga Beli</label>
						<input type="text" id="prohb" class="form-control" readonly/>
					</div>
					<div class="form-group pra">
						<label>Harga Jual</label>
						<input type="text" id="proharga" name="harga_jual" class="form-control" required />
					</div>
					<div class="form-group pasca">
						<label>Biaya Admin</label>
						<input type="text" id="proadm" class="form-control" readonly/>
					</div>
					<div class="form-group pasca">
						<label>Komisi</label>
						<input type="text" id="prokomisi" class="form-control" readonly/>
					</div>
					<div class="form-group m-tb-10">
						<button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal" ><i class="fas fa-times"></i> Batal</button>
					</div>
				</form>
				<div class="progress" style="display:none;">
					<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
					<div class="text-center m-t-12">menyimpan produk</div>
				</div>
			</div>
		</div>
	</div>
</div>