<?php

namespace App\XML;

use App\Model\DadoObservado;
use App\Exception\XMLVazioException;
use App\ObservedDataParser;

class MetarXMLParser implements ObservedDataParser {

	private $xml;
	private $dadoObservado;
	
	public function __construct($codigoMetar) {
		$this->xml = simplexml_load_file("http://servicos.cptec.inpe.br/XML/estacao/" . $codigoMetar . "/condicoesAtuais.xml", "SimpleXMLElement", LIBXML_NOCDATA);
		$this->populateObservedData();
	}

	public function getLastUpdateTime() {
		return $this->dadoObservado->getDataObservacao();
	}

	public function populateObservedData() {
		$this->dadoObservado = new DadoObservado();
		$this->dadoObservado->setTipo("METAR");
		$this->dadoObservado->setDataObservacao($this->xml->atualizacao);
		$this->dadoObservado->setTemperatura($this->xml->temperatura);
		$this->dadoObservado->setCondicaoTempo($this->xml->tempo);
	}

	public function getObservedData() {
		if($this->dadoObservado->getTemperatura() == null) {
			throw new XMLVazioException();
		}
		return $this->dadoObservado;
	}

}