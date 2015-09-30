<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\MonologServiceProvider;
use App\XML\CPTECXMLParser;
use App\XML\ClimatempoXMLParser;
use App\JSON\INMETJSONParser;
use App\XML\TempoAgoraXMLParser;
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

$capitaisClimatempo = array("Aracaju/SE" => 384, "Belém/PA" => 232, "Belo Horizonte/MG" => 107, "Boa Vista/RR" => 347, "Brasília/DF" => 61, "Campo Grande/MS" => 212,
                "Cuiabá/MT" => 218, "Curitiba/PR" => 271, "Florianópolis/SC" => 377, "Fortaleza/CE" => 60, "Goiânia/GO" => 88, "João Pessoa/PB" => 256, "Macapá/AP" => 39,
                "Maceió/AL" => 8, "Manaus/AM" => 25, "Natal/RN" => 334, "Palmas/TO" => 593, "Porto Alegre/RS" => 363, "Porto Velho/RO" => 343, "Recife/PE" => 259, 
                "Rio Branco/AC" => 6, "Rio de Janeiro/RJ" => 321, "Salvador/BA" => 56, "São Luís/MA" => 94, "São Paulo/SP" => 558, "Teresina/PI" => 264, "Vitória/ES" => 84);

$capitaisCPTEC = array("Aracaju/SE" => 220, "Belém/PA" => 221, "Belo Horizonte/MG" => 222, "Boa Vista/RR" => 223, "Brasília/DF" => 224, "Campo Grande/MS" => 225,
                "Cuiabá/MT" => 226, "Curitiba/PR" => 227, "Florianópolis/SC" => 228, "Fortaleza/CE" => 229, "Goiânia/GO" => 230, "João Pessoa/PB" => 231,
                "Macapá/AP" => 232, "Maceió/AL" => 233, "Manaus/AM" => 234, "Natal/RN" => 235, "Palmas/TO" => 236, "Porto Alegre/RS" => 237, "Porto Velho/RO" => 238,
                "Recife/PE" => 239, "Rio Branco/AC" => 240, "Rio de Janeiro/RJ" => 241, "Salvador/BA" => 242, "São Luís/MA" => 243, "São Paulo/SP" => 244,
                "Teresina/PI" => 245, "Vitória/ES" => 246);

$capitaisINMET = array("Aracaju/SE" => 2800308, "Belém/PA" => 1501402, "Belo Horizonte/MG" => 3106200, "Boa Vista/RR" => 1400100, "Brasília/DF" => 5300108, "Campo Grande/MS" => 5002704,
                "Cuiabá/MT" => 5103403, "Curitiba/PR" => 4106902, "Florianópolis/SC" => 4205407, "Fortaleza/CE" => 2304400, "Goiânia/GO" => 5208707, "João Pessoa/PB" => 2507507,
                "Macapá/AP" => 1600303, "Maceió/AL" => 2704302, "Manaus/AM" => 1302603, "Natal/RN" => 2408102, "Palmas/TO" => 1721000, "Porto Alegre/RS" => 4314902, "Porto Velho/RO" => 1100205,
                "Recife/PE" => 2611606, "Rio Branco/AC" => 1200401, "Rio de Janeiro/RJ" => 3304557, "Salvador/BA" => 2927408, "São Luís/MA" => 2111300, "São Paulo/SP" => 3550308,
                "Teresina/PI" => 2211001, "Vitória/ES" => 3205309);

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
    ->match('/', function () use ($app) {
        $app['monolog']->addInfo('Logging in the status route');

        return 'Executando!';
    })
    ->method('GET|POST');

return $app;