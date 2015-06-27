<?php

namespace App\Tests;

use App\XML\CPTECXMLParser;
use App\Model\Previsao;

class TestCPTECXMLParser extends \PHPUnit_Framework_TestCase {

	private $xml;

	/**
	 * @before
	 */
	public function setUp() {
		$this->xml = new CPTECXMLParser(1083);
	}

	/**
	 * @test
	 */
	public function previsaoDeHoje() {
		$this->assertEquals(date('Y-m-d'), $this->xml->getPrevisoes()[0]->getData());
	}
        
        public function testGetNumDiasPrevisao() {
            $this->assertEquals(4, $this->xml->getNumDiasPrevisao());
        }

	public function testQuantidadeElementos() {
		$this->assertCount(4, $this->xml->getPrevisoes());
	}

}