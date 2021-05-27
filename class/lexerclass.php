<?php

class Lexer{

    protected $_lineas;
    protected $_numero;
    protected $_token;
    protected $_tokens = array();

    protected $_afd = array(
        0 => array(3, false, 1, false),
        1 => array(3, 3, 2, false),
        2 => array(3, false, false, false),
        3 => array(3, 3, false, true)
    );

    protected $_tokenList = array(
        //Simbolos
        " " => "ESPACIO",
        '"' => "COMILLA_DOBLE",
        "*" => "MULTIPLICACION",
        "+" => "SUMA",
        "-" => "RESTA",
        "/" => "DIVISION",
        "=" => "IGUAL",
        ">" => "MAYOR_QUE",
        "<" => "MENOR_QUE",
        "$" => "INICIO_VARIABLE",
        ";" => "FIN_DE_CADENA",
        "//" => "COMENTARIO",
        "<?" => "INIT_PHP",
        "/>" => "CLOSE_PHP",


        //Reservadas
        "entero"  => "<font color=\"GREEN\">TIPO_DATO_ENTERO</font>",
        "cadena"  => "<font color=\"GREEN\">TIPO_DATO_CADENA</font>",
        "si"      => "<font color=\"GREEN\">ESTRUCTURA_CONDICIONAL_IF</font>",
        "entonces" => "<font color=\"GREEN\">THEN</font>",
        "imprime" => "<font color=\"GREEN\">echo</font>",
        "fopen" => "<font color=\"GREEN\">ABRIR-ESTRUCTURA</font>",
        "r+" => "<font color=\"GREEN\">ESTRUCTURA-EDITABLE</font>",
        "escriba" => "<font color=\"GREEN\">ESCRITURA</font>",
        "fin"     => "<font color=\"GREEN\">FIN_ESTRUCTURA_CONTROL</font>",
        "si esto entonces" => "<font color=\"GREEN\">IF_THIS_THEN</font>",
        "igual" => "<font color=\"GREEN\">EQUAL</font>",
        "freno" => "<font color=\"GREEN\">BREACK</font>",
        "y" => "<font color=\"GREEN\">ANd</font>",
        "capturar"=>"<font color=\"GREEN\">CATCH</font>",
        "clase" => "<font color=\"GREEN\">CLASS</font>",
        "clonar" => "<font color=\"GREEN\">CLONE</font>",
        "salir" => "<font color=\"GREEN\">EXIT</font>",
        "vacio" => "<font color=\"GREEN\">EMPTY</font>",
        "declaraed"     => "<font color=\"GREEN\">EDDECLARE</font>",
        "termina_si()" => "<font color=\"GREEN\">ENDIF</font>",
        "termina_para" => "<font color=\"GREEN\">ENDFOR</font>",
        "ve_a" => "<font color=\"GREEN\">GOTO</font>",
        "Implementa" => "<font color=\"GREEN\">IMPLEMENTS</font>",
        "incluye"=>"<font color=\"GREEN\">INCLUDE</font>",
        "instanciar" => "<font color=\"GREEN\">INSTANCEOF</font>",
        "interface"=>"<font color=\"GREEN\">INTERFACE</font>",
        "imprime" => "<font color=\"GREEN\">PRINT</font>",
        "ejecuta" => "<font color=\"GREEN\">EXECUTE</font>",
        "saltolinea" => "<font color=\"GREEN\">JUMP_LINE</font>",
        "lanza" => "<font color=\"GREEN\">TROW</font>",
        "limpiarV"=> "<font color=\"GREEN\">UNSET</font>",
        "estilo" => "<font color=\"GREEN\">STYLE</font>",
        "ref" => "<font color=\"GREEN\">REFERENCIA</font>",
        "html" => "<font color=\"GREEN\">HTML</font>",
        "www.a-z.com"=>"<font color=\"GREEN\">LINK</font>",
        "deOtra"=>"<font color=\"GREEN\">else</font>",

        

    );

