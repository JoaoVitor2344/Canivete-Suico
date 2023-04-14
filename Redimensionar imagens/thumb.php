<?php
try {
    // Caminho da imagem original
    $filename = "https://fomentotech.com.br/".$_GET["img"];
    
    // Criando uma nova instância do Imagick
    $imagick = new \Imagick($filename);
    
    // Obtendo a largura e a altura da imagem original
    $width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();
    
    // Definindo a largura e a altura máximas para a imagem redimensionada
    $newWidth = $_GET['width'];
    $newHeight = $_GET['height'];
    
    // Redimensionando a imagem mantendo a proporção
    $imagick->scaleImage($newWidth, $newHeight, true);
    
    // Obtendo os dados da imagem redimensionada
    $imageData = $imagick->getImageBlob();
    
    // Gerando a imagem temporária
    header('Content-Type: image/jpeg');
    echo $imageData;
    
    // Liberando a memória
    $imagick->destroy();
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
