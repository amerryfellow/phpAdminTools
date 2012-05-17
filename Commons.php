<?

class __Exception {
	public $who;
	public $code;
	public $msg;

	function __construct($w, $c, $m) {
		$this->who = $w;
		$this->code = $c;
		$this->msg = $m;
	}

	function toString() {
		return $code.' - '.$msg."\n";
	}
}

interface ___InputStream {
	function pull();
}

interface ___OutputStream {
	function push($lol);
}

interface ___GenericStream extends ___InputStream, ___OutputStream {
	//
}

/*
 * Output buffer - prints to screen
 */
class __Screen implements ___OutputStream {
	public function push($what) {
		print $what;
	}

	public function pushln($what) {
		$this->push($what."\n");
	}
}

?>
