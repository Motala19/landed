<?php
session_start();
require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    exit("Invalid requisition ID");
}

$stmt = $conn->prepare("SELECT * FROM requisitions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$req = $result->fetch_assoc();

if (!$req) {
    exit("Requisition not found");
}

// ==================== CREATE PDF ====================
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Midrand Primary School');
$pdf->SetAuthor('Requisition System');
$pdf->SetTitle('Requisition #' . ($req['requisition_number'] ?? $id));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);

$pdf->AddPage();

// ====================== HEADER ======================
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'MIDRAND PRIMARY SCHOOL', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, 'OFFICIAL REQUISITION FORM', 0, 1, 'C');

// Logo (lower down)
$pdf->Image('assets/images/logo.png', 85, 45, 38, '', '', '', '', false, 300);
$pdf->Ln(55);

$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 8, 'REQUISITION NUMBER: ' . ($req['requisition_number'] ?? 'N/A'), 0, 1, 'C');
$pdf->Ln(4);

// ====================== MAIN CONTENT ======================
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(31,58,95);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(0, 8, 'REQUISITION DETAILS', 1, 1, 'L', true);

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('helvetica', '', 11);

function fieldRow($pdf, $label, $value) {
    $pdf->Cell(65, 8, $label, 1, 0);
    $pdf->Cell(110, 8, $value, 1, 1);
}

fieldRow($pdf, 'Submitted By:', $req['created_by'] ?? '');
fieldRow($pdf, 'Requisition Title:', $req['title'] ?? '');
fieldRow($pdf, 'Department:', $req['department'] ?? '');
fieldRow($pdf, 'Amount (R):', 'R ' . number_format($req['amount'] ?? 0, 2));
fieldRow($pdf, 'Payment Type:', $req['payment_type'] ?? '');
fieldRow($pdf, 'To Whom Payable:', $req['payable_to'] ?? '');
fieldRow($pdf, 'Expense Within Budget:', $req['budget_status'] ?? 'Not Set');

$pdf->Ln(3);

// Description
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 8, 'Requisition Description', 1, 1, 'L');
$pdf->SetFont('helvetica', '', 10.5);
$pdf->MultiCell(0, 0, $req['description'] ?? 'No description provided.', 1, 'L');
$pdf->Ln(3);

// Supporting Document
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 8, 'Supporting Document', 1, 1, 'L');
$pdf->SetFont('helvetica', '', 10.5);
$pdf->MultiCell(0, 0, !empty($req['document']) ? 'Document uploaded: ' . $req['document'] : 'No document uploaded', 1, 'L');
$pdf->Ln(3);

// Finance Rejection
if (!empty($req['rejection_reason'])) {
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 8, 'Finance Rejection Reason', 1, 1, 'L');
    $pdf->SetFont('helvetica', '', 10.5);
    $pdf->MultiCell(0, 0, $req['rejection_reason'], 1, 'L');
    $pdf->Ln(3);
}

// Principal Approval/Reject
if ($req['status'] == 'Approved') {
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 8, 'Principal Approval Reason', 1, 1, 'L');
    $pdf->SetFont('helvetica', '', 10.5);
    $pdf->MultiCell(0, 0, $req['principal_reason'] ?? 'Approved without comment', 1, 'L');
    $pdf->Ln(3);
}
if ($req['status'] == 'Rejected') {
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 8, 'Principal Rejection Reason', 1, 1, 'L');
    $pdf->SetFont('helvetica', '', 10.5);
    $pdf->MultiCell(0, 0, $req['principal_reason'] ?? $req['rejection_reason'] ?? '', 1, 'L');
    $pdf->Ln(3);
}

// ====================== SIGNATURE SECTION ======================
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 8, 'Approval and Signatures', 1, 1, 'L');

$pdf->SetFont('helvetica', '', 11);
$colWidth = 63;

$pdf->Cell($colWidth, 8, 'Requested By:', 1, 0);
$pdf->Cell($colWidth, 8, 'Verified By:', 1, 0);
$pdf->Cell($colWidth, 8, 'Approved By:', 1, 1);

$pdf->Cell($colWidth, 12, 'Mrs. C Machethe', 1, 0);
$pdf->Cell($colWidth, 12, 'Mr. M Mulaudzi', 1, 0);
$pdf->Cell($colWidth, 12, 'Ms. B Mahlangu', 1, 1);

// ====================== FOOTER LINE ======================
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 8, 'This document is officially approved by Midrand Primary School.', 0, 1, 'C');

$pdf->Output('Requisition_' . ($req['requisition_number'] ?? $id) . '.pdf', 'D');
exit;
?>
