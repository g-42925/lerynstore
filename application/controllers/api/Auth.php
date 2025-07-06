<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Auth extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$set = $this->func->globalset("semua");
		$production = (strpos($set->midtrans_snap,"sandbox") == true) ? false : true;
		\Midtrans\Config::$serverKey = $set->midtrans_server;
		\Midtrans\Config::$isProduction = $production;
		\Midtrans\Config::$isSanitized = true;
		\Midtrans\Config::$is3ds = true;

		/*if($this->func->maintenis() == TRUE) {
			include(APPPATH.'views/maintenis.php');

			die();
		}*/
	}
	
	// TOKEN MANAGEMENT
	public function updatetoken(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s"),"apptoken"=>$input["token"]));
					//$usr = $this->func->getUser($r->usrid,"semua");
					$this->db->where("token",$r->token);
					$this->db->where("apptoken","");
					$this->db->delete("token");
				}

				echo json_encode(array("success"=>true));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function getsessiontoken(){
		$set = $this->func->globalset("semua");
		$tema = (isset($set->tema)) ? $set->tema: 0;
		$tema = $this->func->tema($tema);
		$data = [
			'light'	=> $tema->hex_light,
			'hover'	=> $tema->hex_hover,
			'foot'	=> $tema->hex_foot,
		];
		$token = md5(date("YmdHis"));		
		$this->db->insert("token",array("token"=>$token,"tgl"=>date("Y-m-d H:i:s")));
		
		echo json_encode(array("success"=>true,"token"=>$token,"tema"=>$data,"usrid"=>0));
	}
	public function loginmode(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->limit(1);
			$db = $this->db->get("token");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$this->db->where("id",$r->id);
					$this->db->update("token",array("last_access"=>date("Y-m-d H:i:s")));
				}
				
				$otp = $this->func->globalset("login_otp") == 1 ? 1 : 2;
				echo json_encode(array("success"=>true,"mode"=>$otp));
			}else{
				echo json_encode(array("success"=>false,"sesihabis"=>true));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	
	//LOGIN LOGOUT REGISTER
	public function login(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			
			$this->db->where("nohp",$input["email"]);
			$this->db->or_where("username",$input["email"]);
			$this->db->limit(1);
			$db = $this->db->get("userdata");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					$token = md5(date("YmdHis").$r->id);
					if($this->func->decode($r->password) === $input["password"]){
						//DESTROY OLD TOKEN SESSION
							$this->db->where("usrid",$r->id);
							$this->db->where("status",0);
							$this->db->update("token",array("status"=>2));
						//CREATE NEW TOKEN SESSION
						$data = array(
							"usrid"	=> $r->id,
							"tgl"	=> date("Y-m-d H:i:s"),
							"token"	=> $token,
							"status"=> 1
						);
						$this->db->insert("token",$data);
						
						echo json_encode(array("success"=>true,"level"=>$r->level,"usrid"=>$r->id,"nama"=>$this->func->getProfil($r->id,"nama","usrid"),"saldo"=>$this->func->getSaldo($r->id,"saldo","usrid",true),"token"=>$token));
					}else{
						echo json_encode(array("success"=>false,"message"=>"Gagal masuk, Email/No HP/Password salah"));
					}
				}
			}else{
				echo json_encode(array("success"=>false,"message"=>"Gagal masuk, Pengguna tidak ditemukan"));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	function loginotp(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			$type = isset($input["tipe"]) ? $input["tipe"] : "none";
			
			if(isset($input["email"]) AND $type == "none"){
				$this->db->where("username",$input["email"]);
				$this->db->or_where("nohp",$input["email"]);
				$this->db->limit(1);
				$db = $this->db->get("userdata");
				$set = $this->func->globalset("semua");

				$generator = "1357902468";
				$otp = "";
				for ($i = 1; $i <= 6; $i++) {
					$otp .= substr($generator, (rand()%(strlen($generator))), 1);
				}
				
				if($db->num_rows() == 0){
					echo json_encode(array("success"=>false));
				}else{
					foreach($db->result() as $res){
						$array = array(
							"tgl"	=> date("Y-m-d H:i:s"),
							"usrid"	=> $res->id,
							"kode"	=> $otp,
							"kadaluarsa"	=> date('Y-m-d H:i:s',strtotime('+10 minutes',strtotime(date("Y-m-d H:i:s")))),
							"status"=> 0
						);
						$this->db->insert("otplogin",$array);
						//$this->session->set_userdata("otp_id",$this->db->insert_id());
						$otp_id = $this->db->insert_id();

						$pesan = "
							<b>PERHATIAN!</b><br/>".
							"JANGAN BERIKAN kode ini kepada siapa pun, TERMASUK TIM ".strtoupper(strtolower($set->nama))."<br/>".
							"WASPADA PENIPUAN!<br/>".
							"Untuk MASUK KE AKUN ".strtoupper(strtolower($set->nama)).", masukkan kode RAHASIA: <b>".$otp."</b>
						";
						$this->func->sendEmail($res->username,$set->nama." - OTP Login",$pesan,"OTP Login");
						$pesan = "
							*PERHATIAN!* \n".
							"JANGAN BERIKAN kode ini kepada siapa pun, TERMASUK TIM ".strtoupper(strtolower($set->nama))."\n".
							"WASPADA PENIPUAN! \n".
							"Untuk MASUK KE AKUN ".strtoupper(strtolower($set->nama)).", masukkan kode RAHASIA: *".$otp."*
						";
						$this->func->sendWAOK($this->func->getProfil($res->id,"nohp","usrid"),$pesan);

						echo json_encode(array("success"=>true,"otpid"=>$otp_id));
					}
				}
			}elseif(isset($input["otpid"]) AND $type == "resend"){
				$this->db->where("id",$input["otpid"]);
				$db = $this->db->get("otplogin");
				$set = $this->func->globalset("semua");

				$generator = "1357902468";
				$otp = "";
				for ($i = 1; $i <= 6; $i++) {
					$otp .= substr($generator, (rand()%(strlen($generator))), 1);
				}
				
				if($db->num_rows() == 0){
					echo json_encode(array("success"=>false));
				}else{
					foreach($db->result() as $res){
						if($res->kadaluarsa < date("Y-m-d H:i:s")){
							$this->db->where("id",$res->id);
							$this->db->update("otplogin",["status"=>2]);

							$array = array(
								"tgl"	=> date("Y-m-d H:i:s"),
								"usrid"	=> $res->usrid,
								"kode"	=> $otp,
								"kadaluarsa"	=> date('Y-m-d H:i:s',strtotime('+10 minutes',strtotime(date("Y-m-d H:i:s")))),
								"status"=> 0
							);
							$this->db->insert("otplogin",$array);
							//$this->session->set_userdata("otp_id",$this->db->insert_id());
						}else{
							$otp = $res->kode;
						}

						$pesan = "
							<b>PERHATIAN!</b><br/>".
							"JANGAN BERIKAN kode ini kepada siapa pun, TERMASUK TIM ".strtoupper(strtolower($set->nama))."<br/>".
							"WASPADA PENIPUAN!<br/>".
							"Untuk MASUK KE AKUN ".strtoupper(strtolower($set->nama)).", masukkan kode RAHASIA: <b>".$otp."</b>
						";
						$this->func->sendEmail($this->func->getUser($res->usrid,"username"),$set->nama." - OTP Login",$pesan,"OTP Login");
						$pesan = "
							*PERHATIAN!* \n".
							"JANGAN BERIKAN kode ini kepada siapa pun, TERMASUK TIM ".strtoupper(strtolower($set->nama))."\n".
							"WASPADA PENIPUAN! \n".
							"Untuk MASUK KE AKUN ".strtoupper(strtolower($set->nama)).", masukkan kode RAHASIA: *".$otp."*
						";
						$this->func->sendWAOK($this->func->getProfil($res->usrid,"nohp","usrid"),$pesan);

						echo json_encode(array("success"=>true));
					}
				}
			}elseif(isset($input["otpid"]) AND isset($input["otp"]) AND $type == "confirm"){
				$this->db->where("id",$input["otpid"]);
				$db = $this->db->get("otplogin");

				$pass = null;
				$aktif = false;
				if($db->num_rows() == 0){
					echo json_encode(array("success"=>false));
					exit;
				}
				foreach($db->result() as $res){
					$pass = $res->kode;
					$aktif = ($res->status == 0) ? false : true;
				}
				if($aktif == true){
					echo json_encode(array("success"=>false));
					exit;
				}

				if($input["otp"] == $pass){
					//$usr = $this->func->getUser($res->usrid,"semua");
					//$this->session->set_userdata("usrid",$usr->id);
					//$this->session->set_userdata("lvl",$usr->level);
					//$this->session->set_userdata("status",$usr->status);

					$this->db->where("id",$res->id);
					$this->db->update("otplogin",["status"=>1,"masuk"=>date("Y-m-d H:i:s")]);

					$r = $this->func->getUser($res->usrid,"semua");
					//$this->session->unset_userdata("otp_id");
					$token = md5(date("YmdHis").$r->id);
					//DESTROY OLD TOKEN SESSION
					$this->db->where("usrid",$r->id);
					$this->db->where("status",0);
					$this->db->update("token",array("status"=>2));
					//CREATE NEW TOKEN SESSION
					$data = array(
						"usrid"	=> $r->id,
						"tgl"	=> date("Y-m-d H:i:s"),
						"token"	=> $token,
						"status"=> 1
					);
					$this->db->insert("token",$data);
					
					echo json_encode(array("success"=>true,"level"=>$r->level,"usrid"=>$r->id,"nama"=>$this->func->getProfil($r->id,"nama","usrid"),"saldo"=>$this->func->getSaldo($r->id,"saldo","usrid",true),"token"=>$token));
				}else{
					echo json_encode(array("success"=>false));
				}
			}else{
				echo json_encode(array("success"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function logout(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$this->db->where("token",$_SERVER['HTTP_AUTHORIZATION']);
			$this->db->where("status",1);
			$this->db->update("token",array("status"=>2,"last_access"=>date("Y-m-d H:i:s")));
			
			$token = md5(date("YmdHis"));
			$this->db->insert("token",array("token"=>$token,"tgl"=>date("Y-m-d H:i:s")));
			echo json_encode(array("success"=>true,"token"=>$token,"message"=>"Berhasil keluar aplikasi, silahkan masuk/daftar untuk menggunakan Aplikasi"));
		}else{
			echo json_encode(array("success"=>false,"message"=>"Gagal logout! ulangi beberapa saat lagi"));
		}
	}
	public function register(){
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, TRUE);
		if(isset($input["nohp"])){
			$users = $this->func->getUser($input["email"],"semua","username");
			if($input["nohp"] == null OR $input["nama"] == null OR $input["password"] == null){
				echo json_encode(array("success"=>false,"message"=>"Formulir belum lengkap, mohon lengkapi dahulu sesuai format yg disediakan"));
				exit;
			}
			if($users->id > 0){
				echo json_encode(array("success"=>false,"message"=>"Alamat email sudah terdaftar!"));
				exit;
			}
			$data = array(
				"username"	=> $input["email"],
				"nama"	=> $input["nama"],
				"nohp"	=> $input["nohp"],
				"level"	=> 1,
				"password"	=> $this->func->encode($input["password"])
			);
			$this->db->insert("userdata",$data);
			$usrid = $this->db->insert_id();

			$data = array(
				"usrid"	=> $usrid,
				"nama"	=> $input["nama"],
				"nohp"	=> $input["nohp"]
			);
			$this->db->insert("profil",$data);
			$this->db->insert("saldo",["usrid"=>$usrid,"saldo"=>0,"apdet"=>date("Y-m-d H:i:s")]);
			
			/*$pesan = "Terimakasih telah bergabung menjadi mitra OKE Kasir dan salam OKE pasti SUKSES!";
			$this->func->sendEmail($input["email"],"Pendaftaran OKE Kasir",$pesan,"Aplikasi OKE Kasir");*/
			$this->func->verifEmail($usrid);
			$this->func->verifWA($usrid);
			
			echo json_encode(array("success"=>true,"message"=>"berhasil"));
			
		}else{
			echo json_encode(array("success"=>false,"message"=>"Akses ditolak, silahkan masukkan data dengan benar"));
		}
	}
	function registerotp(){
		if(isset($_SERVER['HTTP_AUTHORIZATION'])){
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE);
			$type = isset($input["tipe"]) ? $input["tipe"] : "none";

			if(isset($input["email"]) AND $type == "none"){
				$this->db->where("username",$input["email"]);
				$this->db->or_where("nohp",$input["email"]);
				$this->db->limit(1);
				$db = $this->db->get("userdata");
				if($db->num_rows() > 0){
					echo json_encode(["success"=>false]);
					exit;
				}
				$set = $this->func->globalset("semua");

				$generator = "1357902468";
				$otp = "";
				for ($i = 1; $i <= 6; $i++) {
					$otp .= substr($generator, (rand()%(strlen($generator))), 1);
				}
				
				$array = array(
					"tgl"	=> date("Y-m-d H:i:s"),
					"emailhp"	=> $input["email"],
					"kode"	=> $otp,
					"kadaluarsa"=> date('Y-m-d H:i:s',strtotime('+10 minutes',strtotime(date("Y-m-d H:i:s")))),
					"status"=> 0
				);
				$this->db->insert("otpdaftar",$array);
				$otp_id = $this->db->insert_id();

				if(strpos($input["email"],"@") !== false){
					$pesan = "
						<b>PERHATIAN!</b><br/>".
						"JANGAN BERIKAN kode ini kepada siapa pun, TERMASUK TIM ".strtoupper(strtolower($set->nama))."<br/>".
						"WASPADA PENIPUAN!<br/>".
						"Untuk MASUK KE AKUN ".strtoupper(strtolower($set->nama)).", masukkan kode RAHASIA: <b>".$otp."</b>
					";
					$this->func->sendEmail($input["email"],$set->nama." - OTP Login",$pesan,"OTP Login");
				}else{
					$pesan = "
						*PERHATIAN!* \n".
						"JANGAN BERIKAN kode ini kepada siapa pun, TERMASUK TIM ".strtoupper(strtolower($set->nama))."\n".
						"WASPADA PENIPUAN! \n".
						"Untuk MASUK KE AKUN ".strtoupper(strtolower($set->nama)).", masukkan kode RAHASIA: *".$otp."*
					";
					$this->func->sendWAOK($input["email"],$pesan);
				}

				echo json_encode(array("success"=>true,"otpid"=>$otp_id));
			}elseif(isset($input["otpid"]) AND $type == "resend"){
				$this->db->where("id",$input["otpid"]);
				$db = $this->db->get("otpdaftar");
				$set = $this->func->globalset("semua");

				$generator = "1357902468";
				$otp = "";
				for ($i = 1; $i <= 6; $i++) {
					$otp .= substr($generator, (rand()%(strlen($generator))), 1);
				}
				
				if($db->num_rows() == 0){
					echo json_encode(array("success"=>false));
				}else{
					foreach($db->result() as $res){
						$otp_id = $input["otpid"];
						if($res->kadaluarsa < date("Y-m-d H:i:s")){
							$this->db->where("id",$input["otpid"]);
							$this->db->update("otpdaftar",["status"=>2]);

							$array = array(
								"tgl"	=> date("Y-m-d H:i:s"),
								"emailhp"	=> $res->emailhp,
								"kode"	=> $otp,
								"kadaluarsa"	=> date('Y-m-d H:i:s',strtotime('+10 minutes',strtotime(date("Y-m-d H:i:s")))),
								"status"=> 0
							);
							$this->db->insert("otpdaftar",$array);
							$otp_id = $this->db->insert_id();
						}else{
							$otp = $res->kode;
						}

						if(strpos($res->emailhp,"@") !== false){
							$pesan = "
								<b>PERHATIAN!</b><br/>".
								"JANGAN BERIKAN kode ini kepada siapa pun, TERMASUK TIM ".strtoupper(strtolower($set->nama))."<br/>".
								"WASPADA PENIPUAN!<br/>".
								"Untuk MASUK KE AKUN ".strtoupper(strtolower($set->nama)).", masukkan kode RAHASIA: <b>".$otp."</b>
							";
							$this->func->sendEmail($res->emailhp,$set->nama." - OTP Login",$pesan,"OTP Login");
						}else{
							$pesan = "
								*PERHATIAN!* \n".
								"JANGAN BERIKAN kode ini kepada siapa pun, TERMASUK TIM ".strtoupper(strtolower($set->nama))."\n".
								"WASPADA PENIPUAN! \n".
								"Untuk MASUK KE AKUN ".strtoupper(strtolower($set->nama)).", masukkan kode RAHASIA: *".$otp."*
							";
							$this->func->sendWAOK($res->emailhp,$pesan);
						}

						echo json_encode(array("success"=>true,"otpid"=>$otp_id));
					}
				}
			}elseif(isset($input["otpid"]) AND isset($input["otp"]) AND $type == "confirm"){
				$this->db->where("id",$input["otpid"]);
				$db = $this->db->get("otpdaftar");

				$pass = null;
				$aktif = false;
				if($db->num_rows() == 0){
					echo json_encode(array("success"=>false));
					exit;
				}
				foreach($db->result() as $res){
					$pass = $res->kode;
					$aktif = ($res->status == 0) ? false : true;
				}
				if($aktif == true){
					echo json_encode(array("success"=>false));
					exit;
				}
				
				$email = "";
				$nohp = "";
				if(strpos($res->emailhp,"@") !== false){
					$email = $res->emailhp;
				}else{
					$nohp = $res->emailhp;
				}

				if($input["otp"] == $pass){
					$this->db->insert("userdata",["status"=>1,"username"=>$email,"nohp"=>$nohp,"password"=>"","nama"=>"","tgl"=>date("Y-m-d H:i:s"),"level"=>1]);
					$usrid = $this->db->insert_id();
					$this->db->insert("profil",["usrid"=>$usrid,"nohp"=>$nohp,"nama"=>"User_".$usrid,"lahir"=>"0000-00-00","kelamin"=>0,"foto"=>"user.png"]);
					$this->db->insert("saldo",["usrid"=>$usrid,"saldo"=>0,"apdet"=>date("Y-m-d H:i:s")]);

					//$this->session->set_userdata("usrid",$usrid);
					//$this->session->set_userdata("lvl",1);
					//$this->session->set_userdata("status",1);

					$this->db->where("id",$input["otpid"]);
					$this->db->update("otpdaftar",["status"=>1,"masuk"=>date("Y-m-d H:i:s")]);
					//$this->session->unset_userdata("otp_id");
					
					//$r = $this->func->getUser($usrid,"semua");
					//$this->session->unset_userdata("otp_id");
					$token = md5(date("YmdHis").$usrid);
					//DESTROY OLD TOKEN SESSION
					$this->db->where("usrid",$usrid);
					$this->db->where("status",0);
					$this->db->update("token",array("status"=>2));
					//CREATE NEW TOKEN SESSION
					$data = array(
						"usrid"	=> $usrid,
						"tgl"	=> date("Y-m-d H:i:s"),
						"token"	=> $token,
						"status"=> 1
					);
					$this->db->insert("token",$data);
					
					echo json_encode(array("success"=>true,"level"=>1,"usrid"=>$usrid,"nama"=>"User_".$usrid,"saldo"=>0,"token"=>$token));
				}else{
					echo json_encode(array("success"=>false));
				}
			}else{
				echo json_encode(array("success"=>false));
			}
		}else{
			echo json_encode(array("success"=>false,"sesihabis"=>false));
		}
	}
	public function lupa(){
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, TRUE);
		
		if(isset($input["email"])){
			$this->db->where("username",$input["email"]);
			$this->db->or_where("nohp",$input["email"]);
			$this->db->limit(1);
			$db = $this->db->get("userdata");
			$nama = $this->func->globalset("nama");
			if($db->num_rows() > 0){
				foreach($db->result() as $r){
					//$this->func->sendEmail($r->email,"Reset password ".$nama,"Reset password","Aplikasi ".$nama);
					$this->func->resetPass($r->username);
					echo json_encode(array("success"=>true,"message"=>"Berhasil mereset password, silahkan cek email anda untuk detail password yang baru"));
				}
			}else{
				echo json_encode(array("success"=>false,"message"=>"Alamat Email atau No Handphone tidak terdaftar!"));
			}
		}else{
			echo json_encode(array("success"=>false,"message"=>"Masukkan alamat email/nomor handphone"));
		}
	}

}