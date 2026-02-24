<?php

$conn = pg_connect("
host=dpg-d6beq00boq4c73fjl5h0-a 
port=5432 
dbname=job_portal_i8bj 
user=job_portal_i8bj_user 
password=RENDER_PASSWORD_HERE
");

if (!$conn) {
    die("Connection failed");
}

?>