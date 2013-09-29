<?php

function executeJoinQuery($db, $sql){
    $result = mysqli_query($db, $sql);
    $array = array();
    while($row = mysqli_fetch_assoc($result)) {
      $array[] = $row;
    }
    return $array;
}

?>
