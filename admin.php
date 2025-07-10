<?php
session_start();

// Redirect to login if not authenticated
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

// Get registration data with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if ($search) {
    $where = "WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$query = "SELECT * FROM registrations $where ORDER BY created_at DESC LIMIT $offset, $perPage";
$stmt = $db->prepare($query);
$stmt->execute($params);
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$totalStmt = $db->prepare("SELECT COUNT(*) FROM registrations $where");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Get stats
$totalRegistrations = $db->query("SELECT COUNT(*) FROM registrations")->fetchColumn();

// New metrics
$todayRegistrations = $db->query("SELECT COUNT(*) FROM registrations WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$weekRegistrations = $db->query("SELECT COUNT(*) FROM registrations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
$topCity = $db->query("SELECT city, COUNT(*) as count FROM registrations GROUP BY city ORDER BY count DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Chakra Healing Course</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        .sidebar {
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 40;
            transition: transform 0.3s ease-in-out;
        }
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            transition: margin 0.3s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .table-header {
                display: none;
            }
            .mobile-card {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                padding: 1rem;
            }
            .desktop-table {
                display: none;
            }
        }
        @media (min-width: 769px) {
            .mobile-card {
                display: none;
            }
            .desktop-table {
                display: table;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Mobile Menu Button -->
    <div class="md:hidden fixed top-0 left-0 p-4 z-50">
        <button id="mobileMenuButton" class="text-indigo-700 focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar bg-indigo-700 text-white">
            <div class="p-5 bg-indigo-800">
                <h3 class="text-xl font-semibold">Chakra Healing Admin</h3>
            </div>
            <div class="mt-5">
                <ul>
                    <li>
                        <a href="admin.php" class="block px-5 py-3 bg-indigo-600">
                            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="registrations.php" class="block px-5 py-3 hover:bg-indigo-600">
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
        <div class="main-content flex-1">
            <div class="p-4 md:p-8">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded-lg shadow mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Dashboard</h2>
                    <div class="flex items-center">
                        <img src="https://via.placeholder.com/40" alt="Admin" class="w-10 h-10 rounded-full mr-3">
                        <span class="text-gray-700"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    </div>
                </div>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="stats-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                    <!-- Total Registrations -->
                    <div class="bg-white p-4 md:p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm md:text-base">Total Registrations</p>
                                <h3 class="text-2xl md:text-3xl font-bold mt-1 md:mt-2"><?php echo number_format($totalRegistrations); ?></h3>
                                <p class="text-xs md:text-sm text-gray-500 mt-1">All time</p>
                            </div>
                            <div class="bg-indigo-100 p-2 md:p-3 rounded-full">
                                <i class="fas fa-users text-indigo-600 text-lg md:text-xl"></i>
                            </div>
                        </div>
                        <a href="registrations.php" class="inline-block text-indigo-600 text-xs md:text-sm mt-2 md:mt-4 hover:underline">View all</a>
                    </div>

                    <!-- Today's Registrations -->
                    <div class="bg-white p-4 md:p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm md:text-base">Today's Registrations</p>
                                <h3 class="text-2xl md:text-3xl font-bold mt-1 md:mt-2"><?php echo number_format($todayRegistrations); ?></h3>
                                <p class="text-xs md:text-sm text-gray-500 mt-1"><?php echo date('d M Y'); ?></p>
                            </div>
                            <div class="bg-blue-100 p-2 md:p-3 rounded-full">
                                <i class="fas fa-calendar-day text-blue-600 text-lg md:text-xl"></i>
                            </div>
                        </div>
                        <a href="registrations.php?date=today" class="inline-block text-indigo-600 text-xs md:text-sm mt-2 md:mt-4 hover:underline">View today</a>
                    </div>

                    <!-- This Week's Registrations -->
                    <div class="bg-white p-4 md:p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm md:text-base">This Week</p>
                                <h3 class="text-2xl md:text-3xl font-bold mt-1 md:mt-2"><?php echo number_format($weekRegistrations); ?></h3>
                                <p class="text-xs md:text-sm text-gray-500 mt-1">Last 7 days</p>
                            </div>
                            <div class="bg-green-100 p-2 md:p-3 rounded-full">
                                <i class="fas fa-calendar-week text-green-600 text-lg md:text-xl"></i>
                            </div>
                        </div>
                        <a href="registrations.php?date=week" class="inline-block text-indigo-600 text-xs md:text-sm mt-2 md:mt-4 hover:underline">View this week</a>
                    </div>

                    <!-- Top City -->
                    <div class="bg-white p-4 md:p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm md:text-base">Top City</p>
                                <h3 class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">
                                    <?php echo $topCity ? htmlspecialchars($topCity['city']) : 'N/A'; ?>
                                </h3>
                                <p class="text-xs md:text-sm text-gray-500 mt-1">
                                    <?php echo $topCity ? $topCity['count'] . ' participants' : 'No data'; ?>
                                </p>
                            </div>
                            <div class="bg-purple-100 p-2 md:p-3 rounded-full">
                                <i class="fas fa-map-marker-alt text-purple-600 text-lg md:text-xl"></i>
                            </div>
                        </div>
                        <a href="registrations.php?city=<?php echo $topCity ? urlencode($topCity['city']) : ''; ?>" class="inline-block text-indigo-600 text-xs md:text-sm mt-2 md:mt-4 hover:underline">View by city</a>
                    </div>
                </div>

                <!-- Recent Registrations -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <h3 class="text-lg font-medium text-gray-900 mb-2 md:mb-0">Recent Registrations</h3>
                            <form method="GET" action="admin.php" class="flex">
                                <input type="text" name="search" placeholder="Search..." 
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       class="w-full md:w-64 px-3 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700 text-sm">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile Cards View -->
                    <div class="md:hidden p-4">
                        <?php if (empty($registrations)): ?>
                            <div class="text-center text-gray-500 py-4">
                                No registrations found
                            </div>
                        <?php else: ?>
                            <?php foreach ($registrations as $reg): ?>
                            <div class="mobile-card">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-medium">#<?php echo $reg['id']; ?></span>
                                </div>
                                <div class="mb-1">
                                    <span class="font-medium">Name:</span> <?php echo htmlspecialchars($reg['name']); ?>
                                </div>
                                <div class="mb-1">
                                    <span class="font-medium">Email:</span> <?php echo htmlspecialchars($reg['email']); ?>
                                </div>
                                <div class="mb-1">
                                    <span class="font-medium">Phone:</span> <?php echo htmlspecialchars($reg['phone']); ?>
                                </div>
                                <div class="mb-1">
                                    <span class="font-medium">City:</span> <?php echo htmlspecialchars($reg['city']); ?>
                                </div>
                                <div class="text-sm text-gray-500 mt-2">
                                    <?php echo date('d M Y', strtotime($reg['created_at'])); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="desktop-table min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($registrations)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No registrations found
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($registrations as $reg): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?php echo $reg['id']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($reg['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($reg['email']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($reg['phone']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($reg['city']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('d M Y', strtotime($reg['created_at'])); ?></td>
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
                                <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>"
                                   class="relative inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>"
                                   class="ml-3 relative inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
                                        <a href="?page=1&search=<?php echo urlencode($search); ?>"
                                           class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">First</span>
                                            <i class="fas fa-angle-double-left"></i>
                                        </a>
                                        <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>"
                                           class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-angle-left"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php 
                                    $start = max(1, $page - 2);
                                    $end = min($totalPages, $page + 2);
                                    
                                    for ($i = $start; $i <= $end; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"
                                           class="<?php echo $i === $page ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>"
                                           class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-angle-right"></i>
                                        </a>
                                        <a href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>"
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
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuButton').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.getElementById('mobileMenuButton');
            
            if (window.innerWidth < 768 && 
                !sidebar.contains(event.target) && 
                !menuButton.contains(event.target)) {
                sidebar.classList.remove('open');
            }
        });
    </script>
</body>
</html>