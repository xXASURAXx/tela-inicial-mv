TELA INICIAL MV
DESCRIÇÃO DO PROJETO
Este projeto consiste numa aplicação web desenvolvida em PHP e CSS, que visa criar uma tela inicial e gerenciar informações de documentos, ramais e sugestões de forma organizada. Além disso, há funcionalidades de autenticação e administração de documentos.

ESTRUTURA DO PROJETO
ARQUIVOS PRINCIPAIS
index.php: Página inicial do sistema.
registro.php: Página para registro de novos usuários.
metodos.php: Contém funções reutilizáveis que auxiliam em diversas funcionalidades do projeto.
banco_documentos.sql: Script SQL para a criação da base de dados necessária para o sistema.
DIRETÓRIOS IMPORTANTES
Ramais/: Contém arquivos relacionados ao gerenciamento de ramais, incluindo as páginas de login (login.php), logout (logout.php), e a adição de ramais (ad-ramais.php e ramais.php).
repositorio-documento/: Diretório que gerencia o repositório de documentos, com arquivos como:
adm.php: Página de administração do repositório.
conexao.php: Script de conexão com a base de dados.
upload.php: Responsável pelo upload de novos documentos.
visualizar.php: Página para visualizar os documentos armazenados.
style.css: Arquivo de estilo para o layout da página.
INSTRUÇÕES PARA USO
Clonar o Repositório: Faça o clone deste repositório para sua máquina local.

Configurar a Base de Dados: Importe o arquivo banco_documentos.sql para o seu servidor MySQL para criar a base de dados.

Configurar o Ambiente: Certifique-se de que seu servidor web (por exemplo, Apache ou Nginx) esteja configurado para rodar aplicações PHP e que a base de dados esteja acessível.

Acessar a Aplicação: Abra o arquivo index.php no seu navegador para acessar a tela inicial do sistema.

Administração: Use a página de administração (adm.php) para gerenciar documentos e outras funcionalidades administrativas.

FUNCIONALIDADES
Gerenciamento de Ramais: Adição, remoção e edição de ramais.
Repositório de Documentos: Upload, visualização, e download de documentos.
Autenticação: Sistema de login e logout para controle de acesso.
Sugestões: Possibilidade de enviar sugestões e comentários.
DEPENDÊNCIAS
Este projeto depende de:

Servidor Web (Apache ou Nginx)
PHP 7.x ou superior
MySQL para a base de dados
CONTRIBUIÇÃO
Para contribuir com este projeto, faça um fork, crie uma branch e envie um pull request com as suas alterações.

DESENVOLVIDO

Desenvolvido por Lucas Silveira com participação Joabe Santos, Leandro Cardoso, Thiago Santos, Adaildo Santana e a Equipe de Tecnologia do Hospital Geral de Itapevi