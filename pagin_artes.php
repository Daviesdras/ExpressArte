<?php
session_start();

// Configuração da conexão com o banco de dados
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

    // Consulta para obter os dados do usuário logado
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

// Consulta para obter as artes aprovadas
$sql = "
SELECT 
    artes.id, 
    artes.titulo, 
    artes.imagem, 
    artes.conteudo,  
    categorias.nome AS categoria, 
    usuarios.nome AS autor 
FROM 
    artes 
LEFT JOIN 
    categorias 
ON 
    artes.categoria_id = categorias.id 
LEFT JOIN 
    usuarios 
ON 
    artes.usuario_id = usuarios.id 
WHERE 
    artes.status = 'aprovado'
ORDER BY 
    artes.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Artes Aprovadas</title>
    <link rel="stylesheet" href="pagin_artes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .curtir-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            margin-left: 8px;
        }

        .curtir-btn i {
            font-size: 1.2em;
            transition: all 0.3s ease;
            color: #666;
        }

        .curtir-btn.curtido i {
            color: #ff0000;
        }

        .curtir-btn:hover i {
            transform: scale(1.1);
        }

        .descricao {
            font-size: 17px;
            margin-top: 10px;
            line-height: 1.5;
        }
    </style>
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
                <li><a href="postar_art.html">Postar</a></li>
                <li><a href="princip_pagin.php">Poemas</a></li>
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === 1): ?>
                    <li><a href="tabela.php">Gerenciar Artes</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="princip_pagin.php">Poemas</a></li>
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
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $titulo = htmlspecialchars($row['titulo']);
                    $imagem = htmlspecialchars($row['imagem']);
                    $conteudo = htmlspecialchars($row['conteudo']);
                    $categoria = $row['categoria'] ?? "Sem categoria";
                    $autor = htmlspecialchars($row['autor']) ?? "Autor desconhecido"; // Exibe "Autor desconhecido" caso o autor não seja encontrado

                    echo '
                    <div class="card">
                        <div class="card-inner" style="--clr:#fff;">
                            <div class="box">
                                <div class="imgBox">
                                    <img src="' . $imagem . '" alt="' . $titulo . '">
                                </div>
                            </div>
                        </div>
                        <div class="content">
                            <h3>' . $titulo . '</h3>
                            <p class="descricao">' . $conteudo . '</p>
                            <p><strong>Autor:</strong> ' . $autor . '</p> <!-- Exibe o nome do autor -->
                            <ul class="info-list">
                                <li class="packaging" style="--clr-tag:#70b3b1;">
                                    Arte
                                </li>
                                <li class="branding" style="--clr-tag:#d3b19a;">
                                    ' . htmlspecialchars($categoria) . '
                                </li>
                            </ul>
                            <button class="curtir-btn" data-arte-id="' . $id . '">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                    </div>';
                }
            } else {
                echo "<p>Nenhuma arte encontrada.</p>";
            }

            // Fechando a conexão
            $conn->close();
            ?>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Carregar curtidas iniciais
            document.querySelectorAll('.curtir-btn').forEach(btn => {
                const arteId = btn.getAttribute('data-arte-id');
                // Verificar se já foi curtido (simulação)
                // Implementar verificação real com AJAX
                const curtido = localStorage.getItem(`curtida-arte-${arteId}`);
                if (curtido) {
                    btn.classList.add('curtido');
                    btn.querySelector('i').classList.replace('fa-regular', 'fa-solid');
                }
            });

            // Lidar com cliques nos botões de curtir
            document.querySelectorAll('.curtir-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const arteId = this.getAttribute('data-arte-id');
                    const heartIcon = this.querySelector('i');
                    const isCurtido = this.classList.contains('curtido');

                    // Simulação - substituir por chamada AJAX real
                    if (isCurtido) {
                        // Descurtir
                        this.classList.remove('curtido');
                        heartIcon.classList.replace('fa-solid', 'fa-regular');
                        localStorage.removeItem(`curtida-arte-${arteId}`);
                    } else {
                        // Curtir
                        this.classList.add('curtido');
                        heartIcon.classList.replace('fa-regular', 'fa-solid');
                        localStorage.setItem(`curtida-arte-${arteId}`, 'true');
                    }

                });
            });
        });
    </script>
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

    <script src="./script.js"></script>
</body>

</html>
