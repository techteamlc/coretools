<?php 
header('Content-Type: application/json');

$img = str_replace('data:image/jpeg;base64,','',$_POST['base64']);

$replace = isset($_POST['replace']);
$keep = isset($_POST['keep']);
$separator = $_POST['separator'];
$type1 = $_POST['type'];
$type = str_replace('image/','',$type1);
$name = str_replace($type,'',$_POST['name']);
$idProduto = strstr($name, $separator, true);
$idProduto = 'P_' . $idProduto;


$field = $_POST['field']; 
switch ($field){
		case "ProductID":
			$data = json_encode(
						array (
							'ProductID' => $idProduto,
							'IntegrationID' => null,
							'Image' => 
							array (
							'EncodedBase64File' => 
							array (
								'FileName' => $_POST['name'],
								'ContentFileEncodedBase64' => $img,
								'ContentType' => "$type",
							),
							),
							'KeepOnlyMedia' => $keep,
							'ReplaceExistingMedia' => $replace,
						)
					);
		break;
		case "IntegrationID":
			$data =  json_encode(
						array (
							'ProductID' => 0,
							'IntegrationID' => $idProduto,
							'Image' => 
							array (
							'EncodedBase64File' => 
							array (
								'FileName' => $_POST['name'],
								'ContentFileEncodedBase64' => $img,
								'ContentType' => "$type",
							),
							),
							'KeepOnlyMedia' => $keep,
							'ReplaceExistingMedia' => $replace,
						)
					);
		break;			
		case "SkuID":
			$data =  json_encode(array(
				array (
					'ProductID' => 0,
					'IntegrationID' => $_POST['name'],
					'Image' => 
					array (
					'EncodedBase64File' => 
					array (
						'FileName' => $_POST['name'],
						'ContentFileEncodedBase64' => $img,
						'ContentType' => "$type",
					),
					),
					'KeepOnlyMedia' => $keep,
					'ReplaceExistingMedia' => $replace,
				)
			));
	    break;
} 
//echo $img;

 if (isset($_POST)) :
   
    $url = 'https://helpdesk.layer.core.dcg.com.br/v1/Catalog/API.svc/web/SaveCatalogMedia';
    //$data = '{"ProductID": "103", "IntegrationID": null, "Image": { "EncodedBase64File": {"FileName": "'.$_POST['name'].'", "ContentFileEncodedBase64": "'.$img.'", "ContentType": "jpeg"} },"KeepOnlyMedia": true, "ReplaceExistingMedia": true}';
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
	$result = @file_get_contents($url, false, $context);

	//echo $options;   
	$json = json_decode($result,JSON_PRETTY_PRINT);

	if (!isset($json['IsValid']) || is_null($json['IsValid'])) :
		$file = './logs/log_' . date('Ymd') . '.txt';
		file_put_contents($file, date('d/m/Y H:i:s') . ' - Erro no arquivo: ' . $name . PHP_EOL, FILE_APPEND);
	endif;


	echo json_encode(array(
		'Name' => $name,
		'FileName' => $name . '.' . $type,
		'Separator' => $separator,
		'ImageType' => $type,
		'ReplaceMedia' => $replace,
		'KeepOnlyMedia' => $keep,
		'ID' => $idProduto,
		'APIresponseSuccess' => $json['IsValid'],
		'Message' => $json['Errors']
	));




endif;

//print_r($_POST);
//sleep(rand(1,5));

?>

