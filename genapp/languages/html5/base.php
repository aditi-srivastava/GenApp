<?php
header('Content-type: application/json');

# setup php session
session_start();
if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 0;
} else {
  $_SESSION['count']++;
}

if ( !sizeof( $_REQUEST ) )
{
    session_write_close();
    require_once "../mail.php";
    $msg = "[PHP code received no \$_REQUEST] Possibly total upload file size exceeded limit.\nLimit is currently set to " . ini_get( 'post_max_size' ) . ".\n";
    error_mail( $msg . "Error occured in __menu:id__ __menu:modules:id__.\n" );
    $results = array("error" => $msg . "Please contact the administrators via feedback if you feel this is in error or if you have need to process total file sizes greater than this limit.\n" );
    $results[ '_status' ] = 'failed';
    echo (json_encode($results));
    exit();
}

$do_logoff = 0;

$window = "";
if ( isset( $_REQUEST[ '_window' ] ) )
{
   $window = $_REQUEST[ '_window' ];
}
if ( !isset( $_SESSION[ $window ] ) )
{
   $_SESSION[ $window ] = array( "logon" => "", "project" => "" );
}

if ( $_REQUEST[ "_logon" ] != $_SESSION[ $window ][ 'logon' ] )
{
   $do_logoff = 1;
   unset( $_SESSION[ $window ][ 'logon' ] );
   $results[ '_logon' ] = "";
}

if ( !isset( $_REQUEST[ '_uuid' ] ) )
{
    $results[ "error" ] = "No _uuid specified in the request";
    $results[ '_status' ] = 'failed';
    echo (json_encode($results));
    exit();
}

$cmd = isset( $_REQUEST[ '_docrootexecutable' ] ) ? "__docroot:html5__/__application__/" . $_REQUEST[ '_docrootexecutable' ] : "__executable_path:html5__/__executable__";
if ( !is_executable( $cmd ) )
{
    $results[ "error" ] = "command not found or not executable $cmd";
    $results[ '_status' ] = 'failed';
    echo (json_encode($results));
    exit();
}

require_once "../joblog.php";

$GLOBALS[ 'module'  ] = "__menu:modules:id__";
$GLOBALS[ 'menu'    ] = "__menu:id__";
$GLOBALS[ 'logon'   ] = isset( $_SESSION[ $window ][ 'logon' ] ) ? $_SESSION[ $window ][ 'logon' ] : 'not logged in';
$GLOBALS[ 'project' ] = isset( $_REQUEST[ '_project' ] ) ? $_REQUEST[ '_project' ] : 'not in a project';
$GLOBALS[ 'command' ] = $cmd;
$GLOBALS[ 'REMOTE_ADDR' ] = isset( $_SERVER[ 'REMOTE_ADDR' ] ) ? $_SERVER[ 'REMOTE_ADDR' ] : "not from an ip";

// if user based, use alternate directory structure
__~uniquedir{$uniquedir = "__uniquedir__";}

__~nojobcontrol{$nojobcontrol = 1;$GLOBALS[ 'modal' ] = true;}

$bdir = "";

$adir = "__docroot:html5__/__application__";

if ( !isset( $uniquedir ) &&
     isset( $_SESSION[ $window ][ 'logon' ] ) &&
     strlen( $_SESSION[ $window ][ 'logon' ] ) > 1 )
{
   $dir = "__docroot:html5__/__application__/results/users/" . $_SESSION[ $window ][ 'logon' ] . "/";
   $bdir = $dir;
   if ( isset( $_REQUEST[ '_project' ] ) &&
        strlen( $_REQUEST[ '_project' ] ) > 1 )
   {
      $dir .= $_REQUEST[ '_project' ];
   } else {
      $dir .= 'no_project_specified';
   }
   $checkrunning     = $dir;
// connect
   if ( !isset( $nojobcontrol ) )
   {
      db_connect( true );
      $coll = $use_db->__application__->joblock;
      if ( $doc = $coll->findOne( array( "name" => $checkrunning ) ) )
      {
          $results[ 'error' ] = "A job is already running in this project, please wait until it completes or change projects";
          $results[ '_status' ] = 'failed';
          echo (json_encode($results));
          exit();
      }
      try {
          $coll->insert( array( "name" => $checkrunning )__~mongojournal{, array("j" => true )});
      } catch(MongoCursorException $e) {
          $results[ 'error' ] = "A job is already running in this project, please wait until it completes or change projects. " . $e->getMessage();
          $results[ '_status' ] = 'failed';
          echo (json_encode($results));
          exit();
      }
   }
} else {
   do {
       $dir = uniqid( "__docroot:html5__/__application__/results/" );
   } while( file_exists( $dir ) );
}
$GLOBALS[ 'dir' ] = $dir;

