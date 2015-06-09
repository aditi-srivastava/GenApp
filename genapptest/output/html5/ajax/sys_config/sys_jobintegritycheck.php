#!/usr/local/bin/php
<?php

$_REQUEST = json_decode( $argv[ 1 ], true );


$results = [];

if ( !sizeof( $_REQUEST ) ) {
    $results[ 'error' ] = "PHP code received no \$_REQUEST?";
    echo (json_encode($results));
    exit();
}

if ( !isset( $_REQUEST[ '_uuid' ] ) ) {
    $results[ "error" ] = "No _uuid specified in the request";
    echo (json_encode($results));
    exit();
}

if ( !isset( $_REQUEST[ '_logon' ] ) ) {
    $results[ "error" ] = "No _logon specified in the request";
    echo (json_encode($results));
    exit();
}

$appconfig = json_decode( file_get_contents( "/home/abhishek/Desktop/GenApp/abhishektest/appconfig.json" ) );

if ( !isset( $appconfig->admin ) ) {
    $results[ "error" ] = "appconfig.json no administrators defined";
    echo (json_encode($results));
    exit();
}    

if ( !in_array( $_REQUEST[ '_logon' ], $appconfig->admin ) ) {
    $results[ "error" ] = "not an administrator";
    echo (json_encode($results));
    exit();
}    

date_default_timezone_set("UTC");

function db_connect( $error_json_exit = false ) {
   global $use_db;
   global $db_errors;

   if ( !isset( $use_db ) ) {
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

$allresources = array_keys( (array) $appconfig->resources );

$results = [];

$results[ 'jobintegrityreport' ] = "jobintegrityreport";

echo json_encode( $results );

?>