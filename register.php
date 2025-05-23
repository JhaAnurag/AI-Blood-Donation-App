<?php
session_start();
include_once 'includes/db.php';
include_once 'includes/auth.php';
include_once 'mail/send.php'; // Include the mail function

// If user is already logged in, redirect to dashboard
if (is_donor_logged_in()) {
    header("Location: dashboard/donor.php");
    exit;
}

$errors = [];
$name = $email = $phone = $age = $blood_group = $city = $state = '';

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid form submission.";
    }
    
    // Validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $blood_group = $_POST['blood_group'] ?? '';
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^\d{10}$/', $phone)) { // Basic validation for 10 digits
        $errors[] = "Invalid phone number format. Please enter 10 digits.";
    }
    
    if (empty($age) || $age < 18) {
        $errors[] = "Age must be 18 or above.";
    } elseif ($age > 100) { // Optional: Add a reasonable upper limit for age
        $errors[] = "Please enter a valid age.";
    }
    
    if (empty($blood_group)) {
        $errors[] = "Blood group is required.";
    }
    
    if (empty($city)) {
        $errors[] = "City is required.";
    }
    
    if (empty($state)) {
        $errors[] = "State is required.";
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Email already registered. Please login instead.";
    }
    $stmt->close();
    
    // If no errors, register the user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, age, blood_group, city, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisss", $name, $email, $hashed_password, $phone, $age, $blood_group, $city, $state);
        
        if ($stmt->execute()) {
            $new_user_id = $stmt->insert_id;
            $_SESSION['donor_id'] = $new_user_id;
            $_SESSION['donor_name'] = $name;
            $_SESSION['donor_email'] = $email;
            $_SESSION['success'] = "Registration successful! Welcome to the Blood Donation System.";
            
            // Send welcome email
            $subject = "Welcome to the Blood Donation System!";
            $message = "Dear " . htmlspecialchars($name) . ",<br><br>Thank you for registering as a blood donor. Your commitment helps save lives!<br><br>You can now log in to your dashboard to book appointments and manage your profile.<br><br>Best regards,<br>The Blood Donation Team";
            
            // Check if email sending was successful
            if (!send_email($email, $subject, $message)) {
                // Log the error or store a temporary message for debugging
                // Note: The user is redirected immediately after, so they won't see this directly
                // You might want to log this to a file instead for better debugging
                error_log("Registration email failed to send to: " . $email); 
                // Optionally set a session variable if you want to try and display a message later,
                // though it might be cleared or overwritten by the redirect target page.
                // $_SESSION['warning'] = "Registration successful, but the welcome email could not be sent.";
            }
            
            header("Location: dashboard/donor.php");
            exit;
        } else {
            $errors[] = "Registration failed. Please try again later.";
        }
        $stmt->close();
    }
}

// Include header AFTER all potential redirects
include_once 'includes/header.php';
?>

