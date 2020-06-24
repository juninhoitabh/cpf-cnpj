<?php

namespace CNPJCPF\Providers;

use CNPJCPF\Data;
use CNPJCPF\Contracts\HttpClientContract;
use CNPJCPF\Contracts\ProviderContract;
use Symfony\Component\DomCrawler\Crawler;

class SiareCPFProvider implements ProviderContract
{
    /**
     * @return Data
     */
    public function getData($cnpj_cpf, HttpClientContract $client)
    {
        $response = $client->post('https://www2.fazenda.mg.gov.br/sol/ctrl/SOL/NFAE/SERVICO_027', [
            'ACAO'                                             => 'PROCURAR',
            'unifwScrollTop'                                   => '0',
            'unifwScrollLeft'                                  => '0',
            'txtIndicadorUC'                                   => 'Requerer+NFA',
            'tipoRequerente.desRequerente'                     => 'Pessoa+F%EDsica',
            'numeroAbaAnterior'                                => '2',
            'tipoDestinatario.identificador'                   => '4',
            'produtorRuralLogado'                              => 'false',
            'tipo.descricao'                                   => 'Sa%EDda',
            'circulacao.desCirculacao'                         => 'Interna',
            'operacaoRequerimentoNFASimplificado'              => 'false',
            'remetente.cpfCnpj'                                => $cnpj_cpf,
            'destinatario.tipoIdentificadorIeDestinatario.id:' => '1',
        ]);

        if(!is_null($response)) 
        {
            try
            {
                $crawler = new Crawler($response);

                $data = $crawler->filter('table.cntbdy td.ctnbdy span span')->html();

                if(!is_null($data))
                {
                    $names = $data;
                    return Data::create([
                        'names' => $names,
                    ]);
                }
            } catch (\InvalidArgumentException $e) {
                return NULL;
            }
        }
    }
}