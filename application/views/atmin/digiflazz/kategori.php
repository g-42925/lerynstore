<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<tr>
			<th scope="col">No</th>
			<th scope="col">icon</th>
			<th scope="col">Nama</th>
			<th scope="col">Kode</th>
			<th class="text-right" scope="col">Aksi</th>
		</tr>
	<?php
		$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
		$cari = (isset($_GET["cari"]) AND $_GET["cari"] != "") ? $_GET["cari"] : "";
		$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
		$perpage = 10;
		
		$where = "nama LIKE '%".$cari."%' OR kode LIKE '%".$cari."%'";
		$this->db->select("id");
		//$this->db->where("jenis",2);
		$this->db->where($where);
		$rows = $this->db->get("ppob_kategori");
		$rows = $rows->num_rows();
		
		//$this->db->where("jenis",2);
		//$this->db->order_by("status","ASC");
		//$this->db->order_by("tipe","ASC");
		$this->db->where($where);
		$this->db->order_by("nama","ASC");
		$this->db->limit($perpage,($page-1)*$perpage);
		$db = $this->db->get("ppob_kategori");
			
		if($rows > 0){
			$no = (($page-1)*$perpage)+1;
			$total = 0;
			foreach($db->result() as $r){
                $icon = ($r->icon) ? $r->icon : "default.png";
	?>
			<tr>
				<td><?=$no?></td>
				<td><img src="<?=base_url('cdn/ppob/'.$icon)?>" height="32px" /></td>
				<td><?=ucwords($r->nama)?></td>
				<td><?=$r->kode?></td>
				<td class="text-right">
					<button onclick="editKategori(<?=$r->id?>)" class="btn btn-xs btn-warning"><i class="fas fa-pencil-alt"></i> edit</button>
				</td>
			</tr>
	<?php	
				$no++;
			}
		}else{
			echo "<tr><td colspan=8 class='text-center text-danger'>Belum ada Kategori produk PPOB</td></tr>";
		}
	?>
	</table>

	<?=$this->admfunc->createPagination($rows,$page,$perpage,"loadKategori");?>
</div>

<script type="text/javascript">
    $(function(){
		$("#katform").on("submit",function(e){
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
					$("#katform").hide();
					formData.append("icon", $("#imgInpkat").get(0).files[0]);
					formData.append("id", $("#katid").val());
					formData.append("nama", $("#katnama").val());
					$.ajax({
						url        : '<?php echo site_url($this->func->admurl()."/ppob/savekategori"); ?>',
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
							$("#katform").show("slow");
							$(".progress").hide();
							var res = eval("("+data+")");
                            if(res.success == true){
                                $("#modalkat").modal("hide");
                                swal.fire("Berhasil","Berhasil menyimpan data","success");
                                setTimeout(() => {
                                    loadKategori(1);
                                }, 1000);
                            }else{
                                swal.fire("Gagal","Gagal menyimpan data","error");
                            }
						}
					});
				}
			});
		});
		
        $("#imgInpkat").change(function() {
            if($(this).val() != ""){
                readURLkat(this);
                $("#blahkat").show();
                $(".deletekat").show();
                $(".textkat").hide();
            }else{
                $("#blahkat").hide();
                $(".deletekat").hide();
                $(".textkat").show();
            }
        });
    });

	function editKategori(id){
		$.post("<?=site_url($this->func->admurl().'/ppob/getkategori')?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
            if(data.success == true){
                var datas = data.data;
                $('#katform')[0].reset();
                $('#katid').val(datas.id);
                $('#katnama').val(datas.nama);
                $("#imgInpkat").val("");
                $("#blahkat").hide();
                $(".deletekat").hide();
                $(".textkat").show();
                
                $("#modalkat").modal();
            }else{
                swal.fire("Error!","gagal memuat data, silahkan refresh dulu halaman ini","error");
            }
		});
	}

    // FOTO UPLOAD
    function readURLkat(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
            $('#blahkat').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    function selectIMGkat(){
        $("#imgInpkat").trigger("click");
    }
    function clearIMGkat(){
        $("#imgInpkat").val(null).trigger("change");
    }
</script>
<div class="modal fade" id="modalkat" tabindex="-1" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">Edit Kategori</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="katform">
					<input type="hidden" name="id" id="katid" value="0" />
					<div class="form-group">
						<label>Ganti icon/logo</label>
						<div id="inputfile">
							<input type='file' accept="image/*" name="icon" id="imgInpkat" style="position:fixed;top:-500px;" />
							<div class="imgInpPreview pointer">
								<div class="text textkat" onclick="selectIMGkat()">Pilih gambar</div>
								<img id="blah blahkat" class="imgpreview" src="#" alt="gambar" />
								<div class="delete deletekat">
									<a href="javascript:void(0)" onclick="clearIMGkat()"><i class="la la-times"></i> ganti gambar</a>
								</div>
							</div>
						</div>
                    </div>
					<div class="form-group">
						<label>Nama</label>
						<input type="text" id="katnama" class="form-control" required />
					</div>
					<div class="form-group m-tb-10">
						<button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal" ><i class="fas fa-times"></i> Batal</button>
					</div>
				</form>
				<div class="progress" style="display:none;">
					<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
					<div class="text-center m-t-12">menyimpan kategori</div>
				</div>
			</div>
		</div>
	</div>
</div>