<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "documento";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql = "SELECT fixo, movel, andar, setor, responsavel FROM ramais 
        WHERE andar LIKE ? OR setor LIKE ? OR fixo LIKE ? OR movel LIKE ? OR responsavel LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Lista de Ramais</title>
</head>
<body>
<div class="container mt-5">

    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Lista de Ramais</h1>
        <a href="login.php" class="btn btn-primary">Login</a> <!-- Botão de login no canto superior direito -->
    </div>
    
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary mb-3">Limpar</a>

    <form method="GET" action="" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Pesquisar ramal, setor ou responsável" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Fixo</th>
            <th>Móvel</th>
            <th>Andar</th>
            <th>Setor</th>
            <th>Responsável</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['fixo']) . "</td><td>" . htmlspecialchars($row['movel']) . "</td><td>" . htmlspecialchars($row['andar']) . "</td><td>" . htmlspecialchars($row['setor']) . "</td><td>" . htmlspecialchars($row['responsavel']) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nenhum ramal encontrado.</td></tr>";
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