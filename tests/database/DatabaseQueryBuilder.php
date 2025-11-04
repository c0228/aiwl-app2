<?php
 class DatabaseQueryBuilder {

  /* private function whereBuilder($conditions, $implode){
      $where = [];
      foreach ($conditions as $col => $val) {
         $where[] = $col . " = '" .$val . "'";
      }
      return implode(' '.$implode.' ', $where);
   } */

   function buildEmptyDataCheckQuery($dbTableName) {
      // Read and decode JSON schema
      $conn = $GLOBALS["DB_CONN"];
      $schemaFile = $GLOBALS["DB_SCHEMAS_PATH"].'/'.$dbTableName.'.json';
      if (!file_exists($schemaFile)) {
         throw new Exception("Schema file not found: $schemaFile");
      }
      $schemaData = json_decode(file_get_contents($schemaFile), true);
      if (!isset($schemaData["columns"])) {
         throw new Exception("Invalid schema format — 'columns' not found in JSON.");
      }
      $columns = $schemaData["columns"];
      $whereClauses = [];
      // Build WHERE conditions dynamically
      foreach ($columns as $col) {
         // Treat timestamp or numeric columns separately
         if (stripos($col, 'On') !== false || $col === 'balance') {
               $whereClauses[] = "$col IS NULL";
         } else {
               $whereClauses[] = "($col IS NULL OR $col = '')";
         }
      }
      return implode(" OR ", $whereClauses);
   }

   public function selectQuery($tableName, $conditions){
      return "SELECT * FROM $tableName WHERE ".$conditions;
   }

   public function deleteQuery($tableName, $conditions){
      return "DELETE FROM $tableName WHERE " .$conditions;
   }

 }
?>