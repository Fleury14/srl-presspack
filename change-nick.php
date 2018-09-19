<?php

$current_user = wp_get_current_user();

function changeName() {
    if ( $_POST['nick'] !== null) {
        wp_update_user( array(
            'display_name' => $_POST['nick']
        ) );
    }
}

if (isset($_POST['sumbit'])) {
    changeName();
}
header('Location: /user-info');

?>