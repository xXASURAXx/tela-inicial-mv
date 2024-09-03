<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT pdf_documento FROM documento_pdf WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $documento = $stmt->fetch(PDO::FETCH_ASSOC);

        header("Content-type: application/pdf");
        echo $documento['pdf_documento'];
    } else {
        echo "Documento não encontrado.";
    }
} else {
    echo "ID do documento não fornecido.";
}
?>
