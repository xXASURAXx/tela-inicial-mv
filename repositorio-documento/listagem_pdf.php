<?php
include 'conexao.php';

function fetchDocuments($tableName, $pdo) {
    $query = "SELECT id, nome_documento, sistema_documento, assunto_documento FROM $tableName";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$documentos_pdf = fetchDocuments('documento_pdf', $pdo);
$documentos_mv = fetchDocuments('documento_mv', $pdo);
$documentos_release_notes = fetchDocuments('documento_release_notes', $pdo);

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $table = $_GET['table'];

    if (is_numeric($delete_id) && in_array($table, ['documento_pdf', 'documento_mv', 'documento_release_notes'])) {
        $delete_stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
        $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);

        if ($delete_stmt->execute()) {
            header("Location: listagem.php?message=deleted&table=$table");
            exit();
        } else {
            echo "Erro ao excluir o documento.";
        }
    } else {
        echo "ID ou tabela inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Documentos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            border-radius: 15px;
            overflow: hidden;
            margin-top: 20px;
        }
        .tab {
            display: inline-block;
            margin-right: 10px;
            cursor: pointer;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .tab-content {
            display: none;
        }
        .active {
            background-color: #f0f0f0;
        }
    </style>
    <script>
        function showTab(tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tab");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.className += " active";
        }
    </script>
</head>
<body>
    <h1>Administrador de Documentos</h1>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
        <p style="color: green;">Documento excluído com sucesso!</p>
    <?php endif; ?>

    <div>
        <div class="tab active" onclick="showTab('documento_pdf')">POP Hospital</div>
        <div class="tab" onclick="showTab('documento_mv')">Manuais MV</div>
        <div class="tab" onclick="showTab('documento_release_notes')">Release Notes</div>
    </div>

    <div id="documento_pdf" class="tab-content" style="display: block;">
        <h2>Documentos PDF</h2>
        <?php if (!empty($documentos_pdf)): ?>
        <table>
            <tr>
                <th>Nome do Documento</th>
                <th>Sistema do Documento</th>
                <th>Assunto do Documento</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($documentos_pdf as $documento): ?>
                <tr>
                    <td><?php echo htmlspecialchars($documento['nome_documento']); ?></td>
                    <td><?php echo htmlspecialchars($documento['sistema_documento']); ?></td>
                    <td><?php echo htmlspecialchars($documento['assunto_documento']); ?></td>
                    <td>
                        <a href="download.php?id=<?php echo $documento['id']; ?>">Baixar PDF</a> |
                        <a href="listagem.php?delete_id=<?php echo $documento['id']; ?>&table=documento_pdf" onclick="return confirm('Tem certeza que deseja excluir este documento?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Nenhum documento encontrado.</p>
        <?php endif; ?>
    </div>

    <div id="documento_mv" class="tab-content">
        <h2>Documentos MV</h2>
        <?php if (!empty($documentos_mv)): ?>
        <table>
            <tr>
                <th>Nome do Documento</th>
                <th>Sistema do Documento</th>
                <th>Assunto do Documento</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($documentos_mv as $documento): ?>
                <tr>
                    <td><?php echo htmlspecialchars($documento['nome_documento']); ?></td>
                    <td><?php echo htmlspecialchars($documento['sistema_documento']); ?></td>
                    <td><?php echo htmlspecialchars($documento['assunto_documento']); ?></td>
                    <td>
                        <a href="download.php?id=<?php echo $documento['id']; ?>">Baixar PDF</a> |
                        <a href="listagem.php?delete_id=<?php echo $documento['id']; ?>&table=documento_mv" onclick="return confirm('Tem certeza que deseja excluir este documento?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Nenhum documento encontrado.</p>
        <?php endif; ?>
    </div>

    <div id="documento_release_notes" class="tab-content">
        <h2>Release Notes</h2>
        <?php if (!empty($documentos_release_notes)): ?>
        <table>
            <tr>
                <th>Nome do Documento</th>
                <th>Sistema do Documento</th>
                <th>Assunto do Documento</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($documentos_release_notes as $documento): ?>
                <tr>
                    <td><?php echo htmlspecialchars($documento['nome_documento']); ?></td>
                    <td><?php echo htmlspecialchars($documento['sistema_documento']); ?></td>
                    <td><?php echo htmlspecialchars($documento['assunto_documento']); ?></td>
                    <td>
                        <a href="download.php?id=<?php echo $documento['id']; ?>">Baixar PDF</a> |
                        <a href="listagem.php?delete_id=<?php echo $documento['id']; ?>&table=documento_release_notes" onclick="return confirm('Tem certeza que deseja excluir este documento?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Nenhum documento encontrado.</p>
        <?php endif; ?>
    </div>

    <script>
        document.querySelector('.tab').click();
    </script>
</body>
</html>