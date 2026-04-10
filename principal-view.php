<?php
include 'includes/db.php';

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM requisitions WHERE id=$id");
$r = $result->fetch_assoc();
?>

<form method="POST" action="principal-process.php">

<input type="hidden" name="id" value="<?php echo $r['id']; ?>">

<!-- READ ONLY FIELDS -->
<input value="<?php echo $r['title']; ?>" disabled class="form-control mb-2">
<input value="<?php echo $r['department']; ?>" disabled class="form-control mb-2">
<textarea disabled class="form-control mb-2"><?php echo $r['description']; ?></textarea>

<!-- BUDGET STATUS -->
<div class="mb-3">
<label>Within Budget?</label>
<input value="<?php echo $r['budget_status']; ?>" disabled class="form-control bg-success text-white">
</div>

<!-- 🔥 IF NOT WITHIN BUDGET -->
<div class="mb-3">
<label>Reason for Approving Outside Budget</label>
<textarea name="approve_reason" class="form-control"></textarea>
</div>

<!-- REJECTION REASON -->
<div class="mb-3">
<label>Reason for Rejection</label>
<textarea name="reject_reason" class="form-control"></textarea>
</div>

<button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
<button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>

</form>