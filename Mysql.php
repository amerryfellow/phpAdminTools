<?
/*
 * Mysql
 */
interface ___Database {
	public function listTables();
	public function tableDump($a, $b);
}

/**
 * Framework to access mysql with.
 *
 * @author		Andrea Paterno'
 * @version		0.1
 */
class __Mysql implements ___Database {
	private $username;
	private $password;
	private $host;
	private $database;
	private $port = null;
	private $link;

	/**
	 * Connects to the database.
	 *
	 * @throws	__Exception		Couldn't connect to the database.
	 */
	private function connect() {
		$this->link = mysql_connect($this->host.(($this->port) ? ':'.$this->port : ''), $this->username, $this->password);

		if(!$this->link) {
			throw new __Exception( __CLASS__, 0, 'Couldn\' connect to MySQL');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Select a db.
	 *
	 * @throws	__Exception		Couldn't select the database.
	 */
	public function dbSelect($db) {
		$r = mysql_select_db($this->database = $db, $this->link);

		if(!$r) {
			throw new __Exception( __CLASS__, 1, mysql_error() );
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * List Databases
	 *
	 * @return	Array list of databases
	 */
	public function listDatabases() {
		$r = mysql_query("SHOW DATABASES");
		
		$a = array();
		while($t = mysql_fetch_array($r))
			$a[] = $t[0];

		return $a;
	}
	
	/**
	 * Constructor
	 *
	 * @param	$h	Host
	 * @param	$u	Username
	 * @param	$p	Password
	 * @param	$n	Port = 3360
	 */
	function __construct($h, $u, $p, $n=3360) {
		// Checks

		$this->host = $h;
		$this->username = $u;
		$this->password = $p;
		$this->port = $n;

		$this->connect();
	}

	/**
	 * List all the tables in the database.
	 *
	 * @return	Array list of the tables
	 */
	public function listTables() {
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
			$tables[] = $row[0];
		return $tables;
	}

	/**
	 * Dumps the contents of a table/more tables.
	 *
	 * @param	$table		Table to be dumped. '*' for all the tables.
	 * @param	$stream		Stream to put the contents in
	 */
	public function tableDump($stream, $table = '*') {
		if(!($stream instanceof ___OutputStream)) {
			throw new __Exception( __CLASS__, 2, 'Stream not valid' );
			return false;
		}

		if($table == '*') {
			$tables = $this->listTables();
		} else {
			$tables = (is_array($table)) ? array($table) : explode(',',$table);
		}

		//cycle through
		foreach($tables as $table) {
			$result = mysql_query('SELECT * FROM '.$table);
			$num_fields = mysql_num_fields($result);

			$stream->pushln('DROP TABLE '.$table.';');

			$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
			$stream->push("\n\n".$row2[1].";\n\n");

			for ($i = 0; $i < $num_fields; $i++)  {
				while($row = mysql_fetch_row($result)) {
					$return = 'INSERT INTO '.$table.' VALUES(';

					for($j=0; $j<$num_fields; $j++) {
						$row[$j] = addslashes($row[$j]);
						$row[$j] = str_replace("\n","\\n",$row[$j]);

						$return .= '"'.$row[$j].'"';

						if($j<($num_fields-1))
							$return.= ',';
					}

					$stream->pushln($return.");");
				}
			}

			$stream->push("\n\n\n");
		}
	}
}

?>
