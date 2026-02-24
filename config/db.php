<?php

$conn = pg_connect(
"host=dpg-d6beq00boq4c73fjl5h0-a.oregon-postgres.render.com 
port=5432 
dbname=job_portal_i8bj 
user=job_portal_she8_user 
password=MCnBolTmE4a4z7qMIMzNdePsDuoFmQ56"
);

if (!$conn) {
    die("Database connection failed.");
}

?>