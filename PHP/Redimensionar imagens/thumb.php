<?php
error_reporting(E_ALL);

try {
    // Caminho da imagem original
    $filename = "https://fomentotech.com.br/".$_GET["img"];
    
    // Obtendo as dimensões da imagem original
    list($originalWidth, $originalHeight) = getimagesize($filename);
    
    // Definindo a largura e a altura máximas para a imagem redimensionada
    $newWidth = (isset($_GET['width'])) ? ($_GET['width'] * 70) / 100 : $originalWidth;
    $newHeight =(isset($_GET['height'])) ? ($_GET['height'] * 70) / 100 : $originalHeight;
    
    // Calculando as novas dimensões mantendo a proporção
    if ($originalWidth > $originalHeight) {
       $newHeight = ceil($originalHeight * ($newWidth / $originalWidth));
    } else {
       $newWidth = ceil($originalWidth * ($newHeight / $originalHeight));
    }
    
    // Criando uma nova imagem a partir do arquivo original
    $extensao = pathinfo($filename, PATHINFO_EXTENSION);
    switch ($extensao) {
       case 'jpg':
       case 'jpeg':
          $imagemOriginal = imagecreatefromjpeg($filename);
          break;
       case 'png':
          $imagemOriginal = imagecreatefrompng($filename);
          break;
    }
    
    // Criando uma nova imagem redimensionada
    $imagemRedimensionada = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($imagemRedimensionada, $imagemOriginal, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // Convertendo a imagem redimensionada para o formato WebP
    header('Content-Type: image/webp');
    imagewebp($imagemRedimensionada);
    
    // Liberando a memória
    imagedestroy($imagemOriginal);
    imagedestroy($imagemRedimensionada);

} catch (Exception $e) {
    echo $e->getMessage();
}
?>