$logdir = "$dir__~logdirectory{/__logdirectory__}";
$GLOBALS[ 'logdir' ] = $logdir; 

if ( !file_exists( $dir ) )
{
   ob_start();

   if ( !mkdir( $dir, 0777, true ) )
   {  
      $cont = ob_get_contents();
      ob_end_clean();
      if ( isset( $checkrunning ) )
      {
         try {
             $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{ "j" => true,} "justOne" => true ));
         } catch(MongoCursorException $e) {
             $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
         }
      }
      $results[ "error" ] .= "Could not create directory " . $dir . " " . $cont;
      $results[ '_status' ] = 'failed';
      echo (json_encode($results));
      exit();
   }
   chmod( $dir, 0775 );
   ob_end_clean();
   $results[ "_fs_clear" ] = "#";
}

if ( !file_exists( $logdir ) )
{
   ob_start();

   if ( !mkdir( $logdir, 0777, true ) )
   {  
      $cont = ob_get_contents();
      ob_end_clean();
      if ( isset( $checkrunning ) )
      {
         try {
             $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{ "j" => true,} "justOne" => true ));
         } catch(MongoCursorException $e) {
             $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
         }
      }
      $results[ "error" ] .= "Could not create directory " . $logdir . " " . $cont;
      $results[ '_status' ] = 'failed';
      echo (json_encode($results));
      exit();
   }
   chmod( $logdir, 0775 );
   ob_end_clean();
   $results[ "_fs_clear" ] = "#";
}

$_REQUEST[ '_base_directory' ] = $dir;
$_REQUEST[ '_log_directory' ] = $logdir;

if ( !isset( $_SESSION[ $window ][ 'udpport' ] ) ||
     !isset( $_SESSION[ $window ][ 'udphost' ] ) || 
     !isset( $_SESSION[ $window ][ 'resources' ] ) ||
     !isset( $_SESSION[ $window ][ 'submitpolicy' ] ) )
{
   $appjson = json_decode( file_get_contents( "__appconfig__" ) );
   $_SESSION[ $window ][ 'udphost'         ] = $appjson->messaging->udphostip;
   $_SESSION[ $window ][ 'udpport'         ] = $appjson->messaging->udpport;
   $_SESSION[ $window ][ 'resources'       ] = $appjson->resources;
   $_SESSION[ $window ][ 'resourcedefault' ] = $appjson->resourcedefault;
   $_SESSION[ $window ][ 'submitpolicy'    ] = $appjson->submitpolicy;
}
session_write_close();

$_REQUEST[ '_udphost' ] =  $_SESSION[ $window ][ 'udphost' ];
$_REQUEST[ '_udpport' ] =  $_SESSION[ $window ][ 'udpport' ];
$_REQUEST[ 'resourcedefault' ] = $_SESSION[ $window ][ 'resourcedefault' ];

__~resource{$useresource = "__resource__";}
__~submitpolicy{$submitpolicy = "__submitpolicy__";}

if ( !isset( $submitpolicy ) )
{
   if ( isset( $_SESSION[ $window ][ 'submitpolicy' ] ) &&
        $_SESSION[ $window ][ 'submitpolicy' ] == "login" &&
        ( !isset( $_SESSION[ $window ][ 'logon' ] ) ||
          strlen( $_SESSION[ $window ][ 'logon' ] ) == 0 ) )
   {
         if ( isset( $checkrunning ) )
         {
            try {
                $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
            } catch(MongoCursorException $e) {
                $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
            }
         }

         $results[ "error" ] .= "You must be logged on to submit";
         $results[ '_status' ] = 'failed';
         echo (json_encode($results));
         exit();
   }
} else {
   if ( $submitpolicy == "login" &&
        ( !isset( $_SESSION[ $window ][ 'logon' ] ) ||
          strlen( $_SESSION[ $window ][ 'logon' ] ) == 0 ) )
   {
         if ( isset( $checkrunning ) )
         {
            try {
                $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
            } catch(MongoCursorException $e) {
                $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
            }
         }

         $results[ "error" ] .= "You must be logged on to submit";
         $results[ '_status' ] = 'failed';
         echo (json_encode($results));
         exit();
   }
}

$cmdprefix = "";

