<?php
require 'vendor/autoload.php';
include 'conexao.php';

// ------------------------------------------------------------------
// PONTO 1: CLASSE PDF CUSTOMIZADA PARA LAYOUT PROFISSIONAL
// ------------------------------------------------------------------
class PDF extends FPDF
{
    private $nomeFornecedor = '';

    // Setter para guardar o nome do fornecedor
    function setNomeFornecedor($nome)
    {
        $this->nomeFornecedor = $this->trataTexto($nome);
    }

    // Função helper para tratar acentuação para o FPDF
    public function trataTexto($texto)
    {
        return utf8_decode($texto);
    }

    // Cabeçalho (será repetido em toda nova página)
    function Header()
    {
        // Opcional: Adicione seu logo
        // $this->Image('path/para/seu/logo.png', 10, 6, 30);

        // Título do Relatório
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80); // Mover para a direita
        $this->Cell(30, 10, $this->trataTexto('Relatório de Faturas'), 0, 0, 'C');
        $this->Ln(5); // Pular linha
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(80);
        $this->Cell(30, 10, $this->nomeFornecedor, 0, 0, 'C');
        $this->Ln(15); // Pular linha antes do conteúdo
    }

    // Rodapé (será repetido em toda nova página)
    function Footer()
    {
        $this->SetY(-15); // Posição a 1.5 cm do final
        $this->SetFont('Arial', 'I', 8);
        
        // Data de geração do relatório
        $this->Cell(0, 10, $this->trataTexto('Gerado em: ') . date('d/m/Y H:i:s'), 0, 0, 'L');
        
        // Número da página
        $this->Cell(0, 10, $this->trataTexto('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // Função para criar o cabeçalho da tabela
    function CabecalhoTabela()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(230, 230, 230); // Fundo cinza claro
        $this->Cell(20, 7, $this->trataTexto('Mês'), 1, 0, 'C', true);
        $this->Cell(25, 7, $this->trataTexto('Emissão'), 1, 0, 'C', true);
        $this->Cell(25, 7, $this->trataTexto('Vencimento'), 1, 0, 'C', true);
        $this->Cell(30, 7, $this->trataTexto('Valor (R$)'), 1, 0, 'C', true);
        $this->Cell(50, 7, $this->trataTexto('Detalhes'), 1, 0, 'C', true);
        $this->Cell(15, 7, $this->trataTexto('Filial'), 1, 0, 'C', true);
        $this->Cell(20, 7, $this->trataTexto('Pedido'), 1, 0, 'C', true);
        $this->Ln();
    }
}


// ------------------------------------------------------------------
// PONTO 2: CÓDIGO PRINCIPAL (MAIS LIMPO E SEGURO)
// ------------------------------------------------------------------

$fornecedor_id = $_GET['fornecedor_id'] ?? null;
if (!$fornecedor_id) {
    die("Fornecedor não encontrado.");
}

// --- CORREÇÃO DE SEGURANÇA (SQL INJECTION) ---
// Busca nome do fornecedor
$sql_fornecedor = "SELECT nome FROM fornecedores WHERE id = ?";
$stmt_fornecedor = $con->prepare($sql_fornecedor);
$stmt_fornecedor->bind_param("i", $fornecedor_id);
$stmt_fornecedor->execute();
$fornecedor = $stmt_fornecedor->get_result()->fetch_assoc();

if (!$fornecedor) {
    die("ID de fornecedor inválido.");
}

// Busca faturas do fornecedor
$sql_faturas = "SELECT * FROM faturas WHERE fornecedor_id = ?";
$stmt_faturas = $con->prepare($sql_faturas);
$stmt_faturas->bind_param("i", $fornecedor_id);
$stmt_faturas->execute();
$faturas = $stmt_faturas->get_result();
// --- FIM DA CORREÇÃO DE SEGURANÇA ---


// Inicia o PDF usando nossa nova classe
$pdf = new PDF();
$pdf->setNomeFornecedor($fornecedor['nome']); // Passa o nome para o cabeçalho
$pdf->AliasNbPages(); // Habilita a contagem total de páginas {nb}
$pdf->AddPage('L', 'A4'); // 'L' para paisagem (melhor para tabelas largas)
$pdf->CabecalhoTabela();

// Conteúdo das faturas
$pdf->SetFont('Arial', '', 9);
$total_faturas = 0;

while ($f = $faturas->fetch_assoc()) {
    $pdf->Cell(20, 6, $pdf->trataTexto($f['mes']), 1);
    $pdf->Cell(25, 6, date('d/m/Y', strtotime($f['data_emissao'])), 1, 0, 'C');
    $pdf->Cell(25, 6, date('d/m/Y', strtotime($f['data_vencimento'])), 1, 0, 'C');
    
    // Formata o valor e alinha à direita
    $valor_formatado = number_format($f['valor'], 2, ',', '.');
    $pdf->Cell(30, 6, $valor_formatado, 1, 0, 'R');
    
    $pdf->Cell(50, 6, $pdf->trataTexto($f['detalhes']), 1); 
    $pdf->Cell(15, 6, $pdf->trataTexto($f['filial']), 1, 0, 'C'); 
    $pdf->Cell(20, 6, $pdf->trataTexto($f['pedido']), 1, 0, 'C'); 
    $pdf->Ln();

    $total_faturas += $f['valor'];
}

// Linha de totalização
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(70, 8, $pdf->trataTexto('Total Geral:'), 1, 0, 'R');
$pdf->Cell(30, 8, number_format($total_faturas, 2, ',', '.'), 1, 0, 'R');
$pdf->Cell(85, 8, '', 'T'); // Apenas a borda superior para alinhar
$pdf->Ln();


$pdf->Output('I', 'Relatorio_Faturas_' . $fornecedor_id . '.pdf');

?>