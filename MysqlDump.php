<?
# Configuro i parametri di accesso al mio DB
$db_host = "localhost";
$db_user = "skiforum_user01";
$db_pass = "skiF7EDUMz2";
$db_name = "skiforum_dati2";

$link = mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name,$link);

/* backup the db OR just a table */
function backup_tables($tables = '*')
{

	$handle = fopen('./a/dump.sql', 'a');
		//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);

		fwrite($handle, 'DROP TABLE '.$table.';');
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		fwrite($handle, "\n\n".$row2[1].";\n\n");

		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return = 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) {
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			
				fwrite($handle, $return);
			}
		}
		fwrite($handle, "\n\n\n");
	}

	fclose($handle);
}

if(!array_key_exists('todo', $_GET)) die('?todo=');

switch($_GET['todo']) {
	case 'list':
		$tmp = mysql_query("SHOW TABLES");
		print "<pre>";
		while($arr = mysql_fetch_assoc($tmp))
			print $arr['Tables_in_skiforum_dati2']."\n";
		print "</pre>";
		
		break;
	default:
		backup_tables($_GET['todo']);
		print file_get_contents('./
		break;
}

?>
