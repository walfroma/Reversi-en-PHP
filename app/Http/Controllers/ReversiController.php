<?php
/**
 * Created by PhpStorm.
 * User: joseph
 * Date: 16/12/17
 * Time: 14:57
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reversi;
use Illuminate\Support\Facades\Input;

class ReversiController
{

    /**
     * @var Reversi
     */
    private $reversiInstance;

    protected function setInstance(Request $request) {

        $reversiInstance = $request->session()->get('reversiInstance');

        if (!$reversiInstance) {
            $reversiInstance = new Reversi();
            $request->session()->put('reversiInstance', $reversiInstance);
        }

        $this->reversiInstance = $reversiInstance;

    }

    /**
     * Crea un nuevo juego para el usuario.
     *
     * @param  int  $width
     * @param  int  $height
     *
     * @return Response
     */
    public function show(Request $request)
    {
        $this->setInstance($request);

        return view('reversi', ['reversiInstance' => $this->reversiInstance]);

    }


    public function move(Request $request) {

        $this->setInstance($request);


        /* Obtener parámetros */
        $xPos = (int) Input::get('xPos');
        $yPos = (int) Input::get('yPos');


        /* Parámetros de proceso */
        if (!$this->reversiInstance->setReversiPiece($xPos, $yPos))
            $request->session()->put('errMsgs', ["Ese movimiento no es legal."]);


        /* Enviar redireccionamiento */
        return redirect('/');

    }


    public function newGame(Request $request) {

        /* Obtener parámetros*/
        $width = (int) Input::get('width') ?: 8;
        $height = (int) Input::get('height') ?: 8;


        /* Validar Parámetros */
        $errMsgs = [];

        if ($width < 4 || $width > 16 || $width % 2 != 0) {
            $errMsgs[] = "El ancho debe ser un número par entre 4 y 16";
        }

        if ($height < 4 || $height > 16 || $height % 2 != 0) {
            $errMsgs[] = "La altura debe ser un número par entre 4 y 16.";
        }


        /* Sesión de actualización */
        if (count($errMsgs) === 0){
            $request->session()->put('reversiInstance', new Reversi($width, $height));
            $request->session()->put('errMsgs2', ["Nuevo Juego."]);
        }
        else
            $request->session()->put('errMsgs', $errMsgs);


        /* Enviar redireccionamiento */
        return redirect('/');

    }

}
