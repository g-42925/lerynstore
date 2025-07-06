<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<tr>
			<th scope="col">#</th>
			<th scope="col">Produk</th>
			<th scope="col">User/Nama</th>
			<th scope="col">Ulasan</th>
			<th scope="col">Status</th>
			<th scope="col">Aksi</th>
		</tr>
	<?php
		$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
		$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
		$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
		$perpage = 10;
		
		$this->db->select("id");
		$rows = $this->db->get("review");
		$rows = $rows->num_rows();
		
		$this->db->order_by("moderasi","ASC");
		$this->db->order_by("id","DESC");
		$this->db->limit($perpage,($page-1)*$perpage);
		$db = $this->db->get("review");
			
		if($rows > 0){
			$no = 1;
			foreach($db->result() as $r){
                $prod = $this->func->getProduk($r->idproduk,"semua");
                $user = $this->func->getUser($r->usrid,"semua");
                $star = "";
                for($i=1; $i<=5; $i++){
                  $color = ($i <= $r->nilai) ? "text-warning" : "text-secondary";
                  $star .= '<i class="fa fa-star '.$color.'"></i>';
                }
                $nama = ($r->jenis == 1) ? "<b>".$r->nama."</b>" : "<b>".$user->nama."</b><br/>".$user->nohp;
                $fake = ($r->jenis == 1) ? "<i class='fas fa-dice-one text-danger'></i>" : "<i class='fas fa-square text-success'></i>";
                $status = ($r->moderasi == 1) ? "<span class='badge badge-success'>Verified</span>" : "<span class='badge badge-warning'>Pending</span>";
                $status = ($r->moderasi == 2) ? "<span class='badge badge-danger'>Ditolak</span>" : $status;
	?>
			<tr>
				<td><?=$fake?></td>
				<td><?="<b>".$prod->nama."</b><br/>Rp ".$this->func->formUang($prod->harga)?></td>
				<td><?=$nama?></td>
				<td style="width:35%;">
                    Nilai: <b><?=$r->nilai?></b> <?=$star?><br/>
                    <i class="text-primary"><?=$r->keterangan?></i>
                </td>
				<td><?=$status?></td>
				<td style="width:120px;">
                    <?php if($r->moderasi == 0){ ?>
					<button onclick="verifikasi(<?=$r->id?>)" class="btn btn-success py-1 px-2"><i class="fas fa-check"></i></button>
                    <?php } ?>
					<button onclick="edit(<?=$r->id?>)" class="btn btn-warning py-1 px-2"><i class="fas fa-pencil-alt"></i></button>
					<button onclick="hapus(<?=$r->id?>)" class="btn btn-danger py-1 px-2"><i class="fas fa-trash-alt"></i></button>
				</td>
			</tr>
	<?php	
				$no++;
			}
		}else{
			echo "<tr><td colspan=6 class='text-center text-danger'>Belum ada data</td></tr>";
		}
	?>
	</table>

	<?=$this->admfunc->createPagination($rows,$page,$perpage,"load");?>
</div>