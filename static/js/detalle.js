let chipip = "";

let fec = [];
let tem = [];
let hum = [];
let pre = [];
let vie = [];
let fwi = [];

// Los datos que nos muestra la pagina
const datos = ['fuego','humedad','temperatura','presion','viento'];

// Objeto que contiene el grafico
let myChart = null

// Pestaña visible
let sectionVisible = "temperatura";

//Cuando la pagina carga...
document.addEventListener("DOMContentLoaded", () => {
	// buscamos el chipid
	chipid = document.querySelector("#chipid").innerHTML;

	// primer carga de 6 datos
	refreshDatos(7);

	// recargar el chipid cada un minuto
	refreshId = setInterval(refreshDatos, 60000, 1);
	console.log("Refresco cada 60000 seg.");

	// Incrementa las visitas de esta estacion
	addVisitStation();

	let dataJsonActual = ""

	//Botones que muestran los datos
	btn__temperatura.addEventListener('click',() => {refreshPanelInfo('temperatura');});
	btn__humedad.addEventListener('click',() => {refreshPanelInfo('humedad');});
	btn__presion.addEventListener('click',() => {refreshPanelInfo('presion');});	
	btn__viento.addEventListener('click',() => {refreshPanelInfo('viento');});
	btn__fuego.addEventListener('click',() => {refreshPanelInfo('fuego');});
})

function procesar(datos, addData = true){

	let hora = ""

	console.log("Filas Json: " + datos.length);

	if(addData == true){
		// Recorremos el Json pero al reves. datos viejos a la izquierda nuevos derecha
		for (let i = datos.length-1; i >= 0; i--) {

			console.log("Vigia Json [" + i + "]" + datos[i].fecha);

			hora = datos[i].fecha.split(" ")[1]

			// Carga de vectores para generar el grÃ¡fico
			fec.push(hora.split(":")[0]+":"+hora.split(":")[1]);
			tem.push(datos[i].temperatura);
			hum.push(datos[i].humedad);
			vie.push(datos[i].viento);
			fwi.push(datos[i].fwi);
			pre.push(datos[i].presion);
		}

		// Elimina los ultimos datos de los vectores si el Ãºltimo fec es igual al anteÃºltimo.
		if(fec[fec.length-1] == fec[fec.length-2]){
			fec.splice(fec.length-1,1);
			hum.splice(fec.length-1,1);
			tem.splice(fec.length-1,1);
			vie.splice(fec.length-1,1);
			fwi.splice(fec.length-1,1);
			pre.splice(fec.length-1,1);
		}else{ // Elimina el primer dato de los vectores
			fec.splice(0,1);
			hum.splice(0,1);
			tem.splice(0,1);
			vie.splice(0,1);
			fwi.splice(0,1);
			pre.splice(0,1);
		}

	}

	// Carga de los datos

	// Seccion repetida
	document.querySelector("#ubicacion").innerHTML = datos[0].ubicacion
	document.querySelector("#fecha").innerHTML = datos[0].fecha.split(" ")[0] + "&nbsp";
	document.querySelector("#hora").innerHTML = "&nbsp"+ datos[0].fecha.split(" ")[1];

	// Seccion de Temperatura
	document.querySelector("#temp").innerHTML = datos[0].temperatura.split(".")[0] + "°C";

	temp__val__int.innerHTML = datos[0].temperatura.split(".")[0]
	temp__val__dec.innerHTML = "."+datos[0].temperatura.split(".")[1]


	document.querySelector("#temp-max").innerHTML = datos[0].tempmax + "°C"
	document.querySelector("#temp-min").innerHTML = datos[0].tempmin + "°C"

	sens__val__int.innerHTML = datos[0].sensacion.split(".")[0]
	sens__val__dec.innerHTML = "."+datos[0].sensacion.split(".")[1]

	document.querySelector("#sens-max").innerHTML = datos[0].sensamax + "°C"
	document.querySelector("#sens-min").innerHTML = datos[0].sensamin + "°C"

	// Seccion de Fuego
	document.querySelector("#fuego").innerHTML = fireDanger(datos[0].fwi)

	document.querySelector("#ffmc").innerHTML = datos[0].ffmc  
	document.querySelector("#dmc").innerHTML = datos[0].dmc  
	document.querySelector("#dc").innerHTML = datos[0].dc  
	document.querySelector("#isi").innerHTML = datos[0].isi 
	document.querySelector("#bui").innerHTML = datos[0].bui  
	document.querySelector("#fwi").innerHTML = datos[0].fwi 

	// Seccion humedad
	// Boton
	humedad.innerHTML = datos[0].humedad.split(".")[0] + "%";
	//document.querySelector("humedad-dec").innerHTML = "." + datos[0].humedad.split(".")[01;

	// display
	humedad__val__int.innerHTML = datos[0].humedad.split(".")[0]
	humedad__val__dec.innerHTML = "." + datos[0].humedad.split(".")[1]

	// Seccion viento
	// Boton
	viento.innerHTML = datos[0].viento.split(".")[0] + "Km/H";
	viento__val__int.innerHTML = datos[0].viento.split(".")[0];
	viento__val__dec.innerHTML = "." + datos[0].viento.split(".")[1];
	// direccion del viento en visor
	viento__val__veleta.innerHTML = datos[0].veleta;

	viento__max.innerHTML = datos[0].maxviento.split(".")[0] + "Km/H";

	// Seccion de Presion

	// Boton
	presion.innerHTML = datos[0].presion.split(".")[0] + "hPa";

	// display
	presion__val__int.innerHTML = datos[0].presion.split(".")[0]
	presion__val__dec.innerHTML = "." + datos[0].presion.split(".")[1]

	procesarCharts();
}

