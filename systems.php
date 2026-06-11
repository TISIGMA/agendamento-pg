
<?php

require_once('controller/systemsController.php');
require_once('utils.php');

$systemsController = new SystemsController($MySQLi);
$systems = $systemsController->findByUser($_SESSION['id']);

if($systems == null) errorAlert('Você não possui nenhum sistema cadastrado. Favor entrar em contato com o administrador.');

?>

<div class="row">
    <div class="col-lg-12">

<?php
if($systems != null){
    echo '<div class="panel-system panel panel-default">
            <div class="panel-body">
                <div class="panel-group" id="accordion">
                    <div class="app-panel">';
?>
            <?php
                foreach ($systems as $system) {
                    echo '<a class="system-box-link" href="'.$system->getSystemUrl().'">
                            <div class="center-box app-box">
                                <div class="center-box app-img">
                                    <img src="'.$system->getIconPath().'">
                                </div>
                                <div class="center-box app-label">
                                    <p class="app-font-label">'.$system->getDescription().'</p>
                                </div>
                            </div>
                        </a>';
                    }
                echo '</div>
                    </div>
                </div>
            </div>';
            }
            ?>
    </div>
</div>
            

      