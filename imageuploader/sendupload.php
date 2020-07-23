<?php 
header('Content-Type: application/json');

if (isset($_POST['replace']) && !empty($_POST['replace'])) :
    echo 'replace';
endif;

//print_r($_POST);
sleep(rand(1,5));
echo json_encode(array(
    'status' => true,
    'name' => $_POST['name']
));