<?php
error_log( "jobrun start\n", 3, "/tmp/php_errors" );
function shutdown() {
    posix_kill(posix_getpid(), SIGHUP);
}

// Do some initial processing

// Switch over to daemon mode.

if ($pid = pcntl_fork())
    return;     // Parent

// ob_end_clean(); // Discard the output buffer and close

fclose(STDIN);  // Close all of the standard
fclose(STDOUT); // file descriptors as we
fclose(STDERR); // are running as a daemon.

register_shutdown_function('shutdown');

if (posix_setsid() < 0)
    return;

if ($pid = pcntl_fork())
    return;     // Parent 

error_log( "jobrun 1\n", 3, "/tmp/php_errors" );
date_default_timezone_set("UTC");
$logdir = "";

if ( isset( $_SERVER[ 'REMOTE_ADDR' ] ) )
{
    error_log( date( "Y M d H:i:s T", time() ) . " : " .  $argv[ 0 ] . " : called with a REMOTE_ADDR\n", 3, "/tmp/php_errors" );
    exit;
}

$cc = 1 + 3; // logon, id, checkrunning

// it might be one less, that's if no login is specified

error_log( "jobrun 2\n", 3, "/tmp/php_errors" );
if ( $argc < $cc - 1 || $argc > $cc )
{
    error_log( date( "Y M d H:i:s T", time() ) . " : " .  $argv[ 0 ] . " : incorrect number of arguments $argc != $cc\n", 3, "/tmp/php_errors" );
    exit;
}

$pos = 0;

$GLOBALS[ 'logon' ] = ( $argc == $cc ) ? $argv[ ++$pos ] : "";
$id = $argv[ ++$pos ];
$_REQUEST[ '_uuid' ] = $id;
$checkrunning = $argv[ ++$pos ];

error_log( "jobrun 3\n", 3, "/tmp/php_errors" );
require_once "/var/www/html/abhishektest/ajax/joblog.php";

if ( !getmenumodule( $id ) )
{
    error_log( date( "Y M d H:i:s T", time() ) . " : " .  $argv[ 0 ] . " : could not find job $id in database\n", 3, "/tmp/php_errors" );
    exit;
}

error_log( "jobrun 4\n", 3, "/tmp/php_errors" );
ob_start();
if ( FALSE === ( $cmd = file_get_contents( "${logdir}_cmds_$id" ) ) )
{
   $cont = ob_get_contents();
   ob_end_clean();
   error_log( date( "Y M d H:i:s T", time() ) . " : " .  $argv[ 0 ] . " : _cmds_$id missing : $cont\n", 3, "/tmp/php_errors" );
   exit;
}

error_log( "jobrun 5\n", 3, "/tmp/php_errors" );
$json = json_decode( file_get_contents( "/home/abhishek/Desktop/GenApp/abhishektest/appconfig.json" ) );
error_log( "jobrun 6\n", 3, "/tmp/php_errors" );
$context = new ZMQContext();
error_log( "jobrun 7\n", 3, "/tmp/php_errors" );
$zmq_socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'abhishektest udp pusher');
error_log( "jobrun 8\n", 3, "/tmp/php_errors" );
$zmq_socket->connect("tcp://" . $json->messaging->zmqhostip . ":" . $json->messaging->zmqport );

error_log( "jobrun 9\n", 3, "/tmp/php_errors" );
logjobupdate( "running" );
error_log( "jobrun 10\n", 3, "/tmp/php_errors" );
$zmq_socket->send( '{"_uuid":"' . $id . '","_status":"running"}' );

error_log( "jobrun 11\n", 3, "/tmp/php_errors" );
logrunning();
error_log( "jobrun 12\n", 3, "/tmp/php_errors" );

$results = exec( $cmd );

error_log( "$cmd\n", 3, "/tmp/php_errors" );

error_log( "$results\n", 3, "/tmp/php_errors" );
//
logjobupdate( "finished", true );
error_log( "jobrun 14\n", 3, "/tmp/php_errors" );
logstoprunning();
error_log( "jobrun 15\n", 3, "/tmp/php_errors" );

if ( !$GLOBALS[ 'wascancelled' ] ) {
    $results = str_replace( "/var/www/html/abhishektest/", "", $results );

    ob_start();
    if ( FALSE === file_put_contents( "${logdir}_stdout_" . $_REQUEST[ '_uuid' ], $results ) ) {
        $cont = ob_get_contents();
        error_log( date( "Y M d H:i:s T", time() ) . " : " .  $argv[ 0 ] . " : error writing _stdout results\n", 3, "/tmp/php_errors" );
    }
    ob_end_clean();
}

if ( $checkrunning == 1 )
{
   if( !clearprojectlock( $GLOBALS[ 'getmenumoduledir' ] ) )
   {
// error ignored since there may not be job control
//      error_log( date( "Y M d H:i:s T", time() ) . " : " .  $argv[ 0 ] . " : " . $GLOBALS[ 'getmenumoduledir' ] . " : error clearprojectlock " . $GLOBALS[ 'lasterror' ] . "\n", 3, "/tmp/php_errors" );
   }
}

if ( !$GLOBALS[ 'wascancelled' ] ) {
    $zmq_socket->send( '{"_uuid":"' . $id . '","_status":"complete"}' );
}
?>

