<?php
session_start();
require_once 'conexao.php';

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

// Validar ID da arte
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("ID inválido ou não fornecido.");
}

// Consulta da ARTE e artista (tabela artes)
$sql = "
    SELECT artes.titulo, artes.conteudo, artes.imagem, 
           usuarios.nome, usuarios.imagem_perfil 
    FROM artes 
    JOIN usuarios ON artes.usuario_id = usuarios.id 
    WHERE artes.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $titulo = $row['titulo'];
    $imagem = $row['imagem'];
    $conteudo = $row['conteudo']; // Campo 'conteudo' da arte
    $nome_artista = $row['nome'];
    $imagem_perfil_artista = $row['imagem_perfil'];
} else {
    $titulo = "Arte não encontrada";
    $conteudo = "";
    $imagem = "";
    $nome_artista = "Desconhecido";
    $imagem_perfil_artista = 'imagens/default-perfil.jpg';
}
$stmt->close();

// Processar envio de comentário (igual ao original)
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
            header("Location: arte.php?id=" . $id); // Atualizado para 'arte.php'
            exit();
        } else {
            echo "Erro ao inserir comentário: " . $stmt_comentario->error;
        }
        $stmt_comentario->close();
    } else {
        echo "<p style='color: red;'>Por favor, insira um comentário.</p>";
    }
}

// Consulta dos comentários (igual ao original)
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

// Contagem de curtidas (ajustado para tabela artes se necessário)
$sql_curtidas = "SELECT COUNT(*) AS total_curtidas_arte FROM curtidas WHERE postagem_id = ?";
$stmt_curtidas = $conn->prepare($sql_curtidas);
$stmt_curtidas->bind_param("i", $id);
$stmt_curtidas->execute();
$result_curtidas = $stmt_curtidas->get_result();
$total_curtidas = $result_curtidas->fetch_assoc()['total_curtidas_arte'] ?? 0;
$stmt_curtidas->close();
?>

<!DOCTYPE html>
<html lang="pt-br"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($conteudo); ?>">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="poema.css">
</head>

<body>
    <nav>
        <a class="logo" href="#">
            <span id="logo-s"><img id="logo" src="imagens/Express_logo.png" width="170px" alt="logo"></span>
        </a>
        <a href="perfil.php">
            <img class="perfil" src="<?php echo $imagem_perfil; ?>" alt="Imagem de perfil" width="100px">
        </a>
    </nav>
    <a href="princip_pagin.php" class="voltar">Voltar</a>

    <div class="Quadrado">
        <?php if ($imagem): ?>
            <img class="img" src="<?php echo htmlspecialchars($imagem); ?>" alt="<?php echo htmlspecialchars($titulo); ?>">
        <?php endif; ?>
        <div class="container">
            <h3 class="titulo"><?php echo htmlspecialchars($titulo); ?></h3>
            <p class="texto"><?php echo nl2br(htmlspecialchars($conteudo)); ?></p>
            <div class="artista">
                <img class="perfil" src="<?php echo htmlspecialchars($imagem_perfil_artista); ?>" alt="Imagem de perfil do artista">
                <h3 class="nome"><?php echo htmlspecialchars($nome_artista); ?></h3>
            </div>
            <div class='curtidas-poema'>
                <button class='curtir-poema-btn' data-postagem-id='<?php echo $id; ?>'>
                    <span class='contador-curtidas-poema'><?php echo $total_curtidas; ?></span> Curtidas
                </button>
            </div>
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
$conn->close();
?>