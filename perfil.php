<?php
session_start();

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
$sql_usuario = "SELECT nome, email, imagem_perfil, descricao FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows > 0) {
    $usuario = $result_usuario->fetch_assoc();
} else {
    echo "Usuário não encontrado.";
    exit();
}

// Atualizar a descrição do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descricao'])) {
    $nova_descricao = $_POST['descricao'];
    $sql_update_descricao = "UPDATE usuarios SET descricao = ? WHERE id = ?";
    $stmt_update_descricao = $conn->prepare($sql_update_descricao);
    $stmt_update_descricao->bind_param("si", $nova_descricao, $usuario_id);
    if ($stmt_update_descricao->execute()) {
        // Atualiza a descrição na sessão para refletir a mudança imediatamente
        $usuario['descricao'] = $nova_descricao;
    } else {
        echo "Erro ao atualizar a descrição.";
    }
    $stmt_update_descricao->close();
}

// Consulta para POEMAS com contagem de curtidas e comentários
$sql_poemas = "
    SELECT 
        p.id, 
        p.titulo, 
        p.poema, 
        p.imagem, 
        c.nome AS categoria,
        (SELECT COUNT(*) FROM curtidas WHERE postagem_id = p.id AND tipo_postagem = 'postagem') AS curtidas,
        (SELECT COUNT(*) FROM comentarios WHERE poema_id = p.id AND tipo_postagem = 'postagem') AS comentarios
    FROM postagens p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.usuario_id = ?";
$stmt_poemas = $conn->prepare($sql_poemas);
$stmt_poemas->bind_param("i", $usuario_id);
$stmt_poemas->execute();
$result_poemas = $stmt_poemas->get_result();

// Consulta para ARTES com contagem de curtidas e comentários
$sql_artes = "
    SELECT 
        a.id, 
        a.titulo, 
        a.conteudo, 
        a.imagem, 
        c.nome AS categoria,
        (SELECT COUNT(*) FROM curtidas WHERE arte_id = a.id AND tipo_postagem = 'arte') AS curtidas,
        (SELECT COUNT(*) FROM comentarios WHERE arte_id = a.id AND tipo_postagem = 'arte') AS comentarios
    FROM artes a
    LEFT JOIN categorias c ON a.categoria_id = c.id
    WHERE a.usuario_id = ?";
