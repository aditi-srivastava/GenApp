<?php

function getProperties(){
  $appjson = json_decode( file_get_contents( "__appconfig__" ) );
  $properties = $appjson->resources->airavata->properties;
  $GLOBALS["AIRAVATA_GATEWAY"] = $properties->gateway;
  $GLOBALS["AIRAVATA_SERVER"] = $properties->server;
  $GLOBALS["AIRAVATA_PORT"] = $properties->port;
  $GLOBALS["AIRAVATA_TIMEOUT"] = $properties->timeout;
  $GLOBALS["AIRAVATA_GATEWAY_NAME"] = $properties->gatewayName;
  $GLOBALS["AIRAVATA_EMAIL"] = $properties->email;
  $GLOBALS["AIRAVATA_LOGIN"] = $properties->login;
  $GLOBALS["AIRAVATA_PROJECT_ACCOUNT"] = $properties->projectAccount;
  $GLOBALS["AIRAVATA_CREDENTIAL_STORE_TOKEN"] = $properties->credentialStoreToken;

}

?>