// Função para compartilhar usando a Web Share API
function share() {
  if (navigator.share !== undefined) {
	navigator.share({
	  title: 'Fomento Tech',
	  text: '<?= $noticia->titulo ?>',
	  url: 'https://fomentotech.com.br/noticias?id=<?= $noticia->id ?>',
	});
  }
}
			
// Verificar se o navegador suporta a Web Share API
if (navigator.share !== undefined) {
	
} else {
  // Esconder o botão de compartilhamento nativo
  document.getElementById("share").style.display = "none";
}