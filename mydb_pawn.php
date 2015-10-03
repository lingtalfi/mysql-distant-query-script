<?php




require_once __DIR__ . "/../app/init.php";


//------------------------------------------------------------------------------/
// CONFIG
//------------------------------------------------------------------------------/
$dbName = MYSQL_DBNAME;
$user = PDOCONF_USER;
$pass = PDOCONF_PASS;
$programName = 'sketchManager';
$tmpConf = "tmpConf.cnf";

function scriptError($msg)
{
    global $programName;
    echo "$programName: fatal: " . $msg . PHP_EOL;
}

function onExit()
{
    global $tmpConf;
    @unlink($tmpConf);
}

// ensure that tmpConf file gets removed, since it contains sensitive data!
register_shutdown_function('onExit');

//------------------------------------------------------------------------------/
// TAMBOUILLE
//------------------------------------------------------------------------------/
$args = $_SERVER['argv'];
array_shift($args); // drop command name
$mysqlCmd = array_shift($args);


if (null !== $mysqlCmd) {

    if (true === chdir(__DIR__)) {

        // creating mysql temp conf file    
        @unlink($tmpConf);
        $s = <<<DDD
[client]
user=$user
password=$pass
DDD;


        if (!file_exists($tmpConf) && is_writable(dirname($tmpConf))) {
            umask(0077); // make sure other users can't read it
            file_put_contents($tmpConf, $s);

            // I like the -t option which makes the tabular boxes that mysql use in interactive mode.
            // Feel free to customize the mysql option that you like here 
            $cmd = "mysql --defaults-file=$tmpConf -t $dbName -e '" . str_replace("'", "\\'", $mysqlCmd) . "'";
            passthru($cmd);
        }
        else {
            scriptError("Could not create tmp file");
        }
    }
    else {
        scriptError("Sorry could not change directory");
    }


}
else {
    echo <<<EEE
Usage: <programName> <mysqlCommand>
Error: You forgot the mysql command.
EEE;

}