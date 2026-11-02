<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container my-5" style="max-width: 1100px;">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="index.php?action=listbook" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Borrowing History</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>My Borrowing History</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?action=mybook" class="btn btn-outline-warning text-dark btn-sm fw-semibold">
                <i class="bi bi-book me-1"></i> My Book Cart
            </a>
            <a href="index.php?action=listbook" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Borrow More Books
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($successMsg) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($errorMsg) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($history)): ?>
        <!-- Empty State -->
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
            <div class="mb-3 text-muted">
                <i class="bi bi-journal-x display-1 text-secondary opacity-50"></i>
            </div>
            <h4 class="fw-bold text-dark">No Borrowing Records Found</h4>
            <p class="text-muted mx-auto" style="max-width: 450px;">
                You haven't submitted any borrow requests yet. Explore our extensive library collection and borrow your favorite books today!
            </p>
            <div class="mt-2">
                <a href="index.php?action=listbook" class="btn btn-warning px-4 py-2 fw-semibold text-dark rounded-pill">
                    <i class="bi bi-search me-1"></i> Explore Books
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Summary Stats Pills -->
        <?php
            $countTotal = count($history);
            $countPending = 0;
            $countApproved = 0;
            $countReturned = 0;
            $countRejected = 0;
            foreach ($history as $h) {
                if ($h['request_status'] === 'Pending') $countPending++;
                elseif ($h['request_status'] === 'Approved') $countApproved++;
                elseif ($h['request_status'] === 'Returned') $countReturned++;
                elseif ($h['request_status'] === 'Rejected') $countRejected++;
            }
        ?>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
                    <span class="text-muted small">Total Requests</span>
                    <h3 class="fw-bold text-dark mb-0"><?= $countTotal ?></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
                    <span class="text-muted small">Pending</span>
                    <h3 class="fw-bold text-warning mb-0"><?= $countPending ?></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
                    <span class="text-muted small">Borrowing</span>
                    <h3 class="fw-bold text-info mb-0"><?= $countApproved ?></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
                    <span class="text-muted small">Returned</span>
                    <h3 class="fw-bold text-success mb-0"><?= $countReturned ?></h3>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <div class="d-flex flex-column gap-3">
            <?php foreach ($history as $req): ?>
                <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
                    <div class="card-header bg-light py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2 border-0">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fw-bold text-dark fs-6">
                                <i class="bi bi-hash text-muted"></i>Request #<?= htmlspecialchars($req['borrow_request_id']) ?>
                            </span>
                            <span class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>Date: <?= date('d/m/Y H:i', strtotime($req['request_date'])) ?>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <?php if ($req['request_status'] === 'Pending'): ?>
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-hourglass-split me-1"></i>Pending Approval
                                </span>
                            <?php elseif ($req['request_status'] === 'Approved'): ?>
                                <span class="badge bg-info text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-book-half me-1"></i>Approved (Borrowing)
                                </span>
                            <?php elseif ($req['request_status'] === 'Returned'): ?>
                                <span class="badge bg-success px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i>Returned
                                </span>
                            <?php elseif ($req['request_status'] === 'Rejected'): ?>
                                <span class="badge bg-danger px-3 py-2 rounded-pill">
                                    <i class="bi bi-x-circle-fill me-1"></i>Rejected / Canceled
                                </span>
                            <?php endif; ?>

                            <?php if ($req['request_status'] === 'Pending'): ?>
                                <a href="index.php?action=cancel_borrow_request&id=<?= $req['borrow_request_id'] ?>" 
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to cancel borrow request #<?= $req['borrow_request_id'] ?>?');"
                                   title="Cancel this request">
                                    <i class="bi bi-trash"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="small text-muted">Receiver Name:</div>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($req['full_name']) ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Contact Phone:</div>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($req['phone']) ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Expected Due Date:</div>
                                <div class="fw-semibold text-primary">
                                    <i class="bi bi-clock me-1"></i><?= !empty($req['schedule_return_date']) ? date('d/m/Y', strtotime($req['schedule_return_date'])) : 'N/A' ?>
                                </div>
                            </div>
                            <?php if (!empty($req['note'])): ?>
                                <div class="col-12 mt-2">
                                    <div class="small text-muted"><i class="bi bi-chat-left-text me-1"></i>Note:</div>
                                    <div class="text-secondary fst-italic bg-light p-2 rounded small"><?= htmlspecialchars($req['note']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Books Included in this Request -->
                        <div class="mt-3 pt-3 border-top">
                            <h6 class="fw-bold text-muted small text-uppercase mb-3">
                                <i class="bi bi-bookshelf me-1"></i>Books in this Request (Total: <?= $req['quantity'] ?>):
                            </h6>
                            <div class="row g-3">
                                <?php if (!empty($req['books'])): ?>
                                    <?php foreach ($req['books'] as $book): ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="d-flex align-items-center p-2 rounded-3 bg-light border">
                                                <img src="images/<?= htmlspecialchars($book['image_url']) ?>" 
                                                     alt="<?= htmlspecialchars($book['book_title']) ?>" 
                                                     class="rounded object-fit-cover me-3 shadow-sm" 
                                                     style="width: 50px; height: 65px;"
                                                     onerror="this.src='images/logo.png'">
                                                <div class="overflow-hidden">
                                                    <div class="fw-bold text-dark small text-truncate" title="<?= htmlspecialchars($book['book_title']) ?>">
                                                        <?= htmlspecialchars($book['book_title']) ?>
                                                    </div>
                                                    <div class="text-muted small text-truncate">
                                                        <?= htmlspecialchars($book['author'] ?: 'Unknown Author') ?>
                                                    </div>
                                                    <div class="badge bg-secondary bg-opacity-25 text-dark small mt-1">
                                                        Qty: <?= htmlspecialchars($book['quantity']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-muted small">No book details recorded.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
