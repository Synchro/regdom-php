#!/usr/bin/env php
<?php
/*
 * Florian Sager, 06.08.2008, sager@agitos.de
 *
 * Auto-Generate PHP array tree that contains all TLDs from the URL (see below);
 * The output has to be copied to reputation-libs/effectiveTLDs.inc.php
 *
 *
 */

DEFINE('URL', 'http://mxr.mozilla.org/mozilla-central/source/netwerk/dns/effective_tld_names.dat?raw=1');

if (PHP_SAPI != "cli") {
	exit;
}

$format = 'php';
if ($argc > 1) {
	if ($argv[1] == 'perl') {
		$format = 'perl';
	} else if ($argv[1] == 'c') {
		$format = 'c';
	}
}

/*
 * Does $search start with $startstring?
 */
function startsWith($search, $startstring) {
	return (substr($search, 0, strlen($startstring)) == $startstring);
}

/*
 * Does $search end with $endstring?
 */
function endsWith($search, $endstring) {
	return (substr($search, -strlen($endstring)) == $endstring);
}


function buildSubdomain(&$node, $tldParts) {

	$dom = trim(array_pop($tldParts));

	$isNotDomain = FALSE;
	if (startsWith($dom, "!")) {
		$dom = substr($dom, 1);
		$isNotDomain = TRUE;
	}

	if (!array_key_exists($dom, $node)) {
		if ($isNotDomain) {
			$node[$dom] = array("!" => "");
		} else {
			$node[$dom] = array();
		}
	}

	if (!$isNotDomain && count($tldParts)>0) {
		buildSubdomain($node[$dom], $tldParts);
	}
}

function printNode($key, $valueTree, $isAssignment = false, $depth = 0) {

	global $format;

	if ($isAssignment) {
		if ($format == "perl") {
			echo "$key = {";
		} else {
			echo "$key = array(";
		}
	} else {
		if (strcmp($key, "!")==0) {
			if ($format == "perl") {
				echo "'!' => {}";
			} else {
				echo "'!' => ''";
			}
			return;
		} else {
			if ($format == "perl") {
				echo "'$key' => {";
			} else {
				echo str_repeat('  ', $depth)."'$key' => array(";
			}
		}
	}

	$keys = array_keys($valueTree);

	for ($i=0; $i<count($keys); $i++) {

		$key = $keys[$i];
		echo "\n";
		printNode($key, $valueTree[$key], false, $depth + 1);

		if ($i+1 != count($valueTree)) {
			echo ",";
		} else {
			echo "";
		}
	}

	if ($format == "perl") {
		echo '}';
	} else {
		echo ')';
	}
}

// sample: root(3:ac(5:com,edu,gov,net,ad(3:nom,co!,*)),de,com)

function printNode_C($key, $valueTree) {

	echo "$key";

	$keys = array_keys($valueTree);

	if (count($keys)>0) {

		if (strcmp($keys['!'], "!")==0) {
			echo "!";
		} else {

			echo "(".count($keys).":";

			for ($i=0; $i<count($keys); $i++) {

				$key = $keys[$i];

				// if (count($valueTree[$key])>0) {
					printNode_C($key, $valueTree[$key]);
				// }

				if ($i+1 != count($valueTree)) {
					echo ",";
				}
			}

			echo ')';
		}
	}
}

// --- main ---

error_reporting(E_ERROR);

$tldTree = array();
$list = file_get_contents(URL);
// $list = "bg\na.bg\n0.bg\n!c.bg\n";
$lines = explode("\n", $list);
$licence = TRUE;
$commentsection = '';

foreach ($lines as $line) {

	if ($licence && startsWith($line, "//")) {

		if ($format == "perl") {
			$commentsection .= "# ".substr($line, 2)."\n";
		} else {
			$commentsection .= $line."\n";
		}

		if (startsWith($line, "// ***** END LICENSE BLOCK")) {
			$licence = FALSE;
			$commentsection .= "\n";
		}
		continue;
	}

	if (startsWith($line, "//") || $line == '') {
		continue;
	}

	// this must be a TLD
	$tldParts = explode('.', $line);

	buildSubdomain($tldTree, $tldParts);
}

// print_r($tldTree);

/*
$tldTree = array(
	'de' => array(),		// test.agitos.de --> agitos.de
	'uk' => array(
		'co' => array(),	// test.agitos.co.uk --> agitos.co.uk
		'xy' => array('!'),	// test.agitos.xy.uk --> xy.uk
		'*' => array()		// test.agitos.ab.uk --> agitos.ab.uk
	)
);
*/

switch($format) {
	case 'c':
		echo $commentsection."\n";
		echo "char* tldString = \"";
		printNode_C("root", $tldTree);
		echo "\";\n";
		break;
	case 'perl':
		echo $commentsection."\n";
		print "package effectiveTLDs;\n\n";
		printNode("\$tldTree", $tldTree, TRUE);
		echo ";\n";
		break;
	case 'php':
	default:
		echo "<?php\n";
		echo $commentsection."\n";
		printNode("\$tldTree", $tldTree, TRUE);
		echo ";\n";
		echo "return \$tldTree;\n";
		break;
}
