<?php

namespace CNPJCPF;

class Data
{
    public $names;

    public $fantasy;

    public $zipcode;
    
    public $phone;

    public $mail;

    public $neighborhood;

    public $street;

    public $streetnumber;

    public $complement;

    public static function create(array $data = [])
    {
        $dados = new self();

        foreach (get_object_vars($dados) as $name => $oldValue) {
            $dados->{$name} = isset($data[$name]) ? $data[$name] : null;
        }
        
        return $dados;
    }
}