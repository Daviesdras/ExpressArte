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

// Verifica se o usuário está logado
if (isset($_SESSION['id_usuario'])) {
    $usuario_id = $_SESSION['id_usuario'];

    // Consulta para obter os dados do usuário logado, incluindo a imagem de perfil
    $sql_usuario = "SELECT imagem_perfil FROM usuarios WHERE id = ?";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bind_param("i", $usuario_id);
    $stmt_usuario->execute();
    $result_usuario = $stmt_usuario->get_result();

    // Verifica se o usuário foi encontrado e pega a imagem de perfil
    if ($result_usuario->num_rows > 0) {
        $usuario = $result_usuario->fetch_assoc(); 
        $imagem_perfil = $usuario['imagem_perfil']; // Pega a imagem do perfil
    } else {
        // Caso o usuário não tenha imagem de perfil, use uma imagem padrão
        $imagem_perfil = 'imagens/default-perfil.jpg';
    }

}

// Consulta para pegar todas as postagens (independente do usuário logado)
$sql = "SELECT id, titulo, poema, imagem FROM postagens";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Poemas</title>
  <link rel="stylesheet" href="./style.css">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>
  <nav>
    <a class="logo" href="#">
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
      <li><a href="postar.html">Postar</a></li>
      <li><a href="login.html">Logar</a></li>
      <li><a href="sobrenos/test.html">Sobre</a></li>
      <li><a href="sobrenos/test.html">Artes</a></li>
    </ul>
    <a href="perfil.php">
        <img class="perfil" src="<?php echo $imagem_perfil; ?>" alt="Imagem de perfil" width="100px">
    </a>
  </nav>

  <section>
    <div class="container">
      <?php
      // Loop para exibir as postagens
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $id = $row['id'];
              $titulo = $row['titulo'];
              $imagem = $row['imagem'];
              $poema = $row['poema'];

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
                          <li style="--clr-tag:#d3b19a;" class="branding">Categoria</li>
                          <li style="--clr-tag:#70b3b1;" class="packaging">Subcategoria</li>
                      </ul>
                  </div>
              </div>';
          }
      } else {
          echo "Nenhum poema encontrado.";
      }

      // Fechando a conexão
      $conn->close();
      ?>
    </div>
  </section>

  <script src="./script.js"></script>
</body>
</html>
