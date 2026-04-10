<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">
            <!-- Logo/Branding -->
            <div class="text-center mb-8">
                <a href="<?php echo BASE_URL; ?>/?page=home" class="inline-block mb-6">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-purple-500 bg-clip-text text-transparent">
                        IdeaSync
                    </h1>
                </a>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Create your account</h2>
                <p class="text-gray-600">Join the campus collaboration platform</p>
            </div>

            <!-- Error Message -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-red-800"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" action="<?php echo BASE_URL; ?>/src/controllers/auth.php" class="space-y-4">
                <input type="hidden" name="action" value="register">

                <!-- Roll Number -->
                <div class="input-group">
                    <label for="roll_number">Roll Number</label>
                    <input type="text" id="roll_number" name="roll_number" placeholder="e.g., LID001"
                           required pattern="LID\d{3,}" title="Format: LID followed by numbers (e.g., LID001)">
                </div>

                <!-- Name -->
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required>
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>
                </div>

                <!-- Branch -->
                <div class="input-group">
                    <label for="branch">Branch</label>
                    <select id="branch" name="branch" required>
                        <option value="">Select your branch</option>
                        <option value="CSE">Computer Science & Engineering</option>
                        <option value="ECE">Electronics & Communication</option>
                        <option value="ME">Mechanical Engineering</option>
                        <option value="CE">Civil Engineering</option>
                        <option value="EE">Electrical Engineering</option>
                        <option value="MBA">MBA</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>

                <!-- Year -->
                <div class="input-group">
                    <label for="year">Year of Study</label>
                    <select id="year" name="year" required>
                        <option value="">Select your year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••"
                           required minlength="8" title="Password must be at least 8 characters">
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="password_confirm">Confirm Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="••••••••"
                           required minlength="8">
                </div>

                <!-- Password Requirements -->
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-900 font-medium mb-2">Password requirements:</p>
                    <ul class="text-xs text-blue-800 space-y-1">
                        <li>✓ At least 8 characters</li>
                        <li>✓ Passwords must match</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-full mt-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Create Account
                </button>

                <!-- Login Link -->
                <p class="text-center text-gray-600 text-sm">
                    Already have an account?
                    <a href="<?php echo BASE_URL; ?>/?page=login" class="text-blue-500 font-medium hover:text-blue-600 transition">
                        Sign in
                    </a>
                </p>
            </form>

            <!-- Divider -->
            <div class="mt-6 flex items-center gap-3">
                <div class="flex-1 h-px bg-gray-300"></div>
                <span class="text-xs text-gray-500 uppercase">Join as Visionary or Builder</span>
                <div class="flex-1 h-px bg-gray-300"></div>
            </div>

            <!-- User Type Info -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="p-3 bg-white border border-gray-200 rounded-lg">
                    <h3 class="text-sm font-bold text-gray-900 mb-1">Visionary</h3>
                    <p class="text-xs text-gray-600">Have an idea? Post it and find collaborators</p>
                </div>
                <div class="p-3 bg-white border border-gray-200 rounded-lg">
                    <h3 class="text-sm font-bold text-gray-900 mb-1">Builder</h3>
                    <p class="text-xs text-gray-600">Explore ideas and collaborate on projects</p>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-6">
                By registering, you agree to our
                <a href="#" class="text-blue-500 hover:underline">Terms of Service</a> and
                <a href="#" class="text-blue-500 hover:underline">Privacy Policy</a>
            </p>
        </div>
    </div>

    <style>
        .min-h-screen {
            min-height: 100vh;
        }

        .bg-gradient-to-br {
            background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
        }

        .from-blue-50 {
            --tw-gradient-from: #eff6ff;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(239, 246, 255, 0));
        }

        .to-purple-50 {
            --tw-gradient-to: #faf5ff;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-center {
            justify-content: center;
        }

        .py-12 {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .w-full {
            width: 100%;
        }

        .max-w-md {
            max-width: 28rem;
        }

        .text-center {
            text-align: center;
        }

        .mb-8 {
            margin-bottom: 2rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .text-3xl {
            font-size: 1.875rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-2xl {
            font-size: 1.5rem;
        }

        .text-gray-900 {
            color: #111827;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .space-y-4 > * + * {
            margin-top: 1rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .bg-red-50 {
            background-color: #fef2f2;
        }

        .border {
            border: 1px solid #e5e7eb;
        }

        .border-red-200 {
            border-color: #fecaca;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .flex-shrink-0 {
            flex-shrink: 0;
        }

        .mt-0.5 {
            margin-top: 0.125rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        .text-red-500 {
            color: #ef4444;
        }

        .text-red-800 {
            color: #991b1b;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .w-full {
            width: 100%;
        }

        .inline-block {
            display: inline-block;
        }

        .gap-4 {
            gap: 1rem;
        }

        .grid {
            display: grid;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .bg-white {
            background-color: #ffffff;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .border-blue-200 {
            border-color: #bfdbfe;
        }

        .text-blue-900 {
            color: #111e3d;
        }

        .font-medium {
            font-weight: 500;
        }

        .text-blue-800 {
            color: #1e40af;
        }

        .text-blue-500 {
            color: #3b82f6;
        }

        .hover\:text-blue-600:hover {
            color: #2563eb;
        }

        .transition {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        .h-px {
            height: 1px;
        }

        .bg-gray-300 {
            background-color: #d1d5db;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .p-3 {
            padding: 0.75rem;
        }

        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-stops));
        }

        .bg-clip-text {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .from-blue-500 {
            --tw-gradient-from: #3b82f6;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(59, 130, 246, 0));
        }

        .to-purple-500 {
            --tw-gradient-to: #8b5cf6;
        }

        .hover\:underline:hover {
            text-decoration: line-through: underline;
        }
    </style>
</body>
</html>
