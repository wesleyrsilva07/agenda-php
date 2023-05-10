<?php
session_start();

$verificaUsuarioLogado = $_SESSION['verificaUsuarioLogado'];
if(!$verificaUsuarioLogado){
    header("Location: index.php?codMsg=003");
}else{
    include 'conectaBanco.php';
    $codigoUsuarioLogado = $_SESSION['codigoUsuarioLogado'];

    $nomeUsuarioLogado = $_SESSION['nomeUsuarioLogado'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Agenda de contatos</title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/bootstrap-icons.css">
        <script src="js/jquery-3.3.1.js"></script>
        <script src="js/bootstrap.bundle.js"></script>
        <script src="js/jquery.validate.js"></script>
        <script src="js/messages_pt_PT.js"></script>
        <script src="js/pwstrength-bootstrap.js"></script>
        <style>
            html{
                height: 100%;
            }

            body{
                background: url('img/dark-blue-background.jpg') no-repeat center center fixed;
                background-size: cover;
                height: 100%;
                overflow-x: hidden;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-dark navbar-dark fixed-top">
            <div class="container">
                <a href="#" class="navbar-brand">
                    <img src="img/icone.svg"  width="30" height="30" alt="Agenda de Contatos">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" id="menuCadastros">
                            <i class="bi-card-list"></i>Cadastros</a>
                            <div class="dropdown-menu" aria-labelledby="menuCadastros">
                                <a href="cadastroContato.php" class="dropdown-item"><i class="bi-person-fill"></i>Novo Contato</a>
                                <a href="listaContatos.php" class="dropdown-item"><i class="bi-list-ul"></i>Lista de Contatos</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false" id="menuConta">
                                <i class="bi-gear-fill"></i>Minha conta</a>
                            <div class="dropdown-menu" aria-labelledby="menuConta">
                                <a href="alterarDados.php" class="dropdown-item"><i class="bi-pencil-square"></i>Alterar dados</a>
                                <a href="logout.php" class="dropdown-item"><i class="bi-door-open"></i>Sair</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-toggle="modal" data-target="#modalSobreAplicacao">
                                <i class="bi-info-circle"></i>Sobre</a>
                        </li>
                    </ul>
                    <form class="form-inline my-2 my-lg-0" method="get" action="listaContatos.php">
                        <input class="form-control mr-sm-2 " type="search" name="busca" placeholder="Pesquisar">
                        <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Pesquisar</button>
                    </form>
                    <span class="navbar-text ml-4">
                        Olá <b><?= $nomeUsuarioLogado ?></b>, seja bem vindo.
                    </span>
                </div>
            </div>
        </nav>
        <div class="h-100 row align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-sm"></div>
                    <div class="col-sm-12">
                        <?php
                        $flagErro = False;

                        if (isset($_POST['acao'])) {
                            $acao = $_POST['acao'];

                            if ($acao == 'salvar') {
                                $nomeUsuario = $_POST['nomeUsuario'];
                                $mailUsuario = $_POST['mailUsuario'];
                                $mail2Usuario = $_POST['mail2Usuario'];
                                $senhaAtualUsuario = $_POST['senhaAtualUsuario'];
                                $senhaUsuario = $_POST['senhaUsuario'];
                                $senha2Usuario = $_POST['senha2Usuario'];

                                if (!empty($nomeUsuario) && !empty($mailUsuario) && !empty($mail2Usuario) && !empty($senhaUsuario) && !empty($senha2Usuario) && !empty($senhaAtualUsuario)) {
                                    if ($mailUsuario == $mail2Usuario && $senha2Usuario == $senha2Usuario) {
                                        if (strlen($mailUsuario) >= 5 && strlen($senhaUsuario) >= 8) {
                                            $senhaAtualUsuarioMD5 = md5($senhaAtualUsuario);
                                            $sqlSenhaUsuario = "SELECT codigoUsuario from usuarios where codigoUsuario=:codigoUsuario and
                                                                   senhaUsuario=:senhaUsuario";
                                            $sqlSenhaUsuarioST = $conexao->prepare($sqlSenhaUsuario);
                                            $sqlSenhaUsuarioST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                                            $sqlSenhaUsuarioST->bindValue(':senhaUsuario', $senhaAtualUsuarioMD5);
                                            $sqlSenhaUsuarioST->execute();
                                            $quantidadeUsuarios = $sqlSenhaUsuarioST->rowCount();

                                            if($quantidadeUsuarios == 1) {
                                                $sqlUsuarios = "SELECT codigoUsuario from usuarios where mailUsuario=:mailUsuario and codigoUsuario<>:codigoUsuario";
                                                $sqlUsuariosST = $conexao->prepare($sqlUsuarios);
                                                $sqlUsuariosST->bindValue(':mailUsuario', $mailUsuario);
                                                $sqlUsuariosST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                                                $sqlUsuariosST->execute();
                                                $quantidadeUsuarios = $sqlUsuariosST->rowCount();

                                                if ($quantidadeUsuarios == 0) {
                                                    $senhaUsuarioMD5 = md5($senhaUsuario);
                                                    $senhaAtualUsuarioMD5 = md5($senhaAtualUsuario);
                                                    if ($senhaAtualUsuarioMD5 == $senhaUsuario) {
                                                        $senhaUsuarioMD5 = $senhaAtualUsuarioMD5;
                                                    }

                                                    $sqlEditarUsuario = "UPDATE usuarios set nomeUsuario=:nomeUsuario, mailUsuario=:mailUsuario,
                                                                        senhaUsuario=:senhaUsuario where codigoUsuario=:codigoUsuario";

                                                    $sqlEditarUsuarioST = $conexao->prepare($sqlEditarUsuario);
                                                    $sqlEditarUsuarioST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                                                    $sqlEditarUsuarioST->bindValue(':nomeUsuario', $nomeUsuario);
                                                    $sqlEditarUsuarioST->bindValue(':mailUsuario', $mailUsuario);
                                                    $sqlEditarUsuarioST->bindValue(':senhaUsuario', $senhaUsuarioMD5);

                                                    if ($sqlEditarUsuarioST->execute()) {
                                                        $mensagemAcao = "Cadastro de usuário editado com sucesso";
                                                    } else {
                                                        $flagErro = True;
                                                        $mensagemAcao = "Código de erro: " . $sqlEditarUsuarioST->errorCode();
                                                    }
                                                } else {
                                                    $flagErro = True;
                                                    $mensagemAcao = "Email já cadastrado para outro usuário";
                                                }
                                            }else{
                                                $flagErro = True;
                                                $mensagemAcao = "Senha atual incorreta";
                                            }
                                        } else {
                                            $flagErro = True;
                                            $mensagemAcao = "Confirme as informações de email e senha";
                                        }
                                    }else{
                                        $flagErro = True;
                                        $mensagemAcao = "A senha deve ter no mínimo 8 caracteres e o email 5 caracteres.";
                                    }
                                } else {
                                    $flagErro = True;
                                    $mensagemAcao = "Preencha todos os campos obrigatórios (*)";
                                }


                                if (!$flagErro) {
                                    $classeMensagem = 'alert-success';
                                } else {
                                    $classeMensagem = 'alert-danger';
                                }

                                echo "<div class=\"alert $classeMensagem alert-dismissible fade show\" role=\"alert\">
                                    $mensagemAcao
                                    <button type=\"button\" class=\"close\" data-bs-dismiss=\"alert\" aria-label=\"Fechar\">
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                   </div>";
                            }

                        }else{
                            $sqlUsuario = "SELECT nomeUsuario, mailUsuario, senhaUsuario FROM usuarios
                                               where codigoUsuario=:codigoUsuario";
                            $sqlUsuarioST = $conexao->prepare($sqlUsuario);
                            $sqlUsuarioST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                            $sqlUsuarioST->execute();
                            $resultadoUsuario = $sqlUsuarioST->fetchAll();
                            list($nomeUsuario, $mailUsuario, $senhaUsuario) = $resultadoUsuario[0];
                            $mail2Usuario = $mailUsuario;
                            $senha2Usuario = $senhaUsuario;
                        }
                        ?>
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5>Alterar dados</h5>
                            </div>
                            <div class="card-body">
                                <form id="novoUsuario" action="alterarDados.php" method="post">
                                    <input type="hidden" name="acao" value="salvar">
                                    <div class="form-group mb-3">
                                        <label for="nomeUsuario">Nome*</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="bi-person-fill"></i></div>
                                            </div>
                                            <input value="<?= $nomeUsuario;?>" type="text" class="form-control" name="nomeUsuario" id="nomeUsuario" placeholder="Digite seu nome" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="mailUsuario">E-mail*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i class="bi-at"></i></div>
                                                    </div>
                                                    <input value="<?= $mailUsuario;?>" type="email" class="form-control" name="mailUsuario" id="mailUsuario" placeholder="Digite seu e-mail" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="mail2Usuario">Repita o e-mail*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i class="bi-at"></i></div>
                                                    </div>
                                                    <input value="<?= $mail2Usuario;?>"type="email" class="form-control" name="mail2Usuario" id="mail2Usuario" placeholder="Repita seu e-mail" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="senhaAtualUsuario">Senha atual*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i class="bi-key-fill"></i></div>
                                                    </div>
                                                    <input type="password" class="form-control" id="senhaAtualUsuario" placeholder="Digite sua senha atual"
                                                           name="senhaAtualUsuario" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm"></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="senhaUsuario">Nova Senha*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i class="bi-key-fill"></i></div>
                                                    </div>
                                                    <input value="<?= $senhaUsuario;?>" type="password" class="form-control" name="senhaUsuario" id="senhaUsuario" placeholder="Digite sua nova senha" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="senha2Usuario">Repita a nova senha*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i class="bi-key-fill"></i></div>
                                                    </div>
                                                    <input value="<?= $senha2Usuario;?>" type="password" class="form-control" name="senha2Usuario" id="senha2Usuario" placeholder="Repita sua nova senha" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="campo_senha">
                                        <div class="col-sm barra_senha"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm text-right">
                                            <button type="submit" class="btn btn-primary" >Salvar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm"></div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalSobreAplicacao" tabindex="-1" role="dialog"
             aria-labelledby="sobreAplicacao" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sobreAplicacao">Sobre</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img src="img/logo.jpg" alt="">
                        <hr>
                        <p>Agenda de Contatos</p>
                        <p>Versao 1.0</p>
                        <p>Todos os direitos reservados &copy;</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery.validator.setDefaults({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is.invalid');
                },
                unhighlight: function(element, erroClass, validClass){
                    $(element).removeClass('is.invalid');
                }
            });

            $(document).ready(function(){
                $('#novoUsuario').validate({
                    rules: {
                        nomeUsuario: {
                            minlength: 5
                        },
                        mail2Usuario: {
                            equalTo: '#mailUsuario'
                        },
                        senha2Usuario: {
                            equalTo: '#senhaUsuario'
                        },
                        senhaUsuario: {
                            minlength: 8
                        }
                    }
                });
                JQuery(document).ready(function () {
                    'use strict';
                    var options = {};
                    options.ui = {
                        container: "#campo_senha",
                        viewports: {
                            progress: ".barra_senha"
                        },
                        showVerdictsInsideProgressBar: true
                    };
                    $('#senhaUsuario').pwstrength(options);
                });
            });
        </script>
    </body>

</html>