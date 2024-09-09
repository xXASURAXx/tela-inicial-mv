<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['username'])) {
    $usuario = $_SESSION['username'];
} else {
    $usuario = "Usuário desconhecido";
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "documento";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$message = "";
$editMode = false;
$editId = null;
$usuario = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $ramal_fixo = $_POST['fixo'];
    $ramal_movel = $_POST['movel'];
    $andar = $_POST['andar'];
    $setor = $_POST['setor'];
    $responsavel = $_POST['responsavel'];

    if (!empty($ramal_fixo) || !empty($ramal_movel) && !empty($andar) && !empty($setor) && !empty($responsavel)) {
        if ($_POST['action'] == 'add') {
            $sql = "INSERT INTO ramais (fixo, movel, andar, setor, responsavel, usuario) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $ramal_fixo, $ramal_movel, $andar, $setor, $responsavel, $usuario);
        } else if ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $sql = "UPDATE ramais SET fixo = ?, movel = ?, andar = ?, setor = ?, responsavel = ?, usuario = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $ramal_fixo, $ramal_movel, $andar, $setor, $responsavel, $usuario, $id);
        }

        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Erro ao salvar o ramal.</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>Por favor, preencha todos os campos obrigatórios.</div>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM ramais WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Erro ao excluir o ramal.</div>";
    }

    $stmt->close();
}

if (isset($_GET['edit'])) {
    $editMode = true;
    $editId = $_GET['edit'];
    $sql = "SELECT * FROM ramais WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editData = $result->fetch_assoc();
    $stmt->close();
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql = "SELECT id, fixo, movel, andar, setor, responsavel FROM ramais 
        WHERE setor LIKE ? OR fixo LIKE ? OR movel LIKE ? OR responsavel LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Administração de Ramais</title>
</head>
<body>
<div class="container mt-5">
    <h1>Administração de Ramais</h1>
    
    <?php echo $message; ?>
    
    <form method="GET" action="" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Pesquisar ramal, setor ou responsável" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
        </div>
    </form>

    <form method="POST" action="">
        <input type="hidden" name="action" value="<?php echo $editMode ? 'edit' : 'add'; ?>">
        <?php if ($editMode): ?>
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label for="fixo" class="form-label">Ramal Fixo</label>
            <input type="text" class="form-control" id="fixo" name="fixo" value="<?php echo $editMode ? htmlspecialchars($editData['fixo']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="movel" class="form-label">Ramal Móvel</label>
            <input type="text" class="form-control" id="movel" name="movel" value="<?php echo $editMode ? htmlspecialchars($editData['movel']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="andar" class="form-label">Andar</label>
            <input type="text" class="form-control" id="andar" name="andar" value="<?php echo $editMode ? htmlspecialchars($editData['andar']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="setor" class="form-label">Setor</label>
            <input type="text" class="form-control" id="setor" name="setor" value="<?php echo $editMode ? htmlspecialchars($editData['setor']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="responsavel" class="form-label">Responsável</label>
            <input type="text" class="form-control" id="responsavel" name="responsavel" value="<?php echo $editMode ? htmlspecialchars($editData['responsavel']) : ''; ?>" required>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $editMode ? 'Atualizar' : 'Adicionar'; ?></button>
    </form>

    <table class="table table-bordered mt-5">
        <thead>
        <tr>
            <th>Fixo</th>
            <th>Móvel</th>
            <th>Andar</th>
            <th>Setor</th>
            <th>Responsável</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['fixo']) . "</td>
                        <td>" . htmlspecialchars($row['movel']) . "</td>
                        <td>" . htmlspecialchars($row['andar']) . "</td>
                        <td>" . htmlspecialchars($row['setor']) . "</td>
                        <td>" . htmlspecialchars($row['responsavel']) . "</td>
                        <td>
                            <a href='?edit=" . $row['id'] . "' class='btn btn-warning btn-sm'>Editar</a>
                            <a href='?delete=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Tem certeza que deseja excluir este ramal?\")'>Excluir</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Nenhum ramal encontrado.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
