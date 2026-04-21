<?php
include 'includes/db.php';

$result = $conn->query("SELECT * FROM requisitions WHERE status='pending' ORDER BY id DESC");
?>

<table class="table">
<thead>
<tr>
<th>Number</th>
<th>Title</th>
<th>Department</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
<?php while($r = $result->fetch_assoc()): ?>
<tr>
<td><?php echo $r['requisition_number']; ?></td>
<td><?php echo $r['title']; ?></td>
<td><?php echo $r['department']; ?></td>
<td>
<a href="principal-view.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary">View</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>