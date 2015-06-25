<?php

namespace App\XML;

use App\Model\Previsao;
use App\Exception\XMLVazioException;
use App\DataParser;

class TempoAgoraXMLParser implements DataParser {

	private $rss;
	private $previsoes = array();
	
	public function __construct($cidade) {
		$content = Utilidades::curl_get_contents( 'http://www.tempoagora.com.br/rss/5dias' );
		$this->rss = new SimpleXmlElement($content);
		$this->populateDadosPrevisao();
	}

	public function getNumDiasPrevisao() {
		return count($this->previsoes);
	}

	public function populateDadosPrevisao() {
		foreach($this->rss->previsao as $previsao) {
			$p = new Previsao();
			$p->setData($this->rss->atualizacao);
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