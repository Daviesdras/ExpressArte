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
$sql_usuario = "SELECT nome, email, imagem_perfil FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows > 0) {
    $usuario = $result_usuario->fetch_assoc();
} else {
    echo "Usuário não encontrado.";
    exit();
} ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações</title>
    <link rel="stylesheet" href="configuracao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <i class="fa-solid fa-arrow-left" onclick="history.back()"></i>
        <h1>Configurações</h1>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
        <form action="upload_imagem.php" method="POST" enctype="multipart/form-data">
            <label for="imagem_perfil">Alterar Imagem de Perfil:</label>
            <input type="file" name="imagem_perfil" id="imagem_perfil" accept="image/*" required>
            <img id="preview" src="#" alt="Pré-visualização da imagem" style="display: none;">
            <button type="submit" id="submit-button" style="display: none;">Salvar</button>
        </form>
        <p class="logout"><a href="logout.php" id="logout-link">Sair da conta</a></p>
    </div>

    <!-- Modal de Confirmação -->
    <div id="confirmation-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <p>Você tem certeza que deseja sair da conta?</p>
            <button id="confirm-logout">Sim</button>
            <button id="cancel-logout">Não</button>
        </div>
    </div>

    <script>
        document.getElementById('imagem_perfil').addEventListener('change', function(event) {
            var submitButton = document.getElementById('submit-button');
            var preview = document.getElementById('preview');
            var file = event.target.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            if (file) {
                reader.readAsDataURL(file);
                submitButton.style.display = 'block';
            } else {
                preview.style.display = 'none';
                submitButton.style.display = 'none';
            }
        });

        var logoutLink = document.getElementById('logout-link');
        var modal = document.getElementById('confirmation-modal');
        var closeButton = document.querySelector('.close-button');
        var confirmLogout = document.getElementById('confirm-logout');
        var cancelLogout = document.getElementById('cancel-logout');

        logoutLink.addEventListener('click', function(event) {
            event.preventDefault();
            modal.style.display = 'block';
        });

        closeButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        cancelLogout.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        confirmLogout.addEventListener('click', function() {
            window.location.href = logoutLink.href;
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>