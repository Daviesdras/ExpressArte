<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_exart";

// Conexão ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtendo o ID do poema pela URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta ao banco de dados com junção
    $sql = "
        SELECT postagens.titulo, postagens.poema, postagens.imagem, usuarios.nome 
        FROM postagens 
        JOIN usuarios ON postagens.usuario_id = usuarios.id 
        WHERE postagens.id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se há resultados
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $titulo = $row['titulo'];
        $imagem = $row['imagem'];
        $poema = $row['poema'];
        $nome_artista = $row['nome'];
    } else {
        $titulo = "Poema não encontrado";
        $poema = "";
        $imagem = ""; // Defina uma imagem padrão, se necessário
        $nome_artista = "Desconhecido";
    }

    $stmt->close();
} else {
    die("ID do poema não fornecido.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="poema.css">
</head>

<body>
    <nav>
        <a class="logo" href="#">
            <span id="logo-s"><img src="imagens/Express_logo.png" width="170px" alt="logo"></span>
        </a>
        <a href="index.php" class="voltar">Voltar</a>

        <div class="Quadrado">
            <?php if ($imagem): ?>
                <img id="imagem" src="<?php echo htmlspecialchars($imagem); ?>" alt="<?php echo htmlspecialchars($titulo); ?>" width="450px">
            <?php endif; ?>
            <h3 class="titulo"><?php echo htmlspecialchars($titulo); ?></h3>
            <p class="texto"><?php echo nl2br(htmlspecialchars($poema)); ?></p>
            <h3 class="nome"><?php echo "--".htmlspecialchars($nome_artista); ?></h3>
        </div>

</body>

</html>