<!--Comentários-->
<div class="conteudo mt-5">
	<div class="explore mb-4">Comentários</div>
	<div class="content w-100">
		<div class="comentarios">
			<?php 
			extract($_GET);
			$sql = $conn->query("SELECT * FROM comentarios WHERE idNoticia = $id");
			$comentarios = $sql->fetchAll(PDO::FETCH_OBJ);
			$quantidade = $sql->rowCount();
			?>
			<h4 class="mb-4" style="font-size: 16px; font-weight: 500;"><?php echo $quantidade ?> Comentários</h4>
			<?php foreach($comentarios as $comentario) { ?>
				<div class="comentario mb-4">
					<?php 
					$dateDB = new DateTime($comentario->data);
					$dateAtual = new DateTime(date('Y-m-d H:i:s'));
					$diff = date_diff($dateDB, $dateAtual);
					foreach($diff as $key => $value) {
						if($value > 0) {
							if($key == "y") $key = "a";
							else if($key == "m") $key = "m";
							else if($key == "d") $key = "d";
							else if($key == "i") $key = "min";
							$diff = $value.$key;
							break;
						}
					}
					?>
					<h4 class="nome"><?php echo $comentario->nome ?> <span><?php echo $diff ?></span></h4>
					<span clas="mensagem"><?php echo $comentario->mensagem ?></span>
				</div>
			<?php } ?>
			<div class="modal fade comentar" id="myModal">
			  <div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Enviar Comentário</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<form class="formComentario" action="noticias" method="POST">
							<input type="hidden" name="idNoticia" value="<?php echo $_GET['id'] ?>">
							<div class="d-flex flex-md-row flex-column col-12 mb-3">
								<div class="col-md-6 col-12 pe-md-2 pe-0 mb-md-0 mb-3" style="padding: 0;"><input class="w-100 rounded" type="text" name="nome" placeholder="Digite seu nome"></div>
								<div class="col-md-6 col-12 ps-md-2 ps-0" style="padding: 0;"><input class="w-100 rounded" type="email" name="email" placeholder="Digite seu e-mail"></div>
							</div>
								<div class="w-100 mb-3">
								<div class="col-12"><textarea class="w-100 rounded" name="mensagem" rows="4" cols="50" placeholder="Digite sua mensagem"></textarea></div>
							</div>
							<div class="modal-footer">
								<button class="btn btn-secondary" data-dismiss="modal">Fechar</button>
								<button type="submit" class="btn" style="background: rgb(47,178,110); color: white;">Salvar</button>
							</div>
						</form>
					</div>
				</div>
			  </div>
			</div>
			<button class="btn-comentar rounded" data-toggle="modal" data-target="#myModal">Comentar</button>

			<div class="comentar">
				<form class="formComentario" action="noticias" method="POST">
					<input type="hidden" name="idNoticia" value="<?php echo $_GET['id'] ?>">
					<div class="d-flex flex-md-row flex-column col-12 mb-3">
						<div class="col-md-6 col-12 pe-md-3 pe-0 mb-md-0 mb-3"><input class="w-100 rounded" type="text" name="nome" placeholder="Digite seu nome"></div>
						<div class="col-md-6 col-12 ps-md-3 ps-0"><input class="w-100 rounded" type="email" name="email" placeholder="Digite seu e-mail"></div>
					</div>
					<div class="w-100 mb-3">
						<div class="col-12"><textarea class="w-100 rounded" name="mensagem" rows="4" cols="50" placeholder="Digite sua mensagem"></textarea></div>
					</div>
					<button class="btn-comentar rounded" data-toggle="modal" data-target="#myModal">Comentar</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(".formComentario").submit(function(e) {
	$("#preloader").show();
	e.preventDefault();
	$.ajax({
		url: $(this).attr('action'),
		method: $(this).attr('method'),
		data: $(this).serialize(),
		success: function(response) {
			$("#preloader").hide();
			
			Swal.fire({
				icon: 'success',
				title: 'Comentário Enviado'
			}).then(function() {
				window.location.href='noticias'+window.location.search;
			});
		}
	}).fail(function(response) {
		$("#preloader").hide();
		Swal.fire({
			icon: 'error',
			title: 'Comentário',
			text: 'Preenchar todos os campos corretamente'
		}); 
	}); 
});
</script>