if ( isset( $_SESSION[ $window ][ 'resourcedefault' ] ) &&
     $_SESSION[ $window ][ 'resourcedefault' ] == "disabled" )
{
      if ( isset( $checkrunning ) )
      {
         try {
             $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
         } catch(MongoCursorException $e) {
             $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
         }
      }
      $results[ "error" ] .= "Job submission is currently disabled";
      $results[ '_status' ] = 'failed';
      echo (json_encode($results));
      exit();
}

if ( isset( $useresource ) &&
     !isset( $_SESSION[ $window ][ 'resources' ]->$useresource ) )
{
      if ( isset( $checkrunning ) )
      {
         try {
             $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
         } catch(MongoCursorException $e) {
             $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
         }
      }

      $results[ "error" ] .= "module specified resource " . $useresource . " is not defined in appconfig";
      $results[ '_status' ] = 'failed';
      echo (json_encode($results));
      exit();
}

if ( !isset( $_SESSION[ $window ][ 'resources' ]->$_SESSION[ $window ][ 'resourcedefault' ] ) &&
     !isset( $useresource ) )
{
     if ( isset( $checkrunning ) )
     {
        try {
            $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
        } catch(MongoCursorException $e) {
            $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
        }
     }
     $results[ "error" ] .= "No default resource specified in appconfig and no resource defined in module";
     $results[ '_status' ] = 'failed';
     echo (json_encode($results));
     exit();
} else {
   if ( isset( $useresource ) )
   {
      $cmdprefix = $_SESSION[ $window ][ 'resources' ]->$useresource;
      $GLOBALS[ 'resource' ] = $useresource;
   } else {
      $cmdprefix = $_SESSION[ $window ][ 'resources' ]->$_SESSION[ $window ][ 'resourcedefault' ];
      $GLOBALS[ 'resource' ] = $_SESSION[ $window ][ 'resourcedefault' ];
   }
  if(isset($cmdprefix->run)){
      $cmdprefix = $cmdprefix->run;
  }
   if ( strlen( $cmdprefix ) > 1 )
   {  
      $fileargs = 1;
   }
}

$org_request = $_REQUEST;

// date_default_timezone_set("UTC");
// $org_request[ '_datetime' ] = date( "Y M d H:i:s T", time() );

function fileerr_msg($code)
{
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            break;
        case UPLOAD_ERR_PARTIAL:
            $message = "The uploaded file was only partially uploaded";
            break;
        case UPLOAD_ERR_NO_FILE:
            $message = "No file was uploaded";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $message = "Missing a temporary folder";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $message = "Failed to write file to disk";
            break;
        case UPLOAD_ERR_EXTENSION:
            $message = "File upload stopped by extension";
            break;
         default:
            $message = "Unknown upload error";
            break;
    }
    return $message;
} 

__~debug:basemylog{error_log( "request\n" . print_r( $_REQUEST, true ) . "\n", 3, "/tmp/mylog" );}

