<?php

date_default_timezone_set("UTC");

function db_connect( $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   if ( !isset( $use_db ) )
   {
      try {
         $use_db = new MongoClient();
      } catch ( Exception $e ) {
         $db_errors = "Could not connect to the db " . $e->getMessage();
         if ( $error_json_exit )
         {
            $results = array( "error" => $db_errors );
            $results[ '_status' ] = 'complete';
            echo (json_encode($results));
            exit();
         }
         return false;
      }
   }

   return true;
}

function logjobstart( $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   if ( !db_connect( $error_json_exit ) )
   {
       return false;
   }
   $coll = $use_db->__application__->jobs;

   $now = new MongoDate();

   $insert[ '_id'          ] = $_REQUEST[ '_uuid'   ];
   $insert[ 'menu'         ] = $GLOBALS[ 'menu'     ];
   $insert[ 'module'       ] = $GLOBALS[ 'module'   ];
   $insert[ 'project'      ] = $GLOBALS[ 'project'  ];
   $insert[ 'user'         ] = $GLOBALS[ 'logon'    ];
   $insert[ 'directory'    ] = $GLOBALS[ 'dir'      ];
   $insert[ 'directorylog' ] = $GLOBALS[ 'logdir'   ];
   $insert[ 'command'      ] = $GLOBALS[ 'command'  ];
   $insert[ 'resource'     ] = $GLOBALS[ 'resource' ];
   $insert[ 'when'         ] = Array( $now );
   $insert[ 'start'        ] = $now;
   $insert[ 'status'       ] = Array( "started" );
   $insert[ 'remoteip'     ] = $GLOBALS[ 'REMOTE_ADDR' ];
   if ( isset( $GLOBALS[ 'modal' ] ) && $GLOBALS[ 'modal' ] ) {
       $insert[ 'modal' ] = true;
   }

   try {
      $coll->insert( $insert__~mongojournal{, array("j" => true )} );
   } catch(MongoCursorException $e) {
      $db_error = "Error updating the database. " . $e->getMessage();
      if ( $error_json_exit )
      {
         $results[ 'error' ] .= $db_error;
         $results[ '_status' ] = 'complete';
         echo (json_encode($results));
         exit();
      }
      return false;
   }
   return true;
}   

function logjobupdate( $status, $log_end = false, $error_json_exit = false, $uuid = false )
{
   global $use_db;
   global $db_errors;

   $GLOBALS['wascancelled'] = false;

   if ( !db_connect( $error_json_exit ) )
   {
       return false;
   }
   $coll = $use_db->__application__->jobs;

   $now = new MongoDate();
   $uuid = $uuid ? $uuid : $_REQUEST[ '_uuid' ];
   __~debug:cancel{error_log( "joblogupdate( $status,.. ) uuid: $uuid\n", 3, "/tmp/mylog" );}

   $update[ '$push' ][ 'when'    ] = $now;
   $update[ '$push' ][ 'status'  ] = $status;
   if ( $log_end )
   {
       $update[ '$set' ][ 'end' ] = $now;
       if ( $doc = $coll->findOne( array( "_id" => $uuid ) ) )
       {
           if ( in_array( "cancelled", $doc[ 'status' ] ) ) {
               __~debug:cancel{error_log( "joblogupdate( $status,.. ) skipping end since cancelled uuid: $uuid\n", 3, "/tmp/mylog" );}
               $GLOBALS['wascancelled'] = true;
               return true;
           }
           $update[ '$set' ][ 'duration' ] = $now->sec + $now->usec * 1.0e-6 - $doc[ 'start' ]->sec - $doc[ 'start' ]->usec * 1.0e-6;
       }
   }
   try {
      $coll->update( array( "_id" => $uuid ), 
                            $update__~mongojournal{, array("j" => true )} );
   } catch(MongoCursorException $e) {
      $db_errors = "Error updating the database in logjobupdate(). " . $e->getMessage();
      if ( $error_json_exit )
      {
         $results[ 'error' ] = $db_errors;
         $results[ '_status' ] = 'complete';
         echo (json_encode($results));
         exit();
      }
      return false;
   }
   return true;
}

function jqgrid_jobs( $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   if ( !db_connect( $error_json_exit ) )
   {
       return false;
   }

   $coll = $use_db->__application__->jobs;

   if ( $GLOBALS[ 'jqgrid_jobs' ] = $coll->find( array( "user" => $GLOBALS[ 'logon' ] ) ) )
   {
       return true;
   }
   return false;
}

function isprojectlocked( $checkproject,  $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   if ( !db_connect( $error_json_exit ) )
   {
       return false;
   }

   $coll = $use_db->__application__->joblock;

   if ( $doc = $coll->findOne( array( "name" => $checkproject ) ) )
   {
       return true;
   }
   return false;
}

