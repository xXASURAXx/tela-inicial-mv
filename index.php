<?php
include 'metodos.php';
atualizar();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Boas-Vindas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
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
          background: linear-gradient(to top, #00154c, #12376e, #23487f);
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

    </style>
</head>
<body>

    <h1><?php echo 'Bem-vindo, ' . $saudacao . ' ' . $inicioAno; ?></h1>
    <h2>Hospital Geral de Itapevi</h2>
    <p>Links uteis para acesso</p>

    <div class="button-container">
        <div class="column">
            <button class="btn" onclick="acessarPagina('http://intranet')">
                <span class="btn-text-one">Intranet</span>
                <span class="btn-text-two">Acesse</span>
            </button>
            <button class="btn" onclick="acessarPagina('http://10.0.18.91/tela-inicial-mv/repositorio-documento/pesquisa_detalhada.php')">
                <span class="btn-text-one">Repositorio Documentos</span>
                <span class="btn-text-two">Acesse</span>
            </button>
            <button class="btn" onclick="acessarPagina('https://')">
                <span class="btn-text-one">Manutenção</span>
                <span class="btn-text-two">Manutenção</span>
            </button>
        </div>
        <div class="column">
            <button class="btn" onclick="acessarPagina('https://cejam.medicsys.com.br/Login')">
                <span class="btn-text-one">Medicsys</span>
                <span class="btn-text-two">Acesse</span>
            </button>
            <button class="btn" onclick="acessarPagina('http://10.0.18.91/tela-inicial-mv/ramais/ramais.php')">
                <span class="btn-text-one">Lista de Ramais</span>
                <span class="btn-text-two">Acesse</span>
            </button>
            <button class="btn" onclick="acessarPagina('https://')">
                <span class="btn-text-one">Manutenção</span>
                <span class="btn-text-two">Manutenção</span>
            </button>
        </div>
    </div>

    <script>
        function acessarPagina(url) {
        
            window.open(url, '_blank');
        }
    </script>

</body>
</html>
