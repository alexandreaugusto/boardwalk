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

$app
    ->match('/processar-dados', function () use ($app) {
        $content = "<h1>Tempo</h1>";
        $cptaCl = new ClimatempoXMLParser(415);
        $SPCl = new ClimatempoXMLParser(558);
        
        $cptaCPT = new CPTECXMLParser(1083);
        $SPCPT = new CPTECXMLParser(244);
        
        $content .= "<h2>Climatempo</h2>";

        $content .= "<h3>Cachoeira Paulista</h3>";
        
        foreach ($cptaCl->getPrevisoes() as $p) {
            $content .= $p->getDataPrevisao() . "<br>";
            $content .= $p->getTMin() . " &deg;<br>";
            $content .= $p->getTMax() . " &deg;<br>";
            $content .= ($p->getChuva())?"Vai chover!":"Nao vai chover!";
            $content .= "<br>";
        }

        $content .= "<h3>Sao Paulo</h3>";

        foreach ($SPCl->getPrevisoes() as $p) {
            $content .= $p->getDataPrevisao() . "<br>";
            $content .= $p->getTMin() . " &deg;<br>";
            $content .= $p->getTMax() . " &deg;<br>";
            $content .= ($p->getChuva())?"Vai chover!":"Nao vai chover!";
            $content .= "<br>";
        }
        
        $content .= "<h2>CPTEC/INPE</h2>";
        
        $content .= "<h3>Cachoeira Paulista</h3>";
        
        foreach ($cptaCPT->getPrevisoes() as $p) {
            $content .= $p->getDataPrevisao() . "<br>";
            $content .= $p->getTMin() . " &deg;<br>";
            $content .= $p->getTMax() . " &deg;<br>";
            $content .= ($p->getChuva())?"Vai chover!":"Nao vai chover!";
            $content .= "<br>";
        }

        $content .= "<h3>Sao Paulo</h3>";

        foreach ($SPCPT->getPrevisoes() as $p) {
            $content .= $p->getDataPrevisao() . "<br>";
            $content .= $p->getTMin() . " &deg;<br>";
            $content .= $p->getTMax() . " &deg;<br>";
            $content .= ($p->getChuva())?"Vai chover!":"Nao vai chover!";
            $content .= "<br>";
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