function getprojectdir( $jobid,  $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   if ( !db_connect( $error_json_exit ) )
   {
       return false;
   }

   $coll = $use_db->__application__->jobs;

   if ( $doc = $coll->findOne( array( "_id" => $jobid, "user" => $GLOBALS[ 'logon' ] ) ) )
   {
      if ( isset( $doc[ 'directory' ] ) )
      {
         $GLOBALS[ "getprojectdir" ] = $doc[ 'directory' ];
         $GLOBALS[ "getprojectlogdir" ] = isset( $doc[ 'directorylog' ] ) ? $doc[ 'directorylog' ] : $doc[ 'directory' ];
         return true;
      }
      return false;
   }
   return false;
}

function getmenumodule( $jobid,  $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   if ( !db_connect( $error_json_exit ) )
   {
       return false;
   }

   $coll = $use_db->__application__->jobs;

   if ( $doc = $coll->findOne( array( "_id" => $jobid, "user" => $GLOBALS[ 'logon' ] ) ) )
   {
      if ( isset( $doc[ 'menu' ] ) && isset( $doc[ 'module' ] ) )
      {
         $GLOBALS[ "getmenumodule"        ] = $doc[ 'menu' ] . "/" . $doc[ 'module' ];
         $GLOBALS[ "getmenumoduleproject" ] = ( isset( $doc[ 'project'      ] ) && strlen( $doc[ 'project'       ] ) ) ? $doc[ 'project' ] : 'no_project_specified';
         $GLOBALS[ "getmenumoduledir"     ] = ( isset( $doc[ 'directory'    ] ) && strlen( $doc[ 'directory'     ] ) ) ? $doc[ 'directory' ] : '_no_project_dir_';
         $GLOBALS[ "getmenumodulelogdir"  ] = ( isset( $doc[ 'directorylog' ] ) && strlen( $doc[ 'directorylog'  ] ) ) ? $doc[ 'directorylog' ] : $GLOBALS[ "getmenumoduledir" ];
         $GLOBALS[ "getmenumodulestatus"  ] = ( isset( $doc[ 'status'       ] ) && count ( $doc[ 'status'        ] ) ) ? end($doc[ 'status' ] ) : '';
         $GLOBALS[ "wascancelled"         ] = $GLOBALS[ "getmenumodulestatus"  ] == "cancelled";
         return true;
      }
      return false;
   }
   return false;
}

function clearprojectlock( $projectdir,  $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   $GLOBALS[ 'lasterror' ] = "";
   $GLOBALS[ 'lastnotice' ] = "";

   $dk = end( explode( "/", $projectdir ) );
   if ( !isprojectlocked( $projectdir, $error_json_exit ) )
   {
      $GLOBALS[ 'lastnotice' ] = "Project '$dk' is not locked. ";
      return false;
   }

   $coll = $use_db->__application__->joblock;

   try {
       $coll->remove( array( "name" => $projectdir ), array( __~mongojournal{"j" => true, }"justOne" => true ));
   } catch(MongoCursorException $e) {
       if ( isprojectlocked( $projectdir, $error_json_exit ) )
       {
           $GLOBALS[ 'lasterror' ] = "Could not remove lock on project '$dk'. " . $e->getMessage();
       } else {
           $GLOBALS[ 'lastnotice' ] = "Project '$dk' is no longer locked. " . $e->getMessage();
           return true;
       }
   }
   return true;
}

function removejob( $jobid,  $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   $GLOBALS[ 'lasterror' ] = "";

   if ( !db_connect( $error_json_exit ) )
   {
      $GLOBALS[ 'lasterror' ] = $db_errors;
      return false;
   }

   $coll = $use_db->__application__->jobs;

   try {
      $coll->remove( array( "_id" => $jobid, "user" => $GLOBALS[ 'logon' ] ) );
   } catch(MongoCursorException $e) {
      $GLOBALS[ 'lasterror' ] = "Could not remove job $v. " . $e->getMessage();
      return false;
   }

   try {
      $use_db->msgs->cache->remove( array( "_id" => $jobid ) );
   } catch(MongoCursorException $e) {
//      $GLOBALS[ 'lasterror' ] = "Could not remove job $v. " . $e->getMessage();
//      return false;
   }
   return true;
}

function cached_msg( $jobid,  $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   $GLOBALS[ 'lasterror' ] = "";
   $GLOBALS[ 'cached_msg' ] = "";

   if ( !db_connect( $error_json_exit ) )
   {
      $GLOBALS[ 'lasterror' ] = $db_errors;
      return false;
   }

   if ( $doc = $use_db->msgs->cache->findOne( array( "_id" => $jobid ) ) )
   {
       $GLOBALS[ 'cached_msg' ] = $doc[ 'data' ];
       return true;
   } else {
       return false;
   }
}   

