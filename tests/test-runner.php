<?php

require_once __DIR__.'/database/DatabaseConfig.php';
require_once __DIR__ . '/database/DatabaseHelper.php';
require_once __DIR__ . '/database/DatabaseQueryBuilder.php';
require_once __DIR__.'/utils/ReportConfig.php';
require_once __DIR__.'/constants/BusinessConstants.php';
// require_once __DIR__ . '/helpers/TestHelper.php';
require_once __DIR__ . '/helpers/TestCaseHelper.php';
require_once __DIR__ . '/helpers/DataLoader.php';

require_once __DIR__.'/utils/utils.api.php';

require_once __DIR__.'/apis/CountriesTest.php';
require_once __DIR__.'/apis/StatesTest.php';
require_once __DIR__.'/apis/CreateUserAccountTest.php';
// Validations
require_once __DIR__.'/validations/CheckDataInsertValidation.php';
require_once __DIR__.'/validations/CheckDataUpdateValidation.php';
require_once __DIR__.'/validations/CheckNoDuplicateValidation.php';
require_once __DIR__.'/validations/CheckNoEmptyValidation.php';
require_once __DIR__.'/validations/CheckNoInsertValidation.php';

// GENERATE DATABASE SCHEMAS for Easy access and Dynamic Query Generation.
$DB_CONN->generateSchemaJSON("./tests/db_schemas");

// Testing Countries API
$countriesTest = new CountriesTest();
$countriesList = $countriesTest->testExecute();

// Testing States API
$statesTest = new StatesTest();
$statesTest->testExecute($countriesList);

// Create User Account
$createUserAccountTest = new CreateUserAccountTest();
$createUserAccountTest->testExecute();

?>