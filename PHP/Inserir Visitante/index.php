function insertVisitor($conn){
      $visitor_cookie = 'visitor_id'; // nome do cookie que será usado para identificar o visitante
      $today = date('Y-m-d'); // data atual

      // verifica se o cookie já existe
      if (!isset($_COOKIE[$visitor_cookie])) {
          // define o cookie com um valor aleatório
          $visitor_id = md5(time() . rand());

          // insere o novo registro na tabela sys_visitors
          $sql = "INSERT INTO sys_visitors (visitor_id, uniques, data) VALUES ('$visitor_id', 1, '$today')";
          $conn->query($sql);

          setcookie($visitor_cookie, $visitor_id, time() + (86400 * 365), "/"); // expira em 1 ano

      } else {
          // atualiza o número de visitas apenas se o cookie já existir
          $visitor_id = $_COOKIE[$visitor_cookie];
          $sql = "UPDATE sys_visitors SET uniques = uniques + 1 WHERE visitor_id = '$visitor_id' AND data = '$today'";
          $conn->query($sql); 
      }
  }
