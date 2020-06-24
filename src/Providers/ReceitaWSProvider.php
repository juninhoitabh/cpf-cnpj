<?php

namespace CNPJCPF\Providers;

use CNPJCPF\Data;
use CNPJCPF\Contracts\HttpClientContract;
use CNPJCPF\Contracts\ProviderContract;

class ReceitaWSProvider implements ProviderContract
{
    /**
     * @return Data|null
     */
    public function getData($cnpj_cpf, HttpClientContract $client)
    {
        $response = $client->get('https://www.receitaws.com.br/v1/cnpj/'.$cnpj_cpf);
        
        if(!is_null($response)) {
            $data = json_decode($response, true);

            if(!is_null($data['nome']))
            {
                return Data::create([
                    'names' => trim($data['nome']),
                    'fantasy' => trim($data['fantasia']),
                    'zipcode' => $data['cep'],
                    'phone' => trim($data['telefone']),
                    'mail' => trim($data['email']),
                    'neighborhood' => trim($data['bairro']),
                    'street' => trim($data['logradouro']),
                    'streetnumber' => trim($data['numero']),
                    'complement' => trim($data['complemento']),
                ]);
            }
        }
    }
}
