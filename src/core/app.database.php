<?php

class Database
{
    private $logger;
	private $serverName;
	private $databaseName;
	private $userName;
	private $password;
	
    function __construct($serverName,$databaseName,$userName,$password) {
      // $this->logger= Logger::getLogger("api.database.php");
	   $this->serverName=$serverName;
	   $this->databaseName=$databaseName;
	   $this->userName=$userName;
	   $this->password=$password;
    }
    
    function dbinteraction() {
	   $conn = new mysqli($this->serverName,$this->userName,$this->password,$this->databaseName);
        if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 
      //  else { $this->logger->info("Database connected Successfully"); }
        return $conn;
    }
    
    function addupdateData($sql) {
       $result="Error";
       $db=new Database($this->serverName,$this->databaseName,$this->userName,$this->password);
       $conn = $db->dbinteraction();
       if ($conn->multi_query($sql) === true) { $result="Success";}
       $conn->close();
      // $this->logger->info("Query(Status-".$result.") : ".$sql); 
       return $result;
    }
    
	function deleteData($sql) {
		$result='Error';
		$db=new Database($this->serverName,$this->databaseName,$this->userName,$this->password);
		$conn = $db->dbinteraction();
		if ($conn->query($sql) === TRUE) {
			$result='Success';
		} 
	    return $result;
	}
   
    function getJSONData($sql) {
        $db=new Database($this->serverName,$this->databaseName,$this->userName,$this->password);
        $conn = $db->dbinteraction();
        $result = mysqli_query($conn, $sql); 
        $json="";
            if (!$result) {   
                 die("Invalid query: " . mysqli_error($conn)); 
           //      $this->logger->error("Query(Status-Invalid) : ".$sql); 
            }
            else {
                $rows= array();
        
                while($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                 }
                 
                $json = json_encode($rows);
            }
         
        mysqli_free_result($result); 
        $conn->close();
        return $json;
    }
    
	function getBulkJSONData($sqlArray){
	  $db=new Database($this->serverName,$this->databaseName,$this->userName,$this->password);
      $conn = $db->dbinteraction();
	  $json='{';
	  foreach($sqlArray as $x => $x_value) {
	    $result = mysqli_query($conn, $x_value); 
		if (!$result) {   
            die("Invalid query: " . mysqli_error($conn)); 
            // $this->logger->error("Query(Status-Invalid) : ".$sql); 
        }
        else {
          $rows= array();
          while($row = $result->fetch_assoc()) { $rows[] = $row; }
		  $json.='"'.$x.'":'.json_encode($rows).',';
        }  
	    mysqli_free_result($result); 
	  }  
      $conn->close();	  
	  $json=chop($json,',');
      $json.='}';
	  return $json;
	}
	
	function getAColumnAsArray($sql,$columnName){
	  $arry_col=array();
	  $dbObj=new Database($this->serverName,$this->databaseName,$this->userName,$this->password);
	  $conn=$dbObj->dbinteraction();
	  $result = $conn->query($sql);
	  if (!$result) { die("Invalid query: " . mysqli_error($conn)); 
        //  $this->logger->error("Query(Status-Invalid) : ".$sql); 
      } else {  while($row = $result->fetch_assoc()){ $arry_col[count($arry_col)] = $row[$columnName];  } }
      $conn->close();
	  return $arry_col;
	}

}
