<?php
    $konek = mysqli_connect("localhost", "xigagiwx_magrowkit", "Natuna.1234567890", "xigagiwx_db_multisensor");
    $sql = mysqli_query($konek, "select * from tb_lalat order by id desc");
    $data = mysqli_fetch_array($sql);
    $lightIntensity = $data['lightIntensity'];
    if($lightIntensity == "") $lightIntensity = 0;
    echo $lightIntensity;






?>
