<?php
session_start();

// Verifica se o usuário está logado e pertence ao setor de TI
if (!isset($_SESSION['loggedin']) || $_SESSION['setor'] !== 'Tecnologia da Informação') {
    // Redireciona para a página de login se não tiver acesso
    header('Location: login.php');
    exit;
}

include 'conexao.php';

$usuario = $_SESSION['username'];

// Função para buscar documentos
function fetchDocuments($tableName, $pdo) {
    $query = "SELECT id, nome_documento, sistema_documento, assunto_documento FROM $tableName";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$documentos_pdf = fetchDocuments('documento_pdf', $pdo);
$documentos_mv = fetchDocuments('documento_mv', $pdo);
$documentos_release_notes = fetchDocuments('documento_release_notes', $pdo);

// Função para excluir documento
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

// Função para carregar documento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_documento = $_POST['nome_documento'];
    $sistema_documento = $_POST['sistema_documento'];
    $assunto_documento = $_POST['assunto_documento'];
    $tabela = $_POST['tabela'];

    if (isset($_FILES['pdf_documento']) && $_FILES['pdf_documento']['error'] == 0) {
        $pdf_nome = $_FILES['pdf_documento']['name'];
        $pdf_tipo = $_FILES['pdf_documento']['type'];
        $pdf_tamanho = $_FILES['pdf_documento']['size'];
        $pdf_temporario = $_FILES['pdf_documento']['tmp_name'];

        if ($pdf_tipo == 'application/pdf') {
            $pdf_conteudo = file_get_contents($pdf_temporario);

            try {
                $stmt = $pdo->prepare("INSERT INTO $tabela (nome_documento, sistema_documento, assunto_documento, pdf_documento) VALUES (:nome_documento, :sistema_documento, :assunto_documento, :pdf_documento)");
                $stmt->bindParam(':nome_documento', $nome_documento);
                $stmt->bindParam(':sistema_documento', $sistema_documento);
                $stmt->bindParam(':assunto_documento', $assunto_documento);
                $stmt->bindParam(':pdf_documento', $pdf_conteudo, PDO::PARAM_LOB);

                if ($stmt->execute()) {
                    echo "Documento carregado com sucesso!";
                } else {
                    echo "Erro ao carregar o documento.";
                }
            } catch (PDOException $e) {
                echo "Erro ao inserir no banco de dados: " . $e->getMessage();
            }
        } else {
            echo "Por favor, carregue um arquivo PDF válido.";
        }
    } else {
        echo "Erro ao enviar o arquivo. Código de erro: " . $_FILES['pdf_documento']['error'];
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
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 50px;
            padding-bottom: 20px;
        }

        .drop-zone {
            width: 100%;
            max-width: 400px;
            height: 200px;
            padding: 20px;
            border: 2px dashed #003366;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 18px;
            color: #003366;
            background-color: #f0f8ff;
            transition: background-color 0.2s ease;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .drop-zone:hover {
            background-color: #e0f0ff;
        }

        .drop-zone.dragover {
            background-color: #c0e0ff;
        }

        .drop-zone p {
            margin: 0;
        }

        #file {
            display: none;
        }

        .cssbuttons-io {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
        }

        .floating-select {
            margin-bottom: 20px;
        }

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

        /* Estilo do botão de Home */
        .home-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            position: fixed;
            top: 20px;
            left: 20px;
            cursor: pointer;
            z-index: 1000;
        }

        .home-button:hover {
            background-color: #45a049;
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

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('file');

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            fileInput.files = e.dataTransfer.files;
            dropZone.querySelector('p').textContent = file.name;
        });

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                dropZone.querySelector('p').textContent = fileInput.files[0].name;
            }
        });
    </script>
</head>
<body>
    <!-- Botão Home no canto superior esquerdo -->
    <a href="adm.php" class="home-button">Home</a>

    <h1>Administrador de Documentos</h1>

    <div class="form-container">
        <form id="uploadForm" action="listagem.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="nome_documento" class="input" placeholder="Nome do Documento" required><br>
            <input type="text" name="sistema_documento" class="input" placeholder="Sistema do Documento" required><br>
            <input type="text" name="assunto_documento" class="input" placeholder="Assunto do Documento" required><br>

            <div class="floating-select">
                <label for="tabela"></label>
                <select name="tabela" id="tabela" class="input" required>
                    <option value="documento_pdf">POP Hospital</option>
                    <option value="documento_mv">Documento MV</option>
                    <option value="documento_release_notes">Release Notes</option>
                </select>
            </div>

            <div class="drop-zone" id="dropZone">
                <p>Arraste e solte o arquivo aqui ou clique para selecionar</p>
                <input id="file" type="file" name="pdf_documento" accept="application/pdf" required>
            </div>

            <button class="cssbuttons-io" type="submit">
                <span>Carregar
                    <svg viewBox="0 0 19.9 19.7" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title desc" class="svg-icon search-icon">
                        <title>Upload Icon</title>
                        <desc id="desc">A magnifying glass icon.</desc>
                        <g stroke="white" fill="none" class="search-path">
                            <path d="M18.5 18.3l-5.4-5.4" stroke-linecap="square"></path>
                            <circle r="7" cy="8" cx="8"></circle>
                        </g>
                    </svg>
                </span>
            </button>
        </form>
    </div>

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
