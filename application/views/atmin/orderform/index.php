
<h4 class="page-title m-b-20">Form Order Produk</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row align-items-center">
			<div class="card-title col-md-8 m-b-10">
                <?php if($id == 0){ ?>
				<button class="btn btn-primary" onclick="$('#modal').modal()"><i class="fas fa-plus-circle"></i> Tambah Form Order</button>
                <?php }else{ ?>
				<a class="btn btn-primary"href="<?=site_url("atmin/orderform/edit/0/".$id)?>"><i class="fas fa-plus-circle"></i> Tambah Form Order</a>
                <?php } ?>
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
	});
	
	function load(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url("atmin/orderform/data?load=true&page=")?>"+page,{"cari":$("#cari").val(),"idpro":"<?=$id?>",[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
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
				$.post("<?=site_url("atmin/orderform/hapus")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
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
	function embed(id){
		$("#embed").modal();
	}
</script>

<div class="modal fade" id="modal" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">Tambah Form Order</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <div class="row">
                    <?php
                        $this->db->where("status",1);
                        $this->db->order_by("nama","ASC");
                        $dbs = $this->db->get("produk");
                        foreach($dbs->result() as $rs){
                    ?>
                        <div class="col-md-6">
                            <a class="w-full" href="<?=site_url("atmin/orderform/edit/0/".$rs->id)?>">
                                <div class="p-all-12 border radius-8 m-b-12">
                                <div class="row">
                                    <div class="col-3 text-center">
                                        <img src="<?=$this->func->getFoto($rs->id)?>" style="max-width:100%;max-height:40px" />
                                    </div>
                                    <div class="col-9">
                                        <div class="font-medium m-l--10"><?=$rs->nama?></div>
                                    </div>
                                </div>
                                </div>
                            </a>
                        </div>
                    <?php
                        }
                    ?>
                </div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="embed" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title"><i class="fas fa-code"></i> &nbsp;Embed Form</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<textarea rows="8" class="form-control"><!-- --></textarea>
			</div>
			<div class="modal-footer text-right">
				<button class="btn btn-primary">copy</button>
			</div>
		</div>
	</div>
</div>