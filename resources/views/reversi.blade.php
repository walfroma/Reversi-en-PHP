<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <title>Reversi</title>

    <!-- Utilizado para el estilo básico de bootstrap.-->
    <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
            integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb"
            crossorigin="anonymous">
    <script
            src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>

    <link href="{{ asset('/css/reversi.css') }}" rel="stylesheet" type="text/css">

</head>
<body>
<!-- Display Reversi Game -->
<div class="reversiBoardContainer" style="position: relative;" id="recargar">
    <div style="position: absolute; width: 100%;">

        <!-- Si el juego ha terminado, muestre una superposición que indique tanto y solicite un nuevo juego. -->
        @if ($reversiInstance->isGameOver())
            <div style="position: absolute; background-color: rgba(255, 255, 255, .75); min-height: 100%; width: 100%; text-align: center; vertical-align: middle;">
                <div class="jumbotron container" style="position: absolute; background: transparent; min-height: 100%; min-width: 100%; margin: 0; z-index: 2000;">
                    <h1 class="display-3" style="text-shadow: 2px 2px 10px #afafaf;">Game Over</h1>
                    <p style="text-shadow: 1px 1px 2px #9f9f9f;">
                        @if ($reversiInstance->getWhitePieceCount() > $reversiInstance->getBlackPieceCount())
                            Jugador 1 Gano, {{ $reversiInstance->getWhitePieceCount() }} - {{ $reversiInstance->getBlackPieceCount() }}
                        @elseif ($reversiInstance->getBlackPieceCount() > $reversiInstance->getWhitePieceCount())
                            Jugador 2 Gano, {{ $reversiInstance->getBlackPieceCount() }} - {{ $reversiInstance->getWhitePieceCount() }}
                        @else
                            Empate
                        @endif
                    </p>

                    <hr class="my-4" />
                    <form method="post" action="newGame?width=8&height=8">
                    <p class="lead">

                            <button class="btn btn-primary btn-lg" style="opacity: .85;">Nuevo Juego</button>

                    </p>
                    </form>
                </div>
            </div>
        @endif

        <!-- El tablero Reversi en sí, que incluye información de puntaje y nuevos botones de juego, abandono y pista. -->
        <table border="1" class="reversiBoard table table-striped table-bordered" style="width: auto; margin-left: auto; margin-right: auto;">


            <!-- Nuestra pantalla de puntuación. -->


            <tbody>
            <tr class="table-sm">

                <th class="table-light" colspan="{{$reversiInstance->getWidth() * .5}}" style="text-align: center;">Puntacion Ficha Blanca</th>
                <th class="table-dark" colspan="{{$reversiInstance->getWidth() * .5}}" style="text-align: center;">Puntuacion Ficha Negra</th>
            </tr>
            <tr class="table-sm">
                <td class="table-light"  id="blanco" colspan="{{$reversiInstance->getWidth() * .5}}" style="text-align: center;">{{ $reversiInstance->getWhitePieceCount() }}</td>
                <td class="table-dark"  id="negro" colspan="{{$reversiInstance->getWidth() * .5}}" style="text-align: center;">{{ $reversiInstance->getBlackPieceCount() }}</td>
            </tr>
            </tbody>


            <!-- Nuestro tablero principal muestra, mostrando todas las piezas y cuadrados vacíos que representan el tablero.-->
            <tbody class="pieces">

                @foreach ($reversiInstance->getPieces() as $row)
                    <tr id="cuadro">

                        @foreach ($row as $column)
                            <td
                                align="center"
                                width="40"
                                @if ($column->name() == "BLANK")
                                    onmouseover="
                                    $(this).attr('data-oldClass', $('div', this).attr('class'));
                                    $('div', this).attr('class', '{{strtolower($reversiInstance->getCurrentPlayer()->name())}}Piece')
                                    "
                                    onmouseout="$('div', this).attr('class', $(this).attr('data-oldClass'));"
                                @endif
                            >

                            @if ($column->name() == "BLANK")
                                    <!-- Si bien es una mala práctica, creo que la experiencia general del usuario es más efectiva cuando se utilizan anclas en lugar de formularios.
                                    Por lo tanto, enviamos nuestros acordes de movimiento con parámetros GET en lugar de los POST. -->
                                    <a href="move?yPos={{$loop->parent->index}}&xPos={{$loop->index}}" style="display: block; overflow: hidden;">
                                        <div class="
                                            blankPiece
                                            @if ($reversiInstance->isLegalMove($reversiInstance->getCurrentPlayer(), $loop->index, $loop->parent->index))
                                                {{strtolower($reversiInstance->getCurrentPlayer()->name())}}Piece hint
                                            @endif
                                        "></div>
                                    </a>
                            @else
                                <div class="{{strtolower($column->name())}}Piece"></div>
                            @endif
                        @endforeach

                    </tr>
                @endforeach

            </tbody>

            <!-- Mensaje quien es el ganador -->
            @if(Session::has('errMsgs3'))
                <tbody class="errors">
                <tr class="table-danger">
                    <td colspan="{{$reversiInstance->getWidth()}}">
                        <h4> Error encontrado!</h4>

                        <ul style="margin-bottom: 0px;">
                            @foreach (Session::get('errMsgs3') AS $errMsg)
                                <li>{{$errMsg3}}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                </tbody>

                <!-- Consuma el error, evite que se vuelva a mostrar.-->
                {{Session::put('errMsgs', null)}}
            @endif


            <!-- Errores de visualización; consumir cualquier mensaje de error colocado en la sesión del usuario. -->
            @if(Session::has('errMsgs'))
                <tbody class="errors">
                <tr class="table-danger">
                    <td colspan="{{$reversiInstance->getWidth()}}">
                        <h4> Error encontrado!</h4>

                        <ul style="margin-bottom: 0px;">
                            @foreach (Session::get('errMsgs') AS $errMsg)
                                <li>{{$errMsg}}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                </tbody>

                <!-- Consuma el error, evite que se vuelva a mostrar.-->
                {{Session::put('errMsgs', null)}}
            @endif


        <!--Bienvenido nuevo juego -->
            @if(Session::has('errMsgs2'))
                <tbody class="info" >
                <tr class="table-info" id="mensaje2">
                    <td colspan="{{$reversiInstance->getWidth()}}">
                        <h4 > Bienvenido!</h4>

                        <ul style="margin-bottom: 0px;">
                            @foreach (Session::get('errMsgs2') AS $errMsg2)
                                <li>{{$errMsg2}}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                </tbody>

                <!-- Consuma el error, evite que se vuelva a mostrar.-->
            {{Session::put('errMsgs2', null)}}
        @endif

            <!-- Mostrar botones para pistas, juegos nuevos. -->
            <tbody class="utilities">
                <tr>
                    <td colspan="{{$reversiInstance->getWidth()}}">
                        <button
                            onclick="$('.hint').toggleClass('hintDisplay'); $(this).text($(this).text() === 'Show Hints' ? 'Hide Hints' : 'sugerencias');"
                            class="btn btn-info form-control"
                        >
                            Mostrar sugerencias
                        </button>
                    </td>
                </tr>

                <form method="get" action="newGame">
                    <tr>
                        <td colspan="{{$reversiInstance->getWidth()}}">
                            <div class="row">
                                <div class="col-6">
                                    <label class="input-group">
                                        <span class="input-group-addon">Ancho</span>
                                        <input class="form-control" style="width: 3em;" name="width" value="8" readonly />
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="input-group">
                                        <span class="input-group-addon">Alto</span>
                                        <input class="form-control" style="width: 3em;" name="height" value="8"  readonly/>
                                    </label>
                                </div>
                            </div>

                            <input type="submit" value="¡Comienzar un nuevo juego!" class="form-control btn btn-success"  id="nuevo"/>
                        </td>
                    </tr>
                </form>
            </tbody>


        </table>
    </div>
</div>

<script type="text/javascript">
    function mostrar(num) {
        var x = $("#num").val();
        alert(x);
    }

    blanco = document.getElementById("blanco").value;
    negro = document.getElementById("negro").value;
    juego = 64
    suma = 0
    ayuda = 0
    //Cuando la página esté cargada completamente
    $(document).ready(function(){
        //Cada 10 segundos (10000 milisegundos) se ejecutará la función refrescar
        setTimeout(refrescar, 5000);




    });
    function refrescar(){
        //Actualiza la página
        location.reload();
      //  $("#cuadro").click(function () {
        //    location.reload();
        //})
    }

    $(document).ready(function() {

            suma = blanco + negro;

            if (((juego = suma) && (blanco > negro))) {
                alert('El ganador es el blaco');

            } else if (((juego = suma) && (negro > blanco))) {
                alert('El ganador es el negro');
            } else  if (((juego = suma) && (negro = blanco))){
                alert('Empatados');
            }


    });





</script>

</body>
</html>
