<?php
include 'conectaBanco.php';

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
        html {
            height: 100%;
        }

        body {
            background: url('img/dark-blue-background.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100%;
            overflow-x: hidden;
        }
    </style>
</head>
<body>
<div class="h-100 row align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10">
                <?php
                $flagErro = False;

                if (isset($_POST['acao'])) {
                    $acao = $_POST['acao'];

                    if ($acao == 'salvar') {
                        $nomeUsuario = $_POST['nomeUsuario'];
                        $mailUsuario = $_POST['mailUsuario'];
                        $mail2Usuario = $_POST['mail2Usuario'];
                        $senhaUsuario = $_POST['senhaUsuario'];
                        $senha2Usuario = $_POST['senha2Usuario'];

                        if (!empty($nomeUsuario) && !empty($mailUsuario) && !empty($mail2Usuario) && !empty($senhaUsuario) && !empty($senha2Usuario)) {
                            if ($mailUsuario == $mail2Usuario && $senha2Usuario == $senha2Usuario) {
                                if (strlen($mailUsuario) >= 5 && strlen($senhaUsuario) >= 8) {
                                    $sqlUsuarios = "SELECT codigoUsuario from usuarios where mailUsuario=:mailUsuario";
                                    $sqlUsuariosST = $conexao->prepare($sqlUsuarios);
                                    $sqlUsuariosST->bindValue(':mailUsuario', $mailUsuario);
                                    $sqlUsuariosST->execute();
                                    $quantidadeUsuarios = $sqlUsuariosST->rowCount();

                                    if ($quantidadeUsuarios == 0) {

                                        $senhaUsuarioMD5 = md5($senhaUsuario);

                                        $sqlNovoUsuario = "insert into usuarios (nomeUsuario, mailUsuario, senhaUsuario) values 
                                                        (:nomeUsuario, :mailUsuario, :senhaUsuario)";

                                        $sqlNovoUsuarioST = $conexao->prepare($sqlNovoUsuario);
                                        $sqlNovoUsuarioST->bindValue(':nomeUsuario', $nomeUsuario);
                                        $sqlNovoUsuarioST->bindValue(':mailUsuario', $mailUsuario);
                                        $sqlNovoUsuarioST->bindValue(':senhaUsuarioMD5', $senhaUsuario);

                                        if ($sqlNovoUsuarioST->execute()) {
                                            $mensagemAcao = "Novo usuário cadastrado com sucesso";
                                        } else {
                                            $flagErro = True;
                                            $mensagemAcao = "Código de erro: " . $sqlNovoUsuarioST->errorCode();
                                        }
                                    } else {
                                        $flagErro = True;
                                        $mensagemAcao = "Email já cadastrado para outro usuário";
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

                }
                ?>
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5>Cadastro de novo usuário</h5>
                    </div>
                    <div class="card-body">
                        <form id="novoUsuario" action="novoUsuario.php" method="post">
                            <input type="hidden" name="acao" value="salvar">
                            <div class="form-group mb-3">
                                <label for="nomeUsuario">Nome*</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="bi-person-fill"></i></div>
                                    </div>
                                    <input type="text" class="form-control" name="nomeUsuario" id="nomeUsuario"
                                           placeholder="Digite seu nome" value="<?= ($flagErro) ? $nomeUsuario : "" ?>"
                                           required>
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
                                            <input type="email" class="form-control" name="mailUsuario" id="mailUsuario"
                                                   placeholder="Digite seu e-mail"
                                                   value="<?= ($flagErro) ? $mailUsuario : "" ?>" required>
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
                                            <input type="email" class="form-control" name="mail2Usuario"
                                                   id="mail2Usuario"
                                                   placeholder="Repita seu e-mail"
                                                   value="<?= ($flagErro) ? $mail2Usuario : "" ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm">
                                    <div class="form-group">
                                        <label for="senhaUsuario">Senha*</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="bi-key-fill"></i></div>
                                            </div>
                                            <input type="password" class="form-control" name="senhaUsuario"
                                                   id="senhaUsuario"
                                                   placeholder="Digite sua senha"
                                                   value="<?= ($flagErro) ? $senhaUsuario : "" ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-group">
                                        <label for="senha2Usuario">Repita a senha*</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="bi-key-fill"></i></div>
                                            </div>
                                            <input type="password" class="form-control" name="senha2Usuario"
                                                   id="senha2Usuario"
                                                   placeholder="Repita sua senha"
                                                   value="<?= ($flagErro) ? $senha2Usuario : "" ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="campo_senha">
                                <div class="col-sm barra_senha"></div>
                            </div>
                            <div class="row">
                                <div class="col-sm text-right">
                                    <button type="submit" class="btn btn-primary">Cadastrar</button>
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
        unhighlight: function (element, erroClass, validClass) {
            $(element).removeClass('is.invalid');
        }
    });

    $(document).ready(function () {
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