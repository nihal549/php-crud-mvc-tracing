<?php

use Zipkin\TracingBuilder;
use Zipkin\Samplers\BinarySampler;
use Zipkin\Endpoint;

function create_tracing($localServiceName, $localServiceIPv4, $localServicePort = null)
{
    $httpReporterURL = getenv('HTTP_REPORTER_URL');
    if ($httpReporterURL === false) {
        $httpReporterURL = 'http://localhost:9411/api/v2/spans';
    }

    $endpoint = Endpoint::create($localServiceName, $localServiceIPv4, null, $localServicePort);$logger = new \Monolog\Logger('log');
    $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());

    $reporter = new Zipkin\Reporters\Http(['endpoint_url' => $httpReporterURL]);
    $sampler = BinarySampler::createAsAlwaysSample();
    $tracing = TracingBuilder::create()
        ->havingLocalEndpoint($endpoint)
        ->havingSampler($sampler)
        ->havingReporter($reporter)
        ->build();
    return $tracing;
}