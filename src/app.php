<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\MonologServiceProvider;
use App\XML\CPTECXMLParser;
use App\XML\ClimatempoXMLParser;
use App\JSON\INMETJSONParser;
use App\XML\TempoAgoraXMLParser;
use App\XML\MetarXMLParser;
use MYurasov\Silex\MongoDB\Provider\MongoClientProvider;

$app = new Silex\Application();

$app['charset'] = "iso-8859-1";

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => sys_get_temp_dir() . '/previsoes-centros.log',
    'monolog.level' => Monolog\Logger::INFO,
    'monolog.name' => 'previsoes-centros'
));

$app->register(new MongoClientProvider(), []);

$app['mongodb.mongo_client_options'] = [
    'server' => '',
    'options' => [],
    'driver_options' => []
];

$app['mongodb.db'] = 'weather';

$capitaisClimatempo = array("Aracaju/SE" => 384, "Bel�m/PA" => 232, "Belo Horizonte/MG" => 107, "Boa Vista/RR" => 347, "Bras�lia/DF" => 61, "Campo Grande/MS" => 212,
                "Cuiab�/MT" => 218, "Curitiba/PR" => 271, "Florian�polis/SC" => 377, "Fortaleza/CE" => 60, "Goi�nia/GO" => 88, "Jo�o Pessoa/PB" => 256, "Macap�/AP" => 39,
                "Macei�/AL" => 8, "Manaus/AM" => 25, "Natal/RN" => 334, "Palmas/TO" => 593, "Porto Alegre/RS" => 363, "Porto Velho/RO" => 343, "Recife/PE" => 259, 
                "Rio Branco/AC" => 6, "Rio de Janeiro/RJ" => 321, "Salvador/BA" => 56, "S�o Lu�s/MA" => 94, "S�o Paulo/SP" => 558, "Teresina/PI" => 264, "Vit�ria/ES" => 84);

$capitaisCPTEC = array("Aracaju/SE" => 220, "Bel�m/PA" => 221, "Belo Horizonte/MG" => 222, "Boa Vista/RR" => 223, "Bras�lia/DF" => 224, "Campo Grande/MS" => 225,
                "Cuiab�/MT" => 226, "Curitiba/PR" => 227, "Florian�polis/SC" => 228, "Fortaleza/CE" => 229, "Goi�nia/GO" => 230, "Jo�o Pessoa/PB" => 231,
                "Macap�/AP" => 232, "Macei�/AL" => 233, "Manaus/AM" => 234, "Natal/RN" => 235, "Palmas/TO" => 236, "Porto Alegre/RS" => 237, "Porto Velho/RO" => 238,
                "Recife/PE" => 239, "Rio Branco/AC" => 240, "Rio de Janeiro/RJ" => 241, "Salvador/BA" => 242, "S�o Lu�s/MA" => 243, "S�o Paulo/SP" => 244,
                "Teresina/PI" => 245, "Vit�ria/ES" => 246);

$capitaisINMET = array("Aracaju/SE" => 2800308, "Bel�m/PA" => 1501402, "Belo Horizonte/MG" => 3106200, "Boa Vista/RR" => 1400100, "Bras�lia/DF" => 5300108, "Campo Grande/MS" => 5002704,
                "Cuiab�/MT" => 5103403, "Curitiba/PR" => 4106902, "Florian�polis/SC" => 4205407, "Fortaleza/CE" => 2304400, "Goi�nia/GO" => 5208707, "Jo�o Pessoa/PB" => 2507507,
                "Macap�/AP" => 1600303, "Macei�/AL" => 2704302, "Manaus/AM" => 1302603, "Natal/RN" => 2408102, "Palmas/TO" => 1721000, "Porto Alegre/RS" => 4314902, "Porto Velho/RO" => 1100205,
                "Recife/PE" => 2611606, "Rio Branco/AC" => 1200401, "Rio de Janeiro/RJ" => 3304557, "Salvador/BA" => 2927408, "S�o Lu�s/MA" => 2111300, "S�o Paulo/SP" => 3550308,
                "Teresina/PI" => 2211001, "Vit�ria/ES" => 3205309);

