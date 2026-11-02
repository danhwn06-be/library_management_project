<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Borrow List</title>
</head>

<body>
    <?php
    require __DIR__ . '/../layouts/headerAdmin.php'; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
        <div class="alert alert-success mt-2">Action completed successfully.</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
        <div class="alert alert-danger mt-2">Action failed.</div>
    <?php endif; ?>
    <div class="container-fluid mt-3 mb-2 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 text-secondary"><i class="bi bi-journals me-2"></i>Borrowing Requests List</h4>
        <a href="index.php?action=admin_overdue_list" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-clock-history me-1"></i> View Overdue Requests Only
        </a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Request ID</th>
                <th>Full name</th>
                <th>Address</th>
                <th>Phone number</th>
                <th>Request date</th>
                <th>Schedule return date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $r): ?>
                <?php 
                    $isOverdue = ($r['status'] === 'Approved' && !empty($r['schedule_return_date']) && strtotime($r['schedule_return_date']) < time());
                ?>
                <tr style="cursor:pointer"
                    class="<?= $isOverdue ? 'table-danger-subtle' : '' ?>"
                    onclick="window.location='index.php?action=admin_borrow_detail&id=<?= $r['id'] ?>'">
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['full_name']) ?></td>
                    <td><?= htmlspecialchars($r['address']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td><?= $r['request_date'] ?></td>
                    <td>
                        <?= $r['schedule_return_date'] ?>
                        <?php if ($isOverdue): ?>
                            <span class="badge bg-danger ms-1">Overdue</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($r['status'] === 'Approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php elseif ($r['status'] === 'Pending'): ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php elseif ($r['status'] === 'Returned'): ?>
                            <span class="badge bg-info text-dark">Returned</span>
                        <?php elseif ($r['status'] === 'Rejected'): ?>
                            <span class="badge bg-secondary">Rejected</span>
                        <?php else: ?>
                            <span class="badge bg-dark"><?= htmlspecialchars($r['status']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
