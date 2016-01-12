<?php

namespace App\XML;

use App\Model\Previsao;
use App\Exception\XMLVazioException;
use App\ForecastDataParser;

class CPTECXMLParser implements ForecastDataParser {

	private $xml;
	private $previsoes = array();
	private static $condicoesChuva = array("ec", "ci", "c", "in", "pp", "cm", "cn", "pt", "pm", "np", "pc", "pn", "cv", "ch", "t", "pnt", "psc", "pcm", "pct",
	"pcn", "npt", "npn", "ncn", "nct", "ncm", "npm", "npp", "ct", "ppn", "ppt", "ppm");
	
	public function __construct($codigoCidade) {
		$this->xml = simplexml_load_file("http://servicos.cptec.inpe.br/XML/cidade/" . $codigoCidade . "/previsao.xml", "SimpleXMLElement", LIBXML_NOCDATA);
		$this->populateDadosPrevisao();
	}

	public function getNumDiasPrevisao() {
		return count($this->previsoes);
	}

	public function populateDadosPrevisao() {
		foreach($this->xml->previsao as $previsao) {
			$p = new Previsao();
			$p->setData($this->xml->atualizacao);
			$p->setDataPrevisao($previsao->dia);
			$p->setTMin($previsao->minima);
			$p->setTMax($previsao->maxima);
			$p->setChuva(in_array($previsao->tempo, self::$condicoesChuva));

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