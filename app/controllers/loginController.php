<?php

    namespace app\controllers;
    use app\models\mainModel;

    class loginController extends mainModel{
        # Controlador iniciar sesión #
        public function iniciarSesionControlador(){

            # Almacenando los datos #
            $usuario = $this->limpiarCadena($_POST['login_usuario']);
            $clave = $this->limpiarCadena($_POST['login_clave']);

            # Verificando campos obligatorios #
            if($usuario=="" || $clave==""){
                echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Ocurrió un error inesperado',
                        text: 'No has llenado todos los campos requeridos',
                        confirmButtonText: 'Aceptar'
                    });
                </script>
                ";
            }else{
                # Verificar la integridad de los datos #
                if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
                    # code...
                    echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un error inesperado',
                            text: 'El usuario no coincide con el formato solicitado',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>
                    ";
                } else {
                    # code...
                    if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave)) {
                        # code...
                        echo "
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Ocurrió un error inesperado',
                                text: 'El clave no coincide con el formato solicitado',
                                confirmButtonText: 'Aceptar'
                            });
                        </script>
                        ";
                    } else {
                        # code...
                        # Verificando usuario #
                        $chek_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_user='$usuario'");

                        if ($chek_usuario->rowCount()==1) {
                            # code...
                            $chek_usuario = $chek_usuario->fetch();
                            if ($chek_usuario['usuario_user']==$usuario && password_verify($clave, $chek_usuario['usuario_key'])) {
                                # code...
                                $_SESSION['id'] = $chek_usuario['usuario_id'];
                                $_SESSION['nombre'] = $chek_usuario['usuario_name'];
                                $_SESSION['apellido'] = $chek_usuario['usuario_lastname'];
                                $_SESSION['usuario'] = $chek_usuario['usuario_user'];
                                $_SESSION['foto'] = $chek_usuario['usuario_photo'];

                                if (headers_sent()) {
                                    # code...
                                    echo "
                                        <scrip>
                                            window.location.href='".APP_URL."dashboard/';
                                        </script>
                                    ";
                                } else {
                                    # code...
                                    header("Location: ".APP_URL."dashboard/");
                                }
                                
                            } else {
                                # code...
                                echo "
                                <script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Ocurrió un error inesperado',
                                        text: 'Usuario o clave incorrectos',
                                        confirmButtonText: 'Aceptar'
                                    });
                                </script>
                                ";
                            }
                            
                        } else {
                            # code...
                            echo "
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Ocurrió un error inesperado',
                                    text: 'Usuario o clave incorrectos',
                                    confirmButtonText: 'Aceptar'
                                });
                            </script>
                            ";
                        }
                        
                    }
                    
                }
                
            }
        }

        # Controlador para cerrar sesión #
        public function cerrarSesionControlador(){
            session_destroy();

            if (headers_sent()) {
                # code...
                echo "
                    <scrip>
                        window.location.href='".APP_URL."login/';
                    </script>
                ";
            } else {
                # code...
                header("Location: ".APP_URL."login/");
            }
        }
    }
