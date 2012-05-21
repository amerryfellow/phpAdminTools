<?
require_once('Commons.php');

$w = (array_key_exists('w', $_GET)) ? $_GET['w'] : 'files';

if($w == 'files') {
	require_once('Files.php');
	$dir = (array_key_exists('dir', $_GET) && $_GET['dir'] != '') ? $_GET['dir'] : '.';
	print "<h1>".$dir."</h1>";
	$n = __File::open( $dir );

	if($n->type == __File::DIR) {
		print '<h1>Directory</h1><table>';
		while( $a = $n->goThrough() ) {
			print	'<tr>
				<td><a href="?dir='.$a->path.'">'.$a->name.'</a></td>
				<td>lol</td>
				</tr>';
		}
		print '</table>';
	} else {
		print '<pre>';
		$screen = new __Screen('html');

		try {
			$n->pop($screen);
		} catch( __Exception $e ) {
			print $e->msg;
			die();
		}
		print '</pre>';
	}
} elseif($w == 'mysql') {
	require_once('Mysql.php');

	$mysql = new __Mysql('localhost', 'root', '');
	$databases = $mysql->listDatabases();

	if(!array_key_exists('d', $_GET) OR $_GET['d'] == '') {
		print '<h1>Select Database</h1>';
		foreach($databases as $k => $a)
			print '<p><a href=\'?w=mysql&d='.$k.'\'>'.$a.'</a></p>';
	} else {
		// DB Select
		try {
			$mysql->dbSelect('mysql');
		} catch( __Exception $e ) {
			print $e->toString();
		}

		$tables = $mysql->listTables();

		if(!array_key_exists('t', $_GET) OR $_GET['t'] == '') {
			print '<h1>Select Table from '.$databases[$_GET['d']].'</h1>';
			foreach($tables AS $k => $a)
				print '<p><a href=\'?w=mysql&d='.$_GET['d'].'&t='.$k.'\'>'.$a.'</a></p>';
		} else {
			$stream = new __Screen();
			$mysql->tableDump($stream, $tables[$_GET['t']]);
		}
	}
}

?>
