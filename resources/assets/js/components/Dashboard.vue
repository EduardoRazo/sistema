<template>
  <main class="main">
      <!-- BreadCrumb -->
      <ol class="breadcrumb">
          <li class="breadcrum-item"><a href="/">Escritorio</a></li>
      </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card-header">
                                    <h4>Ingresos</h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <canvas id="ingresos">

                                        </canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Compras en los ultimos meses.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card-header">
                                    <h4>Ventas</h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <canvas id="ventas">

                                        </canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Ventas en los ultimos meses.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

  </main>
</template>

<script>
    export default {
        data(){
            return{
                //var para grafico
                varIngreso: null,   //donde vamos a almacenar el valor del id donde vamos a mostrar el grafico
                charIngreso:null,   //la que va a crear el grafico char, alimentado por valores vinculados con el id del objeto canvas 
                ingresos: [],       //arreglo que contendra el listado de ingresos
                varTotalIngreso:[], //almacenamos el total de los ingresos de cada mes que mostraremos en grafico
                varMesIngreso:[],   //almacena los nombres de los meses que mostraremos en grafico

                varVenta: null,   //donde vamos a almacenar el valor del id donde vamos a mostrar el grafico
                charVenta:null,   //la que va a crear el grafico char, alimentado por valores vinculados con el id del objeto canvas 
                ventas: [],       //arreglo que contendra el listado de ingresos
                varTotalVenta:[], //almacenamos el total de los ingresos de cada mes que mostraremos en grafico
                varMesVenta:[],   //almacena los nombres de los meses que mostraremos en grafico

            }
        },
        methods : {
            getIngresos(){
                let me=this;
                var url= '/dashboard';
                axios.get(url).then(function (response){ //utilizamos una peticion ajax a las sig. url
                    var respuesta = response.data;
                    me.ingresos = respuesta.ingresos;
                    //cargamos los datos del chart
                    me.loadIngresos();
                })
                .catch(function (error){
                    console.log(error);
                });
            },

            getVentas(){
                let me=this;
                var url= '/dashboard';
                axios.get(url).then(function (response){ //utilizamos una peticion ajax a las sig. url
                    var respuesta = response.data;
                    me.ventas = respuesta.ventas;
                    //cargamos los datos del chart
                    me.loadVentas();
                })
                .catch(function (error){
                    console.log(error);
                });
            },

            loadIngresos(){
                let me=this;
                me.ingresos.map(function(x){ //recorrer el areglo ingresoso usando la funcion map()
                    me.varMesIngreso.push(x.mes); //almacenamos en el arreglo de la propiedad data: lo que tenemos en mes
                    me.varTotalIngreso.push(x.total); //almacenamos en el arreglo de la propiedad data: lo que tenemos en total
                });
                me.varIngreso=document.getElementById('ingresos').getContext('2d'); // establecemos donde se muestre el grafico con funcion document() indicando el id del canvas donde se muestre 

                me.charIngreso = new Chart(me.varIngreso, { //representamos lo que tenemos en ese arreglo 
                    type: 'bar',
                    data: {
                        labels: me.varMesIngreso, //aqui carga mediante el arreglo los meses
                        datasets: [{
                            label: 'Ingresos',
                            data: me.varTotalIngreso, //aqui carga mediante el arreglo los meses
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            },

            loadVentas(){
                let me=this;
                me.ventas.map(function(x){                                                         //recorrer el areglo ingresoso usando la funcion map()
                    me.varMesVenta.push(x.mes);                                                    //almacenamos en el arreglo de la propiedad data: lo que tenemos en mes
                    me.varTotalVenta.push(x.total);                                                //almacenamos en el arreglo de la propiedad data: lo que tenemos en total
                });
                me.varVenta=document.getElementById('ventas').getContext('2d');                    // establecemos donde se muestre el grafico con funcion document() indicando el id del canvas donde se muestre 

                me.charVenta = new Chart(me.varVenta, {                                            //representamos lo que tenemos en ese arreglo 
                    type: 'bar',
                    data: {
                        labels: me.varMesVenta,                                                    //aqui carga mediante el arreglo los meses
                        datasets: [{
                            label: 'Ventas',
                            data: me.varTotalVenta,                                                //aqui carga mediante el arreglo los meses
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            }
        },
        mounted(){
            this.getIngresos();
            this.getVentas();
        }
}
</script>

