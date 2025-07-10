<?php
// Database configuration and form processing (submit.php)
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');

function getDBConnection() {
    $host = 'localhost';
    $dbname = 'skmishrafrom';
    $username = 'root';
    $password = '';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        return null;
    }
}

$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
    'field_errors' => []
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form data
    $name  = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? '');
    $city  = htmlspecialchars(trim($_POST['city'] ?? ''));
    $job   = htmlspecialchars(trim($_POST['job'] ?? ''));

    // Validation
    if (empty($name)) {
        $response['errors'][] = "Name is required";
        $response['field_errors']['name'] = "Please enter your full name";
    } elseif (!preg_match('/^[a-zA-Z\s\.\-]{3,}$/', $name)) {
        $response['errors'][] = "Name must be at least 3 characters";
        $response['field_errors']['name'] = "Only letters, spaces, dots and hyphens allowed";
    }

    if (empty($email)) {
        $response['errors'][] = "Email is required";
        $response['field_errors']['email'] = "Please enter your email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Invalid email format";
        $response['field_errors']['email'] = "Please enter a valid email";
    }

    if (empty($phone)) {
        $response['errors'][] = "Phone number is required";
        $response['field_errors']['phone'] = "Please enter your phone number";
    } elseif (strlen($phone) !== 10) {
        $response['errors'][] = "Phone number must be 10 digits";
        $response['field_errors']['phone'] = "Must be 10 digits";
    }

    if (empty($city)) {
        $response['errors'][] = "City is required";
        $response['field_errors']['city'] = "Please enter your city";
    }

    if (!empty($response['errors'])) {
        $response['message'] = "Please fix the errors below";
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    try {
        // Save to CSV file (backup)
        $file = 'registrations.csv';
        if (!file_exists($file)) {
            $header = ['Name', 'Email', 'Phone', 'City', 'Job Title', 'Submitted At'];
            file_put_contents($file, implode(',', $header) . PHP_EOL);
        }
        $data = [$name, $email, $phone, $city, $job, date("Y-m-d H:i:s")];
        file_put_contents($file, implode(',', $data) . PHP_EOL, FILE_APPEND);

        // Save to database
        $conn = getDBConnection();
        if (!$conn) {
            throw new Exception("Could not connect to database");
        }
        
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT id FROM registrations WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->fetch()) {
            $response['message'] = "This email is already registered";
            $response['field_errors']['email'] = "Email already exists";
            http_response_code(409);
            echo json_encode($response);
            exit;
        }
        
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO registrations 
            (name, email, phone, city, job_title, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())");
        
        if ($stmt->execute([$name, $email, $phone, $city, $job])) {
            // On successful submission, redirect to success page
            header("Location: success.php?name=".urlencode($name)."&email=".urlencode($email)."&phone=".urlencode($phone)."&city=".urlencode($city)."&job=".urlencode($job));
            exit;
        } else {
            $response['message'] = "Database error occurred";
            http_response_code(500);
        }
        
    } catch (PDOException $e) {
        $response['message'] = "Database error: " . $e->getMessage();
        http_response_code(500);
    } catch (Exception $e) {
        $response['message'] = "An error occurred: " . $e->getMessage();
        http_response_code(500);
    }
} else {
    $response['message'] = "Invalid request method";
    http_response_code(405);
}

echo json_encode($response);
?>