$stmt_artes = $conn->prepare($sql_artes);
$stmt_artes->bind_param("i", $usuario_id);
$stmt_artes->execute();
$result_artes = $stmt_artes->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
</head>
<body>
    <nav>
        <a class="logo" href="index.html">
            <img id="logo" src="imagens/Express_logo.png" width="170px" alt="logo">
        </a>
        <ul class="nav-list">
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <li><a href="postar.html">Postar</a></li>
                <li><a href="princip_pagin.php">Poemas</a></li>
                <li><a href="pagin_artes.php">Artes</a></li>
                <li><a href="sobrenos/test.html">Sobre Nós</a></li>
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === 1): ?>
                    <li><a href="tabela.php">Gerenciar Poemas</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="login.html">Login</a></li>
            <?php endif; ?>
        </ul>
        <a href="perfil.php">
            <img class="perfil" src="<?php echo !empty($usuario['imagem_perfil']) ? $usuario['imagem_perfil'] : 'imagens/default-perfil.jpg'; ?>" width="100px">
        </a>
    </nav>
    <section>
        <div class="header">
            <a href="configuracao.php" class="btn-configuracao">
                <i class="fa-solid fa-gear icone-configuracao"></i>
            </a>
        </div>
        <div class="perfil-container">
            <img src="<?php echo !empty($usuario['imagem_perfil']) ? $usuario['imagem_perfil'] : 'imagens/default-perfil.jpg'; ?>" alt="Perfil" class="perfil2">
            <div class="perfil-info">
                <h2><?php echo htmlspecialchars($usuario['nome']); ?></h2>
                <p id="descricao">
                    <?php echo !empty($usuario['descricao']) ? htmlspecialchars($usuario['descricao']) : 'coloque uma descrição sua!'; ?>
                    <i class="fa-solid fa-pencil" onclick="editarDescricao()"></i>
                </p>
                <form id="form-descricao" method="POST" style="display: none;">
                    <textarea name="descricao" rows="3" cols="30"><?php echo !empty($usuario['descricao']) ? htmlspecialchars($usuario['descricao']) : ''; ?></textarea>
                    <button type="submit">Salvar</button>
                    <button type="button" onclick="cancelarEdicao()">Cancelar</button>
                </form>
            </div>
        </div>
        <hr>

        <div class="tabs">
            <button class="tab-button active" onclick="showSection('poemas')">Poemas</button>
            <button class="tab-button" onclick="showSection('artes')">Artes</button>
        </div>

        <!-- Seção de Poemas -->
        <div id="poemas" class="tab-section">
            <div class="container">
                <?php
                if ($result_poemas->num_rows > 0) {
                    while ($poema = $result_poemas->fetch_assoc()) {
                        $categoria = $poema['categoria'] ?? "Sem categoria";
                        $previa_poema = substr($poema['poema'], 0, 90) . '...';
                        echo '  
                        <div class="card" onclick="window.location.href=\'poema.php?id=' . $poema['id'] . '\'">
                            <div class="imgBox">
                                <img src="' . $poema['imagem'] . '" alt="' . htmlspecialchars($poema['titulo']) . '">
                            </div>
                            <div class="content">
                                <h3 id="titulo">' . htmlspecialchars($poema['titulo']) . '</h3>
                                <p id="categoria_tudo">Categoria: <span id="categorias">' . htmlspecialchars($categoria) . '</span></p>
                                <h4 class="nome">Autor: ' . htmlspecialchars($usuario['nome']) . '</h4>
                                Previa:<p id="previa">' . htmlspecialchars($previa_poema) . '</p>
                            </div>
                            <div class="btn">
                                <span title="Curtidas">
                                    <i class="fa-regular fa-heart"></i>
                                    ' . $poema['curtidas'] . '
                                </span>
                                <span title="Comentários">
                                    <i class="fa-regular fa-comment"></i>
                                    ' . $poema['comentarios'] . '
                                </span>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="no-publication-message">Você ainda não fez nenhuma publicação :(</p>';
                }
                ?>
            </div>
        </div>

        <!-- Seção de Artes -->
        <div id="artes" class="tab-section" style="display: none;">
            <div class="container">
                <?php
                if ($result_artes->num_rows > 0) {
                    while ($arte = $result_artes->fetch_assoc()) {
                        $categoria = $arte['categoria'] ?? "Sem categoria";
                        $previa_conteudo = $arte['conteudo'] . '...';
                        echo '  
                        <div class="card">
                            <div class="imgBox2">
                                <img src="' . $arte['imagem'] . '" alt="' . htmlspecialchars($arte['titulo']) . '">
                            </div>
                            <div class="content">
                                <h3 id="titulo">' . htmlspecialchars($arte['titulo']) . '</h3>
                                <p id="categoria_tudo">Categoria: <span id="categorias">' . htmlspecialchars($categoria) . '</span></p>
                                <h4 class="nome">Artista: ' . htmlspecialchars($usuario['nome']) . '</h4>
                                Descrição:<p id="previa2">' . htmlspecialchars($previa_conteudo) . '</p>
                            </div>
                            <div class="btn">
                                <span title="Curtidas">
                                    <i class="fa-regular fa-heart"></i>
                                    ' . $arte['curtidas'] . '
                                </span>
                                <span title="Comentários">
                                    <i class="fa-regular fa-comment"></i>
                                    ' . $arte['comentarios'] . '
                                </span>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="no-publication-message">Você ainda não fez nenhuma publicação :(</p>';
                }
                ?>
            </div>
        </div>
    </section>
    <script>
        function editarDescricao() {
            document.getElementById('descricao').style.display = 'none';
            document.getElementById('form-descricao').style.display = 'block';
        }

        function cancelarEdicao() {
            document.getElementById('form-descricao').style.display = 'none';
            document.getElementById('descricao').style.display = 'block';
        }
    </script>
    <script src="perfil_script.js"></script>
</body>
</html>

<?php
$stmt_usuario->close();
$stmt_poemas->close();
$stmt_artes->close();
$conn->close();
?>