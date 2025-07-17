import axios from "axios";
import Alpine from "alpinejs";
import '../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables-all.js';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';


window.axios = axios;
window.TomSelect = TomSelect;
window.Chart = Chart; 

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.Swal = Swal;

window.Alpine = Alpine;

Alpine.start();

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

