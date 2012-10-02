#!/usr/bin/env php
<?php

/*
 * Calculate the effective registered domain of a fully qualified domain name.
 *
 * <@LICENSE>
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to you under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at:
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * </@LICENSE>
 *
 * Florian Sager, 25.07.2008, sager@agitos.de
 */

if (PHP_SAPI != "cli") {
	exit;
}

if ($argc < 2) {
	echo "test-regDomain.php <(fully-qualified-domain-name )+>\n";
	exit;
}

require 'effectiveTLDs.inc.php';
$tldTree = require 'regDomain.inc.php';

// strip subdomains from every signing domain
// char dom[] = "sub2.sub.registered.nom.ad";

for ($i=1; $i<$argc; $i++) {

	$registeredDomain = getRegisteredDomain($argv[$i], $tldTree);

	if (is_null($registeredDomain)) {
		printf("error: %s\n", $argv[$i]);
	} else {
		printf("valid: %s => %s\n", $argv[$i], $registeredDomain);
	}
}
