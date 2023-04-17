<?php 
if(isset($_POST['consultar'])) {
    extract($_POST);
    
    $consulta = $conn->query("SELECT * FROM cadastro_subsidios WHERE cnpj_cpf = '$consultar'");
    if($consulta->rowCount() > 0) {
        $consulta = $consulta->fetch(PDO::FETCH_OBJ);
        $valor = intval($consulta->consultas)+1;
        $conn->query('UPDATE cadastro_subsidios SET consultas = "'.$valor.'" WHERE cnpj_cpf = "'.$consultar.'" ');
        echo '{"nome":"'.$consulta->empresa_nome.'"}';
        http_response_code(200);
    }
    else http_response_code(400);
}
else if(isset($_POST['empresanome'])) {
    extract($_POST);
    
    try {
        if($empresanome == "") {
            echo json_encode([
                'campo' => 'empresanome'
            ]);
            http_response_code(400);
            exit;
        }
        
        if($email == "") {
            echo json_encode([
                'campo' => 'email'
            ]);
            http_response_code(400);
            exit;
        }
        
        if($cnpjcpf == "") {
            echo json_encode([
                'campo' => 'cnpjcpf'
            ]);
            http_response_code(400);
            exit;
        }
        
        if($conn->query("SELECT * FROM cadastro_subsidios WHERE cnpj_cpf = '$cnpjcpf'")->rowCount() > 0) {
            echo json_encode([
                'cnpjcpf' => 'cnpjcpf'
            ]);
            http_response_code(400);
            exit; 
        }
        
        if($descricao_negocio == "") {
            echo json_encode([
                'campo' => 'descricao_negocio'
            ]);
            http_response_code(400);
            exit;
        }
        
        // Função que gera um token de acesso unico
        function generateToken($length = 32) {
            $token = "";
            $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
            $codeAlphabet.= "0123456789";
            
            $max = strlen($codeAlphabet); // edited
            
            for ($i=0; $i < $length; $i++) {
                $token .= $codeAlphabet[random_int(0, $max-1)];
            }
            
            return $token;
        }
        
        // Utilize PDO para realizar a consulta
        function generateUniqueTokenPDO($pdo, $table, $column) {
            $token = generateToken();
            $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :token");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return generateUniqueTokenPDO($pdo, $table, $column);
            }
            return $token;
        }
        
        $token = generateUniqueTokenPDO($conn, 'cadastro_subsidios', 'token');
        
        require 'PHPMailer/class.phpmailer.php';
        
        $msg = '<link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
        <div style="text-align: justify;">
            <p><div style="text-align: center; padding: 20px; background-color: rgb(0,94,184);"><img src="https://jairperes.com.br/fomento/assets/img/logo.png"></div></p>
            <div>
                <p style="font-size: 21px!important; font-weight: 700; color: black;">Olá, '.$empresanome.',</p>
                <p>Gostaríamos de informar sua solicitação foi cadastrada para participar do nosso programa podendo receber um subsídio de até R$ 2.000,00 para investir em tecnologia, como site, lojas virtuais, páginas e sistemas web.</p>
                <p>O cadastro é totalmente gratuito e será submetido a uma avaliação por um consultor credenciado. Após a avaliação, o consultor entrará em contato com você para fornecer mais informações sobre o passo a passo do programa e orientá-lo(a) em relação aos próximos passos.</p>
                <p>Caso tenha alguma dúvida ou precise de ajuda, nossa equipe de suporte estará à disposição para ajudá-lo(a). Entre em contato conosco através do e-mail '.getStatic($conn, 'site_email').'.</p>
                <p>Agradecemos pela confiança em nossa parceria e esperamos que essa relação traga muitos benefícios para você e sua empresa.</p>
                <p>Atenciosamente,</p>
                <p>Fomento Tech</p>
                <p><a href="https://jairperes.com.br/fomento/cadastro?token='.$token.'">Concluir o cadastro</a></p>
            </div>
        </div>
        
        <style>
            p {
                font-family: "Montserrat";
                style="font-size: 15px;";
            }
        </style>';
        
        $date = date("d-m-Y H:i:s");
        if(sendMail($empresanome, $email, 'Cadastro no programa de investimento', $msg, $conn)) {
            $conn->query("INSERT INTO cadastro_subsidios(tipo, empresa_nome, email, cnpj_cpf, descricao_negocio, token, data_cadastro) VALUES('$tipo', '$empresanome', '$email', '$cnpjcpf', '$descricao_negocio', '$token', '$date')");
        }
    }
    catch(Exception $e) {
        echo json_encode([
            'campo' => $e->getMessage()
        ]);
        http_response_code(400);
    }
}
else {
    chaveSite($conn);
    insertVisitor($conn); // Carrega o contador sempre que chamar a home
    
    include 'views/navbar.php'; 
    ?>
    
    <link rel="stylesheet" href="assets/css/subsidios.css">
    <div id="preloader"><div class="loader" id="loader-1"></div></div>
    
    <?php 
    $pagina = $conn->query("SELECT * FROM paginaSubsidios WHERE id = 1")->fetch(PDO::FETCH_OBJ);
    $topicos = [
        [
            'text' => $pagina->topico1,
            'img' => 'investimento.png'
            
        ],
        [
            'text' => $pagina->topico2,
            'img' => 'treinamento.png'
            
        ],
        [
            'text' => $pagina->topico3,
            'img' => 'marketing.png'
        ],
    ];
    ?>
    
    <!-- Conteúdo -->
    <div class="w-100 d-flex flex-column justify-content-center align-items-center"> <!-- Alinhamento. Melhor não remover -->
        <div class="conteudo"> 
            <?php if($pagina->banner1 != "") { ?> <div class="w-100"><img class="w-100" src="/thumb.php?img=uploads/paginaSubsidios/<?= $pagina->banner1 ?>&width=1200&height=300"></div> <?php } ?>
            <div class="content w-100 mt-5">
                <div class="divPorque">
                    <h4 class="titulo text-md-left text-center">Por que solicitar subsídio?</h4>
                    <div class="d-flex flex-md-row flex-column align-items-center mt-md-5 mt-2">
                        <div class="topicos">
                            <?php foreach($topicos as $topico) { ?>
                                <div class="topico">
                                    <div class="col-md-2 col-12"><img class="w-100" src="/thumb.php?img=assets/img/icons/<?= $topico['img'] ?>&width=100&height=100"></div>
                                    <div class="col-md-10 col-12 conteudo"><?php echo $topico['text'] ?></div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="d-md-block d-none" style="width: 30%"><img class="img-fluid" src="/thumb.php?img=assets/img/icons/presentation.png&width=300&height=300"></div>
                    </div>
                </div>
            </div>
            <div class="content mt-5">
                <div class="d-flex flex-column align-items-center">
                    <div class="divEntenda flex-md-row flex-column">
                        <div class="motivo col-md-4 col-12" style="border-right: 1px solid grey;">
                            <div>
                                <i class="fa-solid fa-building mb-4" style="font-size: 35px;"></i>
                                <h4>Empresa</h4>
                                <div class="conteudo mb-4">Sua empresa pode receber ajuda financeira! Com cadastro simples e CNPJ, invista em pesquisa, desenvolvimento e expansão</div>
                            </div>
                            <button class="btnSolicitar rounded" id="cnpj" data-toggle="modal" data-target="#myModal">SOLICITAR</button>
                        </div>
                        <div class="motivo col-md-4 col-12">
                            <div>
                                <i class="fa-solid fa-person mb-4" style="font-size: 35px;"></i>
                                <h4>Pessoa</h4>
                                <div class="conteudo mb-4">Com alguns cliques, cadastre seu CPF para solicitar subsídio. É rápido e seguro. Não perca tempo, aproveite já!</div>
                            </div>
                            <button class="btnSolicitar rounded" id="cpf" data-toggle="modal" data-target="#myModal">SOLICITAR</button>
                        </div>
                        <div class="motivo col-md-4 col-12" style="border-left: 1px solid grey;">
                            <div>
                                <i class="fa-solid fa-magnifying-glass mb-4" style="font-size: 35px;"></i>
                                <h4>Consultar</h4>
                                <div class="conteudo mb-4">Já cadastrou sua solicitação? Descubra se foi aprovado e não perca essa chance de investir no que importa</div>
                            </div>
                            <button class="btnSolicitar rounded" id="consultar" data-toggle="modal" data-target="#myModalConsultar">CONSULTAR</button>
                        </div>
                    </div>
                    <!--Form Cadastrar Solicitação-->
                    <div class="modal fade comentar" id="myModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" style="padding-left: 15px;"></h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form class="formCadastro" action="subsidios" method="POST">
                                        <input type="hidden" name="tipo" value="juridica">
                                        <div class="d-flex flex-md-row flex-column col-12 mb-3">
                                            <div class="col-md-6 col-12 pe-md-2 pe-0 mb-md-0 mb-3" style="padding: 0;"><input class="w-100 rounded" type="text" name="empresanome" placeholder=""></div>
                                            <div class="col-md-6 col-12 ps-md-2 ps-0" style="padding: 0;"><input class="w-100 rounded" type="email" name="email" placeholder="E-mail"></div>
                                        </div>
                                        <div class="w-100 mb-3">
                                            <div class="col-12"><input class="w-100 rounded" type="tel" name="cnpjcpf" placeholder=""></div>
                                        </div>
                                        <div class="w-100 mb-3">
                                            <div class="col-12"><textarea class="w-100 rounded" name="descricao_negocio" rows="4" cols="50" placeholder="Qual a principal atividade do seu negócio e como pode ser parceira?"></textarea></div>
                                        </div>
                                        <div class="modal-footer">
                                            <!--<button class="btn btn-secondary" data-dismiss="modal">Fechar</button>-->
                                            <button type="submit" class="btn col-12" style="background: rgb(47,178,110); color: white;">Salvar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Form Consultar Solicitação-->
                    <div class="modal fade comentar" id="myModalConsultar">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 style="padding-left: 15px; margin-bottom: 0;">Consultar Solicitação</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form class="formConsultar" action="subsidios" method="POST">
                                        <div class="w-100 mb-3">
                                            <div class="col-12"><input class="w-100 rounded" type="tel" name="consultar" placeholder="CNPJ / CPF"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn col-12" style="background: rgb(47,178,110); color: white;">Consultar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($pagina->banner2 != "") { ?> <div class="w-100 mt-5"><img class="w-100" src="/thumb.php?img=uploads/paginaSubsidios/<?= $pagina->banner2 ?>&width=1200&height=200"></div> <?php } ?>
            <div class="content mt-5">
                <div class="divConteudo"><?php echo $pagina->conteudo ?></div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            $(".btnSolicitar").click(function() {
                tipo = $(this).attr('id');
                if(tipo == 'cnpj') {
                    $(".modal-title").text("Cadastro Pessoa Jurídica"); 
                    $("[name=empresanome]").attr("placeholder", "Empresa");
                    $("[name=tipo]").val('juridica');
                    $("[name=cnpjcpf]").attr("placeholder", "CNPJ");
                    $("[name=cnpjcpf]").mask('00.000.000/0000-00');
                }
                else if(tipo == 'cpf') {
                    $(".modal-title").text("Cadastro Pessoa Física");
                    $("[name=empresanome]").attr("placeholder", "Nome");
                    $("[name=tipo]").val('fisica');
                    $("[name=cnpjcpf]").attr("placeholder", "CPF");
                    $("[name=cnpjcpf]").mask('000.000.000-00');
                }
            });
            
            $(".formCadastro").submit(function(e) {
                $("#preloader").show();
                e.preventDefault();
                $.ajax({
                    url: $(this).attr("action"),
                    method: $(this).attr("method"),
                    data: $(this).serialize(),
                    success: function(response) {
                        $("#preloader").hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Cadastro Realizado',
                            html:
                            'Olá, '+$("[name=empresanome]").val()+', seu cadastro foi enviado para análise e nossa banca de investidores estará avaliando e nos próximos dias um de nossos consultores credenciados entrará em contato.' +
                            '<br>' +
                            '<span style="font-size: 14px; font-style: italic;">Prazo médio para avaliação de 15 dais uteis</span>'
                        });
                    }
                }).fail(function(response) {
                    $("#preloader").hide();
                    
                    if(response.responseText.indexOf('{"campo":"') != -1) {
                        response = response.responseText.substr(response.responseText.indexOf('{"campo":"'), response.responseText.length);
                        response = response.replace('{"campo":"', '');
                        response = response.replace('"}', ''); 
                        
                        if(response == 'empresanome') {
                            if($('.modal-title').text().indexOf('Jurídica') != -1) response = 'empresa';
                            else if($('.modal-title').text().indexOf('Física')) response = 'nome'
                        }
                        
                        message = 'Preenchar o campo '+response;
                    }
                    else {
                        response = response.responseText.substr(response.responseText.indexOf('{"cnpjcpf":"'), response.responseText.length);
                        response = response.replace('{"cnpjcpf":"', '');
                        response = response.replace('"}', ''); 
                        
                        if(response == 'cnpjcpf') {
                            if($('.modal-title').text().indexOf('Jurídica') != -1) message = 'CNPJ Já cadastrado';
                            else if($('.modal-title').text().indexOf('Física')) message = 'CPF Já cadastrado';
                            
                        }
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Cadastro Parceria',
                        text: message
                    });
                }); 
            });
            
            $(".formConsultar").submit(function(e) {
                $("#preloader").show();
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'), 
                    data: $(this).serialize(),
                    
                    success: function(response) {
                        response = response.substr(response.indexOf('{"nome":"'), response.length);
                        response = response.replace('{"nome":"', '');
                        response = response.replace('"}', ''); 
                        
                        nome = response.replace('\u00e3', 'ã');

                        $("#preloader").hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Resultado da Consulta',
                            html:
                            'Olá, '+nome+', seu cadastro foi enviado para análise e nossa banca de investidores estará avaliando e nos próximos dias um de nossos consultores credenciados entrará em contato.' +
                            '<br>' +
                            '<span style="font-size: 14px; font-style: italic;">Prazo médio para avaliação de 15 dais uteis</span>'
                        });
                    }
                }).fail(function() {
                   $("#preloader").hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Consulta',
                        text: 'CNPJ / CPF não encontrado'
                    }); 
                });
            });
            
             $('[name=consultar]').on('keydown', function() {
                if($(this).val().length+1 > 14) $('[name=consultar]').mask('00.000.000/0000-00', {reverse: true});
                else $('[name=consultar]').mask('000.000.000-00', {reverse: true});
            });
        }); 
    </script> <?php 
    
    include 'views/footer.php'; 
}
?>

