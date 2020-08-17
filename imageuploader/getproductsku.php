<?php 
header('Content-Type: application/json');

function GetProductSku($sku, $b64auth, $tenant) { 

$sku = $sku;
$b64auth = $b64auth;
$tenant = $tenant

$data = json_encode(
			array (
			  'Page' => 
			  array (
			    'PageIndex' => 0,
			    'PageSize' => 1,
			  ),
			  'Where' => 'Sku ='."\"".$sku ."\""
			)
        );
 if ($data != '') :
   
    $url = 'https://'.$tenant.'.layer.core.dcg.com.br/v1/Catalog/API.svc/web/SearchProduct';
    
	$options = array(
	    'http' => array(
	    	'header' => array( 
				'Authorization: Basic ' . $b64auth,
	            'Accept: application/json', 
	            'Content-Type: application/json'
	        ),
	        'method'  => 'POST',
	        'content' => ($data)
	        
	    )
	);
	$context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    $json = json_decode($result,JSON_PRETTY_PRINT);

    /*  Logs para consulta
	echo json_encode(array(
		'status' => true,
		'response' => $json,
		'ProductID' => $json['Result'][0]['ProductID']
		
	)); */
endif;
    return $json['Result'][0]['ProductID'];
}

?>

