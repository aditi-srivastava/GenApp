<?php

$GLOBALS['THRIFT_ROOT'] = 'airavata/lib/Thrift/';
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

$GLOBALS['AIRAVATA_ROOT'] = 'airavata/lib/Airavata/';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Airavata.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/AppCatalog/AppInterface/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/Workspace/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/Workspace/Experiment/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Error/Types.php';

require_once 'airavata/lib/AiravataClientFactory.php';
require_once 'airavata/clientProperties.php';

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
use Thrift\Exception\TException;
use Airavata\Model\AppCatalog\AppInterface\InputDataObjectType;
use Airavata\Model\AppCatalog\AppInterface\OutputDataObjectType;
use Airavata\Model\AppCatalog\AppInterface\DataType;



function createProject($projectName){
    $transport = new TSocket($GLOBALS['AIRAVATA_SERVER'], $GLOBALS['AIRAVATA_PORT']);
    $transport->setRecvTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
    $transport->setSendTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
    $gatewayId = $GLOBALS['AIRAVATA_GATEWAY'];
    $owner = $GLOBALS['AIRAVATA_LOGIN'];
    $protocol = new TBinaryProtocol($transport);
    try
    {
        $transport->open();
        $airavataclient = new AiravataClient($protocol);
        $project = new Project();
        $project->owner = $owner;
        $project->name = $projectName;
        $projId = $airavataclient->createProject($gatewayId, $project);
        $outputData = array();

        if ($projId)
        {
            $outputData["ProjectId"] = $projId;
            // return json_encode($output);
        }
        else
        {
            $outputData["error"] = 'Failed to create project.';
        }

        $transport->close();
    }
    catch (InvalidRequestException $ire)
    {
        $outputData["error"] = 'InvalidRequestException: ' . $ire->getMessage();
    }
    catch (AiravataClientException $ace)
    {
        $outputData["error"] = 'Airavata System Exception: ' . $ace->getMessage();
    }
    catch (AiravataSystemException $ase)
    {
        $outputData["error"] = 'Airavata System Exception: ' . $ase->getMessage();
    }
    catch(TException $tx)
    {
        $outputData["error"] = 'Exception: ' . $tx->getMessage();
    }
    catch (\Exception $e)
    {
        $outputData["error"] = 'Exception: ' . $e->getMessage();
    }
    return json_encode($outputData);
}

