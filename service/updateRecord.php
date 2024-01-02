<?php
use Zipkin\Propagation\Map;
require_once  'vendor/autoload.php';
require_once  'tracing.php';

$tracing = create_tracing('update', '127.0.0.2');

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$carrier = array_map(function ($header) {
    return $header[0];
}, $request->headers->all());

/* Extracts the context from the HTTP headers */
$extractor = $tracing->getPropagation()->getExtractor(new Map());
$extractedContext = $extractor($carrier);

$tracer = $tracing->getTracer();
$span = $tracer->nextSpan($extractedContext);
$span->start();
$span->setKind(Zipkin\Kind\SERVER);
$span->setName('parse_request');

$childSpan = $tracer->newChild($span->getContext());
$childSpan->start();
$childSpan->setKind(Zipkin\Kind\CLIENT);
$childSpan->setName('containerDetais:get_list:mysql_query_update');

usleep(50000);
class updateRecord{
	private $logger;

    function __construct($consetup)
		{	
			
			$this->host = $consetup->host;
			$this->user = $consetup->user;
			$this->pass =  $consetup->pass;
			$this->db = $consetup->db; 
			
		}
        //open db
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
        public function updateRecord($obj)
		{	
			try
			{	
				$this->open_db();
				$query=$this->condb->prepare("UPDATE details SET name=?,location=? WHERE id=?");
				$query->bind_param("ssi", $obj->name,$obj->location,$obj->id);
				$query->execute();
				$res=$query->get_result();						
				$query->close();
				$this->close_db();
				return true;
			}
			catch (Exception $e) 
			{
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