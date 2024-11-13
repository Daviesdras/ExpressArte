<?php
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

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e sanitiza os dados enviados pelo formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
    $created = date('Y-m-d H:i:s');

    // SQL para inserir os dados na tabela usuarios
    $sql = "INSERT INTO usuarios (nome, email, senha, created) VALUES (?, ?, ?, ?)";

    // Prepara a instrução SQL
    $stmt = $conn->prepare($sql);

    // Liga os parâmetros (bind_param) - "s" indica que os parâmetros são strings
    $stmt->bind_param("ssss", $nome, $email, $senha, $created);

    // Executa a instrução SQL
    if ($stmt->execute()) {
        echo 'Cadastro realizado com sucesso!
        <a href="index.php">Voltar para a página principal</a>';
    } else {
        echo 'Erro ao cadastrar: ' . $stmt->error;
    }

    // Fecha o statement e a conexão
    $stmt->close();
    $conn->close();
}
?>
