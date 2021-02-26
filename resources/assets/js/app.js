
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.$ = window.jQuery = require('jquery'); //Referencia o importar  jQuery

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('categoria', require('./components/Categoria.vue'));
Vue.component('articulo', require('./components/Articulo.vue'));
Vue.component('cliente', require('./components/Cliente.vue'));
Vue.component('proveedor', require('./components/Proveedor.vue'));
Vue.component('rol', require('./components/Rol.vue'));
Vue.component('user', require('./components/User.vue'));
Vue.component('ingreso', require('./components/Ingreso.vue'));
Vue.component('venta', require('./components/Venta.vue'));
Vue.component('dashboard', require('./components/Dashboard.vue'));
Vue.component('consultaingreso', require('./components/ConsultaIngreso.vue'));
Vue.component('consultaventa', require('./components/ConsultaVenta.vue'));
Vue.component('notification', require('./components/Notification.vue'));

const app = new Vue({
    el: '#app',
    data :{
        menu : 0,
        notifications: []
    },
    created(){
        let me = this;
        axios.post('notification/get').then(function(response) {
            //console.log(response.data);
            me.notifications=response.data; //guardar el responde en la variable local notifications
        }).catch(function(error){
            console.log(error);
        });

        //programar el canal de lado del cliente
        // en esta variable capturaremos el id del ususario que esta trabajando con el sistema a travez d euna etiqueta meta.
        var userId = $('meta[name="userId"]').attr('content');

        //Agregamos un "echo private" para escuchar los eventos de transmision con el metodo notification()
        Echo.private('App.User.' + userId).notification((notification) => {
            me.notifications.unshift(notification); //utilizamos unshift para que el evento sea la 1ra posicion del arreglo, mandandole parametro la notificacion
        });  
        

    }
});
