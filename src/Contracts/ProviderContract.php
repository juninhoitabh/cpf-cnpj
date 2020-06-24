<?php

namespace CNPJCPF\Contracts;

interface ProviderContract
{
    /**
     * @return Data
     */
    public function getData($cnpj_cpf, HttpClientContract $client);
}
