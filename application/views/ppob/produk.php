<?php
    if($brand){
    $this->db->where("brand",$brand);
    }
    $this->db->where("cek",0);
    $this->db->where("kategori",$id);
    $this->db->order_by("harga_jual","ASC");
    $db = $this->db->get("ppob");
    foreach($db->result() as $r){
?>
    <div class="section p-all-12 m-b-12">
        <div class="row">
            <div class="col-9">
                <div class="m-b-4">Kode: <?=$r->kode?></div>
                <div class="font-medium"><?=$r->nama?></div>
                <div class="m-b-4 fs-13"><i><?=$r->deskripsi?></i></div>
                <div class="font-bold text-primary fs-20">Rp <?=$this->func->formUang($r->harga_jual)?> &nbsp;</div>
            </div>
            <div class="col-3">
                <button onclick="pilihProduk('<?=$r->kode?>')" class="btn btn-block btn-primary">BELI</button>
            </div>
        </div>
    </div>
<?php } ?>