<style>
    .pac-container {z-index: 99999999999 !important;}
</style>
<!-- Container -->
	<div class="m-t-30">
		<div class="container m-b-75">
			<h3 class="text-primary font-bold m-b-30">Akun Saya</h3>
			<div class="row">
				<div class="col-md-3">
					<div class="dashboard-menu mb-3">
						<ul class="nav flex-column" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" href="javascript:void(0)" data-link="#saldo"><span><i class="fas fa-wallet"></i>Saldo <?php echo $this->func->globalset("nama"); ?> </span></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="javascript:void(0)" data-link="#rekening"><span><i class="fas fa-money-check"></i>Rekening</span></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="javascript:void(0)" data-link="#alamat"><span><i class="fas fa-map-marker-alt"></i>Alamat</span></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="javascript:void(0)" data-link="#informasi"><span><i class="fas fa-user"></i>Pegaturan Akun</span></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="javascript:void(0)" data-link="logout" onclick="signoutNow()"><span><i class="fas fa-power-off"></i>Logout</span></a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-9">
				<!-- Tab panes -->
					<div class="tab-content">
						<!-- SALDO -->
						<div class="tab-pane in m-b-60" style="display:block;" id="saldo">
							<div class="section row m-lr-0 m-b-40 p-tb-20 p-lr-20">
								<div class="col-md-6 m-lr-auto">
									<div class="p-tb-20">
										<?php 
											$usr = $this->func->getUser($_SESSION["usrid"],"semua");
											$pro = $this->func->getProfil($_SESSION["usrid"],"semua","usrid");
										?>
										<div class="fs-18">Hai, selamat datang kembali</div>
										<div class="fs-24 font-bold text-primary"><?=strtoupper(strtolower($this->security->xss_clean($pro->nama)))?></div>
										<div class="fs-18 m-b-20">
											<div class="badge badge-success">
												<i class="fas fa-check-circle"></i> &nbsp;<?=$this->func->userlevel($usr->level)?>
											</div>	
										</div>
										<div class="">
											Link Afiliasi:<br/>
											<span class="text-dark font-medium"><?=site_url()."?aff=".$_SESSION["usrid"]?></span>
										</div>
									</div>
								</div>
								<div class="col-md-6 m-lr-auto">
									<div class="p-tb-20">
										<div class="fs-20 font-medium">Saldo Saat Ini</div>
										<?php $saldo = $this->func->getSaldo($_SESSION["usrid"],"semua","usrid"); ?>
										<div class="fs-28 font-bold text-success m-b-4"><b>Rp <?php echo $this->func->formUang($saldo->saldo); ?>,-</b></div>
										<div class="font-bold text-warning m-b-20">Koin &nbsp; <i class="fas fa-coins"></i> <b> <?php echo $this->func->formUang($saldo->koin); ?></b></div>
										<div class="row">
											<div class="col-6">
												<a href="javascript:topupSaldo()" class="btn btn-success btn-lg btn-block">
													<i class="fas fa-chevron-circle-up"></i>&nbsp; Top Up
												</a>
											</div>
											<div class="col-6">
												<a href="javascript:tarikSaldo()" class="btn btn-danger btn-lg btn-block">
													<i class="fas fa-chevron-circle-down"></i>&nbsp; Tarik
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Riwayat  Topup -->
							<div class="row p-tb-20 m-lr-0">
								<h4 class="text-primary font-bold">Riwayat Top Up Saldo</h4>
							</div>
							<div id="loadhistorytopup">
							</div>

							<!-- Riwayat  Tarik -->
							<div class="row p-t-30 p-b-20 m-lr-0">
								<h4 class="text-primary font-bold">Transaksi Terakhir</h4>
							</div>
							<div id="loadhistorysaldo">
							</div>
						</div>

						<!-- Tab Informasi Akun -->
						<div class="tab-pane in" id="informasi">
							<div class="row">
								<div class="col-md-6 m-lr-auto m-b-30">
									<h4 class="m-b-20 font-bold text-primary">
										Profil Pengguna
									</h4>
									<div class="p-all-40 section">
										<?php
											$profil = $this->func->getProfil($_SESSION["usrid"],"semua","usrid");
											$user = $this->func->getUser($_SESSION["usrid"],"semua");
										?>
										<form class="form-horizontal" id="profil">
											<div class="form-group m-b-12">
												<label>Nama</label>
												<input class="form-control" type="text" name="nama" value="<?php echo $this->security->xss_clean($profil->nama); ?>">
											</div>
											<div class="form-group m-b-12">
												<label>Email</label>
												<input class="form-control" type="text" name="email" value="<?php echo $this->security->xss_clean($user->username); ?>">
												</div>
											<div class="form-group m-b-12">
												<label>No Whatsapp</label>
												<input class="form-control col-md-6" type="text" name="nohp" value="<?php echo $this->security->xss_clean($profil->nohp); ?>">
											</div>
											<div class="form-group m-b-12">
												<label>Kelamin</label>
												<div class="rs1-select2 rs2-select2">
													<select class="js-select2 form-control" name="kelamin">
														<option value="">Kelamin</option>
															<option value="1" <?php if($profil->kelamin == 1){ echo "selected"; } ?>>Laki-laki</option>
														<option value="2" <?php if($profil->kelamin == 2){ echo "selected"; } ?>>Perempuan</option>
													</select>
													<div class="dropDownSelect2"></div>
												</div>
											</div>
											<div class="form-group m-t-50">
												<a href="javascript:void(0)" onclick="simpanProfil()" class="btn btn-success btn-block btn-lg">
													<i class="fas fa-check-circle"></i> &nbsp;Simpan Profil
													</a>
												<span id="profilload" style="display:none;"><i class='fas fa-spin fa-compact-disc text-success'></i> Menyimpan...</span>
											</div>
										</form>
									</div>
								</div>

								<div class="col-md-6 m-lr-auto p-lr-0 m-b-30">
									<h4 class="m-b-20 font-bold text-primary">
										Ganti Password
									</h4>
									<div class="section p-all-40 m-b-20">
										<form class="form-horizontal" id="gantipassword">
											<div class="form-group m-b-12">
												<label>Password Baru</label>
												<input class="form-control" type="password" name="password" value="">
											</div>
											<div class="form-group m-b-12">
												<label>Ulangi Password</label>
												<input class="form-control" type="password" value="">
											</div>
											<div class="form-group m-t-30">
												<a href="javascript:void(0)" onclick="simpanPassword()" class="btn btn-success btn-block btn-lg">
													<i class="fas fa-check-circle"></i> &nbsp;Simpan Password
												</a>
												<span id="passwload" style="display:none;"><i class='fas fa-spin fa-compact-disc text-success'></i> Menyimpan...</span>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>

						<!-- REKENING -->
						<div class="tab-pane" id="rekening">
							<?php
								$this->db->where("usrid",$_SESSION["usrid"]);
								$db = $this->db->get("rekening");

								if($db->num_rows() <= 10){
							?>
								<div class="row">
									<div class="col-md-6 hidesmall font-bold text-primary">
										<h4>Daftar Rekening</h4>
									</div>
									<div class="col-md-6 text-right m-b-20">
										<a href="javascript:tambahRekening();" class="btn btn-success">
											<i class="fas fa-plus"></i> &nbsp;Tambah Rekening
										</a>
									</div>
								</div>
							<?php
								}
							?>

							<div class="section p-all-30 table-responsive">
								<table class="table table-hover table-bordered table-striped">
									<tr class="table_head">
										<th class="p-l-20">#</th>
										<th>No Rekening</th>
										<th>Atasnama</th>
										<th>Bank</th>
										<th>Kantor Cabang</th>
										<th></th>
									</tr>

									<?php
										$no = 1;
										foreach($db->result() as $res){
									?>
									<tr class="table_row">
										<td class="p-lr-20 p-tb-10">
											<p><?php echo $no; ?></p>
										</td>
										<td>
											<p><?php echo $this->security->xss_clean($res->norek); ?></p>
										</td>
										<td>
											<p><?php echo $this->security->xss_clean($res->atasnama); ?></p>
										</td>
										<td>
											<p>BANK <?php echo $this->func->getBank($res->idbank,"nama"); ?></p>
										</td>
										<td>
											<p><?php echo $this->security->xss_clean($res->kcp); ?></p>
										</td>
										<td>
											<a href="javascript:editRekening(<?php echo $res->id; ?>)" class="btn btn-success btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
											<a href="javascript:hapusRekening(<?php echo $res->id; ?>)" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash-alt"></i></a>
										</td>
									</tr>
									<?php
											$no++;
										}
										if($db->num_rows() == 0){
											echo "<tr><td class='p-all-10 txt-center' colspan=6>
											<p><i class='fas fa-exclamation-triangle text-danger'></i> Belum ada daftar rekening, silahkan tambah data untuk menarik saldo.</p>
											</td></tr>";
										}
									?>
								</table>
							</div>
						</div>

						<!-- ALAMAT -->
						<div class="tab-pane" id="alamat">
							<?php
								$this->db->where("usrid",$_SESSION["usrid"]);
								$db = $this->db->get("alamat");

								if($db->num_rows() <= 10){
							?>
							<div class="row">
								<div class="col-md-6 hidesmall font-bold text-primary">
									<h4>Daftar Alamat</h4>
								</div>
								<div class="col-md-6 text-right m-b-20">
									<a href="javascript:tambahAlamat();" class="btn btn-success">
										<i class="fas fa-plus"></i> &nbsp;Tambah Alamat
									</a>
								</div>
							</div>
							<?php
								}
							?>

							<div class="section p-all-30 table-responsive">
								<table class="table table-hover table-bordered table-striped">
									<tr class="table_head">
										<th class="p-l-20">#</th>
										<th>Nama Penerima</th>
										<th>No Handphone</th>
										<th>Alamat</th>
										<th></th>
									</tr>

									<?php
										$no = 1;
										foreach($db->result() as $res){
									?>
									<tr class="table_row">
										<td class="p-lr-20 p-tb-10">
											<p><?php echo $res->judul; ?></p>
											<?php if($res->status == 1){ echo '<small class="badge badge-warning">Alamat Utama</small>'; } ?>
										</td>
										<td>
											<p><?php echo $this->security->xss_clean($res->nama); ?></p>
										</td>
										<td>
											<p><?php echo $this->security->xss_clean($res->nohp); ?></p>
										</td>
										<td>
											<p><?php echo $this->security->xss_clean($res->alamat)."<br/><small>Kodepos ".$this->security->xss_clean($res->kodepos)."</small>"; ?></p>
										</td>
										<td>
											<a href="javascript:editAlamat(<?php echo $res->id; ?>)" class="btn btn-success btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
											<a href="javascript:hapusAlamat(<?php echo $res->id; ?>)" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash-alt"></i></a>
										</td>
									</tr>
									<?php
											$no++;
										}
										if($db->num_rows() == 0){
											echo "<tr><td class='p-all-10 txt-center' colspan=6>
											<p><i class='fas fa-exclamation-triangle text-danger'></i> Belum ada daftar alamat, silahkan tambah data pengiriman pesanan.</p>
											</td></tr>";
										}
									?>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

  <script type="text/javascript">
    $(function(){
      	$("#loadhistorysaldo").load("<?php echo site_url("assync/getHistoryTarik"); ?>");
		$("#loadhistorytopup").load("<?php echo site_url("assync/getHistoryTopup"); ?>");

		$("#rekeningchange").change(function(){
			if($(this).val() == 0){
				$('.modal').modal("hide");
				$('#tambahrekening').modal();
				//$(this).val("").trigger("change");
			}
		});
			
		$(".nav-link").each(function(){
			var link = $(this);
			var tab = $(this).data("link");
			//var res = $(this).data("link").replace("#","");
				
			if(tab != "logout"){
				$(this).click(function(){
					$(".nav-link").removeClass("active");
					link.addClass("active");
					$(".tab-pane").hide();
					$(tab).show();
					//$(tab).html("<div class='m-lr-auto text-center p-tb-40'><h5><i class='fas fa-spin fa-compact-disc'></i> loading...</h5></div>");
					//$(tab).load("<?php echo site_url("assync/pesanan?status="); ?>"+res);
				});
			}
		});

		$("#tariksaldo form").on("submit",function(e){
			e.preventDefault();
			$(".submitbutton",this).parent().append("<span class='cl1'><i class='fas fa-spin fa-compact-disc text-primary'></i> Memproses...</span>");
			$(".submitbutton",this).hide();
			var submitbtn =	$(".submitbutton",this);
			var datar = $(this).serialize();
			datar = datar + "&"+$("#names").val()+"="+$("#tokens").val();

			$.post("<?php echo site_url("manage/saldo"); ?>",datar,function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				if(data.success == true){
					swal.fire("Berhasil!","Berhasil menarik saldo, tunggu maks. 2 hari kerja sampai uang Anda masuk ke rekening","success").then((value) => {
						location.reload();
					});
				}else{
					swal.fire("Gagal!",data.msg,"error");
					submitbtn.show();
					submitbtn.parent().find("span").remove();
				}
			});
		});

		$("#topupsaldo form").on("submit",function(e){
			e.preventDefault();
			$(".submitbutton",this).parent().append("<span class='cl1'><i class='fas fa-spin fa-compact-disc text-primary'></i> Memproses...</span>");
			$(".submitbutton",this).hide();
			var submitbtn =	$(".submitbutton",this);
			var datar = $(this).serialize();
			datar = datar + "&"+$("#names").val()+"="+$("#tokens").val();

			$.post("<?php echo site_url("assync/topupsaldo"); ?>",datar,function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				if(data.success == true){
					window.location.href= "<?=site_url("home/topupsaldo")?>?inv="+data.idbayar;
				}else{
					swal.fire("Gagal!",data.msg,"error");
					submitbtn.show();
					submitbtn.parent().find("span").remove();
				}
			});
		});

		$("#tambahalamat form").on("submit",function(e){
			e.preventDefault();
			$(".submitbutton",this).parent().append("<span class='cl1'><i class='fas fa-spin fa-compact-disc text-primary'></i> Memproses...</span>");
			$(".submitbutton",this).hide();
			var submitbtn =	$(".submitbutton",this);
			var datar = $(this).serialize();
			datar = datar + "&"+$("#names").val()+"="+$("#tokens").val();

			$.post("<?php echo site_url("assync/tambahalamat"); ?>",datar,function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				if(data.success == true){
					swal.fire("Berhasil!","Berhasil menambah alamat","success").then((value) => {
						location.reload();
						//$("#navalamat").trigger("click");
					});
				}else{
					swal.fire("Gagal!","Gagal menambah alamat baru, silahkan ulangi beberapa saat lagi.","error");
					submitbtn.show();
					submitbtn.parent().find("span").remove();
				}
			});
		});

		$("#tambahrekening form").on("submit",function(e){
			e.preventDefault();
			$(".submitbutton",this).parent().append("<span class='cl1'><i class='fas fa-spin fa-compact-disc text-primary'></i> Memproses...</span>");
			$(".submitbutton",this).hide();
			var submitbtn =	$(".submitbutton",this);
			var datar = $(this).serialize();
			datar = datar + "&"+$("#names").val()+"="+$("#tokens").val();

			$.post("<?php echo site_url("assync/tambahrekening"); ?>",datar,function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				if(data.success == true){
					swal.fire("Berhasil!","Berhasil menambah rekening","success").then((value) => {
						location.reload();
						//$("#navrekening").trigger("click");
					});
				}else{
					swal.fire("Gagal!","Gagal menambah rekening baru, silahkan ulangi beberapa saat lagi.","error");
					submitbtn.show();
					submitbtn.parent().find("span").remove();
				}
			});
		});

		localStorage["isedit"] = false;
		$("#alamatprov").change(function(){
			if(localStorage["isedit"] != true){
	      		changeKab($(this).val(),"");
			}
		});

		$("#alamatkab").change(function(){
			if(localStorage["isedit"] != true){
	      		changeKec($(this).val(),"");
			}
		});

    });

		function copyLink() {
			$("#copylink").select();
			document.execCommand("copy");
			swal.fire("Berhasil menyalin!","silahkan paste/tempel dan kirim alamat yg sudah disalin ke teman Anda","success");
		}

		// FORM CHANGING
		function changeProv(proval){
			$("#alamatprov").val(proval).trigger("change");
		}
		function changeKec(kabval,valu){
			$("#alamatkec").html("<option value=''>Loading...</option>").trigger("change");
			$.post("<?php echo site_url("assync/getkec"); ?>",{"id":kabval,[$("#names").val()]:$("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				$("#alamatkec").html(data.html).promise().done(function(){
					$("#alamatkec").val(valu);
				});
			});
		}
		function changeKab(proval,valu){
			$("#alamatkab").html("<option value=''>Loading...</option>").trigger("change");
			$("#alamatkec").html("<option value=''>Kecamatan</option>").trigger("change");

			$.post("<?php echo site_url("assync/getkab"); ?>",{"id":proval,[$("#names").val()]:$("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				$("#alamatkab").html(data.html).promise().done(function(){
					$("#alamatkab").val(valu);
				})
			});
		}

		// REKENING
		function tambahRekening(){
			$('.modal').modal("hide");
			$('#tambahrekening').modal();
		}
		function editRekening(rek){
			$.post("<?php echo site_url("assync/getRekening"); ?>",{"rek":rek,[$("#names").val()]:$("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);

				if(data.success == true){
					$("#rekeningid").val(rek);
					$("#rekeningidbank").val(data.idbank).trigger("change");
					$("#rekeningatasnama").val(data.atasnama);
					$("#rekeningnorek").val(data.norek);
					$("#rekeningkcp").val(data.kcp);

					$('.modal').modal('hide');
					$('#tambahrekening').modal();
				}else{
					swal.fire("Error!","terjadi kesalahan silahkan ulangi beberapa saat lagi.","error");
				}
			});
		}
		function hapusRekening(rek){
			swal.fire({
				title: "Anda yakin?",
				text: "menghapus rekening ini dari akun Anda?",
				icon: "warning",
				showDenyButton: true,
				confirmButtonText: "Oke",
				denyButtonText: "Batal"
			})
			.then((willDelete) => {
				if (willDelete.isConfirmed) {
					$.post("<?php echo site_url("assync/hapusRekening"); ?>",{"rek":rek,[$("#names").val()]:$("#tokens").val()},function(msg){
						var data = eval("("+msg+")");
						updateToken(data.token);

						if(data.success == true){
							swal.fire("Berhasil!","Berhasil menghapus rekening","success").then((value) => {
								location.reload();
							});
						}else{
							swal.fire("Error!","terjadi kesalahan silahkan ulangi beberapa saat lagi.","error");
						}
					});
				}
			});
		}
		function batalTopup(rek){
			swal.fire({
				title: "Anda yakin?",
				text: "membatalkan topup saldo ini?",
				icon: "warning",
				showDenyButton: true,
				confirmButtonText: "Oke",
				denyButtonText: "Batal"
			})
			.then((willDelete) => {
				if (willDelete.isConfirmed) {
					$.post("<?php echo site_url("assync/bataltopup"); ?>",{"id":rek,[$("#names").val()]:$("#tokens").val()},function(msg){
						var data = eval("("+msg+")");
						updateToken(data.token);

						if(data.success == true){
							swal.fire("Berhasil!","Berhasil membatalkan topup saldo","success").then((value) => {
								location.reload();
							});
						}else{
							swal.fire("Error!","terjadi kesalahan silahkan ulangi beberapa saat lagi.","error");
						}
					});
				}
			});
		}

		// ALAMAT
		function tambahAlamat(){
			localStorage["isedit"] = false;
			$("#alamatid").val(0);
			$("#alamatnama").val("");
			$("#alamatnohp").val("");
			$("#alamatstatus").val(0).trigger("change");
			$("#alamatalamat").val("");
			$("#alamatkodepos").val("");
			$("#alamatjudul").val("");
			$("#alamatprov").val("").trigger("change");
			$("#addressmaploc").val("");
			$('.modal').modal("hide");
			$('#tambahalamat').modal();
			setTimeout(initialize, 500);
			
		}
		function editAlamat(rek){
			localStorage["isedit"] = true;
			$.post("<?php echo site_url("assync/getAlamat"); ?>",{"rek":rek,[$("#names").val()]:$("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);

				if(data.success == true){
					changeProv(data.prov),
					changeKab(data.prov,data.kab),
					changeKec(data.kab,data.idkec);
					$("#alamatid").val(rek);
					$("#alamatnama").val(data.nama);
					$("#alamatnohp").val(data.nohp);
					$("#alamatstatus").val(data.status).trigger("change");
					$("#alamatalamat").val(data.alamat);
					$("#alamatkodepos").val(data.kodepos);
					$("#alamatjudul").val(data.judul);
					$("#addressmaploc").val(data.alamat);
					$("#latitude_gl").val(data.latitude_gl);
					$("#longitude_gb").val(data.longitude_gb);
					$("#jarak_km").val(data.jarak_km);
					$('.modal').modal("hide");
					$('#tambahalamat').modal();
					setTimeout(initialize, 500);
				}else{
					swal.fire("Error!","terjadi kesalahan silahkan ulangi beberapa saat lagi.","error");
				}
			});
		}
		function hapusAlamat(rek){
			swal.fire({
				title: "Anda yakin?",
				text: "menghapus alamat ini dari akun Anda?",
				icon: "warning",
				showDenyButton: true,
				confirmButtonText: "Oke",
				denyButtonText: "Batal"
			})
			.then((willDelete) => {
				if (willDelete.isConfirmed) {
					$.post("<?php echo site_url("assync/hapusAlamat"); ?>",{"rek":rek,[$("#names").val()]:$("#tokens").val()},function(msg){
						var data = eval("("+msg+")");
						updateToken(data.token);

						if(data.success == true){
							swal.fire("Berhasil!","Berhasil menghapus alamat","success").then((value) => {
								location.reload();
							});
						}else{
							swal.fire("Error!","terjadi kesalahan silahkan ulangi beberapa saat lagi.","error");
						}
					});
				}
			});
		}

		// SALDO
		function topupSaldo(){
			$('#topupsaldo').modal();
		}
		function tarikSaldo(){
			$('#tariksaldo').modal();
		}
		function historySaldo(page){
			$("#loadhistorysaldo").html("<i class='fas fa-spin fa-compact-disc text-primary'></i> Loading...");
			$("#loadhistorysaldo").load("<?php echo site_url("assync/getHistoryTarik"); ?>?page="+page);
		}
		function getopupSaldo(page){
			$("#loadhistorysaldo").html("<i class='fas fa-spin fa-compact-disc text-primary'></i> Loading...");
			$("#loadhistorysaldo").load("<?php echo site_url("assync/getHistoryTopup"); ?>?page="+page);
		}
		function simpanProfil(){
			$("#profil a").hide();
			$("#profilload").show();
			var datar = $("#profil").serialize();
			datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
			
			$.post("<?php echo site_url("assync/updateprofil"); ?>",datar,function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				$("#profil a").show();
				$("#profilload").hide();
				if(data.success == true){
					swal.fire("Berhasil!","Berhasil menyimpan informasi pengguna","success");
					$("#profil").reset();
				}else{
					swal.fire("Gagal!","Gagal menyimpan informasi pengguna<br/>"+data.msg,"error");
				}
			});
		}
		function simpanPassword(){
			$("#gantipassword a").hide();
			$("#passwload").show();
			var datar = $("#gantipassword").serialize();
			datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
			
			$.post("<?php echo site_url("assync/updatepass"); ?>",datar,function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				$("#gantipassword a").show();
				$("#passwload").hide();
				if(data.success == true){
					$("#gantipassword input").val("");
					swal.fire("Berhasil!","Berhasil menyimpan password baru","success");
				}else{
					swal.fire("Gagal!","Gagal menyimpan informasi password","error");
				}
			});
		}
  </script>


    <!-- Modal3-Topup Saldo -->
	<div class="modal fade" id="topupsaldo" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Top Up Saldo</h5>
					<button type="button" data-dismiss="modal" aria-label="Close">
						<i class="fas fa-times text-danger fs-24 p-all-2"></i>
					</button>
				</div>
				<div class="modal-body">
					<form method="POST" action="<?=site_url("manage/topupsaldo")?>">
						<div class="col-md-12 p-b-30 m-t-30 p-lr-30">
							<div class="form-group">
								<label for="jumlahtopup">Jumlah</label>
								<input class="form-control fs-20 font-medium" type="text" id="jumlahtopup" name="jumlah" placeholder="Rp" required>
							</div>
							<div class="form-group row">
								<div class="col-6 col-md-3 m-t-10">
									<button type="button" class="btn btn-light btn-block" onclick="$('#jumlahtopup').val(50000)">50.000</button>
								</div>
									<div class="col-6 col-md-3 m-t-10">
									<button type="button" class="btn btn-light btn-block" onclick="$('#jumlahtopup').val(100000)">100.000</button>
								</div>
								<div class="col-6 col-md-3 m-t-10">
									<button type="button" class="btn btn-light btn-block" onclick="$('#jumlahtopup').val(150000)">150.000</button>
								</div>
								<div class="col-6 col-md-3 m-t-10">
									<button type="button" class="btn btn-light btn-block" onclick="$('#jumlahtopup').val(200000)">200.000</button>
								</div>
							</div>
							<div class="form-group m-t-36">
								<button type="submit" class="submitbutton btn btn-success btn-block btn-lg">
									Topup Saldo
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

    <!-- Modal3-Tarik Saldo -->
	<div class="modal fade" id="tariksaldo" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Penarikan Saldo</h5>
					<button type="button" data-dismiss="modal" aria-label="Close">
						<i class="fas fa-times text-danger fs-24 p-all-2"></i>
					</button>
				</div>
				<div class="modal-body p-tb-40">
					<form>
						<div class="p-b-10 p-lr-30">
							<div class="form-group row">
								<div class="col-sm-4">
									<label>Jumlah</label>
								</div>
								<div class="col-md-8 m-b-12">
									<input class="form-control" type="text" name="jumlah" placeholder="Rp" required>
								</div>
								<div class="col-sm-4">
								<label>Rekening Tujuan</label>
								</div>
								<div class="col-md-8">
									<div class="m-b-12 rs1-select2 rs2-select2">
									<select class="js-select2 form-control" id="rekeningchange" name="idrek" required >
										<option value="" id='defaultrek'>Pilih Rekening</option>
										<?php
											$this->db->where("usrid",$_SESSION["usrid"]);
											$db = $this->db->get("rekening");
											foreach($db->result() as $res){
												echo "<option value='".$res->id."'>".$res->norek." - ".$res->atasnama."</option>";
											}
										?>
										<option value="0">+ Tambah Rekening Baru</option>
									</select>
									<div class="dropDownSelect2"></div>
									</div>
								</div>
								<div class="col-sm-4">
									<label>Catatan</label>
								</div>
								<div class="col-md-8 m-b-12">
									<input class="form-control" type="text" name="keterangan" placeholder="Catatan">
								</div>
							</div>
						</div>
						<div class="p-lr-30">
							<button type="submit" class="submitbutton btn btn-success btn-block btn-lg">
								Tarik Saldo
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

    <!-- Modal3-Tambah Rekening -->
	<div class="modal fade" id="tambahrekening" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Informasi Rekening Bank</h5>
					<button type="button" data-dismiss="modal" aria-label="Close">
						<i class="fas fa-times text-danger fs-24 p-all-2"></i>
					</button>
				</div>
				<div class="modal-body p-tb-40">
					<form class="form-horizontal">
						<input type="hidden" name="id" id="rekeningid" value="0" />
						<div class="p-b-20 p-lr-30">
							<div class="m-b-12">
								<label>Bank</label>
								<div class="m-b-12 rs1-select2 rs2-select2">
									<select class="js-select2 form-control" id="rekeningidbank" name="idbank" required >
										<option value="">Pilih Bank</option>
										<?php
											$db = $this->db->get("rekeningbank");
											foreach($db->result() as $res){
												echo "<option value='".$res->id."'>".$res->nama."</option>";
											}
										?>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>
							<div class="m-b-12">
								<label>No Rekening</label>
								<input class="form-control" id="rekeningnorek" type="text" name="norek" placeholder="" required>
							</div>
							<div class="m-b-12">
								<label>Atas Nama</label>
								<input class="form-control" id="rekeningatasnama" type="text" name="atasnama" placeholder="" required>
							</div>
							<div class="m-b-12">
								<label>Kantor Cabang</label>
								<input class="form-control" id="rekeningkcp" type="text" name="kcp" placeholder="" required>
							</div>
						</div>
						<div class="p-lr-30">
							<button type="submit" class="submitbutton btn btn-lg btn-success btn-block">
								Simpan Rekening
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

    <!-- Modal3-Tambah Rekening -->
	<div class="modal fade" id="tambahalamat" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Informasi Alamat</h5>
					<button type="button" data-dismiss="modal" aria-label="Close">
						<i class="fas fa-times text-danger fs-24 p-all-2"></i>
					</button>
				</div>
				<div class="modal-body p-tb-40">
					<form class="form-horizontal">
						<input type="hidden" name="id" id="alamatid" value="0" />
						<div class="p-b-15 p-lr-30">
							<div class="m-b-12">
								<label>Simpan sebagai? <small>cth: Alamat Rumah, Alamat Kantor, dll</small></label>
								<input class="form-control" id="alamatjudul" type="text" name="judul" placeholder="" required>
							</div>
							<div class="m-b-12">
								<label>Nama Penerima</label>
								<input class="form-control" id="alamatnama" type="text" name="nama" placeholder="" required>
							</div>
							<div class="m-b-12">
								<label>No Handphone</label>
								<input class="form-control" id="alamatnohp" type="text" name="nohp" placeholder="" required>
							</div>
							<div class="m-b-12">
								<label>Alamat Lengkap</label>
								<input class="form-control" id="alamatalamat" type="text" name="alamat" placeholder="" required>
							</div>
								<div class="m-b-12">
								<label>Provinsi</label>
								<div class="rs1-select2 rs2-select2">
									<select class="js-select2 form-control" id="alamatprov" required >
										<option value="">Pilih Provinsi</option>
										<?php
											$db = $this->db->get("prov");
											foreach($db->result() as $res){
												echo "<option value='".$res->id."'>".$res->nama."</option>";
											}
										?>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>
							<div class="m-b-12">
								<label>Kabupaten</label>
								<div class="rs1-select2 rs2-select2">
									<select class="js-select2 form-control" id="alamatkab" required >
										<option value="">Pilih Kabupaten/Kota</option>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>
							<div class="m-b-12">
								<label>Kecamatan</label>
								<div class="rs1-select2 rs2-select2">
									<select class="js-select2 form-control" id="alamatkec" name="idkec" required >
										<option value="">Pilih Kecamatan</option>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>
							<div class="m-b-12">
                                <div class="mb-3">
                                    <input type="hidden" class="form-control btn-light" name="gl" placeholder="Garis Lintang" required="" readonly="" id="latitude_gl" />
                                    <input type="hidden" class="form-control btn-light" name="gb" placeholder="Garis Bujur" required="" readonly="" id="longitude_gb" />
                                    <input type="hidden" class="form-control btn-light" name="glgbkm" placeholder="Garis Bujur" required="" readonly="" id="jarak_km" />
                                    <input id="addressmaploc" type="text" class="form-control" placeholder="Cari lokasi disini..." />
                                </div>
                                <div id="googleMap" style="width:100%;height:400px;"></div>
                            </div>
							<div class="m-b-12">
								<label>Kodepos</label>
								<input class="form-control" id="alamatkodepos" type="text" name="kodepos" placeholder="" required>
							</div>
							<div class="m-b-12">
								<label>Simpan Sebagai</label>
								<div class="rs1-select2 rs2-select2">
									<select class="js-select2 form-control" id="alamatstatus" name="status" required >
										<option value="0">Alamat</option>
										<option value="1">Alamat Utama</option>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>
						</div>
						<div class="p-lr-30">
							<button type="submit" class="submitbutton btn btn-success btn-block btn-lg">
								Simpan Alamat
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=AIzaSyDeY_0v4-MA7fDR8mf9Ssw6_skjyTFGbE0&libraries=places"></script>

    <script type="text/javascript">
    var marker;
    var lat = 3.5877165;
    var lng = 98.6611751;
    var latlng;
    
    var lattoko = 3.5877165;
    var lngtoko = 98.6611751;

    var mapProp = {
        center: new google.maps.LatLng(lat, lng),
        zoom: 15,
        fullscreenControl: false,
        streetViewControl: false
    };

    var map;
    var geocoder = new google.maps.Geocoder();

    function initialize() {
        map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        
        var input = document.getElementById('addressmaploc');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();

            latlng = place.geometry['location'];
            lat = place.geometry['location'].lat();
            lng = place.geometry['location'].lng();

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(15);
            }

            initialize_sec();
        });

        google.maps.event.addListener(map, 'click', function (event) {
            latlng = event.latLng;
            lat = event.latLng.lat();
            lng = event.latLng.lng();

            initialize_sec();
        });
    }

    function initialize_sec() {
        if (latlng) {
            if (typeof marker !== 'undefined') {
                marker.setMap(null);
            }

            document.getElementById("latitude_gl").value = lat;
            document.getElementById("longitude_gb").value = lng;
            
            var jarak = calculateDistance(lat, lng, lattoko, lngtoko);
            document.getElementById("jarak_km").value = jarak.toFixed(2);

            marker = new google.maps.Marker({ position: latlng, map: map });

            geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        document.getElementById("alamat_locglgb").value = results[1].formatted_address;
                    } else {
                        alert('No results found, please try again.');
                    }
                } else {
                    alert('Geocoder failed due to: ' + status);
                }
            });
        }
    }
    
    function toRad(Value) {
        return Value * Math.PI / 180;
    }
    
    function calculateDistance(lat1, lng1, lat2, lng2) {
        var R = 6371; // Radius Bumi dalam km
        var dLat = toRad(lat2 - lat1);
        var dLng = toRad(lng2 - lng1);
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var distance = R * c; // Jarak dalam km
        return distance;
    }
    
    </script>
