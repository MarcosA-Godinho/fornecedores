<?php
require 'vendor/autoload.php'; // Usa o autoload do Composer
include 'conexao.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// Adicione estas duas classes para formatação
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$fornecedor_id = $_GET['fornecedor_id'] ?? null;
if (!$fornecedor_id) {
    die("Fornecedor não encontrado.");
}

// ALTERAÇÃO 1: CORREÇÃO DE SEGURANÇA (SQL INJECTION) ---
// Prepara a consulta para evitar SQL Injection
$sql = "SELECT * FROM faturas WHERE fornecedor_id = ?";
$stmt = $con->prepare($sql);

// Associa o parâmetro :fornecedor_id ao valor da variável. "i" significa que é um inteiro.
$stmt->bind_param("i", $fornecedor_id);

// Executa a consulta
$stmt->execute();

// Pega os resultados
$faturas = $stmt->get_result();
// FIM DA ALTERAÇÃO DE SEGURANÇA ---


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Cabeçalhos
$sheet->setCellValue('A1', 'Mês');
$sheet->setCellValue('B1', 'Data de Emissão');
$sheet->setCellValue('C1', 'Data de Vencimento');
$sheet->setCellValue('D1', 'Valor');
$sheet->setCellValue('E1', 'Detalhes');
$sheet->setCellValue('F1', 'Filial');
$sheet->setCellValue('G1', 'Pedido');

// BÔNUS: Deixa o cabeçalho em negrito
$sheet->getStyle('A1:G1')->getFont()->setBold(true);


$row = 2;
while ($f = $faturas->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $f['mes']);

    // ALTERAÇÃO 2: FORMATAÇÃO CORRETA DAS DATAS ---
    // Formata a Data de Emissão
    if (!empty($f['data_emissao'])) {
        $excelDateEmissao = Date::PHPToExcel($f['data_emissao']);
        $sheet->setCellValue('B' . $row, $excelDateEmissao);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
    }
    
    // Formata a Data de Vencimento
    if (!empty($f['data_vencimento'])) {
        $excelDateVencimento = Date::PHPToExcel($f['data_vencimento']);
        $sheet->setCellValue('C' . $row, $excelDateVencimento);
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
    }
    // FIM DA ALTERAÇÃO DE DATAS ---

    $sheet->setCellValue('D' . $row, $f['valor']);
    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Formata valor como número
    
    $sheet->setCellValue('E' . $row, $f['detalhes']);
    $sheet->setCellValue('F' . $row, $f['filial']);
    $sheet->setCellValue('G' . $row, $f['pedido']);
    $row++;
}

// BÔNUS: Ajusta a largura das colunas automaticamente
foreach (range('A', 'G') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}


// Enviar para o navegador
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="faturas_fornecedor_' . $fornecedor_id . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;