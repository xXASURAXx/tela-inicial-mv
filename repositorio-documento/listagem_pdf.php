<?php
include 'conexao.php';

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
        table {
            border-radius: 15px;
            overflow: hidden;
            margin-top: 20px;
        }

        th, td {
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <h1>Lista de Documentos PDF</h1>

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
                    <a href="listagem_pdf.php?delete_id=<?php echo $documento['nome_documento']; ?>" onclick="return confirm('Tem certeza que deseja excluir este documento?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p>Nenhum documento encontrado.</p>
    <?php endif; ?>
</body>
</html>
