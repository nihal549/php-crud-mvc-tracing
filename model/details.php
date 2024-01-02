<?php

class details
{
    // table fields
    public $id;
    public $name;
    public $location;
    // message string
    public $id_msg;
    public $name_msg;
    public $location_msg;
    // constructor set default value
    function __construct()
    {
        $id=0;$name=$location="";
        $id_msg=$name_msg=$location_msg="";
    }
}

?>