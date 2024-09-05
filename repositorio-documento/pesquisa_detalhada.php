<?php
include 'conexao.php';

$sistema_documento = isset($_GET['sistema_documento']) ? $_GET['sistema_documento'] : '';
$assunto_documento = isset($_GET['assunto_documento']) ? $_GET['assunto_documento'] : '';

$query = "SELECT id, nome_documento, sistema_documento, assunto_documento FROM documento_pdf WHERE 1=1";

if (!empty($sistema_documento)) {
    $query .= " AND sistema_documento = :sistema_documento";
}
if (!empty($assunto_documento)) {
    $query .= " AND assunto_documento = :assunto_documento";
}

$stmt = $pdo->prepare($query);

if (!empty($sistema_documento)) {
    $stmt->bindValue(':sistema_documento', $sistema_documento);
}
if (!empty($assunto_documento)) {
    $stmt->bindValue(':assunto_documento', $assunto_documento);
}

$stmt->execute();
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repositorio de Documentos</title>
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
            gap: 10px; /* Ajuste de espaço entre os elementos */
            margin-bottom: 20px;

        }

        .search-container {
            display: flex;
            align-items: center;
            gap: 10px; /* Espaço entre a caixa de texto e os botões */
        }

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

        .button-clear {
            background: linear-gradient(to right, #ff6b6b, #ff8e53);
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

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }


        .input:focus {
            background-color: #e8e8e8;
            transform: scale(1.05);
        }

        table {
            border-radius: 15px;
            overflow: hidden;
            margin-top: 20px;
            width: 80%; /* Centraliza a tabela e ajusta a largura */
        }

        table, th, td {
            border: 1px solid #ddd;
            text-align: center; /* Centraliza o conteúdo da tabela */
            padding: 10px;
        }

        th, td {
            vertical-align: middle; /* Alinha o conteúdo verticalmente ao meio */
        }

        td.action-buttons {
            white-space: nowrap; /* Evita quebra de linha nas células de ação */
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center; /* Centraliza verticalmente os botões */
        }

        .autocomplete-suggestions {
            position: absolute;
            border: 0px solid #ccc;
            background-color: #fff;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
            width: calc(20% - 20px);
        }

        .autocomplete-suggestion {
            padding: 10px;
            cursor: pointer;
        }

        .autocomplete-suggestion:hover {
            background-color: #f0f0f0;
        }

        </style>
</head>
<body>
    <h1>Repositorio de Documentos</h1>

    <div class="form-container">
    <form method="GET" action="" class="search-container">
        <select id="sistema_documento" name="sistema_documento" class="input">
            <option value="">Selecione o Sistema</option>
        </select>

        <input type="text" autocomplete="off" id="assunto_documento" name="assunto_documento" class="input" placeholder="Assunto do Documento" value="<?php echo htmlspecialchars($assunto_documento); ?>">
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

        <button class="cssbuttons-io button-clear" type="button" onclick="limparPesquisa()">
            <span>
                Limpar
                <svg viewBox="0 0 19.9 19.7" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title desc" class="svg-icon search-icon">
                    <title>Clear Icon</title>
                    <desc id="desc">A cross icon.</desc>
                    <g stroke="white" fill="none" class="search-path">
                        <path d="M2 2l16 16m0-16L2 18" stroke-linecap="square"></path>
                    </g>
                </svg>
            </span>
        </button>
    </form>
</div>

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

    <script>
        function fetchOptions(endpoint, selectId) {
            fetch(endpoint)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById(selectId);
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item;
                        option.textContent = item;
                        select.appendChild(option);
                    });
                });
        }

        document.getElementById('assunto_documento').addEventListener('input', function() {
            const query = this.value;

            if (query.length > 1) {
                fetch('sugestoes.php?tipo=assunto&query=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        const suggestionsContainer = document.getElementById('assunto_suggestions');
                        suggestionsContainer.innerHTML = '';
                        data.forEach(suggestion => {
                            const div = document.createElement('div');
                            div.textContent = suggestion;
                            div.classList.add('autocomplete-suggestion');
                            div.addEventListener('click', function() {
                                document.getElementById('assunto_documento').value = suggestion;
                                suggestionsContainer.innerHTML = ''; // Limpar sugestões após a seleção
                            });
                            suggestionsContainer.appendChild(div);
                        });
                    });
            } else {
                document.getElementById('assunto_suggestions').innerHTML = '';
            }
        });;

        function limparPesquisa() {
            document.getElementById('sistema_documento').selectedIndex = 0;
            document.getElementById('assunto_documento').value = '';
            window.location.href = window.location.pathname;
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchOptions('sugestoes.php?tipo=sistema', 'sistema_documento');
            fetchOptions('sugestoes.php?tipo=assunto', 'assunto_documento');
        });
    </script>
</body>
</html>
