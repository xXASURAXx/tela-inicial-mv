<?php
include 'metodos.php';
atualizar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem vindos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }

        .content {
            flex: 1;
        }

        h1 {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.8em;
            color: #666;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 40px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 100px;
        }

        .column {
            display: flex;
            flex-direction: column;
            gap: 100px; 
        }

        .btn {
            width: 300px;  
            height: 70px;  
            background: linear-gradient(to top, #4670A0, #4682B4, #4670A0);
            color: #fff;
            border-radius: 50px;
            border: none;
            outline: none;
            cursor: pointer;
            position: relative;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .btn span {
            font-size: 14px;  
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: top 0.5s;
        }

        .btn-text-one {
            position: absolute;
            width: 100%;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
        }

        .btn-text-two {
            position: absolute;
            width: 100%;
            top: 150%;
            left: 0;
            transform: translateY(-50%);
        }

        .btn:hover .btn-text-one {
            top: -100%;
        }

        .btn:hover .btn-text-two {
            top: 50%;
        }

        footer {
            background-color: #f4f4f4;
            color: #666;
            padding: 20px 0;
            text-align: center;
            font-size: 0.9em;
        }

    </style>
</head>
<body>

    <div class="content">
        <h1><?php echo 'Bem-vindo, ' . $saudacao . ' ' . $inicioAno; ?></h1>
        <h2>Hospital Geral de Itapevi</h2>
        <p>Links uteis para acesso</p>

        <div class="button-container">
            <div class="column">
                <button class="btn" onclick="acessarPagina('http://10.0.16.20', true)">
                    <span class="btn-text-one">Intranet</span>
                    <span class="btn-text-two">Acesse</span>
                </button>
                <button class="btn" onclick="acessarPagina('repositorio-documento/repositorio-mv.php', false)">
                    <span class="btn-text-one">Manuais MV</span>
                    <span class="btn-text-two">Acesse</span>
                </button>
            </div>
            <div class="column">
                <button class="btn" onclick="acessarPagina('repositorio-documento/repositorio-pop.php', false)">
                    <span class="btn-text-one">Repositorio Hospital</span>
                    <span class="btn-text-two">Acesse</span>
                </button>
                <button class="btn" onclick="acessarPagina('repositorio-documento/release-notes.php', false)">
                    <span class="btn-text-one">Release Notes</span>
                    <span class="btn-text-two">Acesse</span>
                </button>
            </div>
        </div>
    </div>

    <footer>
        Tecnologia da Informação - Hospital Geral de Itapevi
    </footer>

    <script>
        function acessarPagina(url, novaGuia) {
            if (novaGuia) {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        }
    </script>

</body>
</html>