function createExperiment($expName, $projId, $appId, $inp){
    $transport = new TSocket($GLOBALS['AIRAVATA_SERVER'], $GLOBALS['AIRAVATA_PORT']);
    $transport->setRecvTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
    $transport->setSendTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
    $protocol = new TBinaryProtocol($transport);
    try
    {
        $transport->open();
        $airavataclient = new AiravataClient($protocol);
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
        $user = $GLOBALS['AIRAVATA_LOGIN'];
        $host = $GLOBALS['AIRAVATA_SERVER'];
        $cmrHost = getRandomResource();
        $GLOBALS["RESOURCE_HOST"] = $cmrHost->host;
        $appId.="_".$GLOBALS["RESOURCE_HOST"];
        // $hostname = $airavataconfig['AIRAVATA_SERVER_ALIAS'];
        $gatewayId = $GLOBALS['AIRAVATA_GATEWAY'];
        $proAccount = $GLOBALS['AIRAVATA_PROJECT_ACCOUNT'];
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
                if($value == $cmrHost->host){
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
        $outputData = array();
        $expId = $airavataclient->createExperiment($gatewayId,$experiment);
        $transport->close();

        if ($expId)
        {
            $outputData["ExperimentId"] = $expId;
        }
        else
        {
            $outputData["error"] = 'Experiment Not Created';
        }
    }
    catch (InvalidRequestException $ire)
    {
        $outputData["error"] = 'InvalidRequestException: ' . $ire->getMessage();
    }
    catch (AiravataClientException $ace)
    {
        $outputData["error"] = 'Airavata System Exception: ' . $ace->getMessage();
    }
    catch (AiravataSystemException $ase)
    {
        $outputData["error"] = 'Airavata System Exception: ' . $ase->getMessage();
    }
    catch(TException $tx)
    {
        $outputData["error"] = 'Exception: ' . $tx->getMessage();
    }
    catch (\Exception $e)
    {
        $outputData["error"] = 'Exception: ' . $e->getMessage();
    }
    return json_encode($outputData);
}

function launchExperiment( $expId){
    $token = $GLOBALS['AIRAVATA_CREDENTIAL_STORE_TOKEN'];
    $transport = new TSocket($GLOBALS['AIRAVATA_SERVER'], $GLOBALS['AIRAVATA_PORT']);
    $transport->setSendTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
    $transport->setRecvTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
    $protocol = new TBinaryProtocol($transport);
    try
    {
        $transport->open();
        $airavataclient = new AiravataClient($protocol);
        $airavataclient->launchExperiment($expId, $token);
        $transport->close();
        $outputData = array();
        $outputData["isExperimentLaunched"] = true;
    }
    catch (InvalidRequestException $ire)
    {
        $outputData["error"] = 'InvalidRequestException: ' . $ire->getMessage();
    }
    catch (AiravataClientException $ace)
    {
        $outputData["error"] = 'Airavata System Exception: ' . $ace->getMessage();
    }
    catch (AiravataSystemException $ase)
    {
        $outputData["error"] = 'Airavata System Exception: ' . $ase->getMessage();
    }
    catch(TException $tx)
    {
        $outputData["error"] = 'Exception: ' . $tx->getMessage();
    }
    catch (\Exception $e)
    {
        $outputData["error"] = 'Exception: ' . $e->getMessage();
    }
    return json_encode($outputData);
}

function getOutput( $expId)
{
    $transport = new TSocket($GLOBALS['AIRAVATA_SERVER'], $GLOBALS['AIRAVATA_PORT']);
    $transport->setSendTimeout($GLOBALS['AIRAVATA_TIMEOUT']);
    $protocol = new TBinaryProtocol($transport);
    $errors = array(
     'CANCELED' => "Experiment Cancelled",
     'SUSPENDED' => "Experiment Suspended",
     'FAILED' => "Experiment Failed"
        );
    try
    {
        $airavataclient = new AiravataClient($protocol);
        $transport->open();
        while(($status=get_experiment_status($airavataclient, $expId))!="COMPLETED"){
            if(isset($errors[$status])){
              $transport->close();
              return "{\"error\":\"".$errors[$status]."\"}";
              exit();
            }
            sleep(1);
        }
        $outputs =  $airavataclient->getExperimentOutputs($expId);
        $transport->close();
        $outputData = array();
        if(!empty($outputs[0]->value)){
            $out = json_decode($outputs[0]->value);
            $out->Computational_Host = $GLOBALS["RESOURCE_HOST"]; 
            $outputData["output"] = json_encode($out);
        }else {
            $outputData["error"] = $outputs[1]->value;
        }

    }
    catch (InvalidRequestException $ire)
    {
        $outputData["error"] = 'InvalidRequestException: ' . $ire->getMessage();
    }
    catch (AiravataClientException $ace)
    {
        $outputData["error"] = 'Airavata System Exception: ' . $ace->getMessage();
    }
    catch (AiravataSystemException $ase)
    {
        $outputData["error"] = 'Airavata System Exception: ' . $ase->getMessage();
    }
    catch(TException $tx)
    {
        $outputData["error"] = 'Exception: ' . $tx->getMessage();
    }
    catch (ExperimentNotFoundException $enf)
    {
        $outputData["error"] = 'ExperimentNotFoundException: ' . $enf->getMessage();
    }
    catch (TTransportException $tte)
    {
        $outputData["error"] = 'TTransportException: ' . $tte->getMessage();
    }
    catch (\Exception $e)
    {
        $outputData["error"] = 'Exception: ' . $e->getMessage();
    }
    return json_encode($outputData);


}

function get_experiment_status($client, $expId)
{
        try
        {
            $outputData = array();
            $experimentStatus = $client->getExperimentStatus($expId);
            return ExperimentState::$__names[$experimentStatus->experimentState];
            exit();
        }
        catch (InvalidRequestException $ire)
        {
            $outputData["error"] = 'InvalidRequestException: ' . $ire->getMessage();
        }
        catch (AiravataClientException $ace)
        {
            $outputData["error"] = 'Airavata System Exception: ' . $ace->getMessage();
        }
        catch (AiravataSystemException $ase)
        {
            $outputData["error"] = 'Airavata System Exception: ' . $ase->getMessage();
        }
        catch(TException $tx)
        {
            $outputData["error"] = 'Exception: ' . $tx->getMessage();
        }
        catch (ExperimentNotFoundException $enf)
        {
            $outputData["error"] = 'ExperimentNotFoundException: ' . $enf->getMessage();
        }
        catch (TTransportException $tte)
        {
            $outputData["error"] = 'TTransportException: ' . $tte->getMessage();
        }
        catch (\Exception $e)
        {
            $outputData["error"] = 'Exception: ' . $e->getMessage();
        }
        return json_encode($outputData);

}

function getRandomResource(){
    $resources = $GLOBALS["COMPUTE_RESOURCE"];
    $size = sizeof($resources);
    return $resources[rand(0, $size-1)];
}

?>
