<?php
require 'model/DetailsModel.php';
require 'model/details.php';
require 'service/selectRecord.php';
require 'service/insertRecord.php';
require 'service/updateRecord.php';
require 'service/deleteRecord.php';
require_once 'config.php';

class ApiController{
    function __construct() 
		{          
			$this->objconfig = new config();
			$this->objsm =  new detailsModel($this->objconfig);
            $this->selectsm = new selectRecord($this->objconfig);
            $this->insertsm = new insertRecord($this->objconfig);
            $this->updatesm = new updateRecord($this->objconfig);
            $this->deletesm = new deleteRecord($this->objconfig);
		}
        //$data = json_decode(file_get_contents("php://input"), true);
        // var_dump($data);
    public function handleRequest($request,$id){
        
        switch ($request) 
			{
                case 'add' :                    
					$this->insert();
					break;						
				case 'update':
					$this->update($id);
					break;				
				case 'delete' :					
					$this -> delete($id);
					break;								
				default:
                  
                    $this->list();
			}
        
    }
    public function checkValidation($detailtb)
        {    $noerror=true;
            // Validate name
            if(empty($detailtb->name)){
                $detailtb->name_msg = "Field is empty.";$noerror=false;
            } elseif(!filter_var($detailtb->name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
                $detailtb->name_msg = "Invalid entry.";$noerror=false;
            }else{$detailtb->name_msg ="";}
            // Validate location
            if(empty($detailtb->location)){
                $detailtb->location_msg = "Field is empty.";$noerror=false;
            } elseif(!filter_var($detailtb->location, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
                $detailtb->location_msg = "Invalid entry.";$noerror=false;
            }else{$detailtb->location_msg ="";}
            return $noerror;
        }
    public function insert(){
        $data = json_decode(file_get_contents("php://input"), true);
         try{
            $detailtb=new details();
            if ($data!=null) 
            {   
                // read form value
                $detailtb->name = trim($data["name"]);
                $detailtb->location = trim($data["name"]);
                //call validation
                $chk=$this->checkValidation($detailtb);
                if($chk)
                {   
                    //call insert record            
                    $pid = $this -> insertsm ->insertRecord($detailtb);
                    
                    if($pid>0){			
                        echo "inserted successfully";
                    }else{
                        echo "Somthing is wrong..., try again.";
                    }
                }
            }
        }catch (Exception $e) 
        {
            $this->close_db();	
            throw $e;
        }
    }
    public function update($id){
        $data = json_decode(file_get_contents("php://input"), true);
        try{
            $detailtb=new details();
            if ($data!=null) 
            {   
                // read form value
                $detailtb->id =trim($data["id"]);
                $detailtb->name = trim($data["name"]);
                $detailtb->location = trim($data["name"]);
                //call validation
                $chk=$this->checkValidation($detailtb);
                if($chk)
                {   
                    //call update record          
                    $res = $this -> updatesm ->updateRecord($detailtb);
                    var_dump($res);
                        if($res){			
                            echo "Updated Successfully";                           
                        }else{
                            echo "Somthing is wrong..., try again.";
                        }
                }
            }
        }catch (Exception $e) 
        {
            $this->close_db();	
            throw $e;
        }
    }
    public function delete($id){
       try
       {
           if (isset($id)) 
           {
               $deleteid=$id;
               $res=$this->deletesm->deleteRecord($deleteid);                
               if($res){
                   echo "deleted successfully";
               }else{
                   echo "Somthing is wrong..., try again.";
               }
           }else{
               echo "Invalid operation.";
           }
       }
       catch (Exception $e) 
       {
           $this->close_db();				
           throw $e;
       }
    }
    public function list(){
        
        
        $result=$this->selectsm->selectRecord(0);
       
         if($result->num_rows > 0){
           
            while($row = mysqli_fetch_array($result)){
               $id = $row['id'];
               $name = $row['name'];
               $location = $row['location'];
               echo "ID: $id, Name: $name, Location: $location \n";
            }
            
         }
        
    }

    private function sendJsonResponse($data) {
        header('Content-Type: application/json');
        return json_encode($data);
    }

}

?>
   