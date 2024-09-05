<?php
include 'conexao.php';

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$query = isset($_GET['query']) ? $_GET['query'] : '';

if ($tipo == 'assunto' && !empty($query)) {
    $stmt = $pdo->prepare("SELECT DISTINCT assunto_documento FROM documento_pdf WHERE assunto_documento LIKE :query LIMIT 10");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($result);
} elseif ($tipo == 'sistema') {
    $stmt = $pdo->query("SELECT DISTINCT sistema_documento FROM documento_pdf");
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($result);
}
?>
