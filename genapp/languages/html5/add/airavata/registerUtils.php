<?php

$GLOBALS['THRIFT_ROOT'] = 'lib/Thrift/';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TTransport.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TBufferedTransport.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Protocol/TProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TApplicationException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TProtocolException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TTransportException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Base/TBase.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Type/TType.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Type/TMessageType.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Factory/TStringFuncFactory.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'StringFunc/TStringFunc.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'StringFunc/Core.php';

$GLOBALS['AIRAVATA_ROOT'] = 'lib/Airavata/';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Airavata.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/AppCatalog/AppDeployment/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/AppCatalog/ComputeResource/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/AppCatalog/AppInterface/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/AppCatalog/GatewayProfile/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/Workspace/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Error/Types.php';

require_once 'lib/AiravataClientFactory.php';
require_once 'modulesUtils.php';
require_once 'clientProperties.php';

use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\InvalidRequestException;
use Airavata\Client\AiravataClientFactory;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;
use Airavata\API\AiravataClient;
use Thrift\Exception\TTransportException;
use Thrift\Exception\TProtocolException;
use Thrift\Exception\TException;
use Airavata\Model\AppCatalog\ComputeResource\ComputeResourceDescription;
use Airavata\Model\AppCatalog\ComputeResource\ResourceJobManager;
use Airavata\Model\AppCatalog\ComputeResource\ResourceJobManagertype;
use Airavata\Model\AppCatalog\ComputeResource\LOCALSubmission;
use Airavata\Model\AppCatalog\AppDeployment\ApplicationModule;
use Airavata\Model\AppCatalog\AppDeployment\ApplicationDeploymentDescription;
use Airavata\Model\AppCatalog\AppDeployment\ApplicationParallelismType;
use Airavata\Model\AppCatalog\AppInterface\InputDataObjectType;
use Airavata\Model\AppCatalog\AppInterface\OutputDataObjectType;
use Airavata\Model\AppCatalog\AppInterface\DataType;
use Airavata\Model\AppCatalog\AppInterface\ApplicationInterfaceDescription;
use Airavata\Model\AppCatalog\GatewayProfile\GatewayResourceProfile;
use Airavata\Model\AppCatalog\GatewayProfile\ComputeResourcePreference;
use Airavata\Model\Workspace\Gateway;

#airavata functions
function register(){
  $gatewayId = $GLOBALS['AIRAVATA_GATEWAY'];
  $transport = new TSocket($GLOBALS['AIRAVATA_SERVER'], $GLOBALS['AIRAVATA_PORT']);
  $transport->setSendTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
  $transport->setRecvTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
  $protocol = new TBinaryProtocol($transport);
  try{
        $transport->open();
        $airavataclient = new AiravataClient($protocol);
        if(!$airavataclient->isGatewayExist($gatewayId)){
        $gateway = new Gateway();
        $gateway->gatewayId = $gatewayId;
        $gateway->gatewayName = "GeanApp_GateWaay";
        $gateway->domain = $GLOBALS['AIRAVATA_SERVER'];
        $gateway->emailAddress = $GLOBALS['AIRAVATA_EMAIL'];;
        $airavataclient->addGateway($gateway);
        registerComputeResourceHost($airavataclient, $GLOBALS['COMPUTE_RESOURCE']);
        }else{
          if(isGatewayRegistered($airavataclient, $gatewayId)){
            $cmrf = $airavataclient
                        ->getGatewayResourceProfile($gatewayId)
                        ->computeResourcePreferences;
            registerComputeResourceHost($airavataclient, getUnRegisteredHost($airavataclient, $cmrf));
          }else{
            registerComputeResourceHost($airavataclient, $GLOBALS['COMPUTE_RESOURCE']);
          }
        }
        $hostIds = getHostIds($airavataclient, $gatewayId);
        $registeredModules = getRegisteredModules($airavataclient, $gatewayId);
        $modulesHost = getUnregisteredModules($airavataclient, $registeredModules);
        foreach ($modulesHost as $hostName => $modules) {
          if(!empty($modules)){
             $moduleids = registerApplicationModule($gatewayId, $airavataclient, $modules);
             registerApplicationDeployments($gatewayId, $airavataclient, $moduleids, $modules, $hostIds[$hostName]);
             registerApplicationInterfaces($gatewayId, $airavataclient, $moduleids, $modules, $hostName);
          }
        }
        $transport->close();
  } 
    catch (InvalidRequestException $ire)
    {
        echo 'InvalidRequestException: ' . $ire->getMessage();
    }
    catch (AiravataClientException $ace)
    {
        echo 'Airavata System Exception: ' . $ace->getMessage();
    }
    catch (AiravataSystemException $ase)
    {
        echo 'Airavata System Exception: ' . $ase->getMessage();
    }
    catch(TException $tx)
    {
        echo 'There is some connection problem, please check if airavata is runnig properly and try again later';
    }
    catch (\Exception $e)
    {
        echo 'Exception: ' . $e->getMessage();
    }
}

