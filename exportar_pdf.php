<?php
require 'bibliotecas/fpdf/fpdf.php';
include 'conexao.php';

$fornecedor_id = $_GET['fornecedor_id'] ?? null;
if (!$fornecedor_id) {
    die("Fornecedor não encontrado.");
}

$fornecedor = $con->query("SELECT * FROM fornecedores WHERE id = $fornecedor_id")->fetch_assoc();
$faturas = $con->query("SELECT * FROM faturas WHERE fornecedor_id = $fornecedor_id");

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Aplica utf8_decode em todos os textos
$pdf->Cell(0, 10, utf8_decode('Relatório de Faturas - ' . $fornecedor['nome']), 0, 1, 'C');

// Cabeçalho da tabela
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(25, 10, utf8_decode('Mês'), 1, 0, 'C');
$pdf->Cell(28, 10, utf8_decode('Emissão'), 1, 0, 'C');
$pdf->Cell(28, 10, utf8_decode('Vencimento'), 1, 0, 'C');
$pdf->Cell(28, 10, utf8_decode('Valor'), 1, 0, 'C');
$pdf->Cell(30, 10, utf8_decode('Detalhes'), 1, 0, 'C');
$pdf->Cell(13, 10, utf8_decode('Filial'), 1, 0, 'C');
$pdf->Cell(22, 10, utf8_decode('Pedido'), 1, 0, 'C');
$pdf->Ln();

// Conteúdo das faturas
$pdf->SetFont('Arial', '', 11);
while ($f = $faturas->fetch_assoc()) {
    $pdf->Cell(25, 10, utf8_decode($f['mes']), 1);
    $pdf->Cell(28, 10, utf8_decode(date('d/m/Y', strtotime($f['data_emissao']))), 1, 0, 'C');
    $pdf->Cell(28, 10, utf8_decode(date('d/m/Y', strtotime($f['data_vencimento']))), 1, 0, 'C');
    $pdf->Cell(28, 10, utf8_decode(number_format($f['valor'], 2, ',', '.')), 1, 0, 'C');
    $pdf->Cell(30, 10, utf8_decode($f['detalhes']), 1, 0, 'C'); // Detalhes
    $pdf->Cell(13, 10, utf8_decode((int)($f['filial'] ?? '')), 1, 0, 'C'); // Filial
    $pdf->Cell(22, 10, utf8_decode((int)($f['pedido'] ?? '')), 1, 0, 'C'); // Pedido
    $pdf->Ln();
}

$pdf->Output();