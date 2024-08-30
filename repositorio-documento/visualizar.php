<?php
// Incluir a conexão ao banco de dados
include 'conexao.php';

// Verificar se o ID foi passado
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Buscar o documento no banco de dados
    $stmt = $pdo->prepare("SELECT pdf_documento FROM documento_pdf WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Verificar se o documento foi encontrado
    if ($stmt->rowCount() > 0) {
        $documento = $stmt->fetch(PDO::FETCH_ASSOC);

        // Exibir o PDF no navegador
        header("Content-type: application/pdf");
        echo $documento['pdf_documento'];
    } else {
        echo "Documento não encontrado.";
    }
} else {
    echo "ID do documento não fornecido.";
}
?>
