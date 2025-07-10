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

// SMS Gateway Configuration (Replace with your actual credentials)
define('SMS_API_KEY', 'your_api_key_here');
define('SMS_SENDER_ID', 'CHAKRA');
define('SMS_ROUTE', 'transactional'); // or 'promotional'

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $phoneNumbers = $_POST['phone_numbers'];
    $message = $_POST['message'];
    $repeat = isset($_POST['repeat_message']) ? 1 : 0;
    $repeatInterval = $_POST['repeat_interval'] ?? 0;
    
    // Validate phone numbers
    $numbers = array_filter(array_map('trim', explode(',', $phoneNumbers)));
    $validNumbers = [];
    
    foreach ($numbers as $number) {
        // Remove any non-numeric characters
        $cleanedNumber = preg_replace('/[^0-9]/', '', $number);
        
        // Validate Indian phone numbers (10 digits starting with 6-9)
        if (preg_match('/^[6-9][0-9]{9}$/', $cleanedNumber)) {
            $validNumbers[] = '91' . $cleanedNumber; // Adding country code
        }
    }
    
    if (empty($validNumbers)) {
        $_SESSION['message_error'] = "Please enter valid Indian phone numbers (10 digits)";
    } elseif (empty($message)) {
        $_SESSION['message_error'] = "Please enter a message";
    } else {
        // Save to database first
        $stmt = $db->prepare("INSERT INTO messages (phone_numbers, message, is_repeating, repeat_interval, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            implode(',', $validNumbers),
            $message,
            $repeat,
            $repeatInterval,
            $_SESSION['admin_id']
        ]);
        
        // Send SMS to each number
        $successCount = 0;
        $failedNumbers = [];
        
        foreach ($validNumbers as $number) {
            $smsSent = sendSMS($number, $message);
            
            if ($smsSent) {
                $successCount++;
            } else {
                $failedNumbers[] = $number;
            }
        }
        
        if (!empty($failedNumbers)) {
            $_SESSION['message_error'] = "Message sent to $successCount numbers. Failed for: " . implode(', ', $failedNumbers);
        } else {
            $_SESSION['message_success'] = "Message sent successfully to $successCount numbers!";
        }
        
        // Redirect to avoid form resubmission
        header("Location: message.php");
        exit;
    }
}

