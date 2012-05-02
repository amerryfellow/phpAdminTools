<?
class __File() {
	private $path;
	private $info;
	private $readable;
	private $writable;
	private $executable;
	private $type;
	
	const FILE = 0;
	const DIR  = 1;
	const LINK = 2;
	const WTF  = 3;
		
	function __construct($path) {
		$this->path = $path;

		if(!file_exists($path)) {
			throw __Exception( __CLASS__, 0, 'File doesn\'t exist!' );
			return;
		}

		$this->update();
	}

	private function update() {
		// Directory or File?
		if(is_file($this->path))
			$this->type = self::FILE;
		elseif(is_dir($this->path))
			$this->type = self::DIR;
		elseif(is_link($this->path))
			$this->type = self::LINK;
		else
			$this->type = self::WTF;
		
		// Readable?
		$this->readable = is_readable($this->path);

		// Writable?
		$this->writable = is_writable($this->path);

		// Executable?
		$this->executable = is_executable($this->path);

		if($this->readable) {
			$h = fopen($this->path, 'r');
			$this->info = fstat($h);
			fclose($h);
		}
	}

	private function dump( $stream ) {
		if(!$stream instanceof __Stream) {
			throw __Exception( __CLASS__, 1, 'Stream provided isn\'t valid' );
			return false;
		}
		
		if(!$this->readable) {
			throw __Exception( __CLASS__, 2, 'File not readable' );
			return false;
		}

		if($this->type == self::DIR) {
			$a = new __Directory( $this );
			return $a->dump();
		}
		
		if($this->type == self::FILE) {
			$handle = fopen($this->path, 'rb');
	}	
}

class __Directory extends __File() {
	private $tree;
	private $index=0;
	
	function __construct( $tree_path ) {
		if( !file_exists( $tree_path ) ) {
			throw __Exception( __CLASS__, 0, 'Path doesn\' exist' );
			return;
		}

		parent::__construct( $tree_path );
		$this->tree = array_slice(scandir( $tree_path ), 2);
	}

	function goThrough() {
		if($this->index == count( $this->tree )) {
			$this->index = 0;
			return FALSE;
		}

		return $this->tree[ $this->index++ ];
	}

	function dump() {
		
	}
}
