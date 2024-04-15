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

            # Comprobar sis e seleccionó una imagen doble #
            if ($_FILES['usuario_foto']['name'] != "" && $_FILES['usuario_foto']['size'] > 0) {
                # Creadno directiorio #
                if(!file_exists($img_dir)){
                    if(mkdir(!$img_dir, 0777)){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ocurrió un error inesperado",
                            "texto" => "Error al crear el directorio.",
                            "icono" => "error"
                        ];
        
                        return json_encode($alerta);
                        exit();
                    }

                }

                # Verifficando el formato de la imagen #
                if(mime_content_type($_FILES['usuario_foto']['tmp_name'] != "image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/png")){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "El formato de la imagen no es válido",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();

                }

                # Verficando el tamaño de la imagen #
                if(($_FILES['usuario_foto']['size'] / 1024) > 5120){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "El tamaño de la imagen no es válido",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();

                }

                # Nombre de la foto Rodrigo Rivera -> Rodrigo_Rivera #
                $foto = str_ireplace(" ","_", $nombre);
                $foto = $foto."_".rand(0, 100);

                # Extensión de la imagen #
                switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){
                    case "image/jpeg":
                        $foto = $foto.".jpg";
                        break;
                    case "image/png":
                        $foto = $foto.".png";
                        break;
                }

                chmod($img_dir, 0777);

                # Moviendo la imagen al directorio #
                if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'], $img_dir.$foto)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "Error al subir la imagen",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();

                }

            } else {
                $foto = "";
            }
            
        }
    }