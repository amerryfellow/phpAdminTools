<html>
<head>
<title>phpAdminToold</title>
<script type="text/javascript">
function backup(file) {
	lol = input('lol');
}
</script>
</head>
<body>

<?
require_once('Commons.php');

date_default_timezone_set('UTC');

// Default choice: files
$w = (array_key_exists('w', $_GET)) ? $_GET['w'] : 'files';

// Head
?>

<table><tr><td><a href="?w=files">Files</a></td><td><a href="?w=mysql">MySQL</a></td></tr></table>

<?

if($w == 'files') {
	require_once('Files.php');
	$file = (array_key_exists('file', $_GET) && $_GET['file'] != '') ? $_GET['file'] : '.';
	print "<h1>".$file."</h1>";
	$n = __File::open( $file );

	if($n->type == __File::DIR) {
		print '<h1>Directory</h1><table border="1"><thead><tr><td>File</td><td>uid</td><td>gid</td><td>Creation Time</td><td>Access Time</td><td>Modification Time</td><td>R</td><td>W</td><td>X</td></tr></thead>';
		while( $a = $n->goThrough() ) {
			print	'<tr>
				<td><a href="?file='.$a->path.'">'.$a->name.'</a></td>
				<td>'.$a->info['uid'].'</td>
				<td>'.$a->info['gid'].'</td>
				<td>'.date('d/m/Y W:H', $a->info['ctime']).'</td>
				<td>'.date('d/m/Y W:H', $a->info['atime']).'</td>
				<td>'.date('d/m/Y W:H', $a->info['mtime']).'</td>
				<td>'.$a->readable.'</td>
				<td>'.$a->writable.'</td>
				<td>'.$a->executable.'</td>
				<td><a href="javascript:backup(\''.$a->path.'\');">DumpToFile</td>
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

	try {
		$mysql = new __Mysql('localhost', 'root', '');
	} catch( __Exception $e ) {
		die($e->toString());
	}

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
