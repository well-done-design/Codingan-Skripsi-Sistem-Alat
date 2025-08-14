<?php
    $konek = mysqli_connect("localhost", "xigagiwx_magrowkit", "Natuna.1234567890", "xigagiwx_db_multisensor");
    $sql = mysqli_query($konek, "select * from tb_larva order by id desc");
    $data = mysqli_fetch_array($sql);
    $temperature = $data['temperature'];
    if($temperature == "") $temperature = 0;
    echo $temperature;






?>
