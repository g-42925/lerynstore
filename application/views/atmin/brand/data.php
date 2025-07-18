<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<tr>
			<th scope="col">No</th>
			<th scope="col">Foto</th>
			<th scope="col">Nama Brand</th>
			<th scope="col">Produk</th>
			<th scope="col">Aksi</th>
		</tr>
	<?php
		$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
		$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
		$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
		$perpage = 10;
		
		$this->db->select("id");
		$rows = $this->db->get("brand");
		$rows = $rows->num_rows();
		
		$this->db->order_by("id","DESC");
		$this->db->limit($perpage,($page-1)*$perpage);
		$db = $this->db->get("brand");
			
		if($rows > 0){
			$no = 1;
			foreach($db->result() as $r){
                $this->db->select("id");
                $this->db->where("brandid",$r->id);
                $dbs = $this->db->get("produk");
	?>
			<tr>
				<td><?=$no?></td>
				<td><img src="<?=base_url("cdn/brand/".$r->icon)?>" style="max-width:120px;max-height:60px;"></td>
				<td><?=$r->nama?></td>
				<td><?=$dbs->num_rows()?></td>
				<td>
					<button onclick="edit(<?=$r->id?>)" class="btn btn-xs btn-warning"><i class="fas fa-pencil-alt"></i> edit</button>
					<button onclick="hapus(<?=$r->id?>)" class="btn btn-xs btn-danger"><i class="fas fa-times"></i> hapus</button>
				</td>
			</tr>
	<?php	
				$no++;
			}
		}else{
			echo "<tr><td colspan=5 class='text-center text-danger'>Belum ada data</td></tr>";
		}
	?>
	</table>

	<?=$this->admfunc->createPagination($rows,$page,$perpage,"load");?>
</div>