<?php

namespace App\Tests;

use App\XML\ClimatempoXMLParser;
use App\Model\Previsao;

class TestClimatempoXMLParser extends \PHPUnit_Framework_TestCase {

	private $xml;

	/**
	 * @before
	 */
	public function setUp() {
		$this->xml = new ClimatempoXMLParser(415);
	}

	/**
	 * @test
	 */
	public function previsaoDeHoje() {
		$this->assertEquals(date('Y-m-d'), $this->xml->getPrevisoes()[0]->getDataPrevisao());
	}

	public function testQuantidadeElementos() {
		$this->assertCount(4, $this->xml->getPrevisoes());
	}

	public function testRetornoChuva() {
		$this->assertFalse($this->xml->getPrevisoes()[0]->getChuva());
	}

}