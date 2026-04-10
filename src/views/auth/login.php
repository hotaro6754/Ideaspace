<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IdeaSync</title>
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
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome back</h2>
                <p class="text-gray-600">Sign in to your account</p>
            </div>

            <!-- Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-green-800"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-red-800"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?php echo BASE_URL; ?>/src/controllers/auth.php" class="space-y-4">
                <input type="hidden" name="action" value="login">

                <!-- Roll Number / Email -->
                <div class="input-group">
                    <label for="identifier">Roll Number or Email</label>
                    <input type="text" id="identifier" name="identifier" placeholder="LID001 or you@example.com" required autofocus>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-blue-500 hover:text-blue-600 transition">Forgot password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-full mt-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign In
                </button>

                <!-- Register Link -->
                <p class="text-center text-gray-600 text-sm">
                    Don't have an account?
                    <a href="<?php echo BASE_URL; ?>/?page=register" class="text-blue-500 font-medium hover:text-blue-600 transition">
                        Sign up
                    </a>
                </p>
            </form>

            <!-- Divider -->
            <div class="mt-6 flex items-center gap-3">
                <div class="flex-1 h-px bg-gray-300"></div>
                <span class="text-xs text-gray-500">Platform Demo</span>
                <div class="flex-1 h-px bg-gray-300"></div>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <h3 class="text-sm font-bold text-amber-900 mb-2">Demo Credentials</h3>
                <div class="space-y-2 text-xs text-amber-800">
                    <p><strong>Visionary:</strong> LID001 / demo123456</p>
                    <p><strong>Builder:</strong> LID002 / demo123456</p>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-6">
                <a href="<?php echo BASE_URL; ?>/?page=home" class="text-blue-500 hover:underline">Back to Home</a>
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

        .gap-2 {
            gap: 0.5rem;
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

        .inline-block {
            display: inline-block;
        }

        .bg-white {
            background-color: #ffffff;
        }

        .bg-green-50 {
            background-color: #f0fdf4;
        }

        .border-green-200 {
            border-color: #bbf7d0;
        }

        .text-green-800 {
            color: #166534;
        }

        .text-green-500 {
            color: #22c55e;
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

        .bg-amber-50 {
            background-color: #fffbeb;
        }

        .border-amber-200 {
            border-color: #fcd34d;
        }

        .text-amber-900 {
            color: #78350f;
        }

        .text-amber-800 {
            color: #92400e;
        }

        .space-y-2 > * + * {
            margin-top: 0.5rem;
        }

        .w-4 {
            width: 1rem;
        }

        .h-4 {
            height: 1rem;
        }

        .rounded {
            border-radius: 0.25rem;
        }

        .justify-between {
            justify-content: space-between;
        }

        .hover\:underline:hover {
            text-decoration: underline;
        }

        input[type="checkbox"] {
            cursor: pointer;
        }
    </style>
</body>
</html>
