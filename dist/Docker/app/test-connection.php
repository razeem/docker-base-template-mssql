<?php
$serverName = "project_name_database,1433";
$connectionOptions = [
  "Database" => getenv('DRUPAL_DEFAULT_DB_NAME'),
  "Uid" => getenv('DRUPAL_DEFAULT_DB_USERNAME'),
  "PWD" => getenv('DRUPAL_DEFAULT_DB_PASSWORD'),
  "TrustServerCertificate" => TRUE,
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
  echo "Connected successfully!\n";
  $result = sqlsrv_query($conn, "SELECT @@VERSION AS SQLVersion");
  while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    echo $row['SQLVersion'] . "\n";
  }
  sqlsrv_close($conn);
}
else {
  echo "Connection failed.\n";
  die(print_r(sqlsrv_errors(), TRUE));
}
