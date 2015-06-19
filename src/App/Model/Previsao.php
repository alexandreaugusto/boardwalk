<?php

namespace App\Model;

class Previsao {

	private $id;
	private $data;
	private $dataPrevisao;
	private $tMin;
	private $tMax;
	private $chuva;

	public function __construct() {
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getData() {
		return $this->data;
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function getDataPrevisao() {
		return $this->dataPrevisao;
	}

	public function setDataPrevisao($dataPrevisao) {
		$this->dataPrevisao = $dataPrevisao;
	}

	public function getTMin() {
		return $this->tMin;
	}

	public function setTMin($tMin) {
		$this->tMin = $tMin;
	}

	public function getTMax() {
		return $this->tMax;
	}

	public function setTMax($tMax) {
		$this->tMax = $tMax;
	}

	public function getChuva() {
		return $this->chuva;
	}

	public function setChuva($chuva) {
		$this->chuva = $chuva;
	}

}