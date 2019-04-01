<?php
class CReq {

	public function Params(){return $this->p;}
	public function Query(){return $this->qP;}
	public function Headers(){return $this->hP;}
	public function Body($json=false){ 
        $ts = fopen('php://temp', 'r+');
        stream_copy_to_stream(fopen('php://input', 'r'), $ts);
        rewind($ts);
        $b = stream_get_contents($ts);
		return $json ? json_decode($b) : $b;
	}
	public function GetContentType(){return "";}

	function __construct($params, $m, $p){
		$this->method = $m;
		$this->template = $p;
		$this->p = $params;
		$this->qP = $this->aTo($_GET);
		$this->hP = $this->aTo(getallheaders());
	}

	function aTo($a){
		$o = new stdClass();
		foreach ($a as $k => $v) $o->$k = $v;
		return $o;
	}
}

class CRes {
	public function Get(){if (isset($this->o)) return $this->o;}
	public function Set($o){$this->o = $o;}
	public function SetContentType($c){$this->ct = $c;}
	public function GetContentType(){return $this->ct;}
	public function SetError($e){$this->e = $e;}
	public function GetError(){return isset($this->e) ? $this->e : null;}
	public function SetJSON($o,$s=true){$t = new stdClass(); $t->success = $s; $t->result = $o; $this->o = $t;}
}

class Carbite {

	static $cbf,$cbp, $cbfil, $gfil;
	static $rParts;
	static $m, $p, $opts;
	static $evts = [];

	public static function GET ($p, $f, $fil=null) {self::chk("GET", $p, $f, $fil);}
	public static function POST ($p, $f, $fil=null) {self::chk("POST", $p, $f, $fil);}
	public static function PUT ($p, $f, $fil=null) {self::chk("PUT", $p, $f, $fil);}
	public static function DELETE ($p, $f, $fil=null) {self::chk("DELETE",$p, $f, $fil);}
	public static function HANDLE ($m, $p, $f, $fil=null) {self::chk($m,$p, $f, $fil);}

	public static function GLOBALFILTER($f){
		if (!isset(self::$gfil)) self::$gfil = array();
			
		if (is_array($f)){
			foreach ($f as $f1)
				array_push(self::$gfil, $f);
		} else array_push(self::$gfil, $f); 
	}

	private static function trigger($event, $data=null){
		if (isset(self::$evts[$event])){
			foreach (self::$evts[$event] as $handler) {
				try{
					$handler($data);
				}catch (Exception $e){

				}
				
			}
		}
	}

	public static function AddEvent($event, $handler){
		if (!isset(self::$evts[$event]))
			self::$evts[$event] = [];

		array_push(self::$evts[$event], $handler);
	}

	public static function Reset(){
		self::$p = self::$m = self::$rParts = self::$gfil = self::$cbfil = self::$cbp = self::$cbf = null;
	}

	public static function SetAttribute($k,$v){
		if (!isset(self::$opts))
			self::$opts = new stdClass();
		self::$opts->$k = $v;
	}

	private static function getAttribute($k){
		$o = self::$opts;
		if (isset($o))
			if (isset($o->$k))
				return $o->$k;
	}


	public static function Start(){
		if (isset(self::$cbf)) return self::call(self::$cbf, self::$cbp);
		else {
			$o = self::getAttribute("no404");	
			
			if (!isset($o)){
				http_response_code(404); echo "404 : Not Found :[";
				self::trigger("notfound");
			}
		}
	}


	static function getRoute() {
		$ru = self::getAttribute("reqUri");
		$ru = isset($ru) ? $ru : $_SERVER['REQUEST_URI'];
		$bp = str_replace($_SERVER["DOCUMENT_ROOT"],"", str_replace("\\","/",__DIR__)) . "/";
		$r = str_replace(str_replace($_SERVER['DOCUMENT_ROOT'], "", $bp), "", $ru);
		if ($r[0] !== "/") $r = "/$r";
		return $r;
	}

	static function filterEval($req,$res){
		if (isset(self::$gfil))
			foreach (self::$gfil as $f) $f($req,$res);

		if (isset(self::$cbfil)){
			if (is_array(self::$cbfil)){
				foreach (self::$cbfil as $f) $f($req,$res);
			} else {$f = self::$cbfil;$f($req,$res);} 
		}
	}

	static function chk($m, $pa, $fu, $fil){
		$mdn = basename(dirname($_SERVER['SCRIPT_FILENAME']));
		$cbp = basename(__DIR__);
		if (strcmp($mdn, $cbp) != 0) $pa = "/$mdn$pa";
		
		if (strcmp($m, $_SERVER["REQUEST_METHOD"]) == 0) {

			$cParts = explode("/", $pa);
			$sIndex;
			if (strpos($pa, '*') !== false) {
				for ($i=0;$i<sizeof($cParts);$i++) {
					if (strlen($cParts[$i]) > 0)
					if ($cParts[$i][0] == '*'){
						$cParts[$i][0] = "@"; 
						$sIndex = $i;
						self::$rParts = null;
						break;
					}
				}
			}
		
			if (!isset(self::$rParts)) {

				$rPath = self::getRoute();
				$qi = strpos($rPath, '?');
				if ($qi) $rPath = substr($rPath, 0, $qi);
				$rParts = array_map('trim', explode('/', $rPath));
				
				if (isset($sIndex)){
					$newRp = array();
					$starVal = "";
					for ($i=0;$i<sizeof($rParts);$i++){
						if ($i < $sIndex)
							array_push($newRp,$rParts[$i]);
						else
							$starVal .= ("/" . $rParts[$i]);
					}
					array_push($newRp,$starVal);
					$rParts = $newRp;
				}
				self::$rParts = $rParts;
			}
				

			if (sizeof($cParts) == sizeof(self::$rParts)){
				$matched = true;
				$p = new stdClass();
				for($i=0; $i<sizeof($cParts); $i++)
					if (strlen($cParts[$i]) !=0){
						if ($cParts[$i][0] == '@'){
							$f = substr($cParts[$i], 1);
							$p->$f = self::$rParts[$i];
						} else {
							if (strcmp($cParts[$i], self::$rParts[$i]) != 0) { $matched = false; break; }
						}
					}
					
				if ($matched){self::$cbp = $p;self::$cbf = $fu;self::$m=$m; self::$p=$pa; self::$cbfil=$fil;}
			}

			if (isset($sIndex))
				self::$rParts = null;
		}
	}

	static function call($f, $p){
		$req = new CReq($p, self::$m, self::$p);
		$res = new CRes();
		self::filterEval($req,$res);
		$f($req, $res);
		self::out($res);
		self::trigger("completed");
		return $res;
	}

	public static function out($res){
		$out = $res->Get();		

		if (isset($out)){
			if (is_object($out) || is_array($out)){$ct = "application/json"; $out = json_encode($out, JSON_PRETTY_PRINT);}
			else {if (strlen($out) > 0) if ($out[0] == '{') $ct = "application/json";}
		}

		if (!isset($ct)) $ct = "text/plain";

		header("Content-type: ". $ct);
		echo $out;
	}
}

function CERR($en, $es, $ef, $el){
	$ec = new Exception($es);
	$ec->no = $en;
	//$ec->message = $es;
	$ec->filename = $ef;
	//$ec->line = $el;
	throw $ec;
}

function CEXP($e){
	$r = new CRes();
	$r->SetJSON($e,false);
	Carbite::out($r);
}

//set_error_handler("CERR", E_ALL);
//set_exception_handler("CEXP");
?>