# IdeaSync - Critical Missing Features: Implementation Guide

## Phase 1: Email Verification System

### Step 1: Database Migration
```sql
-- Add to DATABASE_SCHEMA.sql

CREATE TABLE email_verifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
);

ALTER TABLE users ADD COLUMN email_verified BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL;
```

### Step 2: Add to Existing Models

**File**: `/src/models/User.php` - Add new methods:
```php
/**
 * Create email verification token
 */
public function createVerificationToken($user_id) {
    $token = bin2hex(random_bytes(32));
    $token_hash = hash('sha256', $token);
    $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $query = "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("iss", $user_id, $token_hash, $expires_at);
    
    if ($stmt->execute()) {
        return ['success' => true, 'token' => $token];
    }
    return ['success' => false];
}

/**
 * Verify email with token
 */
public function verifyEmail($token) {
    $token_hash = hash('sha256', $token);
    
    // Check if token exists and is valid
    $query = "SELECT user_id FROM email_verifications 
              WHERE token = ? AND expires_at > NOW() AND verified_at IS NULL";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'error' => 'Invalid or expired token'];
    }
    
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    
    // Mark email as verified
    $update_query = "UPDATE email_verifications SET verified_at = NOW() WHERE token = ?";
    $update_stmt = $this->conn->prepare($update_query);
    $update_stmt->bind_param("s", $token_hash);
    $update_stmt->execute();
    
    // Update user record
    $user_update = "UPDATE users SET email_verified = TRUE, email_verified_at = NOW() WHERE id = ?";
    $user_stmt = $this->conn->prepare($user_update);
    $user_stmt->bind_param("i", $user_id);
    
    if ($user_stmt->execute()) {
        return ['success' => true, 'user_id' => $user_id];
    }
    return ['success' => false, 'error' => 'Verification failed'];
}

/**
 * Check if email is verified
 */
public function isEmailVerified($user_id) {
    $query = "SELECT email_verified FROM users WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return isset($result['email_verified']) && $result['email_verified'];
}

/**
 * Resend verification email
 */
public function resendVerificationEmail($email) {
    // Get user
    $query = "SELECT id, email FROM users WHERE email = ? AND email_verified = FALSE";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        return ['success' => false, 'error' => 'User not found or already verified'];
    }
    
    // Create new token
    return $this->createVerificationToken($user['id']);
}
```

### Step 3: Modify Auth Controller

**File**: `/src/controllers/auth.php` - Update registration method:
```php
public function register() {
    // ... existing validation code ...
    
    $result = $this->user->register($roll_number, $name, $email, $password, $branch, $year);
    
    if ($result['success']) {
        // Create verification token
        $token_result = $this->user->createVerificationToken($result['user_id']);
        
        // Send verification email
        $emailService = new EmailService();
        $verification_link = BASE_URL . "/?page=verify-email&token=" . $token_result['token'];
        $emailService->sendVerificationEmail($email, $name, $verification_link);
        
        $_SESSION['pending_verification'] = $email;
        $_SESSION['message'] = 'Verification email sent. Please check your inbox.';
        redirect(BASE_URL . '/?page=verify-email-pending');
    }
    
    return $result;
}
```

**Add new action handler:**
```php
// In auth.php or as separate handler
if ($_GET['action'] === 'verify-email') {
    $token = $_GET['token'] ?? '';
    if (empty($token)) {
        return ['success' => false, 'error' => 'No token provided'];
    }
    
    $result = $this->user->verifyEmail($token);
    if ($result['success']) {
        $_SESSION['message'] = 'Email verified successfully! You can now login.';
        redirect(BASE_URL . '/?page=login');
    } else {
        $_SESSION['error'] = $result['error'];
        redirect(BASE_URL . '/?page=verify-email-failed');
    }
}
```

### Step 4: Create Views

**File**: `/src/views/verify-email-pending.php`:
```php
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-4">Verify Your Email</h1>
        
        <p class="text-gray-600 mb-4">
            We've sent a verification link to <strong><?php echo sanitize($_SESSION['pending_verification']); ?></strong>
        </p>
        
        <p class="text-gray-600 mb-6">
            Click the link in your email to verify your account. The link will expire in 24 hours.
        </p>
        
        <div class="bg-blue-50 p-4 rounded-lg mb-6">
            <p class="text-sm text-blue-800">
                <strong>Didn't receive the email?</strong><br>
                Check your spam folder or <a href="?page=resend-verification" class="text-blue-600 hover:underline">resend verification email</a>
            </p>
        </div>
        
        <p class="text-center text-gray-600">
            Already verified? <a href="?page=login" class="text-blue-600 hover:underline">Go to login</a>
        </p>
    </div>
</div>
```

