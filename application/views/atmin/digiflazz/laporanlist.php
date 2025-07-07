<div class="text-center m-t-20 m-b-30">
	<h4><b>LAPORAN TRANSAKSI PPOB</b></h4><br/>
	Periode: <?=$this->admfunc->ubahTgl("d/m/Y",$_POST["tglmulai"])?> sampai <?=$this->admfunc->ubahTgl("d/m/Y",$_POST["tglselesai"])?>
</div>
<div class="table-responsive">
	<table class="table table-condensed table-hover table-bordered">
		<tr>
			<th scope="col">No</th>
			<th scope="col">Tanggal</th>
			<th scope="col">ID Transaksi</th>
			<th scope="col">Produk</th>
			<th scope="col">Tujuan</th>
			<th scope="col">Pembeli</th>
			<th scope="col">Status</th>
			<th scope="col">Harga</th>
			<th scope="col">Margin</th>
			<th scope="col">Total</th>
		</tr>
	<?php
		$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
		$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
		$perpage = 10;

		$where = "cek = '0' AND tgl BETWEEN '".$_POST["tglmulai"]." 00:00:00' AND '".$_POST["tglselesai"]." 23:59:59'";
		$whereupdate = "cek = '0' AND selesai BETWEEN '".$_POST["tglmulai"]." 00:00:00' AND '".$_POST["tglselesai"]." 23:59:59'";
		if(isset($_POST["status"])){
			if($_POST["status"] == 1){
				$where = "status > 0 AND status < 3 AND (".$where.")";
			}elseif($_POST["status"] == 2){
				$where = "status = 0 AND (".$where.")";
			}elseif($_POST["status"] == 3){
				$where = "status = 1 AND (".$where.")";
			}elseif($_POST["status"] == 4){
				$where = "status = 2 AND (".$whereupdate.")";
			}elseif($_POST["status"] == 5){
				$where = "status >= 3 AND (".$whereupdate.")";
			}
		}
		
		$this->db->order_by("status ASC, tgl DESC");
		$this->db->where($where);
		$db = $this->db->get("transaksi_ppob");
			
		if($db->num_rows() > 0){
			$no = 1;
            $totalbeli = 0;
            $totalmargin = 0;
            $total = 0;
			foreach($db->result() as $r){				
				if($r->status == 0){
					$status = "Belum Dibayar";
				}elseif($r->status == 1){
					$status = "Sedang Diproses";
				}elseif($r->status == 2){
					$status = "Selesai";
				}elseif($r->status == 3){
					$status = "Gagal Memproses";
				}elseif($r->status == 4){
					$status = "Dibatalkan";
				}else{
					$status = "-";
				}

				$nama = strtoupper(strtolower($this->admfunc->getProfil($r->usrid,"nama","usrid")));
                $totalbeli += $r->harga_beli;
                $totalmargin += $r->total-$r->harga_beli;
                $total += $r->total;
	?>
			<tr>
				<td><?=$no?></td>
				<td><?=$this->admfunc->ubahTgl("d/m/Y H:i",$r->tgl)?></td>
				<td><?=$r->invoice?></td>
				<td><?=$this->func->getPPOB($r->idproduk,"nama")?></td>
				<td><?=$r->nomer?></td>
				<td><?=$nama?></td>
				<td><?=$status?></td>
				<td class='text-right'><?=$this->admfunc->formUang($r->harga_beli)?></td>
				<td class='text-right'><?=$this->admfunc->formUang($r->total-$r->harga_beli)?></td>
				<td class='text-right'><?=$this->admfunc->formUang($r->total)?></td>
			</tr>
	<?php	
				$no++;
			}
			if($total > 0){
				echo "
				<tr>
					<th class='text-right' colspan=7>TOTAL</th>
					<th class='text-right'>Rp. ".$this->admfunc->formUang($totalbeli)."</th>
					<th class='text-right'>Rp. ".$this->admfunc->formUang($totalmargin)."</th>
					<th class='text-right'>Rp. ".$this->admfunc->formUang($total)."</th>
				</tr>
				";
			}else{
				echo "<tr><td colspan=7 class='text-center text-danger'>Belum ada data</td></tr>";
			}
		}else{
			echo "<tr><td colspan=7 class='text-center text-danger'>Belum ada data</td></tr>";
		}
	?>
	</table>
</div>