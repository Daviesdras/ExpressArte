<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = 'banco_exart';


// Cria conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $poema = $_POST['poema'];
    
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
    $sql = "INSERT INTO postagens (titulo, poema, imagem) VALUES ('$titulo', '$poema', '$imagem_caminho')";

    if ($conn->query($sql) === TRUE) {
        echo "Nova postagem criada com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }

    // Fecha a conexão
    $conn->close();
}
?>
<html><br><a href="index.php">Voltar para a página principal</a></html>
