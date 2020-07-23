<?php 
header('Content-Type: application/json');

if (isset($_POST['replace']) && !empty($_POST['replace'])) :
    //echo 'replace';
    $url = 'https://image.uploader:123@helpdesk.layer.core.dcg.com.br/v1/Hub/API.svc/web/SaveCatalogMedia'
    $data = '{"ProductID": "'.$_POST['i.name'].'", "IntegrationID": null, },"Image": { "EncodedBase64File": {"FileName": "'.$_POST['i.name'].'", "ContentFileEncodedBase64": "'.$_POST['i.base64'].'", "ContentType": "'.$_POST['i.type'].'"} },"KeepOnlyMedia": true, "ReplaceExistingMedia": true}';
    $options = array(
	    'http' => array(
	    	'header' => array( 
	            'Accept: application/json', 
	            'Content-Type: application/json'
	        ),
	        'method'  => 'POST',
	        'content' => ($data)
	    )
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

endif;

//print_r($_POST);
sleep(rand(1,5));
echo json_encode(array(
    'status' => true,
    'name' => $_POST['name']
));