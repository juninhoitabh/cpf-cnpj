# CPF e CNPJ Grátis
Com esse pacote você poderá realizar consultas de CPF e CNPJ gratuitamente.

Para evitar problemas com indisponibilidade de serviços, a consulta é realizada paralelamente em providers diferentes:

* [API Receita WS](https://www.receitaws.com.br/v1/cnpj/)
* [WebSite Siare](https://www2.fazenda.mg.gov.br/sol/ctrl/SOL/NFAE/SERVICO_027)

A library irá retornar para você a resposta mais rápida, aumentando assim a performance da consulta.

A library inicialmente foi criada pelo autor abaixo e a este agradeço pela contribuição, mas como estava a muito tempo sem ter atuzliação foi feito alguns ajustes.
"authors": [
    {
        "name": "Jansen Felipe",
        "email": "jansen.felipe@gmail.com"

### Changelog

* 1.0.0 - 24/06/2020 Alteração no retorno dos dois provedores deixando eles assim independentes, o que não estava funcionando normalmente, alterado o retorno para evitar mensagem de erro e deixa a execução da mesmo mais fluida.

### Como utilizar

Adicione a library

```shell
$ composer require juninhoitabh/cfp-cnpj
```
    
Adicione o autoload.php do composer no seu arquivo PHP.

```php
require_once 'vendor/autoload.php';  
```

Agora basta chamar o método `CNPJCPF::search($cpf_cnpj)`

```php
use CNPJCPF\CNPJCPF;

$address = CNPJCPF::search('02540779000163);
```

### License

The MIT License (MIT)