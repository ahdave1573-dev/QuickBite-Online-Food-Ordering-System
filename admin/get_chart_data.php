<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}
$sql_weekly_revenue = "SELECT 
                           DATE(created_at) as sale_date, 
                           SUM(grand_total) as total 
                       FROM orders 
                       WHERE 
                           created_at >= CURDATE() - INTERVAL 6 DAY 
                           AND order_status IN ('Processing', 'Completed')
                       GROUP BY 
                           DATE(created_at) 
                       ORDER BY 
                           sale_date ASC";

$result = $conn->query($sql_weekly_revenue);

// Check if query was successful
if ($result === false) {
    echo json_encode(['error' => 'SQL query failed: ' . $conn->error]);
    $conn->close();
    exit();
}

$labels = [];
$data = [];
$sales_by_date = [];

while ($row = $result->fetch_assoc()) {
    $sales_by_date[$row['sale_date']] = $row['total'];
}

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('D, j M', strtotime($date));
    $data[] = (float)($sales_by_date[$date] ?? 0);
}

echo json_encode(['labels' => $labels, 'data' => $data]);

$conn->close();
?>