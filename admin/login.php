<?php
$pageTitle = "Admin Login";
$basePath = "../";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (\Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-glass p-4 border border-slate-800">
                <div class="card-body text-center">
                    <i class="fa-solid fa-user-shield text-accent-violet fs-1 mb-3"></i>
                    <h2 class="font-outfit fw-bold mb-4">Admin Portal</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger border-0 small py-2 px-3 mb-4 text-start" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="login.php" class="text-start">
                        <div class="mb-3">
                            <label for="username" class="form-label text-muted small">Username</label>
                            <input type="text" class="form-control bg-slate-900 border-slate-800 text-white py-2" id="username" name="username" placeholder="Enter username" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label text-muted small">Password</label>
                            <input type="password" class="form-control bg-slate-900 border-slate-800 text-white py-2" id="password" name="password" placeholder="Enter password" required>
                        </div>
                        <button type="submit" class="btn btn-primary-neon w-100 py-2 fs-6">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Log In
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4 text-muted small">
                <a href="../index.php" class="text-accent-cyan text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Public View
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
