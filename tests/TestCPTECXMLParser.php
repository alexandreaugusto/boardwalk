<?php

class TestCPTECXMLParser extends PHPUnit_Framework_TestCase {

	private $xml;

	/**
	 * @before
	 */
	public function setUp() {
		$this->xml = new CPTECXMLParser(1083);
	}

	/*public function testLoadXMLFile() {
		$this->assertInstanceOf('SimpleXMLElement', $this->xml);
	}*/

	public function quantidadeElementos() {
		$this->assertEquals(4, $this->xml->getNumDiasPrevisoes());
	}

}