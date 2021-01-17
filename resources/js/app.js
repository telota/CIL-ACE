// vendor
window.Vue = require('vue');
window.axios = require('axios');

// 3rd party
//import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/dist/vuetify.min.css';
import Vue from 'vue';
import Vuetify from 'vuetify';
import router from './global/router';
import store from './global/Store';
import VueProgressBar from 'vue-progressbar'
import AxiosAjaxDetct from './global/AxiosAjaxDetect';
import { mapState } from 'vuex';

// this is the vuetify theming options
Vue.use(Vuetify);

// this is the progress bar settings
Vue.use(VueProgressBar,{
    color: '#3f51b5',
    failedColor: '#b71c1c',
    thickness: '5px',
    transition: {
        speed: '0.2s',
        opacity: '0.6s',
        termination: 300
    },
    autoRevert: true,
    inverse: false
});


// global component registrations here
//Vue.component("sessioninfo",        () => import("./modules/sessioninfo.vue")); // Async Load (creates seperate File!!!)

Vue.component('sessioninfo',        require('./modules/sessioninfo.vue').default);
Vue.component('ace',                require('./pages/search.vue').default);


// Own global JS variables/functions
//import editor_format from './global/format';
//import handlers from './global/handlers';
import localization from './global/localization';

//Vue.use(editor_format);
//Vue.use(handlers);
Vue.use(localization);


