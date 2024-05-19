<?php

$connection = include __DIR__."/connection.php";
$query = "SELECT * FROM videos ORDER BY RAND() LIMIT 20";
$result_video = mysqli_query($connection, $query);


for($i=0; $i<mysqli_num_rows($result_video); $i++){
    $video[$i] = mysqli_fetch_assoc($result_video);
} 

//echo json_encode($video);
return $video;
mysqli_close($connection);
?>