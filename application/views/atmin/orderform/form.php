<?php
    $r = $this->func->getOrder($id,"semua");
    $produk = ($r->id > 0) ? $r->idproduk : $produk;
    $prod = $this->func->getProduk($produk,"semua");
    $foto  = ($r->header != "") ? base_url("cdn/uploads/".$r->header) : base_url("cdn/uploads/no-image.png");
    $btntext = ($r->button_text != "") ? $r->button_text : "Bayar";
    $set = $this->func->globalset("semua");
?>
<form method="POST" action="<?=site_url("atmin/orderform/tambah/")?>" enctype="multipart/form-data">
    <input type="file" name="header" id="logoUpload" onchange="readURL(this)" style="display:none;" accept="image/x-png,image/gif,image/jpeg" ></input>
    <div class="row m-b-60">
        <div class="col-md-7">
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Page Name
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="id" value="<?=$id?>" />
                    <input type="hidden" name="idproduk" value="<?=$prod->id?>" />
                    <input type="text" name="nama" placeholder="Enter page name..." class="form-control m-b-5" value="<?=$r->nama?>" />
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Header
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group logoset col-md-8">
                        <div class="logo">
                            <img id="logo" src="<?=$foto?>" />
                            <button type="button" class="btn btn-secondary btn-block logouploadbtn" onclick="$('#logoUpload').trigger('click')"><i class="fas fa-sync"></i> ganti header</button>
                            <div class="progress progreslogo" style="display:none;">
                                <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="<?=$r->title?>" />
                    </div>
                    <div class="form-group m-b-5">
                        <label>Tagline</label>
                        <input type="text" name="tagline" class="form-control" value="<?=$r->tagline?>" />
                    </div>
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Product
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="showproduct" class="form-check-input" id="ceboxproduk" value="1" <?php if($r->id == 0 || $r->showproduct == 1){ echo "checked"; } ?> >
                            <span class="form-check-sign">Show Product</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Guarantee Sales
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group m-b-5">
                        <label>Label Transaksi Aman</label>
                        <select class="form-control" name="garansi1" id="garansi1">
                            <option value="0" <?php if($r->garansi1 == 0){ echo "selected"; } ?>>DISABLED</option>
                            <option value="1" <?php if($r->garansi1 == 1){ echo "selected"; } ?>>Transaksi Aman</option>
                            <option value="2" <?php if($r->garansi1 == 2){ echo "selected"; } ?>>Secured Transaction</option>
                        </select>
                    </div>
                    <div class="form-group m-b-5">
                        <label>Label Jaminan</label>
                        <select class="form-control" name="garansi2" id="garansi2">
                            <option value="0" <?php if($r->garansi2 == 0){ echo "selected"; } ?>>DISABLED</option>
                            <option value="1" <?php if($r->garansi2 == 1){ echo "selected"; } ?>>100% Jaminan Kepuasan</option>
                            <option value="2" <?php if($r->garansi2 == 2){ echo "selected"; } ?>>100% Satisfaction Seal</option>
                            <!--
                            <option value="2" <?php if($r->garansi2 == 2){ echo "selected"; } ?>>Garansi Uang Kembali</option>
                            <option value="3" <?php if($r->garansi2 == 3){ echo "selected"; } ?>>Enkripsi 256-bit</option>
                            <option value="4" <?php if($r->garansi2 == 4){ echo "selected"; } ?>>100% Satisfaction Seal</option>
                            <option value="5" <?php if($r->garansi2 == 5){ echo "selected"; } ?>>Money Back Guarantee</option>
                            <option value="6" <?php if($r->garansi2 == 6){ echo "selected"; } ?>>256-bit Encryption</option>
                            -->
                        </select>
                    </div>
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Requested Form
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group" id="customfield">
                        <?php if($id == 0){ ?>
                        <div class="pc-form-field d-flex" data-noreq="true" id="<?=md5(uniqid(rand(), true))?>">
                            <input type="hidden" name="fieldid[]" class="fieldid" value="0" />
                            <input type="hidden" name="fieldmodel[]" class="fieldmodel" value="stat" />
                            <input type="hidden" name="fieldsambung[]" class="fieldsambung" value="1" />
                            <input type="hidden" name="fieldtype[]" class="fieldtype" value="1" />
                            <input type="hidden" name="field[]" class="field" value="1" />
                            <input type="hidden" name="fieldrequired[]" class="fieldrequired" value="1" />
                            <input type="hidden" name="fieldopsi[]" value="" />
                            <input type="hidden" name="fieldlabel[]" class="fieldlabel" value="Nama" />
                            <input type="hidden" name="fieldplaceholder[]" class="fieldplaceholder" value="Nama" />
                            <input type="hidden" name="fieldactive[]" value="1">
                            <div class="pc-form-field-input form-inline">
                                <label>
                                </label>
                                <div class="input form-control">
                                    <span class="pc-form-field-label">
                                        Nama
                                    </span>
                                    <span class="pc-form-field-required text-danger">*</span>
                                    <i class="fas fa-cog pc-form-field-toggle pull-right" data-original-title="null" onclick="editField($(this).parents('.pc-form-field').first().attr('id'))"></i><!---->
                                </div>
                            </div>
                            <div class="pc-form-field-control btn-group border radius-6">
                                <button type="button" class="btn btn-light" data-original-title="null">
                                    <i class="fas fa-sort"></i>
                                </button>
                            </div>
                        </div>
                        <div class="pc-form-field d-flex" data-noreq="true" id="<?=md5(uniqid(rand(), true))?>">
                            <input type="hidden" name="fieldid[]" class="fieldid" value="0" />
                            <input type="hidden" name="fieldmodel[]" class="fieldmodel" value="stat" />
                            <input type="hidden" name="fieldsambung[]" class="fieldsambung" value="2" />
                            <input type="hidden" name="fieldtype[]" class="fieldtype" value="1" />
                            <input type="hidden" name="field[]" class="field" value="1" />
                            <input type="hidden" name="fieldrequired[]" class="fieldrequired" value="1" />
                            <input type="hidden" name="fieldopsi[]" value="" />
                            <input type="hidden" name="fieldlabel[]" class="fieldlabel" value="No Handphone/Whatsapp" />
                            <input type="hidden" name="fieldplaceholder[]" class="fieldplaceholder" value="No Handphone/Whatsapp" />
                            <input type="hidden" name="fieldactive[]" value="1">
                            <div class="pc-form-field-input form-inline">
                                <label>
                                </label>
                                <div class="input form-control">
                                    <span class="pc-form-field-label">
                                        No Handphone/Whatsapp
                                    </span>
                                    <span class="pc-form-field-required text-danger">*</span>
                                    <i class="fas fa-cog pc-form-field-toggle pull-right" data-original-title="null" onclick="editField($(this).parents('.pc-form-field').first().attr('id'))"></i><!---->
                                </div>
                            </div>
                            <div class="pc-form-field-control btn-group border radius-6">
                                <button type="button" class="btn btn-light" data-original-title="null">
                                    <i class="fas fa-sort"></i>
                                </button>
                            </div>
                        </div>
                        <div class="pc-form-field d-flex" data-noreq="false" id="<?=md5(uniqid(rand(), true))?>">
                            <input type="hidden" name="fieldid[]" class="fieldid" value="0" />
                            <input type="hidden" name="fieldmodel[]" class="fieldmodel" value="stat" />
                            <input type="hidden" name="fieldsambung[]" class="fieldsambung" value="0" />
                            <input type="hidden" name="fieldtype[]" class="fieldtype" value="1" />
                            <input type="hidden" name="field[]" class="field" value="1" />
                            <input type="hidden" name="fieldrequired[]" class="fieldrequired" value="1" />
                            <input type="hidden" name="fieldopsi[]" value="" />
                            <input type="hidden" name="fieldlabel[]" class="fieldlabel" value="Alamat Lengkap" />
                            <input type="hidden" name="fieldplaceholder[]" class="fieldplaceholder" value="Alamat Lengkap" />
                            <div class="pc-form-field-input form-inline">
                                <label>
                                    <input type="hidden" name="fieldactive[]" class="cekno" value="0">
                                    <input type="checkbox" name="fieldactive[]" class="input-checkbox input-checkbox-large cekyes" value="1">
                                    <span></span>
                                </label>
                                <div class="input form-control">
                                    <span class="pc-form-field-label">
                                        Alamat Lengkap
                                    </span>
                                    <span class="pc-form-field-required text-danger" style="display:none">*</span>
                                    <i class="fas fa-cog pc-form-field-toggle pull-right" data-original-title="null" onclick="editField($(this).parents('.pc-form-field').first().attr('id'))"></i><!---->
                                </div>
                            </div>
                            <div class="pc-form-field-control btn-group border radius-6">
                                <button type="button" class="btn btn-light" data-original-title="null">
                                    <i class="fas fa-sort"></i>
                                </button>
                            </div>
                        </div>
                        <div class="pc-form-field d-flex" data-noreq="false" id="<?=md5(uniqid(rand(), true))?>">
                            <input type="hidden" name="fieldid[]" class="fieldid" value="0" />
                            <input type="hidden" name="fieldmodel[]" class="fieldmodel" value="stat" />
                            <input type="hidden" name="fieldsambung[]" class="fieldsambung" value="0" />
                            <input type="hidden" name="fieldtype[]" class="fieldtype" value="1" />
                            <input type="hidden" name="field[]" class="field" value="1" />
                            <input type="hidden" name="fieldrequired[]" class="fieldrequired" value="1" />
                            <input type="hidden" name="fieldopsi[]" value="" />
                            <input type="hidden" name="fieldlabel[]" class="fieldlabel" value="Alamat Email" />
                            <input type="hidden" name="fieldplaceholder[]" class="fieldplaceholder" value="Alamat Email" />
                            <div class="pc-form-field-input form-inline">
                                <label>
                                    <input type="hidden" name="fieldactive[]" class="cekno" value="0">
                                    <input type="checkbox" name="fieldactive[]" class="input-checkbox input-checkbox-large cekyes" value="1">
                                    <span></span>
                                </label>
                                <div class="input form-control">
                                    <span class="pc-form-field-label">
                                        Alamat Email
                                    </span>
                                    <span class="pc-form-field-required text-danger" style="display:none">*</span>
                                    <i class="fas fa-cog pc-form-field-toggle pull-right" data-original-title="null" onclick="editField($(this).parents('.pc-form-field').first().attr('id'))"></i><!---->
                                </div>
                            </div>
                            <div class="pc-form-field-control btn-group border radius-6">
                                <button type="button" class="btn btn-light" data-original-title="null">
                                    <i class="fas fa-sort"></i>
                                </button>
                            </div>
                        </div>
                        <?php 
                            }else{
                                $this->db->where("formid",$id);
                                $this->db->order_by("urutan","ASC");
                                $fm = $this->db->get("formorder_detail");
                                foreach($fm->result() as $f){
                                    $unique = md5(uniqid(rand(), true));
                        ?>
                        <div class="pc-form-field d-flex" data-noreq="false" id="<?=$unique?>">
                            <input type="hidden" name="fieldid[]" class="fieldid" value="<?=$f->id?>" />
                            <input type="hidden" name="fieldmodel[]" class="fieldmodel" value="<?=$f->model?>" />
                            <input type="hidden" name="fieldsambung[]" class="fieldsambung" value="<?=$f->sambung?>" />
                            <input type="hidden" name="fieldtype[]" class="fieldtype" value="<?=$f->type?>" />
                            <input type="hidden" name="field[]" class="field" value="<?=$f->field?>" />
                            <input type="hidden" name="fieldrequired[]" class="fieldrequired" value="<?=$f->required?>" />
                            <input type="hidden" name="fieldopsi[]" class="fieldopsi" value="<?=$f->opsi?>" />
                            <input type="hidden" name="fieldlabel[]" class="fieldlabel" value="<?=$f->label?>" />
                            <input type="hidden" name="fieldplaceholder[]" class="fieldplaceholder" value="<?=$f->placeholder?>" />
                            <?php if($f->model == "stat" AND $f->sambung > 0){ ?>
                            <input type="hidden" name="fieldactive[]" value="1">
                            <?php } ?>
                            <div class="pc-form-field-input form-inline">
                                <label>
                                    <?php if($f->model != "stat" || ($f->model == "stat" AND $f->sambung == 0)){ ?>
                                    <input type="hidden" name="fieldactive[]" class="cekno" value="0" <?php if($f->status == 1){ echo "disabled='true'"; } ?>>
                                    <input type="checkbox" name="fieldactive[]" class="input-checkbox input-checkbox-large cekyes" value="1" <?php if($f->status == 1){ echo "checked='true'"; } ?>>
                                    <span></span>
                                    <?php } ?>
                                </label>
                                <div class="input form-control">
                                    <span class="pc-form-field-label">
                                        <?=$f->label?>
                                    </span>
                                    <span class="pc-form-field-required text-danger" <?php if($f->required == 0){ echo 'style="display:none"'; }?>>*</span>
                                    <i class="fas fa-cog pc-form-field-toggle pull-right" data-original-title="null" onclick="editField($(this).parents('.pc-form-field').first().attr('id'))"></i><!---->
                                    <?php if($f->model != "stat"){ ?>
                                    <i class="fas fa-trash text-danger pc-form-field-toggle pull-right m-r-8" data-original-title="null" onclick="hapusField('<?=$f->id?>','<?=$unique?>')"></i>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="pc-form-field-control btn-group border radius-6">
                                <button type="button" class="btn btn-light" data-original-title="null">
                                    <i class="fas fa-sort"></i>
                                </button>
                            </div>
                        </div>
                        <?php 
                                }
                            }
                        ?>
                    </div>
                    <div id="hapusfield" style="display:none"></div>
                    <div class="text-right p-t-8 p-b-12">
                        <button type="button" class="btn btn-primary" onclick="addCF()"><i class="fas fa-plus"></i> Add Custom Field</button>
                    </div>
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Dropship
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="dropship" class="form-check-input" value="1" <?php if($r->garansi1 == 1){ echo "checked"; } ?> >
                            <span class="form-check-sign">Enable dropship to chart</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Button
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="tema" id="tema" value="<?=$r->button_tema?>" />
                    <input type="hidden" name="temawarna" id="warna" value="<?=$r->button_warna?>" />
                    <div class="font-bold m-b-10">Customize Buy Button</div>
                    <div class="m-b-12 col-8 p-lr-0">
                        <button type="button" class="btn btn-lg btn-block text-light" id="cusbtn" style="background-color:#2980b9;"><?=$btntext?></button>
                    </div>
                    <div class="btn-group g-warna col-12 m-lr-0 form-group m-b-10 col-md-6" role="group">
                        <?php 
                            $light = ($r->button_tema < 2) ? "btn-success" : "btn-light";
                            $dark = ($r->button_tema == 2) ? "btn-success" : "btn-light";
                        ?>
                        <button id="light" onclick="$('.tema1').show();$('.tema2').hide();$('#tema').val(1);$('.g-warna button').removeClass('btn-success');$('.g-warna button').addClass('btn-light');$(this).removeClass('btn-light');$(this).addClass('btn-success');" type="button" style="border: 1px solid #bbb;" class="col-6 btn btn-sm <?=$light?>"><b>GRADIENT</b></button>
                        <button id="dark" onclick="$('.tema1').hide();$('.tema2').show();$('#tema').val(2);$('.g-warna button').removeClass('btn-success');$('.g-warna button').addClass('btn-light');$(this).removeClass('btn-light');$(this).addClass('btn-success');" type="button" style="border: 1px solid #bbb;" class="col-6 btn btn-sm <?=$dark?>"><b>FLAT</b></button>
                    </div>
                    <div class="row m-lr-0 m-b-20" style="align-items:center;">
                        <?php 
                            $dis = ($r->button_tema < 2) ? "" : "style='display:none;'";
                            $tema = $this->func->tema(null,1);
                            for($i=0; $i<count($tema); $i++){
                                $active = ($r->button_tema == 1 AND $r->button_warna == $i) ? "active" : "";
                        ?>
                        <div class="p-all-8 tema1" <?=$dis?>><div class="pilihwarna text-center <?=$active?>" onclick="$('#warna').val(<?=$i?>);$('.pilihwarna.active').removeClass('active');$(this).addClass('active');$('#cusbtn').css('background-image','<?=$tema[$i]['hover']?>');$('.fp-btn-text').css('background-image','<?=$tema[$i]['hover']?>');" style="background-image:<?=$tema[$i]["hover"]?>;"><i class="fas fa-check"></i></div></div>
                        <?php } ?>

                        <?php 
                            $dis = ($r->button_tema == 2) ? "" : "style='display:none;'";
                            $tema = $this->func->tema(null,2);
                            for($i=0; $i<count($tema); $i++){
                                $active = ($r->button_tema == 2 AND $r->button_warna == $i) ? "active" : "";
                        ?>
                        <div class="p-all-8 tema2" <?=$dis?>><div class="pilihwarna text-center <?=$active?>" onclick="$('#warna').val(<?=$i?>);$('.pilihwarna.active').removeClass('active');$(this).addClass('active');$('#cusbtn').css('background-image','<?=$tema[$i]['hover']?>');$('.fp-btn-text').css('background-image','<?=$tema[$i]['hover']?>');" style="background-image:<?=$tema[$i]["hover"]?>;"><i class="fas fa-check"></i></div></div>
                        <?php } ?>
                    </div>
                    <div class="form-group m-b-5">
                        <label>Text Button</label>
                        <input type="text" name="button_text" class="form-control" value="<?=$btntext?>" onkeyup="$('#cusbtn').html($(this).val());$('.fp-btn-text').html($(this).val())"/>
                    </div>
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Description
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Add description to explain product</label>
                        <textarea class="form-control" id="summernote" name="deskripsi"><?=$r->deskripsi?></textarea>
                    </div>
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Bullet Point
                    </div>
                </div>
                <div class="card-body">
                    <div class="font-bold m-b-12">
                        Add Bullet Point
                    </div>
                    <div id="bullet">
                        <?php
                            $this->db->where("formid",$id);
                            $bl = $this->db->get("formorder_bullet");
                            foreach($bl->result() as $b){
                        ?>
                        <div class="row m-b-12">
                            <div class="col-10">
                                <input type="text" placeholder="type here..." name="bullet[]" class="form-control bullet-form" value="<?=$b->isi?>" onkeyup="updateBullet()" />
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-danger" onclick="$(this).parents('.row').first().remove();updateBullet()"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <button type="button" onclick="addBullet()" class="btn btn-primary m-b-5"><i class="fas fa-plus"></i> Add Bullet</button>
                </div>
            </div>
            <div class="card m-b-20">
                <div class="card-header">
                    <div class="card-title">
                        Summary
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="summary" class="form-check-input summary" value="1" <?php if($r->id == 0 || $r->summary == 1){ echo "checked"; } ?> >
                            <span class="form-check-sign">Show Order Summary</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card m-b-40">
                <div class="card-header">
                    <div class="card-title">
                        Metode Pembayaran
                    </div>
                </div>
                <div class="card-body">
                    <?php
                        $bayar = ($r->pembayaran == "") ? [2] : explode("|",$r->pembayaran);
                    ?>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="pembayaran[]" class="form-check-input pembayaran-cod" value="1" <?php if(in_array(1,$bayar)){ echo "checked"; } if($set->payment_cod == 0){ echo "disabled"; } ?> >
                            <span class="form-check-sign">Bayar Ditempat (COD)</span>
                            <?php if($set->payment_cod == 0){ echo "<small class='text-danger'><i>[aktifkan dulu di menu pengaturan]</i></small>"; } ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="pembayaran[]" class="form-check-input pembayaran-transfer" value="2" <?php if(in_array(2,$bayar)){ echo "checked"; if($set->payment_transfer == 0){ echo "disabled"; } } ?> >
                            <span class="form-check-sign">Transfer Manual</span>
                            <?php if($set->payment_transfer == 0){ echo "<small class='text-danger'><i>[aktifkan dulu di menu pengaturan]</i></small>"; } ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="pembayaran[]" class="form-check-input pembayaran-tripay" value="3" <?php if(in_array(3,$bayar)){ echo "checked"; if($set->payment_tripay == 0){ echo "disabled"; } } ?> >
                            <span class="form-check-sign">Tripay</span>
                            <?php if($set->payment_tripay == 0){ echo "<small class='text-danger'><i>[aktifkan dulu di menu pengaturan]</i></small>"; } ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="pembayaran[]" class="form-check-input pembayaran-midtrans" value="4" <?php if(in_array(4,$bayar)){ echo "checked"; if($set->payment_midtrans == 0){ echo "disabled"; } } ?> >
                            <span class="form-check-sign">Midtrans</span>
                            <?php if($set->payment_midtrans == 0){ echo "<small class='text-danger'><i>[aktifkan dulu di menu pengaturan]</i></small>"; } ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="pembayaran[]" class="form-check-input pembayaran-xendit" value="5" <?php if(in_array(5,$bayar)){ echo "checked"; if($set->payment_xendit == 0){ echo "disabled"; } } ?> >
                            <span class="form-check-sign">Xendit</span>
                            <?php if($set->payment_xendit == 0){ echo "<small class='text-danger'><i>[aktifkan dulu di menu pengaturan]</i></small>"; } ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="sticky">
                <div class="row m-b-20 btn-view">
                    <button type="button" class="btn btn-default col-6 desktop" onclick="$('.fp.mobile').hide();$('.fp.desktop').slideDown();$('.btn-view .desktop').removeClass('btn-light');$('.btn-view .desktop').addClass('btn-default');$('.btn-view .mobile').removeClass('btn-default');$('.btn-view .mobile').addClass('btn-light');"><i class="fas fa-desktop"></i> Desktop</button>
                    <button type="button" class="btn btn-light col-6 mobile" onclick="$('.fp.mobile').slideDown();$('.fp.desktop').hide();$('.btn-view .mobile').removeClass('btn-light');$('.btn-view .mobile').addClass('btn-default');$('.btn-view .desktop').removeClass('btn-default');$('.btn-view .desktop').addClass('btn-light');"><i class="fas fa-mobile-alt"></i> Mobile</button>
                </div>
                <div class="fp desktop">
                    <?php
                        $fp_header = ($r->header != "") ? $foto : "";
                        $fp_header_active = ($r->header != "") ? true : false;
                        $produk_active = ($r->id == 0 || ($r->id > 0 AND $r->showproduct == 1)) ? true : false;
                    ?>
                    <div class="text-center m-b-10 fp-header" <?php if(!$fp_header_active){ echo "style='display:none'"; } ?>>
                        <img src="<?=base_url("cdn/uploads/".$r->header)?>" style="max-width:100%;max-height:40px;" />
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="section m-b-8">
                                <div class="font-bold m-b-8 fp-produk" <?php if(!$produk_active){ echo "style='display:none'"; } ?>>Product</div>
                                <div class="row m-b-6 fp-produk">
                                    <div class="col-3">
                                        <div class="foto-produk" style="background-image:url('<?=$this->func->getFoto($prod->id)?>')"></div>
                                    </div>
                                    <div class="col-9 m-l--15">
                                        <div class="m-b-12"><?=$prod->nama?></div>
                                        <div class="font-bold">Rp <?=$this->func->formUang($prod->harga)?></div>
                                    </div>
                                </div>
                                <div class="fp-deskripsi"><?=$r->deskripsi?></div>
                            </div>
                            <div class="section m-b-8">
                                <div class="font-bold m-b-12">Recipient Form</div>
                                <div class="fp-form">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <?php
                                $this->db->where("formid",$r->id);
                                $dbb = $this->db->get("formorder_bullet");
                            ?>
                            <div class="section m-b-8 fp-wyg" <?php if($dbb->num_rows() == 0){ echo "style='display:none'"; } ?>>
                                <div class="font-bold m-b-8">What You Get?</div>
                                <?php
                                    foreach($dbb->result() as $rb){
                                ?>
                                <div class="row m-b-6 fp-wyg-item">
                                    <div class="col-2"><i class="fas fa-check text-success"></i></div>
                                    <div class="col-10 m-l--12"><?=$rb->isi?></div>
                                </div>
                                <?php
                                    }
                                ?>
                            </div>
                            <div class="section m-b-8">
                                <div class="fp-summary m-b-12" <?php if($r->id > 0 AND $r->summary == 0){ echo "style='display:none'"; } ?>>
                                    <div class="font-bold m-b-8">Order Summary</div>
                                    <div class="row">
                                        <div class="col-6">Total</div>
                                        <div class="col-6 text-right font-bold text-primary"><?=$this->func->formUang($prod->harga)?></div>
                                    </div>
                                </div>
                                <div class="fp-pembayaran m-b-20">
                                    <div class="font-bold m-b-8">Metode Pembayaran</div>
                                    <div class="m-b-8">
                                        <div class="fp-pembayaran-item cod" <?php if(!in_array(1,$bayar)){ echo "style='display:none'"; } ?>>
                                            BAYAR DI TEMPAT
                                        </div>
                                        <div class="fp-pembayaran-item transfer" <?php if(!in_array(2,$bayar)){ echo "style='display:none'"; } ?>>
                                            TRANSFER MANUAL
                                        </div>
                                        <div class="fp-pembayaran-item tripay" <?php if(!in_array(3,$bayar)){ echo "style='display:none'"; } ?>>
                                            OTOMATIS by Tripay
                                        </div>
                                        <div class="fp-pembayaran-item midtrans" <?php if(!in_array(4,$bayar)){ echo "style='display:none'"; } ?>>
                                            OTOMATIS by Midtrans
                                        </div>
                                        <div class="fp-pembayaran-item xendit" <?php if(!in_array(5,$bayar)){ echo "style='display:none'"; } ?>>
                                            OTOMATIS by Xendit
                                        </div>
                                    </div>
                                </div>
                                <div class="m-b-30">
                                    <button type="button" class="btn btn-block fp-btn-text text-light" style="background-color:#2980b9;"><?=$btntext?></button>
                                </div>
                                <div class="row">
                                    <div class="col-6 fp-grs2 m-lr-auto">
                                        <?php
                                            $src = [1=>"jaminan-kepuasan.png","satisfaction-guaranteed.png"];
                                            foreach($src as $k => $v){
                                        ?>
                                        <img src="<?=base_url("assets/images/order_label/".$v)?>" class="fp-gs2 fp-gs2-<?=$k?>" style="max-width:100%;max-height:60px;<?php if($r->garansi2 != $k){ echo "display:none"; } ?>" />
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 fp-grs1 m-lr-auto">
                                        <?php
                                            $src = [1=>"transaksi-aman.png","secured-payment.png"];
                                            foreach($src as $k => $v){
                                        ?>
                                        <img src="<?=base_url("assets/images/order_label/".$v)?>" class="fp-gs1 fp-gs1-<?=$k?>" style="max-width:100%;max-height:60px;<?php if($r->garansi1 != $k){ echo "display:none"; } ?>" />
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fp mobile"  style="display:none;">
                    <div class="fp-wrap">
                        <?php
                            $fp_header = ($r->header != "") ? $foto : "";
                            $fp_header_active = ($r->header != "") ? true : false;
                            $produk_active = ($r->id == 0 || ($r->id > 0 AND $r->showproduct == 1)) ? true : false;
                        ?>
                        <div class="text-center m-b-10 fp-header" <?php if(!$fp_header_active){ echo "style='display:none'"; } ?>>
                            <img src="<?=base_url("cdn/uploads/".$r->header)?>" style="max-width:100%;max-height:40px;" />
                        </div>
                        <div class="section m-b-8">
                            <div class="font-bold m-b-8 fp-produk" <?php if(!$produk_active){ echo "style='display:none'"; } ?>>Product</div>
                            <div class="row m-b-6 fp-produk">
                                <div class="col-3">
                                    <div class="foto-produk" style="background-image:url('<?=$this->func->getFoto($prod->id)?>')"></div>
                                </div>
                                <div class="col-9 m-l--15">
                                    <div class="m-b-12"><?=$prod->nama?></div>
                                    <div class="font-bold">Rp <?=$this->func->formUang($prod->harga)?></div>
                                </div>
                            </div>
                            <div class="fp-deskripsi"><?=$r->deskripsi?></div>
                        </div>
                        <div class="section m-b-8">
                            <div class="font-bold m-b-12">Recipient Form</div>
                            <div class="fp-form">
                            </div>
                        </div>
                        <?php
                            $this->db->where("formid",$r->id);
                            $dbb = $this->db->get("formorder_bullet");
                        ?>
                        <div class="section m-b-8 fp-wyg" <?php if($dbb->num_rows() == 0){ echo "style='display:none'"; } ?>>
                            <div class="font-bold m-b-8">What You Get?</div>
                            <?php
                                foreach($dbb->result() as $rb){
                            ?>
                            <div class="row m-b-6 fp-wyg-item">
                                <div class="col-2"><i class="fas fa-check text-success"></i></div>
                                <div class="col-10 m-l--12"><?=$rb->isi?></div>
                            </div>
                            <?php
                                }
                            ?>
                        </div>
                        <div class="section m-b-8">
                            <div class="fp-summary m-b-12" <?php if($r->id > 0 AND $r->summary == 0){ echo "style='display:none'"; } ?>>
                                <div class="font-bold m-b-8">Order Summary</div>
                                <div class="row">
                                    <div class="col-6">Total</div>
                                    <div class="col-6 text-right font-bold text-primary"><?=$this->func->formUang($prod->harga)?></div>
                                </div>
                            </div>
                            <div class="fp-pembayaran m-b-20">
                                <div class="font-bold m-b-8">Metode Pembayaran</div>
                                <div class="m-b-8">
                                    <div class="fp-pembayaran-item cod" <?php if(!in_array(1,$bayar)){ echo "style='display:none'"; } ?>>
                                        BAYAR DI TEMPAT
                                    </div>
                                    <div class="fp-pembayaran-item transfer" <?php if(!in_array(2,$bayar)){ echo "style='display:none'"; } ?>>
                                        TRANSFER MANUAL
                                    </div>
                                    <div class="fp-pembayaran-item tripay" <?php if(!in_array(3,$bayar)){ echo "style='display:none'"; } ?>>
                                        OTOMATIS by Tripay
                                    </div>
                                    <div class="fp-pembayaran-item midtrans" <?php if(!in_array(4,$bayar)){ echo "style='display:none'"; } ?>>
                                        OTOMATIS by Midtrans
                                    </div>
                                    <div class="fp-pembayaran-item xendit" <?php if(!in_array(5,$bayar)){ echo "style='display:none'"; } ?>>
                                        OTOMATIS by Xendit
                                    </div>
                                </div>
                            </div>
                            <div class="m-b-30">
                                <button type="button" class="btn btn-block fp-btn-text text-light" style="background-color:#2980b9;"><?=$btntext?></button>
                            </div>
                            <div class="row">
                                <div class="col-6 fp-grs2 m-lr-auto">
                                    <?php
                                        $src = [1=>"jaminan-kepuasan.png","satisfaction-guaranteed.png"];
                                        foreach($src as $k => $v){
                                    ?>
                                    <img src="<?=base_url("assets/images/order_label/".$v)?>" class="fp-gs2 fp-gs2-<?=$k?>" style="max-width:100%;max-height:60px;<?php if($r->garansi2 != $k){ echo "display:none"; } ?>" />
                                    <?php } ?>
                                </div>
                                <div class="col-6 fp-grs1 m-lr-auto">
                                    <?php
                                        $src = [1=>"transaksi-aman.png","secured-payment.png"];
                                        foreach($src as $k => $v){
                                    ?>
                                    <img src="<?=base_url("assets/images/order_label/".$v)?>" class="fp-gs1 fp-gs1-<?=$k?>" style="max-width:100%;max-height:60px;<?php if($r->garansi1 != $k){ echo "display:none"; } ?>" />
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card saveproduk-imp">
        <div class="card-body text-right m-tb-12">
            <button type="submit" class="btn btn-primary"><i class="la la-check-circle"></i> Simpan</button>
            <button type="reset" class="btn btn-warning"><i class="la la-refresh"></i> Reset</button>
            <button type="button" onclick="history.back()" class="btn btn-danger"><i class="la la-times"></i> Batal</button>
        </div>
    </div>
</form>
<div id="addbullet" style="display:none;">
    <div class="row m-b-12">
        <div class="col-10">
            <input type="text" placeholder="type here..." name="bullet[]" class="form-control bullet-form" onkeyup="updateBullet()" />
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-danger" onclick="$(this).parents('.row').first().remove();updateBullet()"><i class="fas fa-trash-alt"></i></button>
        </div>
    </div>
</div>
<div id="addcf" style="display:none;">
    <div class="pc-form-field d-flex">
        <input type="hidden" name="fieldid[]" class="fieldid" value="0" />
        <input type="hidden" name="fieldmodel[]" class="fieldmodel" value="dyn" />
        <input type="hidden" name="fieldsambung[]" class="fieldsambung" value="0" />
        <input type="hidden" name="fieldtype[]" class="fieldtype" value="1" />
        <input type="hidden" name="field[]" class="field" value="1" />
        <input type="hidden" name="fieldrequired[]" class="fieldrequired" value="1" />
        <input type="hidden" name="fieldopsi[]" class="fieldopsi" value="" />
        <input type="hidden" name="fieldlabel[]" class="fieldlabel" value="Alamat Lengkap" />
        <input type="hidden" name="fieldplaceholder[]" class="fieldplaceholder" value="Alamat Lengkap" />
        <div class="pc-form-field-input form-inline">
            <label>
                <input type="hidden" name="fieldactive[]" class="cekno" value="0">
                <input type="checkbox" name="fieldactive[]" class="input-checkbox input-checkbox-large cekyes" value="1">
                <span></span>
            </label>
            <div class="input form-control">
                <span class="pc-form-field-label">
                    Custom Form
                </span>
                <span class="pc-form-field-required text-danger" style="display:none">*</span>
                <i class="fas fa-cog pc-form-field-toggle pull-right editfield" data-original-title="null" onclick="editField($(this).parents('.pc-form-field').first().attr('id'))"></i>
                <i class="fas fa-trash text-danger pc-form-field-toggle pull-right m-r-8" data-original-title="null" onclick="$(this).parents('.pc-form-field').first().remove();updateForm()"></i>
            </div>
        </div>
        <div class="pc-form-field-control btn-group border radius-6">
            <button type="button" class="btn btn-light" data-original-title="null">
                <i class="fas fa-sort"></i>
            </button>
        </div>
    </div>
</div>
<div id="fieldhapus" style="display:none;">
    <input type="hidden" name="hapusfield[]" value="0">
</div>
<div id="fieldoption" style="display:none;">
    <div class="m-b-8">
        <input type="text" class="form-control" value="Option 1">
    </div>
</div>
<div id="addoption" style="display:none;">
    <div class="input-group m-b-8">
        <input type="text" class="form-control" value="Option">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" onclick="$(this).parents('.input-group').first().remove()"><i class="fas fa-trash text-danger"></i></button>
        </div>
    </div>
</div>
<div id="wygitem" style="display:none;">
    <div class="row m-b-6 fp-wyg-item">
        <div class="col-2"><i class="fas fa-check text-success"></i></div>
        <div class="col-10 m-l--12 isi"></div>
    </div>
</div>
<div id="formitem" style="display:none;">
    <div class="form-group fp-form-item">
        <label><span class='fp-form-label'></span>&nbsp;<sup class='fp-form-required text-danger font-bold'>*</sup></label>
        <input type="text" class="fp-form-input form-control" placeholder="" disabled/>
        <textarea rows="4" class="fp-form-area form-control" placeholder="" disabled></textarea>
        <select class="fp-form-select form-control" placeholder="" disabled></select>
    </div>
</div>

<script type="text/javascript">
	$(function(){
		loadText();
        updateForm();

        $(".cekyes").click(function(){
            if($(this).is(':checked')){
                $(this).parent("label").find(".cekno").attr("disabled",true);
            }else{
                $(this).parent("label").find(".cekno").attr("disabled",false);
            }
            updateForm();
        });
        $(".summary").click(function(){
            if($(this).is(':checked')){
                $(".fp-summary").show();
            }else{
                $(".fp-summary").hide();
            }
        });

        // PEMBAYARAN
        <?php
            $byr = [1=>"cod","transfer","tripay","midtrans","xendit"];
            foreach($byr as $k=>$v){
        ?>
        $(".pembayaran-<?=$v?>").click(function(){
            if($(this).is(':checked')){
                $(".fp-pembayaran-item.<?=$v?>").show();
            }else{
                $(".fp-pembayaran-item.<?=$v?>").hide();
            }
        });
        <?php } ?>

        $("#addfieldtype").change(function(){
            if($(this).val() == 1){
                $("#addfield .select").hide();
                $("#addfield .input").show();
            }else if($(this).val() == 3){
                $("#addfield .select").show();
                $("#addfield .input").hide();
            }else{
                $("#addfield .select").hide();
                $("#addfield .input").hide();
            }
        });

        $("#ceboxproduk").click(function(){
            if($(this).is(':checked')){
                $(".fp-produk").show();
            }else{
                $(".fp-produk").hide();
            }
        });

        <?php if($r->id == 0){ ?>
        $("#garansi1").val(1).trigger("change");
        $("#garansi2").val(1).trigger("change");
        $(".fp-gs1-1").show();
        $(".fp-gs2-1").show();
        <?php } ?>
        $("#garansi1").change(function(){
            if($(this).val() == 0){
                $(".fp-gs1").hide();
                $(".fp-grs1").hide();
            }else if($(this).val() > 0){
                $(".fp-grs1").show();
                $(".fp-gs1").hide();
                $(".fp-gs1-"+$(this).val()).show();
            }
        });
        $("#garansi2").change(function(){
            if($(this).val() == 0){
                $(".fp-gs2").hide();
                $(".fp-grs2").hide();
            }else if($(this).val() > 0){
                $(".fp-grs2").show();
                $(".fp-gs2").hide();
                $(".fp-gs2-"+$(this).val()).show();
            }
        });
	});

    function addBullet(){
        $('#bullet').append($('#addbullet').html());
        $("#wygitem .isi").html("-");
        $(".fp-wyg").show();
        $(".fp-wyg").append($("#wygitem").html());
    }
    function updateBullet(){
        $(".fp-wyg .fp-wyg-item").remove();
        $("#bullet .bullet-form").each(function(){
            console.log($(this).val());
            $("#wygitem .isi").html($(this).val());
            $(".fp-wyg").append($("#wygitem").html());
        });
    }
    function updateForm(){
        var field = ["text","text","date","number","email","url","file"];
        $(".fp-form .fp-form-item").remove();
        $("#customfield .pc-form-field").each(function(){
            console.log($(this).val());
            if($(".fieldrequired",this).val() == 1){
                $("#formitem .fp-form-required").show();
            }else{
                $("#formitem .fp-form-required").hide();
            }
            $("#formitem .fp-form-label").html($(".fieldlabel",this).val());
            
            if($(".fieldtype",this).val() == 1){
                $("#formitem .fp-form-input").show();
                $("#formitem .fp-form-area").hide();
                $("#formitem .fp-form-select").hide();
                $("#formitem .fp-form-input").attr("placeholder",$(".fieldplaceholder",this).val());
                $("#formitem .fp-form-input").attr("type",field[$(".field",this).val()]);
            }else if($(".fieldtype",this).val() == 2){
                $("#formitem .fp-form-input").hide();
                $("#formitem .fp-form-area").show();
                $("#formitem .fp-form-select").hide();
                $("#formitem .fp-form-area").attr("placeholder",$(".fieldplaceholder",this).val());
            }else if($(".fieldtype",this).val() == 3){
                $("#formitem .fp-form-input").hide();
                $("#formitem .fp-form-area").hide();
                $("#formitem .fp-form-select").show();
                $("#formitem .fp-form-select").attr("placeholder",$(".fieldplaceholder",this).val());
                var opsi = $(this).find(".fieldopsi").val();
                var myarr = opsi.split(";;");
                for (let i = 0; i < myarr.length; i++) {
                    //console.log(myarr[i]);
                    $("#formitem .fp-form-select").append("<option>"+myarr[i]+"</option>");
                }
            }

            if($(".cekyes",this).is(':checked')){
                $(".fp-form").append($("#formitem").html());
            }else{
                if($(".fieldmodel",this).val() == "stat" && $(".fieldsambung",this).val() > 0){
                    $(".fp-form").append($("#formitem").html());
                }
            }
        });
    }
    function addCF(){
        $("#addfield .input").show();
        $("#addfield .select").hide();
        $("#addfieldtype").val(1);
        $("#addfieldinputtype").val(1);
        $("#addfieldrequired").val(1);
        $("#addfieldplaceholder,#addfieldlabel").val("Custom Field");
        $("#addfieldoption").html($("#fieldoption").html());
        $("#addfield").modal();
    }
    function addCFOK(){
        $("#addcf .pc-form-field").attr("id",randomID());
        $("#addcf .fieldtype").val($("#addfieldtype").val());
        $("#addcf .field").val($("#addfieldinputtype").val());
        $("#addcf .fieldplaceholder").val($("#addfieldplaceholder").val());
        $("#addcf .fieldlabel").val($("#addfieldlabel").val());
        $("#addcf .pc-form-field-label").html($("#addfieldlabel").val());
        $("#addcf .fieldrequired").val($("#addfieldrequired").val());
        if($("#addfieldrequired").val() == 1){
            $("#addcf .pc-form-field-required").show();
        }else{
            $("#addcf .pc-form-field-required").hide();
        }
        $('#addcf .cekno').attr("disabled",true);
        $('#addcf .cekyes').attr("checked",true);
        var opsi = "";
        $('#addfieldoption .form-control').each(function(){
            if(opsi != ""){
                opsi = opsi + ";;" + $(this).val();
            }else{
                opsi = $(this).val();
            }
        });
        //console.log(opsi);
        $("#addcf .fieldopsi").val(opsi);
        $('#customfield').append($('#addcf').html());
        $("#addfield").modal('hide');
        updateForm();
    }
    function addOption(){
        $("#addfieldoption").append($("#addoption").html());
    }
    function addEditOption(){
        $("#editfieldoption").append($("#addoption").html());
    }
    function editField(field){
        //console.log(field);
        field = $("#"+field);
        $("#editfieldid").val(field.attr("id"));
        $("#editfieldlabel").val(field.find(".fieldlabel").val());
        $("#editfieldplaceholder").val(field.find(".fieldplaceholder").val());
        if(field.find(".fieldmodel").val() == "stat"){
            if(field.data('noreq') == true){
                $("#editfield .noreq").hide();
            }else{
                $("#editfield .noreq").show();
                $("#editfieldrequired").val(field.find(".fieldrequired").val());
            }
            $("#editfield .dyn").hide();
            $("#editfield").modal();
        }else{
            $("#editfield .dyn").show();
            if(field.find(".fieldtype").val() == 1){
                $("#editfield .select").hide();
                $("#editfield .input").show();
            }else if(field.find(".fieldtype").val() == 3){
                var opsi = field.find(".fieldopsi").val();
                var myarr = opsi.split(";;");
                for (let i = 0; i < myarr.length; i++) {
                    //console.log(myarr[i]);
                    $("#addoption .form-control").attr("value",myarr[i]);
                    $("#editfieldoption").append($("#addoption").html());
                }
                $("#editfield .select").show();
                $("#editfield .input").hide();
            }else{
                $("#editfield .select").hide();
                $("#editfield .input").hide();
            }
            $("#editfieldtype").val(field.find(".fieldtype").val());
            $("#editfieldinputtype").val(field.find(".field").val());
            $("#editfieldoption").html("");
            $("#editfield").modal();
        }
    }
    function saveField(){
        var field = $("#"+$("#editfieldid").val());
        field.find(".fieldlabel").val($("#editfieldlabel").val());
        field.find(".fieldplaceholder").val($("#editfieldplaceholder").val());
        field.find(".pc-form-field-label").html($("#editfieldlabel").val());
        if(field.find(".fieldmodel").val() == "stat"){
            if(field.data('noreq') != true){
                field.find(".fieldrequired").val($("#editfieldrequired").val());
            }
        }else{
            field.find(".fieldrequired").val($("#editfieldrequired").val());
            if($("#editfieldrequired").val() == 1){
                field.find(".pc-form-field-required").show();
            }else{
                field.find(".pc-form-field-required").hide();
            }
            field.find(".fieldtype").val($("#editfieldtype").val());
            field.find(".field").val($("#editfieldinputtype").val());
            var opsi = "";
            if($("#editfieldtype").val() == 3){
                $('#editfieldoption .form-control').each(function(){
                    if(opsi != ""){
                        opsi = opsi + ";;" + $(this).val();
                    }else{
                        opsi = $(this).val();
                    }
                });
            }
            //console.log(opsi);
            $("#addcf .fieldopsi").val(opsi);
        }
        $("#editfield").modal('hide');
        updateForm();
    }
	function hapusField(id,selec) {
        $("#fieldhapus input").attr("value",id);
        $("#hapusfield").append($("#fieldhapus").html());
		$("#"+selec).remove();
        updateForm();
	}

    function readURL(input) {
        var url = input.value;
        //console.log(input);
        var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
        if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg" || ext == "webp")) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#logo').attr('src', e.target.result);
                $('.fp-header img').attr('src', e.target.result);
                $('.fp-header').show();
            }
            reader.readAsDataURL(input.files[0]);
        }
        else{
            $('#logo').attr('src', '<?=base_url("cdn/uploads/no-image.png")?>');
        }
    }
	function loadText(){
		$('textarea#summernote').summernote('destroy');
		$('textarea#summernote').summernote({
			height: "40vh",
			callbacks: {
				onImageUpload: function(image) {
					uploadImage(image[0]);
				},
				onMediaDelete : function(target) {
					deleteImage(target[0].src);
				},
                onKeyup: function(e){
                    $(".fp-deskripsi").html($('textarea#summernote').val());
                }
			}
		});
	}
	function uploadImage(image) {
		var data = new FormData();
		data.append("image", image);
		$.ajax({
			url: "<?php echo site_url('atmin/editor/uploadimage')?>",
			cache: false,
			contentType: false,
			processData: false,
			data: data,
			type: "POST",
			success: function(url) {
				$('textarea#summernote').summernote("insertImage", url);
			},
			error: function(data) {
				console.log(data);
			}
		});
	}
	function deleteImage(src) {
		$.ajax({
			data: {src : src},
			type: "POST",
			url: "<?php echo site_url('atmin/editor/deleteimage')?>",
			cache: false,
			success: function(response) {
				console.log(response);
			}
		});
	}
    function randomID(){
        var length = 16;
        var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
        var result = '';
        for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
        return result;
    }
</script>
<script type="text/javascript">
</script>

<div class="modal fade" id="editfield" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body">
                <div class="row p-t-8">
                    <input type="hidden" id="editfieldid" />
                    <div class="col-6 m-b-12 dyn">
                        <label class="font-bold">Field Type</label>
                        <select class="form-control" id="editfieldtype">
                            <option value="1">Input</option>
                            <option value="2">Textarea</option>
                            <option value="3">Select</option>
                        </select>
                    </div>
                    <div class="col-6 m-b-12 input dyn">
                        <label class="font-bold">Input Type</label>
                        <select class="form-control" id="editfieldinputtype">
                            <option value="1">Text</option>
                            <option value="2">Date</option>
                            <option value="3">Number</option>
                            <option value="4">Email</option>
                            <option value="5">URL</option>
                            <option value="6">File</option>
                        </select>
                    </div>
                    <div class="col-6 m-b-12 noreq">
                        <label class="font-bold">Required</label>
                        <select class="form-control" id="editfieldrequired">
                            <option value="1">Yes (Required)</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-6 m-b-12">
                        <label class="font-bold">Label</label>
                        <input class="form-control" id="editfieldlabel" value="Custom Field"/>
                    </div>
                    <div class="col-6 m-b-12">
                        <label>Placeholder</label>
                        <input class="form-control" id="editfieldplaceholder" value="Custom Field"/>
                    </div>
                </div>
                <div class="select dyn" style="display:none;">
                    <hr/>
                    <div class="font-bold">Options</div>
                    <div id="editfieldoption">
                        <div class="m-b-8">
                            <input type="text" class="form-control" value="Option 1">
                        </div>
                    </div>
                    <div class="text-right">
                        <button onclick="addEditOption()" class="btn btn-primary">Add Option</button>
                    </div>
                </div>
			</div>
			<div class="modal-footer text-right">
                <button class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="saveField()">Save</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="addfield" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">Add Custom Field</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <div class="row">
                    <div class="col-6 m-b-12">
                        <label class="font-bold">Field Type</label>
                        <select class="form-control" id="addfieldtype">
                            <option value="1">Input</option>
                            <option value="2">Textarea</option>
                            <option value="3">Select</option>
                        </select>
                    </div>
                    <div class="col-6 m-b-12 input">
                        <label class="font-bold">Input Type</label>
                        <select class="form-control" id="addfieldinputtype">
                            <option value="1">Text</option>
                            <option value="2">Date</option>
                            <option value="3">Number</option>
                            <option value="4">Email</option>
                            <option value="5">URL</option>
                            <option value="6">File</option>
                        </select>
                    </div>
                    <div class="col-6 m-b-12">
                        <label class="font-bold">Required</label>
                        <select class="form-control" id="addfieldrequired">
                            <option value="1">Yes (Required)</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-6 m-b-12">
                        <label class="font-bold">Label</label>
                        <input class="form-control" id="addfieldlabel" value="Custom Field"/>
                    </div>
                    <div class="col-6 m-b-12">
                        <label class="font-bold">Placeholder</label>
                        <input class="form-control" id="addfieldplaceholder" value="Custom Field"/>
                    </div>
                </div>
                <div class="select" style="display:none;">
                    <hr/>
                    <div class="font-bold">Options</div>
                    <div id="addfieldoption">
                        <div class="m-b-8">
                            <input type="text" class="form-control" value="Option 1">
                        </div>
                    </div>
                    <div class="text-right">
                        <button onclick="addOption()" class="btn btn-primary">Add Option</button>
                    </div>
                </div>
			</div>
			<div class="modal-footer text-right">
                <button class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="addCFOK()">Save</button>
			</div>
		</div>
	</div>
</div>