<?php
	$page = (isset($_GET["page"]) AND $_GET["page"] != "" AND intval($_GET["page"]) > 0) ? intval($_GET["page"]) : 1;
	$perpage = 10;

	$this->db->where("cek",0);
	$this->db->where("usrid",$_SESSION["usrid"]);
	$rows = $this->db->get("transaksi_ppob");
	$rows = $rows->num_rows();

	$this->db->where("cek",0);
	$this->db->where("usrid",$_SESSION["usrid"]);
	$this->db->order_by("status ASC,id DESC");
	$this->db->limit($perpage,($page-1)*$perpage);
	$db = $this->db->get("transaksi_ppob");
	if($db->num_rows() > 0){
?>
<div class="pesanan">
<?php
    foreach($db->result() as $rx){
        $prod = $this->func->getPPOB($rx->idproduk,'semua');
        $kat = $this->func->getPPOBKategori($prod->kategori_id,'semua');
        $iconkat = ($kat->icon) ? $kat->icon : 'default.png';
        $iconp = ($prod->icon) ? $prod->icon : 'default.png';
        $icon = ($prod->tipe == 1) ? $iconkat : $iconp;
        $kategori = ($prod->tipe == 1) ? $kat->nama : $prod->brand;
?>
		<div class="m-b-30">
			<div class="pesanan-item p-all-20 m-lr-0-xl">
                <div class="row m-b-12">
                    <div class="col-9">
                        <div class="row">
                        <div class="col-3 col-md-2 text-center">
                            <img src="<?=base_url('cdn/ppob/'.$icon)?>" style="max-width:90%;max-height:48px;">
                        </div>
                        <div class="col-9 col-md-10">
                            <div class="font-bold"><?=$kategori?></div>
                            <div class="fs-13"><?=$this->func->ubahTgl("d M Y",$rx->tgl)?></div>
                        </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <?php if($rx->status == 0){ ?>
                        <div class="p-all-8 radius-8 bg-warning text-center fs-12">Belum Bayar</div>
                        <?php }elseif($rx->status == 1){ ?>
                        <div class="p-all-8 radius-8 bg-warning text-center fs-12">Diproses</div>
                        <?php }elseif($rx->status == 2){ ?>
                        <div class="p-all-8 radius-8 bg-success text-center fs-12 text-light">Berhasil</div>
                        <?php }elseif($rx->status == 3){ ?>
                        <div class="p-all-8 radius-8 bg-danger text-center fs-12 text-light">Dibatalkan</div>
                        <?php } ?>
                    </div>
                </div>
				<hr/>
				<div class="row">
					<div class="col-md-6 m-b-12">
						<div class="text-dark font-medium">
							Order ID <span class="text-success">#<?php echo $rx->invoice; ?></span>
						</div>
						<div class="text-primary font-medium">
							<?php echo $prod->nama; ?>
						</div>
						<div class="text-dark font-medium">
							<?php echo $rx->nomer; ?>
						</div>
					</div>
					<div class="col-md-6">
						<?php if($rx->detail){ ?>
						<div class="bg-warning p-all-12 radius-8">
							<table cellpadding=0 cellspacing=0>
							<?php
								$dt = json_decode($rx->detail,true);
								foreach($dt as $k=>$v){
									echo "<tr><td>".$k."&nbsp;</td><td>: ".$v."</td></tr>";
								}
							?>
							</table>
						</div>
						<?php } ?>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-6 m-b-12">
                        Total Pembelian
						<h5 class="text-primary font-bold">Rp <?php echo $this->func->formUang($rx->total); ?></h5>
					</div>
					<div class="col-md-3 col-6">
						<?php if($rx->status == 0){ ?>
                        <button class="btn btn-success btn-block" onclick="bayarPPOB('<?php echo $rx->invoice; ?>')">Bayar</button>
						<?php } ?>
					</div>
					<div class="col-md-3 col-6">
						<?php if($prod->tipe == 1){ ?>
                        <button class="btn btn-primary btn-block" onclick="beliTopup('<?php echo $prod->kategori_id; ?>')">Beli Lagi</button>
						<?php }else{ ?>
                        <button class="btn btn-primary btn-block" onclick="beliTagihan('<?php echo $prod->id; ?>')">Beli Lagi</button>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	<?php
		}
		echo $this->func->createPagination($rows,$page,$perpage,"refreshPPOB");
	?>
</div>
<?php
	}else{
?>
	<div class="text-center p-tb-40 section">
		<i class="fas fa-box-open fs-120 text-danger m-b-20"></i>
		<h5 class="text-dark font-bold">TIDAK ADA PESANAN</h5>
	</div>
<?php
	}
?>

<script type="text/javascript">
	$(document).ready(function(){
		$(".show-product").hide();
		$(".view-product").click(function(){
			$(this).parent().parent().find(".show-product").slideToggle();
			$(this).parent().parent().find(".view-product").toggle();
		});
	});
</script>
