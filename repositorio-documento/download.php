<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT nome_documento, pdf_documento FROM documento_pdf WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $documento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($documento) {
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=" . $documento['nome_documento'] . ".pdf");
        echo $documento['pdf_documento'];
    } else {
        echo "Documento não encontrado.";
    }
} else {
    echo "ID inválido.";
}
?>