$capitaisMETAR = array("Aracaju/SE" => "SBAR", "Bel�m/PA" => "SBBE", "Belo Horizonte/MG" => "SBBH", "Boa Vista/RR" => "SBBV",
                "Bras�lia/DF" => "SBBR", "Campo Grande/MS" => "SBCG", "Cuiab�/MT" => "SBCY", "Curitiba/PR" => "SBCT", "Florian�polis/SC" => "SBFL",
                "Fortaleza/CE" => "SBFZ", "Goi�nia/GO" => "SBGO", "Jo�o Pessoa/PB" => "SBJP", "Macap�/AP" => "SBMQ", "Macei�/AL" => "SBMO",
                "Manaus/AM" => "SBMN", "Natal/RN" => "SBNT", "Palmas/TO" => "SBPJ", "Porto Alegre/RS" => "SBPA", "Porto Velho/RO" => "SBPV",
                "Recife/PE" => "SBRF", "Rio Branco/AC" => "SBRB", "Rio de Janeiro/RJ" => "SBGL", "Salvador/BA" => "SBSV", "S�o Lu�s/MA" => "SBSL",
                "S�o Paulo/SP" => "SBSP", "Teresina/PI" => "SBTE", "Vit�ria/ES" => "SBVT");

$app
    ->match('/processar-dados', function () use ($app, $capitaisClimatempo, $capitaisCPTEC, $capitaisINMET) {

        $cont = 0;

        $time_start = microtime(true);

        try {

            foreach ($capitaisClimatempo as $cidade => $codigo) {
                $climatempo = new ClimatempoXMLParser($codigo);

                $city = explode("/", $cidade);

                foreach ($climatempo->getPrevisoes() as $p) {
                    $app['mongodb.mongo_client']->selectCollection('weather', 'previsoes')->insert(array('cidade' => utf8_encode($city[0]), 'uf' => $city[1], 'centro_previsao' => 'Climatempo',
    'data' => new \MongoDate(strtotime($p->getData())), 'data_prev' => new \MongoDate(strtotime($p->getDataPrevisao())),
    'tmax' => new \MongoInt32($p->getTMax()), 'tmin' => new \MongoInt32($p->getTMin()), 'chuva' => $p->getChuva()));
                    $cont++;
                }
            }

        } catch(\Exception $ex) {

            $app['monolog']->addError("Erro na execucao do Climatempo: " . $ex->getMessage());

        }

        $time_end = microtime(true);

        $app['monolog']->addInfo("Tempo de processamento do climatempo: " . (($time_end - $time_start)/60));

        $time_start = microtime(true);

        try {
        
            foreach ($capitaisCPTEC as $cidade => $codigo) {
                $cptec = new CPTECXMLParser($codigo);

                $city = explode("/", $cidade);

                foreach ($cptec->getPrevisoes() as $p) {
                    $app['mongodb.mongo_client']->selectCollection('weather', 'previsoes')->insert(array('cidade' => utf8_encode($city[0]), 'uf' => $city[1], 'centro_previsao' => 'CPTEC',
    'data' => new \MongoDate(strtotime($p->getData())), 'data_prev' => new \MongoDate(strtotime($p->getDataPrevisao())),
    'tmax' => new \MongoInt32($p->getTMax()), 'tmin' => new \MongoInt32($p->getTMin()), 'chuva' => $p->getChuva()));
                    $cont++;
                }
            }

        } catch(\Exception $ex) {

            $app['monolog']->addError("Erro na execucao do CPTEC: " . $ex->getMessage());

        }

        $time_end = microtime(true);

        $app['monolog']->addInfo("Tempo de processamento do CPTEC: " . (($time_end - $time_start)/60));

        $time_start = microtime(true);

        try {
        
            foreach ($capitaisINMET as $cidade => $codigo) {
                $inmet = new INMETJSONParser($codigo);

                $city = explode("/", $cidade);

                foreach ($inmet->getPrevisoes() as $p) {
                    $app['mongodb.mongo_client']->selectCollection('weather', 'previsoes')->insert(array('cidade' => utf8_encode($city[0]), 'uf' => $city[1], 'centro_previsao' => 'INMET',
    'data' => new \MongoDate(strtotime($p->getData())), 'data_prev' => new \MongoDate(strtotime($p->getDataPrevisao())),
    'tmax' => new \MongoInt32($p->getTMax()), 'tmin' => new \MongoInt32($p->getTMin()), 'chuva' => $p->getChuva()));
                    $cont++;
                }
            }

        } catch(\Exception $ex) {

            $app['monolog']->addError("Erro na execucao do INMET: " . $ex->getMessage());

        }

        $time_end = microtime(true);

        $app['monolog']->addInfo("Tempo de processamento do INMET: " . (($time_end - $time_start)/60));

        $time_start = microtime(true);

        try {
        
            foreach ($capitaisINMET as $cidade => $codigo) {
                $tempoAgora = new TempoAgoraXMLParser($cidade);

                $city = explode("/", $cidade);

                foreach ($tempoAgora->getPrevisoes() as $p) {
                    $app['mongodb.mongo_client']->selectCollection('weather', 'previsoes')->insert(array('cidade' => utf8_encode($city[0]), 'uf' => $city[1], 'centro_previsao' => 'Somar',
    'data' => new \MongoDate(strtotime($p->getData())), 'data_prev' => new \MongoDate(strtotime($p->getDataPrevisao())),
    'tmax' => new \MongoInt32($p->getTMax()), 'tmin' => new \MongoInt32($p->getTMin()), 'chuva' => $p->getChuva()));
                    $cont++;
                }
            }

        } catch(\Exception $ex) {

            $app['monolog']->addError("Erro na execucao da Somar: " . $ex->getMessage());

        }

        $time_end = microtime(true);

        $app['monolog']->addInfo("Tempo de processamento da Somar: " . (($time_end - $time_start)/60));

        return 'ok-'.$cont;

    })
    ->method('GET|POST');

    $app
    ->get('/recuperar-metar', function () use ($app, $capitaisMETAR) {
        $cont = 0;

        $time_start = microtime(true);
        $log = "";

        try {

            foreach ($capitaisMETAR as $cidade => $codigo) {
                $city = explode("/", $cidade);
                
                $data_obs = new MetarXMLParser($codigo);
                
                $obs = $data_obs->getObservedData();
                $data = \DateTime::createFromFormat('d/m/Y H:i:s', $obs->getDataObservacao());

                $abc = $app['mongodb.mongo_client']->selectCollection('weather', 'observacoes')->find(array('cidade' => utf8_encode($city[0]), 'uf' => $city[1], 'tipo' => "METAR"))->sort(array('data_hora' => -1))->limit(1);

                if($abc->hasNext()) {
                    $abc->next();
                    $temp = $abc->current();
                    
                    if($data->format('Y-m-d H:i:s') != date('Y-m-d H:i:s', explode(" ", $temp['data_hora'])[1])) {
                        $app['mongodb.mongo_client']->selectCollection('weather', 'observacoes')->insert(array('cidade' => utf8_encode($city[0]), 'uf' => $city[1], 'tipo' => $obs->getTipo(),
            'data_hora' => new \MongoDate(strtotime($data->format('Y-m-d H:i:s'))), 'temperatura' => new \MongoInt32($obs->getTemperatura()), 'condicao_tempo' => utf8_encode($obs->getCondicaoTempo())));
                        $log .= $cidade . " - " . $codigo . " / " . $data->format('Y-m-d H:i:s') . " | ";
                        $cont++;
                    }
                }
            }

        } catch(\Exception $ex) {

            $app['monolog']->addError("Erro na execucao do METAR: " . $ex->getMessage());

        }

        if($log != "")
            $app['monolog']->addInfo(utf8_encode($log));

        return $cont;
    });

$app
    ->match('/', function () use ($app) {
        $app['monolog']->addInfo('Logging in the status route');

        return 'Executando!';
    })
    ->method('GET|POST');

return $app;
