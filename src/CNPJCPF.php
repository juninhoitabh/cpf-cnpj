<?php

namespace CNPJCPF;

use CNPJCPF\Clients\CurlHttpClient;
use CNPJCPF\Contracts\HttpClientContract;
use CNPJCPF\Contracts\ProviderContract;
use CNPJCPF\Exceptions\CNPJ_CPFInvalidParameterException;
use CNPJCPF\Exceptions\CNPJ_CPFTimeoutException;
use CNPJCPF\Providers\SiareCPFProvider;
use CNPJCPF\Providers\ReceitaWSProvider;
use CNPJCPF\Providers\SiareCNPJProvider;
use CNPJCPF\Data;

/**
 * Class to query CNPJ/CPF.
 */
class CNPJCPF
{
    /**
     * @var HttpClientContract
     */
    private $client;

    /**
     * @var ProviderContract[]
     */
    private $providers = [];

    /**
     * @var int
     */
    private $timeout = 20;

    /**
     * CNPJCPJ constructor.
     */
    public function __construct()
    {
        $this->client = new CurlHttpClient();
    }

    /**
     * Search CNPJ/CPF on all providers.
     *
     * @param string $cnpj_cpf CNPJ/CPF 
     *
     * @return Data
     */
    public static function search($cnpj_cpf)
    {
        $CNPJCPJ = new self();
        if(self::isCnpj($cnpj_cpf))
        {
            $CNPJCPJ->addProvider(new ReceitaWSProvider());
            $CNPJCPJ->addProvider(new SiareCNPJProvider());
        }
        elseif(self::isCpf($cnpj_cpf))
        {
            $CNPJCPJ->addProvider(new SiareCPFProvider());
        }
        else
        {
            throw new CNPJ_CPFInvalidParameterException('CNPJ or CPF is invalid');
        }

        $data = $CNPJCPJ->resolve($cnpj_cpf);

        return $data;
    }

    /**
     * Performs provider CNPJ/CPF search.
     *
     * @param string $cnpj_cpf CNPJ/CPFEP
     *
     * @return Data
     */
    public function resolve($cnpj_cpf)
    {
        if (count($this->providers) == 0) {
            throw new CNPJ_CPFInvalidParameterException('No providers were informed');
        }

        /*
         * Execute
         */
        $time       = time();
        $dataactive = NULL;
        $data       = NULL;
        $cont       = 0;

        do 
        {
            foreach ($this->providers as $provider) {
                if($cont == 1)
                {
                    $this->m_sleep(1);
                }
                $dataactive = $provider->getData($cnpj_cpf, $this->client);

                if(!is_null($dataactive))
                {
                    $data = $dataactive;
                }
            }

            if((time() - $time) >= $this->timeout) {
                //throw new CNPJ_CPFTimeoutException("Maximum execution time of $this->timeout seconds exceeded in PHP");
                $address = Data::create([
                    'names' => NULL,
                ]);
            }
            $cont++;

        } while (is_null($data));

        /*
         * Return
         */
        $data = $data == '1' ? NULL : $data;

        return $data;
    }

    /**
     * Set client http.
     *
     * @param HttpClientContract $client
     */
    public function setClient(HttpClientContract $client)
    {
        $this->client = $client;
    }

    /**
     * Set array providers.
     *
     * @param HttpClientContract $client
     */
    public function addProvider(ProviderContract $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * Set timeout.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Remove máscara de um texto
     *
     * @param  string $texto
     * @return string (Texto sem a mascara)
     */
    public static function unmask($texto)
    {
        return preg_replace('/[\-\|\(\)\/\.\: ]/', '', $texto);
    }

    public static function isCnpj($cnpj)
    {
        $valid = true;
        $cnpj = str_pad(self::unmask($cnpj), 14, '0', STR_PAD_LEFT);

        if (!ctype_digit($cnpj))
            return false;

        for ($x = 0; $x < 10; $x++) {
            if ($cnpj == str_repeat($x, 14)) {
                $valid = false;
            }
        }

        if ($valid) {
            if (strlen($cnpj) != 14) {
                $valid = false;
            } else {
                for ($t = 12; $t < 14; $t++) {
                    $d = 0;
                    $c = 0;
                    for ($m = $t - 7; $m >= 2; $m--, $c++) {
                        $d += $cnpj{$c} * $m;
                    }
                    for ($m = 9; $m >= 2; $m--, $c++) {
                        $d += $cnpj{$c} * $m;
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cnpj{$c} != $d) {
                        $valid = false;
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    /**
     * Metodo para verificar se um CPF é válido
     *
     * @param  string $cpf
     * @return boolean
     */
    public static function isCpf($cpf)
    {
        $valid = true;
        $cpf = str_pad(self::unmask($cpf), 11, '0', STR_PAD_LEFT);

        if (!ctype_digit($cpf))
            return false;

        for ($x = 0; $x < 10; $x++) {
            if ($cpf == str_repeat($x, 11)) {
                $valid = false;
            }
        }

        if ($valid) {
            if (strlen($cpf) != 11) {
                $valid = false;
            } else {
                for ($t = 9; $t < 11; $t++) {
                    $d = 0;
                    for ($c = 0; $c < $t; $c++) {
                        $d += $cpf{$c} * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf{$c} != $d) {
                        $valid = false;
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    private function m_sleep($milliseconds) {
        return usleep($milliseconds * 1000); // Microseconds->milliseconds
    }
}