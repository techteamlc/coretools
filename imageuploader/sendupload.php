<?php 
header('Content-Type: application/json');

$img = str_replace('data:image/jpeg;base64,','',$_POST['base64']);
//echo $img;

 if (isset($_POST['replace']) && !empty($_POST['replace'])) :
   
    $url = 'https://helpdesk.layer.core.dcg.com.br/v1/Catalog/API.svc/web/SaveCatalogMedia';
    $data = '{"ProductID": "103", "IntegrationID": null, "Image": { "EncodedBase64File": {"FileName": "'.$_POST['name'].'", "ContentFileEncodedBase64": "'.$img.'", "ContentType": "jpeg"} },"KeepOnlyMedia": true, "ReplaceExistingMedia": true}';
	$options = array(
	    'http' => array(
	    	'header' => array( 
	            'Authorization: Basic aW1hZ2UudXBsb2FkZXI6MTIz',
	            'Accept: application/json', 
	            'Content-Type: application/json'
	        ),
	        'method'  => 'POST',
	        'content' => ($data)
	    )
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

//echo $options;   
//echo $json = json_decode($result,JSON_PRETTY_PRINT);


endif;

//print_r($_POST);
//sleep(rand(1,5));
echo json_encode(array(
    'status' => true,
	'name' => $_POST['name'],
	'response' => $json
));

?>

