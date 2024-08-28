<?php
include 'conexao.php';

$nome_documento = isset($_GET['nome_documento']) ? $_GET['nome_documento'] : '';
$sistema_documento = isset($_GET['sistema_documento']) ? $_GET['sistema_documento'] : '';
$assunto_documento = isset($_GET['assunto_documento']) ? $_GET['assunto_documento'] : '';

$documentos = [];

if (!empty($nome_documento) || !empty($sistema_documento) || !empty($assunto_documento)) {
    $query = "SELECT id, nome_documento, sistema_documento, assunto_documento FROM documento_pdf WHERE 1=1";

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

    if (!empty($nome_documento)) {
        $stmt->bindValue(':nome_documento', "%$nome_documento%");
    }
    if (!empty($sistema_documento)) {
        $stmt->bindValue(':sistema_documento', "%$sistema_documento%");
    }
    if (!empty($assunto_documento)) {
        $stmt->bindValue(':assunto_documento', "%$assunto_documento%");
    }

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
        }

        .button:hover::before {
            transform: scaleX(1);
        }

        .button-content {
            position: relative;
            z-index: 1;
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

        .autocomplete-suggestions {
            border: 1px solid #e0e0e0;
            background-color: #fff;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            z-index: 1000;
            width: calc(100% - 20px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            left: 50%;
            transform: translateX(-50%);
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
    <h1>Pesquisa de Manuais PDF</h1>
    <div class="form-container">
        <form method="GET" action="">
            <input type="text" autocomplete="off" name="nome_documento" class="input" placeholder="Nome do Documento" value="<?php echo htmlspecialchars($nome_documento); ?>">
            <input type="text" autocomplete="off" id="sistema_documento" name="sistema_documento" class="input" placeholder="Sistema do Documento" value="<?php echo htmlspecialchars($sistema_documento); ?>">
            <div id="sistema_suggestions" class="autocomplete-suggestions"></div>
            <input type="text" autocomplete="off" id="assunto_documento" name="assunto_documento" class="input" placeholder="Assunto do Documento" value="<?php echo htmlspecialchars($assunto_documento); ?>">
            <div id="assunto_suggestions" class="autocomplete-suggestions"></div>

            <button class="cssbuttons-io">
                <span>Pesquisar
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
            }
        });

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
            }
        });
    </script>
</body>
</html>
