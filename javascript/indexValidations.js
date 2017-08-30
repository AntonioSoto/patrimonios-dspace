function validateForm() {

    if( document.forma.dcCreator.value == "" ){

        alert("Debe llenar el campo");
        document.forma.dcCreator.focus();
        return false;
    }
    if( document.forma.arqAcabados.value == "" ){

        alert("Debe llenar el campo");
        document.forma.arqAcabados.focus();
        return false;
    }
    if( document.forma.arqBienes.value == "" ){

        alert("Debe llenar el campo");
        document.forma.arqBienes.focus();
        return false;
    }
    if( document.forma.arqCatalogo.value == "" ){

        alert("Debe llenar el campo");
        document.forma.arqCatalogo.focus();
        return false;
    }
    if( document.forma.arqCategoriaActual.value == "" ){

        alert("Debe llenar el campo");
        document.forma.arqCategoriaActual.focus();
        return false;
    }
    if( document.forma.arqCategoriaOrigen.value == "" ){

        alert("Debe llenar el campo");
        document.forma.arqCategoriaOrigen.focus();
        return false;
    }
    if( document.forma.arqEntidad.value == "" ){

        alert("Debe llenar el campo");
        return false;
    }
    if( document.forma.arqLocalidad.value == "" ){

        alert("Debe llenar el campo");
        document.forma.arqLocalidad.focus();
        return false;
    }
    if( document.forma.arqNombre.value == "" ){

        alert("Debe llenar el campo");
        document.forma.arqNombre.focus();
        return false;
    }

    return true;
}

function validateForm2() {

    if( document.forma2.itemUUID.value == "" ){

        alert("Debe llenar el campo");
        document.forma2.itemUUID.focus();
        return false;
    }
    if( document.forma2.itemDescription.value == "" ){

        alert("Debe llenar el campo");
        document.forma2.itemDescription.focus();
        return false;
    }

    return true;
}