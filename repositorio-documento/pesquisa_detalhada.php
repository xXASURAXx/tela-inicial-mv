<?php
// Incluir a conexão ao banco de dados
include 'conexao.php';

// Inicializar variáveis de pesquisa
$nome_documento = isset($_GET['nome_documento']) ? $_GET['nome_documento'] : '';
$sistema_documento = isset($_GET['sistema_documento']) ? $_GET['sistema_documento'] : '';
$assunto_documento = isset($_GET['assunto_documento']) ? $_GET['assunto_documento'] : '';

// Inicializar a variável que armazenará os resultados da pesquisa
$documentos = [];

// Verificar se pelo menos um campo foi preenchido para realizar a pesquisa
if (!empty($nome_documento) || !empty($sistema_documento) || !empty($assunto_documento)) {
    // Construir a consulta SQL com base nos critérios de pesquisa
    $query = "SELECT id, nome_documento, sistema_documento, assunto_documento FROM documento_pdf WHERE 1=1";

    // Adicionar as condições de filtro com base nos campos preenchidos
    if (!empty($nome_documento)) {
        $query .= " AND nome_documento LIKE :nome_documento";
    }
    if (!empty($sistema_documento)) {
        $query .= " AND sistema_documento LIKE :sistema_documento";
    }
    if (!empty($assunto_documento)) {
        $query .= " AND assunto_documento LIKE :assunto_documento";
    }

    $stmt = $pdo->prepare($query);

    // Vincular os parâmetros de pesquisa com base nos campos preenchidos
    if (!empty($nome_documento)) {
        $stmt->bindValue(':nome_documento', "%$nome_documento%");
    }
    if (!empty($sistema_documento)) {
        $stmt->bindValue(':sistema_documento', "%$sistema_documento%");
    }
    if (!empty($assunto_documento)) {
        $stmt->bindValue(':assunto_documento', "%$assunto_documento%");
    }

    // Executar a consulta e buscar os resultados
    $stmt->execute();
    $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa Detalhada de Documentos PDF</title>
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

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            position: relative;
        }

        .search-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Botão de Pesquisa Estilizado */
        .cssbuttons-io {
            position: relative;
            font-family: inherit;
            font-weight: 600;
            font-size: 17px;
            border-radius: 0.8em;
            cursor: pointer;
            border: none;
            background: linear-gradient(to right, #00bf63, #5ce1e6);
            color: ghostwhite;
            overflow: hidden;
            transform: translateY(-20px);
        }

        .cssbuttons-io svg {
            width: 1.2em;
            height: 1.2em;
            margin-left: 0.7em;
            stroke-width: 2px;
        }

        .cssbuttons-io span {
            position: relative;
            z-index: 10;
            transition: color 0.4s;
            display: inline-flex;
            align-items: center;
            padding: 0.8em 0.9em 0.8em 1.02em;
        }

        .cssbuttons-io::before,
        .cssbuttons-io::after {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .cssbuttons-io::before {
            content: "";
            background: #000;
            width: 120%;
            left: -10%;
            transform: skew(30deg);
            transition: transform 0.4s cubic-bezier(0.3, 1, 0.8, 1);
        }

        .cssbuttons-io:hover::before {
            transform: translate3d(100%, 0, 0);
        }

        .cssbuttons-io:active {
            transform: scale(0.95);
        }

        /* Estilo dos botões de download e visualização */
        .button-content {
            position: relative;
            z-index: 1;
            font-size: 12px; /* Diminuir o tamanho da fonte */
        }

        .button {
            position: relative;
            overflow: hidden;
            height: 3rem;
            padding: 0 2rem; /* Reduzir o padding para ajustar o tamanho */
            border-radius: 1.5rem;
            background: #3d3a4e;
            background-size: 400%;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 15px;
            box-shadow: none; /* Remover sombra */
            text-decoration: none; /* Remover sublinhados */
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

        /* Estilo para os botões lado a lado */
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        

        .input:focus {
            background-color: #e8e8e8; /* Cor uniforme ao focar */
            transform: scale(1.05);
        }

        /* Sugestões de autocomplete */
        .autocomplete-suggestions {
            position: absolute;
            top: 55px; /* Ajuste a altura conforme necessário */
            background-color: #fff;
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            width: 100%; /* Ajustar a largura para coincidir com o campo */
            z-index: 1000;
        }

        .autocomplete-suggestion {
            padding: 10px;
            cursor: pointer;
            font-size: 14px;
        }

        .autocomplete-suggestion:hover {
            background-color: #fff;
        }

    </style>
</head>
<body>
    <h1>Pesquisa Detalhada de Documentos PDF</h1>

    <div class="form-container">
    <form method="GET" action="" class="search-container">
        <input type="text" autocomplete="off" name="nome_documento" class="input" placeholder="Nome do Documento" value="<?php echo htmlspecialchars($nome_documento); ?>">

        <input type="text" autocomplete="off" id="sistema_documento" name="sistema_documento" class="input" placeholder="Sistema do Documento" value="<?php echo htmlspecialchars($sistema_documento); ?>">
        <!-- Div para sugestões do campo "Sistema do Documento" -->
        <div id="sistema_suggestions" class="autocomplete-suggestions"></div>

        <input type="text" autocomplete="off" id="assunto_documento" name="assunto_documento" class="input" placeholder="Assunto do Documento" value="<?php echo htmlspecialchars($assunto_documento); ?>">
        <!-- Div para sugestões do campo "Assunto do Documento" -->
        <div id="assunto_suggestions" class="autocomplete-suggestions"></div>

        <button class="cssbuttons-io" type="submit">
            <span>
                Pesquisar
                <svg viewBox="0 0 19.9 19.7" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title desc" class="svg-icon search-icon">
                    <title>Search Icon</title>
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


    <!-- Exibir resultados apenas se houver documentos correspondentes -->
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
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET)): ?>
        <p>Nenhum resultado encontrado.</p>
    <?php endif; ?>

    <script>
        // Função para buscar sugestões do sistema
        document.getElementById('sistema_documento').addEventListener('input', function() {
            var input = this.value;
            if (input.length > 1) {
                fetch('sugestoes.php?tipo=sistema&query=' + input)
                    .then(response => response.json())
                    .then(data => {
                        var suggestions = document.getElementById('sistema_suggestions');
                        suggestions.innerHTML = '';
                        data.forEach(function(suggestion) {
                            var div = document.createElement('div');
                            div.textContent = suggestion;
                            div.classList.add('autocomplete-suggestion');
                            div.addEventListener('click', function() {
                                document.getElementById('sistema_documento').value = suggestion;
                                suggestions.innerHTML = '';
                            });
                            suggestions.appendChild(div);
                        });
                    });
            } else {
                document.getElementById('sistema_suggestions').innerHTML = '';
            }
        });

        // Função para buscar sugestões do assunto
        document.getElementById('assunto_documento').addEventListener('input', function() {
            var input = this.value;
            if (input.length > 1) {
                fetch('sugestoes.php?tipo=assunto&query=' + input)
                    .then(response => response.json())
                    .then(data => {
                        var suggestions = document.getElementById('assunto_suggestions');
                        suggestions.innerHTML = '';
                        data.forEach(function(suggestion) {
                            var div = document.createElement('div');
                            div.textContent = suggestion;
                            div.classList.add('autocomplete-suggestion');
                            div.addEventListener('click', function() {
                                document.getElementById('assunto_documento').value = suggestion;
                                suggestions.innerHTML = '';
                            });
                            suggestions.appendChild(div);
                        });
                    });
            } else {
                document.getElementById('assunto_suggestions').innerHTML = '';
            }
        });
    </script>
</body>
</html>