<div class="bg-gray-100 dark:bg-[#121212] py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-white dark:bg-[#252525] p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center text-primary-dark dark:text-primary-light mb-6">Register as a Blood Donor</h2>
            
            <?php 
            // Display any errors
            if (!empty($errors)) {
                echo '<div class="bg-red-100 dark:bg-red-900 border border-danger dark:border-red-700 text-danger dark:text-red-200 px-4 py-3 rounded relative mb-4" role="alert">';
                echo '<ul class="list-disc list-inside">';
                foreach ($errors as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="name">Full Name</label>
                    <input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                           type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="email">Email Address</label>
                    <input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                           type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="password">Password</label>
                    <input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                           type="password" name="password" id="password" required>
                    <div class="mt-1" id="password-strength-container">
                        <div class="h-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full">
                            <div id="password-strength-meter" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="password-strength-text" class="text-xs mt-1 text-gray-600 dark:text-gray-300"></p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="confirm_password">Confirm Password</label>
                    <input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                           type="password" name="confirm_password" id="confirm_password" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="phone">Phone Number</label>
                    <input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                           type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="age">Age</label>
                    <input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                           type="number" name="age" id="age" min="18" value="<?php echo htmlspecialchars($age); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="blood_group">Blood Group</label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                            name="blood_group" id="blood_group" required>
                        <option value="" disabled <?php echo empty($blood_group) ? 'selected' : ''; ?>>Select Blood Group</option>
                        <option value="A+" <?php echo $blood_group === 'A+' ? 'selected' : ''; ?>>A+</option>
                        <option value="A-" <?php echo $blood_group === 'A-' ? 'selected' : ''; ?>>A-</option>
                        <option value="B+" <?php echo $blood_group === 'B+' ? 'selected' : ''; ?>>B+</option>
                        <option value="B-" <?php echo $blood_group === 'B-' ? 'selected' : ''; ?>>B-</option>
                        <option value="AB+" <?php echo $blood_group === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                        <option value="AB-" <?php echo $blood_group === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                        <option value="O+" <?php echo $blood_group === 'O+' ? 'selected' : ''; ?>>O+</option>
                        <option value="O-" <?php echo $blood_group === 'O-' ? 'selected' : ''; ?>>O-</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="city">City</label>
                    <input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                           type="text" name="city" id="city" value="<?php echo htmlspecialchars($city); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2" for="state">State</label>
                    <input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light dark:bg-gray-800 dark:text-white" 
                           type="text" name="state" id="state" value="<?php echo htmlspecialchars($state); ?>" required>
                </div>
                
                <div class="mb-6">
                    <button class="w-full bg-primary-dark hover:bg-primary-light text-white py-2 px-4 rounded-md dark:bg-primary-dark dark:hover:bg-primary-light focus:outline-none focus:ring-2 focus:ring-primary-light focus:ring-opacity-50" 
                            type="submit">Register</button>
                </div>
                
                <p class="text-center text-gray-600 dark:text-gray-300">
                    Already have an account? <a href="login.php" class="text-accent-dark dark:text-accent-light hover:underline">Login here</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.getElementById('password-strength-meter');
    const strengthText = document.getElementById('password-strength-text');
    
    // Colors for different strength levels with our new color theme
    const strengthColors = {
        1: 'bg-danger',   // Very weak - red
        2: 'bg-accent-dark', // Weak - deep coral
        3: 'bg-accent-light', // Medium - coral orange
        4: 'bg-primary-light',   // Strong - bright indigo
        5: 'bg-success'   // Very strong - green
    };
    
    // Password strength levels text descriptions
    const strengthLevels = {
        0: 'Enter a password',
        1: 'Very weak - Use 8+ characters with letters, numbers & symbols',
        2: 'Weak - Add more character types',
        3: 'Medium - Add more complexity',
        4: 'Strong - Good password!',
        5: 'Very strong - Excellent password!'
    };
    
    // Listen for password input changes
    passwordInput.addEventListener('input', updateStrength);
    
    function updateStrength() {
        const password = passwordInput.value;
        let strength = calculatePasswordStrength(password);
        
        // Update the strength meter width
        strengthMeter.style.width = strength * 20 + '%'; // 0-100% in 20% increments
        
        // Remove any previous color classes
        Object.values(strengthColors).forEach(color => {
            strengthMeter.classList.remove(color);
        });
        
        // Add current strength color class
        if (password.length > 0) {
            strengthMeter.classList.add(strengthColors[strength] || 'bg-gray-300');
        } else {
            strengthMeter.classList.add('bg-gray-300');
        }
        
        // Update strength text
        strengthText.textContent = strengthLevels[strength] || '';
    }
    
    function calculatePasswordStrength(password) {
        if (!password) return 0;
        
        let score = 0;
        
        // Length check
        if (password.length > 0) score += 1;
        if (password.length >= 8) score += 1;
        if (password.length >= 12) score += 1;
        
        // Complexity checks
        const hasLower = /[a-z]/.test(password);
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        
        // Add 1 point if multiple character types are used
        if ((hasLower && hasUpper) || 
            (hasLower && hasNumber) || 
            (hasLower && hasSpecial) || 
            (hasUpper && hasNumber) || 
            (hasUpper && hasSpecial) || 
            (hasNumber && hasSpecial)) {
            score += 1;
        }
        
        // Add 1 point for having 3 or more character types
        const charTypesCount = [hasLower, hasUpper, hasNumber, hasSpecial].filter(Boolean).length;
        if (charTypesCount >= 3) {
            score += 1;
        }
        
        // Ensure max score is 5
        return Math.min(score, 5);
    }
    
    // Initialize strength display
    updateStrength();
});
</script>

<?php include_once 'includes/footer.php'; ?>