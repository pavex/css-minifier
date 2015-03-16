#!/usr/bin/env php
<?php
// Command line utility to join and minify stylesheets
// Pavel Machacek <pavex@ines.cz>, 2015

$exe = array_shift($argv); // remove filename
$exe = basename($exe, '.php');

$HELP = <<<EOT
CSS-minifier 0.9, (c) pavex
Usage: $exe [options]...

Options include:

    -h, --help      Show this message
    -f=input-file   Add input stylesheet filename
    -o=output-file  Set output filename
    -m              Apply minifier


EOT;

$opts = getopt('f:o:mh', array('help'));

//
if (!$fname = array_shift($argv)) {
	echo $HELP;
	exit(1);
}

//
$files = array();
$outputfile = FALSE;


//
if (isset($opts['f'])) {
	$files = (array) $opts['f'];
}
if (isset($opts['o'])) {
	$outputfile = $opts['o'];
}


//
$buffer = "";
foreach ($files as $file) {
	if ($outputfile) {
		echo "Merge file `$file`.\n";
	}
	$buffer .= file_get_contents($file);
}


// Minifier
function filter_minify($buffer, array $opts = array()) {
	global $outputfile;
	if ($outputfile) {
		$size = strlen($buffer) / 1024;
	}
// Remove comments
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
// Remove space after colons
	$buffer = str_replace(': ', ':', $buffer);
// Remove linebreak
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
// Remove whitespace
	$buffer = preg_replace('/\s+/', ' ', $buffer);
//
	if ($outputfile) {
		$newsize = strlen($buffer) / 1024;
		$ratio = $newsize / $size * 100;

		echo sprintf("Minify %0.2fKB buffer into to %0.2fKB with radio %0.1f%%.\n",
			$size, $newsize, $ratio);
	}
	return $buffer;
}


//
if (isset($opts['m'])) {
	$buffer = filter_minify($buffer, $opts);
}


//
if ($outputfile) {
	echo "Write output into file `$outputfile`.\n";
	file_put_contents($outputfile, $buffer);
}
else {
	echo $buffer;
}

?>