**File**: `/src/views/verify-email-failed.php`:
```php
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-4 text-red-600">Verification Failed</h1>
        
        <p class="text-gray-600 mb-4">
            <?php echo sanitize($_SESSION['error'] ?? 'The verification link is invalid or has expired.'); ?>
        </p>
        
        <a href="?page=resend-verification" class="block w-full bg-blue-600 text-white py-2 px-4 rounded text-center hover:bg-blue-700 mb-3">
            Request New Verification Email
        </a>
        
        <a href="?page=login" class="block w-full bg-gray-200 text-gray-800 py-2 px-4 rounded text-center hover:bg-gray-300">
            Back to Login
        </a>
    </div>
</div>
```

### Step 5: Update Router

**File**: `/public/index.php` - Add routes:
```php
$routes = [
    // ... existing routes ...
    'verify-email' => 'src/views/verify-email-pending.php',
    'verify-email-pending' => 'src/views/verify-email-pending.php',
    'verify-email-failed' => 'src/views/verify-email-failed.php',
    'resend-verification' => 'src/views/resend-verification.php',
];
```

---

## Phase 2: Password Reset System

### Step 1: Database Migration
```sql
CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user (user_id),
    INDEX idx_expires (expires_at)
);
```

### Step 2: Add to User Model

```php
/**
 * Create password reset token
 */
public function createPasswordResetToken($email) {
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        return ['success' => false, 'error' => 'User not found'];
    }
    
    $token = bin2hex(random_bytes(32));
    $token_hash = hash('sha256', $token);
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $insert = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
    $stmt = $this->conn->prepare($insert);
    $stmt->bind_param("iss", $user['id'], $token_hash, $expires_at);
    
    if ($stmt->execute()) {
        return ['success' => true, 'token' => $token, 'user_id' => $user['id']];
    }
    return ['success' => false];
}

/**
 * Reset password with token
 */
public function resetPasswordWithToken($token, $new_password) {
    if (strlen($new_password) < 8) {
        return ['success' => false, 'error' => 'Password too short'];
    }
    
    $token_hash = hash('sha256', $token);
    
    // Find reset token
    $query = "SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'error' => 'Invalid or expired reset token'];
    }
    
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    
    // Update password
    $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
    $update = "UPDATE users SET password_hash = ? WHERE id = ?";
    $stmt = $this->conn->prepare($update);
    $stmt->bind_param("si", $password_hash, $user_id);
    
    if ($stmt->execute()) {
        // Delete used token
        $delete = "DELETE FROM password_resets WHERE token = ?";
        $del_stmt = $this->conn->prepare($delete);
        $del_stmt->bind_param("s", $token_hash);
        $del_stmt->execute();
        
        return ['success' => true];
    }
    return ['success' => false];
}
```

### Step 3: Create Controller Handler

**File**: `/src/controllers/password-reset.php`:
```php
<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/EmailService.php';

$db = new Database();
$conn = $db->connect();
$user = new User($conn);
$emailService = new EmailService();

$action = $_GET['action'] ?? '';

if ($action === 'forgot-password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $_SESSION['error'] = 'Email is required';
        redirect(BASE_URL . '/?page=forgot-password');
    }
    
    $result = $user->createPasswordResetToken($email);
    
    if ($result['success']) {
        $reset_link = BASE_URL . "/?page=reset-password&token=" . $result['token'];
        $emailService->sendPasswordResetEmail($email, $reset_link);
        
        $_SESSION['message'] = 'Password reset link sent to your email (valid for 1 hour)';
        redirect(BASE_URL . '/?page=login');
    } else {
        $_SESSION['message'] = 'If this email exists, a reset link has been sent';
        redirect(BASE_URL . '/?page=login');
    }
}

if ($action === 'reset-password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if ($password !== $password_confirm) {
        $_SESSION['error'] = 'Passwords do not match';
        redirect(BASE_URL . '/?page=reset-password&token=' . urlencode($token));
    }
    
    $result = $user->resetPasswordWithToken($token, $password);
    
    if ($result['success']) {
        $_SESSION['message'] = 'Password reset successfully. Please login with your new password.';
        redirect(BASE_URL . '/?page=login');
    } else {
        $_SESSION['error'] = $result['error'];
        redirect(BASE_URL . '/?page=reset-password-failed');
    }
}
?>
```

