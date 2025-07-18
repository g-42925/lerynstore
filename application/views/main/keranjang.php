
	<!-- breadcrumb
	<div class="container">
		<div class="bread-crumb m-b-30">
			<a href="/" class="text-primary">
				Home
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="cl4">
				Shopping Cart
			</span>
		</div>
	</div>
 	-->

	<!-- Shoping Cart -->
	<div class="container">
		<div class="row m-lr-0">
			<div class="col-md-6 m-r-auto m-l-auto m-b-40 m-t-40">
				<div class="m-lr-0-xl">
					<h2 class="font-black text-primary text-center">
						Keranjang Belanja
					</h2>
				</div>
			</div>
		</div>
      <?php
		$keranjang = $this->func->getKeranjang();
		$set = $this->func->globalset("semua");
		$hapusProduk = "";
		$totalbayar = 0;
        if($keranjang > 0){
      ?>
	  		<form action="<?=site_url("checkout")?>" method="POST">
				<div class="row">
					<div class="col-md-8">
						<div class="m-b-20">
							<?php
								if(isset($_SESSION["usrid"])){
									$this->db->where("usrid",$_SESSION["usrid"]);
								}elseif(isset($_SESSION["usrid_temp"])){
									$this->db->where("usrid_temp",$_SESSION["usrid_temp"]);
								}else{
									$this->db->where("usrid","xzact");
								}
								$this->db->where("idtransaksi",0);
								$this->db->order_by("gudang","ASC");
								$ca = $this->db->get("transaksiproduk");
								foreach ($ca->result() as $car) {
									$produk = $this->func->getProduk($car->idproduk,"semua");
									if($produk == null){ $hapusProduk .= "hapusProduk(".$car->id.")"; }
									$totalbayar += $car->harga * $car->jumlah;
									$variasi = $this->func->getVariasi($car->variasi,"semua");
									$subvariasi = ($variasi->size > 0) ? " ".$produk->subvariasi." ".$this->func->getSize($variasi->size,'nama') : "";
							?>
							<?php if(isset($gudang) && $gudang != $car->gudang){ ?>
									</div>
								</div>
							<?php } ?>
							<?php
								if(!isset($gudang) || $gudang != $car->gudang){
									$kota = $this->func->getGudang($car->gudang,"idkab");
									$kota = ($car->gudang > 0) ? $this->func->getKab($kota,"semua") : $this->func->getKab($set->kota,"semua");
									$kota = $kota->tipe." ".$kota->nama;
									$gudang = $car->gudang;
							?>
								<div class="card section m-b-20">
									<div class="card-header bg-foot">
										dikirim dari &nbsp;<b class="text-primary"><i class="fas fa-map-marker-alt"></i> <?=$kota?></b>
									</div>
									<div class="card-body">
							<?php
								}
							?>
								<div class="keranjang row" id="produk_<?php echo $car->id; ?>">
									<div class="col-md-1 col-4 pointer">
										<input type="checkbox" class="pointer cebox" name="idproduk[]" value="<?=$car->id?>" id="cebox_<?=$car->id?>" data-total="<?=$car->harga*$car->jumlah?>" />
									</div>
									<div class="col-md-1 col-4 pointer">
										<div class="img" style="background-image:url('<?php echo $this->func->getFoto($produk->id,"utama"); ?>')" onclick="window.location.href='<?php echo site_url('produk/'.$produk->url); ?>'"></div>
									</div>
									<div class="col-md-10 col-12 row m-lr-0">
										<div class="col-md-5 m-b-10 centered flex-column pointer" onclick="window.location.href='<?php echo site_url('produk/'.$produk->url); ?>'">
											<span class="font-medium w-full"><?php echo $produk->nama; ?></span>
											<?php
												if($produk->digital > 0){
													echo "<div class='text-left w-full'><span class=\"badge badge-primary font-medium\"><i class='fas fa-cloud'></i> &nbsp;PRODUK DIGITAL</span></div>";
												}
												if($produk->preorder > 0){
													echo "<div class='text-left w-full'><span class=\"badge badge-warning font-medium\"><i class='fas fa-history'></i> &nbsp;PRE ORDER</span></div>";
												}
												if($car->variasi > 0){
													echo "<span class='text-info w-full' style='font-size:80%;'><b>".$produk->variasi." ".$this->func->getWarna($variasi->warna,'nama').$subvariasi."</b></span>";
												}
												if($car->keterangan != ""){
													echo "<span class='text-warning w-full' style='font-size:80%;'><b>Note: </b> <i>".$car->keterangan."</i></span>";
												}
											?>
										</div>
										<div class="col-md-3 col-12 m-b-20 centered">
											<div class="wrap-num-product input-group">
												<div class="num-product-down input-group-prepend cursor-pointer">
													<span class="input-group-text btn-primary"><i class="fs-16 fas fa-minus text-light"></i></span>
												</div>

												<input class="form-control text-center num-product produk-jumlah p-lr-0" type="number" min="<?php echo $produk->minorder; ?>" id="jumlah_<?php echo $car->id; ?>" name="jumlah[]" value="<?php echo $car->jumlah; ?>" data-proid="<?php echo $car->id; ?>" />

												<div class="num-product-up input-group-append cursor-pointer">
													<span class="input-group-text btn-primary"><i class="fs-16 fas fa-plus text-light"></i></span>
												</div>
											</div>
										</div>
										<div class="col-8 col-md-3 centered">
											Rp&nbsp;<span id="totalhargauang_<?php echo $car->id; ?>"><?php echo $this->func->formUang($car->harga*$car->jumlah); ?></span>
										</div>
										<div class="col-4 col-md-1 centered">
											<a href="javascript:void(0)" onclick="hapus(<?=$car->id?>)" class="text-danger"><i class="fa fa-trash-alt fs-20"></i><span class='showsmall-inline'>&nbsp;hapus</span></a>
										</div>
									</div>
								</div>
								<input type="hidden" id="harga_<?php echo $car->id; ?>" value="<?php echo $car->harga; ?>" />
								<input type="hidden" class="totalhargaproduk" id="totalharga_<?php echo $car->id; ?>" value="<?php echo $car->harga*$car->jumlah; ?>" />
								<input type="hidden" name="id[]" value="<?php echo $car->id; ?>" />
							<?php
								}
							?>
								</div>
							</div>
						</div>
						<div class="alert alert-warning text-center">
							Pilih produk yang akan Anda bayar terlebih dahulu, pastikan jenis produknya sama (digital atau fisik) dan asal pengiriman juga sama.
							Apabila yang Anda pilih tercampur, maka hanya akan ada satu jenis saja yang di proses untuk checkout ke pembayaran dan produk lainnya akan dikembalikan ke keranjang belanja Anda.
						</div>
					</div>
					<div class="col-md-4">
						<div class="section p-all-20 m-b-40">
							<div class="m-b-20 row">
								<div class="col-4">
									<h5 class="font-bold text-success">Total</h5>
								</div>
								<div class="col-8 text-right">
									<h5 class="font-bold text-success">Rp <span id="totalbayar"><?php echo $this->func->formUang($totalbayar); ?></span></h5>
								</div>
							</div>
							<div class="text-center">
								<button type="submit" class="btn btn-success w-full">
									Selesaikan Pesanan
								</button>
								<hr/>
								<a href="<?php echo site_url("shop"); ?>" class="btn btn-primary w-full">
									Kembali Berbelanja
								</a>
							</div>
						</div>
					</div>
				</div>
			</form>
		<?php
			}else{
		?>
			<div class="row p-lr-16">
				<div class="col-lg-12 m-lr-auto p-tb-150 m-t-40 m-b-150 text-light bg-info" style="border-radius: 16px;">
					<div class="m-lr-0-xl text-center">
						<h3>Keranjang belanja masih kosong.</h3>
					</div>
				</div>
			</div>
		<?php
			}
		?>
	</div>
	<script>
		$(function(){
			<?=$hapusProduk?>

			$(".cebox").change(function(){
				var item = 0;
				var total = 0;
				var totalawal = <?=$totalbayar?>;
				$(".cebox").each(function(){
					if($(this)[0].checked){
						total = total + parseFloat($(this).data("total"));
						item = item + 1;
					}
				});
				
				if(item > 0){
					$("#totalbayar").html(formUang(total));
				}else{
					$("#totalbayar").html(formUang(totalawal));
				}
			});
			
			$('.num-product-down').on('click', function(e){
				e.stopPropagation();
				e.preventDefault();
				var numProduct = Number($(this).next().val());
				if(numProduct > 1) $(this).next().val(numProduct - 1).trigger("change");
			});

			$('.num-product-up').on('click', function(e){
				e.stopPropagation();
				e.preventDefault();
				var numProduct = Number($(this).prev().val());
				$(this).prev().val(numProduct + 1).trigger("change");
			});

			$(".produk-jumlah").on('change',function(){
			var jumlah = $(this).val();
			var prodid = $(this).attr("data-proid");
			var ndis = $(this);

			if(jumlah > 0){

				if(jumlah < Number($(this).attr("min"))){
					$(this).val($(this).attr("min")).trigger("change");
					return;
				}

				$.post("<?php echo site_url("assync/updatekeranjang"); ?>",{"update":prodid,"jumlah":jumlah,[$("#names").val()]: $("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						var harga = data.harga;
						$("#harga_"+prodid).val(harga);
						var hargatotal = Number(jumlah) * harga;
						$("#totalhargauang_"+prodid).html(formUang(hargatotal));
						$("#totalharga_"+prodid).val(hargatotal);
						$("#cebox_"+prodid).data("total",hargatotal);
						$("#cebox_"+prodid).trigger("change");
						var sum = 0;
						$(".totalhargaproduk").each(function(){
							sum += parseFloat($(this).val());
						});
						$("#totalbayar").html(formUang(sum));
					}else{
						swal.fire("Gagal",data.msg,"error")
						.then((willDelete) => {
							location.reload();
						});
					}
				});

			}else{
				swal.fire({
					title: "Anda yakin?",
					text: "menghapus produk dari keranjang belanja",
					icon: "warning",
					showDenyButton: true,
					confirmButtonText: "Oke",
					denyButtonText: "Batal",
				})
				.then((willDelete) => {
				if (willDelete.isConfirmed) {
				//$("#produk_"+prodid).hide();
					$.post("<?php echo site_url("assync/hapuskeranjang"); ?>",{"hapus":prodid,[$("#names").val()]: $("#tokens").val()},function(msg){
						var data = eval("("+msg+")");
						updateToken(data.token);
						if(data.success == true){
							location.reload();
						}else{
							swal.fire("Gagal","Gagal menghapus pesanan, silahkan ulangi beberapa saat lagi","error");
						}
					});
				}else{
					$(this).val($(this).attr("min"));
				}
				});
			}
			});
		});
		
		function hapus(id){
			$("#jumlah_"+id).val(0).trigger("change");
		}
		
		function hapusProduk(id){
			$.post("<?php echo site_url("assync/hapuskeranjang"); ?>",{"hapus":id,[$("#names").val()]: $("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				if(data.success == true){
					location.reload();
				}else{
					swal.fire("Gagal","Gagal menghapus pesanan, silahkan ulangi beberapa saat lagi","error");
				}
			});
		}
</script>
