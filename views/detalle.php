
<?php

	if (isset($_GET["chipid"])){
		$chipid = $_GET["chipid"];	
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Panel</title>
	<link rel="stylesheet" href="../static/css/app.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" 
/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js" defer></script>
	<script type="text/javascript" src="https://mattprofe.com.ar/alumno/6846/app-estacion/static/js/detalle.js" ></script>
</head>
<body>
	<div id="chipid" style="display:none;"><?php echo $chipid ?></div>
	<div class="container_detalle">
	<header>
		<nav id="opciones">
			<button id="btn__temperatura">
				<i class="material-symbols-outlined amarillo">device_thermostat</i> 
				<div id="temp">33°C</div>
			</button>
			<button id="btn__humedad">
				<i class="material-symbols-outlined celeste">water_drop</i>
				<div id="humedad">99%</div> 
			</button>
			<button id="btn__presion">
				<i class="material-symbols-outlined naranja verde">arrow_circle_down</i>
				<div id="presion">888hPa</div> 
			</button>
			<button id="btn__viento">
				<i class="material-symbols-outlined">air</i>
				<div id="viento">45Km/H</div>
			</button>
			<button id="btn__fuego">
				<i class="material-symbols-outlined naranja">local_fire_department</i>
				<div id="fuego">ALTO</div>
			</button>
		</nav>
	</header>
	<div id="contenido">
		<div id="panel-container">
			<div id="panel-title">
				<div class="title-info centrar">
					<h3 id="fecha">2024-10-25</h3>
					<h3 id="hora">16:20:07</h3>	
				</div>
				<div class="title-info centrar">
					<i class="material-symbols-outlined rojo">location_on</i><h2 id="ubicacion"></h2>
				</div>
				<h4 id="title-sub centrar"><i class="material-symbols-outlined ">device_thermostat</i>TEMPERATURA</h4>
			</div>
			<div id="panel-container-temperatura" style="display: visible;">
				<div class="panel-col">
					<div class="col-items">
						<div class="item-title">
							<i class="material-symbols-outlined ">device_thermostat</i>TEMPERATURA
						</div>
					</div>
					<div class="col-items">
						<div class="col-important">
							<div id="temp__val__int" class="important-val-int">23</div>
							<div class="important-detail">
								<div class="important-val-unit">
									°C
								</div>
								<div id="temp__val__dec" class="important-val-dec">
									.30
								</div>
							</div>
						</div>

						<div class="panel-row">
							<div class="item">
								<div class="item-title"><i class="material-symbols-outlined rojo">arrow_drop_up</i>Máxima</div>
								<div id="temp-max">25.40ºC</div>
							</div>
							<div class="item">
								<div class="item-title"><i class="material-symbols-outlined verde">arrow_drop_down</i>Mínima</div>
								<div id="temp-min">25.40ºC</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-col">
					<div class="col-items">
						<div class="item-title">
							<i class="material-symbols-outlined ">emoji_people</i>SENSACIÓN
						</div>
					</div>
					<div class="col-items">
						<div class="col-important">
							<div id="sens__val__int" class="important-val-int">23</div>
							<div class="important-detail">
								<div class="important-val-unit">
									°C
								</div>
								<div id="sens__val__dec" class="important-val-dec">
									.30
								</div>
							</div>
						</div>

						<div class="panel-row">
							<div class="item">
								<div class="item-title"><i class="material-symbols-outlined ">arrow_drop_up</i>Máxima</div>
								<div id="sens-max">25.40ºC</div>
							</div>
							<div class="item">
								<div class="item-title"><i class="material-symbols-outlined ">arrow_drop_down</i>Mínima</div>
								<div id="sens-min">25.40ºC</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="panel-container-humedad" style="display: none;">
				<div class="col-items">
					<div class="col-important">
						<div id="humedad__val__int" class="important-val-int">23</div>
						<div class="important-detail">
							<div class="important-val-unit">
								%
							</div>
							<div id="humedad__val__dec" class="important-val-dec">
								.30
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="panel-container-presion" style="display: none">
				<div class="col-items">
					<div class="col-important">
						<div id="presion__val__int" class="important-val-int">887</div>
						<div class="important-detail">
							<div class="important-val-unit">
								hPa
							</div>
							<div id="presion__val__dec" class="important-val-dec">
								.76
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="panel-container-viento" style="display: none">
				<div class="panel-col">
					<div class="col-items">
						<div class="item-title">
							<i class="material-symbols-outlined">air</i>VELOCIDAD
						</div>
					</div>
					<div class="col-items">
						<div class="col-important">
							<div id="viento__val__int" class="important-val-int">8</div>
							<div class="important-detail">
								<div class="important-val-unit">
									Km/H
								</div>
								<div id="viento__val__dec" class="important-val-dec">
									.50
								</div>
							</div>
						</div>

						<div class="panel-row">
							<div class="item">
								<div class="item-title"><i class="material-symbols-outlined ">arrow_drop_up</i>Máximo</div>
								<div id="viento__max">25.40Km/H</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-col">
					<div class="panel-row">
							<div class="item">
								<div id="item-viento">
									<i class="material-symbols-outlined ">explore</i>
								<div id="viento__val__veleta">
									ESTE
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="panel-container-fuego" style="display: none">
				<div class="panel-col">
					<div class="col-items">
						<div class="item">
							<div class="item-title">
								FFMC
							</div>
							<div id="ffmc" class="item-value">
								81.2696
							</div>
							<div class="item-title">
								DMC
							</div>
							<div id="dmc" class="item-value">
								81.2696
							</div>
							<div class="item-title">
								DC
							</div>
							<div id="dc" class="item-value">
								81.2696
							</div>
						</div>	
					</div>
				</div>
				<div class="panel-col">
					<div class="col-items">
						<div class="item">
							<div class="item-title">
								ISI
							</div>
							<div id="isi" class="item-value">
								81.2696
							</div>
							<div class="item-title">
								BUI
							</div>
							<div id="bui" class="item-value">
								81.2696
							</div>
							<div class="item-title">
								FWI
							</div>
							<div id="fwi" class="item-value">
								81.2696
							</div>
						</div>	
					</div>
				</div>
			</div>
		</div>
		<div id="grafico">
			<div id="grafico__container" class="chartjs-render-monitor">
				<canvas id="myChart" class="border-debug chartjs-render-monitor" style="background-color: rgba(0, 0, 0, 0); display: block;"></canvas>
			</div>
		</div>
	</div>
	</div>
</body>
</html>