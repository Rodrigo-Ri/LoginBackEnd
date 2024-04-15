<?php

    namespace app\controllers;
    use app\models\viewsModel;

    class viewsController extends viewsModel{

        public function obtenerVistasControlador($vistas){

            if($vistas != ""){
                $respuesta = $this->obtenerVistasModelo($vistas);
            }else{
                $respuesta = "login";
            }
            return $respuesta;

        }

    }