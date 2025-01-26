<?php
echo "¡Bienvenido a reservas!";


if (isset($_POST['reservar_casa'])){
    echo "<br>Id de la casa = " . $_POST['id'];
}else {
    echo "<br>Id de la habitación = " . $_POST['id'];
}