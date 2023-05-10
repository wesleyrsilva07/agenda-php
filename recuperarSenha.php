<?php
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if(isset($_POST['mailUsuario'])){
    $mailUsuario = $_POST['mailUsuario'];
    include 'conectaBanco.php';

    $sqlUsuario = "SELECT codigoUsuario, nomeUsuario FROM usuarios WHERE mailUsuario:mailUsuario LIMIT 1";

    $sqlUsuarioST = $conexao->prepare($sqlUsuario);
    $sqlUsuarioST->bindValue(':mailUsuario', $mailUsuario);
    $sqlUsuarioST->execute();

    $quantidadeUsuarios = $sqlUsuarioST->rowCount();

    if($quantidadeUsuarios == 1){
        $resultadoUsuario = $sqlUsuarioST->fetchAll();
        list($codigoUsuario, $nomeUsuario) = $resultadoUsuario[0];
        $nomeCompletoUsuario = explode(' ', $nomeUsuario);
        $nomeUsuario = $nomeCompletoUsuario[0];
        include 'gerarSenha.php';
        $novaSenha = gerarSenha(8);
        $novaSenhaMD5 = md5($novaSenha);

        $sqlAlterarSenha = "UPDATE usuarios SET senhaUsuario=:novaSenhaMD5 where codigoUsuario=:codigoUsuario";
        $sqlAlterarSenhaST = $conexao->prepare($sqlAlterarSenha);
        $sqlAlterarSenhaST->bindValue(':novaSenhaMD5', $novaSenhaMD5);
        $sqlAlterarSenhaST->bindValue(':codigoUsuario', $codigoUsuario);

        if($sqlAlterarSenhaST->execute()){
            include 'constances.php';

            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->UserName = GUSER;
            $mail->Password = GPMD;

            $mensagem = "Olá $nomeUsuario ! <br><br>
                         Sua nova senha é <span style='font-weight: bold; color: red'>$novaSenha</span>";

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(GUSER, GNAME);
            $mail->addAddress($mailUsuario);
            $mail->Subject = 'Recuperação de senha';
            $mail->Body = $mensagem;
            if($mail->send()){
                header("Location: index.php?codMsg:008");
            }else{
                header("Location: index.php?codMsg:007");
            }
        }else{
            header("Location: index.php?codMsg:006");
        }

    }else{
        header("Location: index.php?codMsg:005");
    }
}else{
    header("Location: index.php?codMsg:004");
}