function cached_progress( $jobid,  $error_json_exit = false )
{
   global $use_db;
   global $db_errors;

   $GLOBALS[ 'lasterror' ] = "";
   $GLOBALS[ 'cached_progress' ] = "";

   if ( !db_connect( $error_json_exit ) )
   {
      $GLOBALS[ 'lasterror' ] = $db_errors;
      return false;
   }

   if ( $doc = $use_db->msgs->cache->findOne( array( "_id" => $jobid ) ) )
   {
      $json = json_decode( $doc[ 'data' ], true );
      if ( isset( $json[ '_progress' ] ) )
      {
         $GLOBALS[ 'cached_progress' ] = $json[ '_progress' ];
         return true;
      }
   }
   return false;
}

// take an array of files and extract the project directories
function get_projects( $files, $error_json_exit = false )
{
    $uniq = array_flip( preg_replace( '/\/.*/', '', $files ) );
    $base = $GLOBALS[ 'dir' ] . $GLOBALS[ 'logon' ] . "/";
    $result = array();
    foreach ( $uniq as $k => $v )
    {
        $result[] = $base . $k;
    }
    return $result;   
}

function get_projects_locked( $files, $error_json_exit = false )
{
    $uniq = array_flip( preg_replace( '/\/.*/', '', $files ) );
    $base = $GLOBALS[ 'dir' ] . $GLOBALS[ 'logon' ] . "/";
    $result = array();
    foreach ( $uniq as $k => $v )
    {
        $projdir = $base . $k;
        if ( isprojectlocked( $projdir, $error_json_exit ) )
        {
            $result[] = $k;
        }
    }
    return $result;
}

function logrunning( $error_json_exit = false ) {
    global $use_db;
    global $db_errors;

    $GLOBALS[ 'lasterror' ] = "";

    if ( !db_connect( $error_json_exit ) ) {
        $GLOBALS[ 'lasterror' ] = $db_errors;
        return false;
    }

    try {
        $use_db->__application__->running->update(
            array( "_id" => $_REQUEST[ '_uuid' ] ),
            array( 
                '$push' => array( "pid" => array( "where" => "local", "pid" => getmypid(), "what" => "parent" ) )
            ),
            array( "upsert" => true__~mongojournal{, "j" => true} ) 
            );
    } catch( MongoCursorException $e ) {
        $GLOBALS[ 'lasterror' ]  = "Error updating. " . $e->getMessage();
        return false;
    }

    return true;
}

function logstoprunning( $error_json_exit = false ) {
    global $use_db;
    global $db_errors;

    $GLOBALS[ 'lasterror' ] = "";

    if ( !db_connect( $error_json_exit ) ) {
        $GLOBALS[ 'lasterror' ] = $db_errors;
        return false;
    }

    try {
        $use_db->__application__->running->remove(
            array( "_id" => $_REQUEST[ '_uuid' ] ),
            array( "justOne" => true )
            );
    } catch( MongoCursorException $e ) {
        $GLOBALS[ 'lasterror' ]  = "Error updating. " . $e->getMessage();
        return false;
    }

    return true;
}

