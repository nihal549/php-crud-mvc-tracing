<?php
    require 'model/DetailsModel.php';
    require 'model/details.php';
    require 'service/selectRecord.php';
    require 'service/insertRecord.php';
    require 'service/updateRecord.php';
    require 'service/deleteRecord.php';
    require_once 'config.php';
    session_status() === PHP_SESSION_ACTIVE ? TRUE : session_start();

	class Controller
	{

 		function __construct() 
		{          
			$this->objconfig = new config();
			$this->objsm =  new detailsModel($this->objconfig);
            $this->selectsm = new selectRecord($this->objconfig);
            $this->insertsm = new insertRecord($this->objconfig);
            $this->updatesm = new updateRecord($this->objconfig);
            $this->deletesm = new deleteRecord($this->objconfig);
		}
        
        // mvc handler request
		public function mvcHandler() 
		{
			$act = isset($_GET['act']) ? $_GET['act'] : NULL;
			switch ($act) 
			{
                case 'add' :                    
					$this->insert();
					break;						
				case 'update':
					$this->update();
					break;				
				case 'delete' :					
					$this -> delete();
					break;								
				default:
                    $this->list();
			}
		}		
        // page redirection
		public function pageRedirect($url)
		{
			header('Location:'.$url);
		}	
        // check validation
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
        // add new record
		public function insert()
		{   
            try{
                $detailtb=new details();
                if (isset($_POST['addbtn'])) 
                {   
                    // read form value
                    $detailtb->name = trim($_POST['name']);
                    $detailtb->location = trim($_POST['location']);
                    //call validation
                    $chk=$this->checkValidation($detailtb);
                    if($chk)
                    {   
                        //call insert record            
                        $pid = $this -> insertsm ->insertRecord($detailtb);
                        
                        if($pid>0){			
                            $this->list();
                        }else{
                            echo "Somthing is wrong..., try again.";
                        }
                    }else
                    {    
                        $_SESSION['detailtbl0']=serialize($detailtb);//add session obj
                        $this->pageRedirect("view/insert.php");                
                    }
                }
            }catch (Exception $e) 
            {
                $this->close_db();	
                throw $e;
            }
        }
        // update record
        public function update()
		{
            try
            {
                
                if (isset($_POST['updatebtn'])) 
                {
                    $detailtb=unserialize($_SESSION['detailtbl0']);
                    $detailtb->id = trim($_POST['id'])?? null;
                   
                    $detailtb->name = trim($_POST['name']);
                    $detailtb->location = trim($_POST['location']);
                    
                    // check validation  
                    $chk=$this->checkValidation($detailtb);
                    if($chk)
                    {
                        $res = $this -> updatesm ->updateRecord($detailtb);
                        if($res){			
                            $this->list();                           
                        }else{
                            echo "Somthing is wrong..., try again.";
                        }
                    }else
                    {         
                        $_SESSION['detailtbl0']=serialize($detailtb);
                        $this->pageRedirect("view/update.php");                
                    }
                }elseif(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
                    $id=$_GET['id'];
                    $result=$this->objsm->selectRecord($id);
                    $row=mysqli_fetch_array($result);  
                    $detailtb=new details();
                    $detailtb->id=$row["id"];
                    $detailtb->location=$row["location"];
                    $detailtb->name=$row["name"];
                    $_SESSION['detailtbl0']=serialize($detailtb);
                    $this->pageRedirect('view/update.php');
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
        // delete record
        public function delete()
		{
            try
            {
                if (isset($_GET['id'])) 
                {
                    $id=$_GET['id'];
                    $res=$this->deletesm->deleteRecord($id);                
                    if($res){
                        $this->pageRedirect('index.php');
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
            include "view/list.php";                                        
        }
    }
		
	
?>