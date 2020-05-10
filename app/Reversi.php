<?php

namespace App;

/**
 * Esto modela Reversi, implementando la verificación de movimiento extendida desde la clase base {@link Board}.
 *
 */
class Reversi extends Board
{

    /** @var boolean Si el juego ha sido forzado a terminar por un jugador. Si se establece, ningún movimiento se considera legal, y esta clase debe ser de solo lectura. */
    private $hasGivenUp = false;

    /** @var array Desplazamientos que forman líneas direccionales. Se incluyen todas las líneas posibles en una cuadrícula cuadrada. */
    const VALID_SHIFTS = [
        [-1, -1],// Arriba y a la izquierda
        [0, -1], // Arriba
        [1, -1], // Arriba y a la dereche

        [-1, 0], // Izquierda
        [1, 0], // Derecha

        [-1, 1], // Abajo y a la izquierda
        [0, 1], // Abajo
        [1, 1], // Abajo y a la derecha
    ];


    /**
     * Cree un tablero Reversi con un ancho y una altura dados, y coloque las cuatro piezas iniciales en el medio del tablero.
     *
     * @param $width int El ancho (número de columnas) del tablero.
     * @param $height int La altura (número de filas) del tablero.
     */
    public function __construct(int $width = 8, int $height = 8) {
        parent::__construct($width, $height);

        /* Place the initial pieces on the board. */
        $this->setPiece(new Piece(Piece::WHITE), $width / 2 - 1, $height / 2 - 1);
        $this->setPiece(new Piece(Piece::BLACK), $width / 2,     $height / 2 - 1);
        $this->setPiece(new Piece(Piece::BLACK), $width / 2 - 1, $height / 2    );
        $this->setPiece(new Piece(Piece::WHITE), $width / 2,     $height / 2    );
    }



    /**
     * @param piece Pieza El tipo de pieza a buscar.
     * @return int El número de piezas coincidentes en el tablero, o el número de cuadrados en el tablero si la pieza
     * opuesta no tiene ninguno. (si de alguna manera no hay piezas en el tablero, esto devolverá el número de cuadrados en el tablero, independientemente de la entrada).
     */
    public function getPieceCount(Piece $piece) : int {
        return parent::getPieceCount($piece->opposite()) == 0
            ? $this->getHeight() * $this->getWidth()
            : parent::getPieceCount($piece);
    }


    /**
     * @return int El numero de {@link Piece}. Piezas blancas en el tablero.
     */
    public function getWhitePieceCount() : int {
        return $this->getPieceCount(new Piece(Piece::WHITE));
    }

    /**
     * @return int el numero de {@link Piece}. Piezas negras en el tablero.
     */
    public function getBlackPieceCount() : int {
        return $this->getPieceCount(new Piece(Piece::BLACK));
    }


    /**
     * Determine si, desde la posición inicial, la línea en la dirección de [shiftX, shiftY] está formada por un color de pieza y luego terminó con la pieza de color opuesta.
     *
     * @param $endingWith Piece la pieza que debe estar al final de la línea.
     * @param $startX int La coordenada X para comenzar a formar una línea en.
     * @param $startY int La coordenada Y para comenzar a formar una línea en.
     * @param $shiftX int El cambio X para usar cuando se buscan cadenas de piezas.
     * @param $shiftY int El cambio Y se usa al buscar cadenas de piezas.
     *
     * @return True si existe una línea de piezas que coincida con los criterios anteriores, falso de lo contrario.
     */
    public function isLegalLine(Piece $endingWith, int $startX, int $startY, int $shiftX, int $shiftY) : bool {
        return !$this->hasGivenUp // The game mustn't have been ended manually.
            && $this->isInsideBoard($startX + $shiftX, $startY + $shiftY) // The target piece must be legal.
            && $this->getPiece($startX, $startY) == $endingWith->opposite() // Our starting piece must be opposite our ending piece.
            && ( // The next piece must either be...
                $this->getPiece($startX + $shiftX, $startY + $shiftY) == $endingWith // The ending piece.
                || $this->isLegalLine($endingWith, $startX + $shiftX, $startY + $shiftY, $shiftX, $shiftY) // Or a row of the opposite piece ending with the ending piece.
            );
    }


