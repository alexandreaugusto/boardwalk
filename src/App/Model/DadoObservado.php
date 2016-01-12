<?php

namespace App\Model;

class DadoObservado {

	private $id;
	private $tipo;
	private $dataObservacao;
	private $temperatura;
	private $condicaoTempo;

	public function __construct() {
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getTipo() {
		return $this->tipo;
	}

	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}

	public function getDataObservacao() {
		return $this->dataObservacao;
	}

	public function setDataObservacao($dataObservacao) {
		$this->dataObservacao = $dataObservacao;
	}

	public function getTemperatura() {
		return $this->temperatura;
	}

	public function setTemperatura($temperatura) {
		$this->temperatura = $temperatura;
	}

	public function getCondicaoTempo() {
		return $this->condicaoTempo;
	}

	public function setCondicaoTempo($condicaoTempo) {
		$this->condicaoTempo = $condicaoTempo;
	}

}