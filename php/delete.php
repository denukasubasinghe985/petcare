<?php

require_once 'db.php';

require_post();

$user_id = require_login();

$id = (int) ($_POST['id'] ?? 0);

$sql = "DELETE FROM appointments
        WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $id, $user_id);

$stmt->execute();

if ($stmt->affected_rows === 0) {

    respond(false, 'Appointment not found', [], 404);
}

respond(true, 'Appointment deleted successfully');