<?php 
function sendMail($nomeDestino, $emailDestino, $assunto, $msg, PDO $conn, $arquivo = ""){
        // $montaMsg = '<div style="text-align: left"><img src="../assets/img/logo-n.png"></div>';
        // $montaMsg .= '<br /><div style="font-family: Arial; color: #666; font-size: 14px;">'.$msg.'<br /><br />';
        
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->SMTPDebug   = 1;
		$mail->SMTPSecure = 'ssl';
		$mail->Debugoutput = 'html';
		$mail->Host        = getStatic($conn, 'site_mail_host');
		$mail->Port        = getStatic($conn, 'site_mail_porta');
		$mail->SMTPAuth    = true;
		$mail->Username    = getStatic($conn, 'site_mail_user'); 
		$mail->Password    = getStatic($conn, 'site_mail_pass');
		$mail->SetFrom(getStatic($conn, 'site_mail_user'), getStatic($conn, 'site_mail_exibicao'));
		$mail->AddAddress($emailDestino, utf8_decode($nomeDestino));
		$mail->Subject = utf8_decode($assunto);
		$mail->msgHTML(utf8_decode($msg));
		if($arquivo == true)
			$mail->AddAttachment($arquivo);

		if(!$mail->send()){
			saveLog($conn, "Falha envio de e-mail: ".$mail->ErrorInfo);
			return false;
		}else{
			return true;
		}
	}
?>