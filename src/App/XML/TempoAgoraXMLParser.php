<?php

namespace App\XML;

use App\Model\Previsao;
use App\Exception\XMLVazioException;
use App\ForecastDataParser;
use App\Utils\Utilidades;

class TempoAgoraXMLParser implements ForecastDataParser {

	private $rss;
	private $desc;
	private $previsoes = array();
	
	public function __construct($cidade) {
		$content = Utilidades::curl_get_contents( 'http://www.tempoagora.com.br/rss/5dias' );
		$this->rss = new \SimpleXmlElement($content);
		$this->desc = $this->rss->xpath("//item[contains(title,'" . str_replace('/', ' - ', utf8_encode($cidade)) . "')]");
		$this->populateDadosPrevisao();
	}

	public function getNumDiasPrevisao() {
		return count($this->previsoes);
	}

	public function populateDadosPrevisao() {
		$previsoes = array_slice(explode("\n", $this->desc[0]->description), 1, 4);
		foreach($previsoes as $previsao) {
			$p = new Previsao();
			$forecastLine = explode(") - ", $previsao);
			$p->setData(date("Y-m-d", strtotime(str_replace("/", "-", $this->rss->channel->lastBuildDate))));
			$date = explode("/", strip_tags($forecastLine[0]));
			$p->setDataPrevisao(date("Y-m-d", mktime(0, 0, 0, filter_var($date[1], FILTER_SANITIZE_NUMBER_INT), abs(filter_var($date[0], FILTER_SANITIZE_NUMBER_INT)), date('Y'))));
			$dadosPrevisao = explode(" / ", substr($forecastLine[1], 0, strpos($forecastLine[1], '<')));
			$p->setTMin(filter_var($dadosPrevisao[0], FILTER_SANITIZE_NUMBER_INT));
			$p->setTMax(filter_var($dadosPrevisao[1], FILTER_SANITIZE_NUMBER_INT));
			$p->setChuva(filter_var($dadosPrevisao[2], FILTER_SANITIZE_NUMBER_INT) > 0);

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