<?php

namespace App\XML;

use App\Model\Previsao;
use App\Exception\XMLVazioException;
use App\ForecastDataParser;
use App\Utils\Utilidades;

class ClimatempoXMLParser implements ForecastDataParser {

	private $xml;
	private $previsoes = array();
	
	public function __construct($codigoCidade) {
		$content = Utilidades::curl_get_contents( 'http://selos.climatempo.com.br/selos/selo.php' , array( 'CODCIDADE' => $codigoCidade ) );
		$this->xml = simplexml_load_string(utf8_encode($content), "SimpleXMLElement", LIBXML_NOCDATA);
		$this->populateDadosPrevisao();
	}

	public function getNumDiasPrevisao() {
		return count($this->previsoes);
	}

	public function populateDadosPrevisao() {
		foreach($this->xml->cidade as $previsao) {
			$p = new Previsao();
			$p->setData(date("Y-m-d"));
			$dt = explode(" ", $previsao['data'])[0];
			$tmp = strtotime(explode("/", $dt)[1] . "/" . explode("/", $dt)[0]);
			$p->setDataPrevisao(date('Y-m-d', $tmp));
			$p->setTMin($previsao['low']);
			$p->setTMax($previsao['high']);
			$p->setChuva($previsao['mm'] > 0);

			array_push($this->previsoes, $p);
		}
	}

	public function getPrevisoes() {
		if(count($this->previsoes) == 0) {
			throw new XMLVazioException();
		}
		return $this->previsoes;
	}

}