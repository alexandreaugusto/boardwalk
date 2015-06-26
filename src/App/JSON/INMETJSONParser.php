<?php

namespace App\JSON;

use App\Model\Previsao;
use App\Exception\XMLVazioException;
use App\DataParser;
use App\Utils\Utilidades;

class INMETJSONParser implements DataParser {

	private $json;
	private $previsoes = array();
	private $codCidade;
	
	public function __construct($codigoCidade) {
		$content = Utilidades::curl_get_contents( 'http://www.inmet.gov.br/portal/index.php' , array( 'r' => 'prevmet/previsaoWebservice/getJsonPrevisaoDiariaPorCidade', 'code' => $codigoCidade ) );
		$this->json = json_decode($content);
		$this->codCidade = $codigoCidade;
		$this->populateDadosPrevisao();
	}

	public function getNumDiasPrevisao() {
		return count($this->previsoes);
	}

	public function populateDadosPrevisao() {
		$periodos = array('manha', 'tarde', 'noite');$cont = 0;
		foreach ($this->json->{$this->codCidade} as $dataPrevisao => $dados) {
                        $cont++;
			$p = new Previsao();
			$p->setData(date("Y-m-d"));
			$p->setDataPrevisao(date("Y-m-d", strtotime($dataPrevisao)));
			$tMax = -1000000000;
			$tMin = 1000000000;
			$chuva = false;
			if($cont < 3) {
				for ($i=0;$i<count($periodos);$i++) {
					$tMax = ($dados->{$periodos[$i]}->temp_max > $tMax)?$dados->{$periodos[$i]}->temp_max:$tMax;
					$tMin = ($dados->{$periodos[$i]}->temp_min < $tMin)?$dados->{$periodos[$i]}->temp_min:$tMin;
					if(strpos(strtolower($dados->{$periodos[$i]}->resumo), 'chuv') !== false || strpos(strtolower($dados->{$periodos[$i]}->tempo), 'chuv') !== false)
						$chuva = true;
				}
			}
			$p->setTMin(($cont < 3)?$tMin:$dados->temp_min);
			$p->setTMax(($cont < 3)?$tMax:$dados->temp_max);
			$p->setChuva(($cont < 3)?$chuva:(strpos(strtolower($dados->resumo), 'chuv') !== false || strpos(strtolower($dados->tempo), 'chuv') !== false));
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