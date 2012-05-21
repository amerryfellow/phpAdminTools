<?
// Does this class make sense? Why not use Exception?
class __Exception extends Exception {
	public $who;
	public $code;
	public $msg;

	function __construct($w, $c, $m) {
		$this->who = $w;
		$this->code = $c;
		$this->msg = $m;
	}

	function toString() {
		return $this->code.' - '.$this->msg."\n";
	}
}

interface ___InputStream {
	function pop($lol);
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
	private $type;

	public function __construct($type='text') {
		$this->type = $type;
		print '<pre>';
	}

	public function __destruct() {
		print '</pre>';
	}

	public function push($what) {
		print ($this->type == 'html') ? htmlentities($what) : $what;
	}

	public function pushln($what) {
		$this->push($what."\n");
	}
}

?>
