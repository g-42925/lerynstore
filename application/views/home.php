<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<?php
    $segmen = json_decode($set->homepage);
    $notin = [];
    foreach($segmen as $seg){
        $warna = ($seg->warna != "#ffffff") ? 'style="background-color:'.$seg->warna.'"' : "";
        if($seg->tipe == 1){
?>
    <!-- Slider -->	
    <div class="carousel slider p-tb-30" <?=$warna?>>
        <?php
            $this->db->where("tgl<=",date("Y-m-d H:i:s"));
            $this->db->where("tgl_selesai>=",date("Y-m-d H:i:s"));
            $this->db->where("tags",$seg->tags);
            $this->db->where("status",1);
            $this->db->order_by("tgl","DESC");
            $sld = $this->db->get("promo");
            if($sld->num_rows() > 0){
                foreach($sld->result() as $s){
        ?>
            <div class="slider-item" style="cursor:pointer;" data-onclick="<?=strip_tags($s->link)?>">
                <div class="wrap">
                    <img src="<?= base_url('cdn/promo/'.$s->gambar) ?>" />
                </div>
            </div>
        <?php
                }
            }
        ?>
    </div>
<?php
        }elseif($seg->tipe == 2){
            $this->db->where("tgl<=",date("Y-m-d H:i:s"));
            $this->db->where("tgl_selesai>=",date("Y-m-d H:i:s"));
            $this->db->where("tags",$seg->tags);
            $this->db->where("status",1);
            $this->db->order_by("tgl","ASC");
            $this->db->limit($seg->jumlah);
            $ikl = $this->db->get("promo");

            if($ikl->num_rows() > 0){
?>
    <section class="banner-iklans p-t-30 p-b-40" <?=$warna?>>
        <div class="container center">
            <div class="text-primary font-medium p-b-20">
                <h3 class="t-center">
                    <?=strtoupper(strtolower($seg->judul))?>
                </h3>
            </div>
            <div class="slide-iklan">
                <div class="swiper-wrapper">
                    <?php
                        foreach($ikl->result() as $iklan){
                    ?>
                        <div class="swiper-slide iklans m-b-20">
                            <a href="<?=strip_tags($iklan->link)?>">
                                <div class="iklan-wrap bg-foot">
                                    <div class="m-b-8"><img src="<?= base_url('cdn/promo/'.$iklan->gambar) ?>" /></div>
                                    <div class="font-bold m-b-8"><?=$iklan->caption?></div>
                                    <div class="fs-12 m-b-8"><?=$iklan->keterangan?></div>
                                </div>
                            </a>
                        </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </section>
<?php
            }
        }elseif($seg->tipe == 3){
            $this->db->limit(20);
            $db = $this->db->get("ppob_kategori");
            $this->db->where("tipe",2);
            $this->db->limit(20);
            $dbt = $this->db->get("ppob");
            if($set->digiflazz == 1 && ($db->num_rows() > 0 || $dbt->num_rows() > 0)){
?>
    <!-- PPOB -->
    <section class="banner p-tb-30" <?=$warna?>>
        <div class="container">
            <div class="sec-title p-b-20">
                <h2 class="t-center">
                    <?=$seg->judul?>
                </h2>
            </div>
            <div class="slide-kategori">
                <div class="swiper-wrapper">
                    <?php
                        $no = 1;
                        foreach($db->result() as $r){
                            $icon = ($r->icon) ? $r->icon : "default.png";
                    ?>
                        <div class="swiper-slide m-b-24 pointer" onclick="beliTopup(<?=$r->id?>)">
                            <div class="bg-foot radius-12 p-all-12">
                                <div class="cat-bg">
                                    <img src='<?=base_url("cdn/ppob/".$icon)?>'>
                                </div>
                            </div>
                            <div class="cat-nama line-clamp"><?=ucwords($r->nama)?></div>
                        </div>
                    <?php
                            $no++;
                        }
                        foreach($dbt->result() as $r){
                            $icon = ($r->icon) ? $r->icon : "default.png";
                    ?>
                        <div class="swiper-slide m-b-24 pointer" onclick="beliTagihan(<?=$r->id?>)">
                            <div class="bg-foot radius-12 p-all-12">
                                <div class="cat-bg">
                                    <img src='<?=base_url("cdn/ppob/".$icon)?>'>
                                </div>
                            </div>
                            <div class="cat-nama line-clamp"><?=ucwords($r->nama)?></div>
                        </div>
                    <?php
                            $no++;
                        }
                    ?>
                </div>
            </div>
            <!--
            <div class="row">
                <div class="col-md-6 m-b-12">
                    <div class="section p-all-12">
                        <div class="t-center font-medium text-primary m-b-8 fs-18">
                            TOP UP
                        </div>
                        <div class="row">
                            <?php
                                $no = 1;
                                foreach($db->result() as $r){
                                    $icon = ($r->icon) ? $r->icon : "default.png";
                            ?>
                                <div class="col-3 col-md-2 m-tb-8 cursor-pointer" onclick="beliTopup(<?=$r->id?>)">
                                    <div class="cat-bg">
                                        <img src='<?=base_url("cdn/ppob/".$icon)?>'>
                                    </div>
                                    <div class="cat-nama fs-12 line-clamp"><?=$r->nama?></div>
                                </div>
                            <?php
                                    $no++;
                                }
                            ?>
                            <?php
                                if($db->num_rows() == 11){
                                    $icon = "default.png";
                            ?>
                                <div class="col-3 col-md-2 m-tb-8 cursor-pointer" onclick="beliTopup(0)">
                                    <div class="cat-bg">
                                        <img src='<?=base_url("cdn/ppob/".$icon)?>'>
                                    </div>
                                    <div class="cat-nama fs-12 line-clamp">Produk Lainnya</div>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section p-all-12">
                        <div class="t-center font-medium text-primary m-b-8 fs-18">
                            TAGIHAN
                        </div>
                        <div class="row">
                            <?php
                                $no = 1;
                                foreach($dbt->result() as $r){
                                    $icon = ($r->icon) ? $r->icon : "default.png";
                            ?>
                                <div class="col-3 col-md-2 m-tb-8 cursor-pointer" onclick="beliTagihan(<?=$r->id?>)">
                                    <div class="cat-bg">
                                        <img src='<?=base_url("cdn/ppob/".$icon)?>'>
                                    </div>
                                    <div class="cat-nama fs-12 line-clamp"><?=$r->nama?></div>
                                </div>
                            <?php
                                    $no++;
                                }
                            ?>
                            <?php
                                if($dbt->num_rows() == 11){
                                    $icon = "default.png";
                            ?>
                                <div class="col-3 col-md-2 m-tb-8 cursor-pointer" onclick="beliTagihan(0)">
                                    <div class="cat-bg">
                                        <img src='<?=base_url("cdn/ppob/".$icon)?>'>
                                    </div>
                                    <div class="cat-nama fs-12 line-clamp">Produk Lainnya</div>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
                            -->
        </div>
    </section>
<?php
            }
        }elseif($seg->tipe == 4){
            if($set->link_playstore != ""){
?>
    <!-- Banner -->
    <section class="banner playstore-section p-tb-20" <?=$warna?>>
        <div class="container center">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="font-bold text-dark2">Belanja kini lebih mudah</h2>
                    <h5>Langsung dari handphone Anda, download aplikasinya sekarang!</h5>
                </div>
                <div class="col-md-4">
                    <a href="<?=$set->link_playstore?>" class="playstore">
                        <img src="<?=base_url("assets/images/playstore.png")?>" />
                    </a>
                    <div class="m-t-10 showsmall"></div>
                </div>
            </div>
        </div>
    </section>
<?php 
            }
        }elseif($seg->tipe == 5){
            $this->db->where("mulai <=",date("Y-m-d H:i:s"));
            $this->db->where("selesai >=",date("Y-m-d H:i:s"));
            $this->db->order_by("RAND()");
            //$this->db->limit($seg->jumlah);
            $db = $this->db->get("flashsale");
            if($db->num_rows() > 0){
?>
    <!-- FlashSale -->
    <section class="newproduct p-tb-30" <?=$warna?>>
        <div class="container">
            <div class="p-b-20">
                <div class="row align-center">
                    <div class="col-8">
                        <h3 class="font-bold text-primary">
                            <i class="fas fa-bolt text-warning"></i> &nbsp;<?=strtoupper(strtolower($seg->judul))?>
                        </h3>
                    </div>
                    <div class="col-4 text-right font-medium text-dark fs-18">
                        <a href="<?=site_url("flashsale")?>">Lihat Semua</a>
                    </div>
                </div>
            </div>

            <!-- Slide2 -->
            <div class="display-flex produk-wrap swiper">
                <div class="swiper-wrapper">
                    <?php
                        $totalproduk = 0;
                        $no =  1;
                        foreach($db->result() as $fs){
                            $lolos = true;
                            $r = $this->func->getProduk($fs->idproduk,"semua");
                            if($seg->kategori > 0){
                                $lolos = ($r->idcat != $seg->kategori) ? false : $lolos;
                            }
                            if($seg->brand > 0){
                                $lolos = ($r->brandid != $seg->brand) ? false : $lolos;
                            }
                            if($seg->jenis > 0){
                                $lolos = (($seg->jenis == 1 && $r->digital == 1) || ($seg->jenis == 2 && $r->digital == 0)) ? false : $lolos;
                            }

                            if($no <= $seg->jumlah && $lolos == true){
                                $notin[] = $fs->idproduk;
                                $totalstok = $fs->stok;

                                $totalproduk += 1;
                                $wishis = ($this->func->cekWishlist($r->id)) ? "active" : "";
                                $hargadapat = $fs->harga;
                                $diskon = $r->hargacoret > $hargadapat ? ($r->hargacoret-$hargadapat)/$r->hargacoret*100 : null;
                                $kota = ($r->gudang > 0) ? $this->func->getGudang($r->gudang,"idkab") : $set->kota;
                                $kota = $this->func->getKab($kota,"semua");
                                $kota = $kota->tipe." ".$kota->nama;
                                $fspersen = ($fs->terjual > 0) ? $fs->terjual / ($fs->stok + $fs->terjual) * 100 : 0;
                    ?>
                        <div class="col-8 col-md-4 col-lg-3 m-b-30 cursor-pointer produk-item swiper-slide">
                            <!-- Block2 -->
                            <div class="block2">
                                <!-- <div class="block2-wishlist" onclick="tambahWishlist(<?=$r->id?>,'<?=$r->nama?>')"><i class="fas fa-heart <?=$wishis?>"></i></div> -->
                                <?php if($r->digital == 1){ ?><div class="block2-digital bg-primary"><i class="fas fa-cloud"></i> digital</div><?php } ?>
                                <?php if($r->preorder == 1){ ?><div class="block2-digital bg-warning"><i class="fas fa-history"></i> preorder</div><?php } ?>
                                <div class="block2-img wrap-pic-w of-hidden pos-relative" style="background-image:url('<?=$this->func->getFoto($r->id,"utama")?>');" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'"></div>
                                <div class="block2-txt" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'">
                                    <a href="<?php echo site_url('produk/'.$r->url); ?>" class="block2-name dis-block p-b-5">
                                        <?=$r->nama?>
                                    </a>
                                    <div class="btn-block">
                                        <?php if($r->hargacoret > $hargadapat){ ?><span class="block2-price-coret">Rp. <?=$this->func->formUang($r->hargacoret)?></span><?php } ?>
                                        <?php if($diskon != null){ ?><span class="block2-label"><?=round($diskon,0)?>%</span><?php } ?>
                                    </div>
                                    <span class="block2-price p-r-5 font-medium">
                                        <?php 
                                            echo "Rp. ".$this->func->formUang($fs->harga);
                                        ?>
                                    </span>
                                </div>
                                <?php
                                    $label = json_decode($r->customlabel);
                                    if($label){
                                ?>
                                    <div class="block2-txt p-tb-2">
                                <?php
                                        $nos = 1;
                                        foreach($label as $lab){
                                ?>
                                    <div class="customlabel" style="color: <?=$lab->warna?>;background-color: <?=$lab->background?>;"><?=$lab->text?></div>
                                <?php
                                        }
                                ?>
                                    </div>
                                <?php
                                    }
                                ?>
                                <div class="block2-ulasan" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'">
                                    <div class="progress m-b-12">
                                        <div class="progress-bar bg-danger" style="width:<?=$fspersen?>%"><?php if($fspersen > 50){ echo "terjual ".$fs->terjual; } ?></div>
                                        <div class="progress-bar bg-light text-dark" style="width:<?=100-$fspersen?>%"><?php if($fspersen <= 50){ echo "terjual ".$fs->terjual; } ?></div>
                                    </div>
                                    <div class="text-center fs-13">Promo Berakhir Dalam</div>
                                    <div class="text-center font-bold text-warning">
                                        <div class="countdown" data-tgl="<?=$this->func->ubahTgl("Y-m-d H:i:s",$fs->selesai)?>">....</div>
                                    </div>
                                </div>
                                <div class="row m-lr-0">
                                    <button type="button" class="btn btn-sm btn-light btn-block p-all-12" onclick="addtocart(<?=$r->id?>)"><i class="fas fa-plus text-success"></i> keranjang</button>
                                </div>
                            </div>
                        </div>
                    <?php
                            $no++;
                            }
                        }
                                
                        if($totalproduk == 0){
                            echo "<div class='col-12 text-center m-tb-40'><h2><mark>Produk Kosong</mark></h2></div>";
                        }
                    ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>

        </div>
    </section>
<?php 
            }
        }elseif($seg->tipe == 6){
            if(count($notin) > 0){
                $this->db->where_not_in("id",$notin);
            }
            if($seg->jenis > 0){
                $jenis = ($seg->jenis == 1) ? 0 : 1;
                $this->db->where("digital",$jenis);
            }
            if($seg->kategori > 0){
                $this->db->where("idcat",$seg->kategori);
            }
            if($seg->brand > 0){
                $this->db->where("brandid",$seg->brand);
            }
            $this->db->where("stok >",0);
            $this->db->where("status",1);
            $this->db->limit($seg->jumlah);
            $this->db->order_by("RAND()");
            $db = $this->db->get("produk");
            $totalproduk = 0;
            if($db->num_rows() > 0){
?>
    <!-- New Product -->
    <section class="newproduct p-tb-30" <?=$warna?>>
        <div class="container">
            <div class="p-b-20">
                <div class="row align-center">
                    <div class="col-8">
                        <h3 class="font-bold text-primary">
                            <?=strtoupper(strtolower($seg->judul))?>
                        </h3>
                    </div>
                    <div class="col-4 text-right font-medium text-dark fs-18">
                        <a href="<?=site_url("shop")?>">Lihat Semua</a>
                    </div>
                </div>
            </div>

            <!-- Slide2 -->
            <div class="display-flex produk-wrap swiper">
                <div class="swiper-wrapper">
                    <?php
                        foreach($db->result() as $r){
                            $level = isset($_SESSION["lvl"]) ? $_SESSION["lvl"] : 0;
                            if($level == 5){
                                $result = $r->hargadistri;
                            }elseif($level == 4){
                                $result = $r->hargaagensp;
                            }elseif($level == 3){
                                $result = $r->hargaagen;
                            }elseif($level == 2){
                                $result = $r->hargareseller;
                            }else{
                                $result = $r->harga;
                            }
                            $ulasan = $this->func->getReviewProduk($r->id);
                            $ulasan['nilai'] = ($ulasan['nilai'] > 0) ? $ulasan['nilai'] : 5;

                            $this->db->where("idproduk",$r->id);
                            $dbv = $this->db->get("produkvariasi");
                            $totalstok = ($dbv->num_rows() > 0) ? 0 : $r->stok;
                            $hargs = 0;
                            $harga = array();
                            foreach($dbv->result() as $rv){
                                $totalstok += $rv->stok;
                                if($level == 5){
                                    $harga[] = $rv->hargadistri;
                                }elseif($level == 4){
                                    $harga[] = $rv->hargaagensp;
                                }elseif($level == 3){
                                    $harga[] = $rv->hargaagen;
                                }elseif($level == 2){
                                    $harga[] = $rv->hargareseller;
                                }else{
                                    $harga[] = $rv->harga;
                                }
                                $hargs += $rv->harga;
                            }

                            $totalproduk += 1;
                            $wishis = ($this->func->cekWishlist($r->id)) ? "active" : "";
                            $hargadapat = $hargs > 0 ? min($harga) : $result;
                            $diskon = $r->hargacoret > $hargadapat ? ($r->hargacoret-$hargadapat)/$r->hargacoret*100 : null;
                            $kota = ($r->gudang > 0) ? $this->func->getGudang($r->gudang,"idkab") : $set->kota;
                            $kota = $this->func->getKab($kota,"semua");
                            $kota = $kota->tipe." ".$kota->nama;
                            $terjual = $this->func->getTerjual($r->id);
                            $terjual = ($terjual >= 99) ? "99+" : $terjual;
                    ?>
                        <div class="col-8 col-md-4 col-lg-3 m-b-30 cursor-pointer produk-item swiper-slide">
                            <!-- Block2 -->
                            <div class="block2">
                                <!--<div class="block2-wishlist" onclick="tambahWishlist(<?=$r->id?>,'<?=$r->nama?>')"><i class="fas fa-heart <?=$wishis?>"></i></div>-->
                                <?php if($r->digital == 1){ ?><div class="block2-digital bg-primary"><i class="fas fa-cloud"></i> digital</div><?php } ?>
                                <?php if($r->preorder == 1){ ?><div class="block2-digital bg-warning"><i class="fas fa-history"></i> preorder</div><?php } ?>
                                <div class="block2-img wrap-pic-w of-hidden pos-relative" style="background-image:url('<?=$this->func->getFoto($r->id,"utama")?>');" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'"></div>
                                <div class="block2-txt" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'">
                                    <a href="<?php echo site_url('produk/'.$r->url); ?>" class="block2-name dis-block p-b-5">
                                        <?=$r->nama?>
                                    </a>
                                    <div class="btn-block">
                                        <?php if($r->hargacoret > $hargadapat){ ?><span class="block2-price-coret">Rp. <?=$this->func->formUang($r->hargacoret)?></span><?php } ?>
                                        <?php if($diskon != null){ ?><span class="block2-label"><?=round($diskon,0)?>%</span><?php } ?>
                                    </div>
                                    <span class="block2-price p-r-5 font-medium">
                                        <?php 
                                            if($hargs > 0){
                                                if(max($harga) > min($harga)){
                                                    echo "Rp. ".$this->func->formUang(min($harga))." - ".$this->func->formUang(max($harga));
                                                }else{
                                                    echo "Rp. ".$this->func->formUang(min($harga));
                                                }
                                            }else{
                                                echo "Rp. ".$this->func->formUang($result);
                                            }
                                        ?>
                                    </span>
                                </div>
                                <?php
                                    $label = json_decode($r->customlabel);
                                    if($label){
                                ?>
                                    <div class="block2-txt p-tb-2">
                                <?php
                                        $nos = 1;
                                        foreach($label as $lab){
                                ?>
                                    <div class="customlabel" style="color: <?=$lab->warna?>;background-color: <?=$lab->background?>;"><?=$lab->text?></div>
                                <?php
                                        }
                                ?>
                                    </div>
                                <?php
                                    }
                                ?>
                                <div class="block2-ulasan" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'">
                                    <span class="text-warning font-bold"><i class='fa fa-star'></i> <?=$ulasan['nilai']?></span> | 
                                    <span class="fs-14">terjual <?=$terjual?></span>
                                </div>
                                <div class="row m-lr-0">
                                    <button type="button" class="col-3 col-md-6 btn btn-sm btn-light p-all-12" onclick="tambahWishlist(<?=$r->id?>,'<?=$r->nama?>')"><i class="fas fa-heart text-danger"></i> <span class='hidesmall'>wishlist</span></button>
                                    <button type="button" class="col-9 col-md-6 btn btn-sm btn-light p-all-12" onclick="addtocart(<?=$r->id?>)"><i class="fas fa-shopping-basket text-success"></i> +keranjang</button>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                                
                        if($totalproduk == 0){
                            echo "<div class='col-12 text-center m-tb-40'><h2><mark>Produk Kosong</mark></h2></div>";
                        }
                    ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
<?php
            }
        }elseif($seg->tipe == 7){
?>
    <!-- Testismoni -->
    <section class="testismoni p-tb-30" <?=$warna?>>
        <div class="container">
            <div class="p-b-12">
                <h2 class="t-center font-bold text-primary">
                    <?=strtoupper(strtolower($seg->judul))?>
                </h2>
            </div>
            <div class="testimoni">
                <div class="m-r-12"></div>
            <?php
                $this->db->where("status",1);
                $this->db->limit($seg->jumlah);
                $db = $this->db->get("testimoni");
                foreach($db->result() as $r){
            ?>
                <div class="testimoni-item">
                    <div class="testimoni-wrap">
                        <div class="m-b-20 testimoni-komentar">" <?=$r->komentar?> "</div>
                        <div class="row m-lr-0">
                            <div class="col-3 p-lr-0">
                                <div class="testimoni-img" style="background-position:center center;background-image:url('<?=base_url("cdn/uploads/".$r->foto)?>');background-size:cover;"></div>
                            </div>
                            <div class="col-9 p-r-4">
                                <div class="font-bold text-primary fs-14 ellipsis"><?=$r->nama?></div>
                                <div class="fs-12"><?=$r->jabatan?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>
            </div>
        </div>
    </section>
<?php
        }elseif($seg->tipe == 8){
?>
    <!-- Blog Terbaru -->
    <div class="p-tb-30 blog-section" <?=$warna?>>
        <div class="container">
            <div class="row align-center">
                <div class="col-8">
                    <h3 class="font-bold text-primary">
                        <i class="fas fa-newspaper"></i> &nbsp;<?=strtoupper(strtolower($seg->judul))?>
                    </h3>
                </div>
                <div class="col-4 text-right font-medium text-dark fs-18">
                    <a href="<?=site_url("blog")?>">Lihat Semua</a>
                </div>
            </div>
            <div class="m-t-20 m-b-30 prod-thumb" style="justify-content:center;">
                <?php
                    $this->db->limit($seg->jumlah);
                    $this->db->order_by("tgl DESC");
                    $db = $this->db->get("blog");
                    
                    if($db->num_rows() > 0){
                        foreach($db->result() as $res){
                            $img = (file_exists(FCPATH."cdn/uploads/".$res->img)) ? base_url("cdn/uploads/".$res->img) : base_url("cdn/uploads/no-image.png");
                ?>
                    <div class="blog-wrap">
                        <div class="blog" onclick="window.location.href='<?=site_url('blog/'.$res->url)?>'">
                            <div class="img" style="background-image: url('<?=$img?>')"></div>
                            <div class="text">
                                <div class="titel">
                                    <?=$this->func->potong($res->judul,80,"...")?>
                                </div>
                                <div class="konten">
                                    <?=$this->func->ubahTgl("d M Y",$res->tgl)?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                        }
                    }else{
                        echo "
                            <div class='text-danger text-center p-tb-30'>
                                BELUM ADA POSTINGAN
                            </div>
                        ";
                    }
                ?>
            </div>
        </div>
    </div>
<?php
        }elseif($seg->tipe == 9){
            $this->db->order_by("RAND()");
            $this->db->limit(12);
            $db = $this->db->get("brand");
            if($db->num_rows() > 0){
?>
    <!-- Testismoni -->
    <section class="testismoni p-tb-30" <?=$warna?>>
        <div class="container">
            <h2 class="t-center font-bold text-primary m-b-0">
                <?=strtoupper(strtolower($seg->judul))?>
            </h2>
            <div class="brands">
            <?php
                foreach($db->result() as $r){
            ?>
                <div class="brands-wrap">
                    <div class="item bg-white">
                        <img src="<?=base_url("cdn/brand/".$r->icon)?>" alt="<?=$r->nama?>" title="<?=$r->nama?>">
                    </div>
                </div>
            <?php
                }
            ?>
            </div>
        </div>
    </section>
<?php
            }
        }elseif($seg->tipe == 10){
            $this->db->where("parent",0);
            $this->db->limit($seg->jumlah);
            $db = $this->db->get("kategori");
            if($db->num_rows() > 0){
?>
    <!-- Kategori -->
    <section class="kategori-wrapper p-tb-30" <?=$warna?>>
        <div class="container">
            <div class="row align-center m-b-20">
                <div class="col-8">
                    <h3 class="font-bold text-primary">
                        <i class="fas fa-bars-staggered"></i> &nbsp;<?=strtoupper(strtolower($seg->judul))?>
                    </h3>
                </div>
                <div class="col-4 text-right font-medium text-dark fs-18">
                    <a href="<?=site_url("home/kategori")?>">Lihat Semua</a>
                </div>
            </div>
            <div class="row hidesmall">
                <?php
                    $no = 1;
                    foreach($db->result() as $r){
                ?>
                    <div class="col-3 col-md-2 m-b-24 pointer" onclick="window.location.href='<?=site_url('kategori/'.$r->url)?>'">
                        <div class="bg-foot radius-12 p-all-12">
                            <div class="cat-bg">
                                <img src='<?=base_url("cdn/kategori/".$r->icon)?>'>
                            </div>
                        </div>
                        <div class="cat-nama"><?=ucwords($r->nama)?></div>
                    </div>
                <?php
                        $no++;
                    }
                ?>
            </div>
            <div class="slide-kategori showsmall">
                <div class="swiper-wrapper">
                    <?php
                        $no = 1;
                        foreach($db->result() as $r){
                    ?>
                        <div class="swiper-slide m-b-24 pointer" onclick="window.location.href='<?=site_url('kategori/'.$r->url)?>'">
                            <div class="bg-foot radius-12 p-all-12">
                                <div class="cat-bg">
                                    <img src='<?=base_url("cdn/kategori/".$r->icon)?>'>
                                </div>
                            </div>
                            <div class="cat-nama"><?=ucwords($r->nama)?></div>
                        </div>
                    <?php
                            $no++;
                        }
                    ?>
                </div>
            </div>
        </div>
    </section>
<?php
            }
        }
    }
?>

<!-- POPUP BANNER -->
<?php
    $this->db->where("tgl<=",date("Y-m-d H:i:s"));
    $this->db->where("tgl_selesai>=",date("Y-m-d H:i:s"));
    $this->db->where("tags","popupbanner");
    $this->db->where("status",1);
    $this->db->order_by("RAND()");
    $this->db->limit(1);
    $ikl = $this->db->get("promo");

    if($ikl->num_rows() > 0){
        foreach($ikl->result() as $iklan){
?>
    <div class="modal popup-banner" data-backdrop="static" data-keyboard="false" id="popBanner" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-all-0">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                    <a href="<?=$iklan->link?>">
                        <img src="<?= base_url('cdn/promo/'.$iklan->gambar) ?>" style="width:100%;" />
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){
            $("#popBanner").modal();
        });
    </script>
<?php
        }
    }
?>

<?php if($set->notif_booster > 0){ ?>
<div id="toaster" class="toaster row" style="display:none;">
    <div class="col-3 img p-lr-6"><img id="toast-foto" src="<?=base_url("cdn/uploads/520200116140232.jpg")?>" /></div>
    <div class="col-9 p-lr-6">
        <b id="toast-user">USER</b> telah membeli<br/>
        <b id="toast-produk">Nama Produknya</b>
        <small class="btn-block"><i class="fas fa-check-circle text-success"></i> &nbsp;verified by <b><?=$set->nama?></b></small>
    </div>
</div>
<?php } ?>
<script type="text/javascript">
    $(function(){
        $('.carousel .slick-slide').on('click', function(ev){
            var slideIndex = $(ev.currentTarget).data('slick-index');
            var current = $('.carousel').slick('slickCurrentSlide');
            if(slideIndex == current){
                window.location.href= $(this).data("onclick");
            }else{
                $('.carousel').slick('slickGoTo',parseInt(slideIndex));
            }
        });
        
        $('.swiper').each(function(){
            const swiper = new Swiper(this, {
                loop: false,
                slidesPerView: 1.8,
                breakpoints: {
                    480: {
                    slidesPerView: 4.2,
                    },
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoHeight:true,
            });

            setTimeout(() => {
                const wrapper = swiper.wrapperEl;
                console.log(wrapper.clientHeight);
                const SLIDER = swiper.slides;
                SLIDER.forEach(function(slide) {
                    slide.style.height = wrapper.clientHeight + "px";
                });
            }, 1000);
        });
        const swiper2 = new Swiper('.slide-kategori', {
            loop: false,
            slidesPerView: 3.3,
            spaceBetween: 8,
            breakpoints: {
                600: {
                slidesPerView: 8,
                spaceBetween: 12,
                },
            },
        });

        $('.slide-iklan').each(function(){
            const swiper = new Swiper(this, {
                loop: false,
                slidesPerView: 1.6,
                spaceBetween: 12,
                breakpoints: {
                    600: {
                    slidesPerView: 5,
                    spaceBetween: 20,
                    },
                },
                autoHeight:true,
            });

            setTimeout(() => {
                const wrapper = swiper.wrapperEl;
                console.log(wrapper.clientHeight);
                const SLIDER = swiper.slides;
                SLIDER.forEach(function(slide) {
                    slide.style.height = wrapper.clientHeight + "px";
                });
            }, 1000);
        });

    });

    function refreshTabel(page){
        window.location.href = "<?=site_url("blog")?>?page="+page;
    }
    function refreshPPOB(){
        window.location.href = "<?=site_url("manage/pesanan")?>";
    }

    <?php if($set->notif_booster > 0){ ?>
    $(function(){
        setTimeout(() => {
            toaster();
        }, 3000);
        
    });

    function toaster(){
        $.post("<?=site_url("assync/booster")?>",{"id":0,[$("#names").val()]:$("#tokens").val()},function(msg){
            var data = eval("("+msg+")");
            updateToken(data.token);
            if(data.success == true){
                $("#toast-foto").attr("src",data.foto);
                $("#toast-user").html(data.user);
                $("#toast-produk").html(data.produk);

                $("#toaster").show("slow");
                setTimeout(() => {
                    $("#toaster").hide("slow");
                    setTimeout(() => {
                        toaster();
                    }, 3000);
                }, 5000);
            }else{
                setTimeout(() => {
                    toaster();
                }, 5000);
            }
        });
    }
    <?php } ?>
</script>