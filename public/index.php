<?php
session_start();

/* CONFIG*/
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/core/Auth.php';

/* MODELS*/
require_once dirname(__DIR__) . '/app/models/user.php';
require_once dirname(__DIR__) . '/app/models/book.php';
require_once dirname(__DIR__) . '/app/models/category.php';
require_once dirname(__DIR__) . '/app/models/borrowRequest.php';

/*CONTROLLERS*/
require_once dirname(__DIR__) . '/app/controllers/authController.php';
require_once dirname(__DIR__) . '/app/controllers/bookController.php';
require_once dirname(__DIR__) . '/app/controllers/adminController.php';
require_once dirname(__DIR__) . '/app/controllers/borrowController.php';
require_once dirname(__DIR__) . '/app/controllers/SiteController.php';
$database = new Database();
$db = $database->connect();

/* CONTROLLER INSTANCES*/
$authController = new AuthController(new User($db));
$bookController = new BookController($db);
$borrowController = new BorrowController($db);
$adminController = new AdminController($db);
$siteController = new SiteController($db);



$action = isset($_GET['action']) ? $_GET['action'] : 'login';

switch ($action) {

    case 'register':
        $authController->registerView();
        break;

    case 'doregister':
        $authController->doRegister();
        break;

    case 'login':
        $authController->login();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'profile':
        $authController->profile();
        break;

    case 'update_profile':
        $authController->updateProfile();
        break;

    case 'history':
        $borrowController->history();
        break;

    case 'cancel_borrow_request':
        $borrowController->cancelRequest();
        break;

    case 'listbook':
        $bookController->showListBook();
        break;

    case 'category':
        $bookController->showByCategory();
        break;

    case 'about':
        $siteController->about();
        break;

    case 'contact':
        $siteController->contact();
        break;

    case 'add_to_mybook':
        $borrowController->addToMyBook($_GET['id'], $_GET['title'], $_GET['author'], $_GET['img']);
        break;

    case 'bookdetail':
        $id = $_GET['id'] ?? null;
        $bookController->viewDetail($id);
        break;

    case 'mybook':
        $borrowController->showMyBook();
        break;

    case 'show_borrow_form':
        $borrowController->showFormBookRequest();
        break;

    case 'submit_borrow':
        $borrowController->submitRequest();
        break;

    case 'remove_from_cart':
        $borrowController->removeFromCart();
        break;

    case 'update_cart_qty':
        $borrowController->updateCartQty();
        break;

    case 'search':
        $bookController->search();
        break;

    case 'book_management':
        $adminController->bookManagement();
        break;

    case 'show_form_add_book':
        $adminController->showAddBookForm();
        break;

    case 'addbook':
        $adminController->doAddBook();
        break;

    case 'edit_book':
        $id = $_GET['id'] ?? null;
        $adminController->showEditBookForm($id);
        break;

    case 'do_edit_book':
        $adminController->doEditBook();
        break;

    case 'delete_book':
        $id = $_GET['id'] ?? null;
        $adminController->doDeleteBook($id);
        break;

    case 'admin_borrow_list':
        $adminController->list();
        break;

    case 'admin_borrow_detail':
        $id = $_GET['id'] ?? null;
        $adminController->detail($id);
        break;

    case 'admin_update_borrow_status':
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;
        $adminController->updateStatus($id, $status);
        break;

    case 'import_book_by_excel':
        $adminController->show_form_import();
        break;

    case 'do_import_book':
        $adminController->doImportBook();
        break;

    case 'admin_user_list':
        $adminController->userManagement();
        break;

    case 'admin_lock_user':
        $adminController->lockUser();
        break;

    case 'admin_unlock_user':
        $adminController->unlockUser();
        break;

    case 'admin_statistic':
        $adminController->showStatistic();
        break;

    case 'admin_overdue_list':
        $adminController->overdueList();
        break;

    case 'admin_send_reminder':
        $id = $_GET['id'] ?? null;
        $adminController->sendReminder($id);
        break;

    case 'admin_batch_reminder':
        $adminController->batchSendReminders();
        break;

    default:
        header("Location: index.php?action=listbook");
        exit();
}
?>