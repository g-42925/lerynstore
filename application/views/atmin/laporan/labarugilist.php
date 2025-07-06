<div class="text-center m-t-20 m-b-30">
	<h4><b>LAPORAN LABA RUGI PENJUALAN</b></h4><br/>
	<?php
		if(isset($_POST["gudang"]) AND $_POST["gudang"] != "semua"){
			if($_POST["gudang"] > 0){
				$gudang = $this->admfunc->getGudang($_POST["gudang"],"semua");
				$kota = $this->admfunc->getKab($gudang->idkab,"semua");
				$kota = $kota->tipe." ".$kota->nama;
				echo "<div class='fs-18 m-t--12 m-b-20'><i class='fas fa-map-marker-alt'></i> ".strtoupper(strtolower($gudang->nama))." - ".$kota."</div>";
			}else{
				$set = $this->admfunc->globalset("semua");
				$kota = $this->admfunc->getKab($set->kota,"semua");
				$kota = $kota->tipe." ".$kota->nama;
				echo "<div class='fs-18 m-t--12 m-b-20'><i class='fas fa-map-marker-alt'></i> PUSAT - ".$kota."</div>";
			}
		}
	?>
	Periode: <?=$this->admfunc->ubahTgl("d/m/Y",$_POST["tglmulai"])?> sampai <?=$this->admfunc->ubahTgl("d/m/Y",$_POST["tglselesai"])?>
</div>
<div class="table-responsive">
	<table class="table table-condensed table-hover table-bordered">
		<tr>
			<th scope="col">No</th>
			<th scope="col">Tanggal</th>
			<th scope="col">ID Transaksi</th>
			<th scope="col">Nama Pembeli</th>
			<th scope="col">Total</th>
			<th scope="col">Ongkir</th>
			<th scope="col">Diskon</th>
			<th scope="col">Harga Beli</th>
			<th scope="col">Laba/Rugi</th>
		</tr>
	<?php
		$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
		$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
		$perpage = 10;

		$whereupdate = "tglupdate BETWEEN '".$_POST["tglmulai"]." 00:00:00' AND '".$_POST["tglselesai"]." 23:59:59'";
		$where = "status = 3 AND (".$whereupdate.")";
		if(isset($_POST["jenis"])){
			if($_POST["jenis"] == 1){
				$where = "digital = 0 AND ".$where;
			}elseif($_POST["jenis"] == 2){
				$where = "digital = 1 AND ".$where;
			}
		}
		if(isset($_POST["gudang"])){
			if($_POST["gudang"] != "semua"){
				$where = "gudang = ".$_POST["gudang"]." AND ".$where;
			}
		}
		//echo $where;
		
		$this->db->order_by("status","ASC");
		$this->db->where($where);
		$db = $this->db->get("transaksi");
			
		if($db->num_rows() > 0){
			$no = 1;
			$total = 0;
			$totalbayar = 0;
			$totaldiskon = 0;
			$totalmargin = 0;
			$totalongkir = 0;
			foreach($db->result() as $r){
				$bayar = $this->admfunc->getBayar($r->idbayar,"semua");
				$totalbayar += $bayar->diskon + $bayar->total - $bayar->kodebayar;
				$totalongkir += $r->ongkir;
				$totaldiskon += $bayar->diskon;
                $totals = $bayar->diskon + $bayar->total - $bayar->kodebayar - $r->ongkir;

				$nama = strtoupper(strtolower($this->admfunc->getUserTemp($r->usrid_temp,"nama")))."<i class='text-danger m-l-8'>(non member)</i>";
				$nama = ($r->usrid > 0) ? strtoupper(strtolower($this->admfunc->getProfil($r->usrid,"nama","usrid"))) : $nama;
                $this->db->where("idtransaksi",$r->id);
                $dbs = $this->db->get("transaksiproduk");
                $margin = 0;
                foreach($dbs->result() as $tp){
                    $margin += $tp->jumlah * $tp->hargabeli;
                }
                $totalmargin += $margin;
                $totals = $totals - $margin;
                $total += $totals;
                $totul = $totals > 0 ? "text-success font-bold" : "text-danger font-bold";
	?>
			<tr>
				<td><?=$no?></td>
				<td><?=$this->admfunc->ubahTgl("d/m/Y H:i",$r->tgl)?></td>
				<td><?=$r->orderid?></td>
				<td><?=$nama?></td>
				<td class='text-right'><?=$this->admfunc->formUang($bayar->diskon + $bayar->total - $bayar->kodebayar)?></td>
				<td class='text-right'><?=$this->admfunc->formUang($r->ongkir)?></td>
				<td class='text-right'><?=$this->admfunc->formUang($bayar->diskon)?></td>
				<td class='text-right'><?=$this->admfunc->formUang($margin)?></td>
				<td class='text-right <?=$totul?>'><?=$this->admfunc->formUang($totals)?></td>
			</tr>
	<?php	
				$no++;
			}
			if($total > 0){
                $tutul = $total > 0 ? "text-success font-bold" : "text-danger font-bold";
				echo "
				<tr>
					<th class='text-right' colspan=4>TOTAL</th>
					<th class='text-right'>Rp. ".$this->admfunc->formUang($totalbayar)."</th>
					<th class='text-right'>Rp. ".$this->admfunc->formUang($totalongkir)."</th>
					<th class='text-right'>Rp. ".$this->admfunc->formUang($totaldiskon)."</th>
					<th class='text-right'>Rp. ".$this->admfunc->formUang($totalmargin)."</th>
					<th class='text-right ".$tutul."'>Rp. ".$this->admfunc->formUang($total)."</th>
				</tr>
				";
			}else{
				echo "<tr><td colspan=9 class='text-center text-danger'>Belum ada data</td></tr>";
			}
		}else{
			echo "<tr><td colspan=9 class='text-center text-danger'>Belum ada data</td></tr>";
		}
	?>
	</table>
</div>