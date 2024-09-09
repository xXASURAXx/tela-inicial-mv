<?php
include 'conexao.php';

$query = "SELECT id, nome_documento, sistema_documento, assunto_documento FROM documento_release_notes ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Release Notes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            background-color: #f4f4f9;
            padding-top: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            border-radius: 15px;
            overflow: hidden;
            margin-top: 20px;
            width: 80%;
        }

        table, th, td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 10px;
        }

        th, td {
            vertical-align: middle;
        }

        td.action-buttons {
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

        .button-content {
            position: relative;
            z-index: 1;
            font-size: 12px;
        }

        .button {
            position: relative;
            overflow: hidden;
            height: 3rem;
            padding: 0 2rem;
            border-radius: 1.5rem;
            background: #3d3a4e;
            background-size: 400%;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 15px;
            box-shadow: none;
            text-decoration: none;
            transform: translateY(-10px);
        }

        .button:hover::before {
            transform: scaleX(1);
        }

        .button::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            transform: scaleX(0);
            transform-origin: 0 50%;
            width: 100%;
            height: inherit;
            border-radius: inherit;
            background: linear-gradient(
                82.3deg,
                rgba(150, 93, 233, 1) 10.8%,
                rgba(99, 88, 238, 1) 94.3%
            );
            transition: all 0.475s;
        }

    </style>
</head>
<body>
    <h1>Release Notes</h1>

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
                <td class="action-buttons">
                    <a href="visualizar.php?id=<?php echo $documento['id']; ?>" target="_blank">
                        <button class="button">
                            <span class="button-content">Visualizar</span>
                        </button>
                    </a>
                    <a href="download.php?id=<?php echo $documento['id']; ?>">
                        <button class="button">
                            <span class="button-content">Download</span>
                        </button>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p>Nenhum documento encontrado.</p>
    <?php endif; ?>
</body>
</html>
