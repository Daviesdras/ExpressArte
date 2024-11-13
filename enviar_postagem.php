<?php session_start(); // Inicia a sessão

// Configurações de conexão com o banco de dados
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
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $poema = mysqli_real_escape_string($conn, $_POST['poema']);
    
    // Processa a imagem
    $imagem = $_FILES['imagem'];
    $imagem_nome = basename($imagem["name"]);
    $imagem_caminho = "uploads/" . $imagem_nome;
    
    // Move o arquivo para o diretório de uploads
    if (move_uploaded_file($imagem["tmp_name"], $imagem_caminho)) {
        echo "Imagem enviada com sucesso.";
    } else {
        echo "Erro ao enviar a imagem.";
    }

    // Insere dados na tabela postagens
    $usuario_id = $_SESSION['id_usuario']; // Presumindo que você já tenha iniciado a sessão e armazenado o ID do usuário
    $sql = "INSERT INTO postagens (usuario_id, titulo, poema, imagem) VALUES ('$usuario_id', '$titulo', '$poema', '$imagem_caminho')";

    if ($conn->query($sql) === TRUE) {
        echo "Nova postagem criada com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }

    // Fecha a conexão
    $conn->close();
}
?>
<a href="index.php">Voltar para a página principal</a>
