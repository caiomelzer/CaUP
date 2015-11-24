<?php
if (PHP_SAPI != 'cli') {
	echo "<pre>";
}

$strings = array(
	1 => $_GET['text']
);

echo '{"data":{';


require_once __DIR__ . '/../autoload.php';
$sentiment = new \PHPInsight\Sentiment();
foreach ($strings as $string) {

	// calculations:
	$scores = $sentiment->score($string);
	$class = $sentiment->categorise($string);

	// output:
	echo '"text": "'.$string.'",';
	echo '"dominant": "'.$class.'",';
	echo '"score": '.json_encode($scores); 
	echo "}}";
}