### Step 4: Create Views

**File**: `/src/views/forgot-password.php`:
```php
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-4">Reset Your Password</h1>
        
        <p class="text-gray-600 mb-6">
            Enter your email address and we'll send you a link to reset your password.
        </p>
        
        <form method="POST" action="/src/controllers/password-reset.php?action=forgot-password">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email Address</label>
                <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-bold hover:bg-blue-700 mb-3">
                Send Reset Link
            </button>
        </form>
        
        <p class="text-center text-gray-600">
            Remember your password? <a href="?page=login" class="text-blue-600 hover:underline">Back to login</a>
        </p>
    </div>
</div>
```

**File**: `/src/views/reset-password.php`:
```php
<?php
$token = $_GET['token'] ?? '';
if (empty($token)) {
    echo '<div class="text-red-600">Invalid reset link</div>';
    exit;
}
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-4">Set New Password</h1>
        
        <form method="POST" action="/src/controllers/password-reset.php?action=reset-password">
            <input type="hidden" name="token" value="<?php echo sanitize($token); ?>">
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">New Password</label>
                <input type="password" id="password" name="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-2">Minimum 8 characters</p>
            </div>
            
            <div class="mb-4">
                <label for="password_confirm" class="block text-gray-700 font-bold mb-2">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-bold hover:bg-blue-700">
                Reset Password
            </button>
        </form>
    </div>
</div>
```

---

## Phase 3: Rate Limiting Enforcement

### Update Auth Controller

**File**: `/src/controllers/auth.php`:
```php
public function login() {
    // ... existing validation ...
    
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // CHECK RATE LIMIT
    $security = new Security();
    $rate_limit_check = $security->rateLimit($_SERVER['REMOTE_ADDR'], 'login', 5, 3600);
    
    if (!$rate_limit_check['allowed']) {
        return [
            'success' => false,
            'error' => 'Too many login attempts. Try again in ' . $rate_limit_check['retry_after'] . ' minutes'
        ];
    }
    
    // Try login
    $result = $this->user->login($identifier, $password);
    
    if (!$result['success']) {
        // Record failed attempt
        $security->recordFailedAttempt($_SERVER['REMOTE_ADDR'], 'login');
        return $result;
    }
    
    // Login successful - clear rate limit
    $security->clearRateLimit($_SERVER['REMOTE_ADDR'], 'login');
    
    // ... existing login code ...
}
```

---

## Phase 4: CSRF Protection

### Add to Base View Template

**File**: `/src/views/base.php` or in each form:
```php
<?php
// At top of view file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- In every form: -->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo sanitize($_SESSION['csrf_token']); ?>">
    <!-- other form fields -->
</form>
```

### Verify in Controllers

```php
public function handleFormSubmission() {
    // Verify CSRF token
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        return ['success' => false, 'error' => 'Security token mismatch'];
    }
    
    // ... process form ...
}
```

---

## Estimated Implementation Time

- Email Verification: 4-6 hours
- Password Reset: 3-4 hours
- Rate Limiting: 1-2 hours
- CSRF Protection: 2-3 hours
- Testing & Debugging: 4-5 hours

**Total**: 14-20 hours

---

## Testing Checklist

- [ ] User registration sends verification email
- [ ] Verification link works and marks user as verified
- [ ] Expired tokens rejected
- [ ] Resend verification email works
- [ ] Forgot password sends reset email
- [ ] Reset password link works
- [ ] Expired reset tokens rejected
- [ ] Login rate limited after 5 failures
- [ ] Rate limit cleared on successful login
- [ ] CSRF token validated on all forms
- [ ] CSRF token regenerated after login
- [ ] Token mismatch returns error

---

This implementation guide provides the exact code needed for the 4 most critical missing features. Start here for the MVP.
