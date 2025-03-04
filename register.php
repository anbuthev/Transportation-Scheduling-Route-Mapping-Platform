<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 text-center">Register</h2>
        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-600 font-medium">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    placeholder="Enter your username"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>
            <div>
                <label for="email" class="block text-gray-600 font-medium">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    placeholder="Enter your email"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>
            <div>
                <label for="password" class="block text-gray-600 font-medium">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="Enter your password"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>
            <div>
                <label for="role" class="block text-gray-600 font-medium">Select Role</label>
                <select 
                    name="role" 
                    id="role"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
                    <option value="driver">Driver</option>
                    <option value="conductor">Conductor</option>
                    <option value="manager">Manager</option>
                    <option value="otheruser">Other User</option>
                </select>
            </div>
            <button 
                type="submit" 
                class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-300">
                Register
            </button>
        </form>
        <?php if (isset($_GET['error'])): ?>
            <p class="text-red-600 text-center mt-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p class="text-green-600 text-center mt-4"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>
        <p class="text-sm text-center text-gray-500 mt-4">
            Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Login</a>
        </p>
    </div>
</body>
</html>
