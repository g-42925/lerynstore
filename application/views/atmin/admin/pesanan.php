<h4 class="page-title">Pesanan</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row">
			<div class="tabs col-md-8">
				<a href="javascript:loadBayar(1)" class="tabs-item active bayar" data-item="bayar">
					Belum Dibayar
				</a>
				<a href="javascript:loadDikemas(1)" class="tabs-item dikemas" data-item="dikemas">
					Perlu Dikirim
				</a>
				<a href="javascript:loadDikirim(1)" class="tabs-item dikirim" data-item="dikirim">
					Dikirim
				</a>
				<a href="javascript:loadSelesai(1)" class="tabs-item selesai" data-item="selesai">
					Selesai
				</a>
				<a href="javascript:loadBatal(1)" class="tabs-item batal" data-item="batal">
					Dibatalkan
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
		loadBayar(1);
		
		$(".tabs-item").on('click',function(){
			$(".tabs-item.active").removeClass("active");
			$(this).addClass("active");
		});
		
		$("#cetakInvoice").on("submit",function(e){
			e.preventDefault();
			window.open("<?=site_url($this->func->admurl()."/api/cetakInvoice")?>"+"?id="+$("#inv").val());
		});
		$("#cetakInvoiceThermal").on("submit",function(e){
			e.preventDefault();
			window.open("<?=site_url($this->func->admurl()."/api/cetakInvoice")?>"+"?type=thermal&id="+$("#inv").val());
		});
		
		$("#cari").change(function(){
			var load = $(".tabs-item.active").data("item");
			$.post("<?=site_url($this->func->admurl()."/api/pesanan")?>?page=1&load="+load,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				$("#load").html(data.result);
			});
		});
	});
	
	function loadBayar(page){
		$(".tabs-item").removeClass("active");
		$(".bayar").addClass("active");
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/api/pesanan?load=bayar&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function loadDikemas(page){
		$(".tabs-item").removeClass("active");
		$(".dikemas").addClass("active");
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/api/pesanan?load=dikemas&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function loadDikirim(page){
		$(".tabs-item").removeClass("active");
		$(".dikirim").addClass("active");
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/api/pesanan?load=dikirim&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function loadSelesai(page){
		$(".tabs-item").removeClass("active");
		$(".selesai").addClass("active");
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/api/pesanan?load=selesai&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function loadBatal(page){
		$(".tabs-item").removeClass("active");
		$(".batal").addClass("active");
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/api/pesanan?load=batal&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function loadingDulu(){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Memproses data...');
	}
	
	function detail(id){
		$("#modaldetail").modal();
		$("#detailoader").show();
		$("#detaiload").hide();
		$("#modaldetail .modal-body").load("<?=site_url($this->func->admurl().'/api/detailpesanan')?>?theid="+id,function(){
			$("#detailoader").hide();
			$("#detaiload").show();
			$("#inv").val(id);
		});
	}
	
	function cetak(inv){
		window.open("<?=site_url($this->func->admurl()."/api/cetakInvoice")?>"+"?id="+inv);
	}
	function cetakThermal(inv){
		window.open("<?=site_url($this->func->admurl()."/api/cetakInvoice")?>"+"?type=thermal&id="+inv);
	}
	function cetakLabel(id){
		$.post("<?=site_url($this->func->admurl()."/api/cetakLabel")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#loadLabel").html(data.result);
			printDiv("#loadLabel");
		});
	}
	function printDiv(divId) {
		var content = $(divId).html();
		var mywindow = window.open('', 'Print', 'width=auto');
		
		mywindow.document.write(content);

		mywindow.document.close();
		mywindow.focus()
		mywindow.print();
		mywindow.close();
	}
	function lacakPaket(orderid){
		$("#modalacak").modal();
		$("#lacakloader").show();
		$("#lacakload").hide();
		$("#modalacak #lacakload").load("<?=site_url($this->func->admurl().'/api/lacakiriman')?>?orderid="+orderid,function(){
			$("#lacakloader").hide();
			$("#lacakload").show();
		});
	}
	function selesai(id){
		swal.fire({
			text: "status pesanan akan diupdate ke selesai",
			title: "Yakin pesanan sudah selesai?",
			type: "warning",
			showCancelButton: true,
			cancelButtonColor: "#ff646d",
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url($this->func->admurl()."/api/terimapesanan")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						loadSelesai(1);
						swal.fire("Berhasil","data sudah di update","success");
					}else{
						swal.fire("Gagal!","gagal mengupdate data, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
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
</script>
<div id="loadInvoice" style="display:none">invoice goes here</div>
<div id="loadLabel" style="display:none">label goes here</div>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title"><i class="fas fa-plus"></i> Tambah Data</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="simpan" action="<?=site_url("ngadimin/tambahvariasi")?>" method="POST">
				<input type="hidden" id="id" name="id" value="0" />
				<input type="hidden" id="jenis" name="jenis" value="" />
				<div class="modal-body">
					<div class="form-group">
						<label>Nama</label>
						<input type="text" class="form-control" id="nama" name="nama" required />
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="submit" class="btn btn-success">Simpan</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modaldetail" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title"><i class="fas fa-boxes"></i> Detail Pesanan</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="detaiload"></div>
				<div id="detailoader"><i class="fas fa-spin fa-spinner"></i> Memuat, tunggu sebentar...</div>
			</div>
			<div class="modal-footer">
				<div class="row w-full">
					<div class="col-md-6">
						Cetak Invoice:
						<div class="m-b-10 showsmall"></div>
					</div>
					<div class="col-md-6 row m-lr-0">
						<div class="col-md-6">
							<form id="cetakInvoice">
								<input type="hidden" id="inv" name="inv" name="invoice" />
								<button class="btn btn-sm btn-secondary w-full" type="submit"><i class="fas fa-print"></i> Printer A4/F4</button>
							</form>
						</div>
						<div class="col-md-6">
							<form id="cetakInvoiceThermal">
								<input type="hidden" id="inv" name="inv" name="invoice" />
								<button class="btn btn-sm btn-secondary w-full" type="submit"><i class="fas fa-print"></i> Printer Thermal</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalacak" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title"><i class="fas fa-shipping-fast"></i> Lacak Paket Pesanan</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="lacakload"></div>
				<div id="lacakloader"><i class="fas fa-spin fa-spinner"></i> Memuat, tunggu sebentar...</div>
			</div>
		</div>
	</div>
</div>