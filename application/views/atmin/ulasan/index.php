
<h4 class="page-title m-b-20">Ulasan Pembeli</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row align-items-center">
			<div class="card-title col-md-8 m-b-10">
				<a href="javascript:tambah()" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Tambah Ulasan</a>
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
		<div class="card-body">
			<div class="m-b-12">
				<i class='fas fa-dice-one text-danger'></i> <i>ulasan buatan/fake</i> &nbsp;|&nbsp; <i class='fas fa-square text-success'></i> <i>Ulasan asli dari pembeli</i>
			</div>
			<div id="load">
				<i class="fas fa-spin fa-spinner"></i> Loading data...
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		load(1);
		
		$("#sbform").on("submit",function(e){
			e.preventDefault();
            $.post("<?=site_url($this->func->admurl()."/ulasan/tambah")?>",$(this).serialize(),function(msg){
                var data = eval("("+msg+")");
                updateToken(data.token);
                $("#modal").modal('hide');
                if(data.success == true){
                    load(1);
                    swal.fire("Berhasil","data berhasil disimpan","success");
                }else{
                    swal.fire("Gagal!","gagal menyimpan data, coba ulangi beberapa saat lagi","error");
                }
            });
		});
	});
	
	function load(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url($this->func->admurl()."/ulasan/data?load=true&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function edit(id){
        $(".modal-title").html("Edit Ulasan");
		$.post("<?=site_url($this->func->admurl().'/ulasan/data')?>",{"formid":id,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
            //$("#loadproduk").hide();
			$("#id").val(id);
			$("#nilai").val(data.nilai);
			$("#idproduk").val(data.idproduk);
			$("#moderasi").val(data.moderasi);
			$("#keterangan").val(data.keterangan);
			$("#usrid").val(data.usrid);
			$("#nama").val(data.nama);
			if(data.jenis == 1){
				//$('.static .select2').select2('readonly',false);
				$('.asli').hide();
				$('.asli select').attr('required',false);
				$('.palsu').show();
				$('.palsu .form-control').attr('required',true);
			}else{
				//$('.static .select2').select2('readonly',true);
				$('.asli').show();
				$('.asli select').attr('required',true);
				$('.palsu').hide();
				$('.palsu .form-control').attr('required',false);
			}
			
			$("#modal").modal();
		});
	}
	function tambah(){
        $(".modal-title").html("Tambah Ulasan Fake");
        
		//$('#sbform #file').show();
		$('#sbform input').val("");
		$('#sbform #id').val("0");
		
		//$('.static .select2').select2('readonly',false);
		$('.asli').hide();
		$('.asli select').attr('required',false);
		$('.palsu').show();
		$('.palsu .form-control').attr('required',true);
		
		$("#modal").modal();
	}
	function verifikasi(id){
		swal.fire({
			text: "ulasan akan ditampilkan di halaman produk",
			title: "Verifikasi data ini?",
			type: "warning",
			showCancelButton: true,
			cancelButtonColor: "#ff646d",
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url($this->func->admurl()."/ulasan/tambah")?>",{"id":id,"moderasi":1,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						load(1);
						swal.fire("Berhasil","data sudah diverifikasi","success");
					}else{
						swal.fire("Gagal!","gagal memverifikasi data, coba ulangi beberapa saat lagi","error");
					}
				});
			}
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
				$.post("<?=site_url($this->func->admurl()."/ulasan/hapus")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
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
					<div class="form-group static">
						<label>Produk</label>
						<select name="idproduk" id="idproduk" class="select2" required>
                            <?php
								$this->db->order_by('tglupdate','DESC');
                                $db = $this->db->get("produk");
                                foreach($db->result() as $r){
                            ?>
                                <option value="<?=$r->id?>"><?=$r->nama." | Rp. ".$this->func->formUang($r->harga)?></option>
                            <?php
                                }
                            ?>
                        </select>
					</div>
					<div class="form-group asli static">
						<label>User</label>
						<select name="usrid" id="usrid" class="select2" required>
                            <?php
								$this->db->order_by('tgl','DESC');
                                $db = $this->db->get("userdata");
                                foreach($db->result() as $r){
                            ?>
                                <option value="<?=$r->id?>"><?=$r->nama." | ".$r->nohp?></option>
                            <?php
                                }
                            ?>
                        </select>
					</div>
					<div class="form-group palsu">
						<label>Nama User</label>
						<input type="text" class="form-control" name="nama" id="nama" required>
					</div>
					<div class="form-group">
						<label>Nilai</label>
						<select name="nilai" id="nilai" class="form-control" required>
                            <option value="1">1 ★☆☆☆☆</option>
                            <option value="2">2 ★★☆☆☆</option>
                            <option value="3">3 ★★★☆☆</option>
                            <option value="4">4 ★★★★☆</option>
                            <option value="5">5 ★★★★★</option>
                        </select>
					</div>
					<div class="form-group">
						<label>Ulasan Detail</label>
						<textarea name="keterangan" id="keterangan" class="form-control" rows="5"></textarea>
					</div>
					<div class="form-group">
						<label>Status Moderasi</label>
						<select name="moderasi" id="moderasi" class="form-control" required>
                            <option value="1">Terverifikasi</option>
                            <option value="0">Pending</option>
                            <option value="2">Ditolak</option>
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