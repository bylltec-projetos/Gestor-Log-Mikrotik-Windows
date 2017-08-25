<?php
// A sess�o precisa ser iniciada em cada p�gina diferente
if (!isset($_SESSION))
    session_start();
$nivel_necessario = 5;
// Verifica se n�o h� a vari�vel da sess�o que identifica o usu�rio
if (!isset($_SESSION['UsuarioID']) OR ( $_SESSION['UsuarioNivel'] > $nivel_necessario)) {
    // Destr�i a sess�o por seguran�a
    session_destroy();
    // Redireciona o visitante de volta pro login
    header("Location: /site/login/index.php");
    exit;
}
?>
<?php set_time_limit(0); ?>
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../../Connections/site.php');
$iduser = $_SESSION['iduser'];
$data1 = mysql_real_escape_string($_REQUEST['data1']);
$data2 = mysql_real_escape_string($_REQUEST['data2']);
$tipo = mysql_real_escape_string($_REQUEST['tipo']);
$conta = mysql_real_escape_string($_REQUEST['conta']);
$ip = mysql_real_escape_string($_REQUEST['ipusuariolog']);
//$data1br = date('d/m/Y', strtotime($data1));
//$data2br = date('d/m/Y', strtotime($data2));
//if ( $data1 == "") {
//   $data1 = date('Y-m-d', strtotime("-7 days")); 
//   
//  
//}
//if ( $data2 == "") {
//    
//   $data2 = date('Y-m-d'); 
//  
//}
//echo '<tr><td>Conta: '.$conta.'</td>  <td>'$tipo'</td>  <td>'$data1''$data2'</td> </tr>';
//echo $iduser;
//seleciona banco de dados
mysql_select_db($database_site, $site);

$sqlselecionausuariolog = "SELECT * FROM `usuario_log` ";
$queryselecionausuariolog = mysql_query($sqlselecionausuariolog) or die("sql select erro");

if ($ip != "") {

    $sqlselecionausuariolog2 = "SELECT * FROM `usuario_log` where `ipusuariolog`= '$ip' ";
    $queryselecionausuariolog2 = mysql_query($sqlselecionausuariolog2) or die("sql select erro2");
    $row_rsselecionausuariolog2 = mysql_fetch_assoc($queryselecionausuariolog2);
    $usuariolog = $row_rsselecionausuariolog2['usuariolog'];
}

//SELECT dominio, Count(dominio) AS ContDominio FROM log WHERE `data`>='2014-09-12' and `data`<='2014-09-13' GROUP BY  Dominio ORDER BY `ContDominio` DESC LIMIT 0,30
$queryfiltro = "SELECT dominio, Count(dominio) AS ContDominio FROM log WHERE `linhalog` LIKE '%$ip%' and `tipo`='Dominio' and `data`>='$data1' and `data`<='$data2' GROUP BY  Dominio ORDER BY `ContDominio` DESC LIMIT 0,30";

$resultfiltro = mysql_query($queryfiltro) or die(mysql_error());

while ($row_rslistafiltrografico2 = mysql_fetch_assoc($resultfiltro)) {
    $dominio = $row_rslistafiltrografico2['dominio'];
    $queryfiltro2 = "SELECT * FROM log WHERE `dominio` LIKE '$dominio' AND `linhalog` LIKE '%$ip%' and `tipo`='Dominio' and `data`>='$data1' and `data`<='$data2'  ";
    $resultfiltro2 = mysql_query($queryfiltro2) or die(mysql_error());
    $resultadototalfiltro = mysql_num_rows($resultfiltro2);
    $nomefiltrocategoria = $row_rslistafiltrografico2["dominio"];
//echo $resultadototalfiltro;

    if ($resultadototalfiltro <= 0) {

        $resultadototalfiltro = 0;
    } else {


        $tudo = "['$nomefiltrocategoria',       $resultadototalfiltro],";
        //echo "['$nomefiltrocategoria',       $resultadototalfiltro],";  
        $tudo2 = $tudo2 . $tudo;
    }
}
//echo $tudo2;
?>
<form action="?pagina=estatistica" method="POST">

    <table border="0" align="center">

        <tbody>
            <tr>
                <td>Usuario:</td>
                <td><select name="ipusuariolog" id="usuariolog">

<?php
echo '<option value="">Todos</option>';
while ($row_rsselecionausuariolog = mysql_fetch_assoc($queryselecionausuariolog)) {

    echo '<option value="' . $row_rsselecionausuariolog['ipusuariolog'] . '">' . $row_rsselecionausuariolog['usuariolog'] . '</option>';
}
?>



                    </select></td>
                <td>Tipo:</td>
                <td><select name="tipografico" id="tipografico">

<?php
echo '<option value="pizza">Pizza</option>';
echo '<option value="funil">Funil</option>';
?>



                    </select></td>
                <td>De:</td>
                <td><input type="date" name="data1" value="<?php echo $data1; ?>"></td>
                <td>Até:</td>
                <td><input type="date" name="data2" value="<?php echo $data2; ?>"></td>
                <td><input type="submit" value="ok" /></td>
            </tr>
        </tbody>
    </table>
</form>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Grafico Funil</title>

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript">
            $(function () {

                $('#container').highcharts({
                    chart: {
                        type: 'funnel',
                        marginRight: 400
                    },
                    title: {
                        text: '<?php echo "Grafico de utilização por Dominio de $usuariolog IP: $ip " ?>',
                        x: -50
                    },
                    plotOptions: {
                        series: {
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b> ({point.y:,.0f})',
                                color: 'black',
                                softConnector: true
                            },
                            neckWidth: '50%',
                            neckHeight: '0%'

                                    //-- Other available options
                                    // height: pixels or percent
                                    // width: pixels or percent
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    series: [{
                            name: 'Visitas:',
                            data: [
<?php
echo $tudo2;
?>
                            ]
                        }]
                });
            });
        </script>
    </head>
    <body>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/funnel.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>

        <div id="container" style="min-width: 410px; max-width: 600px; height: 100%; margin: 0 auto"></div>
    </body>
</html>