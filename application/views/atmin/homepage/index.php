<h4 class="page-title m-b-20">Homepage Editor</h4>

<div class="m-b-60 row">
    <div class="col-md-8">
        <form id="setting">
            <div class="card">
                <div class="card-header">
                    Elemen Homepage
                </div>
                <div class="card-body" id="segmen">
                    <?php
                        $home = json_decode($set->homepage);
                        $no = 1;
                        foreach($home as $k => $v){
                    ?>
                        <div class="card m-b-12 border-secondary rounded-lg" id="elemen<?=$no?>">
                            <input type="hidden" name="elemen[]" value="elemen<?=$no?>" />
                            <input type="hidden" name="tipe[elemen<?=$no?>]" value="<?=$v->tipe?>" />
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <i class="fas fa-grip m-r-12"></i>
                                        <b><?=$tipe[$v->tipe]?></b> <i class="fas fa-arrow-right-long"></i> <span id="judul<?=$no?>"><?=$v->judul?></span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <button type="button" onclick="togel('<?=$no?>')" class="btn btn-default p-lr-6 p-tb-3">
                                            <i class="fas fa-chevron-down" id="down<?=$no?>"></i>
                                            <i class="fas fa-chevron-up" id="up<?=$no?>" style="display:none;"></i>
                                        </button>
                                        <button type="button" onclick="hapus(<?=$no?>)" class="btn btn-danger p-lr-6 p-tb-3">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-b-20" id="body<?=$no?>" style="display:none;"><!-- -->
                                <div class="form-group">
                                    <label>Judul Segmen</label>
                                    <input type="text" name="judul[elemen<?=$no?>]" value="<?=$v->judul?>" data-elemen="<?=$no?>" class="form-control judul" />
                                </div>
                                <div class="form-group">
                                    <label>Warna Background</label>
                                    <input type="color" name="warna[elemen<?=$no?>]" value="<?=isset($v->warna) ? $v->warna : "#ffffff"?>" data-elemen="<?=$no?>" class="form-control warna py-0 px-0 col-md-4 col-6" />
                                </div>
                                <?php if(in_array($v->tipe,[5,6])){ ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kategori Produk</label>
                                            <select name="kategori[elemen<?=$no?>]" data-elemen="<?=$no?>" class="form-control">
                                                <option value="0">Semua Kategori</option>
                                                <?php
                                                    $this->db->where("parent",0);
                                                    $kat = $this->db->get("kategori");
                                                    foreach($kat->result() as $res){
                                                        $sel = ($res->id == $v->kategori) ? "selected" : "";
                                                ?>
                                                <option value="<?=$res->id?>" <?=$sel?>><?=$res->nama?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Brand Produk</label>
                                            <select name="brand[elemen<?=$no?>]" data-elemen="<?=$no?>" class="form-control">
                                                <option value="0">Semua Brand</option>
                                                <?php
                                                    $bran = $this->db->get("brand");
                                                    foreach($bran->result() as $res){
                                                        $sel = ($res->id == $v->brand) ? "selected" : "";
                                                ?>
                                                <option value="<?=$res->id?>" <?=$sel?>><?=$res->nama?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jenis Produk</label>
                                            <select name="jenis[elemen<?=$no?>]" data-elemen="<?=$no?>" class="form-control">
                                                <option value="0" <?=($v->jenis == 0) ? "selected" : ""?>>Semua Produk</option>
                                                <option value="1" <?=($v->jenis == 1) ? "selected" : ""?>>Produk Fisik</option>
                                                <option value="2" <?=($v->jenis == 2) ? "selected" : ""?>>Produk Digital</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Maks. Produk Ditampilkan</label>
                                            <div class="input-group">
                                                <input type="number" name="jumlah[elemen<?=$no?>]" value="<?=$v->jumlah?>" data-elemen="<?=$no?>" class="form-control" />
                                                <div class="input-group-append"><span class="input-group-text">produk</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }else{ ?>
                                    <?php if(in_array($v->tipe,[1,2])){ ?>
                                        <div class="form-group">
                                            <label>Promo Tags</label>
                                            <select name="tags[elemen<?=$no?>]" data-elemen="<?=$no?>" class="form-control">
                                                <?php
                                                    $this->db->group_by('tags');
                                                    $tags = $this->db->get("promo");
                                                    foreach($tags->result() as $res){
                                                        $sel = ($res->tags == $v->tags) ? "selected" : "";
                                                ?>
                                                <option value="<?=$res->tags?>" <?=$sel?>><?=$res->tags?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php }else{ ?>
                                        <input type="hidden" name="tags[elemen<?=$no?>]" value="" />
                                    <?php } ?>
                                    <?php if(in_array($v->tipe,[2,7,8,10,11])){ ?>
                                        <div class="form-group">
                                            <label>Maksimal Item Ditampilkan</label>
                                            <div class="input-group col-md-6 p-lr-0">
                                                <input type="number" name="jumlah[elemen<?=$no?>]" value="<?=$v->jumlah?>" data-elemen="<?=$no?>" class="form-control" />
                                                <div class="input-group-append"><span class="input-group-text">item</span></div>
                                            </div>
                                        </div>
                                    <?php }else{ ?>
                                        <input type="hidden" name="jumlah[elemen<?=$no?>]" value="0" />
                                    <?php } ?>
                                <input type="hidden" name="kategori[elemen<?=$no?>]" value="0" />
                                <input type="hidden" name="brand[elemen<?=$no?>]" value="0" />
                                <input type="hidden" name="jenis[elemen<?=$no?>]" value="0" />
                                <input type="hidden" name="produk[elemen<?=$no?>]" value="0" />
                                <?php } ?>
                            </div>
                        </div>
                    <?php
                            $no++;
                        }
                    ?>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan Homepage</button>
                </div>
            </div>
        </form>
	</div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Tambah Segmen
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Judul Segmen</label>
                    <input type="text" class="form-control" id="juduladd" />
                </div>
                <div class="form-group m-b-12">
                    <label>Tipe Segmen</label>
                    <select id="tipeadd" class="form-control">
                        <?php
                            foreach($tipe as $k => $v){
                        ?>
                        <option value="<?=$k?>"><?=$v?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary w-full" type="button" onclick="tambahSegmen()"><i class="fas fa-plus"></i> Tambah Segmen</button>
                </div>
            </div>
        </div>
	</div>
</div>

<div style="display:none" id="tambahbody56">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Kategori Produk</label>
                <select type="number" name="kategori[elemen]" data-elemen="" class="form-control kategorielemen">
                    <option value="0">Semua Kategori</option>
                    <?php
                        foreach($kat->result() as $res){
                    ?>
                    <option value="<?=$res->id?>"><?=$res->nama?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Brand Produk</label>
                <select type="number" name="brand[elemen]" data-elemen="" class="form-control brandelemen">
                    <option value="0">Semua Brand</option>
                    <?php
                        foreach($bran->result() as $res){
                            $sel = ($res->id == $v->brand) ? "selected" : "";
                    ?>
                    <option value="<?=$res->id?>" <?=$sel?>><?=$res->nama?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Jenis Produk</label>
                <select type="number" name="jenis[elemen]" data-elemen="" class="form-control jeniselemen">
                    <option value="0">Semua Produk</option>
                    <option value="1">Produk Fisik</option>
                    <option value="2">Produk Digital</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Maks. Produk Ditampilkan</label>
                <div class="input-group">
                    <input type="number" name="jumlah[elemen]" value="" data-elemen="" class="form-control jumlahelemen" />
                    <div class="input-group-append"><span class="input-group-text">produk</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display:none" id="tambahbody72">
    <div class="form-group">
        <label>Promo Tags</label>
        <select name="tags[elemen]" data-elemen="" class="form-control tags">
            <?php
                foreach($tags->result() as $res){
            ?>
            <option value="<?=$res->tags?>"><?=$res->tags?></option>
            <?php } ?>
        </select>
    </div>
</div>
<div style="display:none" id="tambahselain72">
    <input type="hidden" name="tags[elemen]" value="" class="tags" />
</div>
<div style="display:none" id="tambahbody278">
    <div class="form-group">
        <label>Maksimal Item Ditampilkan</label>
        <div class="input-group col-md-6 p-lr-0">
            <input type="number" name="jumlah[elemen]" value="" data-elemen="" class="form-control jumlah" />
            <div class="input-group-append"><span class="input-group-text">item</span></div>
        </div>
    </div>
</div>
<div style="display:none" id="tambahselain278">
    <input type="hidden" name="jumlah[elemen]" value="0" class="jumlah" />
</div>
<div style="display:none" id="tambahselain56">
    <input type="hidden" name="kategori[elemen]" value="0" class="kategori" />
    <input type="hidden" name="brand[elemen]" value="0" class="brand" />
    <input type="hidden" name="jenis[elemen]" value="0" class="jenis" />
</div>
<div style="display:none" id="tambahsegmen">
    <div class="card m-b-12 border-secondary rounded-lg tambahsegmen" id="elemen">
        <input type="hidden" name="elemen[]" class="tambahelemen" value="elemen" />
        <input type="hidden" name="tipe[elemen]" class="tambahtipe" value="1" />
        <input type="hidden" name="produk[elemen]" value="0" class="produk" />
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <i class="fas fa-grip m-r-12"></i>
                    <b class="tambahtipetitle"></b> <i class="fas fa-arrow-right-long"></i> <span class="tambahjudul" id="judul"></span>
                </div>
                <div class="col-4 text-right">
                    <button type="button" onclick="togel('')" class="btn btn-default p-lr-6 p-tb-3 togel">
                        <i class="fas fa-chevron-down togeldown" id="down" style="display:none;"></i>
                        <i class="fas fa-chevron-up togelup" id="up"></i>
                    </button>
                    <button type="button" onclick="hapus()" class="btn btn-danger p-lr-6 p-tb-3 hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-b-20 bodyno" id="bodyno">
            <div class="form-group">
                <label>Judul Segmen</label>
                <input type="text" name="judul[elemen]" oninput="$('').html(this.value)" class="form-control judul tambahjuduls" />
            </div>
            <div class="form-group">
                <label>Warna Background</label>
                <input type="color" name="warna[elemen]" oninput="$('').html(this.value)" class="form-control warna tambahwarna py-0 px-0 col-md-4 col-6" />
            </div>
            <div id="bodyinject">

            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.13.1/TweenMax.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.13.1/utils/Draggable.min.js"></script> 
<script type="text/javascript">
    $(function(){
        createSortable("#segmen");

        $(".judul").each(function(){
            $(this).on('input',function(){
                $("#judul"+$(this).data('elemen')).html($(this).val());
            });
        });

        $("#setting").on('submit',function(e){
            e.preventDefault();
            $.post('<?=site_url($this->func->admurl().'/home/simpan')?>',$(this).serialize(),function(msg){
                var data = eval("("+msg+")");
                if(data.success == true){
                    swal.fire("Sukses!","layout homepage berhasil disimpan","success").then(()=>{
                        location.reload();
                    });
                }else{
                    swal.fire({
                        html: "gagal menyimpan layout homepage, silahkan ulangi lagi nanti<br/><span class='text-danger'>"+data.msg+"</span>",
                        title: "Gagal!",
                        type: "error",
                    });
                }
            });
        });
    });

    function tambahSegmen(){
        if($("#tipeadd").val() != null && $("#tipeadd").val() != "" && $("#juduladd").val() != null && $("#juduladd").val() != "" ){
            var id = makeid(5);
            var elemen = 'elemen'+id;
            var tipeadd = $("#tipeadd").val();
            var juduladd = $("#juduladd").val();
            var tipejudul = $("#tipeadd option:selected").text();
            console.log(juduladd);
            $("#tambahsegmen .tambahelemen").val(elemen);
            $("#tambahsegmen .tambahelemen").attr('name',"elemen["+elemen+"]");
            $("#tambahsegmen .tambahtipe").val(tipeadd);
            $("#tambahsegmen .tambahtipe").attr('name',"tipe["+elemen+"]");
            $("#tambahsegmen .tambahtipetitle").html(tipejudul);
            $("#tambahsegmen .tambahjudul").html(juduladd);
            $("#tambahsegmen .tambahjuduls").attr('name',"judul["+elemen+"]");
            $("#tambahsegmen .tambahjuduls").attr('data-elemen',id);
            $("#tambahsegmen .tambahjuduls").attr('value',juduladd);
            $("#tambahsegmen .tambahjuduls").attr('oninput',"$('#judul"+id+"').html(this.value)");
            $("#tambahsegmen .tambahwarna").attr('name',"warna["+elemen+"]");
            $("#tambahsegmen .tambahwarna").attr('data-elemen',id);
            $("#tambahsegmen .tambahwarna").attr('oninput',"$('#warna"+id+"').html(this.value)");
            $("#tambahsegmen .produk").attr('name',"produk["+elemen+"]");
            $("#tambahsegmen .togel").attr('onclick',"togel('"+id+"')");
            $("#tambahsegmen .hapus").attr('onclick',"hapus('"+id+"')");
            $("#tambahsegmen .tambahsegmen").attr('id',elemen);
            $("#tambahsegmen .tambahjudul").attr("id","judul"+id);
            $("#tambahsegmen .togelup").attr("id","up"+id);
            $("#tambahsegmen .togeldown").attr("id","down"+id);
            $("#tambahsegmen .bodyno").attr('id','body'+id);
            
            var lima6 = ["5","6"];
            var tujuh2 = ["7","2"];
            var dua78 = ["2","7","8","10","11"];
            if(lima6.includes(tipeadd)){
                $("#tambahbody56 .kategorielemen").attr('name',"kategori["+elemen+"]");
                $("#tambahbody56 .brandelemen").attr('name',"brand["+elemen+"]");
                $("#tambahbody56 .jeniselemen").attr('name',"jenis["+elemen+"]");
                $("#tambahbody56 .jumlahelemen").attr('name',"jumlah["+elemen+"]");
                $("#bodyinject").append($("#tambahbody56").html());
            }else{
                $("#tambahselain56 .kategori").attr('name',"kategori["+elemen+"]");
                $("#tambahselain56 .brand").attr('name',"brand["+elemen+"]");
                $("#tambahselain56 .jenis").attr('name',"jenis["+elemen+"]");
                $("#bodyinject").append($("#tambahselain56").html());
            }

            if(tujuh2.includes(tipeadd)){
                $("#tambahbody72 .tags").attr('name',"tags["+elemen+"]");
                $("#bodyinject").append($("#tambahbody72").html());
            }else{
                $("#tambahselain72 .tags").attr('name',"tags["+elemen+"]");
                $("#bodyinject").append($("#tambahselain72").html());
            }

            if(dua78.includes(tipeadd)){
                $("#tambahbody278 .jumlah").attr('name',"jumlah["+elemen+"]");
                $("#bodyinject").append($("#tambahbody278").html());
            }else{
                $("#tambahselain278 .jumlah").attr('name',"jumlah["+elemen+"]");
                $("#bodyinject").append($("#tambahselain278").html());
            }

            setTimeout(() => {
                $("#segmen").append($("#tambahsegmen").html());
                setTimeout(() => {
                    $("#juduladd").val("");
                    $('html, body').animate({
                        scrollTop: $("#"+elemen).offset().top
                    }, 500);
                    $("#tambahsegmen .tambahsegmen").attr('id',"elemen__");
                    $("#tambahsegmen .tambahjudul").attr("id","judulid__");
                    $("#tambahsegmen .togelup").attr("id","upid__");
                    $("#tambahsegmen .togeldown").attr("id","downid__");
                    $("#tambahsegmen .bodyno").attr('id','bodyid__');
                }, 500);
            }, 500);
        }else{
            swal.fire("Lengkapi Form","lengkapi judul dan tipe segmen terlebih dahulu!","error");
        }
    }

    function hapus(id){
		swal.fire({
			text: "elemen akan dihapus dan tidak ditampilkan di homepage setelah Anda simpan",
			title: "Hapus elemen ini?",
			type: "warning",
			showCancelButton: true,
			cancelButtonColor: "#ff646d",
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
                $("#elemen"+id).remove();
            }
        });
    }
    function togel(id){
        $('#body'+id).slideToggle();
        $('#down'+id).toggle();
        $('#up'+id).toggle();
    }

    function createSortable(selector) {
        var sortable = document.querySelector(selector);
        Draggable.create(sortable.children, {
            type: "y",
            bounds: sortable,
            edgeResistance: 1,
            onPress: sortablePress,
            onDragStart: sortableDragStart,
            onDrag: sortableDrag,
            liveSnap: sortableSnap,
            onDragEnd: sortableDragEnd
        });
    }

    function sortablePress(event) {
        var t = this.target,
            i = 0,
            child = t;  
        
        while(child = child.previousSibling) {
            if (child.nodeType === 1) i++;
        }
        
        t.currentIndex = i;
        t.currentHeight = t.offsetHeight;
        t.kids = Array.prototype.slice.call(t.parentNode.children, 0);
        }

        function sortableDragStart() {
    }
                    
    function sortableDrag() {
        var t = this.target,      
            elements = t.kids.slice(0), // clone      
            indexChange = Math.round(this.y / t.currentHeight),
            bound1 = t.currentIndex,
            bound2 = bound1 + indexChange;
            
        if (bound1 < bound2) { // moved down
            TweenLite.to(elements.splice(bound1+1, bound2-bound1), 0.15, { yPercent: -100 });
            TweenLite.to(elements, 0.5, { yPercent: 0 });
        } else if (bound1 === bound2) {
            elements.splice(bound1, 1);
            TweenLite.to(elements, 0.5, { yPercent: 0 });
        } else { // moved up
            TweenLite.to(elements.splice(bound2, bound1-bound2), 0.15, { yPercent: 100 });
            TweenLite.to(elements, 0.5, { yPercent: 0 });
        }
        TweenLite.set(this.target, { color: "#007bff" });
    }

    function sortableSnap(y) {
        var h = this.target.currentHeight;
        return Math.round(y / h) * h;
    }
                    
    function sortableDragEnd() {
        var t = this.target,
            max = t.kids.length - 1,
            yPos = this.y || endY,
            newIndex = Math.round(this.y / t.currentHeight);  
        
        newIndex += (newIndex < 0 ? -1 : 0) + t.currentIndex;
        if (newIndex === max) {
            t.parentNode.appendChild(t);
        } else {
            t.parentNode.insertBefore(t, t.kids[newIndex+1]);
        }
        TweenLite.set(t.kids, { yPercent: 0, overwrite: "all" });
        TweenLite.set(t, { y: 0, color: "" });
    }
    
    function makeid(length) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        let counter = 0;
        while (counter < length) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
        counter += 1;
        }
        return result;
    }
</script>