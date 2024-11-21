<?php

function login($username, $password, $conn) {
    $stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    //memastikan data yg diambil ada atau tidak
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return ['user_id' => $user['id'], 'role' => $user['role']];
    }

    //tidak ada data yg diambil

    return ['status' => 'error'];
}


?>