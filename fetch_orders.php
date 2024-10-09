<?php
require 'database.php';

$database = new Database();
$db = $database->getConnection();

$stmt = $db->query("SELECT id, client_name, status, driver_id FROM orders");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($orders as $order) {
    echo "<tr>
            <td>" . htmlspecialchars($order['id']) . "</td>
            <td>" . htmlspecialchars($order['client_name']) . "</td>
            <td>" . htmlspecialchars($order['status']) . "</td>
            <td>" . htmlspecialchars($order['driver_id']) . "</td>
        </tr>";
}
