<?php

/**
 * Una enumeración básica para las piezas del juego, y se usa con frecuencia como sustituto de los jugadores.
 * Tiene tres valores: NEGRO, BLANCO y EN BLANCO.
 */

namespace App;


class Piece
{

    const BLANK = "BLANK";
    const WHITE = "WHITE";
    const BLACK = "BLACK";

    const BLANK_OPPOSITE = "BLANK";
    const WHITE_OPPOSITE = "BLACK";
    const BLACK_OPPOSITE = "WHITE";


    private $name;

    public function __construct($name)
    {
        $this->name = constant('self::' . $name);
    }

    public function name() : string {
        return $this->name;
    }

    public function opposite() : Piece {
        return new Piece($this->name() . "_OPPOSITE");

        /*
        if ($this->name() === "BLANK")
            return new Piece("BLANK");
        elseif ($this->name() === "WHITE")
            return new Piece("BLACK");
        elseif ($this->name() === "BLACK")
            return new Piece("WHITE");
        else
            throw new \Exception("Invalid piece.");*/
    }
}
