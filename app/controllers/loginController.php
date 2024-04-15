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
                    }
                    
                }
                
            }
        }
    }