if ( sizeof( $_FILES ) )
{
__~debug:basemylog{   error_log( "files\n" . print_r( $_FILES, true ) . "\n", 3, "/tmp/mylog" );}
   foreach ( $_FILES as $k=>$v )
   {
      if ( is_array( $v[ 'error' ] ) )
      {
         foreach ( $v[ 'error' ] as $k1=>$v1 )
         {
            if ( $v[ 'error' ][ $k1 ] )
            {
               if ( isset( $checkrunning ) )
               {
                  try {
                      $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
                  } catch(MongoCursorException $e) {
                      $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
                  }
               }
               if ( !isset( $results[ "error" ] ) )
               {
                   $results[ "error" ] = "";
               }
               if ( is_string( $v[ 'name' ][ $k1 ] ) && !strlen( $v[ 'name' ][ $k1 ] ) )
               {
                  $results[ "error" ] .= "Missing file input for identifier " . $k;
               } else {
                  $results[ "error" ] .= "Error uploading file " . $v[ 'name' ][ $k1 ] . " Error code:" . $v[ 'error' ][ $k1 ] . " " . fileerr_msg( $v[ 'error' ][ $k1 ] );
               }
               $results[ '_status' ] = 'failed';
               echo (json_encode($results));
               exit();
            }
//            error_log( "move_uploaded_file( " . $v[ 'tmp_name' ][ $k1 ] . ',' .  $dir . '/' . $v[ 'name' ][ $k1 ] . "\n", 3, "/var/tmp/my-errors.log");
            if ( !move_uploaded_file( $v[ 'tmp_name' ][ $k1 ], $dir . '/' . $v[ 'name' ][ $k1 ] ) )
            {
               if ( isset( $checkrunning ) )
               {
                  try {
                      $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
                  } catch(MongoCursorException $e) {
                      $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
                  }
               }
               if ( !isset( $results[ "error" ] ) )
               {
                   $results[ "error" ] = "";
               }
               $results[ "error" ] .= "Could not move file " . $v[ 'name' ][ $k1 ];
               $results[ '_status' ] = 'failed';
               echo (json_encode($results));
               exit();
            }
            if ( !isset( $_REQUEST[ $k ] ) || !is_array( $_REQUEST[ $k ] ) )
            {
               $_REQUEST[ $k ] = array();
            }
            $_REQUEST[ $k ][] = $dir . '/' . $v[ 'name' ][ $k1 ];
            if ( !isset( $org_request[ $k ] ) || !is_array( $org_request[ $k ] ) )
            {
               $org_request[ $k ] = array();
            }
            $org_request[ $k ][] = $v[ 'name' ][ $k1 ];
         }
      } else {
         if ( $v[ 'error' ] == 4 &&
              isset( $_REQUEST[ '_selaltval_' . $k ] ) &&
              isset( $_REQUEST[ $_REQUEST[ '_selaltval_' . $k ] ] ) &&
              count( $_REQUEST[ $_REQUEST[ '_selaltval_' . $k ] ] ) == 1 ) 
         {
            $f = $bdir . substr( base64_decode( $_REQUEST[ $_REQUEST[ '_selaltval_' . $k ] ][ 0 ] ), 2 );
            if ( !file_exists( $f ) )
            {
               if ( isset( $checkrunning ) )
               {
                  try {
                      $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
                  } catch(MongoCursorException $e) {
                      $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
                  }
               }
               $results[ "error" ] = "missing file input for identifier " . $k;
               $results[ '_status' ] = 'failed';
               echo (json_encode($results));
               exit();
            }

            if ( !isset( $_REQUEST[ $k ] ) || !is_array( $_REQUEST[ $k ] ) )
            {
               $_REQUEST[ $k ] = array();
            }
            $_REQUEST[ $k ][] = $f;
            unset( $_REQUEST[ $_REQUEST[ '_selaltval_' . $k ] ] );
            unset( $_REQUEST[ '_selaltval_' . $k ] );
         } else {
            if ( $v[ 'error' ] )
            {
               if ( isset( $checkrunning ) )
               {
                  try {
                      $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
                  } catch(MongoCursorException $e) {
                      $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
                  }
               }
               if ( !isset( $results[ "error" ] ) )
               {
                   $results[ "error" ] = "";
               }
               if ( is_string( $v[ 'name' ] ) && !strlen( $v[ 'name' ] ) )
               {
                  $results[ "error" ] .= "missing file input for identifier " . $k;
               } else {
                  $results[ "error" ] .= "Error uploading file " . $v[ 'name' ] . " Error code:" . $v[ 'error' ] . " " . fileerr_msg( $v[ 'error' ] );
               }
               $results[ '_status' ] = 'failed';
               echo (json_encode($results));
               exit();
            }
//         error_log( "move_uploaded_file( " . $v[ 'tmp_name' ] . ',' .  $dir . '/' . $v[ 'name' ] . "\n", 3, "/var/tmp/my-errors.log");
            if ( !move_uploaded_file( $v[ 'tmp_name' ], $dir . '/' . $v[ 'name' ] ) )
            {
               if ( isset( $checkrunning ) )
               {
                  try {
                      $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
                  } catch(MongoCursorException $e) {
                      $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
                  }
               }
               $results[ "error" ] .= "Could not move file " . $v[ 'name' ];
               $results[ '_status' ] = 'failed';
               echo (json_encode($results));
               exit();
            }
            if ( !isset( $_REQUEST[ $k ] ) || !is_array( $_REQUEST[ $k ] ) )
            {
               $_REQUEST[ $k ] = array();
            }
            $_REQUEST[ $k ][] = $dir . '/' . $v[ 'name' ];
            if ( !isset( $org_request[ $k ] ) || !is_array( $org_request[ $k ] ) )
            {
               $org_request[ $k ] = array();
            }
            $org_request[ $k ][] = $v[ 'name' ];
         }
      }
   }
}

