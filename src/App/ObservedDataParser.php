<?php

namespace App;

interface ObservedDataParser {

	public function getLastUpdateTime();
	public function populateObservedData();

}