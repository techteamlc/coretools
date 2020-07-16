<?php
if (isset($_POST['action'])) :
    switch ($_POST['action']) :
        case 'task':
            $user     = $_POST['user'];
            $password = $_POST['password'];
            $store    = $_POST['store'];
            $service  = $_POST['service'];

            $url = 'https://' . $user . ':' . $password . '@' . $store . '.layer.core.dcg.com.br/v1/Task/API.svc/web/RunTask?taskName=' . $service;
            $options = array(
                'http' => array(
                    'header' => array(
                        'Accept: application/json',
                        'Content-Type: application/json'
                    ) ,
                    'method' => 'GET'
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            if ($result === false) :
                echo json_encode(
                    array(
                        'status' => false,
                        'message' => '"# Opa! Algo de errado não está certo!'
                    )
                );
            else :
                $json = json_decode($result);
                $color = $json->IsValid;

                $message = '';

                if ($color == 1) :
                    $message = '';
                else :
                    $message = '';
                endif;

                if (!empty($_POST["remember"])) :
                    setcookie ("user",$_POST["user"],time()+ 3600);
                    setcookie ("password",$_POST["password"],time()+ 3600);
                else :
                    setcookie("username","");
                    setcookie("password","");                
                endif;

                echo json_encode(
                    array(
                        'status' => true,
                        'message' => $message
                    )
                );
            endif;
        break;
    endswitch;
endif;