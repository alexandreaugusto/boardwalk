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
		$cont = 0;
		$periodos = array('manha', 'tarde', 'noite');
		foreach ($this->json->{$this->codCidade} as $previsao) {
			$p = new Previsao();
			$p->setData(date("Y-m-d"));
			$p->setDataPrevisao(date("Y-m-d", mktime(0, 0, 0, date('m'), intval(date('d')) + $cont++, date('Y'))));
			$tMax = -1000000000;
			$tMin = 1000000000;
			$chuva = false;
			if($cont < 3) {
				for ($i=0;$i<count($periodos);$i++) {
					$tMax = ($previsao->{$periodos[$i]}->temp_max > $tMax)?$previsao->{$periodos[$i]}->temp_max:$tMax;
					$tMin = ($previsao->{$periodos[$i]}->temp_min < $tMin)?$previsao->{$periodos[$i]}->temp_min:$tMin;
					if(strpos(strtolower($previsao->{$periodos[$i]}->resumo), 'chuv') !== false || strpos(strtolower($previsao->{$periodos[$i]}->tempo), 'chuv') !== false)
						$chuva = true;
				}
				$p->setTMin($tMin);
				$p->setTMax($tMax);
				$p->setChuva($chuva);
			} else {
				$p->setTMin($previsao->temp_min);
				$p->setTMax($previsao->temp_max);
				$p->setChuva((strpos(strtolower($previsao->resumo), 'chuv') !== false || strpos(strtolower($previsao->tempo), 'chuv') !== false));
			}

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