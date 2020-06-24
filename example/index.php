<?php

require_once '../vendor/autoload.php';

use CNPJCPF\CNPJCPF;

if (isset($_POST['cnpj_cpf'])) {
    $dados = CNPJCPF::search($_POST['cnpj_cpf']);

    var_dump($dados);
    die;
}

?>

<form method="POST">

    <input type="text" name="cnpj_cpf" placeholder="CNPJ/CPF" />
    <button type="submit">Consultar</button>

</form>