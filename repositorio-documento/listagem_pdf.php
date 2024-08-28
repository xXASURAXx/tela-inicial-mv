<?php
// Incluir a conexão ao banco de dados
include 'conexao.php';

// Verificar se um documento deve ser excluído
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Verificar se o ID é válido e é um número
    if (is_numeric($delete_id)) {
        // Excluir o documento do banco de dados
        $delete_stmt = $pdo->prepare("DELETE FROM documento_pdf WHERE id = :id");
        $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);

        if ($delete_stmt->execute()) {
            // Redirecionar após exclusão bem-sucedida
            header("Location: listagem_pdf.php?message=deleted");
            exit();
        } else {
            echo "Erro ao excluir o documento.";
        }
    } else {
        echo "ID inválido.";
    }
}

// Consultar todos os documentos para exibição
$query = "SELECT id, nome_documento, sistema_documento, assunto_documento FROM documento_pdf";
$stmt = $pdo->prepare($query);
$stmt->execute();
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Documentos PDF</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Arredondar as bordas da tabela */
        table {
            border-radius: 15px;
            overflow: hidden;
            margin-top: 20px;
        }

        /* Arredondar as bordas das células da tabela */
        th, td {
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <h1>Lista de Documentos PDF</h1>

    <!-- Exibir mensagens, se houver -->
    <?php if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
        <p style="color: green;">Documento excluído com sucesso!</p>
    <?php endif; ?>

    <!-- Exibir a lista de documentos -->
    <?php if (!empty($documentos)): ?>
    <table>
        <tr>
            <th>Nome do Documento</th>
            <th>Sistema do Documento</th>
            <th>Assunto do Documento</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($documentos as $documento): ?>
            <tr>
                <td><?php echo htmlspecialchars($documento['nome_documento']); ?></td>
                <td><?php echo htmlspecialchars($documento['sistema_documento']); ?></td>
                <td><?php echo htmlspecialchars($documento['assunto_documento']); ?></td>
                <td>
                    <a href="download.php?id=<?php echo $documento['id']; ?>">Baixar PDF</a> |
                    <a href="listagem_pdf.php?delete_id=<?php echo $documento['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este documento?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p>Nenhum documento encontrado.</p>
    <?php endif; ?>
</body>
</html>
