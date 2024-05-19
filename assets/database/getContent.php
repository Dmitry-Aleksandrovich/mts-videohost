<?php

$connection = include __DIR__."/connection.php";
$query = "SELECT * FROM banword";
$result_banword = mysqli_query($connection, $query);

for($i=0; $i<mysqli_num_rows($result_banword); $i++){
    $banword[$i] = mysqli_fetch_assoc($result_banword);
} 
//echo json_encode($banword);
return $banword;
mysqli_close($connection);