    protected $_delimitadores = ' "'; // Los delimitadores son ESPACIO y COMILLA_DOBLE
    
    function __construct($txt){
        $this->_lineas   = preg_split("/(\r\n|\n|\r)/", trim($txt));
 
        foreach($this->_lineas as $numero => $linea){
            $this->_numero = $numero;
            $this->lexico($linea);
        }

        $this->printTokens();
    }

    function lexico($linea){
        $tokens_line = new StringTokenizer($linea, $this->_delimitadores);
        foreach ($tokens_line as $pos => $tok) {
            $this->_token = $tok;
            $busqueda = $this->buscarExpresion();
            if($busqueda === false){
                if($this->esNumerico())
                    $this->_tokens[] =  $this->returnTokenItem("NUMERO");
                elseif($this->esIdentificador())
                    $this->_tokens[] = $this->returnTokenItem("PALABRA");
                elseif($this->esVariable())
                    $this->_tokens[] = $this->returnTokenItem("VARIABLE");
                else
                    $this->_tokens[] = $this->returnTokenItem("<font color=\"red\">ERROR</font>");
            }else{
                $this->_tokens[] =  $this->returnTokenItem();
            }
        }
    }



    function buscarExpresion($c=null){
        if($c==null) $c = $this->_token;
        foreach($this->_tokenList as $exp => $name)
            if($exp == $c) return $name;
        return false;
    }

    function returnTokenItem($v=false){
        if($v==false) $v = $this->_tokenList;
        else $token =  $v;
        if(is_array($v)) $token = $this->buscarExpresion();
        return array(
            'lexema' => $this->_token,
            'token' => $token,
            'linea' => $this->_numero+1
        ); 
    }

    function esLetra($c=null){
        if($c==null) $c = $this->_token;
        $c = ord(strtolower($c));
        if($c >= 97 && $c <= 122) return true;
        return false;
    }

    function esNumerico($c=null){
        if($c==null) $c = $this->_token;
        if(is_numeric($c)){
            $c = ord(strtolower((int)$c));
            if($c >= 48 && $c <= 57) return true;
        }
        return false;
    }

    function esGuionBajo($c=null){
        if($c==null) $c = $this->_token;
        return ($c == "_");
    }

    function esVariable($c=null){
        if($c==null) $c = $this->_token;
        return ($c == "$ a-z");
    }

    function esIdentificador($c=null){
        if($c==null) $c = $this->_token;
        $transiciones = strlen($c);
        $i = 0; $estado = 0;
        while($i <= $transiciones){
            if($i==$transiciones) $entrada = 3; // COLUMNA DE ACEPTACION
            else{
                $entrada = $c[$i];
                if($this->esLetra($entrada))           $entrada = 0; // Letra
                elseif($this->esNumerico($entrada))    $entrada = 1; // Digito
                elseif($this->esGuionBajo($entrada))   $entrada = 2; // GuionBajo
                else return false;
            }
            $estado = $this->_afd[$estado][$entrada];
            if($estado === false || $estado === true) return $estado;
            $i++;
        }
    }

    function printTokens(){
        echo "
        <table class='lexer'>
            <thead>
                <tr>
                    <th>NRO</th>
                    <th>LEXEMA</th>
                    <th>TIPO</th>
                    <th>LINEA</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($this->_tokens as $num => $item) {
            echo "<tr><td>".($num+1)."</td>";
            foreach ($item as $valor){
                if($valor == "ERROR") $valor = "<b>".$valor."</b>";
                echo "<td>".$valor."</td>";
            }
            echo "</tr>";
        }


        echo "</tbody>
        </table>";
    }

    function printTokenList(){
        echo "
        <table class='lexer2'>
            <thead>
                <tr>
                    <th>LEXEMA</th>
                    <th>TIPO</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($this->_tokenList as $lexema => $tipo) {
            echo "<tr><td>".$lexema."</td><td>".$tipo."</td></tr>";
        }


        echo "</tbody>
        </table>";
    }
}