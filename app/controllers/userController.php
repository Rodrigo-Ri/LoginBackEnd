<?php

    namespace app\controllers;
    use app\models\mainModel;

    class userController extends mainModel{

        # Controlador para registrar usuario #
        public function registrarUsuarioControlador(){
            # Almacenando los datos #
            $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
            $apellido = $this->limpiarCadena($_POST['usuario_apellido']);

            $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
            $email = $this->limpiarCadena($_POST['usuario_email']);
            $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
            $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

            # Verificando campos obligatorios #
            if($nombre=="" || $apellido=="" || $usuario=="" || $clave1=="" || $clave2==""){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Debes llenar los campos que son bligatorios",
                    "icono"=>"error"
                ];

                return json_encode($alerta);
                exit();

            }

            # Verificar la integridad de los datos #
            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El nombre no coincide con el formato solictado",
                    "icono"=>"error"
                ];

                return json_encode($alerta);
                exit();
                
            }

            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El apellido no coincide con el formato solictado",
                    "icono"=>"error"
                ];

                return json_encode($alerta);
                exit();
                
            }

            if($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El usuario no coincide con el formato solictado",
                    "icono"=>"error"
                ];

                return json_encode($alerta);
                exit();
                
            }

            if($this->verificarDatos("[a-zA-Z0-9\$\@\.\-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9\$\@\.\-]{7,100}", $clave2)){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"La contraseña no coincide con el formato solictado",
                    "icono"=>"error"
                ];

                return json_encode($alerta);
                exit();
                
            }

            # Verificando e-mail #
            if($email!=""){

                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $chek_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");

                    if($chek_email->rowCount()>0){
                        $alerta = [
                            "tipo"=>"simple",
                            "titulo"=>"Ocurrió un error inesperado",
                            "texto"=>"El correo ya está en uso",
                            "icono"=>"error"
                        ];

                        return json_encode($alerta);
                        exit();

                    }
                }else{
                    $alerta = [
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"El E-mail no es válido",
                        "icono"=>"error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                    
                }
            }

            # Verificando claves #
            if($clave1!=$clave2){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Las contraseñas no coinciden",
                    "icono"=>"error"
                ];

                return json_encode($alerta);
                exit();

            }else{
                $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost"=>10]);
            }

            # Verificando usuario #
            $chek_usuario = $this->ejecutarConsulta("SELECT usuario_user FROM usuario WHERE usuario_user='$usuario'");

            if ($chek_usuario->rowCount() > 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El usuario ya está en uso",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();

            }

            # Directorio de la imágenes #
            $img_dir = "../views/fotos/";
        }
    }