<?php
   (include('class/stringtokenizerclass.php')) || die('Failed to include fileI!');
   (include('class/lexerclass.php')) || die('Failed to include fileII!');
    
    
?>
<!DOCTYPE html>
<html>
<head>
    
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Analizador AFCS</title>
    <link rel="stylesheet" href="css/style.css" />
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

</head>
<body>

        <h4>ANALIZADOR LEXICO</h4>
    <div class="main">

    <button onClick="window.location.reload();"> Analizar de Nuevo</button>
        <?php

            $txt = '';
            $fp = fopen("Textos/Codigo.txt", "r");
            while(!feof($fp)) $txt .= fgets($fp);
            fclose($fp);

            echo "<b>ENTRADA (Textos/Codigo.txt)</b>:";
            echo "<BR/><BR/> <PRE>".$txt."</PRE>";
            $lexer = new Lexer($txt);
        ?>
        
        
    </div>

    <h4>Editor de Codigo</h4>

    <div class="main">

    <table>
    <tr><td>Archivo:</td></tr>
    <tr>
        <td colspan="3">
            <textarea id="inputTextToSave" cols="80" rows="10"></textarea>
        </td>
    </tr>
    <tr>
        <td>Guardar Como:</td>
        <td><input id="inputFileNameToSaveAs"></input></td>
        <td><button onclick="saveTextAsFile()">Descargar</button></td>
    </tr>
    <tr>
        <td>Carga el Archivo:</td>
        <td><input type="file" id="fileToLoad"></td>
        <td><button onclick="loadFileAsText()">Cargar</button><td>
    </tr>
</table>
 
<script type="text/javascript">
 
function saveTextAsFile()
{
    var textToSave = document.getElementById("inputTextToSave").value;
    var textToSaveAsBlob = new Blob([textToSave], {type:"text/plain"});
    var textToSaveAsURL = window.URL.createObjectURL(textToSaveAsBlob);
    var fileNameToSaveAs = document.getElementById("inputFileNameToSaveAs").value;
 
    var downloadLink = document.createElement("a");
    downloadLink.download = fileNameToSaveAs;
    downloadLink.innerHTML = "Download File";
    downloadLink.href = textToSaveAsURL;
    downloadLink.onclick = destroyClickedElement;
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
 
    downloadLink.click();
}
 
function destroyClickedElement(event)
{
    document.body.removeChild(event.target);
}
 
function loadFileAsText()
{
    var fileToLoad = document.getElementById("fileToLoad").files[0];
 
    var fileReader = new FileReader();
    fileReader.onload = function(fileLoadedEvent) 
    {
        var textFromFileLoaded = fileLoadedEvent.target.result;
        document.getElementById("inputTextToSave").value = textFromFileLoaded;
    };
    fileReader.readAsText(fileToLoad, "UTF-8");
}
 
</script>

    </div>


</body>
</html>