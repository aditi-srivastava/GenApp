<?php
$modules = array();
$exec_path = "/home/abhishek/Desktop/GenApp/abhishektest/bin";
array_push($modules,"center");
array_push($modules,"align");
array_push($modules,"filetest");
array_push($modules,"data_interpolation");
array_push($modules,"build_1");
array_push($modules,"build_2");
array_push($modules,"interact_1");
array_push($modules,"interact_2");
array_push($modules,"simulate_1");
array_push($modules,"simulate_2");
array_push($modules,"calculate_1");
array_push($modules,"calculate_2");
array_push($modules,"analyze_1");
array_push($modules,"analyze_2");

$modules["exec_path"] = stripslashes($exec_path);
$json = json_encode($modules, JSON_FORCE_OBJECT);
$json = addslashes($json);
echo exec("java -jar JsonOut.jar $json");
echo $modules["exec_path"];
?>