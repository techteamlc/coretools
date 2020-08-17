<?php 
header('Content-Type: application/json');
$tenant = $_POST['tenant'];
$login = $_POST['login'];
$pass = $_POST['pass'];
$b64auth = base64_encode(sprintf('%s:%s', $login, $pass));

 if (isset($_POST)) :
   
	$url = 'https://'.$tenant.'.layer.core.dcg.com.br/v1/Mashup/API.svc/web/CheckCredentials';
	echo ($url);
    
	$options = array(
	    'http' => array(
	    	'header' => array( 
				'Authorization: Basic ' . $b64auth,
	            'Accept: application/json', 
				'Content-Type: application/json',
				'User-Agent': 'PostmanRuntime/7.26.3'
	        ),
	        'method'  => 'POST'
	        
	    )
	);
	$context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    $json = json_decode($result,JSON_PRETTY_PRINT);


	echo json_encode(array(
		'status' => true,
		'response' => $json
	));
endif;


?>

