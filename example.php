<?php

require_once('vendor/autoload.php');

use ProducePdf\Client;

$client = new Client("myApiKey");

//// client with defaults
//$client = new Client(array(
//    'api_key' => 'myApiKey',
//    'page_size' => 'A6',
//    'scale' => '50'
//));

$html = "<!DOCTYPE html>
<html>
<body>

<h1>My First Heading</h1>

<p>My first paragraph.</p>

</body>
</html>
";

file_put_contents("test/my_html.pdf", $client->generatePdfFromHtml($html));
// override defaults
//file_put_contents("test/my_html.pdf", $client->generatePdfFromHtml(array('html' => $html, 'page_size' => 'A6')));


file_put_contents("test/my_url.pdf", $client->generatePdfFromUrl("https://www.microsoft.com"));

// override defaults
//file_put_contents("test/my_url.pdf", $client->generatePdfFromUrl(array('url' => "https://www.microsoft.com", 'page_size' => 'A5')));
