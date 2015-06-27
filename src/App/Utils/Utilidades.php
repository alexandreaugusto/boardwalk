<?php

namespace App\Utils;

class Utilidades {

	/**
	 * Faz uma requisi��o HTTP utilizando cURL
	 * @param string $url A URL que ser� recuperada
	 * @param string $method O m�todo HTTP que ser� utilizado para recuperar (GET, POST, etc...)
	 * @param array $data Matriz contendo os campos que ser�o enviados com a requisi��o
	 * @param array $headers Matriz contendo cabe�alhos HTTP
	 * @return string O conte�do recuperado
	 */
	public static function curl_get_contents( $url , array $data = array() , $method = 'GET' , array $headers = array() ){
		$ret = null;

		if ( function_exists( 'curl_init' ) ){
			if ( ( $curl = curl_init() ) !== false ){
				curl_setopt( $curl , CURLOPT_HEADER , false );
				curl_setopt( $curl , CURLOPT_FOLLOWLOCATION , true );
				curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true );
				curl_setopt( $curl , CURLOPT_HTTPHEADER , $headers );
				curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $method );

				if ( count( $data ) ){
					if ( strtoupper( $method ) == 'POST' )
						curl_setopt( $curl , CURLOPT_POSTFIELDS , $data );
					elseif ( count( $data ) )
						$url = sprintf( '%s?%s' , $url , http_build_query( $data ) );
				}

				curl_setopt( $curl , CURLOPT_URL , $url );

				$ret = curl_exec( $curl );
				$err = curl_error( $curl );
				$ern = curl_errno( $curl );

				curl_close( $curl );

				if ( $ern ) trigger_error( sprintf( 'cURL[ %d ]: %s' , $ern , $err ) , E_USER_ERROR );
			} else trigger_error( 'cURL: N�o foi poss�vel iniciar cURL' , E_USER_ERROR );
		} else trigger_error( '� necess�rio ter a extens�o cURL instalada.' , E_USER_ERROR );

		return $ret;
	}

}