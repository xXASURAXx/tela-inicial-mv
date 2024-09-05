<?php
include 'conexao.php';

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$query = isset($_GET['query']) ? $_GET['query'] : '';

if (!empty($tipo) && !empty($query)) {
    if ($tipo == 'sistema') {
        $stmt = $pdo->prepare("SELECT DISTINCT sistema_documento FROM documento_pdf WHERE sistema_documento LIKE :query LIMIT 10");
    } elseif ($tipo == 'assunto') {
        $stmt = $pdo->prepare("SELECT DISTINCT assunto_documento FROM documento_pdf WHERE assunto_documento LIKE :query LIMIT 10");
    }

    $stmt->bindValue(':query', "%$query%");
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($resultados);
}
?>
