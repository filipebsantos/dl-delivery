function openWhatsApp() {
    // Obtém o valor do número de telefone do campo de entrada
    const phoneNumber = document.getElementById('wppTel').value;
    // Constrói a URL do WhatsApp
    const waUrl = `https://wa.me/55${phoneNumber}`;
    // Abre a URL em uma nova janela
    window.open(waUrl, '_blank');
}