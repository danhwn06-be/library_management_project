 <?php
class AuthController
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /* ================= LOGIN ================= */
   public function login()
    {
        $message = null;

        if (isset($_POST['login'])) {
            $message = $this->handleLogin(
                $_POST['email'] ?? '',
                $_POST['password'] ?? ''
            );
        }

        require dirname(__DIR__) . '/views/auth/login.php';
    }

    private function handleLogin($email, $password)
    {
        $user = $this->model->getUserByEmail($email);

        if (!$user) {
            return "You did not register yet!";
        }

        if (!password_verify($password, $user['password'])) {
            return "Wrong email or password!";
        }

        // Kiểm tra tài khoản có bị khóa không (OI-10)
        if (isset($user['is_locked']) && (int)$user['is_locked'] === 1) {
            return "Your account has been locked by the administrator!";
        }

        // Tái tạo session ID sau khi đăng nhập thành công (OI-19)
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'       => $user['user_id'],
            'username' => $user['username'],
            'role'     => (int)$user['role']
        ];

        // Redirect theo role
        if ((int)$user['role'] === 1) {
            header("Location: index.php?action=admin_borrow_list");
        } else {
            header("Location: index.php?action=listbook");
        }
        exit();
    }


    public function logout()
    {
        session_destroy();
        header("Location: index.php?action=listbook");
        exit();
    }

    /* ================= PROFILE ================= */
    public function profile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit();
        }

        require_once dirname(__DIR__) . '/models/category.php';
        $categoryModel = new Category($this->model->getDb());
        $categories = $categoryModel->getAllCategories();

        $userId = $_SESSION['user']['id'];
        $user = $this->model->getUserById($userId);

        if (!$user) {
            header("Location: index.php?action=logout");
            exit();
        }

        $successMsg = $_SESSION['profile_success'] ?? null;
        $errorMsg = $_SESSION['profile_error'] ?? null;
        unset($_SESSION['profile_success'], $_SESSION['profile_error']);

        require_once dirname(__DIR__) . '/views/user/profile.php';
    }

    public function updateProfile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $fullName = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $gender = $_POST['gender'] ?? '0';
            $dob = $_POST['date_of_birth'] ?? null;
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($fullName)) {
                $_SESSION['profile_error'] = "Full name cannot be empty!";
                header("Location: index.php?action=profile");
                exit();
            }

            $data = [
                'full_name'     => $fullName,
                'phone'         => $phone,
                'address'       => $address,
                'gender'        => $gender,
                'date_of_birth' => !empty($dob) ? $dob : null
            ];

            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $_SESSION['profile_error'] = "New password must be at least 6 characters!";
                    header("Location: index.php?action=profile");
                    exit();
                }
                if ($newPassword !== $confirmPassword) {
                    $_SESSION['profile_error'] = "Password confirmation does not match!";
                    header("Location: index.php?action=profile");
                    exit();
                }
                $data['password'] = $newPassword;
            }

            $result = $this->model->updateProfile($userId, $data);
            if ($result) {
                $_SESSION['profile_success'] = "Profile updated successfully!";
            } else {
                $_SESSION['profile_error'] = "Failed to update profile. Please try again.";
            }
        }

        header("Location: index.php?action=profile");
        exit();
    }


    /* ================= REGISTER ================= */



    public function registerView()
    {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function doRegister()
    {
        $fullname = trim($_POST['fullname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $password = $_POST['pwd'] ?? '';

        // Server-side validation (OI-19)
        if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please fill in all required fields!';
            header("Location: index.php?action=register");
            exit();
        }

        if (strlen($username) < 3) {
            $_SESSION['error'] = 'Username must be at least 3 characters long!';
            header("Location: index.php?action=register");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email address format!';
            header("Location: index.php?action=register");
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters long!';
            header("Location: index.php?action=register");
            exit();
        }

        if (!empty($phone) && !preg_match('/^[0-9]{10,11}$/', $phone)) {
            $_SESSION['error'] = 'Invalid phone number format (must be 10-11 digits)!';
            header("Location: index.php?action=register");
            exit();
        }

        $data = [
            'fullname'      => $fullname,
            'username'      => $username,
            'email'         => $email,
            'phone'         => $phone,
            'password'      => $password,
            'address'       => trim($_POST['address'] ?? ''),
            'gender'        => $_POST['gender'] ?? 0,
            'date_of_birth' => $_POST['date_of_birth'] ?? '',
        ];

        $result = $this->model->register($data);
        if ($result['status'] === true) {
            header("Location: index.php?action=login");
            exit();
        } else {
            $_SESSION['error'] = $result['message'];
            header("Location: index.php?action=register");
            exit();
        }
    }

}