const editor = new Vue({

    vuetify: new Vuetify({

        theme: {
            dark: false,
            themes: {
                light: {
                    app_bg:     '#eeeeee',
                    primary:    '#377686',
                    accent:     '#b51212',
                    secondary:  '#93bacb',
                    bar_prim:   '#e0e0e0',

                    invert:     '#111111',
                    marked:     '#666666',
                    imgbg:      '#606060',
                },
                dark: {
                    app_bg:     '#181818',
                    primary:    '#8a8a8a',
                    //secondary:  '#b0bec5',
                    accent:     '#8c9eff',

                    invert:     '#eeeeee',
                    marked:     '#666666',
                    imgbg:      '#606060',
                },
            },
        },

        icons: {
            iconfont: 'md'
        }
    }),

    el: '#editor',
    router,
    store,


    data () {
        return {
            loading: false,
            about: false,
            error: {
                active: false
            },
            snack: {
                color: null,
                message: null
            },
            child_dialog: {
                width: '75%',
                fullscreen: false
            },

            preferences: {
                show_filters: false,
            },
            language: 'en'
        }
    },

    mounted () {
        const self = this

        // progress bar top
        AxiosAjaxDetct.init(
            () => { self.$Progress.start() },
            () => { self.$Progress.finish() }
        );
    },

    created () {
        //this.drawer.active = this.$vuetify.breakpoint.mdAndUp;
    },

    computed: {
        getBreadcrumbs() {
            return store.getters.getBreadcrumbs
        },

        labels() {
            return this.$localization[this.language]
        },

        // Map VueX Store State
        ...mapState ({
            snackbarMessage:    state => state.snackbarMessage,
            snackbarColor:      state => state.snackbarColor,
            snackbarDuration:   state => state.snackbarDuration
        })
    },

    methods: {
        async InitializeSession (data) {
            // set language (and check if language is supported)
            this.language = Object.keys(this.$localization).includes(data.language) ? data.language : 'en'
        },

        /*async changePresets (key, value) {
            this.loading = true

            console.log('PRESETS: update \'' + key + '\' (' + value + ')')
            this.presets[key] = value

            if (key === 'color_theme') { this.$vuetify.theme.dark = this.presets.color_theme === 1 ? true : false }
            else if (key === 'language') { this.language = this.presets.language}

            const response = await this.DBI_INPUT_POST('dashboard', 'input', this.presets);
            if (response.success) { this.snackbar('Presets updated!', 'success'); }

            this.loading = false
        },

        logout () {
            axios.post('logout').then(() => { window.location.href = 'login' });
        },

        snackbar (message, state) {
            const self = this
            message = typeof message === 'string' ? message : (message[this.language] ? message[this.language] : message.en)
            self.snack = { color: state ? state : null, message: message }
            setTimeout (() => { self.snack = { color: null, message: null }}, 4000)
        },*/

        label (string) {
            if (string) {
                const response = []
                string.split(',').forEach((value) => {
                    if (this.labels[value]) {
                        response.push(this.labels[value])
                    }
                    else {
                        response.push(value.slice(0, 1).toUpperCase() + value.slice(1).replaceAll('_', ' '))
                    }
                })
                return response.join(" ")
            }
            else {
                return 'NONE'
            }
        },

        openInNewTab (link) {
            if (link) { window.open(link) }
        },

        // JK: DBI-API-AXIOS Functions ----------------------------------------------------------------------------------
        async DBI_SELECT_GET (entity, id) {
            if (entity) {
                const self = this
                const source = 'dbi/' + entity + (id ? ('/' + id) : '')
                let dbi = {}

                console.log ('AXIOS: Fetching Data from "' + source + '" using GET. Awaiting Server Response ...');

                await axios.get(source)
                    .then((response) => {
                        dbi = response.data
                        console.log('AXIOS: ' + (dbi.contents?.[0] ? ((dbi.contents?.[0].id ? dbi.contents?.length : 0) + ' items') : 'data') + ' received.')
                        console.log(response)
                    })
                    .catch((error) => {
                        self.AXIOS_ERROR_HANDLER(error)
                    })

                return dbi
            }
        },

        /*async DBI_SELECT_POST (entity, params, search) {
            if (entity) {
                const self = this
                const source = 'dbi/' + entity
                let dbi = {}

                console.log ('AXIOS: Fetching Data from "' + source + '" using POST. Awaiting Server Response ...');

                if (search) {
                    for (const[key, value] of Object.entries(search)) {
                        params[key] = value
                    }
                }

                await axios.post(source, Object.assign ({}, params))
                    .then((response) => {
                        dbi = response.data
                        console.log ('AXIOS: ' + (dbi.contents?.[0] ? ((dbi.contents?.[0].id ? dbi.contents?.length : 0) + ' items') : 'data') + ' received.')
                        console.log (response)
                    })
                    .catch((error) => {
                        self.AXIOS_ERROR_HANDLER (error)
                    })

                return dbi
            }
        },

        async DBI_INPUT_POST (entity, mode, item) {
            if (entity && ['input', 'delete', 'connect'].includes(mode)) {
                const self = this
                let dbi = {}
                const is_ok = entity === 'dashboard' ? true : this.InputPermissionCheck(item)

                if (is_ok) {
                    this.$root.loading = true
                    const url = 'dbi/' + entity + '/' + mode

                    console.log ('AXIOS: Sending Data to "' + url + '" using POST. Awaiting Server Response ...')

                    await axios.post(url, Object.assign ({}, item))
                        .then((response) => {
                            if (response.data.success) {
                                dbi = response.data
                                console.log('RESPONSE CHECK: Server accepted input as valid:')
                            }
                            else {
                                self.snackbar('Validation Issue!', 'error')
                                self.error = { active: true, validation: response.data?.[self.language] ? response.data[self.language] : response.data }
                                console.log('RESPONSE CHECK: Validation Issue: Server declined input as invalid:')
                            }
                            console.log(response);
                        })
                        .catch((error) => { self.AXIOS_ERROR_HANDLER(error) })

                    this.$root.loading = false
                }

                return dbi
            }
            else {
                alert('ERROR: wrong Parameters given.')
            }
        },*/

        AXIOS_ERROR_HANDLER (error) {
            this.snackbar('System Error!', 'error')
            console.log('RESPONSE CHECK: System Error:')
            console.log(error)

            this.error = {
                active:     true,
                validation: null,
                resource:   error.config   ? (error.config.url ? error.config.url : 'unknown') : 'unknown',
                exception:  error.response ? error.response.data.exception : (error.request ? error.request.data.exception : 'unknown'),
                message:    error.response ? error.response.data.message   : (error.request ? error.request.data.message   : (error.message ? error.message : '--') ),
                params:     error.config   ? (error.config.data ? error.config.data : 'none given') : 'none given'
            }
        },

        /*InputPermissionCheck (item) {
            const id_user = this.user?.id ? this.user.id : 0
            const rank_user = this.user?.level ? this.user.level : 10
            let rank_required = 12
            let message = '\nYou are not permitted to create or edit any object.\nPlease contact the team administration for more information.'

            // Calculate required minimum level
            if (item?.creator) {
                if (item.public === 1) {
                    console.log('Input Permission Check: item is already published => set required Level to 18.')
                    message = '\nYou are not permitted to edit an already published object.\nPlease contact an authorized team member if you consider an update necessary.'
                    rank_required = 18
                }
                else if (item.creator === id_user) {
                    console.log('Input Permission Check: current user is creator of selected not published item => set required level to 11.')
                    rank_required = 11
                }
                else {
                    message = '\nYou are not permitted to edit an object created by another user.\nPlease contact the creator or an authorized team member if you consider an update necessary.'
                    console.log('Input Permission Check: current user is not creator of selected not published item => set required level to 12.')
                }
            }
            else {
                console.log('Input Permission Check: item has no creator => set required Level to 11.')
                rank_required = 11
            }

            // Check reuqired level and user level
            if (rank_user >= rank_required) {
                console.log('Input Permission Check passed!')
                return true
            }
            else {
                console.log('Input Permission Check failed!')
                this.snackbar('Validation Issue!', 'error')
                this.error = { active: true, validation: message }
                return false
            }
        }*/
    }
});
