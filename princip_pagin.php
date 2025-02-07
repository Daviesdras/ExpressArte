<?php
session_start(); // Inicia a sessão

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

// Definir um valor padrão para a imagem de perfil
$imagem_perfil = "imagens/sem_perfil.jpg";

// Verifica se o usuário está logado
if (isset($_SESSION['id_usuario'])) {
    $usuario_id = $_SESSION['id_usuario'];

    // Consulta para obter os dados do usuário logado, incluindo a imagem de perfil
    $sql_usuario = "SELECT imagem_perfil FROM usuarios WHERE id = ?";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bind_param("i", $usuario_id);
    $stmt_usuario->execute();
    $result_usuario = $stmt_usuario->get_result();

    if ($result_usuario->num_rows > 0) {
        $usuario = $result_usuario->fetch_assoc();
        if (!empty($usuario['imagem_perfil'])) {
            $imagem_perfil = $usuario['imagem_perfil'];
        }
    }
}

// Consulta para obter as postagens mais recentes e mais curtidas
$sql = "
SELECT 
    postagens.id, 
    postagens.titulo, 
    postagens.imagem, 
    categorias.nome AS categoria, 
    postagens.data_postagem, 
    COALESCE(curtidas.total, 0) AS total_curtidas 
FROM 
    postagens 
LEFT JOIN 
    categorias ON postagens.categoria_id = categorias.id 
LEFT JOIN 
    (SELECT postagem_id, COUNT(*) AS total FROM curtidas GROUP BY postagem_id) AS curtidas 
    ON postagens.id = curtidas.postagem_id
WHERE 
    postagens.status = 'aprovado'
ORDER BY 
    total_curtidas DESC, postagens.data_postagem DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Poemas</title>
    <link rel="stylesheet" href="princip_pagin.css">
</head>

<body>
<nav>
    <a class="logo" href="index.html">
        <span id="logo-s"><img src="imagens/Express_logo.png" width="170px" alt="logo"></span>
    </a>
    <div class="group">
        <svg viewBox="0 0 24 24" aria-hidden="true" class="icon">
            <g>
                <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
            </g>
        </svg>
        <input class="input" type="search" placeholder="Explorar" />
    </div>
    <ul class="nav-list">
        <?php if (isset($_SESSION['id_usuario'])): ?>
        <li><a href="postar.html">Postar</a></li>
        <li><a href="pagin_artes.php">Artes</a></li>
        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === 1): ?>
            <li><a href="tabela.php">Gerenciar Poemas</a></li>
        <?php endif; ?>
        <?php else: ?>
            <li><a href="pagin_artes.php">Artes</a></li>
        <li><a href="login.html">Login</a></li>
        <?php endif; ?>
    </ul>

    <a href="#" id="perfil-link">
        <img class="perfil" src="<?php echo htmlspecialchars($imagem_perfil); ?>" alt="Imagem de perfil" width="100px">
    </a>
</nav>

<section>
    <div class="container">
        <?php
        // Loop para exibir as postagens
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $titulo = htmlspecialchars($row['titulo']);
                $imagem = htmlspecialchars($row['imagem']);
                $categoria = $row['categoria'] ?? "Sem categoria"; // Se não tiver categoria, exibe "Sem categoria"
                $tipo = "Poema"; // Como os dados vêm da tabela "postagens", sempre será um poema
                $curtidas = $row['total_curtidas']; // Número de curtidas

                echo '
                <div class="card">
                    <div class="card-inner" style="--clr:#fff;">
                        <div class="box">
                            <a href="poema.php?id=' . $id . '">
                                <div class="imgBox">
                                    <img src="' . $imagem . '" alt="' . $titulo . '">
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="content">
                        <h3>' . $titulo . '</h3>
                        <ul>
                            <li style="--clr-tag:#d3b19a;" class="branding"> ' . htmlspecialchars($categoria) . '</li>
                            <li style="--clr-tag:#70b3b1;" class="packaging"> ' . $tipo . '</li>
                        </ul>
                        <p class="likes">Curtidas: ' . $curtidas . '</p>
                    </div>
                </div>';
            }
        } else {
            echo "<p>Nenhum poema encontrado.</p>";
        }

        // Fechando a conexão
        $conn->close();
        ?>
    </div>
</section>

<script src="./script.js"></script>
<script>
document.getElementById('perfil-link').addEventListener('click', function(event) {
    <?php if (!isset($_SESSION['id_usuario'])): ?>
        event.preventDefault();
        window.location.href = 'login.html';
    <?php else: ?>
        window.location.href = 'perfil.php';
    <?php endif; ?>
});
</script>
</body>
</html>
