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

            $usuario_datos_reg = [
                [
                    "campo_nombre" => "usuario_name",
                    "campo_marcador" => ":Nombre",
                    "campo_valor" => $nombre
                ],
                [
                    "campo_nombre" => "usuario_lastname",
                    "campo_marcador" => ":Apellido",
                    "campo_valor" => $apellido
                ],
                [
                    "campo_nombre" => "usuario_email",
                    "campo_marcador" => ":Email",
                    "campo_valor" => $email
                ],
                [
                    "campo_nombre" => "usuario_user",
                    "campo_marcador" => ":Usuario",
                    "campo_valor" => $usuario
                ],
                [
                    "campo_nombre" => "usuario_key",
                    "campo_marcador" => ":Clave",
                    "campo_valor" => $clave
                ],
                [
                    "campo_nombre" => "usuario_photo",
                    "campo_marcador" => ":Foto",
                    "campo_valor" => $foto
                ],
                [
                    "campo_nombre" => "usuario_created",
                    "campo_marcador" => ":Creado",
                    "campo_valor" => date("Y-m-d H:i:s")
                ],
                [
                    "campo_nombre" => "usuario_updated",
                    "campo_marcador" => ":Actualizado",
                    "campo_valor" => date("Y-m-d H:i:s")
                ]
            ];
            
            $registrar_usuario = $this->guardarDatos("usuario", $usuario_datos_reg);

            if ($registrar_usuario->rowCount()==1) {
                # code...
                $alerta = [
                    "tipo" => "limpiar",
                    "titulo" => "Usuario registrado",
                    "texto" => "El usuario ".$usuario." ha sido registrado con éxito",
                    "icono" => "success"
                ];
            } else {
                if(is_file($img_dir.$foto)){
                    chmod($img_dir.$foto, 0777);
                    unlink($img_dir.$foto);
                }
                # code...
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Usuaruio no registrado",
                    "icono" => "error"
                ];
            }
            return json_encode($alerta);
        }

        # Controlador listar usuario #
        public function listarUsuarioControlador($pagina, $registro, $url, $busqueda){
            $pagina = $this->limpiarCadena($pagina);
            $registro = $this->limpiarCadena($registro);

            $url = $this->limpiarCadena($url);
            $url = APP_URL.$url."/";

            $busqueda = $this->limpiarCadena($busqueda);
            $tabla = "";

            $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
            $inicio = ($pagina>0) ? (($pagina*$registro)-$registro) : 0;

            if (isset($busqueda) && $busqueda!="") {
                # code...
                $consulta_datos = "SELECT * FROM usuario WHERE ((usuario_id != '".$_SESSION['id']."' AND usuario_id != '1') AND (usuario_name LIGHT '%$busqueda%' OR usuario_lastname LIGHT '%$busqueda%' OR usuario_email LIGHT '%$busqueda%' OR usuario_user LIGHT '%$busqueda%')) ORDER BY usuario_name ASC LIMIT $inicio, $registro";

                $consulta_total = "SELECT COUNT() FROM usuario WHERE ((usuario_id != '".$_SESSION['id']."' AND usuario_id != '1') AND (usuario_name LIGHT '%$busqueda%' OR usuario_lastname LIGHT '%$busqueda%' OR usuario_email LIGHT '%$busqueda%' OR usuario_user LIGHT '%$busqueda%')) ORDER BY usuario_name ASC LIMIT $inicio, $registro";
            } else {
                # code...
                $consulta_datos = "SELECT * FROM usuario WHERE usuario_id != '".$_SESSION['id']."' AND usuario_id != '1' ORDER BY usuario_name ASC LIMIT $inicio, $registro";

                $consulta_total = "SELECT COUNT(usuario_id) FROM usuario WHERE usuario_id != '".$_SESSION['id']."' AND usuario_id != '1'";
            }
            
            $datos = $this->ejecutarConsulta($consulta_datos);
            $datos = $datos->fetchAll();

            $total = $this->ejecutarConsulta($consulta_total);
            $total = (int) $total->fetchColumn();

            $numeroPaginas = ceil($total/$registro);

            $tabla .= '<div class="table-container">
                        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                            <thead>
                                <tr>
                                    <th class="has-text-centered">#</th>
                                    <th class="has-text-centered">Nombre</th>
                                    <th class="has-text-centered">Usuario</th>
                                    <th class="has-text-centered">Email</th>
                                    <th class="has-text-centered">Creado</th>
                                    <th class="has-text-centered">Actualizado</th>
                                    <th class="has-text-centered" colspan="3">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>';

            if ($total>=1 && $pagina<=$numeroPaginas) {
                # code...
                $contador = $inicio+1;
                $pag_inicio = $inicio+1;

                foreach ($datos as $rows) {
                    # code...
                    $tabla .= '<tr class="has-text-centered">
                                <td>'.$contador.'</td>
                                <td>'.$rows['usuario_name'].' '.$rows['usuario_lastname'].'</td>
                                <td>'.$rows['usuario_user'].'</td>
                                <td>'.$rows['usuario_email'].'</td>
                                <td>'.date("d-m-Y h:i:s A", strtotime($rows['usuario_created'])).'</td>
                                <td>'.date("d-m-Y h:i:s A", strtotime($rows['usuario_updated'])).'</td>
                                <td>
                                    <a href="'.APP_URL.'userPhoto/'.$rows['usuario_id'].'/" class="button is-info is-rounded is-small">Foto</a>
                                </td>
                                <td>
                                    <a href="'.APP_URL.'userUpdate/'.$rows['usuario_id'].'/" class="button is-success is-rounded is-small">Actualizar</a>
                                </td>
                                <td>
                                    <form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off">

                                        <input type="hidden" name="modulo_usuario" value="eliminar">
                                        <input type="hidden" name="usuario_id" value="'.$rows['usuario_id'].'">

                                        <button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
                                    </form>
                                </td>
                            </tr>';
                    $contador++;
                }

                $pag_final = $contador-1;
            } else {
                # code...
                if ($total>=1) {
                    # code...
                    $tabla .= '<tr class="has-text-centered" >
                                <td colspan="7">
                                    <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
                                        Haga clic acá para recargar el listado
                                    </a>
                                </td>
                            </tr>';
                } else {
                    # code...
                    $tabla .= '<tr class="has-text-centered" >
                                <td colspan="7">
                                    No hay registros en el sistema
                                </td>
                            </tr>';
                }
                
            }
            
            $tabla .= '</tbody>
                </table>
            </div>';

            if($total>=1 && $pagina<=$numeroPaginas){
                $tabla .= '<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
                $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);

            }

            return $tabla;
        }

        # Controlador para eliminar usuario #
        public function eliminarUsuarioControlador(){
            $id = $this->limpiarCadena($_POST['usuario_id']);

            if($id==1){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"No puedes eliminar el usuario principal del sistema",
                    "icono"=>"error"
                ];

                return json_encode($alerta);
                exit();

            }

            # Verificar si el usuario existe #
            $datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");

            if ($datos->rowCount()<=0) {
                # code...
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El usuario que intentas eliminar no existe en el sistema",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            } else {
                # code...
                $datos = $datos->fetch();
            }
            
            $eliminarUsuario = $this->eliminarRegistro("usuario","usuario_id", $id);

            if ($eliminarUsuario->rowCount()==1) {
                # code...
                if(is_file("../views/fotos/".$datos['usuario_photo'])){
                    chmod("../views/fotos/".$datos['usuario_photo'], 0777);
                    unlink("../views/fotos/".$datos['usuario_photo']);
                }

                $alerta = [
                    "tipo"=>"recargar",
                    "titulo"=>"Usuario eliminado",
                    "texto"=>"El usuario ".$datos['usuario_name']." ".$datos['usuario_lastname']." ha sido eliminado con éxito",
                    "icono"=>"success"
                ];
            } else {
                # code...
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"No se pudo eliminar el usuario ".$datos['usuario_name']." ".$datos['usuario_lastname']."",
                    "icono"=>"error"
                ];
            }
            return json_encode($alerta);

        }
        
    }