<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\MonologServiceProvider;
use App\XML\CPTECXMLParser;
use App\XML\ClimatempoXMLParser;

$app = new Silex\Application();

$app['debug'] = true;
$app['charset'] = "iso-8859-1";

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => sys_get_temp_dir() . '/dev.log'
));

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
                "Cuiabá/MT" => 5103403, "Curitiba/PR" => 4106902, "Florianópolis/SC" => 4205407, "Fortaleza/CE" => 229, "Goiânia/GO" => 230, "João Pessoa/PB" => 231,
                "Macapá/AP" => 232, "Maceió/AL" => 233, "Manaus/AM" => 234, "Natal/RN" => 235, "Palmas/TO" => 236, "Porto Alegre/RS" => 237, "Porto Velho/RO" => 238,
                "Recife/PE" => 239, "Rio Branco/AC" => 240, "Rio de Janeiro/RJ" => 241, "Salvador/BA" => 242, "São Luís/MA" => 243, "São Paulo/SP" => 244,
                "Teresina/PI" => 245, "Vitória/ES" => 246);

$app
    ->match('/processar-dados', function () use ($app, $capitaisClimatempo, $capitaisCPTEC) {
        $content = "<h1>Tempo</h1>";
        
        $content .= "<h2>Climatempo</h2>";

        foreach ($capitaisClimatempo as $key => $value) {
            $content .= "<h3>" . $key . "</h3>";
            $climatempo = new ClimatempoXMLParser($value);

            foreach ($climatempo->getPrevisoes() as $p) {
                $content .= $p->getDataPrevisao() . "<br>";
                $content .= $p->getTMin() . " &deg;<br>";
                $content .= $p->getTMax() . " &deg;<br>";
                $content .= ($p->getChuva())?"Vai chover!":"Nao vai chover!";
                $content .= "<br>";
            }
        }
        
        $content .= "<h2>CPTEC/INPE</h2>";
        
        foreach ($capitaisCPTEC as $key => $value) {
            $content .= "<h3>" . $key . "</h3>";
            $cptec = new CPTECXMLParser($value);

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