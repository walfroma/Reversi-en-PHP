<?php
/**
 * Un tablero de juego simple con un ancho fijo, altura fija y que contiene piezas modeladas por {@link Piece}
 * - es decir, que puede ser uno de dos estados, blanco o negro.
 */

namespace App;

class Board
{

    /** @var int El número total de columnas en el tablero. */
    private $width;

    /** @var int El número total de filas en el tablero.*/
    private $height;

    /** @var Piece[][] Las piezas en el tablero, agrupadas en filas y luego separadas como columnas. */
    private $pieces = [];

    /** @var Piece El color de la última pieza que se jugó. Si se omite el turno de un jugador, este será el jugador omitido.*/
    private $lastPiece = null;

    /** @var array El número de piezas de cierto tipo en el tablero. */
    private $pieceCount = [
        Piece::BLANK => 0,
        Piece::WHITE => 0,
        Piece::BLACK => 0,
    ];


    /**
     * Cree un tablero con un ancho y alto determinados, y establezca todas las piezas en {@link Piece} .BLANK.
     *
     * @param $width int El ancho (número de columnas) del tablero.
     * @param $height int La altura (número de filas) del tablero.
     */
    public function __construct(int $width = 8, int $height = 8) {
        $this->width = $width;
        $this->height = $height;

        // Set all pieces to Piece.BLANK.
        for($row = 0; $row < $this->height; $row++) {
            $this->pieces[$row] = [];

            for ($column = 0; $column < $width; $column++) {
                $this->setPiece(new Piece(Piece::BLANK), $column, $row);
            }
        }
    }



    /**
     * @return {@link Board#width}.
     */
    public function getWidth() : int {
        return $this->width;
    }


    /**
     * @return {@link Board#height}.
     */
    public function getHeight() : int {
        return $this->height;
    }


    /**
     * @return Piece[][] {@link Board#pieces}.
     */
    public function getPieces() : array {
        return $this->pieces;
    }


    /**
     * @param $xPos int El desplazamiento x, indexado a 0.
     * @param $yPos int El desplazamiento y, indexado a 0.
     *
     * @return Piece La pieza actualmente colocada en la ubicación.
     */
    public function getPiece(int $xPos, int $yPos) : ? Piece {
    if (!$this->isInsideBoard($xPos, $yPos)) {
        return null;
    }

    else {
        return $this->pieces[$yPos][$xPos] ?? null;
    }
}


    /**
     * @return {@link Board#lastPiece}
     */
    public function getLastPiece() : Piece {
        return $this->lastPiece;
    }


    /**
     * @param piece el tipo de pieza a buscar.
     *
     * @return int El número de piezas coincidentes en el tablero..
     */
    public function getPieceCount(Piece $piece) : int {
        return $this->pieceCount[$piece->name()];
    }


    /**
     * @param $xPos int El desplazamiento x.
     * @param $yPos int El desplazamiento y.
     *
     * @return True si las compensaciones dadas son una ubicación válida en el tablero, falso de lo contrario.
     */
    public function isInsideBoard(int $xPos, int $yPos) : bool {
    return $xPos >= 0
        && $yPos >= 0
        && $xPos < $this->getWidth()
        && $yPos < $this->getHeight();
    }


    /**
     *    Consigue al jugador que le toca colocar piezas.
     *
     * @return Piece
     */
    public function getCurrentPlayer() : Piece {
        return $this->lastPiece->opposite();
    }


    /**
     * Coloque la pieza dada en la posición dada.
     *
     * @param $piece Piece La pieza a poner.
     * @param $xPos int El desplazamiento y, indexado a 0.
     * @param $yPos int El desplazamiento y, indexado a 0.
     */
    protected function setPiece(Piece $piece, int $xPos, int $yPos) {

// Disminuye el recuento de piezas al eliminar la pieza anterior.
        $oldPiece = $this->getPiece($xPos, $yPos);

        if ($oldPiece) {
            $this->pieceCount[$oldPiece->name()] -= 1;
        }


// Actualiza la matriz de piezas.
        $this->pieces[$yPos][$xPos] = $piece;

// Actualiza la última pieza.
        $this->lastPiece = $piece;


// Incremento nuevo recuento de piezas.
        $this->pieceCount[$piece->name()] += 1;
    }


    /**
     *     Salta el turno del jugador actual.

     */
    protected function skipTurn() {
        $this->lastPiece = $this->lastPiece->opposite();
    }


    /**
     * @return String que contiene información muy básica sobre el tablero de juego.
     */
    public function __toString() : String {
        $string = "[Game Board; Width = " . $this->width . "; Height = " . $this->height . ";";

        foreach ($this->pieces AS $row) {
            $string .= "\n    |";

            foreach ($row AS $piece) {
                $string .= $piece->name() . "|";
            }
        }

        $string .= "\n]";

        return $string;
    }
}
