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
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Error/Types.php';

require_once 'lib/AiravataClientFactory.php';
require_once 'modulesUtils.php';

use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\InvalidRequestException;
use Airavata\Client\AiravataClientFactory;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;
use Airavata\API\AiravataClient;
use Thrift\Exception\TTransportException;
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

#airavata functions
function register(){	
	$airavataconfig = parse_ini_file("airavata-client-properties.ini");
	$transport = new TSocket($airavataconfig['AIRAVATA_SERVER'], $airavataconfig['AIRAVATA_PORT']);
	$transport->setSendTimeout($airavataconfig['AIRAVATA_TIMEOUT']);
	$protocol = new TBinaryProtocol($transport);
	$transport->open();
	try{
		$airavataclient = new AiravataClient($protocol);
		$registeredModules = getRegisteredModules($airavataclient);
		// $modules = getUnregisteredModules($registeredModules);
		// $modules = getModulesNames();
        echo var_dump($registeredModules);
		// echo var_dump($modules);
		// if(!empty($modules)){
  //          $localhostId = registerLocalHost($airavataclient);
  //          $moduleids = registerApplicationModule($airavataclient, $modules);
  //          registerApplicationDeployments($airavataclient, $moduleids, $modules, $localhostId);
  //          registerApplicationInterfaces($airavataclient, $moduleids, $modules, $localhostId);
		// 	// $host = $airavataclient->registerComputeResource();
		// }
	}	
	catch (InvalidRequestException $ire)
	{
	    print 'InvalidRequestException: ' . $ire->getMessage()."\n";
	}
	catch (AiravataClientException $ace)
	{
	    print 'Airavata System Exception: ' . $ace->getMessage()."\n";
	}
	catch (AiravataSystemException $ase)
	{
	    print 'Airavata System Exception: ' . $ase->getMessage()."\n";
	}
	$transport->close();
}


function getRegisteredModules($client){
 //    $registeredModules = array();    
	// $allDeployed = $client->getAllApplicationDeployments();
	// 	foreach ($allDeployed as $module) {
	//   		$registeredModules[$client->getApplicationModule($module->appModuleId)->appModuleName] = $module->executablePath;
	// 	}
	return $client->getAllApplicationInterfaceNames();    
}

function getUnregisteredModules($registeredModules){
	$unregisteredModules = array();
	$exec_path = getExecutablePath();
	$modules = getModulesNames();
	foreach ($modules as $id) {
		if(isset($registeredModules[$id])){
			if(strcmp($registeredModules[$id], $exec_path."/".$id) == 0){
                echo $id." is already registered \n";
			}else {
				$unregisteredModules[] = $id;
			}
		}else{
			$unregisteredModules[] = $id;
		}
	}
	return $unregisteredModules;
}

function registerLocalHost($client){
	echo "## Registering for localhost ##\n";
    $resourceDesc = createComputeResourceDescription("localhost", "LocalHost", null, null);
    $localhostId = $client->registerComputeResource($resourceDesc);
    $resourceJobManager = createResourceJobManager(ResourceJobManagerType::FORK, null, null, null);
    $submission = new LOCALSubmission();
    $submission->resourceJobManager = $resourceJobManager;
    $localSubmission = $client->addLocalSubmissionDetails($localhostId,1,$submission);
    echo "registered ".$localSubmission."\n";
    return $localhostId;
    // echo var_dump(ResourceJobManagerType::FORK);
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

function registerApplicationModule($client, $modules){
	$moduleids = array();
	foreach($modules as $module){
        $moduleids[$module] = $client->registerApplicationModule(
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

function registerApplicationDeployments($client, $moduleIds, $moduleNames, $localhostId){
        echo "#### Registering Application Deployments on Localhost ####\n";
        foreach ($moduleNames as $name) {
            $deployId = $client->registerApplicationDeployment(
                createApplicationDeployment($moduleIds[$name], $localhostId,
                    getExecutablePath()."/".$name, ApplicationParallelismType::SERIAL, 
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

function registerApplicationInterfaces($client, $moduleIds, $moduleNames, $localhostId) {
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

        $InterfaceId = $client->registerApplicationInterface(
                 createApplicationInterfaceDescription($module , $module." application description",
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