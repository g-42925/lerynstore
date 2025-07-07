<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<tr>
			<th scope="col">No Transaksi</th>
			<th scope="col">Nama Pembeli & No/ID</th>
			<th scope="col">Produk</th>
			<th scope="col">Total</th>
			<th scope="col">Detail</th>
			<th scope="col">Keterangan</th>
		</tr>
		<?php
			$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
			$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
			$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
			$perpage = 10;
			$set = $this->admfunc->globalset("semua");
			
			$in = -1;
			$int = -1;
			$arr = array();
			$arrt = array();
			$this->db->select("usrid");
			$this->db->like("nama",$cari);
			$this->db->or_like("nohp",$cari);
			$al = $this->db->get("profil");
			foreach($al->result() as $l){
				if($l->usrid > 0){
					$arr[] = $l->usrid;
				}
			}
			$this->db->select("id");
			$this->db->like("nama",$cari);
			$this->db->or_like("kategori",$cari);
			$this->db->or_like("deskripsi",$cari);
			$this->db->or_like("kode",$cari);
			$al = $this->db->get("ppob");
			foreach($al->result() as $l){
				$arry[] = $l->id;
			}
			$arr = array_unique($arr);
			$arr = array_values($arr);
			for($i=0; $i<count($arr); $i++){
				$ins = ",".$arr[$i];
				$in = ($in != 0) ? $in.$ins : $arr[$i];
			}
			$arrt = array_unique($arrt);
			$arrt = array_values($arrt);
			for($i=0; $i<count($arrt); $i++){
				$inst = ",".$arrt[$i];
				$int = ($int != 0) ? $int.$inst : $arrt[$i];
			}

			$where = "cek = '0' AND status = '2' AND (invoice LIKE '%$cari%' OR bayar LIKE '%$cari%' OR usrid IN(".$in.") OR idproduk IN(".$int."))"; 
			$this->db->select("id");
			$this->db->where($where);
			//$this->db->like("orderid",$cari);
			//$this->db->where("status",3);
			$rows = $this->db->get("transaksi_ppob");
			$rows = $rows->num_rows();

			$this->db->where($where);
			//$this->db->like("orderid",$cari);
			//$this->db->where("status",3);
			$this->db->order_by("selesai","DESC");
			$this->db->limit($perpage,($page-1)*$perpage);
			$db = $this->db->get("transaksi_ppob");
			
			if($rows > 0){
				$no = 1;
				foreach($db->result() as $r){
					$profil = $this->admfunc->getProfil($r->usrid,"semua","usrid");
					$produk = $this->func->getPPOB($r->idproduk,"semua");
					$detail = "";
					if($r->detail){
						$dt = json_decode($r->detail,true);
						foreach($dt as $k=>$v){
							$detail .= ($detail != "") ? "<br/>".$k.": ".$v : $k.": ".$v;
						}
					}
		?>
			<tr>
				<td><b>#<?=$r->invoice?></b><br/><i class="fas fa-check-circle text-success"></i> &nbsp;<?=$this->admfunc->ubahTgl("d/m/Y H:i",$r->selesai);?></td>
				<td><?=$profil->nama."<br/>".$r->nomer?></td>
				<td><?=$produk->kode."<br/>".$produk->nama?></td>
				<td>
					Rp <?=$this->admfunc->formUang($r->total)?><br/>
					<span class="text-success fs-12">+Rp <?=$this->admfunc->formUang($r->total-$r->harga_beli)?></span>
				</td>
				<td><?=$detail?></td>
				<td><?=$r->keterangan?></td>
			</tr>
		<?php	
					$no++;
				}
			}else{
				echo "<tr><td colspan=6 class='text-center text-danger'>Belum ada pesanan</td></tr>";
			}
		?>
	</table>

	<?=$this->admfunc->createPagination($rows,$page,$perpage,"loadSelesai");?>
</div>