if ( sizeof( $_REQUEST ) )
{
    ob_start();
    if ( !file_put_contents( "$logdir/_input_" . $_REQUEST[ '_uuid' ], json_encode( $org_request  ) ) )
    {
        $cont = ob_get_contents();
        ob_end_clean();
        if ( isset( $checkrunning ) )
        {
            try {
                $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
            } catch(MongoCursorException $e) {
                $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
            }
        }
        $results[ "error" ] .= "Could not write _input file data " . $cont;
        $results[ '_status' ] = 'failed';
        echo (json_encode($results));
        exit();
    }
    ob_end_clean();
    unset( $org_request );

    $decodekeys = preg_grep( '/^_decodepath_/', array_keys( $_REQUEST ) );
__~debug:basemylog{    error_log( "decode keys\n" . print_r( $decodekeys, true ) . "\n", 3, "/tmp/mylog" );}
    foreach ( $decodekeys as $v ) {                      
        $v1 = substr( $v, 12 );
__~debug:basemylog{        error_log( "decode key $v -> $v1\n", 3, "/tmp/mylog" );}
        if ( isset( $_REQUEST[ $v1 ] ) ) {
__~debug:basemylog{            error_log( "is set request $v1\n", 3, "/tmp/mylog" );}
            foreach ( $_REQUEST[ $v1 ] as $k2=>$v2 ) {
__~debug:basemylog{                error_log( "foreach set request $v1: $k2 => $v2\n", 3, "/tmp/mylog" );}
                $_REQUEST[ $v1 ][ $k2 ] = $bdir . substr( base64_decode( $v2 ), 2 );
            }
        } else {
__~debug:basemylog{            error_log( "is NOT set request $v1\n", 3, "/tmp/mylog" );}
        }
    }

    $keys = preg_grep( "/-/", array_keys( $_REQUEST ) );
    foreach ( $keys as $k => $v )
    {
        $a = preg_split( "/-/", $v );
        $_REQUEST[ $a[ 0 ] ][ $a[ 1 ] - 1 ] = $_REQUEST[ $v ];
        unset( $_REQUEST[ $v ] );
    }

__~debug:basemylog{    error_log( "request ready to jsonize\n" . print_r( $_REQUEST, true ) . "\n", 3, "/tmp/mylog" );}
    $json = json_encode( $_REQUEST );
    $json = str_replace( "'", "_", $json );
    ob_start();
    if ( !chdir( $dir ) )
    {
      $cont = ob_get_contents();
      ob_end_clean();
      if ( isset( $checkrunning ) )
      {
         try {
             $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
         } catch(MongoCursorException $e) {
             $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
         }
      }
      $results[ "error" ] .= "Could not create directory " . $dir . " " . $cont;
      $results[ '_status' ] = 'failed';
      echo (json_encode($results));
      exit();
    }
    ob_end_clean();
    if ( isset( $fileargs ) )
    {
      ob_start();
      if (!file_put_contents( "$logdir/_args_" . $_REQUEST[ '_uuid' ], $json ) )
      {
         $cont = ob_get_contents();
         ob_end_clean();
         if ( isset( $checkrunning ) )
         {
            try {
                $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
            } catch(MongoCursorException $e) {
                $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
            }
         }
         $results[ "error" ] .= "Could not write _args for remote submission " . $cont;
         $results[ '_status' ] = 'failed';
         echo (json_encode($results));
         exit();
      }
      ob_end_clean();
      if($cmdprefix == "airavatarun"){ 
         $cmd = "$adir/$cmdprefix";
         $cmd .= " __menu:modules:id__";
         $cmd .= " '$json'"; 
      }else{
        $register = "perl $adir/util/ga_regpid_udp.pl __application__ " . 
        $GLOBALS['resource'] . " " . 
        $_REQUEST[ '_udphost' ] . " " .
        $_REQUEST[ '_udpport' ] . " " .
        $_REQUEST[ '_uuid' ] . " " .
        '$$';

        $cmd = "$cmdprefix '$register;cd $dir;$cmd \"\$(< $logdir/_args_" . $_REQUEST[ '_uuid' ] . ")\"'";
      }
    } else {
      $cmd .= " '$json'";
    }

    $cmd .= " 2> $logdir/_stderr_" . $_REQUEST[ '_uuid' ] . " | head -c50000000";
//    error_log( "\tcmd: <$cmd>\n", 3, "/tmp/mylog" );

    $cmdfile = "$logdir/_cmds_" . $_REQUEST[ '_uuid' ];

    ob_start();
    if ( !file_put_contents( $cmdfile, $cmd ) )
    {
       $cont = ob_get_contents();
       ob_end_clean();
       if ( isset( $checkrunning ) )
       {
          try {
              $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
          } catch(MongoCursorException $e) {
              $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
          }
       }
       $results[ "error" ] .= "Could not write _cmds_ for remote submission " . $cont;
       $results[ '_status' ] = 'failed';
       echo (json_encode($results));
       exit();
    }
    ob_end_clean();

    logjobstart();

    $altcmd = "nohup /usr/local/bin/php __docroot:html5__/__application__/util/jobrun.php " . $GLOBALS[ 'logon' ] . " " . $_REQUEST[ '_uuid' ] . " " . ( isset( $checkrunning ) ? "1" : "0" ) . " 2>&1 >> /tmp/php_errors &";

//    error_log( "\taltcmd:\n$altcmd\n", 3, "/tmp/mylog" );

    __~debug:runjob{error_log( "base.php exec nohup jobrun\n", 3, "/tmp/php_errors" );}
      
    exec( $altcmd );

    if ( isset( $results[ "_fs_clear" ] ) )
    {
        $fsc = $results[ "_fs_clear" ];
        $results = '{"_status":"started"__~debug:job{,"jobrun":"started"},"_fs_clear":"' . $fsc . '"}';
    } else {
        $results = '{"_status":"started"__~debug:job{,"jobrun":"started"}}';
    }
    
    if ( $do_logoff == 1 )
    {   
        $results = substr( trim( $results ), 0, -1 ) . ",\"_login\":\"\"}";
    }

    echo $results;
    exit;

    $results = exec( $cmd );

    logjobupdate( "finished", true );

    $results = str_replace( "__docroot:html5__/__application__/", "", $results );
    if ( $do_logoff == 1 )
    {   
        $results = substr( trim( $results ), 0, -1 ) + ",\"_login\":\"\"}";
    }

    ob_start();
    file_put_contents( "$logdir/_stdout_" . $_REQUEST[ '_uuid' ], $results );
    ob_end_clean();

    ob_start();
    $test_json = json_decode( $results );
    if ( $test_json == NULL )
    {   
        $cont = ob_get_contents();
        ob_end_clean();

        if ( isset( $checkrunning ) )
        {
           try {
               $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
           } catch(MongoCursorException $e) {
               $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
           }
        }

        if ( strlen( $results ) )
        {
            $results[ "error" ] = "Malformed JSON returned from executable $cont";
            if ( strlen( $results ) > 1000 )
            {
                $results[ "executable_returned_end" ] = substr( $results, 0, 450  ) . " .... " . substr( $results, -450 );
                $results[ "notice" ] = "The executable return string was greater than 1000 characters, so only the first 450 and the last 450 are shown above.  Check $logdir/_stdout for the full output";
            } else {
                $results[ "executable_returned" ] = substr( $results, 0, 1000 );
            }
        } else {
            $results[ "error" ] = "Empty JSON returned from executable $cont";
        }

        ob_start();
        $stderr = trim( file_get_contents( "$logdir/_stderr_" . $_REQUEST[ '_uuid' ] ) );
        $cont = ob_get_contents();
        ob_end_clean();
        $results[ "error_output" ] = ( strlen( $stderr ) > 0 ) ? $stderr : "EMPTY";
        if ( strlen( $cont ) )
        {
            $results[ "error_output_issue" ] = "reading _stderr reported $cont";
        }           

        echo (json_encode($results));
        exit();
    }
    ob_end_clean();
    if ( isset( $checkrunning ) )
    {
       try {
           $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
       } catch(MongoCursorException $e) {
           $test_json[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
       }
       $results = json_encode( $test_json );
    }
} else {
    if ( isset( $checkrunning ) )
    {
       try {
           $coll->remove( array( "name" => $checkrunning ), array(__~mongojournal{"j" => true,} "justOne" => true ));
       } catch(MongoCursorException $e) {
           $results[ 'error' ] = "Error removing running project record from database.  This project is now locked. " . $e->getMessage();
       }
    }
    $results[ "error" ] .= "PHP code received no \$_REQUEST?";
    echo (json_encode($results));
    exit();
}

// cleanup CURRENTLY DISABLED!
if ( sizeof( $_FILES ) )
{
   $files = new RecursiveIteratorIterator(
       new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
       RecursiveIteratorIterator::CHILD_FIRST
   );

   foreach ($files as $fileinfo) {
      $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
//      $todo( $fileinfo->getRealPath() );
   }
//   rmdir( $dir );
}
echo $results;
?>
