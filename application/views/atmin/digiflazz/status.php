<div class="p-all-20">
    <?php
        if($data){
            $status = ($data->status == "Sukses") ? "<span class='text-success'><i class='fas fa-check'></i> Sukses</span>" : "<span class='text-warning'><i class='fas fa-clock'></i> Pending</span>";
            $status = ($data->status == "Gagal") ? "<span class='text-danger'><i class='fas fa-times'></i> Gagal</span>" : $status;
            $harga = isset($data->price) ? $this->func->formUang($data->price) : "-";
    ?>
        <div class="">No Invoice</div>
        <div class="font-bold m-b-12 fs-16">#<?=$data->ref_id?></div>
        <div class="">Nomor/ID Tujuan</div>
        <div class="font-bold m-b-12 fs-16"><?=$data->customer_no?></div>
        <div class="">Kode Produk</div>
        <div class="font-bold m-b-12 fs-16"><?=$data->buyer_sku_code?></div>
        <div class="">Harga Beli</div>
        <div class="font-bold m-b-12 fs-16">Rp <?=$harga?></div>
        <div class="">Status</div>
        <div class="font-bold m-b-12 fs-16"><?=$status?></div>
        <div class="">Pesan Sistem</div>
        <div class="font-bold m-b-12 fs-16"><?=$data->message?></div>
        <div class="">Serial Number</div>
        <div class="font-bold m-b-12 fs-16"><?=$data->sn?></div>
        <?php if(!empty($data->tele)){ ?>
        <div class="">Telegram Suplier</div>
        <div class="font-bold m-b-12 fs-16"><?=$data->tele?></div>
        <?php } ?>
        <?php if(!empty($data->wa)){ ?>
        <div class="">Whatsapp Suplier</div>
        <div class="font-bold m-b-12 fs-16"><?=$data->wa?></div>
        <?php } ?>
    <?php }else{ ?>
        <div class="p-tb-20 text-center text-danger">Gagal cek status pesanan, silahkan ulangi lagi nanti</div>
    <?php } ?>
</div>