function showSection(sectionId) {
    // Esconde todas as seções
    const sections = document.querySelectorAll('.tab-section');
    sections.forEach(section => section.style.display = 'none');
  
    // Remove a classe ativa de todos os botões
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(button => button.classList.remove('active'));
  
    // Mostra a seção correspondente e ativa o botão
    document.getElementById(sectionId).style.display = 'block';
    document.querySelector(`.tab-button[onclick="showSection('${sectionId}')"]`).classList.add('active');
  }