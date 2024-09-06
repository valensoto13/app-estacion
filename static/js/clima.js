document.addEventListener("DOMContentLoaded", () => {

	// pedimos las estaciones
	Clima().then( data => {

		// recorremos el listado de estaciones
		data.forEach(function(element, index){

			// creamos los botones de estaciones
			console.log(element);
			BotonClima(element)
			})
		})
})

	function BotonClima(clim){
		let tpl = document.querySelector("#boton-clima")
		let clon = tpl.content.cloneNode(true)

		clon.querySelector(".btn-clima").setAttribute("href", "./detalle/"+clim.chipid)
		clon.querySelector(".clima-ubicacion").innerHTML= '<i class="fa-solid fa-location-dot"></i>'+clim.ubicacion
		clon.querySelector(".clima-visitas").innerHTML = clim.visitas+'<i class="fa-solid fa-person"></i>'
		clon.querySelector(".clima-apodo").innerHTML = clim.apodo
		
		// Agrega un nuevo botón de estación
		document.querySelector("#list-estacion").appendChild(clon)
	}
    //tengo la info del clima
	async function Clima(){
			const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations")
			const data = await response.json()
			
			return data
		}