<?

/**
 * Provides an easy and secure stream to access files
 * while taking care of possible memory exhaustions.
 *
 * @author		Andrea Paterno'
 * @version		0.1
 */
class __File implements ___GenericStream {
	private $path;
	private $info;
	private $readable;
	private $writable;
	private $executable;
	private $type;
	private $tmprhandle;
	
	const FILE = 0;
	const DIR  = 1;
	const LINK = 2;
	const WTF  = 3;

	// Max Buffer Size
	public $MBS = 1000;
		
	function __construct($path) {
		$this->path = $path;

		if(!file_exists($path)) {
			throw __Exception( __CLASS__, 0, 'File doesn\'t exist!' );
			return;
		}

		$this->update();
	}

	/*
	 * self::OPEN
	 * ---
	 * WHAT YOU GOTTA USE
	 */
	static function open($path) {
		if(@is_dir($path))
			return new __Directory($path);
		else
			return new __File($path);
	}

	/*
	 * UPDATE
	 * ---
	 * Updates file properties
	 */
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

	/*
	 * Reads $length characters from a file, starting at offset $offset.
	 *
	 * @param	$offset		Offset to start reading from
	 * @param	$length		How many bytes to read
	 * @return				Chosen portion of the file
	 */
	public function read( $offset = 0, $length = 0 ) {
		if($length = 0)
			$length = $this->info['size'];

		if(!$this->isreadable) {
			throw __Exception( __CLASS__, 2, 'File not readable' );
			return false;
		}
		
		if($length > $this->MBS) {
			throw __Exception( __CLASS__, 3, 'Specified bytes '.$length.' exceed maximum buffer size ('.$this->MBS.')');
			return false;
		}

		$handle = fopen($this->path, 'r');
		fseek($handle, $offset);

		return fread($handle, $length);
	}

	/*
	 * Writes data $what into a file, starting at offset $offset.
	 * If $offset == 'a', then it'll append $what to the file.
	 *
	 * Notes:
	 * This method does not take into account memory exhaustions,
	 * because the problem relies on variable storings. Once a certain
	 * $what is given, then we don't have to care about anything.
	 *
	 * @param	$what		Data to be written to file
	 * @param	$offset		Offset in the file
	 * @return				Number of bytes written
	 */
	private function write( $what, $offset = 0 ) {
		if(!$this->writable) {
			throw __Exception( __CLASS__, 4, 'File not writable' );
			return false;
		}

		if($offset == 'a') {
			$handle = fopen( $this->path, 'a' );
		} elseif( is_numeric($offset) ) {
			fseek($handle, $offset);
		} else {
			throw __Exception( __CLASS__, 5, 'Offset not valid' );
			return false;
		}

		// Should include max buffer size?

		return fwrite($handle, $what);
	}

	/**
	 * Dump the file contents.
	 *
	 * @param	$stream			Stream to put the files contents in
	 * @return					Nothing.
	 * @throws	__Exception		Stream invalid
	 * @throws	__Exception		File unreadable
	 */

	public function dump( $stream ) {
		if(!$stream instanceof __OutputStream) {
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
			$this->pop($stream);
		}
	}

	/*
	 * Reads a file safely in chunks of the size specified by MBS ( Max Buffer Size )
	 * and writes them in the stream $stream.
	 *
	 * @param	$stream			Stream to put the file contents in
	 * @throws	__Exception		File not readable
	 */
	public function pop($stream) {
		if(!$this->isreadable) {
			throw __Exception( __CLASS__, 3, 'File not readable' );
			return false;
		}

		$cycles = ceil(($this->info['size'])/$this->MBS);
		
		for($i=0;$i<$cycles; $i++) {
			$stream->push( $this->read( $i*$this->MBS, ($i+1)*$this->MBS) );
		}
	}

	/*
	 * Appends $what to file. Basically, just a synonym of the
	 * write method. Write mode is append.
	 *
	 * @param	$what	What to append
	 */
	public function push($what) {
		if(!$this->iswritable) {
			throw __Exception( __CLASS__, 4, 'File not writable' );
			return false;
		}

		$this->write($what, 'a');
	}
}

/**
 * Framework that provides useful but simple methods to access
 * directories. It's strictly related to the __File class, of which
 * is an extension, and provides additional methods, while overloading
 * previous ones.
 *
 * @author		Andrea Paterno'
 * @version		0.1
 */

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

	/**
	 * Go through a directory list.
	 *
	 * @return 		Element name
	 */
	private function goThrough() {
		if($this->index == count( $this->tree )) {
			$this->index = 0;
			return FALSE;
		}

		return $this->tree[ $this->index++ ];
	}

	/**
	 * Dumps the contents of a directory into a stream.
	 *
	 * @param	$stream		Stream to dump the directory elements in
	 */
	function dump($stream) {
		while($c = __File::open($this->goThrough()))
			$c->dump($stream);
	}
}
