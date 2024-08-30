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
        /* Centralizar o conteúdo */
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f4f4f9;
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
        }

        table {
            width: 80%;
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0;
            text-align: center;
        }

        /* Estilo dos botões de pesquisa */
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .button {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e8e8e8;
            background-color: #212121;
            width: 70px;
            height: 70px;
            font-size: 24px;
            text-transform: uppercase;
            border: 2px solid #212121;
            border-radius: 10px;
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 5px 5px 2px rgba(0, 0, 0, 0.15),
                        2px 2px 2px rgba(0, 0, 0, 0.1),
                        -3px -3px 2px rgba(255, 255, 255, 0.05),
                        -2px -2px 1px rgba(255, 255, 255, 0.05);
            overflow: hidden;
            cursor: pointer;
        }

        .button .span {
            position: relative;
            z-index: 2;
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .button::before {
            content: "";
            position: absolute;
            background-color: #e8e8e8;
            width: 100%;
            height: 100%;
            left: 0%;
            bottom: 0%;
            transform: translate(-100%, 100%);
            border-radius: 10px;
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .button:hover::before {
            transform: translate(0, 0);
            transition-delay: 0.4s;
        }

        .button:hover .span {
            scale: 1.2;
        }

        .button:hover {
            box-shadow: none;
        }

        .button:active {
            scale: 0.95;
        }

        /* Estilo dos botões de download e visualização */
        .button-content {
            position: relative;
            z-index: 1;
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
            margin-top: 10px;
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
    <h1>Pesquisa Detalhada de Documentos PDF</h1>

    <div class="form-container">
        <form method="GET" action="" class="search-container">
            <input type="text" autocomplete="off" name="nome_documento" class="input" placeholder="Nome do Documento" value="<?php echo htmlspecialchars($nome_documento); ?>">
            <input type="text" autocomplete="off" id="sistema_documento" name="sistema_documento" class="input" placeholder="Sistema do Documento" value="<?php echo htmlspecialchars($sistema_documento); ?>">
            <input type="text" autocomplete="off" id="assunto_documento" name="assunto_documento" class="input" placeholder="Assunto do Documento" value="<?php echo htmlspecialchars($assunto_documento); ?>">

            <button class="button" type="submit">
                <span class="span">🔎</span>
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
                <td>
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
</body>
</html>
