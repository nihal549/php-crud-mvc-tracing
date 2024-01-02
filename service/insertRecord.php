<?php
use Zipkin\Propagation\Map;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once  'vendor/autoload.php';
require_once  'tracing.php';

$tracing = create_tracing('insert', '127.0.0.2');

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$carrier = array_map(function ($header) {
    return $header[0];
}, $request->headers->all());

/* Extracts the context from the HTTP headers */
$extractor = $tracing->getPropagation()->getExtractor(new Map());
$extractedContext = $extractor($carrier);

/* Get users from DB */
$tracer = $tracing->getTracer();
$span = $tracer->nextSpan($extractedContext);
$span->start();
$span->setKind(Zipkin\Kind\SERVER);
$span->setName('parse_request_insert');

$childSpan = $tracer->newChild($span->getContext());
$childSpan->start();
$childSpan->setKind(Zipkin\Kind\CLIENT);
$childSpan->setName('containerDetais:get_list:mysql_query_insert');

usleep(50000);

class insertRecord{
	private $logger;
    // set database config for mysql
		function __construct($consetup)
		{	
			
			$this->host = $consetup->host;
			$this->user = $consetup->user;
			$this->pass =  $consetup->pass;
			$this->db = $consetup->db; 
			$this->logger = new Logger('insert');
			$this->logger->pushHandler(new StreamHandler(__DIR__.'/logs.log', Logger::DEBUG));           					
		}
		
		
		// open mysql data base
		public function open_db()
		{	
			$this->condb=new mysqli($this->host,$this->user,$this->pass,$this->db);
			if ($this->condb->connect_error) 
			{
    			die("Erron in connection: " . $this->condb->connect_error);
			}
		}
		// close database
		public function close_db()
		{
			$this->condb->close();
		}	
		

		// insert record
		public function insertRecord($obj)
		{	
			
			try
			{	
				$this->logger->info('inserting record');
				$this->open_db();
				$query=$this->condb->prepare("INSERT INTO details (name,location) VALUES (?, ?)");
				$query->bind_param("ss",$obj->name,$obj->location);
				$query->execute();
				$res= $query->get_result();
				$last_id=$this->condb->insert_id;
				$query->close();
				$this->close_db();
				return $last_id;
			}
			catch (Exception $e)
			 
			{	$this->logger->error(' error while inserting ');
				$this->close_db();	
            	throw $e;
        	}
			
		}
}
$childSpan->finish();

$span->finish();

/* Sends the trace to zipkin once the response is served */
register_shutdown_function(function () use ($tracer) {
    $tracer->flush();
});
?>