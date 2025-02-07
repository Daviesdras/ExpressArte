<?php 
session_start(); // Inicia a sessão

// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_exart";

// Cria conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos obrigatórios foram preenchidos
    if (!isset($_POST['titulo'], $_POST['poema'], $_POST['categoria'], $_FILES['imagem'])) {
        die("Erro: Todos os campos são obrigatórios!");
    }

    // Captura e sanitiza os dados do formulário
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $poema = mysqli_real_escape_string($conn, $_POST['poema']);
    $categoria_id = mysqli_real_escape_string($conn, $_POST['categoria']);

    // Verifica se o usuário está logado
    if (!isset($_SESSION['id_usuario'])) {
        die("Erro: Usuário não autenticado.");
    }
    
    $usuario_id = $_SESSION['id_usuario'];

    // Processa a imagem
    $imagem = $_FILES['imagem'];
    if ($imagem['error'] === 0) {
        $imagem_nome = basename($imagem["name"]);
        $imagem_caminho = "uploads/" . $imagem_nome;

        if (!file_exists('uploads/')) {
            mkdir('uploads/', 0777, true); // Cria a pasta se não existir
        }

        if (!move_uploaded_file($imagem["tmp_name"], $imagem_caminho)) {
            die("Erro ao mover a imagem para a pasta de uploads.");
        }
    } else {
        die("Erro ao enviar a imagem: Código de erro " . $imagem['error']);
    }

    // Insere os dados na tabela postagens
    $sql = "INSERT INTO postagens (usuario_id, titulo, poema, imagem, categoria_id) 
            VALUES ('$usuario_id', '$titulo', '$poema', '$imagem_caminho', '$categoria_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: princip_pagin.php"); // Redireciona após o sucesso
        exit();
    } else {
        echo "Erro ao inserir no banco de dados: " . $conn->error;
    }

    // Fecha a conexão
    $conn->close();
}
?>
