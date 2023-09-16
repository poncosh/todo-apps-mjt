<?php

namespace Database;

function connect()
{
  $conn = mysqli_connect(
    getenv("DB_HOST_NAME"),
    getenv("DB_USERNAME"),
    getenv("DB_PASSWORD"),
    getenv("DB_NAME")
  );
  return $conn;
}
