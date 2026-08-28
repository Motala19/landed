<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? '';
$from = $_GET['from'] ?? 'finance';

if ($id === 0) {
    header("Location: requisitions.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM requisitions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$requisition = $result->fetch_assoc();

if (!$requisition) {
    die("Requisition not found.");
}

// Back link
switch ($from) {
    case 'requisitions':
        $backLink = 'requisitions.php';
        break;
    case 'principal':
        $backLink = 'principal-dashboard.php';
        break;
    case 'treasurer':
        $backLink = 'treasurer-dashboard.php';
        break;
    default:
        $backLink = 'finance-dashboard.php';
}

// Status heading
$status = $requisition['status'] ?? '';
$statusTitle = 'View Requisition';
$statusClass = '';

switch ($status) {
    case 'New':
        $statusTitle = 'NEW REQUISITION';
        $statusClass = 'text-primary';
        break;
    case 'Pending':
        $statusTitle = 'PENDING REQUISITION';
        $statusClass = 'text-warning';
        break;
    case 'Principal Approved':
        $statusTitle = 'PRINCIPAL APPROVED';
        $statusClass = 'text-info';
        break;
    case 'Approved':
        $statusTitle = 'APPROVED REQUISITION';
        $statusClass = 'text-success';
        break;
    case 'Paid':
        $statusTitle = 'PAID REQUISITION';
        $statusClass = 'text-success';
        break;
    case 'Rejected':
        $statusTitle = 'REJECTED REQUISITION';
        $statusClass = 'text-danger';
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Requisition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?php echo htmlspecialchars($backLink); ?>" class="btn btn-secondary mb-2">← Back</a>
            <h3 class="<?php echo $statusClass; ?>"><?php echo $statusTitle; ?></h3>
            <small class="text-muted">
                <?php echo htmlspecialchars($requisition['requisition_number'] ?? ''); ?>
            </small>
        </div>
        <div class="text-end">
            <strong><?php echo htmlspecialchars($userName); ?></strong><br>
            <small class="text-muted"><?php echo ucfirst($role); ?></small>
        </div>
    </div>

    <!-- DETAILS -->
    <div class="card shadow-sm p-4">
        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Submitted By</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($requisition['created_by'] ?? ''); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Requisition Title</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($requisition['title'] ?? ''); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Department</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($requisition['department'] ?? ''); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Amount (R)</strong></label>
                <input type="text" class="form-control" value="<?php echo number_format((float)($requisition['amount'] ?? 0), 2); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Payment Type</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($requisition['payment_type'] ?? ''); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>To Whom Payable</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($requisition['payable_to'] ?? ''); ?>" readonly>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label"><strong>Requisition Description</strong></label>
                <textarea class="form-control" rows="5" readonly><?php echo htmlspecialchars($requisition['description'] ?? ''); ?></textarea>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Expense Within Approved Budget</strong></label>
                <input type="text" class="form-control"
                       value="<?php echo !empty($requisition['budget_status']) ? htmlspecialchars($requisition['budget_status']) : 'Not Set'; ?>"
                       readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Status</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($status); ?>" readonly>
            </div>

            <!-- SUPPORTING DOCUMENT -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Supporting Document</strong></label>
                <div>
                    <?php if (!empty($requisition['document'])): ?>
                        <a href="uploads/<?php echo htmlspecialchars($requisition['document']); ?>"
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            View Document
                        </a>
                    <?php else: ?>
                        <span class="text-muted">No document uploaded</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PRINCIPAL REASON -->
            <?php if (!empty($requisition['principal_reason'])): ?>
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Principal Reason / Comment</strong></label>
                <textarea class="form-control" rows="3" readonly><?php echo htmlspecialchars($requisition['principal_reason']); ?></textarea>
            </div>
            <?php endif; ?>

            <!-- REJECTION -->
            <?php if ($status === 'Rejected'): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Rejected By</strong></label>
                <input type="text" class="form-control"
                       value="<?php echo htmlspecialchars($requisition['action_by'] ?? 'Unknown'); ?>"
                       readonly>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Rejection Reason</strong></label>
                <textarea class="form-control bg-danger-subtle" rows="3" readonly><?php
                    echo htmlspecialchars($requisition['rejection_reason'] ?? '');
                ?></textarea>
            </div>
            <?php endif; ?>

           <!-- ===================== -->
<!-- PAID: POP + INVOICE (Finance / Admin only) -->
<!-- ===================== -->
<?php if ($status === 'Paid' && in_array($role, ['finance', 'admin'])): ?>
<div class="col-12">
    <hr>
    <h5 class="mb-3">Payment Documents</h5>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label"><strong>Proof of Payment (POP)</strong></label>
    <div class="d-flex flex-wrap gap-2">
        <?php if (!empty($requisition['payment_proof'])): ?>
            <a href="uploads/<?php echo htmlspecialchars($requisition['payment_proof']); ?>"
               target="_blank" class="btn btn-outline-info btn-sm">
                View POP
            </a>
        <?php else: ?>
            <span class="text-muted align-self-center">No POP uploaded</span>
        <?php endif; ?>

        <a href="replace-pop.php?id=<?php echo (int)$requisition['id']; ?>"
           class="btn btn-warning btn-sm">
            <?php echo !empty($requisition['payment_proof']) ? 'Replace POP' : 'Upload POP'; ?>
        </a>
    </div>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label"><strong>Invoice</strong></label>
    <div>
        <?php if (!empty($requisition['invoice'])): ?>
            <a href="uploads/<?php echo htmlspecialchars($requisition['invoice']); ?>"
               target="_blank" class="btn btn-outline-success btn-sm">
                View Invoice
            </a>
        <?php else: ?>
            <span class="text-muted">No invoice uploaded yet</span>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($requisition['paid_by']) || !empty($requisition['paid_at'])): ?>
<div class="col-md-6 mb-3">
    <label class="form-label"><strong>Paid By</strong></label>
    <input type="text" class="form-control"
           value="<?php echo htmlspecialchars($requisition['paid_by'] ?? ''); ?>" readonly>
</div>
<div class="col-md-6 mb-3">
    <label class="form-label"><strong>Paid At</strong></label>
    <input type="text" class="form-control"
           value="<?php echo !empty($requisition['paid_at']) ? date('d M Y H:i', strtotime($requisition['paid_at'])) : ''; ?>"
           readonly>
</div>
<?php endif; ?>
<?php endif; ?>

        <!-- BOTTOM ACTIONS -->
        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="<?php echo htmlspecialchars($backLink); ?>" class="btn btn-secondary">Back</a>

            <?php if (in_array($status, ['Approved', 'Paid'])): ?>
                <a href="export-approved-pdf.php?id=<?php echo (int)$requisition['id']; ?>"
                   class="btn btn-success" target="_blank">
                    Download PDF
                </a>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>