<h4 class="page-title">Pesan Masuk</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row">
			<!--
			<div class="tabs col-md-8 m-b-10">
				<a href="javascript:loadBaca(1)" class="tabs-item baca active" data-selector="baca">
					Belum Dibaca
				</a>
				<a href="javascript:loadSemua(1)" class="tabs-item semua" data-selector="semua">
					Semua Pesan
				</a>
			</div>
			-->
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" class="form-control" onchange="cariData()" placeholder="cari pesan" id="cari" />
					<div class="input-group-append">
						<button class="btn btn-sm btn-info w-full" onclick="cariData()"><i class="fas fa-search"></i></button>
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
		loadSemua(1);
		
		$(".tabs-item").on('click',function(){
			$(".tabs-item.active").removeClass("active");
			$(this).addClass("active");
		});
	});
	
	function loadSemua(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/manage/pesan?load=semua&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function openPesan(id,nama){
		$("#tujuan").val(id);
		$("#temp").val(id);
		$(".modal-title").html('<i class="fa fa-user"></i> '+nama);
		$("#modalpesan").modal();
		$("#pesan").html('<div class="pesanwrap center"><div class="isipesan"><i class="fas fa-spin fa-compact-disc"></i> memuat pesan...</div></div>');
		$( "#modalpesan" ).on('shown.bs.modal', function(){
			loadPesan(0);
			var setin = setInterval(() => {
				loadPesan(1);
			}, 10000);
			$( "#modalpesan" ).on('hidden.bs.modal', function(){
				clearInterval(setin);
				//loadSemua(1);
				$(".semua").trigger("click");
			});
		});
	}
	
	function loadPesan(nul){
		var id = $("#temp").val();
		$("#pesan").load("<?=site_url($this->func->admurl()."/api/pesanmasuk")?>/"+id,function(){
			if(nul == 0){
				$("#pesan").animate({ scrollTop: $("#pesan").prop('scrollHeight')}, 1000);
			}
		});
	}
	function cariData(){
		if($(".tabs-item.active").data("selector") == "baca"){
			loadBaca(1);
		}else{
			loadSemua(1);
		}
	}
</script>

<script type="text/javascript">
	$(function(){
		$("#kirimpesan").on("submit",function(e){
			e.preventDefault();
			var datar = $(this).serialize();
			datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
			$.post("<?=site_url($this->func->admurl()."/api/kirimpesan")?>",datar,function(s){
				var data = eval("("+s+")");
				updateToken(data.token);
				if(data.success == true){
					$("#isipesan").val("");
					$("#idproduk").val("");
					$("#tagproduk").hide("slow");
					$("#pesan").html('<div class="isipesan"><i class="fas fa-spin fa-compact-disc"></i> memuat pesan...</div>');
					loadPesan();
				}else{
					swal("GAGAL!","terjadi kendala saat mengirim pesan, coba ulangi beberapa saat lagi","error");
				}
			});
		});
	});
</script>
<input type="hidden" id="temp" style="display:none" />

	<div class="modal fade" id="modalpesan" tabindex="-1" role="dialog" style="background: rgba(0,0,0,.5);" style="bottom:0;right:0; aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title"><i class="fa fa-comments"></i> Live Chat</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body pesan" id="pesan">
					<div class="pesanwrap center">
						<div class="isipesan"><i class="fa fa-spin fa-spinner"></i> memuat pesan...</div>
					</div>
				</div>
				<form id="kirimpesan" method="POST">
					<input type="hidden" id="tujuan" name="tujuan" value="0" />
					<div class="modal-footer" style="display:block">
						<div class="m-b-12 w-full" id="tagproduk" style="display:none">
							<select class="select2" id="idproduk" name="idproduk">
								<option value="">Pilih Produk</option>
								<?php
									$this->db->order_by("nama","ASC");
									$db = $this->db->get("produk");
									foreach($db->result() as $r){
										echo "<option value='".$r->id."'>".$r->nama."</option>";
									}
								?>
							</select>
						</div>
						<div class="input-group w-full" style="margin-left:0;">
							<div class="input-group-prepend">
								<button type="button" class="btn btn-primary" onclick="$('#tagproduk').slideToggle()"><i class="fa fa-box"></i> PRODUK</button>
							</div>
							<input type="text" class="form-control" id="isipesan" placeholder="ketik pesan..." name="isipesan" />
							<div class="input-group-append">
								<button type="submit" id="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i> KIRIM</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>