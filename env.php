<?php
  $variables = [
      'DB_HOST' => 'localhost',
      'DB_USERNAME' => 'postgres',
      'DB_PASSWORD' => 'dcb285'
  ];

  foreach ($variables as $key => $value) {
      putenv("$key=$value");
  }
?>