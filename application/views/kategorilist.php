
<!-- breadcrumb -->
<div class="container">
    <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
        <a href="<?php echo site_url(); ?>" class="text-primary">
            Home
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>

        <span class="stext-109 cl4">
            Kategori
        </span>
    </div>
</div>



<!-- Shoping Cart -->
<style rel="stylesheet">
    @media only screen and (min-width:721px){
        .mobilefix{
            margin-left: -36px;
        }
    }
</style>
<div class="p-t-0 p-b-85">
    <div class="container p-t-20 p-b-50">
        <div class="row kategori">
            <?php
                $this->db->where("parent",0);
                $this->db->order_by("nama","asc");
                $db = $this->db->get("kategori");
                foreach($db->result() as $r){
            ?>
            <div class="col-md-6 m-b-20">
                <div class="card">
                    <div class="card-header bg-primary text-light">
                        <div class="row">
                            <div class="col-md-1 col-2 p-r-0">
                                <img src="<?=base_url("cdn/kategori/".$r->icon)?>"/>
                            </div>
                            <div class="col-md-11 col-10">
                                <a href="<?=site_url("kategori/".$r->url)?>" class="w-full"><?=strtoupper(strtolower($r->nama))?> <i class="fas fa-chevron-right float-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php
                        $this->db->where("parent",$r->id);
                        $this->db->order_by("nama","asc");
                        $dbs = $this->db->get("kategori");
                        if($dbs->num_rows() > 0){
                    ?>
                    <div class="card-body">
                        <div class="row">
                            <?php
                                foreach($dbs->result() as $rs){
                            ?>
                            <div class="col-md-4 mb-2">
                                <a href="<?=site_url("kategori/".$rs->url)?>" class="text-primary font-medium"><?=$rs->nama?></a>
                            </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
</div>