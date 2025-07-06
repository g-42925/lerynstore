<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<tr>
			<th scope="col">No</th>
			<th scope="col">Checkout Page</th>
			<th scope="col">View</th>
			<th scope="col">Order</th>
			<th scope="col">Paid</th>
			<th scope="col">Net Revenue</th>
			<th scope="col">Aksi</th>
		</tr>
	<?php
		$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
		$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
		$idpro = (isset($_POST["idpro"]) AND $_POST["idpro"] != "") ? intval($_POST["idpro"]) : 0;
		$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
		$perpage = 10;
        
        $arr = [];
        if($cari != ""){
            $this->db->select("id");
            $this->db->like("nama",$cari);
            $this->db->or_like("kode",$cari);
            $this->db->or_like("url",$cari);
            $this->db->or_like("deskripsi",$cari);
            $this->db->or_like("harga",$cari);
            $al = $this->db->get("produk");
            foreach($al->result() as $l){
                if($l->id > 0){
                    $arr[] = $l->id;
                }
            }
            $arr = array_unique($arr);
            $arr = array_values($arr);
        }
		
		$this->db->select("id");
        if(count($arr) > 0 && $idpro == 0){
            $this->db->where_in("idproduk",$arr);
        }
        if($idpro > 0){
            $this->db->where("idproduk",$idpro);
        }
		$rows = $this->db->get("formorder");
		$rows = $rows->num_rows();
		
        if(count($arr) > 0 && $idpro == 0){
            $this->db->where_in("idproduk",$arr);
        }
        if($idpro > 0){
            $this->db->where("idproduk",$idpro);
        }
		$this->db->order_by("id","DESC");
		$this->db->limit($perpage,($page-1)*$perpage);
		$db = $this->db->get("formorder");
			
		if($rows > 0){
			$no = 1;
			foreach($db->result() as $r){
                $order = 0; $paid = 0; $total = 0; $paidpersen = 0;
                $this->db->where("formid",$r->id);
                $dbs = $this->db->get("pembayaran");
                $order = $dbs->num_rows();
                foreach($dbs->result() as $p){
                    if($p->status == 1){
                        $paid += 1;
                        $total += $p->total - $p->kodebayar;
                    }
                }
                $paidpersen = ($paid > 0) ? ($paid/$order)*100 : 0;
	?>
			<tr>
				<td><?=$no?></td>
				<td>
                    <?=$r->nama?><br/>
                    <span class="fs-12 text-primary"><i class="fas fa-box fs-10"></i> &nbsp;<?=$this->admfunc->getProduk($r->idproduk,"nama")?></span>
                </td>
				<td><?=$this->admfunc->formUang($r->views)?></td>
				<td><?=$this->admfunc->formUang($order)?></td>
				<td><?=$this->admfunc->formUang($paid)." (".$paidpersen."%)"?></td>
				<td><?=$this->admfunc->formUang($total)?></td>
				<td>
					<button onclick="embed(<?=$r->id?>)" class="btn btn-xs btn-primary"><i class="fas fa-code"></i></button>
					<a href="<?=site_url("f/".$r->url)?>" class="btn btn-xs btn-primary" target="_blank"><i class="fas fa-eye"></i></a>
					<a href="<?=site_url("atmin/orderform/edit/".$r->id)?>" class="btn btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                    <?php if($order == 0){ ?>
					<button onclick="hapus(<?=$r->id?>)" class="btn btn-xs btn-danger"><i class="fas fa-trash-alt"></i></button>
                    <?php } ?>
				</td>
			</tr>
	<?php	
				$no++;
			}
		}else{
			echo "<tr><td colspan=7 class='text-center text-danger'>Belum ada form order</td></tr>";
		}
	?>
	</table>

	<?=$this->admfunc->createPagination($rows,$page,$perpage,"load");?>
</div>