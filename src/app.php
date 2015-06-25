<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\MonologServiceProvider;
use App\XML\CPTECXMLParser;
use App\XML\ClimatempoXMLParser;
use App\JSON\INMETJSONParser;

$app = new Silex\Application();

$app['debug'] = true;
$app['charset'] = "iso-8859-1";

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => sys_get_temp_dir() . '/dev.log'
));

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

$app
    ->match('/processar-dados', function () use ($app, $capitaisClimatempo, $capitaisCPTEC, $capitaisINMET) {
        $content = "<h1>Tempo</h1>";
        
        $content .= "<h2>Climatempo</h2>";

        foreach ($capitaisClimatempo as $cidade => $codigo) {
            $content .= "<h3>" . $cidade . "</h3>";
            $climatempo = new ClimatempoXMLParser($codigo);

            foreach ($climatempo->getPrevisoes() as $p) {
                $content .= $p->getDataPrevisao() . "<br>";
                $content .= $p->getTMin() . " &deg;<br>";
                $content .= $p->getTMax() . " &deg;<br>";
                $content .= ($p->getChuva())?"Vai chover!":"Nao vai chover!";
                $content .= "<br>";
            }
        }
        
        $content .= "<h2>CPTEC/INPE</h2>";
        
        foreach ($capitaisCPTEC as $cidade => $codigo) {
            $content .= "<h3>" . $cidade . "</h3>";
            $cptec = new CPTECXMLParser($codigo);

            foreach ($cptec->getPrevisoes() as $p) {
                $content .= $p->getDataPrevisao() . "<br>";
                $content .= $p->getTMin() . " &deg;<br>";
                $content .= $p->getTMax() . " &deg;<br>";
                $content .= ($p->getChuva())?"Vai chover!":"Nao vai chover!";
                $content .= "<br>";
            }
        }

        $content .= "<h2>INMET</h2>";
        
        foreach ($capitaisINMET as $cidade => $codigo) {
            $content .= "<h3>" . $cidade . "</h3>";
            $cptec = new INMETJSONParser($codigo);

            foreach ($cptec->getPrevisoes() as $p) {
                $content .= $p->getDataPrevisao() . "<br>";
                $content .= $p->getTMin() . " &deg;<br>";
                $content .= $p->getTMax() . " &deg;<br>";
                $content .= ($p->getChuva())?"Vai chover!":"Nao vai chover!";
                $content .= "<br>";
            }
        }

        return $content;

    })
    ->method('GET|POST');

$app
    ->match('/', function () use ($app) {
        $app['monolog']->addInfo('Logging in the status route');

        return 'Executando!';
    })
    ->method('GET|POST');

return $app;