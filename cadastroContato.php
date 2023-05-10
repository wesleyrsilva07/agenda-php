<?php
session_start();

$verificaUsuarioLogado = $_SESSION['verificaUsuarioLogado'];
include 'formataData.php';
if (!$verificaUsuarioLogado) {
    header("Location: index.php?codMsg=003");
} else {
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
    <script src="js/dateITA.js"></script>
    <script src="js/jquery.mask.js"></script>
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

        .custom-file-input ~ .custom-file-label::after {
            content: "Selecionar";
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-dark navbar-dark fixed-top">
    <div class="container">
        <a href="#" class="navbar-brand">
            <img src="img/icone.svg" width="30" height="30" alt="Agenda de Contatos">
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
                        <a href="cadastroContato.php" class="dropdown-item"><i class="bi-person-fill"></i>Novo
                            Contato</a>
                        <a href="listaContatos.php" class="dropdown-item"><i class="bi-list-ul"></i>Lista de
                            Contatos</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false" id="menuConta">
                        <i class="bi-gear-fill"></i>Minha conta</a>
                    <div class="dropdown-menu" aria-labelledby="menuConta">
                        <a href="alterarDados.php" class="dropdown-item"><i class="bi-pencil-square"></i>Alterar
                            dados</a>
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
<div class="h-100 row align-items-center pt-5">
    <div class="container">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-12">
                <?php
                $flagErro = False;
                $flagSucesso = False;
                $mostrarMensagem = False;

                $dadosContato = array('codigoContato',
                    'nomeContato',
                    'nascimentoContato',
                    'sexoContato',
                    'mailContato',
                    'fotoContato',
                    'fotoAtualContato',
                    'telefone1Contato',
                    'telefone2Contato',
                    'telefone3Contato',
                    'telefone4Contato',
                    'logradouroContato',
                    'complementoContato',
                    'bairroContato',
                    'estadoContato',
                    'cidadeContato'
                );
                foreach ($dadosContato as $campo) {
                    $$campo = "";
                }

                if (isset($_POST['codigoContato'])) {
                    $codigoContato = $_POST['codigoContato'];
                    $nomeContato = addslashes($_POST['nomeContato']);
                    $nascimentoContato = $_POST['nascimentoContato'];

                    if (isset($_POST['sexoContato'])) {
                        $sexoContato = $_POST['sexoContato'];
                    } else {
                        $sexoContato = "";
                    }

                    $mailContato = $_POST['mailContato'];
                    $fotoContato = $_FILES['fotoContato'];
                    $fotoAtualContato = $_POST['fotoAtualContato'];
                    $telefone1Contato = $_POST['telefone1Contato'];
                    $telefone2Contato = $_POST['telefone2Contato'];
                    $telefone3Contato = $_POST['telefone3Contato'];
                    $telefone4Contato = $_POST['telefone4Contato'];
                    $logradouroContato = addslashes($_POST['logradouroContato']);
                    $complementoContato = addslashes($_POST['complementoContato']);
                    $bairroContato = addslashes($_POST['bairroContato']);
                    $estadoContato = $_POST['estadoContato'];
                    $cidadeContato = $_POST['cidadeContato'];

                    $telefonesContatos = array($telefone1Contato, $telefone2Contato,
                        $telefone3Contato, $telefone4Contato);
                    $telefonesFiltradosContato = array_filter($telefonesContatos);
                    $telefonesValidadosContato = preg_grep('/^\(\d{2}\}\s\d{4,5}\-\d{4}$/', $telefonesContatos);
//                    echo '<pre>';
//                    print_r($telefonesValidadosContato);
//                    print_r($telefonesFiltradosContato);
//                    exit;

                    if ($telefonesFiltradosContato === $telefonesValidadosContato) {
                        $erroTelefones = False;
                    } else {
                        $erroTelefones = True;
                    }
                    if (empty($nomeContato) || empty($sexoContato) || empty($mailContato) || empty($telefone1Contato) ||
                        empty($logradouroContato) || empty($complementoContato) || empty($bairroContato) || empty($cidadeContato) || empty($estadoContato)) {
                        $flagErro = True;
                        $mensagemAcao = "Preencha todos os campos obrigatórios.*";
                    } else if (strlen($nomeContato) < 5) {
                        $flagErro = True;
                        $mensagemAcao = "Informe a quantidade mínima de caracteres para o campo: Nome(5).";
                    } else if (!preg_match("/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $mailContato)) {
                        $flagErro = True;
                        $mensagemAcao = "Verifique o email informado.";
//                    } else if ($fotoContato['error'] != 4) {
//                        if (!in_array($fotoContato['type'], array('image/jpg', 'image/jpeg', 'image/png')) ||
//                            $fotoContato['size'] > 2000000) {
//                            $flagErro = True;
//                            $mensagemAcao = "A foto do contato deve ser no formato JPG, JPEG ou PNG e ter no máximo 2MB.";
//                        } else {
//                            list($larguraFoto, $alturaFoto) = getimagesize($fotoContato['tmp_name']);
//                            if ($larguraFoto > 500 || $alturaFoto > 200) {
//                                $flagErro = True;
//                                $mensagemAcao = "A dimensão da foto deve ser no máximo 500x200 pixels.";
//                            }
//                        }
                    }
                    // else if ($erroTelefones) {
//                        $flagErro = True;
//                        $mensagemAcao = "Os campos de telefones devem ser no formato: xxxxx-xxxx";
//                    }
//                    echo '<pre>';
//                    print_r($codigoUsuarioLogado . '<br>');
//                    print_r($nomeContato . '<br>');
//                    print_r($nascimentoContato . '<br>');
//                    print_r($sexoContato . '<br>');
//                    print_r($mailContato . '<br>');
//                    print_r($telefone1Contato . '<br>');
//                    print_r($telefone2Contato . '<br>');
//                    print_r($telefone3Contato . '<br>');
//                    print_r($telefone4Contato . '<br>');
//                    print_r($logradouroContato . '<br>');
//                    print_r($complementoContato . '<br>');
//                    print_r($bairroContato . '<br>');
//                    print_r($estadoContato . '<br>');
//                    print_r($cidadeContato . '<br>');
//                    exit;
                    if (!$flagErro) {
                        if (empty($codigoContato)) {
                            $sqlContato = "INSERT INTO contatos (codigoUsuario, nomeContato, nascimentoContato, sexoContato,
                                mailContato, fotoContato, telefone1Contato, telefone2Contato, telefone3Contato, telefone4Contato,
                                logradouroContato, complementoContato, bairroContato, cidadeContato, estadoContato) VALUES (:codigoUsuario,
                                :nomeContato, :nascimentoContato, :sexoContato, :mailContato, :fotoContato, :telefone1Contato, :telefone2Contato,
                                :telefone3Contato, :telefone4Contato, :logradouroContato, :complementoContato, :bairroContato,
                                :cidadeContato, :estadoContato)";

                            $sqlContatoST = $conexao->prepare($sqlContato);
                            $sqlContatoST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                            $sqlContatoST->bindValue(':nomeContato', $nomeContato);
                            $nacimentoContato = formataData($nascimentoContato);
                            $sqlContatoST->bindValue(':nascimentoContato', $nascimentoContato);
                            $sqlContatoST->bindValue(':sexoContato', $sexoContato);
                            $sqlContatoST->bindValue(':mailContato', $mailContato);
                            $sqlContatoST->bindValue(':fotoContato', '');
                            $sqlContatoST->bindValue(':telefone1Contato', $telefone1Contato);
                            $sqlContatoST->bindValue(':telefone2Contato', $telefone2Contato);
                            $sqlContatoST->bindValue(':telefone3Contato', $telefone3Contato);
                            $sqlContatoST->bindValue(':telefone4Contato', $telefone4Contato);
                            $sqlContatoST->bindValue(':logradouroContato', $logradouroContato);
                            $sqlContatoST->bindValue(':complementoContato', $complementoContato);
                            $sqlContatoST->bindValue(':bairroContato', $bairroContato);
                            $sqlContatoST->bindValue(':cidadeContato', $cidadeContato);
                            $sqlContatoST->bindValue(':estadoContato', $estadoContato);
                            $sqlContatoST->execute();


//                            if ($fotoContato['error'] == 0) {
//                                $extensaoFoto = pathinfo($fotoContato['name'], PATHINFO_EXTENSION);
//                                $nomeFoto = "fotos/" . strtotime(date("Y-m-d H:i:s")) . $codigoUsuarioLogado . '.' . $extensaoFoto;
//
//                                if (copy($fotoContato['tmp_name'], $nomeFoto)) {
//                                    $fotoEnviada = True;
//                                } else {
//                                    $fotoEnviada = False;
//                                }
//                                $sqlContatoST->bindValue(':fotoContato', $nomeFoto);
//                            }
                            if ($sqlContatoST->execute()) {
                                $flagSucesso = True;
                                $mensagemAcao = "Novo contato cadastrado com sucesso";
                            } else {
                                $flagErro = True;
                                $mensagemAcao = "Erro ao cadastrar novo contato.";
                                $nascimentoContato = formataData($nascimentoContato);
//                                if ($fotoEnviada) {
//                                    unlink($nomeFoto);
//                                }
                            }
                        } else {
                            $sqlContato = "UPDATE contatos SET nomeContato=:nomeContato, nascimentoContato=:nascimentoContato, 
                                                sexoContato=:sexoContato,mailContato=:mailContato, fotoContato=:fotoContato, 
                                                telefone1Contato=:telefone1Contato, telefone2Contato=:telefone2Contato, 
                                                telefone3Contato=:telefone3Contato,telefone4contato=:telefone4Contato, 
                                                logradouroContato=:logradouroContato, complementoContato=:complementoContato, 
                                                bairroContato=:bairroContato,estadoContato=:estadoContato, cidadeContato=:cidadeContato
                                                WHERE codigoContato=:codigoContato and codigoUsuario=:codigoUsuario";
                            $sqlContatoST = $conexao->prepare($sqlContato);
                            $sqlContatoST->bindValue(':codigoContato', $codigoContato);
                            $sqlContatoST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                            $sqlContatoST->bindValue(':nomeContato', $nomeContato);
                            $nascimentoContato = formataData($nascimentoContato);
                            $sqlContatoST->bindValue(':nascimentoContato', $nascimentoContato);
                            $sqlContatoST->bindValue(':sexoContato', $sexoContato);
                            $sqlContatoST->bindValue(':mailContato', $mailContato);
                            $sqlContatoST->bindValue(':fotoContato', '');
                            $sqlContatoST->bindValue(':telefone1Contato', $telefone1Contato);
                            $sqlContatoST->bindValue(':telefone2Contato', $telefone2Contato);
                            $sqlContatoST->bindValue(':telefone3Contato', $telefone3Contato);
                            $sqlContatoST->bindValue(':telefone4Contato', $telefone4Contato);
                            $sqlContatoST->bindValue(':logradouroContato', $logradouroContato);
                            $sqlContatoST->bindValue(':complementoContato', $complementoContato);
                            $sqlContatoST->bindValue(':bairroContato', $bairroContato);
                            $sqlContatoST->bindValue(':estadoContato', $estadoContato);
                            $sqlContatoST->bindValue(':cidadeContato', $cidadeContato);
                            if ($fotoContato['error'] == 0) {
                                $extensaoFoto = pathinfo($fotoContato['name'], PATHINFO_EXTENSION);
                                $nomeFoto = "fotos/" . strtotime(date("Y-m-d H:i:s")) . $codigoUsuarioLogado . '.' . $extensaoFoto;

                                if (copy($fotoContato['tmp_name'], $nomeFoto)) {
                                    $fotoEnviada = True;
                                } else {
                                    $fotoEnviada = False;
                                }
                                $sqlContatoST->bindValue(':fotoContato', $nomeFoto);
                            }
                            if ($sqlContatoST->execute()) {
                                $flagSucesso = True;
                                $mensagemAcao = "Cadastro editado com sucesso";
                            } else {
                                $flagErro = True;
                                $mensagemAcao = "Erro ao cadastrar novo contato.";
                                $nascimentoContato = formataData($nascimentoContato);
                                if ($fotoEnviada) {
                                    unlink($nomeFoto);
                                }


                            }
                        }
                    }
                } else {
                    if (isset($_GET['codigoContato'])) {
                        $codigoContato = $_GET['codigoContato'];

                        $sqlContato = 'SELECT * FROM contatos where codigoContato=:codigoContato and codigoUsuario=:codigoUsuario';

                        $sqlContatoST = $conexao->prepare($sqlContato);
                        $sqlContatoST->bindValue(':codigoContato', $codigoContato);
                        $sqlContatoST->bindValue(':codigoUsuario', $codigoUsuarioLogado);

                        $sqlContatoST->execute();
                        $quantidadeContatos = $sqlContatoST->rowCount();

                        if ($quantidadeContatos == 1) {
                            $resultadoContato = $sqlContatoST->fetchAll();
                            list($codigoContato, $codigoUsuario, $nomeContato, $nascimentoContato, $sexoContato, $mailContato,
                                $fotoContato, $telefone1Contato, $telefone2Contato, $telefone3Contato, $telefone4Contato,
                                $logradouroContato, $complementoContato, $bairroContato, $estadoContato, $cidadeContato) = $resultadoContato[0];

                            $fotoAtualContato = $fotoContato;

                            $nascimentoContato = formataData($nascimentoContato);
                        }else{
                            $flagErro = True;
                            $mensagemAcao = "Contato não cadastrado.";
                        }
                    }
                }
                if ($flagErro) {
                    $classeMensagem = 'alert-danger';
                    $mostrarMensagem = True;
                } else if ($flagSucesso) {
                    $classeMensagem = 'alert-success';
                    $mostrarMensagem = True;
                }
                if ($mostrarMensagem) {
                    echo "<div class=\"alert $classeMensagem alert-dismissible fade show my-5\" role=\"alert\">
                                      $mensagemAcao
                                    <button type=\"button\" class=\"close\" data-bs-dismiss=\"alert\" aria-label=\"Fechar\">
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                   </div>";
                }
                ?>
                <div class="card border-primary my-5">
                    <div class="card-header bg-primary text-white">
                        <h5>Cadastro de contato</h5>
                    </div>
                    <div class="card-body">
                        <form id="cadastroContato" action="cadastroContato.php" method="post"
                              enctype="multipart/form-data">
                            <input type="hidden" name="codigoContato" value="<?= $codigoContato ?>">
                            <input type="hidden" name="fotoAtualContato" value="<?= $fotoAtualContato ?>">
                            <h5 class="text-primary">Dados pessoais</h5>
                            <hr>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="nomeContato">Nome*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-person-circle"></i>
                                                        </div>
                                                    </div>
                                                    <input value="<?= $nomeContato ?>" class="form-control"
                                                           type="text" name="nomeContato"
                                                           required id="nomeContato" placeholder="Digite um nome">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="nascimentoContato">Data de nascimento</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-calendar-date"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control" type="text" name="nascimentoContato"
                                                           id="nascimentoContato"
                                                           value="<?= $nascimentoContato ?>"
                                                           placeholder="DD/MM/AAAA">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="sexoContato">Sexo*</label>
                                                <div class="input-group">
                                                    <div class="form-check form-check-inline">
                                                        <?php
                                                        if (isset($sexoContato) && $sexoContato == "M") {
                                                            $checkedMasculino = 'checked';
                                                            $checkedFeminino = '';
                                                        } else if (isset($sexoContato) && $sexoContato == "F") {
                                                            $checkedMasculino = '';
                                                            $checkedFeminino = 'checked';
                                                        } else {
                                                            $checkedMasculino = '';
                                                            $checkedFeminino = '';
                                                        }
                                                        ?>
                                                        <input class="form-check-input" type="radio"
                                                               name="sexoContato" id="sexoMasculino"
                                                               value="M" <?= $checkedMasculino ?>>
                                                        <label class="form-check-label"
                                                               for="sexoMasculino">Masculino</label>
                                                        &nbsp&nbsp;
                                                        <input class="form-check-input" type="radio"
                                                               name="sexoContato" id="sexoFeminino"
                                                               value="F" <?= $checkedFeminino ?>>
                                                        <label class="form-check-label"
                                                               for="sexoFeminino">Feminino</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="mailContato">E-mail*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-at"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control" type="email" name="mailContato"
                                                           value="<?= $mailContato ?>"
                                                           required id="mailContato" placeholder="Digite o e-mail">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <div class="form-group">
                                                        <label for="fotoContato">Foto</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <i class="bi-file-earmark-person"></i>
                                                                </div>
                                                            </div>
                                                            <div class="custom-file">
                                                                <input class="custom-file-input" type="file"
                                                                       id="fotoContato" name="fotoContato">
                                                                <label class="custom-file-label" for="fotoContato">Escolha
                                                                    a foto</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="text-primary">Telefone</h5>
                            <hr>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="telefone1Contato">Telefone*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-phone"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control mascara-telefone" type="text"
                                                           name="telefone1Contato"
                                                           value="<?= $telefone1Contato ?>" required
                                                           id="telefone1Contato" placeholder="(xx) xxxxx-xxxx">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="telefone2Contato">Telefone</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-phone"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control mascara-telefone" type="text"
                                                           name="telefone2Contato"
                                                           value="<?= $telefone2Contato ?>" id="telefone2Contato"
                                                           placeholder="(xx) xxxxx-xxxx">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="telefone3Contato">Telefone</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-phone"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control mascara-telefone" type="text"
                                                           name="telefone3Contato"
                                                           value="<?= $telefone3Contato ?>" id="telefone3Contato"
                                                           placeholder="(xx) xxxxx-xxxx">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="telefone4Contato">Telefone</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-phone"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control mascara-telefone" type="text"
                                                           name="telefone4Contato"
                                                           value="<?= $telefone4Contato ?>" id="telefone4Contato"
                                                           placeholder="(xx) xxxxx-xxxx">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="text-primary">Endereço</h5>
                            <hr>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="logradouroContato">Logradouro*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-map"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control" type="text" name="logradouroContato"
                                                           value="<?= $logradouroContato ?>" required
                                                           id="logradouroContato"
                                                           placeholder="Rua, avenida, travessa...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="complementoContato">Complemento*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-pin"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control" type="text" value="<?= $complementoContato?>"
                                                           name="complementoContato"
                                                           required id="complementoContato"
                                                           placeholder="Número, quadra, lote e outros...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="estadoContato">Estado*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-globe"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control"
                                                            name="estadoContato" id="estadoContato" required>
                                                        <option value="">Escolha o estado</option>
                                                        <?php
                                                        $sqlEstados = "select codigoEstado, nomeEstado FROM estados";

                                                        $resultadoEstados = $conexao->query($sqlEstados)->fetchAll();
                                                        foreach ($resultadoEstados as list($codigoEstado, $nomeEstado)) {
                                                            if ($estadoContato == $codigoContato) {
                                                                $selected = 'selected';
                                                            } else {
                                                                $selected = '';
                                                            }
                                                            echo "<option value='$codigoEstado' $selected>$nomeEstado</option>\n";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="bairroContato">Bairro*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-globe"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control" type="text" name="bairroContato"
                                                           required id="bairroContato" value="<?= $bairroContato?>"
                                                           placeholder="Digite o bairro">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm">
                                            <div class="form-group">
                                                <label for="cidadeContato">Cidade*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="bi-globe"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" required
                                                            name="cidadeContato" id="cidadeContato">
                                                        <option value="<?= $cidadeContato ?>">Escolha a cidade
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm text-right">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
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
        unhighlight: function (element, erroClass, validClass) {
            $(element).removeClass('is.invalid');
        }
    });

    $(document).ready(function () {
        $('#cadastroContato').validate({
            rules: {
                nomeContato: {
                    minlength: 5
                },
                nascimentoContato: {
                    dateITA: true
                },
                sexoConato: {
                    required: true
                }
            }
        });
        $('#nascimentoContato').mask('00/00/0000');

        var SPMaskBehavior = function (val) {
                return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            },
            spOptions = {
                onKeyPress: function (val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            };

        $('.mascara-telefone').mask(SPMaskBehavior, spOptions);

        $("#estadoContato").change(function () {
            $("#cidadeContato").html('<option>Carregando...</option>');
            $("#cidadeContato").load('listaCidades.php?codigoEstado=' + $("#estadoContato").val());
        });
        <?php
        if (!empty($estadoContato) && !empty($cidadeContato)) {
            echo "$(\"#cidadeContato\").html('<option>Carregando...</option>');
                  $(\"#cidadeContato\").load('listaCidades.php?codigoEstado="
                  . $estadoContato . "&codigoCidade=" . $cidadeContato . "');";
        }
        ?>
    });

</script>
</body>
</html>