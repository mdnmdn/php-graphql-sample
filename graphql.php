<?php
namespace Sandbox;

require(__DIR__.'/vendor.phar');

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

date_default_timezone_set('Europe/Rome');

$processor = new Processor(new Schema([
  'query' => new ObjectType([
      'name' => 'RootQueryType',
      'fields' => [
          'currentTime' => [
              'type' => new StringType(),
              'resolve' => function() {
                  return date('Y-m-d H:ia');
              }
            ],
          'hi' => [
            'type' => new StringType(),
            'resolve' => function() { return 'wow!';},
          ]
      ]
  ])
]));

header('Access-Control-Allow-Credentials: false', true);
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    return;
}

if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
  $rawBody     = file_get_contents('php://input');
  $requestData = json_decode($rawBody ?: '', true);
} else if (isset($_POST['query'])){
  $requestData = $_POST;
} else {
  $requestData = $_GET;
}

$payload   = isset($requestData['query']) ? $requestData['query'] : null;
$variables = isset($requestData['variables']) ? $requestData['variables'] : null;
//$processor = new Processor($schema);
$response = $processor->processPayload($payload, $variables)->getResponseData();
header('Content-Type: application/json');
echo json_encode($response);