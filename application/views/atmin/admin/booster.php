
<h4 class="page-title m-b-20">Daftar Sales Proof Custom</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row align-items-center">
			<div class="card-title col-md-6 m-b-10">
				<a href="javascript:tambahSB()" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Tambah SProof</a>
			</div>
			<div class="col-md-6">
				<div class="btn-group col-12" role="group">
					<?php 
						$set = $this->admfunc->globalset("notif_booster"); 
						$setasli = ($set == 3) ? "btn-success" : "btn-light";
						$setpalsu = ($set == 2) ? "btn-success" : "btn-light";
						$setaktif = ($set == 1) ? "btn-success" : "btn-light";
						$setnonaktif = ($set == 0) ? "btn-danger" : "btn-light";
					?>
					<button id="aktifasli" onclick="saveSetting(3)" type="button" style="border: 1px solid #bbb;" class="col-3 btn btn-sm <?=$setasli?>"><b>TRANSAKSI</b></button>
					<button id="aktifpalsu" onclick="saveSetting(2)" type="button" style="border: 1px solid #bbb;" class="col-3 btn btn-sm <?=$setpalsu?>"><b>LIST</b></button>
					<button id="aktifnotif" onclick="saveSetting(1)" type="button" style="border: 1px solid #bbb;" class="col-3 btn btn-sm <?=$setaktif?>"><b>AKTIF SEMUA</b></button>
					<button id="matinotif" onclick="saveSetting(0)" type="button" style="border: 1px solid #bbb;" class="col-3 btn btn-sm <?=$setnonaktif?>"><b>NON AKTIF</b></button>
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
		$('.selectto').select2({theme: "bootstrap",width:'resolve'});
		loadSB(1);
		
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
					var datar = $("#sbform").serialize();
					datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
					$.post("<?=site_url($this->func->admurl()."/api/tambahbooster")?>",datar,function(msg){
						var data = eval("("+msg+")");
						updateToken(data.token);
						if(data.success == true){
							loadSB(1);
							$("#modal").modal("hide");
							swal.fire("Berhasil","booster baru sudah tersimpan","success");
						}else{
							swal.fire("Gagal!","gagal menyimpan data, coba ulangi beberapa saat lagi","error");
						}
					});
				}
			});
		});
	});
	
	function saveSetting(val){
		<?php if($this->admfunc->demo()){ ?>
			swal.fire("Mode Demo Terbatas!","Maaf, Anda tidak dapat mengubah pengaturan saat mode demo","error");
		<?php }else{ ?>
			$(".btn-group button").removeClass("btn-success");
			$(".btn-group button").removeClass("btn-danger");
			$(".btn-group button").removeClass("btn-light");
			$.post("<?=site_url($this->func->admurl().'/api/savesetting')?>",{"notif_booster":val,[$("#names").val()]:$("#tokens").val()},function(ev){
				var data = eval("("+ev+")");
				updateToken(data.token);
				if(val == 1){
					$("#aktifnotif").addClass("btn-success");
					$("#matinotif").addClass("btn-light");
					$("#aktifpalsu").addClass("btn-light");
					$("#aktifasli").addClass("btn-light");
				}else if(val == 2){
					$("#aktifnotif").addClass("btn-light");
					$("#matinotif").addClass("btn-light");
					$("#aktifpalsu").addClass("btn-success");
					$("#aktifasli").addClass("btn-light");
				}else if(val == 3){
					$("#aktifnotif").addClass("btn-light");
					$("#matinotif").addClass("btn-light");
					$("#aktifpalsu").addClass("btn-light");
					$("#aktifasli").addClass("btn-success");
				}else{
					$("#aktifnotif").addClass("btn-light");
					$("#matinotif").addClass("btn-danger");
					$("#aktifpalsu").addClass("btn-light");
					$("#aktifasli").addClass("btn-light");
				}
			});
		<?php } ?>
	}
	function loadSB(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/api/booster?load=true&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function edit(id){
		$.post("<?=site_url($this->func->admurl().'/api/booster')?>",{"formid":id,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
			$("#id").val(id);
			
			$("#produk option").each(function(){
				if($(this).val() == data.idproduk){
					$(this).prop("selected",true);
				}else{
					$(this).prop("selected",false);
				}
			});
			$("#produk").trigger("change");
			$("#nama").val(data.nama);
			
			$("#modal").modal();
		});
	}
	function tambahSB(){
		$('#sbform')[0].reset();
		
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
				$.post("<?=site_url($this->func->admurl()."/api/hapusbooster")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						loadSB(1);
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
				<h6 class="modal-title">Edit Sales Proof</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="sbform">
					<input type="hidden" name="id" id="id" value="0" />
					<div class="form-group">
						<label>Nama Pengguna</label>
						<input id="nama" name="nama" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Produk</label>
						<select id="produk" name="idproduk" class="select2" required >
							<?php
								$this->db->where("status",1);
								$this->db->where("preorder",0);
								$this->db->order_by("nama","ASC");
								$dbs = $this->db->get("produk");
								foreach($dbs->result() as $rs){
									echo "<option value='".$rs->id."'>".$rs->nama."</option>";
								}
							?>
						</select>
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