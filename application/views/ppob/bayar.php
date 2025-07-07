<?php
    $saldo = $this->func->getSaldo($_SESSION['usrid'],'semua');
    $prod = $this->func->getPPOB($data->idproduk,'semua');
    $cek = $this->func->getTransaksiPPOB($data->id,'semua','cek');
    $kat = $this->func->getPPOBKategori($prod->kategori_id,'semua');
    $iconkat = ($kat->icon) ? $kat->icon : 'default.png';
    $iconp = ($prod->icon) ? $prod->icon : 'default.png';
    $icon = ($prod->tipe == 1) ? $iconkat : $iconp;
    $kategori = ($prod->tipe == 1) ? $kat->nama : $prod->brand;
?>
<div class="section p-all-12" id="form">
    <div class="head">
        <div class="row">
            <div class="col-2 text-center">
                <img src="<?=base_url("cdn/ppob/".$icon)?>" class="icon">
            </div>
            <div class="col-10">
                <div class="font-bold"><?=$kategori?></div>
                <div class="fs-13">#<?=$data->invoice?></div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="font-medium"><?=$prod->nama?></div>
    <div class="m-b-12"><?=$data->nomer?></div>
    <?php 
        if($cek->id > 0){
            $dt = json_decode($cek->detail);
            $detail = (isset($dt->SN)) ? $dt->SN : "-";
    ?>
    <div class="m-b-12 font-medium"><?=$detail?></div>
    <?php } ?>
    <div class="">Total Harga</div>
    <div class="font-bold fs-18 m-b-12">Rp <?=$this->func->formUang($data->bayar)?></div>
    <?php if($data->status == 0 && $saldo->koin > 0){ ?>
    <div class="m-t-12">
        <input type="checkbox" id="koin" value="1" />&nbsp;
        <label for="koin">Gunakan Koin (<span class="text-danger"><?=$this->func->formUang($saldo->koin)?></span>)</label>
    </div>
    <?php } ?>
    <hr/>
    <div class="p-t-12">
        <?php if($data->status == 0){ ?>
        <button class="btn btn-block btn-success" onclick="bayarSekarang()">Bayar Sekarang</button>
        <?php }elseif($data->status == 1){ ?>
        <div class="status orange" *ngIf="status == 1"><fa-icon icon="clock"></fa-icon> &nbsp;Sedang diproses</div>
        <?php }elseif($data->status == 2){ ?>
        <div class="status green" *ngIf="status == 2"><fa-icon icon="check"></fa-icon> &nbsp;Sukses</div>
        <?php }elseif($data->status == 3){ ?>
        <div class="status red" *ngIf="status == 3"><fa-icon icon="times"></fa-icon> &nbsp;Dibatalkan</div>
        <?php } ?>
    </div>
</div>
<div id="loading" class="p-tb-40 text-center" style="display:none">
    <i class="fas fa-spin fa-compact-disc fs-48 text-primary m-b-12"></i><br/>memproses pembayaran, mohon tunggu sebentar...
</div>
<script type="text/javascript">
    function bayarSekarang(){
        var koin = $("#koin").prop("checked") ? 1 : 0;
        $("#form").slideUp();
        $("#loading").show();
        //console.log("Produk kepilih");
        //swal.fire("Error","gagal memproses pesanan, silahkan hubungi admin atas kendala ini. kode error: 123","error");
        $.post("<?=site_url("ppob/prosesbayar")?>",{"invoice":'<?=$data->invoice?>',"koin": koin},function(msg){
            var data = eval("("+msg+")");
            $("#loading").slideUp();
            $("#form").show();
            if(data.success){
                swal.fire('Berhasil','pesanan Anda akan kami teruskan ke sistem untuk diproses, mohon ditunggu sebentar','success').then(()=>{
                    closeatc();
                    refreshPPOB();
                });
            }else{
                swal.fire('Gagal melakukan pembayaran',data.msg,'error').then(()=>{
                    closeatc();
                });
            }
        });
    }
</script>