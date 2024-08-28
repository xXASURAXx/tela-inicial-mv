<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_documento = $_POST['nome_documento'];
    $sistema_documento = $_POST['sistema_documento'];
    $assunto_documento = $_POST['assunto_documento'];
    
    if (isset($_FILES['pdf_documento']) && $_FILES['pdf_documento']['error'] == 0) {
        $pdf_nome = $_FILES['pdf_documento']['name'];
        $pdf_tipo = $_FILES['pdf_documento']['type'];
        $pdf_tamanho = $_FILES['pdf_documento']['size'];
        $pdf_temporario = $_FILES['pdf_documento']['tmp_name'];

        
        if ($pdf_tipo == 'application/pdf') {
            
            $pdf_conteudo = file_get_contents($pdf_temporario);

            try {
                
                $stmt = $pdo->prepare("INSERT INTO documento_pdf (nome_documento, sistema_documento, assunto_documento, pdf_documento) VALUES (:nome_documento, :sistema_documento, :assunto_documento, :pdf_documento)");
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
    <title>Upload de PDF</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Centralizar o formulário de upload */
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 50px; /* Adicionar padding superior para subir os elementos */
            padding-bottom: 20px;
        }

        /* Área de arrastar e soltar */
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

        /* Ocultar o input de arquivo padrão */
        #file {
            display: none;
        }

        /* Centralizar o botão */
        .cssbuttons-io {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h1>Carregar Documento PDF</h1>
    <div class="form-container">
        <form id="uploadForm" action="upload_pdf.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="nome_documento" class="input" placeholder="Nome do Documento" required><br>
            <input type="text" name="sistema_documento" class="input" placeholder="Sistema do Documento" required><br>
            <input type="text" name="assunto_documento" class="input" placeholder="Assunto do Documento" required><br>

            <!-- Área de Drag-and-Drop -->
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

    <script>
        // Selecionar elementos
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('file');

        // Adicionar eventos de drag-and-drop
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
            // Obter o arquivo que foi solto
            const file = e.dataTransfer.files[0];
            // Adicionar o arquivo ao input de arquivo
            fileInput.files = e.dataTransfer.files;
            dropZone.querySelector('p').textContent = file.name; // Atualizar o texto para mostrar o nome do arquivo
        });

        // Também permitir o clique na área de drag-and-drop
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        // Atualizar o texto da área de drag-and-drop quando o arquivo for selecionado manualmente
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                dropZone.querySelector('p').textContent = fileInput.files[0].name;
            }
        });
    </script>
</body>
</html>
