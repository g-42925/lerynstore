
    <?php if($data->status == "Sukses"){ ?>
        <div class="section p-all-12 m-b-12">
            <div class="m-b-12 fs-18 text-center font-bold">Informasi Tagihan</div>
            <div class="fs-13">Nomor/ID Tagihan</div>
            <div class="m-b-8 font-medium"><?=$data->customer_no?></div>
            <div class="fs-13">Nama Customer</div>
            <div class="m-b-8 font-medium"><?=$data->customer_name?></div>
            <div class="fs-13">Total Tagihan</div>
            <div class="m-b-12 font-bold text-primary fs-18">Rp <?=$this->func->formUang($data->selling_price)?> &nbsp;</div>
            <div class="m-b-12">
                <?php 
                    if(isset($data->desc?->detail)){
                        foreach($data->desc->detail as $v){ ?>
                <div class="bor-1 p-all-8">
                    <div class="fs-13 font-medium text-primary"><?=$v->periode?></div>
                    <div class="fs-11">Tagihan: Rp <?=$this->func->formUang($v->nilai_tagihan)?> | Admin: Rp <?=$this->func->formUang($v->admin)?></div>
                </div>
                <?php 
                        }
                    }
                ?>
            </div>
            <button onclick="bayarTagihan()" class="btn btn-block btn-primary">Bayar Sekarang</button>
        </div>
    <?php }else{ ?>
        <div class="section p-all-12 text-light bg-danger m-b-12">
            <div class="p-tb-12">
                Gagal cek tagihan, berikut beberapa kemungkinan penyeban kendalanya:
                <ul>
                    <li>Nomor/ID Tagihan salah</li>
                    <li>Tagihan sudah lunas</li>
                    <li>Sistem sedang dalam waktu cutoff (23.00 - 01.30 WIB)</li>
                    <li>Sedang ada gangguan pada produk/server</li>
                </ul>
            </div>
        </div>
    <?php } ?>