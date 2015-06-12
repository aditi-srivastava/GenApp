<?php

$GLOBALS['THRIFT_ROOT'] = 'airavata-0.15/lib/Thrift/';
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

$GLOBALS['AIRAVATA_ROOT'] = 'airavata-0.15/lib/Airavata/';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Airavata.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/AppCatalog/AppInterface/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/Workspace/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/Workspace/Experiment/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Error/Types.php';

require_once 'airavata-0.15/lib/AiravataClientFactory.php';

use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\InvalidRequestException;
use Airavata\Client\AiravataClientFactory;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;
use Airavata\API\AiravataClient;
use Airavata\Model\Workspace\Project;
use Airavata\Model\Workspace\Experiment\Experiment;
use Airavata\Model\Workspace\Experiment\UserConfigurationData;
use Airavata\Model\Workspace\Experiment\ComputationalResourceScheduling;
use Airavata\API\Error\ExperimentNotFoundException;	
use Airavata\Model\Workspace\Experiment\ExperimentState;
use Thrift\Exception\TTransportException;
use Airavata\Model\AppCatalog\AppInterface\InputDataObjectType;
use Airavata\Model\AppCatalog\AppInterface\OutputDataObjectType;
use Airavata\Model\AppCatalog\AppInterface\DataType;



function createProject($projectName){
	$airavataconfig = parse_ini_file("airavata-0.15/airavata-client-properties.ini");
	$transport = new TSocket($airavataconfig['AIRAVATA_SERVER'], $airavataconfig['AIRAVATA_PORT']);
	$transport->setRecvTimeout($airavataconfig['AIRAVATA_TIMEOUT']);
	$transport->setSendTimeout($airavataconfig['AIRAVATA_TIMEOUT']);
    $gatewayId = $airavataconfig['AIRAVATA_GATEWAY'];
    $owner = $airavataconfig['AIRAVATA_LOGIN'];
	$protocol = new TBinaryProtocol($transport);
	$transport->open();
	$airavataclient = new AiravataClient($protocol);
	try
	{	    
		$project = new Project();
		$project->owner = $owner;
		$project->name = $projectName;		
		$projId = $airavataclient->createProject($gatewayId, $project);
		
		if ($projId)
		{
		    return "$projId";
		}
		else
		{
		    echo 'Failed to create project.';
		}
	    
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

function createExperiment($expName, $projId, $appId, $inp){	
	$airavataconfig = parse_ini_file("airavata-0.15/airavata-client-properties.ini");
	$transport = new TSocket($airavataconfig['AIRAVATA_SERVER'], $airavataconfig['AIRAVATA_PORT']);
	$transport->setSendTimeout($airavataconfig['AIRAVATA_TIMEOUT']);
	$protocol = new TBinaryProtocol($transport);
	$transport->open();
	$airavataclient = new AiravataClient($protocol);
	try
	{	
		/* Experiment input and output data. */
		$input = new InputDataObjectType();
		$input->name = "input";
		$input->value = $inp;		
		$input->type = DataType::STRING;
		$exInputs = array($input);		
		$output = new OutputDataObjectType();
		$output->name = "output";
		$output->value = "";
		$output->type = DataType::STDOUT;
		$err = new OutputDataObjectType();
		$err->name = "output_err";
		$err->value = "";
		$err->type = DataType::STDERR;
		$exOutputs = array($output,$err);

		/* Create Experiment: needs to update using unique project ID. */
    	$user = $airavataconfig['AIRAVATA_LOGIN'];
    	$host = $airavataconfig['AIRAVATA_SERVER'];
    	$hostname = $airavataconfig['AIRAVATA_SERVER_ALIAS'];
    	$gatewayId = $airavataconfig['AIRAVATA_GATEWAY'];
    	$proAccount = $airavataconfig['AIRAVATA_PROJECT_ACCOUNT'];
		$exp_name = $expName;
		$proj = $projId;

		$experiment = new Experiment();
		$experiment->projectID = $proj;
		$experiment->userName = $user;
		$experiment->name = $exp_name;
		$experiment->applicationId = $appId;
		$experiment->experimentInputs = $exInputs;
		$experiment->experimentOutputs = $exOutputs;
	    $computeResources = $airavataclient->getAvailableAppInterfaceComputeResources($appId);
	    if(isset($computeResources) && !empty($computeResources)){
	    	foreach ($computeResources as $key => $value) {
	    		if($value == $host || $value == $hostname){
					$cmRST = new ComputationalResourceScheduling();
					$cmRST->resourceHostId = $key;
					$cmRST->computationalProjectAccount = $proAccount;
					$cmRST->nodeCount = 1;
					$cmRST->numberOfThreads = 1;
					$cmRST->queueName = "normal";
					$cmRST->totalCPUCount = 1;
					$cmRST->wallTimeLimit = 30;
					$cmRST->jobStartTime = 0;
					$cmRST->totalPhysicalMemory = 1;
					$userConfigurationData = new UserConfigurationData();
					$userConfigurationData->airavataAutoSchedule = 0;
					$userConfigurationData->overrideManualScheduledParams = 0;
					$userConfigurationData->computationalResourceScheduling = $cmRST;
					$experiment->userConfigurationData = $userConfigurationData;
	    		}
	    	}
	    }  
		$expId = $airavataclient->createExperiment($gatewayId,$experiment);

		if ($expId)
		{
		    return $expId;
		}
		else
		{
		    return 0;
		}
	}
	catch (InvalidRequestException $ire)
	{
		
	    print 'InvalidRequestException: ' . $ire->getMessage()."\n";
	    return -1;
	}
	catch (AiravataClientException $ace)
	{
	    print 'Airavata System Exception: ' . $ace->getMessage()."\n";
	    return -2;
	}
	catch (AiravataSystemException $ase)
	{
	    print 'Airavata System Exception: ' . $ase->getMessage()."\n";
	    return -3;
	}
	$transport->close();
}

function launchExperiment( $expId){
	//include 'airavata/getAiravataClient.php';
	// global $airavataclient;
	$airavataconfig = parse_ini_file("airavata-0.15/airavata-client-properties.ini");
	$token = $airavataconfig['AIRAVATA_CREDENTIAL_STORE_TOKEN'];
	$transport = new TSocket($airavataconfig['AIRAVATA_SERVER'], $airavataconfig['AIRAVATA_PORT']);
	$transport->setSendTimeout($airavataconfig['AIRAVATA_TIMEOUT']);
	$protocol = new TBinaryProtocol($transport);
	$transport->open();
	$airavataclient = new AiravataClient($protocol);	
	try
	{	  
	  // echo var_dump($airavataclient);
	   $airavataclient->launchExperiment($expId, $token);
	   return 1;
	}
	catch (InvalidRequestException $ire)
	{
	    return -1;
	}
	catch (AiravataClientException $ace)
	{
	    return -2;
	}
	catch (AiravataSystemException $ase)
	{
	    return -3;
	}
	catch (ExperimentNotFoundException $enf)
	{
	    return -4;
	}
	$transport->close();
}

function getOutput( $expId)
{
    $airavataconfig = parse_ini_file("airavata/airavata-client-properties.ini");
	$transport = new TSocket($airavataconfig['AIRAVATA_SERVER'], $airavataconfig['AIRAVATA_PORT']);
	$transport->setSendTimeout($airavataconfig['AIRAVATA_TIMEOUT']);
	$protocol = new TBinaryProtocol($transport);
	$transport->open();
	$airavataclient = new AiravataClient($protocol);
	$errors = array(
     'CANCELED' => "Experiment Cancelled",
     'SUSPENDED' => "Experiment Suspended",
     'FAILED' => "Experiment Failed"
		);
	while(($status=get_experiment_status($airavataclient, $expId))!="COMPLETED"){
        if(isset($errors[$status])){
          return "{\"error\":\"".$errors[$status]."\"}";
          exit();
        }
		sleep(1);
	}
    try
    {
        $outputs =  $airavataclient->getExperimentOutputs($expId);
        if(!empty($outputs[0]->value)){
			return $outputs[0]->value;	
        }else {
			return $outputs[1]->value;	
        }

    }
    catch (InvalidRequestException $ire)
    {
        echo 'InvalidRequestException!<br><br>' . $ire->getMessage();
    }
    catch (ExperimentNotFoundException $enf)
    {
        echo 'ExperimentNotFoundException!<br><br>' . $enf->getMessage();
    }
    catch (AiravataClientException $ace)
    {
        echo 'AiravataClientException!<br><br>' . $ace->getMessage();
    }
    catch (AiravataSystemException $ase)
    {
        echo 'AiravataSystemException!<br><br>' . $ase->getMessage();
    }
    catch (TTransportException $tte)
    {
        echo 'TTransportException!<br><br>' . $tte->getMessage();
    }
    catch (\Exception $e)
    {
        echo 'Exception!<br><br>' . $e->getMessage();
    }

	$transport->close();
}

function get_experiment_status($client, $expId)
{
	    try
	    {
		$experimentStatus = $client->getExperimentStatus($expId);
	    }
	    catch (InvalidRequestException $ire)
	    {
		echo 'InvalidRequestException!<br><br>' . $ire->getMessage();
	    }
	    catch (ExperimentNotFoundException $enf)
	    {
		echo 'ExperimentNotFoundException!<br><br>' . $enf->getMessage();
	    }
	    catch (AiravataClientException $ace)
	    {
		echo 'AiravataClientException!<br><br>' . $ace->getMessage();
	    }
	    catch (AiravataSystemException $ase)
	    {
		echo 'AiravataSystemException!<br><br>' . $ase->getMessage();
	    }
	    catch (\Exception $e)
	    {
		echo 'Exception!<br><br>' . $e->getMessage();
	    }

	    return ExperimentState::$__names[$experimentStatus->experimentState];
}

?>