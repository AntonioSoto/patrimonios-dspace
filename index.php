<html>

	<head>

		<title>Dspace requests</title>

		<meta charset="UTF-8">
        <script src="javascript/indexValidations.js"></script>

        <script>
            <!--
            window.onload = function(){

                document.forma.onsubmit = function(){

                    return validateForm();
                };

                document.forma2.onsubmit = function(){

                    return validateForm2();
                }
            };
            //-->
		</script>
		
	</head>
	
	<body>

        <!--
		<form name="forma" method="post" action="savesDspace.php" enctype="multipart/form-data">
		
			<table border="1">

                <tr>
                    <td>Nombre de quien sube los datos: </td>
                    <td><input type="text" name="dcCreator" size="30"></td>
                </tr>

                <tr>
                    <td>Nombre de la estructura: </td>
                    <td><input type="text" name="arqNombre" size="30"></td>
                </tr>

                <tr>
                    <td>Entidad: </td>
                    <td>
                        <select name="arqEntidad">
                            <option value="Yucatán">Yucatán</option>
                            <option value="Campeche">Campeche</option>
                            <option value="Quintana Roo">Quintana Roo</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Localidad: </td>
                    <td><input type="text" name="arqLocalidad" size="30"></td>
                </tr>
			
				<tr>
					<td>Acabados: </td>
					<td><input type="text" name="arqAcabados" size="30"></td>
				</tr>
				
				<tr>
					<td>Bienes: </td>
					<td><input type="text" name="arqBienes" size="30"></td>
				</tr>
				
				<tr>
					<td>Catálogo: </td>
					<td><input type="text" name="arqCatalogo" size="30"></td>
				</tr>
				
				<tr>
					<td>Categoría Actual: </td>
					<td><input type="text" name="arqCategoriaActual" size="30"></td>
				</tr>
				
				<tr>
					<td>Categoría de Origen: </td>
					<td><input type="text" name="arqCategoriaOrigen" size="30"></td>
				</tr>
			
			</table>
			
			<input type="submit" name="btn_enviar" value="Enviar">
		
		</form>
        -->

        <form name="forma2" method="post" action="savesDspace.php" enctype="multipart/form-data">

            <table border="1">

                <tr>
                    <td>UUID: </td>
                    <td><input type="text" name="itemUUID" size="50"></td>
                </tr>

                <tr>
                    <td>Autoría de la foto: </td>
                    <td><input type="text" name="itemDescription" size="50"></td>
                </tr>

                <tr>
                    <td>Foto: </td>
                    <td><input name="file" type="file" id="file"/></td>
                </tr>

            </table>

            <input type="submit" name="btn_subirFoto" value="Subir foto">

        </form>

        <form name="forma3" method="post" action="savesDspace.php" enctype="multipart/form-data">

            <table border="1">

                <tr>
                    <td>Metadatos en Excel: </td>
                    <td><input name="excelFile" type="file" id="excelFile"/></td>
                </tr>

            </table>

            <input type="submit" name="btn_importItems" value="Importar datos de Excel a Dspace">

        </form>

        <form name="forma4" method="post" action="savesDspace.php" enctype="multipart/form-data">

            <table border="1">

                <tr>
                    <td>Metadatos en Excel: </td>
                    <td><input name="excelFile" type="file" id="excelFile"/></td>
                </tr>

            </table>

            <input type="submit" name="btn_refreshItems" value="Actualizar datos de Excel a Dspace">

        </form>

        <form name="forma5" method="post" action="savesDspace.php" enctype="multipart/form-data">

            <table border="1">

                <tr>
                    <td>Fotos/Archivos en Excel: </td>
                    <td><input name="excelFile" type="file" id="excelFile"/></td>
                </tr>

            </table>

            <input type="submit" name="btn_importBitstreams" value="Importar fotos de Excel a Dspace">

        </form>

        <form method="post" action="savesExcel.php">

            <input type="submit" name="btn_excel" value="Exportar datos de Dspace a Excel">

        </form>

        <form method="post" action="savesDspace.php">

            <input type="submit" name="btn_deleteItems" value="Eliminar todos los items de Dspace">

        </form>
		
	</body>

</html>