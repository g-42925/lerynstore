<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<tr>
			<th scope="col">No</th>
			<th scope="col">icon</th>
			<th scope="col">SKU</th>
			<th scope="col">Nama</th>
			<th scope="col">Kategori</th>
			<th scope="col">Brand</th>
			<th scope="col">Harga Beli</th>
			<th scope="col">Harga Jual</th>
			<th class="text-right" scope="col">Aksi</th>
		</tr>
	<?php
		$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
		$cari = (isset($_GET["cari"]) AND $_GET["cari"] != "") ? $_GET["cari"] : "";
		$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
		$perpage = 10;
		
		$where = "cek = '0' AND tipe = '1' AND (nama LIKE '%".$cari."%' OR kategori LIKE '%".$cari."%' OR brand LIKE '%".$cari."%' OR deskripsi LIKE '%".$cari."%' OR kode LIKE '%".$cari."%' OR harga_jual LIKE '%".$cari."%')";
		$this->db->select("id");
		//$this->db->where("jenis",2);
		$this->db->where($where);
		$rows = $this->db->get("ppob");
		$rows = $rows->num_rows();
		
		//$this->db->where("jenis",2);
		//$this->db->order_by("status","ASC");
		//$this->db->where("cek",0);
		//$this->db->where("tipe",1);
		$this->db->where($where);
		$this->db->order_by("brand","ASC");
		$this->db->order_by("harga_beli","ASC");
		$this->db->order_by("nama","ASC");
		$this->db->limit($perpage,($page-1)*$perpage);
		$db = $this->db->get("ppob");
			
		if($rows > 0){
			$no = (($page-1)*$perpage)+1;
			$total = 0;
			foreach($db->result() as $r){
                $jenis = ($r->tipe == 1) ? "Prabayar" : "Pascabayar";
                $icon = ($r->icon) ? $r->icon : "default.png";
	?>
			<tr>
				<td><?=$no?></td>
				<td><img src="<?=base_url('cdn/ppob/'.$icon)?>" height="32px" /></td>
				<td><?=$r->kode?></td>
				<td><?=$r->nama?></td>
				<td><?=$r->kategori?></td>
				<td><?=$r->brand?></td>
				<td><?=$this->func->formUang($r->harga_beli)?></td>
				<td>
                    <?=$this->func->formUang($r->harga_jual)?><br/>
                    <small class="text-success">+<?=$this->func->formUang($r->harga_jual-$r->harga_beli)?><b></b></small>
                </td>
				<td class="text-right">
					<button onclick="editProduk(<?=$r->id?>)" class="btn btn-xs btn-warning"><i class="fas fa-pencil-alt"></i> edit</button>
				</td>
			</tr>
	<?php	
				$no++;
			}
		}else{
			echo "<tr><td colspan=8 class='text-center text-danger'>Belum ada produk PPOB</td></tr>";
		}
	?>
	</table>

	<?=$this->admfunc->createPagination($rows,$page,$perpage,"loadProduk");?>
</div>