<?php

require 'PHPMailer/class.phpmailer.php';
    
$msg = '<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<div style="text-align: justify;">
<p><div style="text-align: center; padding: 20px; background-color: rgb(0,94,184);"><img src="https://jairperes.com.br/fomento/assets/img/logo.png"></div></p>
<div>
    <p style="font-size: 21px!important; font-weight: 700; color: black;">Olá, '.$responsavel.',</p>
    <p>Gostaríamos de informar que sua solicitação foi cadastrada para participar do nosso programa de investimento em sua empresa ou negócio. Com este programa, você poderá receber um subsídio de até R$ 2.000,00 para investir em tecnologia, tais como sites, lojas virtuais, páginas e sistemas web.</p>
    <p>O cadastro é totalmente gratuito e será avaliado por um consultor credenciado. Após a avaliação, o consultor entrará em contato com você para fornecer mais informações sobre o programa e orientá-lo sobre os próximos passos.</p>
    <p>Se tiver alguma dúvida ou precisar de ajuda, nossa equipe de suporte estará à disposição para ajudá-lo. Entre em contato conosco através do e-mail '.getStatic($conn, 'site_email').'.</p>
    <p>Agradecemos pela confiança em nossa parceria e esperamos que essa relação traga muitos benefícios para você e sua empresa.</p>
    <p>Atenciosamente,</p>
    <p>Fomento Tech</p>
</div>
</div>

<style>
p {
    font-family: "Montserrat";
    style="font-size: 15px;";
}
</style>';

if(sendMail($empresanome, $email, 'Cadastro no programa de investimento', $msg, $conn)) {
$conn->query('INSERT INTO cadastro_parcerias(empresa, cnpj, cep, endereco, bairro, cidade, complemento, numero, responsavel, telefone, whatsapp, email, descricao_negocio , data_cadastro) VALUES("'.$empresa.'", "'.$cnpj.'", "'.$cep.'", "'.$endereco.'", "'.$bairro.'", "'.$cidade.'", "'.$complemento.'", "'.$numero.'", "'.$responsavel.'", "'.$telefone.'", "'.$whatsapp.'", "'.$email.'", "'.$descricao_negocio.'", "'.date("d-m-Y H:i:s").'")');
}

?>
