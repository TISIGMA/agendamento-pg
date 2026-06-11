
<?php
    require('evonik-agendamentos/pages/class.php');
    require('conn.php');
    require('evonik-agendamentos/functions.php');

    date_default_timezone_set("America/Sao_Paulo");
    $usuario = new Usuario();

    if(isset($_GET['delete']) && $_GET['delete'] != null){
        if($usuario->deletarUsuario($_GET['delete'], $MySQLi)==true){
            echo messageSuccess('Usuário excluído com sucesso!');
        };
    }
    $usuarios = $usuario->listarUsuarios($MySQLi);
    $users = array();
    
    $count = 0;
    foreach(evonik_records_iter($usuarios) as $dados){ 
        $usuario = new Usuario();
        $usuario->setId($dados['id']);
        $usuario->setNome($dados['nome']);
        $usuario->setUsername($dados['username']);
        $usuario->setPassword($dados['password']);
        $usuario->setData($dados['dataInclusao']);
        $usuario->setTipo($dados['tipo']);
        $usuario->setUsuarioCriacao($dados['usuarioCriacao']);
        $users[$count] = $usuario;
        $count++;
    }

?>

   
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Usuários</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Visualize aqui todos os usuários cadastrados.
                        </div>
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Username</th>
                                        <th>Data de Inclusão/Ediçao</th>
                                        <th>Criado/editado por</th>
                                        <th>Editar</th>
                                        <th>Excluir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                        <?php
                                            foreach($users as $user) { 
                                                echo '<tr class="odd gradeX">';
                                                echo '<td>'.$user->getNome().'</td>';
                                                echo '<td>'.$user->getUsername().'</td>';
                                                echo '<td>'.date('d/m/Y', strtotime($user->getData())).'</td>';//
                                                echo '<td>'.$user->getUsuarioCriacao().'</td>';
                                                echo '<td class="text-center"><a href="home.php?conteudo=new-user.php&edit='.$user->getId().'"><span class="fa fa-edit text-primary"></span></a></td>';
                                                echo '<td class="text-center"><a href="home.php?conteudo=listarUsuarios.php&delete='.$user->getId().'"><span class="fa fa-trash-o text-danger"></span></a></td>';
                                                echo '</tr>';
                                                $contLin++;
                                            }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

     
