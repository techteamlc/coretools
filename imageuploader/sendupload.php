<?php 
include 'getproductsku.php';
header('Content-Type: application/json');

function saveCatalogMedia($data) {
	
	global $name, $type, $separator, $replace, $keep, $idProduto;
	$login = $_POST['user'];
	$pass = $_POST['pass'];
	$b64auth = base64_encode(sprintf('%s:%s', $login, $pass));
	$tenant = $_POST['tenant'];

	$url = 'https://'.$tenant.'.layer.core.dcg.com.br/v1/Catalog/API.svc/web/SaveCatalogMedia';
    //$data = '{"ProductID": "103", "IntegrationID": null, "Image": { "EncodedBase64File": {"FileName": "'.$_POST['name'].'", "ContentFileEncodedBase64": "'.$img.'", "ContentType": "jpeg"} },"KeepOnlyMedia": true, "ReplaceExistingMedia": true}';
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
	$result = file_get_contents($url, false, $context);

	//echo $options;   
	$json = json_decode($result,JSON_PRETTY_PRINT);

	if (!isset($json['IsValid']) || is_null($json['IsValid'])) :
		$file = './log/log_' . date('Ymd') . '.txt';
		file_put_contents($file, date('d/m/Y H:i:s') . ' - Erro no arquivo: ' . $name . PHP_EOL, FILE_APPEND);
	endif;
	

	return array(
		'Name' => $name,
		'FileName' => $name . '.' . $type,
		'Separator' => $separator,
		'ImageType' => $type,
		'ReplaceMedia' => $replace,
		'KeepOnlyMedia' => $keep,
		'ID' => $idProduto,
		'APIresponseSuccess' => $json['IsValid'],
		'Message' => $json['Errors']
	);
}

$img = str_replace('data:image/jpeg;base64,','',$_POST['base64']);

// obtem o nome do arquivo para ser usado expressão regular para obter os dados do produto da API
$name = $_POST['name'];
$pattern = '/(\d+|\w+)(\S)(\d+)/m';
preg_match_all($pattern, $name, $matches);
/*  Relação de Match
print_r($matches);
echo('Nome: ' . $matches[0][0] . '     ');
echo('Codigo Produto: ' . $matches[1][0] . '    ');
echo('Separador: ' . $matches[2][0] . '    ');
echo('Posição da imagem: ' . $matches[3][0] . '    ');
*/

//adicionado variaveis aos resultados da expresão regular
$img_name = $matches[0][0];
$img_id = $matches[1][0];
$img_separator = $matches[2][0];
$img_position = $matches[3][0];

$idProduto = $img_id;
$replace = isset($_POST['replace']);
$keep = isset($_POST['keep']);
$separator = $img_separator;
$type1 = $_POST['type'];
$type = str_replace('image/','',$type1);
//$name = str_replace($type,'',$_POST['name']);
if ($separator == '') :
	$idProduto = $img_name;
else : 
	$idProduto = $idProduto;
endif;
//$idProduto = 'P_' . $idProduto;

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
								'FileName' => "$img_name",
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
								'FileName' => "$img_name",
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
			$login = $_POST['user'];
			$pass =  $_POST['pass'];
			$b64auth = base64_encode(sprintf('%s:%s', $login, $pass));
			$tenant = $_POST['tenant'];	
			$data = json_encode(
						array (
							'ProductID' => GetProductSku($idProduto, $b64auth, $tenant),
							'IntegrationID' => null,
							'Image' => 
							array (
							'EncodedBase64File' => 
							array (
								'FileName' => "$img_name",
								'ContentFileEncodedBase64' => $img,
								'ContentType' => "$type",
							),
							),
							'KeepOnlyMedia' => $keep,
							'ReplaceExistingMedia' => $replace,
						)
					);
	    break;
} 
//echo $img;
$return = '';
if (isset($_POST) && $field != 'SkuID') :
 
	$return = saveCatalogMedia($data);
	echo json_encode($return);
	
else :
	
	/**
	 * Chamar o GetSKU
	 * no response, popular o $data e chamar a função saveCatalogMedia
	 */
	$return = saveCatalogMedia($data);
	echo json_encode($return);

endif;

//print_r($_POST);
//sleep(rand(1,5));

?>

