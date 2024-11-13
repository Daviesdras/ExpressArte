<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: login.html");
    exit();
}

// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_exart";

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtém o ID do usuário logado
$usuario_id = $_SESSION['id_usuario'];

// Consulta para obter os dados do usuário
$sql_usuario = "SELECT nome, email, imagem_perfil FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

// Verifica se o usuário foi encontrado
if ($result_usuario->num_rows > 0) {
    $usuario = $result_usuario->fetch_assoc(); // Carrega os dados do usuário
} else {
    echo "Usuário não encontrado.";
    exit();
}

// Consulta para obter os poemas postados pelo usuário
$sql_poemas = "SELECT titulo, poema FROM postagens WHERE usuario_id = ?";
$stmt_poemas = $conn->prepare($sql_poemas);
$stmt_poemas->bind_param("i", $usuario_id);
$stmt_poemas->execute();
$result_poemas = $stmt_poemas->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

<section>
    <h2>Perfil de <?php echo htmlspecialchars($usuario['nome']); ?></h2>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>

    <!-- Exibir a imagem de perfil -->
    <img class="perfil2" src="<?php echo !empty($usuario['imagem_perfil']) ? $usuario['imagem_perfil'] : 'imagens/default-perfil.jpg'; ?>" alt="Imagem de perfil" width="100px">

    <!-- Formulário para alterar a imagem de perfil -->
    <form action="upload_imagem.php" method="POST" enctype="multipart/form-data">
        <label for="imagem_perfil">Alterar Imagem de Perfil:</label>
<br>
        <input type="file" name="imagem_perfil" id="imagem_perfil" accept="image/*"required> 
<br>
        <button type="submit">Salvar Imagem</button>
    </form>

    <h3>Poemas postados:</h3>
    <ul>
        <?php
        if ($result_poemas->num_rows > 0) {
            while ($poema = $result_poemas->fetch_assoc()) {
                echo '<li><strong>' . htmlspecialchars($poema['titulo']) . ':</strong> ' . htmlspecialchars($poema['poema']) . '</li>';
            }
        } else {
            echo "<p>Você ainda não postou nenhum poema.</p>";
        }
        ?>
    </ul>
</section>

</body>
</html>

<?php
// Fechar as conexões e liberar os recursos
$stmt_usuario->close();
$stmt_poemas->close();
$conn->close();
?>
