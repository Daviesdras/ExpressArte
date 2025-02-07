<?php
session_start();
require_once 'conexao.php'; // Inclui o arquivo de conexão

// Função para obter a imagem de perfil
function obterImagemPerfil($conn, $usuario_id) {
    $sql = "SELECT imagem_perfil FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        return $usuario['imagem_perfil'];
    }
    return 'imagens/default-perfil.jpg';
}

// Imagem de perfil do usuário logado ou padrão
$imagem_perfil = isset($_SESSION['id_usuario'])
    ? obterImagemPerfil($conn, $_SESSION['id_usuario'])
    : "imagens/sem_perfil.jpg";

// Validar ID do poema
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("ID inválido ou não fornecido.");
}

// Consulta do poema e autor
$sql = "
    SELECT postagens.titulo, postagens.poema, postagens.imagem, 
           usuarios.nome, usuarios.imagem_perfil 
    FROM postagens 
    JOIN usuarios ON postagens.usuario_id = usuarios.id 
    WHERE postagens.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $titulo = $row['titulo'];
    $imagem = $row['imagem'];
    $poema = $row['poema'];
    $nome_artista = $row['nome'];
    $imagem_perfil_artista = $row['imagem_perfil'];
} else {
    $titulo = "Poema não encontrado";
    $poema = "";
    $imagem = "";
    $nome_artista = "Desconhecido";
    $imagem_perfil_artista = 'imagens/default-perfil.jpg';
}
$stmt->close();

// Processar envio de comentário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_usuario'])) {
    $comentario = $_POST['comentario'] ?? '';
    $usuario_id = $_SESSION['id_usuario'];
    if (!empty($comentario)) {
        $sql_comentario = "
            INSERT INTO comentarios (poema_id, usuario_id, comentario, data_criacao) 
            VALUES (?, ?, ?, NOW())";
        $stmt_comentario = $conn->prepare($sql_comentario);
        $stmt_comentario->bind_param("iis", $id, $usuario_id, $comentario);
        if ($stmt_comentario->execute()) {
            header("Location: poema.php?id=" . $id);
            exit();
        } else {
            echo "Erro ao inserir comentário: " . $stmt_comentario->error;
        }
        $stmt_comentario->close();
    } else {
        echo "<p style='color: red;'>Por favor, insira um comentário.</p>";
    }
}

// Consulta dos comentários
$sql_comentarios = "
    SELECT comentarios.comentario, comentarios.data_criacao, 
           usuarios.nome, usuarios.imagem_perfil 
    FROM comentarios 
    JOIN usuarios ON comentarios.usuario_id = usuarios.id 
    WHERE comentarios.poema_id = ? 
    ORDER BY comentarios.data_criacao DESC";
$stmt_comentarios = $conn->prepare($sql_comentarios);
$stmt_comentarios->bind_param("i", $id);
$stmt_comentarios->execute();
$result_comentarios = $stmt_comentarios->get_result();

// Contagem de curtidas do poema
$sql_curtidas = "SELECT COUNT(*) AS total_curtidas_poema FROM curtidas WHERE postagem_id = ?";
$stmt_curtidas = $conn->prepare($sql_curtidas);
$stmt_curtidas->bind_param("i", $id);
$stmt_curtidas->execute();
$result_curtidas = $stmt_curtidas->get_result();
$total_curtidas_poema = $result_curtidas->fetch_assoc()['total_curtidas_poema'] ?? 0;
$stmt_curtidas->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="poema.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <nav>
        <a class="logo" href="#">
            <span id="logo-s"><img id="logo" src="imagens/Express_logo.png" width="170px" alt="logo"></span>
        </a>
        </ul>

        <a href="perfil.php">
            <img class="perfil" src="<?php echo $imagem_perfil; ?>" alt="Imagem de perfil" width="100px">
        </a>
    </nav>
    <i class="fa-solid fa-arrow-left" onclick="history.back()"></i>

    <div class="Quadrado">
        <?php if ($imagem): ?>
            <img class="img" src="<?php echo htmlspecialchars($imagem); ?>" alt="<?php echo htmlspecialchars($titulo); ?>">
        <?php endif; ?>
        <div class="container">
            <h3 class="titulo"><?php echo htmlspecialchars($titulo); ?></h3>
            <p class="texto"><?php echo nl2br(htmlspecialchars($poema)); ?></p>
            <div class='curtidas-poema'>
                <button class="like-button curtir-poema-btn" data-postagem-id="<?php echo $id; ?>" onclick="toggleLike(this)">
                    <svg
                        class="like-icon"
                        fill-rule="nonzero"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"></path>
                    </svg>
                    <span class="like-text">Gostei</span>
                    <span class="contador-curtidas-poema"><?php echo $total_curtidas_poema; ?></span>
                </button>
            </div>
            <div class="artista">
                <img class="perfil" src="<?php echo htmlspecialchars($imagem_perfil_artista); ?>" alt="Imagem de perfil do artista">
                <h3 class="nome"><?php echo htmlspecialchars($nome_artista); ?></h3>
            </div>

            <script>
                function toggleLike(button) {
                    button.classList.toggle('active');
                    // Implement your AJAX call or other logic here
                }
            </script>
        </div>
    </div>

    <div class="comentarios">
        <h3>Comentários</h3>
        <?php while ($comentario = $result_comentarios->fetch_assoc()): ?>
            <div class='comentario'>
                <img class='perfil' src='<?php echo htmlspecialchars($comentario['imagem_perfil']); ?>' alt='Imagem do usuário'>
                <h4><?php echo htmlspecialchars($comentario['nome']); ?></h4>
                <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                <span><?php echo htmlspecialchars($comentario['data_criacao']); ?></span>
            </div>
        <?php endwhile; ?>
        <div class="formulario_comentario">
            <h4>Deixe um comentário:</h4>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>">
                <textarea name="comentario" rows="4" cols="50" required></textarea><br>
                <input type="submit" value="Enviar Comentário">
            </form>
        </div>
    </div>

    <script src="curtidas.js"></script>
</body>
</html>
<?php
$stmt_comentarios->close();
$conn->close(); // Fecha a conexão no final
?>