function procesarCharts(){
	// Grafico

	let itemsGrafico = ""

	if(sectionVisible == "temperatura"){
		itemsGrafico =
				[{
					label: 'Temperatura',
					borderColor: '#ffbf69',
					data: tem
				}]	
	}else{
		if(sectionVisible == "humedad"){
				itemsGrafico = 
						[{
							label: 'Humedad',
							borderColor: '#00bbf9',
							data: hum
						}]
		}else{
			if(sectionVisible == "viento"){
				itemsGrafico = 
				[{
					label: 'Viento',
					borderColor: '#e0fbfc',
					data: vie
				}]
			}else{
				if(sectionVisible == "presion"){
					itemsGrafico = 
					[{
						label: 'Presion',
						borderColor: '#6ee55d',
						data: pre
					}]
				}else{
					if(sectionVisible == "fuego"){

					itemsGrafico = 
					[{
						label: 'FWI',
						borderColor: '#ec512b',
						data: fwi
					}]
					}
				}

			}

		}

	}

	// invocamos a la funcion que carga y actualiza los datos en el grafico
	renderCharts(datos[0].ubicacion, fec, itemsGrafico);
}

function renderCharts(estacion, fecha, itemsGrafico){

	// si el objeto grafico ya esta instanciado lo destruyo para que se vuelva a crear limpio
	if(myChart!=null){
        myChart.destroy();
    }

	const ctx= document.querySelector("#myChart").getContext("2d")

	myChart= new Chart(ctx, {
	type: "line",
	data:{
		labels: fecha,
		datasets: itemsGrafico
		},
		options: {
			scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero:true,
	                    fontColor: 'white'
	                },
	            }],
	         	xAxes: [{
	                ticks: {
	                    fontColor: 'white'
	                },
	            }]
	        } ,
			legend: {
				display: false,
				position: 'top',
				labels: {
					padding: 15,
					boxWidth: 40,
					fontFamily: 'system-ui',
					fontColor: 'white',
				}
			},
			tooltips: {
				backgroundColor: '#0584f6',
				titleFontSize: 20,
				xPadding: 20,
				yPadding: 20,
				mode: 'index', 
			},
			elements: {

				line: {
					borderWidth: 2,
					fill: false,
				},
				point: {
					radius: 6,
					borderWidth: 4,
					backgroundColor: 'white',
					hoverRadius: 8,
					hoverRadiusWidth: 4,	
				}
			},
			animation: {
				duration: 0
			},
			responsiveAnimationDuration: 0,
			responsive: true,
			maintainAspectRatio: false
		}
	})

	// actualiza el contenido del grafico si lo estamos usando sin destruir en cada ocasion
	//myChart.update()

}

// Incrementa las visitas en una estacion especifica
async function addVisitStation(){
	const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=" + chipid + "&mode=visit-station");
	const data = await response.json();

	return data;
}

// Hace la peticion asincrona al archivo php y recupera el Json con los datos
async function refreshDatos(cantfilas){

	const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid="+chipid+"&cant="+cantfilas);
	const data = await response.json();

	dataJsonActual = data;

	procesar(data);
}

function refreshPanelInfo(dato){
	let title__sub = document.getElementById('title-sub');
	datos.forEach((element) =>{
		if (element == dato){
			sectionVisible = element;
			console.log(sectionVisible);
			procesarCharts();
			document.getElementById(`panel-container-${element}`).setAttribute("style", "display: visible;");

			if (element == datos[0]) {
				title__sub.innerHTML = `<i class="material-symbols-outlined naranja">local_fire_department</i> FUEGO`;
			} else if (element == datos[1]){
				title__sub.innerHTML = `<i class="material-symbols-outlined celeste">water_drop</i> HUMEDAD`;
			} else if (element == datos[2]){
				title__sub.innerHTML = `<i class="material-symbols-outlined amarillo">device_thermostat</i> TEMPERATURA`;
			} else if (element == datos[3]) {
				title__sub.innerHTML = `<i class="material-symbols-outlined naranja verde">arrow_circle_down</i> PRESION`;
			} else {
				title__sub.innerHTML = `<i class="material-symbols-outlined">air</i> VIENTO`;
			}
		} else {
			document.getElementById(`panel-container-${element}`).setAttribute("style", "display: none;");
		}
	});
}

// Retorna el peligro de incendio con una frase
function fireDanger(fwi){
	let fwiFloat = parseFloat(fwi)

	if(fwiFloat>=50){
		return "EXTREMO"
	}else{
		if(fwiFloat>=38){
			return "MUY ALTO"
		}else{
			if(fwiFloat>=21.3){
				return "ALTO"
			}else{
				if(fwiFloat>=11.2){
					return "MODERADO"
				}else{
					if(fwiFloat>=5.2){
						return "BAJO"
					}else{
						return "MUY BAJO"
					}
				}
			}
		}
	}
}