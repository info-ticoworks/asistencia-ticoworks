if (typeof navigator.geolocation == 'object'){
    navigator.geolocation.getCurrentPosition(mostrar_ubicacion);
}
 
function mostrar_ubicacion(p)
{
    var posicion = p.coords.latitude+','+p.coords.longitude;
    window.location.href = window.location.href + "?w1=" + posicion;
}

/*function mostrar_ubicacion(p)
{
     var posicion = p.coords.latitude+','+p.coords.longitude;
    alert('posición: '+posicion);
}*/