<?php
use Zipkin\Propagation\Map;
require_once  'vendor/autoload.php';
require_once  'tracing.php';
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$tracing = create_tracing('select', '127.0.0.2');

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
$span->setName('parse_request');

$childSpan = $tracer->newChild($span->getContext());
$childSpan->start();
$childSpan->setKind(Zipkin\Kind\CLIENT);
$childSpan->setName('containerDetais:get_list:mysql_query_select');

usleep(50000);
class selectRecord{
	private $logger;
    function __construct($consetup)
		{	
			
			$this->host = $consetup->host;
			$this->user = $consetup->user;
			$this->pass =  $consetup->pass;
			$this->db = $consetup->db; 
			$this->logger = new Logger('insert');
			$this->logger->pushHandler(new StreamHandler(__DIR__.'/logs.log', Logger::DEBUG));           					
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
        public function selectRecord($id)
		{	
			
			try
			{	$this->logger->info(' querying the db with  id :'.$obj);
                $this->open_db();
                if($id>0)
				{	
					$query=$this->condb->prepare("SELECT * FROM details WHERE id=?");
					$query->bind_param("i",$id);
				}
                else
                {	
					$this->logger->info(' querying the db without  id :');
					$query=$this->condb->prepare("SELECT * FROM details");	
				}
				
				$query->execute();
				$res=$query->get_result();	
				$query->close();				
				$this->close_db();                
                return $res;
			}
			catch(Exception $e)
			{	$this->logger->error('erorr while querying ..');
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