<?php

namespace CNPJCPF\Providers;

use CNPJCPF\Data;
use CNPJCPF\Contracts\HttpClientContract;
use CNPJCPF\Contracts\ProviderContract;
use Symfony\Component\DomCrawler\Crawler;

class SiareCNPJProvider implements ProviderContract
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

                $tr1 = $crawler->filter('div.boxConteudo table')->eq(2);
                $data = $tr1->filter('tr:nth-child(3)  td:nth-child(2) span span')->html();

                if(!is_null($data))
                {
                    $tr2 = $crawler->filter('div.boxConteudo table')->eq(3);

                    $names        = trim($data);
                    $zipcode      = $tr2->filter('tr:nth-child(2) td:nth-child(2) span span')->html();
                    $neighborhood = trim($tr2->filter('tr:nth-child(5) td:nth-child(2) span span')->html());
                    $street       = trim($tr2->filter('tr:nth-child(6) td:nth-child(2) span span')->html().' '.$tr2->filter('tr:nth-child(6) td:nth-child(4) span span')->html());
                    $streetnumber = trim($tr2->filter('tr:nth-child(6) td:nth-child(5) span:nth-child(2) span')->html());
                    $complement   = trim($tr2->filter('tr:nth-child(7) td:nth-child(2) span span')->html());
                    return Data::create([
                        'names' => $names,
                        'fantasy' => NULL,
                        'zipcode' => $zipcode,
                        'phone' => NULL,
                        'mail' => NULL,
                        'neighborhood' => $neighborhood,
                        'street' => $street,
                        'streetnumber' => $streetnumber,
                        'complement' => $complement,
                    ]);
                }
            } catch (\InvalidArgumentException $e) {
                return NULL;
            }
        }
    }
}