function isGatewayRegistered($client, $gatewayId){
   $gCRs = $client->getAllGatewayComputeResources();
   $exist = false;
   foreach ($gCRs as $gCr) {
       if($gCr->gatewayID === $gatewayId){
            $exist = true;
       }
   }
   return $exist;
}


function getRegisteredModules($client, $gatewayId){
  return $client->getAllApplicationInterfaceNames($gatewayId);    
}

function getUnregisteredModules($client,$registeredModules){
  $unregisteredModules = array();
  $unregisteredModulesForHost = array();
  $modules = getModulesNames();
  foreach ($GLOBALS['COMPUTE_RESOURCE'] as $resource) {
      $unregisteredModules[$resource->host] =  array();
  }
  foreach ($modules as $index => $id) {
      foreach ($GLOBALS['COMPUTE_RESOURCE'] as $resource) {
        $name = $id."_".$resource->host;
        $unregisteredModules[$resource->host][] = $id ;     
        if(isset($registeredModules[$name])){
            foreach ($client->getAvailableAppInterfaceComputeResources($name) as $hostId => $hostName) {
              if($hostName == $resource->host){
                  echo $name." is already registered for $hostName \n";
                  unset($unregisteredModules[$hostName][$index]);  
              }
            }
        }
      }
  }
  return $unregisteredModules;
}

function registerHost($client, $resource){
    $resourceDesc = createComputeResourceDescription($resource->host, $resource->description, null, null);
    $hostId = $client->registerComputeResource($resourceDesc);
    $resourceJobManager = createResourceJobManager(ResourceJobManagerType::FORK, null, null, null);
    $submission = new LOCALSubmission();
    $submission->resourceJobManager = $resourceJobManager;
    $localSubmission = $client->addLocalSubmissionDetails($hostId,1,$submission);
    echo "registered ".$localSubmission."\n";
    return $hostId;
}

function registerComputeResourceHost($client, $host){
    foreach ($host as $key => $resourceHost) {
        echo "## Registering for $resourceHost->host ##\n";
        $id = registerHost($client, $resourceHost);
        registerGateWayProfile($client, $id);
    }
}

function registerGateWayProfile($client, $hostId){
   $gatewayId = $GLOBALS['AIRAVATA_GATEWAY'];
   $user = $GLOBALS['AIRAVATA_LOGIN'];
   $resourcePreference = new ComputeResourcePreference();
   $resourcePreference->computeResourceId = $hostId;
   $resourcePreference->allocationProjectNumber = "genappModules";
   $resourcePreference->overridebyAiravata = false;
   $resourcePreference->loginUserName = $user;
   $resourceList = array();
   $resourceList[] = $resourcePreference;
   $gatewayProfile = new GatewayResourceProfile();
   $gatewayProfile->gatewayID = $gatewayId;
   $gatewayProfile->computeResourcePreferences = $resourceList;
   $client->registerGatewayResourceProfile($gatewayProfile);
}

function getUnRegisteredHost($client, $cmrf){
  $resources = $client->getAllComputeResourceNames();
  $host = array();
  foreach ($GLOBALS["COMPUTE_RESOURCE"] as $cmr) {
    $exist = 0;
    foreach ($resources as $key => $value) {
       if($value == $cmr->host){
          foreach ($cmrf as $index => $cmrfResource) {
             if($cmrfResource->computeResourceId == $key){
                unset($resources[$key]);
                unset($cmrf[$index]);
                $exist = 1; 
             }
          }
       }
    }
       if($exist == 0){
          $host[] = $cmr;
       }
  }
    return $host;
}

function getHostIds($client, $gatewayId) {
    $cmrf = $client->getGatewayResourceProfile($gatewayId)->computeResourcePreferences;
    $resources = $client->getAllComputeResourceNames();
    $tempIds = array();
    $tempDetails = array();
    $executable = array();
    $hostIds = array();

    foreach ($cmrf as $cmrfResource) {
        $tempIds[] = $cmrfResource->computeResourceId;
    }

    foreach ($GLOBALS['COMPUTE_RESOURCE'] as $resource) {
        $executable[$resource->host] = $resource->executable;
    }

    foreach ($resources as $hostId => $hostName) {
       $tempDetails[$hostId] = $hostName;
    }

    foreach ($tempIds as $id) {
      if(isset($tempDetails[$id])){
         $hostIds[$tempDetails[$id]] = array(
                  'id' => $id,
                  'executable' => $executable[$tempDetails[$id]]);
      }
    }
    return $hostIds;
}

function createComputeResourceDescription($hostName, $hostDesc, $hostAliases, $ipAddresses) {
        $host = new ComputeResourceDescription();
        $host->hostName = $hostName;
        $host->resourceDescription = $hostDesc;
        $host->ipAddresses = $ipAddresses;
        $host->hostAliases = $hostAliases;
        return $host;
    }

