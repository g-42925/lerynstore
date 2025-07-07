<?php
    $sukses = false;
    $tipeco = (isset($_SESSION["usrid"])) ? 0 : 1;
    $berat = 0;
    $total = 0;
    $jenis = null;
    $gudang = null;
    if(isset($_POST["idproduk"]) AND is_array($_POST["idproduk"]) AND count($_POST["idproduk"]) > 0){
        //print_r($_POST["idproduk"]);exit;
        for($i=0; $i<count($_POST["idproduk"]); $i++){
            $pro = $this->func->getTransaksiProduk($_POST["idproduk"][$i],"semua");
            if($pro->id > 0 AND $pro->idtransaksi == 0){
                $prod = $this->func->getProduk($pro->idproduk,"semua");
                if($prod->id > 0){
                    $jenis = ($jenis === null) ? $prod->digital : $jenis;
                    if($jenis == $prod->digital){
                        $gudang = ($gudang === null) ? $prod->gudang : $gudang;
                        if($gudang == $prod->gudang){
                            $sukses = $sukses == false ? true : $sukses;
                            $berat += $prod->berat * $pro->jumlah;
                            $total += $pro->harga * $pro->jumlah;
                        }else{
                            unset($_POST["idproduk"][$i]);
                        }
                    }else{
                        unset($_POST["idproduk"][$i]);
                    }
                }else{
                    unset($_POST["idproduk"][$i]);
                }
            }else{
                unset($_POST["idproduk"][$i]);
            }
        }

        if(isset($_POST["idproduk"]) AND is_array($_POST["idproduk"]) AND count($_POST["idproduk"]) > 0){
            if(isset($_SESSION["prebayar"])){
                if($tipeco == 0){
                    $this->db->where("usrid",$_SESSION["usrid"]);
                }else{
                    $this->db->where("usrid_temp",$_SESSION["usrid_temp"]);
                }
                $this->db->where("tipeco",$tipeco);
                $this->db->where("status",0);
                $this->db->update("pembayaran_pre",["status"=>2]);
                $this->session->unset_userdata("prebayar");
            }
            $set = $this->func->globalset("semua");
            $dari = ($gudang == 0) ? $set->kota : $this->func->getGudang($gudang,"idkab");
            $data = array(
                "tipeco"=> $tipeco,
                "tgl"   => date("Y-m-d H:i:s"),
                "dari"  => $dari,
                "gudang"=> $gudang,
                "total" => $total,
                "digital" => $jenis,
                "berat" => $berat,
                "produk"=> implode("|",$_POST["idproduk"])
            );
            if($tipeco == 0){
                $data["usrid"] = $_SESSION["usrid"];
            }else{
                $data["usrid_temp"] = $_SESSION["usrid_temp"];
            }
            $this->db->insert("pembayaran_pre",$data);
            $id = $this->db->insert_id();
            $this->session->set_userdata("prebayar",$id);
?>
    <div class="progress-wrap col-md-10 m-lr-auto p-lr-0 p-t-40" style="overflow:hidden;">
        <div class="row progress-checkout">
            <div class="line"></div>
            <div class="col-4 alamats">
                <div class="wrap active">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="titles">Alamat</div>
                </div>
            </div>
            <div class="col-4 kurir">
                <div class="wrap">
                    <i class="fas fa-shipping-fast"></i>
                    <div class="titles">Kurir</div>
                </div>
            </div>
            <div class="col-4 bayar">
                <div class="wrap">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <div class="titles hidesmall">Pembayaran</div>
                    <div class="titles showsmall">Bayar</div>
                </div>
            </div>
        </div>
        <div class="p-all-24 m-t-20 m-b-60">
            <div class="load">
                <div class="p-tb-30 text-center">
                    <i class="fas fa-compact-disc fa-spin text-primary"></i> tunggu sebentar...
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=AIzaSyDeY_0v4-MA7fDR8mf9Ssw6_skjyTFGbE0&libraries=places"></script>

    <script type="text/javascript">
    var marker;
    var lat = 3.5787483; // 3.5877165
    var lng = 98.625768; // 98.6611751
    var latlng;
    
    var lattoko = 3.5787483;
    var lngtoko = 98.625768;

    var mapProp = {
        center: new google.maps.LatLng(lat, lng),
        zoom: 15,
        fullscreenControl: false,
        streetViewControl: false
    };

    var map;
    var geocoder = new google.maps.Geocoder();

    function initialize() {
        map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        
        var input = document.getElementById('addressmaploc');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();

            latlng = place.geometry['location'];
            lat = place.geometry['location'].lat();
            lng = place.geometry['location'].lng();

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(15);
            }

            initialize_sec();
        });

        google.maps.event.addListener(map, 'click', function (event) {
            latlng = event.latLng;
            lat = event.latLng.lat();
            lng = event.latLng.lng();

            initialize_sec();
        });
    }

    function initialize_sec() {
        if (latlng) {
            if (typeof marker !== 'undefined') {
                marker.setMap(null);
            }

            document.getElementById("latitude_gl").value = lat;
            document.getElementById("longitude_gb").value = lng;
            
            var jarak = calculateDistance(lat, lng, lattoko, lngtoko);
            document.getElementById("jarak_km").value = jarak.toFixed(2);

            marker = new google.maps.Marker({ position: latlng, map: map });

            geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        document.getElementById("alamat_locglgb").value = results[1].formatted_address;
                    } else {
                        alert('No results found, please try again.');
                    }
                } else {
                    alert('Geocoder failed due to: ' + status);
                }
            });
        }
    }
    
    function toRad(Value) {
        return Value * Math.PI / 180;
    }
    
    function calculateDistance(lat1, lng1, lat2, lng2) {
        var R = 6371; // Radius Bumi dalam km
        var dLat = toRad(lat2 - lat1);
        var dLng = toRad(lng2 - lng1);
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var distance = R * c; // Jarak dalam km
        return distance;
    }
    
    </script>

    <script type="text/javascript">
		$(function(){
            <?php if($jenis == 0){ ?>
            $(".load").load("<?=site_url('checkout/alamat')?>", function() {
                // Panggil initialize() setelah alamat dimuat
                initialize();
            });
            <?php }else{ ?>
            $(".progress-checkout").hide();
            loadBayar();
            <?php } ?>
        });

        function loadKurir(){
            $(".progress-checkout .wrap").removeClass("active");
            $(".progress-checkout .kurir .wrap").addClass("active");
            $(".load").html('<div class="p-tb-40 text-center m-tb-20 section"><i class="fas fa-compact-disc fa-spin text-primary fs-32 m-b-12"></i><br/>tunggu sebentar, sedang memuat pilihan kurir yang dapat mengirim pesanan ke alamat Anda</div>');
            $(".load").load("<?=site_url("checkout/kurir")?>");
        }
        function loadBayar(){
            $(".progress-checkout .wrap").removeClass("active");
            $(".progress-checkout .bayar .wrap").addClass("active");
            $(".load").html('<div class="p-tb-40 text-center m-tb-20 section"><i class="fas fa-compact-disc fa-spin text-primary"></i> tunggu sebentar...</div>');
            $(".load").load("<?=site_url("checkout/bayar")?>");
        }
    </script>
<?php
        }else{
            $sukses = false;
        }
    }

    if($sukses!=true){
?>
    <script type="text/javascript">
		$(function(){
            swal.fire("Pilih Produk","Sebelum checkout, silahkan pilih produk yang akan Anda bayar terlebih dahulu","error").then(()=>{
                history.back();
            });
        });
    </script>
<?php
    }
?>