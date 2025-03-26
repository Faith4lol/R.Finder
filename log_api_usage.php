<?php
session_start();
include 'db.php'; // Include your database connection file

// Debugging: Log incoming request data
file_put_contents('debug.log', "Request received: " . file_get_contents('php://input') . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $endpoint = $data['endpoint'];
    $api_key = '7c3b0e00c7a84c8d9d5508f9e7eba407'; // Replace with your Spoonacular API key

    // Debugging: Log the endpoint and API key
    file_put_contents('debug.log', "Endpoint: $endpoint, API Key: $api_key\n", FILE_APPEND);

    // Insert the API request into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO api_usage (endpoint, api_key) VALUES (?, ?)");
        $stmt->execute([$endpoint, $api_key]);

        // Debugging: Log success
        file_put_contents('debug.log', "API usage logged successfully\n", FILE_APPEND);

        echo json_encode(['success' => true]);
    } catch (\PDOException $e) {
        // Debugging: Log the error
        file_put_contents('debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);

        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    // Debugging: Log invalid request method
    file_put_contents('debug.log', "Invalid request method\n", FILE_APPEND);
}
?>