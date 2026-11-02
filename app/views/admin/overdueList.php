<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Borrowing Reminders - TVAN Library Admin</title>
</head>

<body>
    <?php require __DIR__ . '/../layouts/headerAdmin.php'; ?>

    <div class="container-fluid px-4 py-3">

        <!-- Flash Messages -->
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'reminder_success'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Success!</strong> Overdue reminder email has been sent successfully to the borrower.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'reminder_error'): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                <strong>Failed!</strong> Could not deliver reminder email via SMTP. Please check internet connection or mail configuration.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'batch_completed'): ?>
            <?php 
                $batchRes = $_SESSION['batch_reminder_result'] ?? null;
                unset($_SESSION['batch_reminder_result']);
            ?>
            <?php if ($batchRes): ?>
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-info-circle-fill me-2"></i>Batch Reminder Execution Completed</h5>
                    <p class="mb-1">
                        Found <strong><?= (int)$batchRes['total_eligible'] ?></strong> eligible request(s) for reminder (interval &ge; 2 days).
                        Successfully sent: <span class="badge bg-success"><?= (int)$batchRes['success_count'] ?></span> | 
                        Failed: <span class="badge bg-danger"><?= (int)$batchRes['failed_count'] ?></span>
                    </p>
                    <?php if (!empty($batchRes['details'])): ?>
                        <ul class="mb-0 mt-2 small ps-3">
                            <?php foreach ($batchRes['details'] as $item): ?>
                                <li>
                                    Request #<?= $item['id'] ?> (<?= htmlspecialchars($item['name']) ?> - <?= htmlspecialchars($item['email']) ?>): 
                                    <strong><?= $item['status'] ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Top Header & Action Controls -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-7">
                <h3 class="fw-bold text-danger mb-1">
                    <i class="bi bi-clock-history me-2"></i>Overdue Book Loan Management
                </h3>
                <p class="text-muted mb-0">
                    Track overdue borrowers and dispatch reminder notices directly without waiting for automated server schedule.
                </p>
            </div>
            <div class="col-md-5 text-md-end">
                <a href="index.php?action=admin_borrow_list" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i>All Requests
                </a>
                <a href="index.php?action=admin_batch_reminder" 
                   class="btn btn-danger shadow-sm fw-semibold"
                   onclick="return confirm('Do you want to scan and send reminder emails to ALL eligible overdue borrowers right now?');">
                    <i class="bi bi-send-fill me-1"></i>Run Overdue Reminders Now (<?= $totalPendingReminder ?>)
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <div class="text-white-50 text-uppercase small fw-bold">Total Overdue Requests</div>
                            <div class="fs-2 fw-bold"><?= $totalOverdue ?></div>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <div class="text-muted text-uppercase small fw-bold">Eligible for Reminder (Today)</div>
                            <div class="fs-2 fw-bold"><?= $totalPendingReminder ?></div>
                        </div>
                        <i class="bi bi-envelope-exclamation-fill fs-1 text-black-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <div class="text-white-50 text-uppercase small fw-bold">Anti-Spam Threshold</div>
                            <div class="fs-2 fw-bold">2 Days</div>
                        </div>
                        <i class="bi bi-shield-check fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold text-secondary">
                    <i class="bi bi-list-ul me-2"></i>Overdue Borrowers List
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($overdueRequests)): ?>
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-check-circle text-success display-4 mb-3 d-block"></i>
                        <h5>No Overdue Borrowing Records Found!</h5>
                        <p class="mb-0">All approved book loans are currently within the allowed schedule return date.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 80px;">ID</th>
                                    <th>Borrower Info</th>
                                    <th>Borrowed Books</th>
                                    <th>Scheduled Return</th>
                                    <th>Overdue Days</th>
                                    <th>Last Reminder Sent</th>
                                    <th class="text-center" style="width: 180px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueRequests as $req): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold">#<?= $req['id'] ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($req['full_name']) ?></div>
                                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($req['email']) ?></div>
                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($req['phone'] ?? 'N/A') ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($req['books'])): ?>
                                                <ul class="list-unstyled mb-0 small">
                                                    <?php foreach ($req['books'] as $b): ?>
                                                        <li>
                                                            <i class="bi bi-book me-1 text-primary"></i>
                                                            <?= htmlspecialchars($b['book_title']) ?> 
                                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">x<?= (int)$b['quantity'] ?></span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <span class="text-muted small">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-danger fw-semibold">
                                                <?= date('Y-m-d H:i', strtotime($req['schedule_return_date'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger fs-6">
                                                +<?= (int)$req['overdue_days'] ?> days
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($req['last_reminder_date'])): ?>
                                                <span class="badge bg-info text-dark">
                                                    <i class="bi bi-clock me-1"></i><?= date('Y-m-d H:i', strtotime($req['last_reminder_date'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Never</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="index.php?action=admin_send_reminder&id=<?= $req['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   title="Send Email Reminder Now"
                                                   onclick="return confirm('Send overdue reminder email to <?= htmlspecialchars($req['full_name']) ?> (<?= htmlspecialchars($req['email']) ?>)?');">
                                                    <i class="bi bi-envelope-fill me-1"></i>Remind
                                                </a>
                                                <a href="index.php?action=admin_borrow_detail&id=<?= $req['id'] ?>" 
                                                   class="btn btn-sm btn-outline-secondary"
                                                   title="View Request Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
</body>

</html>
