<?php
session_start();

// Authentication check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Database connection
try {
    $db = new PDO('mysql:host=localhost;dbname=skmishrafrom', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle actions (delete, export, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        // Delete registration
        $stmt = $db->prepare("DELETE FROM registrations WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $_SESSION['message'] = "Registration deleted successfully";
        header("Location: registrations.php");
        exit;
    }
    
    if (isset($_POST['export'])) {
        // Export to CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="registrations_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header row
        $columns = $db->query("SHOW COLUMNS FROM registrations")->fetchAll(PDO::FETCH_COLUMN);
        fputcsv($output, $columns);
        
        // Data rows
        $stmt = $db->query("SELECT * FROM registrations");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';
$city_filter = isset($_GET['city_filter']) ? $_GET['city_filter'] : '';

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

if ($date_filter) {
    switch ($date_filter) {
        case 'today':
            $where[] = "DATE(created_at) = CURDATE()";
            break;
        case 'week':
            $where[] = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $where[] = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }
}

if ($city_filter) {
    $where[] = "city = ?";
    $params[] = $city_filter;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get unique cities for filter dropdown
$cities = $db->query("SELECT DISTINCT city FROM registrations WHERE city IS NOT NULL AND city != '' ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get registrations
$stmt = $db->prepare("SELECT * FROM registrations $whereClause ORDER BY created_at DESC LIMIT $offset, $perPage");
$stmt->execute($params);
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$totalStmt = $db->prepare("SELECT COUNT(*) FROM registrations $whereClause");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();
$totalPages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrations Management | Chakra Healing Course</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-hidden {
                display: none;
            }
            .mobile-menu-btn {
                display: block;
            }
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
            .sidebar {
                display: block !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn fixed top-4 left-4 z-50 bg-indigo-600 text-white p-2 rounded-lg md:hidden">
        <i class="fas fa-bars"></i>
    </button>

    <div class="flex flex-col md:flex-row h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 bg-indigo-700 text-white h-full fixed hidden md:block">
            <div class="p-5 bg-indigo-800">
                <h3 class="text-xl font-semibold">Chakra Healing Admin</h3>
            </div>
            <div class="mt-5">
                <ul>
                    <li>
                        <a href="admin.php" class="block px-5 py-3 hover:bg-indigo-600">
                            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="registrations.php" class="block px-5 py-3 bg-indigo-600">
                            <i class="fas fa-users mr-3"></i> Registrations
                        </a>
                    </li>
                    <li>
                        <a href="messages.php" class="block px-5 py-3 hover:bg-indigo-600">
                            <i class="fas fa-envelope mr-3"></i> Messages
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="block px-5 py-3 hover:bg-indigo-600">
                            <i class="fas fa-sign-out-alt mr-3"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content md:ml-64 flex-1 p-4 md:p-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded-lg shadow mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Registrations Management</h2>
                <div class="flex items-center">
                    <img src="https://via.placeholder.com/40" alt="Admin" class="w-10 h-10 rounded-full mr-3">
                    <span class="text-gray-700"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                </div>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <form method="GET" class="space-y-4 md:space-y-0 md:grid md:grid-cols-4 md:gap-4">
                    <div class="md:col-span-2">
                        <input type="text" name="search" placeholder="Search by name, email or phone" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <select name="date_filter" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Dates</option>
                            <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="week" <?php echo $date_filter === 'week' ? 'selected' : ''; ?>>Last 7 Days</option>
                            <option value="month" <?php echo $date_filter === 'month' ? 'selected' : ''; ?>>Last 30 Days</option>
                        </select>
                    </div>
                    
                    <div>
                        <select name="city_filter" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Cities</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>" <?php echo $city_filter === $city ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="flex space-x-2 md:col-span-4">
                        <button type="submit" class="w-1/2 md:w-auto px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Filter
                        </button>
                        <button type="reset" onclick="window.location.href='registrations.php'" 
                                class="w-1/2 md:w-auto px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Registrations Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 mobile-hidden">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider mobile-hidden">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider mobile-hidden">City</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider mobile-hidden">Job Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($registrations)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        No registrations found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registrations as $reg): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $reg['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($reg['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($reg['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 mobile-hidden"><?php echo htmlspecialchars($reg['phone']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 mobile-hidden"><?php echo htmlspecialchars($reg['city']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 mobile-hidden"><?php echo htmlspecialchars($reg['job_title']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('d M Y', strtotime($reg['created_at'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="registrations.php" class="inline">
                                            <input type="hidden" name="delete_id" value="<?php echo $reg['id']; ?>">
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to delete this registration?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&date_filter=<?php echo urlencode($date_filter); ?>&city_filter=<?php echo urlencode($city_filter); ?>"
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&date_filter=<?php echo urlencode($date_filter); ?>&city_filter=<?php echo urlencode($city_filter); ?>"
                               class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?php echo $offset+1; ?></span> to <span class="font-medium"><?php echo min($offset+$perPage, $total); ?></span> of <span class="font-medium"><?php echo $total; ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=1&search=<?php echo urlencode($search); ?>&date_filter=<?php echo urlencode($date_filter); ?>&city_filter=<?php echo urlencode($city_filter); ?>"
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">First</span>
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                    <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&date_filter=<?php echo urlencode($date_filter); ?>&city_filter=<?php echo urlencode($city_filter); ?>"
                                       class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php 
                                $start = max(1, $page - 2);
                                $end = min($totalPages, $page + 2);
                                
                                for ($i = $start; $i <= $end; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&date_filter=<?php echo urlencode($date_filter); ?>&city_filter=<?php echo urlencode($city_filter); ?>"
                                       class="<?php echo $i === $page ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&date_filter=<?php echo urlencode($date_filter); ?>&city_filter=<?php echo urlencode($city_filter); ?>"
                                       class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                    <a href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&date_filter=<?php echo urlencode($date_filter); ?>&city_filter=<?php echo urlencode($city_filter); ?>"
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Last</span>
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (!sidebar.contains(event.target) && !menuBtn.contains(event.target) && window.innerWidth < 768) {
                sidebar.classList.add('hidden');
            }
        });
    </script>
</body>
</html>