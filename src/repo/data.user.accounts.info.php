<?php
class UserAccountsInfo {
   function query_add_newUserAccount($name, $country, $state, $mobile, $balance){
	$query="INSERT INTO user_accounts_info (userId, name, country, state, mobile, balance, createdOn, lastUpdatedOn) ";
	$query.="SELECT uid, '".$name."', '".$country."', '".$state."', '".$mobile."', '".$balance."', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP ";
	$query.="FROM ( ";
    $query.="SELECT CONCAT( 'acc-', LPAD(FLOOR(RAND() * 10000), 4, '0'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0') ) AS uid ";
	$query.=") AS gen ";
	$query.="WHERE NOT EXISTS ( SELECT 1 FROM user_accounts_info WHERE userId = gen.uid ) ";
	$query.="LIMIT 1;";
	return $query;
   }
}

$userAccountsInfo = new UserAccountsInfo();
?>