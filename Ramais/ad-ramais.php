<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $ramal = $_POST['ramal'];
    $setor = $_POST['setor'];
    $responsavel = $_POST['responsavel'];
    $tipo = $_POST['tipo'];

    if (!empty($ramal) && !empty($setor) && !empty($responsavel) && !empty($tipo)) {
        if ($tipo == 'fixo') {
            $sql = "INSERT INTO ramais (fixo, setor, responsavel) VALUES (?, ?, ?)";
        } else {
            $sql = "INSERT INTO ramais (movel, setor, responsavel) VALUES (?, ?, ?)";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $ramal, $setor, $responsavel);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Erro ao adicionar o ramal.</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>Por favor, preencha todos os campos.</div>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM ramais WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: ".$_SERVER['PHP_SELF']);
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $ramal = $_POST['ramal'];
    $setor = $_POST['setor'];
    $responsavel = $_POST['responsavel'];
    $tipo = $_POST['tipo'];
    $id = $_POST['id'];

    if (!empty($ramal) && !empty($setor) && !empty($responsavel) && !empty($tipo)) {
        if ($tipo == 'fixo') {
            $sql = "UPDATE ramais SET fixo = ?, setor = ?, responsavel = ?, movel = NULL WHERE id = ?";
        } else {
            $sql = "UPDATE ramais SET movel = ?, setor = ?, responsavel = ?, fixo = NULL WHERE id = ?";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $ramal, $setor, $responsavel, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Erro ao atualizar o ramal.</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>Por favor, preencha todos os campos.</div>";
    }
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql = "SELECT id, fixo, movel, setor, responsavel FROM ramais 
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
            <label for="ramal" class="form-label">Ramal</label>
            <input type="text" class="form-control" id="ramal" name="ramal" value="<?php echo $editMode ? htmlspecialchars($editData['fixo'] ?? $editData['movel']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="setor" class="form-label">Setor</label>
            <input type="text" class="form-control" id="setor" name="setor" value="<?php echo $editMode ? htmlspecialchars($editData['setor']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="responsavel" class="form-label">Responsável</label>
            <input type="text" class="form-control" id="responsavel" name="responsavel" value="<?php echo $editMode ? htmlspecialchars($editData['responsavel']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-control" id="tipo" name="tipo" required>
                <option value="fixo" <?php echo $editMode && !empty($editData['fixo']) ? 'selected' : ''; ?>>Fixo</option>
                <option value="movel" <?php echo $editMode && !empty($editData['movel']) ? 'selected' : ''; ?>>Móvel</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $editMode ? 'Atualizar' : 'Adicionar'; ?></button>
    </form>

    <table class="table table-bordered mt-5">
        <thead>
        <tr>
            <th>Fixo</th>
            <th>Móvel</th>
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
                        <td>" . htmlspecialchars($row['setor']) . "</td>
                        <td>" . htmlspecialchars($row['responsavel']) . "</td>
                        <td>
                            <a href='?edit=" . $row['id'] . "' class='btn btn-warning btn-sm'>Editar</a>
                            <a href='?delete=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Tem certeza que deseja excluir este ramal?\")'>Excluir</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Nenhum ramal encontrado.</td></tr>";
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
