<?php
// scratch/dump_complete_sql.php
$dumpBin = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
$dest = 'c:\\xampp\\htdocs\\sia-project\\sia_highschool_complete_database.sql';
$cmd = "\"$dumpBin\" -u root --databases sia_highschool_db > \"$dest\"";
exec($cmd, $out, $ret);
if ($ret === 0) {
    echo "Dumped complete database to sia_highschool_complete_database.sql (" . filesize($dest) . " bytes)\n";
} else {
    echo "mysqldump failed with return code $ret\n";
}