function jobcancel( $jobs,  $error_json_exit = false ) {
    __~debug:cancel{error_log( "jobcancel() called with jobs of " . print_r( $jobs, true ), 3, "/tmp/mylog" );}

    $GLOBALS[ 'lasterror' ] = "";
    $GLOBALS[ 'lastnotice' ] = "";

    global $use_db;
    global $db_errors;

    $appconfig = json_decode( file_get_contents( "__appconfig__" ), true );
    if ( !isset( $appconfig[ 'resources' ] ) ) {
        $GLOBALS[ 'lasterror' ] = "Internal error: could not find resource configuration information in appconfig.json";
        require_once "mail.php";
        error_mail( "[joblog.php jobcancel()] " . $GLOBALS[ 'lasterror' ] );
        return false;
    }
        
    if ( !db_connect( $error_json_exit ) )
    {
        $GLOBALS[ 'lasterror' ] = $db_errors;
        return false;
    }

    $context = new ZMQContext();
    $zmq_socket = $context->getSocket(ZMQ::SOCKET_PUSH, '__application__ udp pusher');
    $zmq_socket->connect("tcp://" . $appconfig['messaging']['zmqhostip'] . ":" . $appconfig['messaging']['zmqport'] );

    // $udp_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

    $runs = $use_db->__application__->running->find( array( "_id" => array( '$in' => $jobs ) ) );

    $fjobs = array_flip( $jobs );
    $projectdirs  = array();
    $tokillparent = array();
    $tokill       = array();
    $kjobs        = array();

    foreach ( $runs as $v ) {
       $uuid = $v['_id'];
       $job = $use_db->__application__->jobs->findOne( array( "_id" => $uuid ) );
       $pids = $v['pid'];
       foreach ( $pids as $k2 => $v2 ) {
           if ( $v2['pid'] < 2 ) {
               require_once "mail.php";
               error_mail( "[joblog.php jobcancel()] invalid pid for kill! " . $v2[ 'pid' ] );
           } else {
               $where = $v2['where'];
               if ( $v2['what'] == "parent" ) {
                   if ( !isset( $tokillparent[ $where ] ) ) {
                       $tokillparent[ $where ] = array( $v2[ 'pid' ] );
                   } else {
                       $tokillparent[ $where ][] = $v2[ 'pid' ];
                   }
               } else {
                   if ( !isset( $tokill[ $where ] ) ) {
                       $tokill[ $where ] = array( $v2[ 'pid' ] );
                   } else {
                       $tokill[ $where ][] = $v2[ 'pid' ];
                   }
               }
           }
       }
       unset( $fjobs[ $uuid ] );
       $kjobs[ $uuid ] = true;
       // send messages also if running about "cancelled"
       // also manually clear job locks and push update to jobs as in jobrun.php

       if ( !logjobupdate( "cancelled", true, $error_json_exit, $uuid ) ) {
           __~debug:cancel{error_log( "jobcancel() $uuid logjobupdate error $db_errors\n", 3, "/tmp/mylog" );}
       }
       logstoprunning();

       $zmq_socket->send( json_encode( array( "_uuid" => $uuid,
                                      "Notice" => "This job has been cancelled by user request",
                                      "_cancel" => "true",
                                      "_status" => "cancelled" ) ) );

       // $jsonmsg = json_encode( array( "_uuid" => $uuid,
       //                               "Notice" => "This job has been cancelled by user request",
       //                               "_status" => "cancelled" ) );
       
       // socket_sendto( $udp_socket, $jsonmsg, strlen( $jsonmsg ), 0, $appconfig['messaging'][ 'udphostip' ], $appconfig['messaging']['udpport'] );

       if ( getprojectdir( $uuid ) ) {
           $projectdirs[ $GLOBALS[ 'getprojectdir' ] ] = true;
       }
    }

    foreach ( $tokill as $k => $v ) {
        if ( !isset( $appconfig[ 'resources' ][ $k ] ) ) {
            $GLOBALS[ 'lasterror' ] .= "Resource $k missing from resource configuration information in appconfig.json";
            require_once "mail.php";
            error_mail( "[joblog.php jobcancel()] " . $GLOBALS[ 'lasterror' ] );
        } else {
            $kill = $appconfig[ 'resources' ][ $k ] . " __docroot:html5__/__application__/util/ga_killprocs.pl __docroot:html5__/__application__/log $k all " . implode( ' ', $v );
            __~debug:cancel{error_log( "jobcancel() $ " . $kill . " 2>&1\n", 3, "/tmp/mylog" );}
            ob_start();
            exec( $kill, $execout );
            __~debug:cancel{error_log( "jobcancel() kill output: " . implode( "\n", $execout ) . "\n", 3, "/tmp/mylog" );}
            ob_end_clean();
        }
    }   

    foreach ( $tokillparent as $k => $v ) {
        if ( !isset( $appconfig[ 'resources' ][ $k ] ) ) {
            $GLOBALS[ 'lasterror' ] .= "Resource $k missing from resource configuration information in appconfig.json";
            require_once "mail.php";
            error_mail( "[joblog.php jobcancel()] " . $GLOBALS[ 'lasterror' ] );
        } else {
            $kill = $appconfig[ 'resources' ][ $k ] . " __docroot:html5__/__application__/util/ga_killprocs.pl __docroot:html5__/__application__/log $k child " . implode( ' ', $v );
            __~debug:cancel{error_log( "jobcancel() $ " . $kill . " 2>&1\n", 3, "/tmp/mylog" );}
            ob_start();
            exec( $kill, $execout );
            __~debug:cancel{error_log( "jobcancel() kill children output: " . implode( "\n", $execout ) . "\n", 3, "/tmp/mylog" );}
            ob_end_clean();
        }
    }   

    foreach ( $projectdirs as $k => $v ) {
        clearprojectlock( $k );
    }

    $msgs = count( $kjobs ) ? ( "Job id" . ( count( $kjobs ) > 1 ? "s " : " " ) . implode( ",", array_keys( $kjobs ) ) . " cancelled." ) : "";
    $msgs .= count( $fjobs ) ? ( "\nNotice; Job id" . ( count( $fjobs ) > 1 ? "s " : " " ) . implode( ",", array_keys( $fjobs ) ) . " not running so not cancelled." ) : "";

    $GLOBALS[ 'lastnotice' ] = $msgs;
    return true;
}
?>
