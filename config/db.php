<?php

$database_url = getenv("DATABASE_URL");

if (!$database_url) {
    // जर environment variable नसेल तर direct URL वापर
    $database_url = "postgresql://USERNAME:PASSWORD@dpg-d6beq00boq4c73fjl5h0-a/job_portal_i8bj";
}

$conn = pg_connect($database_url);

if (!$conn) {
    die("Database connection failed.");
}

?>