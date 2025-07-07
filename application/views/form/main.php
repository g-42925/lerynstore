<div class="p-tb-40 m-t-30">
    <form action="<?=site_url("form/submit")?>" method="POST">
        <div class="m-lr-auto col-md-8">
            <div class="text-center m-b-20">
                <img src="<?=base_url("cdn/uploads/".$data->header)?>" style="max-width:100%;max-height:60px;" />
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="section p-all-24 m-b-30">
                        <div class="font-bold m-b-20">Product</div>
                        <div class="row m-b-20">
                            <div class="col-3">
                                <div class="foto-produk" style="background-image:url('<?=$this->func->getFoto($prod->id)?>')"></div>
                            </div>
                            <div class="col-9 m-l--15">
                                <div class="m-b-12"><?=$prod->nama?></div>
                                <div class="font-bold">Rp <?=$this->func->formUang($prod->harga)?></div>
                            </div>
                        </div>
                        <div class=""><?=$data->deskripsi?></div>
                    </div>
                    <div class="section p-all-24">
                        <div class="font-bold m-b-20">Recipient Form</div>
                        <?php
                            $this->db->where("formid",$data->id);
                            $this->db->where("status",1);
                            $db = $this->db->get("formorder_detail");
                            foreach($db->result() as $r){
                                $field = [1=>"text","date","number","email","url","file"];
                        ?>
                            <div class="form-group">
                                <label><?=$r->label?> <?php if($r->required == 1){ echo "<sup class='text-danger font-bold'>*</sup>"; } ?></label>
                                <input type="hidden" name="form[<?=$r->id?>][1]" value="<?=$r->sambung?>" />
                                <?php if($r->type == 1){ ?>
                                    <input type="<?=$field[$r->field]?>" class="form-control" placeholder="<?=$r->placeholder?>" name="form[<?=$r->id?>][0]" <?php if($r->required == 1){ echo "required"; } ?> />
                                <?php }elseif($r->type == 2){ ?>
                                    <textarea rows="4" class="form-control" placeholder="<?=$r->placeholder?>" name="form[<?=$r->id?>][0]" <?php if($r->required == 1){ echo "required"; } ?>></textarea>
                                <?php }elseif($r->type == 3){ ?>
                                    <select class="form-control" placeholder="<?=$r->placeholder?>" name="form[<?=$r->id?>][0]" <?php if($r->required == 1){ echo "required"; } ?>>
                                        <?php
                                            $pilihan = explode(";;",$r->opsi);
                                            foreach($pilihan as $k => $v){
                                                echo "<option value='".$v."'>".$v."</option>";
                                            }
                                        ?>
                                    </select>
                                <?php } ?>
                            </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="section p-all-24 m-b-30">
                        <div class="font-bold m-b-20">What You Get?</div>
                        <?php
                            $this->db->where("formid",$data->id);
                            $db = $this->db->get("formorder_bullet");
                            foreach($db->result() as $r){
                        ?>
                        <div class="row m-b-12">
                            <div class="col-2"><i class="fas fa-check text-success"></i></div>
                            <div class="col-10 m-l--20"><?=$r->isi?></div>
                        </div>
                        <?php
                            }
                        ?>
                    </div>
                    <div class="section p-all-24">
                        <div class="font-bold m-b-20">Order Summary</div>
                        <div class="row m-b-20">
                            <div class="col-6">Total</div>
                            <div class="col-6 text-right font-bold text-primary"><?=$this->func->formUang($prod->harga)?></div>
                        </div>
                        <div class="m-b-30">
                            <button type="submit" class="btn btn-primary btn-block"><?=$data->button_text?></button>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <?php
                                    $src = [1=>"jaminan-kepuasan.png","satisfaction-guaranteed.png"];
                                    $src = $src[$data->garansi2];
                                ?>
                                <img src="<?=base_url("assets/images/order_label/".$src)?>" style="max-width:100%;max-height:60px;" />
                            </div>
                            <div class="col-6">
                                <?php
                                    $src = [1=>"transaksi-aman.png","secured-payment.png"];
                                    $src = $src[$data->garansi1];
                                ?>
                                <img src="<?=base_url("assets/images/order_label/".$src)?>" style="max-width:100%;max-height:60px;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</form>
</div>