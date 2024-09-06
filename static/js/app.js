// Evento que se ejecuta cuando se carga completamente la pÃ¡gina
document.addEventListener("DOMContentLoaded", () => {
    // pedimos las estaciones
    loadEstaciones().then( data => {
        // recorremos el listado de estaciones
        data.forEach(function(element, index){
            // creamos los botones de estaciones
            addBtnEstacion(element)
        })
    })
})
// PeticiÃ³n asincrona de la lista de estaciones
async function loadEstaciones(){
    const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations")
    const data = await response.json()
    return data
}
// Crea un nuevo botÃ³n con los datos de info
function addBtnEstacion(info){
    let tpl = document.querySelector("#tpl-btn-estacion");
    let clon = tpl.content.cloneNode(true);

    console.log(info);

    // cargamos los datos del botÃ³n clonado
    clon.querySelector(".btn-estacion").setAttribute("href", "views/detalle.php?chipid="+info.chipid);
    clon.querySelector(".estacion-ubicacion").innerHTML= '<i class="rojo material-symbols-outlined">location_on</i>'+'<h3>'+info.ubicacion+'<h3>';
    clon.querySelector(".estacion-visitas").innerHTML = '<h3>'+info.visitas+'</h3>'+'<i class="rojo material-symbols-outlined">rss_feed</i>'
    clon.querySelector(".estacion-apodo").innerHTML = '<h2>'+info.apodo+'</h2>';

// Agrega un nuevo botÃ³n de estaciÃ³n
    document.querySelector("#list-estacion").appendChild(clon)
} 