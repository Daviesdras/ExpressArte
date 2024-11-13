<?php
session_start(); // Inicia a sessão

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "banco_exart");

// Verifica se houve erros de conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['senha'];

    // Escapando caracteres perigosos para prevenir SQL Injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Consulta para verificar o usuário
    $sql = "SELECT id, nome, senha FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica se a senha está correta
        if (password_verify($password, $row['senha'])) {
            // Salva as informações do usuário na sessão
            $_SESSION['id_usuario'] = $row['id'];
            $_SESSION['nome_usuario'] = $row['nome'];

            // Redireciona para a página inicial ou dashboard
            header("Location:  index.php");

            exit();
        } else {
            echo "Senha incorreta.";
        }
    } else {
        echo "Email não encontrado.";
    }
}

$conn->close();
?>
