<?php
require 'c:/xampp/htdocs/webpupuk/config/database.php';
$res = $conn->query("DESCRIBE petani");
if ($res) {
    while($r=$res->fetch_assoc()) echo $r['Field']." ";
} else {
    echo $conn->error;
}
