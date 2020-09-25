require('./bootstrap');


require('./bootstrap')
import TicketCheckout from './components/TicketCheckout.vue'
const app = new Vue({
    components: {
        TicketCheckout,
    },
})

app.$mount('#app')
