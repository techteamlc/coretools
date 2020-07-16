<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreTools</title>

    <!-- estilos que tem impacto direto no layout da página, podem ficar no <head> -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    
    <form method="post" data-url="action.php">
        <input type = "checkbox" id = "remember" name = "remember" value = "1" checked> Manter Logado?
        <input type="text" name="user" placeholder="Usuário" value="<?php if (isset($_COOKIE['user'])) { echo $_COOKIE['user']; } ?>" >	<br>
        <input type="password" name="password" placeholder="Senha" value="<?php if (isset($_COOKIE['password'])) { echo $_COOKIE['password']; } ?>" >	<br>
        <input type="text" name="store" id=1 value="" placeholder="loja" value = "<?php if (isset($_COOKIE['store'])) { echo ($_POST['store']); } ?>">	<br>

        <input type="hidden" name="action" value="task">

        <select name="service">
            <option>Selecione o Serviço</option>
            <option value="EZ.Store.Integration.Microvix.Tasks.IntegrateCatalogMicrovix">MCX - Integrar Produtos/preço/estoque </option>
            <option value="EZ.Store.Integration.Microvix.Tasks.IntegrateSalesMicrovix">MCX - Integrar Pedidos/Status </option>
            <option value="">-- -- --</option>
            <option value="EZ.Store.Integration.LinxERP.Tasks.LinxERPInventoryFromERP">LinxERP - Integrar Estoque </option>
            <option value="EZ.Store.Integration.LinxERP.Tasks.LinxERPPriceFromERP">LinxERP - Integrar Preços </option>
            <option value="EZ.Store.Integration.LinxERP.Tasks.LinxERPSkuFromERP">LinxERP - Integrar Skus </option>
            <option value="EZ.Store.Integration.LinxERP.Tasks.LinxERPOrderFromERP">LinxERP - Integrar Status Pedido </option>
            <option value="">-- -- --</option>
            <option value="EZ.Store.Services.Queue.QueuedTasks.ImportCustomDeliveryQueuedTask">Core - Importar Planilha delivery </option>
            <option value="EZ.Store.Services.Queue.QueuedTasks.ImportCustomDeliveryQueuedTask">Core - Exportar Planilha delivery </option>
        </select> 
        <!--<input type="submit" name="submit" value="Run Task">-->
        <button class="btn-task">run task</button>
    </form> 

    <div class="icons">
        <i class="fa fa-refresh w3-xxxlarge w3-spin"></i>
        <i class="fa fa-check w3-xxxlarge"></i>
        <i class="fa fa-times w3-xxxlarge"></i>
    </div>

    <!-- sempre que possível, deixa os teus scripts externos no final do arquivo -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="/js/scripts.js"></script>
</body>
</html>