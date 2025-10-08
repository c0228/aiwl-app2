<?php
/*
  { "session_set" : [ { "key" : "key-01" , "value" : "value-01" },
                      { "key" : "key-02" , "value" : "value-02" } ],
	"session_get" : [ "key-01", "key-02", "key-03" ]
  }
 */
session_start();
if(isset($_POST["action"]) && $_POST["action"]=='Session') {
  if(isset($_POST["SESSION_JSON"])) {
  $sessionJSON=$_POST["SESSION_JSON"];
  $sessionDeJSON = json_decode($sessionJSON);
  $session_set=$sessionDeJSON->{"session_set"};
  
  foreach($session_set[0] as $key=>$value){
	 $_SESSION[$key]  = $value;
  }
  
  $session_get=$sessionDeJSON->session_get;
  $content='{';
  for($index=0;$index<count($session_get);$index++) {
    $key=$session_get[$index];$value='';
	if(isset($_SESSION[$key])){ $value=$_SESSION[$key]; }
    $content.='"'.$key.'" : "'.str_replace("\"","\\\"",$value).'",';
  }
  $content=chop($content,",");
  $content.='}';
  echo $content;
  } else { echo "MISSING_SESSION"; }
  
}
else if(isset($_POST["action"]) && $_POST["action"]=='DestroySession') {
   session_destroy();
}
?>