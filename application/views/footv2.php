	<?php 
		$set = $this->func->globalset("semua");
		$usrid = (isset($_SESSION["usrid"])) ? $_SESSION["usrid"] : 0;
		$nowasap = intval($set->wasap);
		$nowasap = (substr($nowasap, 0, 2) != '62') ? "62".$nowasap : $nowasap;
	?>
	<!-- Footer -->
	<div class="bg-foot-gradient p-t-40"></div>
	<footer class="bg-foot p-t-45 p-b-43" style="margin-top: -2px;">
		<div class="p-b-40 container m-l-auto m-r-auto">
			<div class="row m-b-20">
				<div class="col-md-4 m-b-20">
					<img src="<?= base_url('assets/images/'.$set->logo) ?>" style="width:80%;" class="m-b-20" />

					<div class="m-b-20">
						<table>
							<tr class="pointer" onclick="window.open('https://wa.me/<?=$nowasap?>')">
								<td class='p-r-10'>
									<i class="fab fa-whatsapp text-primary fs-26"></i>
								</td>
								<td>
									Whatsapp
									<div class="font-medium fs-18 m-t--4 m-b-8"><?=$set->wasap?></div>
								</td>
							</tr>
							<tr class="pointer" onclick="window.open('https://instagram.com/<?=$set->instagram?>')">
								<td class='p-r-10'>
									<i class="fab fa-instagram text-primary fs-26"></i>
								</td>
								<td>
									Instagram
									<div class="font-medium fs-18 m-t--4 m-b-8">@<?=$set->instagram?></div>
								</td>
							</tr>
							<tr class="pointer" onclick="window.open('mailto:<?=$set->email?>')">
								<td class='p-r-10'>
									<i class="fas fa-envelope-open text-primary fs-24"></i>
								</td>
								<td>
									Email
									<div class="font-medium fs-18 m-t--4 m-b-8"><?=$set->email?></div>
								</td>
							</tr>
							<tr>
								<td class='p-r-10'>
									<i class="fas fa-map-marker-alt text-primary fs-26"></i>
								</td>
								<td>
									Alamat
									<div class="font-medium fs-18 m-t--4"><?=nl2br($set->alamat)?></div>
								</td>
							</tr>
						</table>
					</div>
					<!--
					<div class="m-b-20">
						<a target="_blank" onclick="fbq('track','Contact')" href="https://wa.me/<?=$this->func->getRandomWasap()?>/?text=Halo,%20mohon%20infonya%20untuk%20menjadi%20reseller%20*<?=$this->func->getSetting("nama")?>*%20caranya%20bagaimana%20ya?%20dan%20syaratnya%20apa%20saja,%20terima%20kasih" class="btn btn-success btn-block"><i class="fab fa-whatsapp"></i> Hubungi Admin</a>
						&nbsp;<p>Dapatkan potongan harga khusus untuk reseller.
						</div>
					<div class="flex-m p-t-10">
						<a onclick="fbq('track','Contact')" href="https://fb.me/<?=$set->facebook?>" style="color: #2980b9;" class="fs-32 color1 p-r-20 fab fa-facebook-square"></a>
						<a onclick="fbq('track','Contact')" href="https://instagram.com/<?=$set->instagram?>" style="color: #dd2a7b;" class="fs-32 color1 p-r-20 fab fa-instagram"></a>
						<a onclick="fbq('track','Contact')" href="mailto:<?=$set->email?>" class="color1 fs-32 color1 p-r-20 fas fa-envelope"></a>
					</div>
					-->
				</div>

				<div class="col-md-8">
					<div class="row">
						<div class="col-md-3 col-6 m-b-20">
							<h5 class="font-medium foot-title p-b-20">
								Umum
							</h5>

							<ul class="foot-menu">
								<li class="p-b-9">
									<a href="<?=site_url("blog")?>">
										Berita Terbaru
									</a>
								</li>
								<?php
									$this->db->where("jenis",1);
									$this->db->where("status",1);
									$this->db->limit(9);
									$page = $this->db->get("page");
									foreach($page->result() as $r){
								?>
								<li class="p-b-9">
									<a href="<?=site_url("page/".$r->slug)?>">
										<?=ucwords(strtolower($r->nama))?>
									</a>
								</li>
								<?php
									}
								?>
							</ul>
						</div>
						<div class="col-md-3 col-6 m-b-20">
							<h5 class="font-medium foot-title p-b-20">
								Informasi
							</h5>

							<ul class="foot-menu">
								<?php
									$this->db->where("jenis",2);
									$this->db->where("status",1);
									$this->db->limit(9);
									$page = $this->db->get("page");
									foreach($page->result() as $r){
								?>
								<li class="p-b-9">
									<a href="<?=site_url("page/".$r->slug)?>">
										<?=ucwords(strtolower($r->nama))?>
									</a>
								</li>
								<?php
									}
								?>
							</ul>
						</div>
						<div class="col-md-3 col-6 m-b-20">
							<h5 class="font-medium foot-title p-b-20">
								Ketentuan
							</h5>

							<ul class="foot-menu">
								<?php
									$this->db->where("jenis",3);
									$this->db->where("status",1);
									$this->db->limit(9);
									$page = $this->db->get("page");
									foreach($page->result() as $r){
								?>
								<li class="p-b-9">
									<a href="<?=site_url("page/".$r->slug)?>">
										<?=ucwords(strtolower($r->nama))?>
									</a>
								</li>
								<?php
									}
								?>
							</ul>
						</div>
						<div class="col-md-3 col-6 m-b-20">
							<h5 class="font-medium foot-title p-b-20">
								Ikuti Kami
							</h5>
							<div class="flex-m">
								<a onclick="fbq('track','Contact')" href="https://fb.me/<?=$set->facebook?>" style="color: #2980b9;" class="fs-32 color1 p-r-20 fab fa-facebook-square"></a>
								<a onclick="fbq('track','Contact')" href="https://instagram.com/<?=$set->instagram?>" style="color: #dd2a7b;" class="fs-32 color1 p-r-20 fab fa-instagram"></a>
								<a onclick="fbq('track','Contact')" href="mailto:<?=$set->email?>" class="color1 fs-32 color1 p-r-20 fas fa-envelope"></a>
							</div>
						</div>
					</div>
					<div class="text-center p-all-12 bg-primary text-light rounded">
						<?=nl2br($set->jamkerja)?>
					</div>
				</div>
			</div>

			<div class="text-center m-b-20">
				<div class="m-b-12">
					<h4 class="font-medium foot-title">Metode Pembayaran</h4>
				</div>
				<div class="row">
					<?php
						$datab = [];
						if ($handle = opendir('assets/images/payment')) {
							while (false !== ($entry = readdir($handle))) {
								if ($entry != "." && $entry != "..") {
									$datab[] = $entry;
								}
							}
							closedir($handle);
						}
						foreach($datab as $key=>$val){
							echo "<div class='col-3 col-md-1 m-b-12 m-lr-auto'><img src='".base_url('assets/images/payment/'.$val)."' class='w-full'></div>";
						}
					?>
				</div>
			</div>
			<div class="text-center">
				<div class="m-b-12">
					<h4 class="font-medium foot-title">Jasa Pengiriman</h4>
				</div>
				<div class="row">
					<?php
						$kurir = explode("|",$set->kurir);
						for($i=0; $i<count($kurir); $i++){
							$kur = $this->func->getKurir($kurir[$i],"rajaongkir");
							if(file_exists(FCPATH."assets/images/kurir/".$kur.".png")){
								echo "<div class='col-3 col-md-1 m-b-12 m-lr-auto'><img src='".base_url("assets/images/kurir/".$kur.".png")."' /></div>";
							}
						}
					?>
				</div>
			</div>
		</div>

		<div class="t-center p-l-15 p-r-15">
			<div class="t-center p-t-20">
				Copyright © <?=date('Y');?> <?=ucwords(strtolower($set->nama))?>
				<?php if($this->func->demo() == true){ ?> | made with <i class="fas fa-heart text-danger"></i> by Masbil</a><?php } ?>
			</div>
		</div>
	</footer>



	<!-- Back to top
	<div class="btn-back-to-top bg0-hov" id="myBtn">
		<span class="symbol-btn-back-to-top">
			<i class="fa fa-angle-double-up" aria-hidden="true"></i>
		</span>
	</div> -->
	<input type="hidden" id="names" value="<?=$this->security->get_csrf_token_name()?>" />
	<input type="hidden" id="tokens" value="<?=$this->security->get_csrf_hash();?>" />

	<?php if($this->func->cekLogin() == true){ ?>
	<script type="text/javascript">
		$(function(){
			//$("#modalpesan").modal();
			$("#modalpilihpesan,#modalpesan").on('shown.bs.modal', function(){
				$(".chat-sticky").hide();
			});
			$("#modalpilihpesan,#modalpesan").on('hidden.bs.modal', function(){
				$(".chat-sticky").show();
			});
			$("#modalpesan").on('shown.bs.modal', function(){
				fbq("track","Contact");
				loadPesan(0);
				var seti = setInterval(()=>{ loadPesan(1); },10000);
				$("#modalpesan").on('hidden.bs.modal', function(){
					clearInterval(seti);
				});
			});
			
			$("#kirimpesan").on("submit",function(e){
				e.preventDefault();
				var datar = $(this).serialize();
				datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
				$("input,button",this).attr("disabled",true);
				$.post("<?=site_url("assync/kirimpesan")?>",datar,function(s){
					$("#kirimpesan input,#kirimpesan button").attr("disabled",false);
					fbq("track","Contact");
					var data = eval("("+s+")");
					updateToken(data.token);
					$("#kirimpesan input").val("");
					if(data.success == true){
						$("#pesan").html('<div class="isipesan"><i class="fas fa-spin fa-compact-disc text-success"></i> memuat pesan...</div>');
						loadPesan(0);
					}else{
						swal.fire("GAGAL!","terjadi kendala saat mengirim pesan, coba ulangi beberapa saat lagi","error");
					}
				});
			});
			
			//$("#modalpilihpesan").modal();
			
			function loadPesan(nul){
				$("#pesan").load("<?=site_url("assync/pesanmasuk")?>",function(){
					if(nul != 1){
						$("#pesan").animate({ scrollTop: $("#pesan").prop('scrollHeight')}, 1000);
					}
				});
			}

		});
	</script>
	<div class="modal fade" id="modalpesan" tabindex="-1" role="dialog" style="background: rgba(0,0,0,.5);" style="bottom:0;right:0;" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-primary font-medium"><i class="fa fa-comments"></i> Live Chat</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body pesan" id="pesan">
					<div class="pesanwrap center">
						<div class="isipesan"><i class="fas fa-spin fa-compact-disc text-success"></i> memuat pesan...</div>
					</div>
				</div>
				<form id="kirimpesan" method="POST">
					<div class="modal-footer">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="ketik pesan..." name="isipesan" required />
							<div class="input-group-append">
								<button type="submit" id="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i> KIRIM</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php }else{ ?>
	<!--<a href="https://wa.me/<?=$this->func->getRandomWasap()?>" class="chat-sticky hidesmall" target="_blank"><i class="fas fa-comment-dots"></i> Live Chat</a>-->
	<?php }?>
	<div class="modal fade" id="modalpilihpesan" tabindex="-1" role="dialog" style="background: rgba(0,0,0,.5);" style="bottom:0;right:0;" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content p-lr-30 p-tb-40 text-center">
				<h3 class="text-primary font-bold">Hubungi Admin</h3><br/>
				<a href="https://wa.me/<?=$this->func->getRandomWasap()?>" target="_blank" class="btn btn-lg btn-block btn-success m-b-10"><i class="fab fa-whatsapp"></i> &nbsp;Hubungi via Whatsapp</a>
				<?php if($this->func->cekLogin() == true){ ?>
					<button onclick="$('#modalpilihpesan').modal('hide');$('#modalpesan').modal()" class="btn btn-lg btn-block btn-primary"><i class="fas fa-comments"></i> &nbsp;Live Chat &nbsp;<b class="badge badge-danger p-lr-8 notifchat" style="display:none">0</b></button>
				<?php }else{ ?>
					<a href="<?=site_url("home/signin")?>" class="btn btn-lg btn-block btn-primary"><i class="fas fa-comments"></i> &nbsp;Live Chat</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<a href="javascript:void(0)" class="chat-sticky hidesmall" onclick='$("#modalpilihpesan").modal()'><b class="badge badge-danger p-lr-8 notifchat" style="display:none">0</b><i class="fas fa-comment-dots"></i> Live Chat</a>
	
	<?php
		if($this->func->demo() == true AND !isset($_SESSION["demo"])){
	?>
	<div class="modal fade" id="modaldisclaimer" tabindex="-1" role="dialog" style="background: rgba(0,0,0,.5);" style="bottom:0;right:0;" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-danger font-medium"><i class="fa fa-exclamation-triangle"></i> &nbsp;Pemberitahuan Penting!</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="p-all-28 text-center">
						Website ini adalah versi demo untuk aplikasi Toko Online by <b class="text-info">Masbil Al Muhammad</b>.<br/>
						Apabila berminat dapat langsung menghubungi pembuat aplikasi ini melalui whatsapp, apabila Anda membeli aplikasi ini melalui pihak lain selain pada kontak yang tertera, maka 
						apabila ada kendala atau kerugian material yg lain bukan tanggungjawab kami selaku pengembang aplikasi.<br/>&nbsp;<br/>
						<b class="text-success">Beli Langsung Klik Dibawah</b><br/>
						<a href="https://wa.me/6285691257411" target="_blank" class="btn btn-success m-t-10"><i class="fab fa-whatsapp"></i> Hubungi Pengembang</a><br/>
						<small>atau</small><br/>
						<a href="https://member.jadioke.com" target="_blank"><i class="fas fa-arrow-right"></i> https://member.jadioke.com</a>
						<br/>&nbsp;<br/>
						<span class="fs-18">hargailah jerih payah pengembang aplikasi dengan <b class="text-danger">tidak menggunakan aplikasi ilegal&nbsp;</b></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function(){
			$("#modaldisclaimer").modal();
		});
	</script>
	<?php
			$this->session->set_userdata("demo",true);
		}
	?>
	
	<div class="modal fade" id="modalatc" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title font-medium"><i class="fa fa-shopping-basket text-warning"></i> &nbsp;Tambah ke keranjang</h5>
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

	<script type="text/javascript" src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
	<script type="text/javascript">
		$(".js-select2").each(function(){
			$(this).select2({
    			theme: 'bootstrap4',
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		});
	</script>
	<script type="text/javascript" src="<?= base_url('assets/vendor/slick/slick.min.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/vendor/swal/sweetalert2.min.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/main.js') ?>"></script>
	<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
	<script type="text/javascript">
		$(function(){
		    
		    var swiper = new Swiper('.slide-kategori', {
                slidesPerView: 'auto',
                spaceBetween: 12,
                loop: true,
                autoplay: {
                    delay: 2500, // waktu delay dalam milidetik
                    disableOnInteraction: false, // tetap autoplay meskipun ada interaksi pengguna
                },
            });
            
			$(".countdown").each(function(){
				var elem = $(this);
				var tgl = elem.data("tgl");
				var countDownDate = new Date(tgl).getTime();
				var x = setInterval(function() {
					var now = new Date().getTime();
					var distance = countDownDate - now;
					var days = Math.floor(distance / (1000 * 60 * 60 * 24));
					var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
					var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
					var seconds = Math.floor((distance % (1000 * 60)) / 1000);
					if(days < 10){ days = "0"+days; }
					if(hours < 10){ hours = "0"+hours; }
					if(minutes < 10){ minutes = "0"+minutes; }
					if(seconds < 10){ seconds = "0"+seconds; }

					if(days > 0){
						var res = days + " <small>hari</small> " + hours + " : " + minutes + " : " + seconds;
					}else{
						var res = hours + " : " + minutes + " : " + seconds;
					}
					elem.html(res);

					if (distance < 0) {
						clearInterval(x);
						//document.getElementById("demo").innerHTML = "EXPIRED";
					}
				}, 1000);
			});
		});
			
		window.onscroll = function() {myFunction()};
		var navbar = document.getElementById("navbar-sticky");
		var sticky = navbar.offsetTop;
		function myFunction() {
			if (window.pageYOffset >= sticky) {
				navbar.classList.add("menu-sticky")
			} else {
				navbar.classList.remove("menu-sticky");
			}
		}
		
		var dataText = ["Cari produk favoritmu, ketik disini","Ketik saja nama produk atau kategori produk"];
	
		function typeWriter(text, i, fnCallback) {
			// chekc if text isn't finished yet
			if (i < (text.length)) {
				// add next character to h1
				$(".typedtext").attr("placeholder",text.substring(0, i+1));

				// wait for a while and call this function again for next character
				setTimeout(function() {
					typeWriter(text, i + 1, fnCallback)
				}, 100);
			}
			// text finished, call callback if there is a callback function
			else if (typeof fnCallback == 'function') {
			// call callback after timeout
				setTimeout(fnCallback, 2000);
			}
		}
		// start a typewriter animation for a text in the dataText array
		function StartTextAnimation(i) {
			if (typeof dataText[i] == 'undefined'){
				setTimeout(function() {
					StartTextAnimation(0);
				}, 5000);
			}else{
				// check if dataText[i] exists
				if (i < dataText[i].length) {
				// text exists! start typewriter animation
					typeWriter(dataText[i], 0, function(){
					// after callback (and whole text has been animated), start next text
						StartTextAnimation(i + 1);
					});
				}
			}
		}
		// start the text animation
		StartTextAnimation(0);

  		//AOS.init();
		new ClipboardJS('.clip');
		  
		function formUang(data){
			return data.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
		}
		function signoutNow(){
			swal.fire({
				title: "Logout",
				text: "yakin akan logout dari akun anda?",
				icon: "warning",
				showDenyButton: true,
				confirmButtonText: "Oke",
				denyButtonText: "Batal"
			})
			.then((willDelete) => {
				if (willDelete.isConfirmed) {
					window.location.href="<?=site_url("home/signout")?>";
				}
			});
		}

		function tambahWishlist(id,nama){
			$.post("<?php echo site_url("assync/tambahwishlist/"); ?>"+id,{[$("#names").val()]:$("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				var wish = parseInt($(".wishlistcount").html());
				updateToken(data.token);
				if(data.success == true){
					$(".wishlistcount").html(wish+1);
					swal.fire(nama, "berhasil ditambahkan ke wishlist", "success");
				}else{
					swal.fire("Gagal", data.msg, "error");
				}
			});
		}
		function beliTopup(id){
			<?php if($this->func->cekLogin() == true){ ?>
				$("#modalatc .modal-title").html('<i class="fa fa-shopping-basket text-warning"></i> &nbsp;Top Up');
				$("#modalatc .modal-body").load("<?=site_url("ppob/topup")?>/"+id);
				$("#modalatc").modal();
			<?php }else{ ?>
				window.location.href="<?=site_url("home/signin");?>";
			<?php } ?>
		}
		function beliTagihan(id){
			<?php if($this->func->cekLogin() == true){ ?>
				$("#modalatc .modal-title").html('<i class="fa fa-shopping-basket text-warning"></i> &nbsp;Bayar Tagihan');
				$("#modalatc .modal-body").load("<?=site_url("ppob/tagihan")?>/"+id);
				$("#modalatc").modal();
			<?php }else{ ?>
				window.location.href="<?=site_url("home/signin");?>";
			<?php } ?>
		}
		function bayarPPOB(id){
			<?php if($this->func->cekLogin() == true){ ?>
				$("#modalatc .modal-title").html('<i class="fa fa-receipt text-primary"></i> &nbsp;Bayar Pesanan');
				$("#modalatc .modal-body").load("<?=site_url("ppob/bayarpesanan")?>/"+id);
				$("#modalatc").modal();
			<?php }else{ ?>
				window.location.href="<?=site_url("home/signin");?>";
			<?php } ?>
		}
		function addtocart(id){
			$("#modalatc .modal-title").html('<i class="fa fa-shopping-basket text-warning"></i> &nbsp;Tambah Keranjang');
			$("#modalatc").modal();
			$("#modalatc .modal-body").load("<?=site_url("home/formatc")?>/"+id);
		}
		function closeatc(){
			$("#modalatc").modal("hide");
		}
		function updateKeranjang(){
			var jum = parseFloat($(".jmlkeranjang").html())+1;
			$(".jmlkeranjang").html(jum);
		}

		function updateToken(token){
			$("#tokens,.tokens").val(token);
		}

		$(".block2-wishlist .fas").on("click",function(){
			$(this).removeClass("active");
			$(this).addClass("active");
		});

		function pesanProduk(id){
			$.post("<?=site_url("assync/kirimpesan")?>",{"idproduk":id,"isipesan":"",[$("#names").val()]:$("#tokens").val()},function(s){
				var data = eval("("+s+")");
				updateToken(data.token);
				if(data.success == true){
					$('#modalpesan').modal()
				}else{
					swal.fire("GAGAL!","terjadi kendala saat mengirim pesan, coba ulangi beberapa saat lagi","error");
				}
			});
		}

		setInterval(() => {
			$.post("<?=site_url("assync/notifchat")?>",{"id":<?=$usrid?>},function(s){
				var data = eval("("+s+")");
				if(data.notif > 0){
					$(".notifchat").html(data.notif);
					$(".notifchat").show();
				}else{
					$(".notifchat").hide();
				}
			});
		}, 2000);
	</script>

	<!-- Facebook Pixel Code -->
		<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window, document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '<?=$set->fb_pixel?>');
		fbq('track', 'PageView');
		</script>
		<noscript>
		<img height="1" width="1" style="display:none" 
			src="https://www.facebook.com/tr?id=<?=$set->fb_pixel?>&ev=PageView&noscript=1"/>
		</noscript>
	<!-- End Facebook Pixel Code -->

</body>
</html>
