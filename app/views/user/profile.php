<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container my-5" style="max-width: 960px;">
    <!-- Breadcrumb & Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="index.php?action=listbook" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge me-2 text-primary"></i>My Profile</h2>
        </div>
        <div>
            <a href="index.php?action=history" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-clock-history me-1"></i> View Borrowing History
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

    <div class="row g-4">
        <!-- Sidebar Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 text-center p-4 h-100 bg-white">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold" 
                         style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user['full_name'] ?? 'User') ?></h4>
                <p class="text-muted small mb-2">@<?= htmlspecialchars($user['username'] ?? '') ?></p>
                <div>
                    <span class="badge <?= ($user['role'] == 1) ? 'bg-danger' : 'bg-primary' ?> px-3 py-2 rounded-pill">
                        <i class="bi <?= ($user['role'] == 1) ? 'bi-shield-check' : 'bi-mortarboard-fill' ?> me-1"></i>
                        <?= ($user['role'] == 1) ? 'Administrator' : 'Library Member' ?>
                    </span>
                </div>
                
                <hr class="my-4 text-muted">

                <div class="text-start small">
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-envelope me-2"></i>Email:</span>
                        <span class="fw-semibold text-truncate ms-2" style="max-width: 170px;" title="<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-telephone me-2"></i>Phone:</span>
                        <span class="fw-semibold"><?= htmlspecialchars($user['phone'] ?: 'Not set') ?></span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-geo-alt me-2"></i>Address:</span>
                        <span class="fw-semibold text-truncate ms-2" style="max-width: 170px;" title="<?= htmlspecialchars($user['address'] ?: 'Not set') ?>"><?= htmlspecialchars($user['address'] ?: 'Not set') ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-shield-check me-2"></i>Status:</span>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold">Active</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Edit Form Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <h4 class="fw-bold mb-3 text-dark pb-2 border-bottom">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Personal Information
                </h4>

                <form action="index.php?action=update_profile" method="POST" class="needs-validation">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label fw-semibold text-muted small">Username</label>
                            <input type="text" class="form-control bg-light" id="username" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            <div class="form-text">Username cannot be changed.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold text-muted small">Email Address</label>
                            <input type="email" class="form-control bg-light" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            <div class="form-text">Email cannot be changed.</div>
                        </div>

                        <div class="col-md-12">
                            <label for="full_name" class="form-label fw-semibold text-dark small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold text-dark small">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0901234567">
                        </div>

                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label fw-semibold text-dark small">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Gender</label>
                            <div class="d-flex gap-4 pt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="0" <?= ($user['gender'] == '0' || $user['gender'] === 'Nam' || empty($user['gender'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="gender_male">Male (Nam)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="1" <?= ($user['gender'] == '1' || $user['gender'] === 'Nữ') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="gender_female">Female (Nữ)</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="address" class="form-label fw-semibold text-dark small">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Your residential address">
                        </div>
                    </div>

                    <!-- Change Password (Optional) -->
                    <div class="mt-4 pt-3 border-top">
                        <button class="btn btn-link text-decoration-none p-0 fw-semibold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#changePasswordSection">
                            <i class="bi bi-key me-1"></i> Change Password (Optional) <i class="bi bi-chevron-down small ms-1"></i>
                        </button>
                        
                        <div class="collapse mt-3" id="changePasswordSection">
                            <div class="p-3 bg-light rounded-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label fw-semibold text-dark small">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="At least 6 characters">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label fw-semibold text-dark small">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter new password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                        <a href="index.php?action=listbook" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4 fw-semibold text-dark">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
