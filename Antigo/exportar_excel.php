<?php
require 'vendor/autoload.php'; // Usa o autoload do Composer
include 'conexao.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$fornecedor_id = $_GET['fornecedor_id'] ?? null;
if (!$fornecedor_id) {
    die("Fornecedor não encontrado.");
}

$sql = "SELECT * FROM faturas WHERE fornecedor_id = $fornecedor_id";
$faturas = $con->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ✅ Cabeçalhos
$sheet->setCellValue('A1', 'Mês');
$sheet->setCellValue('B1', 'Data de Emissão');
$sheet->setCellValue('C1', 'Data de Vencimento');
$sheet->setCellValue('D1', 'Valor');
$sheet->setCellValue('E1', 'Detalhes');
$sheet->setCellValue('F1', 'Filial');
$sheet->setCellValue('G1', 'Pedido');

// ✅ Dados
$row = 2;
while ($f = $faturas->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $f['mes']);
    $sheet->setCellValue('B' . $row, $f['data_emissao']);
    $sheet->setCellValue('C' . $row, $f['data_vencimento']);
    $sheet->setCellValue('D' . $row, $f['valor']);
	$sheet->setCellValue('E' . $row, $f['detalhes']);
	$sheet->setCellValue('F' . $row, $f['filial']);
	$sheet->setCellValue('G' . $row, $f['pedido']);
    $row++;
}

// ✅ Enviar para o navegador
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="faturas_fornecedor_' . $fornecedor_id . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
