<?php
// A sessão precisa ser iniciada em cada página diferente
if (!isset($_SESSION)) session_start();
$nivel_necessario = 5;
// Verifica se não há a variável da sessão que identifica o usuário
if (!isset($_SESSION['UsuarioID']) OR ($_SESSION['UsuarioNivel'] > $nivel_necessario)) {
	// Destrói a sessão por segurança
	session_destroy();
	// Redireciona o visitante de volta pro login
	header("Location: /site/login/index.php"); exit;
}
?>
<?php

//recebe os dados 
$nsenha = $_POST["nsenha"];
$rsenha = $_POST["rsenha"];
//$rsenha = sha1($_POST["rsenha"]);
$iduser = $_SESSION['iduser'];
//validação de dados
if ( $nsenha == "" ) {
    echo '<head><meta http-equiv=refresh content="0; URL=/site/gestorserver/log/?pagina=alterar_senha"></head>';
  echo "<script type='text/javascript'> alert('É necessario uma senha');</script>"; 
  }
elseif ( $nsenha != $rsenha) {
    echo '<head><meta http-equiv=refresh content="0; URL=/site/gestorserver/log/?pagina=alterar_senha"></head>';
  echo "<script type='text/javascript'> alert('Repita a senha corretamente.');</script>"; 
  }
elseif (strlen($nsenha)<5) {
     echo '<head><meta http-equiv=refresh content="0; URL=/site/gestorserver/log/?pagina=alterar_senha"></head>';
  echo "<script type='text/javascript'> alert('A senha deve conter pelomenos 5 caracteres');</script>"; 
  }
    
else {  
require_once('../Connections/site.php');
//$iduser = $_SESSION['iduser'];
//echo $iduser;
//seleciona banco de dados
mysql_select_db($database_site, $site);
$novasenharedefinida = sha1($nsenha);
$sqlredefinirsenha = "UPDATE `$database_site`.`usuarios` SET `senha` = '$novasenharedefinida' WHERE `usuarios`.`iduser` = '$iduser';";
//verifica se foi inserido corretamente os dados na tabela
mysql_query($sqlredefinirsenha) or die ("nao foi possivel redefinir a nova senha");

		// Redireciona o visitante
 echo '<head><meta http-equiv=refresh content="0; URL=/site/gestorserver/log/?pagina=status"></head>';
	// Mensagem de erro quando os dados são inválidos e/ou o usuário não foi encontrado
	echo "<script type='text/javascript'> alert('Senha alterada com sucesso.');</script>"; 
} 

?>

