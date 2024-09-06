let chipid = "";
// Vectores de...
let fec = []; // fecha
let tem = []; // temperatura
let hum = []; // humedad
let vie = []; // viento
let fwi = []; // fuego
let pre = []; // presion

const MAX_DATOS = 7;
const INTERVAL_REFRESH = 60000;

let dataJsonActual = ""


let btnControls = [["temperatura", '<i class="material-icons-outlined">thermostat</i>'],
				 ["fuego", '<i class="fas fa-fire color-fuego"></i>'],
				 ["humedad", '<i class="fas fa-tint color-humedad"></i>'],
				 ["viento",'<i class="fas fa-wind color-viento"></i>' ],
				 ["presion",'<i class="fas fa-arrow-circle-down color-presion"></i>']]


let sectionVisible = ""


let myChart = null

document.addEventListener("DOMContentLoaded", function(event){
	// chipid
	chipid = document.querySelector("#chipid").innerHTML;
	console.log("Web Cargada para el chipid: " + chipid);

	
	addVisitStation();

	
	refreshDatos(MAX_DATOS);

	
	refreshId = setInterval(refreshDatos, INTERVAL_REFRESH, 1);
	console.log("Refresco cada "+INTERVAL_REFRESH+" seg.");

	
	btnControls.forEach(function(btn, i){			
		if(btn[0]=="temperatura"){
			sectionVisible = btn[0]
			document.querySelector("#title-sub").innerHTML = btn[1] + '&nbsp' + btn[0].toUpperCase()
			document.querySelector("#panel-container-" + btn[0]).setAttribute("style", "display: grid;")
			
		}else{
			document.querySelector("#panel-container-" + btn[0]).setAttribute("style", "display: none;")
			
		}
	})
	

	btnControls.forEach(function(element, index){
		document.querySelector( "#btn-" + element[0] ).addEventListener("click", event => {

			event.preventDefault()

			document.querySelector("#title-sub").innerHTML = element[1] + '&nbsp' + element[0].toUpperCase()

			if(element[0] != "temperatura" && element[0] != "viento"){
				document.querySelector("#title-sub").setAttribute("style", "display: visible;")
			}else{
				document.querySelector("#title-sub").setAttribute("style", "display: none;")
			}

		
			btnControls.forEach(function(btn, i){
				
				if(btn[0]==element[0]){
					sectionVisible = btn[0]
					document.querySelector("#panel-container-" + btn[0]).setAttribute("style", "display: grid;")

				}else{
					document.querySelector("#panel-container-" + btn[0]).setAttribute("style", "display: none;")
			
				}
			})

			console.log(sectionVisible)

			procesar(dataJsonActual, false)
		})
	})
});



async function addVisitStation(){
	const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=" + chipid + "&mode=visit-station")
	const data = await response.json()

	return data
}


async function refreshDatos(cantfilas){
	const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=" + chipid + "&cant=" + cantfilas)
	const data = await response.json()

	dataJsonActual = data

	procesar(data)
}


function procesar(datos, addData = true){

	let hora = ""

	console.log("Filas Json: " + datos.length);

	if(addData == true){
		
		for (let i = datos.length-1; i >= 0; i--) {

			console.log("Vigia Json [" + i + "]" + datos[i].fecha);

			hora = datos[i].fecha.split(" ")[1]

			
			fec.push(hora.split(":")[0]+":"+hora.split(":")[1]);
			tem.push(datos[i].temperatura);
			hum.push(datos[i].humedad);
			vie.push(datos[i].viento);
			fwi.push(datos[i].fwi);
			pre.push(datos[i].presion);
		}

		
		if(fec[fec.length-1] == fec[fec.length-2]){
			fec.splice(fec.length-1,1);
			hum.splice(fec.length-1,1);
			tem.splice(fec.length-1,1);
			vie.splice(fec.length-1,1);
			fwi.splice(fec.length-1,1);
			pre.splice(fec.length-1,1);
		}else{ 
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

	// Temperatura
	
	document.querySelector("#temp").innerHTML = datos[0].temperatura.split(".")[0] + "ºC";

	document.querySelector("#temp-val-int").innerHTML = datos[0].temperatura.split(".")[0]
	document.querySelector("#temp-val-dec").innerHTML = "."+datos[0].temperatura.split(".")[1]

	
	document.querySelector("#temp-max").innerHTML = datos[0].tempmax + "ºC"
	document.querySelector("#temp-min").innerHTML = datos[0].tempmin + "ºC"

	document.querySelector("#sens-val-int").innerHTML = datos[0].sensacion.split(".")[0]
	document.querySelector("#sens-val-dec").innerHTML = "."+datos[0].sensacion.split(".")[1]

	document.querySelector("#sen-max").innerHTML = datos[0].sensamax + "ºC"
	document.querySelector("#sen-min").innerHTML = datos[0].sensamin + "ºC"

	// Fuego
	// 
	document.querySelector("#fuego").innerHTML = fireDanger(datos[0].fwi)

	document.querySelector("#ffmc").innerHTML = datos[0].ffmc  
	document.querySelector("#dmc").innerHTML = datos[0].dmc  
	document.querySelector("#dc").innerHTML = datos[0].dc  
	document.querySelector("#isi").innerHTML = datos[0].isi 
	document.querySelector("#bui").innerHTML = datos[0].bui  
	document.querySelector("#fwi").innerHTML = datos[0].fwi 

	// Seccion humedad
	// Botón
	humedad.innerHTML = datos[0].humedad.split(".")[0] + "%";
	//document.querySelector("humedad-dec").innerHTML = "." + datos[0].humedad.split(".")[01;

	// display
	humedad__val__int.innerHTML = datos[0].humedad.split(".")[0]
	humedad__val__dec.innerHTML = "." + datos[0].humedad.split(".")[1]

	// viento

	// Botón
	viento.innerHTML = datos[0].viento.split(".")[0] + "Km/H";
	//document.querySelector("viento-dec").innerHTML = "." +  datos[0].viento.split(".")[1];
	viento__val__int.innerHTML = datos[0].viento.split(".")[0];
	viento__val__dec.innerHTML = "." + datos[0].viento.split(".")[1];
	// dirección del viento en visor
	viento__val__veleta.innerHTML = datos[0].veleta;

	// botón dirección del viento
	viento__direccion.innerHTML = datos[0].veleta

	viento__max.innerHTML = datos[0].maxviento.split(".")[0] + "Km/H";

	// Seccion de Presión
	

	// Botón
	presion.innerHTML = datos[0].presion.split(".")[0] + "hPa";

	// display
	presion__val__int.innerHTML = datos[0].presion.split(".")[0]
	presion__val__dec.innerHTML = "." + datos[0].presion.split(".")[1]

	// Gráfico


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
				


	renderCharts(datos[0].ubicacion, fec, itemsGrafico);
}



// renderizamos el gráfico


function renderCharts(estacion, fecha, itemsGrafico){

	// Destruir el gráfico actual si existe
    if (myChart) {
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

}

// Retorna el peligro de incendio con una frase

function fireDanger(fwi){
	let fwiFloat = parseFloat(fwi)
	
	if(fwiFloat>=50){
		return "Extremo"
	}else{
		if(fwiFloat>=38){
			return "Muy alto"
		}else{
			if(fwiFloat>=21.3){
				return "Alto"
			}else{
				if(fwiFloat>=11.2){
					return "Moderado"
				}else{
					if(fwiFloat>=5.2){
						return "Bajo"
					}else{
						return "Muy bajo"
					}
				}
			}
		}
	}

}