<?php 
include("airavata.php");

$projId=createProject("admin", "test");
echo var_dump($projId);
$expId=createExperiment("admin", "exp5", $projId, "test", "{\"input1\":\"13\",\"input2\":\"3\",\"input3\":\"true\"}"); 
// $expId =  "exp5_e7b826cf-a429-4b3e-b519-28d859fe4b64";
echo var_dump($expId);
$res = launchExperiment($expId);
echo var_dump($res); 
while(($status=get_experiment_status($expId))!="COMPLETED"){    
	echo "$status\n";
	sleep(1);
}
$results = getOutput($expId); 
echo var_dump($results);
?>