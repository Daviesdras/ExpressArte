document.addEventListener('DOMContentLoaded', () => {
    const curtirBtns = document.querySelectorAll('.curtir-poema-btn');

    curtirBtns.forEach(button => {
        button.addEventListener('click', () => {
            const postagemId = button.getAttribute('data-postagem-id');
            
            fetch('curtidas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `postagem_id=${postagemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.action === 'added') {
                    const contador = button.querySelector('.contador-curtidas-poema');
                    contador.textContent = parseInt(contador.textContent) + 1;
                } else if (data.action === 'removed') {
                    const contador = button.querySelector('.contador-curtidas-poema');
                    contador.textContent = parseInt(contador.textContent) - 1;
                }
            })
            .catch(error => console.error('Erro:', error));
        });
    });
});