function createResourceJobManager($resourceJobManagerType, $pushMonitoringEndpoint,
          $jobManagerBinPath, $jobManagerCommands) {
        $resourceJobManager = new ResourceJobManager();
        $resourceJobManager->resourceJobManagerType = $resourceJobManagerType;
        $resourceJobManager->pushMonitoringEndpoint = $pushMonitoringEndpoint;
        $resourceJobManager->jobManagerBinPath = $jobManagerBinPath;
        $resourceJobManager->jobManagerCommands = $jobManagerCommands;
        return $resourceJobManager;
    }

function registerApplicationModule($gatewayId, $client, $modules){
  $moduleids = array();
  foreach($modules as $module){
        $moduleids[$module] = $client->registerApplicationModule($gatewayId,
          createApplicationModule($module, "1.0", $module." discription"));
  }
  return $moduleids;
}

function createApplicationModule($appModuleName, $appModuleVersion, $appModuleDescription) {
        $module = new ApplicationModule();
        $module->appModuleDescription = $appModuleDescription;
        $module->appModuleName = $appModuleName;
        $module->appModuleVersion = $appModuleVersion;
        return $module;
}

function registerApplicationDeployments($gatewayId, $client, $moduleIds, $moduleNames, $host){
        echo "#### Registering Application Deployments on Localhost ####\n";
        foreach ($moduleNames as $name) {
            $deployId = $client->registerApplicationDeployment($gatewayId, 
                createApplicationDeployment($moduleIds[$name], $host["id"],
                    $host["executable"]."/".$name, ApplicationParallelismType::SERIAL, 
                    $name+" application description"));
            echo "Successfully registered ".$name." application on localhost, application Id = ".$deployId."\n";
        }
}

function createApplicationDeployment($appModuleId, $computeResourceId, $executablePath,
                              $parallelism, $appDeploymentDescription) {
        $deployment = new ApplicationDeploymentDescription();
//      deployment.setIsEmpty(false);
        $deployment->appDeploymentDescription = $appDeploymentDescription;
        $deployment->appModuleId = $appModuleId;
        $deployment->computeHostId = $computeResourceId;
        $deployment->executablePath = $executablePath;
        $deployment->parallelism = $parallelism;
        return $deployment;
    }

function registerApplicationInterfaces($gatewayId, $client, $moduleIds, $moduleNames, $hostName) {
    foreach ($moduleNames as $module) {
        echo "#### Registering ".$module." Interface ####\n";
        $appModules = array();
        $appModules[] = $moduleIds[$module];

        $input = createAppInput("Input_JSON", "{}",
                DataType::STRING, null, false, "JSON String", null);

        $applicationInputs = array();
        $applicationInputs[] = $input;

        $output = createAppOutput("JSON_Output","{}", DataType::STRING);
    $applicationOutputs = array();
        $applicationOutputs[] = $output;

        $InterfaceId = $client->registerApplicationInterface($gatewayId, 
                 createApplicationInterfaceDescription($module."_".$hostName , $module." application description",
                        $appModules, $applicationInputs, $applicationOutputs));
        echo $module." Application Interface Id ".$InterfaceId."\n";

    }
}

function createAppInput($inputName, $value, $type,
            $applicationArgument, $stdIn, $description, $metadata) {
        $input = new InputDataObjectType();
        if (isset($inputName)) $input->name = $inputName;
        if (isset($value)) $input->value = $value;
        if (isset($type)) $input->type = $type;
        if (isset($applicationArgument)) $input->applicationArgument = $applicationArgument;
        if (isset($description)) $input->userFriendlyDescription = $description;
        $input->standardInput = $stdIn;
        if (isset($metadata)) $input->metaData = $metadata;
        return $input;
}

function createAppOutput($inputName, $value, $type) {
        $outputDataObjectType = new OutputDataObjectType();
        if (isset($inputName)) $outputDataObjectType->name = $inputName;
        if (isset($value)) $outputDataObjectType->value = $value;
        if (isset($type)) $outputDataObjectType->type = $type;
        return $outputDataObjectType;
}

function createApplicationInterfaceDescription($applicationName, $applicationDescription, 
                                  $applicationModules, $applicationInputs, $applicationOutputs) {
        $applicationInterfaceDescription = new ApplicationInterfaceDescription();

        $applicationInterfaceDescription->applicationName = $applicationName;
        $applicationInterfaceDescription->applicationInterfaceId = $applicationName;
        if (isset($applicationDescription)) 
          $applicationInterfaceDescription->applicationDescription = $applicationDescription;
        if (isset($applicationModules)) 
          $applicationInterfaceDescription->applicationModules = $applicationModules;
        if (isset($applicationInputs)) 
          $applicationInterfaceDescription->applicationInputs = $applicationInputs;
        if (isset($applicationOutputs)) 
          $applicationInterfaceDescription->applicationOutputs = $applicationOutputs;

        return $applicationInterfaceDescription;
    }

?>