    /**
     * Obtenga todas las líneas legales, según lo definido por {@link Reversi # getLegalLines (edu.metrostate.ics425.jtp307.reversi.model.Piece, int, int)},
     * que existe para una posición y pieza de inicio.
     * Esto verificará las ocho líneas definidas por {@link Reversi # VALID_SHIFTS}, y devolverá alguna combinación de ellas.
     *
     * @param $piece Piece La pieza que se está jugando en la posición [X, Y].
     * @param $posX int La posición X de la pieza que se está jugando, indexada en 0.
     * @param $posY int La posición Y de la pieza que se está jugando, indexada en 0.
     *
     * @return array Some combination of {@link Reversi#VALID_SHIFTS}.
     */
    public function getLegalLines(Piece $piece, int $posX, int $posY) : array {
        $matchedLines = [];

        foreach (Reversi::VALID_SHIFTS AS $shiftPair) {
            if ($this->isLegalLine($piece, $posX + $shiftPair[0], $posY + $shiftPair[1], $shiftPair[0], $shiftPair[1])) {
                $matchedLines[] = $shiftPair;
            }
        }

        return $matchedLines;
    }


    /**
     * Determine si una pieza se puede jugar legalmente en una posición determinada.
     * @param $piece Piece
    La pieza que se está jugando en la posición [X, Y].
     * @param $posX int La posición X de la pieza que se está jugando, indexada en 0.
     * @param $posY int La posición Y de la pieza que se está jugando, indexada en 0.
     *
     * @return True si la colocación de la pieza dada es legal, de lo contrario es falsa.
     */
    public function isLegalMove(Piece $piece, int $posX, int $posY) : bool {
        return $this->getPiece($posX, $posY) == new Piece(Piece::BLANK)
            && count($this->getLegalLines($piece, $posX, $posY)) > 0;
    }


    /**
     * Establece la pieza cuyo turno está en el tablero en la ubicación dada.
     *No hará nada si el movimiento es ilegal.
     *
     * @param $posX int El desplazamiento x, indexado a 0.
     * @param $posY int El desplazamiento y, indexado a 0.
     *
     * @return True si el intento de movimiento es legal, falso de lo contrario.
     */
    public function setReversiPiece(int $posX, int $posY) : bool {
        $piece = $this->getCurrentPlayer();

        // Don't place on non-blank squares.
        if ($this->getPiece($posX, $posY) == new Piece(Piece::BLANK)) {
            $legalLines = $this->getLegalLines($piece, $posX, $posY);

            // Don't place if no legal lines are found.
            if (count($legalLines) > 0) {

                // Flip all pieces that are part of legal lines.
                foreach ($legalLines as $shiftPair) {
                    // Set the piece itself.
                    $this->setPiece($piece, $posX, $posY);

                    // Set all pieces in the line.
                    $posXLine = $posX;
                    $posYLine = $posY;

                    while ($this->getPiece($posXLine += $shiftPair[0], $posYLine += $shiftPair[1]) != $piece) {
                        $this->setPiece($piece, $posXLine, $posYLine);
                    }
                }


// Salta el turno del siguiente jugador si no tiene movimiento disponible.
                if (!$this->isMoveAvailable($this->getCurrentPlayer())) {
                    $this->skipTurn();
                }


// Devuelve verdadero para indicar que se ha colocado una pieza.
                return true;

            }
        }

        return false;
    }


    /**
     * @param piece El color de una pieza que se está reproduciendo.
     *
     * @return bool Verdadero si la pieza dada se puede colocar en algún lugar del tablero, falso de lo contrario.
     */
    public function isMoveAvailable(Piece $piece) : bool {
        for ($row = 0; $row < $this->getHeight(); $row++) {
            for ($column = 0; $column < $this->getWidth(); $column++) {
                if ($this->isLegalMove($piece, $column, $row)) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * @return bool Verdadero si el juego ha terminado (porque no hay movimientos legales disponibles), falso de lo contrario.
     */
    public function isGameOver() : bool {
        return false;

// Cuando se coloca una pieza, el turno del siguiente jugador se omite automáticamente si no hay movimiento disponible.
// Por lo tanto, esto solo será cierto cuando ninguno de los jugadores tenga un movimiento legal.
        return !$this->isMoveAvailable($this->getCurrentPlayer());
    }


    /**
     * Establece la bandera de abandono, terminando el juego prematuramente.
     */
    public function giveUp() {
        $this->hasGivenUp = true;
    }
}
