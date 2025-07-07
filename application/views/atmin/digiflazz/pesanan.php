<h4 class="page-title">Pesanan PPOB</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row">
			<div class="tabs col-md-8">
				<a href="javascript:loadProses(1)" class="tabs-item active proses" data-item="proses">
					Proses
				</a>
				<a href="javascript:loadSelesai(1)" class="tabs-item selesai" data-item="selesai">
					Selesai
				</a>
				<a href="javascript:loadBatal(1)" class="tabs-item batal" data-item="batal">
					Batal
				</a>
			</div>
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" class="form-control" placeholder="cari pesanan" id="cari" />
					<div class="input-group-append">
						<button class="btn btn-info" type="button"><i class="fas fa-search"></i></button>
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
		loadProses(1);
		
		$(".tabs-item").on('click',function(){
			$(".tabs-item.active").removeClass("active");
			$(this).addClass("active");
		});

		$("#cari").change(function(){
			var load = $(".tabs-item.active").data("item");
			$.post("<?=site_url($this->func->admurl()."/ppob/pesananload")?>?page=1&load="+load,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				$("#load").html(data.result);
			});
		});
	});
	
	function loadProses(page){
		$(".tabs-item").removeClass("active");
		$(".proses").addClass("active");
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/ppob/pesananload?load=proses&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function loadSelesai(page){
		$(".tabs-item").removeClass("active");
		$(".selesai").addClass("active");
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/ppob/pesananload?load=selesai&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function loadBatal(page){
		$(".tabs-item").removeClass("active");
		$(".batal").addClass("active");
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/ppob/pesananload?load=batal&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function loadingDulu(){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Memproses data...');
	}
	function batalkan(id){
		swal.fire({
			text: "pesanan yang sudah dibatalkan tidak dapat dikembalikan lagi",
			title: "Yakin membatalkan pesanan ini?",
			type: "warning",
			showCancelButton: true,
			cancelButtonColor: "#ff646d",
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url($this->func->admurl()."/api/batalkanpesanan/bymin")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						loadBatal(1);
						swal.fire("Berhasil","pesanan sudah dibatalkan","success");
					}else{
						swal.fire("Gagal!","gagal membatalkan pesanan, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
	}
	function cekPesanan(id){
		$("#modalstatus .modal-body").html('<i class="fas fa-spin fa-compact-disc text-success"></i> &nbsp;Loading...');
		$("#modalstatus").modal();
		$("#modalstatus .modal-body").load("<?=site_url($this->func->admurl()."/ppob/status")?>/"+id);
	}
</script>
	
<div class="modal fade" id="modalstatus" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-medium"><i class="fa fa-receipt text-primary"></i> &nbsp;Status Pesanan PPOB</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<i class="fas fa-spin fa-compact-disc text-success"></i> &nbsp;Loading...
			</div>
		</div>
	</div>
</div>