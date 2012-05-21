<?
require_once('Commons.php');
require_once('Files.php');
require_once('Mysql.php');

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

?>