// Function to send SMS via API
function sendSMS($number, $message) {
    // Encode the message for URL
    $encodedMessage = urlencode($message);
    
    // Prepare API URL (example using MSG91)
    $apiUrl = "https://api.msg91.com/api/v2/sendsms?country=91";
    
    // Prepare request data
    $postData = [
        'sender' => SMS_SENDER_ID,
        'route' => SMS_ROUTE,
        'country' => '91',
        'sms' => [
            [
                'message' => $message,
                'to' => [$number]
            ]
        ]
    ];
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "authkey: " . SMS_API_KEY,
        "Content-Type: application/json"
    ]);
    
    // Execute and get response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Check if successful (2xx status code)
    return ($httpCode >= 200 && $httpCode < 300);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Chakra Healing Course</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .number-chip {
            transition: all 0.2s ease;
        }
        .number-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .add-number-btn:hover {
            transform: scale(1.1);
        }
        
        /* Mobile menu styles */
        .mobile-menu-button {
            display: none;
        }
        @media (max-width: 768px) {
            .mobile-menu-button {
                display: block;
            }
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 50;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 40;
            }
            .overlay.open {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Mobile menu button -->
    <div class="md:hidden fixed top-4 left-4 z-30">
        <button id="mobileMenuButton" class="p-2 rounded-md bg-indigo-600 text-white focus:outline-none">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="overlay"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar - Hidden on mobile by default -->
        <div id="sidebar" class="sidebar w-64 bg-indigo-700 text-white fixed h-full shadow-lg">
            <div class="p-5 bg-indigo-800 text-center">
                <h3 class="text-xl font-semibold">Chakra Healing Admin</h3>
            </div>
            <div class="py-4">
                <ul>
                    <li class="px-5 py-3 hover:bg-indigo-600"><a href="admin.php" class="flex items-center"><i class="fas fa-tachometer-alt mr-3"></i> Dashboard</a></li>
                    <li class="px-5 py-3 hover:bg-indigo-600"><a href="registrations.php" class="flex items-center"><i class="fas fa-users mr-3"></i> Registrations</a></li>
                    <li class="px-5 py-3 bg-indigo-600 border-l-4 border-white"><a href="message.php" class="flex items-center"><i class="fas fa-envelope mr-3"></i> Messages</a></li>
                    <li class="px-5 py-3 hover:bg-indigo-600"><a href="logout.php" class="flex items-center"><i class="fas fa-sign-out-alt mr-3"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div id="mainContent" class="main-content md:ml-64 flex-1 p-4 md:p-8">
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded-lg shadow mb-6 mt-8 md:mt-0">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2 md:mb-0">Send SMS Messages</h2>
                <div class="flex items-center">
                    <img src="https://via.placeholder.com/40" alt="Admin" class="w-8 h-8 md:w-10 md:h-10 rounded-full mr-2 md:mr-3">
                    <span class="text-gray-700 text-sm md:text-base"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </div>

            <?php if (isset($_SESSION['message_error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <?php echo $_SESSION['message_error']; unset($_SESSION['message_error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['message_success'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <?php echo $_SESSION['message_success']; unset($_SESSION['message_success']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-4 md:p-6 rounded-lg shadow mb-6">
                <form method="POST" action="message.php" id="smsForm">
                    <div class="mb-4">
                        <label for="phone_numbers" class="block text-gray-700 font-medium mb-2">Phone Numbers</label>
                        <div class="relative">
                            <input type="text" id="phone_numbers" name="phone_numbers" 
                                   class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="9876543210, 8765432109" required>
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="absolute right-3 top-2.5 text-indigo-600 cursor-pointer add-number-btn transition-transform"
                                 onclick="addAnotherNumber()" title="Add another number">
                                <i class="fas fa-plus-circle text-lg"></i>
                            </div>
                        </div>
                        <p class="text-xs md:text-sm text-gray-500 mt-1">Enter 10-digit Indian numbers. Tap <i class="fas fa-plus-circle text-indigo-600"></i> to add more</p>
                        <div class="text-xs md:text-sm text-gray-500 mt-1" id="numberCounter">0 numbers entered</div>
                        
                        <!-- Number chips display -->
                        <div class="flex flex-wrap gap-2 mt-2" id="numberChips"></div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                        <textarea id="message" name="message" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 min-h-[120px] md:min-h-[150px]"
                                  required oninput="updateSMSCounter()"></textarea>
                        <div class="text-xs md:text-sm text-right text-gray-500 mt-1">
                            <span id="charCount">0</span> chars | 
                            <span id="smsCount">0</span> SMS | 
                            <span id="encoding">GSM-7</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="repeat_message" name="repeat_message" class="mr-2 h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <label for="repeat_message" class="text-gray-700">Repeat this message</label>
                    </div>
                    
                    <div class="mb-6 hidden" id="repeat_options">
                        <label for="repeat_interval" class="block text-gray-700 font-medium mb-2">Repeat Interval (days)</label>
                        <input type="number" id="repeat_interval" name="repeat_interval" min="1" value="7"
                               class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <button type="submit" name="send_message" 
                            class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded transition duration-200 flex items-center justify-center">
                        <i class="fas fa-paper-plane mr-2"></i> Send Message
                    </button>
                </form>
            </div>

            <div class="mt-6">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Message Templates</h3>
                
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 md:gap-4">
                    <div class="bg-gray-50 border-l-4 border-indigo-600 p-3 md:p-4 rounded cursor-pointer hover:bg-gray-100 transition duration-200 group"
                         onclick="useTemplate('Welcome to Chakra Healing! We are delighted to have you join our upcoming session on {date}. Please arrive 15 minutes early.')">
                        <div class="font-semibold text-gray-800 mb-1 group-hover:text-indigo-700 text-sm md:text-base">Welcome Message</div>
                        <div class="text-gray-600 text-xs md:text-sm">Welcome to Chakra Healing! We are delighted...</div>
                    </div>
                    
                    <div class="bg-gray-50 border-l-4 border-indigo-600 p-3 md:p-4 rounded cursor-pointer hover:bg-gray-100 transition duration-200 group"
                         onclick="useTemplate('Reminder: Your Chakra Healing session is tomorrow at {time}. Location: {address}. Bring comfortable clothing.')">
                        <div class="font-semibold text-gray-800 mb-1 group-hover:text-indigo-700 text-sm md:text-base">Session Reminder</div>
                        <div class="text-gray-600 text-xs md:text-sm">Reminder: Your Chakra Healing session is tomorrow...</div>
                    </div>
                    
                    <div class="bg-gray-50 border-l-4 border-indigo-600 p-3 md:p-4 rounded cursor-pointer hover:bg-gray-100 transition duration-200 group"
                         onclick="useTemplate('Thank you for attending our Chakra Healing session. We hope you found it beneficial. Your feedback is valuable to us.')">
                        <div class="font-semibold text-gray-800 mb-1 group-hover:text-indigo-700 text-sm md:text-base">Follow-up Message</div>
                        <div class="text-gray-600 text-xs md:text-sm">Thank you for attending our Chakra Healing session...</div>
                    </div>
                    
                    <div class="bg-gray-50 border-l-4 border-indigo-600 p-3 md:p-4 rounded cursor-pointer hover:bg-gray-100 transition duration-200 group"
                         onclick="useTemplate('Dear participant, we have a special offer for our next Chakra Healing workshop. Reply YES for details.')">
                        <div class="font-semibold text-gray-800 mb-1 group-hover:text-indigo-700 text-sm md:text-base">Promotional Message</div>
                        <div class="text-gray-600 text-xs md:text-sm">Dear participant, we have a special offer...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const mainContent = document.getElementById('mainContent');
        
        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
        
        // Show/hide repeat options
        document.getElementById('repeat_message').addEventListener('change', function() {
            const repeatOptions = document.getElementById('repeat_options');
            repeatOptions.classList.toggle('hidden', !this.checked);
        });
        
        // Use template
        function useTemplate(template) {
            document.getElementById('message').value = template;
            updateSMSCounter();
            // Scroll to message field
            document.getElementById('message').scrollIntoView({ behavior: 'smooth' });
        }
        
        // Add another number
        function addAnotherNumber() {
            const input = document.getElementById('phone_numbers');
            const currentValue = input.value.trim();
            
            if (currentValue !== '' && !currentValue.endsWith(',')) {
                input.value = currentValue + ', ';
            }
            
            input.focus();
            updateNumberDisplay();
        }
        
        // Update number display as chips
        function updateNumberDisplay() {
            const input = document.getElementById('phone_numbers');
            const numbers = input.value.split(',')
                .map(num => num.trim())
                .filter(num => num !== '');
            
            // Update counter
            document.getElementById('numberCounter').textContent = `${numbers.length} numbers entered`;
            
            // Create chips
            const chipsContainer = document.getElementById('numberChips');
            chipsContainer.innerHTML = '';
            
            numbers.forEach((num, index) => {
                if (num !== '') {
                    const chip = document.createElement('div');
                    chip.className = 'flex items-center bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs md:text-sm number-chip';
                    chip.innerHTML = `
                        ${num}
                        <button type="button" onclick="removeNumber(${index})" class="ml-1 text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    `;
                    chipsContainer.appendChild(chip);
                }
            });
        }
        
        // Remove number from list
        function removeNumber(index) {
            const input = document.getElementById('phone_numbers');
            const numbers = input.value.split(',')
                .map(num => num.trim())
                .filter(num => num !== '');
            
            numbers.splice(index, 1);
            input.value = numbers.join(', ');
            updateNumberDisplay();
        }
        
        // SMS character counter
        function updateSMSCounter() {
            const message = document.getElementById('message').value;
            const charCount = message.length;
            document.getElementById('charCount').textContent = charCount;
            
            // Determine encoding (GSM-7 or Unicode)
            const gsm7bitChars = "@£$¥èéùìòÇØøÅåΔ_ΦΓΛΩΠΨΣΘΞ^{}\[~]|€ÆæßÉ!\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà ";
            let isGSM7 = true;
            
            for (let i = 0; i < message.length; i++) {
                if (!gsm7bitChars.includes(message[i])) {
                    isGSM7 = false;
                    break;
                }
            }
            
            const encoding = isGSM7 ? 'GSM-7' : 'Unicode';
            document.getElementById('encoding').textContent = encoding;
            
            // Calculate SMS count
            let smsCount;
            if (isGSM7) {
                smsCount = Math.ceil(charCount / 160);
            } else {
                smsCount = Math.ceil(charCount / 70);
            }
            
            document.getElementById('smsCount').textContent = smsCount;
            
            // Update color based on length
            const charCountElement = document.getElementById('charCount');
            if (isGSM7 && charCount > 160 || !isGSM7 && charCount > 70) {
                charCountElement.classList.add('text-orange-500');
                charCountElement.classList.remove('text-gray-500');
            } else {
                charCountElement.classList.remove('text-orange-500');
                charCountElement.classList.add('text-gray-500');
            }
        }
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            updateNumberDisplay();
            updateSMSCounter();
            
            // Add event listener for phone number input
            document.getElementById('phone_numbers').addEventListener('input', function() {
                updateNumberDisplay();
            });
        });
    </script>
</body>
</html>