<?php
$connection = include __DIR__.'/connection.php';
$query = 'SELECT * FROM genre WHERE используется=1';
$result_genre = mysqli_query($connection, $query);

for($i=0; $i<mysqli_num_rows($result_genre); $i++){
    $data[$i] = mysqli_fetch_assoc($result_genre);
}

return $data;
mysqli_close($connection);
?>