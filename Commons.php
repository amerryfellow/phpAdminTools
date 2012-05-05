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

/*
 * This is not how it is meant to be.
 * TODO
 * Add file / print support
 */
interface ___Stream {
	function push();
	function pull();
}

class __Stream implements ___Stream {
	private $buffer = "";

	public function push($what) {
		$this->buffer .= $what;
	}

	public function pushln($what) {
		$this->push($what."\n");
	}

	public function pull() {
		return $this->buffer;
	}
}

?>
