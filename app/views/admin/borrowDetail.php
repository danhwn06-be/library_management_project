<?php
require __DIR__ . '/../layouts/headerAdmin.php'; ?>

<div class="container mt-4">
    <?php 
    $isOverdue = (($request['status'] ?? '') === 'Approved' && !empty($request['schedule_return_date']) && strtotime($request['schedule_return_date']) < time());
    $overdueDays = $isOverdue ? ceil((time() - strtotime($request['schedule_return_date'])) / 86400) : 0;
    ?>

    <?php if ($isOverdue): ?>
        <div class="alert alert-danger d-flex justify-content-between align-items-center mb-4 shadow-sm">
            <div>
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <strong>OVERDUE ALERT:</strong> This request is overdue by <strong><?= $overdueDays ?> days</strong>!
            </div>
            <a href="index.php?action=admin_send_reminder&id=<?= $request['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Send overdue reminder email to <?= htmlspecialchars($request['full_name']) ?>?')">
                <i class="bi bi-envelope-at me-1"></i> Send Reminder Email
            </a>
        </div>
    <?php endif; ?>

    <div class="card-info">
        <h3>Request Information</h3>
        <p><strong>Borrowing ID:</strong> <?= $request['id'] ?? '' ?></p>
        <p><strong>Full name:</strong> <?= htmlspecialchars($request['full_name'] ?? '') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($request['phone'] ?? '') ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($request['address'] ?? '') ?></p>
        <p><strong>Borrow date:</strong> <?= $request['request_date'] ?? '' ?></p>
        <p>
            <strong>Return date:</strong> <?= $request['schedule_return_date'] ?? '' ?>
            <?php if ($isOverdue): ?>
                <span class="badge bg-danger ms-2"><?= $overdueDays ?> days overdue</span>
            <?php endif; ?>
        </p>
        <p><strong>Status:</strong> <?= $request['status'] ?? '' ?></p>
        <p><strong>Note:</strong> <?= htmlspecialchars(!empty($request['note']) ? $request['note'] : 'None') ?></p>
    </div>
</div>

<h4 class="mt-4 mb-3">Books in this request</h4>

<table class="table table-hover shadow-sm">
    <thead class="table-primary">
        <tr>
            <th>Barcode (Mã vạch)</th>
            <th>Book Title</th>
            <th>Author</th>
            <th>Quantity</th>
            <th>Category</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $i): ?>
        <tr>
            <td>
                <code class="fw-bold text-dark" style="font-size: 1.1rem;">
                        <?= $i['barcode'] ?? 'N/A' ?>
                    </code>
            </td>
            <td><?= htmlspecialchars($i['book_title']) ?></td>
            <td><?= htmlspecialchars($i['author']) ?></td>
            <td><span class="badge bg-secondary"><?= $i['quantity'] ?></span></td>
            <td><?= htmlspecialchars($i['category']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($request['status'] === 'Pending'): ?>
<div class="d-flex justify-content-center gap-3 mt-5">
    <a href="index.php?action=admin_update_borrow_status&id=<?= $request['id'] ?>&status=Approved"
        class="btn btn-success btn-lg px-5 shadow" onclick="return confirm('Approve and minus stock?')">
        Accept
    </a>
    <a href="index.php?action=admin_update_borrow_status&id=<?= $request['id'] ?>&status=Rejected"
        class="btn btn-danger btn-lg px-5 shadow">
        Refuse
    </a>
</div>
<?php elseif ($request['status'] === 'Approved'): ?>
<div class="d-flex justify-content-center mt-5">
    <a href="index.php?action=admin_update_borrow_status&id=<?= $request['id'] ?>&status=Returned"
        class="btn btn-primary btn-lg px-5 shadow"
        onclick="return confirm('Confirm book returned and add back to stock?')">
        <i class="fas fa-undo"></i> Confirm Return
    </a>
</div>
<?php elseif ($request['status'] === 'Returned'): ?>
<div class="alert alert-success text-center mt-5">
    <strong>Status:</strong> This request has been completed and books were returned.
</div>
<?php endif; ?>
</div>
