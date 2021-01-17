<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CIL | ACE</title>

        <!-- Styles -->
        {{--<link rel="shortcut icon" href="favicon.ico">--}}
        <link rel="icon" type="image/png" href="favicon" sizes="96x96">
        {{--<link rel="icon" type="image/png" href="favicon.png" sizes="96x96">--}}
        {{--<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">--}}
        <link href="{{ asset('css/extend.css') }}" rel="stylesheet">
        {{--<link href="{{ asset('css/editor.css') }}" rel="stylesheet">--}}
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">

        <!-- App JS -->
        <script type="application/javascript">
            var LSK_APP = {};
            LSK_APP.APP_URL = '{{env('APP_URL')}}';
        </script>

    </head>


    <body id="editor-body">

        <!-- EDITOR -- ------------------------------------------ ------------------------------------------ ------------------------------------------>
        <div id="editor">

            <!-- Session -->
            <sessioninfo :data='{!! json_encode($app) !!}'></sessioninfo>

            <!-- Initial Loading Screen -->
            <div class="loader">
                <div class="loader-background-gradient">
                    <div class="loader-text"><b>ACE</b></div>
                    <div class="loader-spinner"></div>
                </div>
                <div class="loader-footer">
                    <p>©&ensp;2020{!! date('y') > 20 ? ('&ndash;'.date('y')) : ('') !!}</p>
                    <p>CORPUS INSCRIPTIONUM LATINARUM&ensp;|&ensp;TELOTA IT/DH<br />Berlin-Brandenburg Academy of Sciences and Humanities</p>
                </div>
            </div>


            <!-- Vue : Start ------------------------------------------ ------------------------------------------ -->
            <template>
                <v-app id="inspire">

                    <!-- Appbar -->
                    <v-app-bar
                        app
                        fixed
                        style="border-bottom: 4px solid #b51212; background-color: #fefefe"
                    >
                        <v-row>
                            <v-col cols=1></v-col>
                            <v-col cols=3>
                                <a href="https://cil.bbaw.de" alt="CIL Homepage" target="_blank">
                                    <v-img
                                    src="/cil-logo.png"
                                    max-height="45"
                                    max-width="250"
                                    contain
                                    ></v-img>
                                </a>
                            </v-col>
                            <v-col cols=4>
                            </v-col>
                            <v-col cols=3>
                                <div class="d-flex mr-n5">
                                    <v-spacer></v-spacer>
                                    <a href="https://www.bbaw.de" alte="BBAW Homepage" target="_blank">
                                        <v-img
                                            src="/bbaw-logo.svg"
                                            max-height="45"
                                            max-width="150"
                                            contain
                                        ></v-img>
                                    </a>
                                </div>
                            </v-col>
                            <v-col cols=1>
                                <div class="d-flex justify-end align-center">
                                    <v-card
                                        tile
                                        flat
                                        class="title pa-2"
                                        v-text="$root.language === 'de' ? 'EN' : 'DE'"
                                        @click="$root.language = $root.language === 'de' ? 'en' : 'de'"
                                    ></v-card>
                                </div>
                            </v-col>
                        </v-row>

                        <!-- Left Section
                        <advbtn icon="menu" tooltip="Togle Navigation" v-on:click="drawer.active = !drawer.active" class="ml-n3"></advbtn>
                        <advbtn
                            v-if="drawer.active && $vuetify.breakpoint.mdAndUp"
                            :icon="drawer.mini ? 'keyboard_arrow_right' : 'keyboard_arrow_left'"
                            :tooltip="drawer.mini ? 'Expand Navigation' : 'Collapse Navigation'"
                            :key="drawer.mini ? 'collapsed' : 'expanded'"
                            v-on:click="drawer.mini = !drawer.mini"
                        ></advbtn>
                        <v-toolbar-title class="ml-3">CN <span class="font-weight-thin">Editor</span></v-toolbar-title>

                        <v-spacer></v-spacer> -->

                        <!-- Right Section
                        <advbtn icon="help_outline" tooltip="Wiki" v-on:click=""></advbtn>
                        <advbtn
                            :icon="$vuetify.theme.dark ? 'invert_colors' : 'invert_colors_off'"
                            :tooltip="$vuetify.theme.dark ? 'Switch to light Theme' : 'Switch to dark Theme'"
                            :key="$vuetify.theme.dark ? 'dark' : 'light'"
                            v-on:click="changePresets('color_theme', $vuetify.theme.dark === true ? 0 : 1)"
                        ></advbtn>
                        <advbtn
                            :text="language.toUpperCase()"
                            :tooltip="language === 'de' ? 'Switch to English' : 'Zu Deutsch wechseln'"
                            :key="language"
                            v-on:click="changePresets('language', language === 'de' ? 'en' : 'de')"
                        ></advbtn>
                        <v-divider vertical></v-divider>
                        <advbtn icon="power_settings_new" tooltip="Logout" v-on:click="logout()" class="mr-n4"></advbtn>  -->

                    </v-app-bar>


                    <!-- Routed Component -->
                    <v-main class="app_bg" style="background: url('/background.jpg')">
                        <v-fade-transition>
                            <div class="pa-5">
                                <router-view></router-view>
                            </div>
                        </v-fade-transition>
                    </v-main>


                    <!-- Footer -->
                    <v-footer app fixed padless class="pr-5 pl-5 pt-3 pb-3">
                        <v-row>

                            <v-col cols="12" sm="12" md="4" lg="4" :class="'pa-0 ma-0'+($vuetify.breakpoint.mdAndUp ? '' : ' text-center')">
                            </v-col>

                            <v-col cols="12" sm="12" md="4" lg="4" class="pa-0 ma-0 text-center">
                                <button @click="about = true">
                                    <span class="font-weight-thin">© 2020{!! date('y') > 20 ? ('&ndash;'.date('y')) : ('') !!}&ensp;TELOTA&nbsp;-&nbsp;IT/DH</span>
                                </button>
                            </v-col>

                            <v-col cols="12" sm="12" md="4" lg="4" :class="'pa-0 ma-0 '+($vuetify.breakpoint.mdAndUp ? 'text-right' : 'text-center')">
                            </v-col>

                        </v-row>
                    </v-footer>

                </v-app>


                <!-- About -->
                <v-dialog v-model="about" max-width="500">
                    <v-card tile>
                        <v-card-text>
                            <div class="pa-5 title d-flex justify-center">ACE App</div>
                            <div>
                                <p>
                                    <b>Published by:</b><br />
                                    Berlin-Brandenburg Academy of Sciences and Humanities<br />
                                    Jägerstraße 22/23<br />
                                    10117 Berlin
                                </p><p>
                                    <b>Represented by:</b><br />
                                    Professor Dr. Dr. h. c. mult. Christoph Markschies<br />
                                    Tel.: +49 30 20 37 06 45/-20, E-Mail: bbaw@bbaw.de
                                </p><p>
                                    <b>Legal status:</b><br />
                                    public law legal entity (Rechtsfähige Körperschaft öffentlichen Rechts)
                                </p><p>
                                    <b>VAT Identification Nr.:</b><br />
                                    DE 167 449 058 (according to §27 a of the Value Added Tax Law of the Federal Republic of Germany)
                                </p><p>
                                    <b>Technical realisation:</b><br />
                                    Jan Köster (jan.koester(at)bbaw.de)<br />
                                    Telota - IT/DH<br />
                                    Berlin-Brandenburg Academy of Sciences and Humanities<br />
                                    Jägerstraße 22/23<br />
                                    10117 Berlin<br /><br />
                                    © 2020{!! date('y') > 20 ? ('&ndash;'.date('y')) : ('')!!}
                                </p>
                            </div>
                            <div class="mb-n3 d-flex justify-center"><v-btn text @click="about=false">Close</v-btn></div>
                        </v-card-text>
                    </v-card>
                </v-dialog>


                <!-- Loading Screen ------------------------------------------ -->
                <div v-if="loading" class="loader loader-half-transparent">
                    <div class="loader-background">
                        <div class="loader-text"><b>ACE</b></div>
                        <div class="loader-spinner"></div>
                    </div>
                </div>

                <!-- Error Dialog -->
                <Error-Dialog v-if="error.active" :error="error" v-on:close="error = {active: false}"></Error-Dialog>

                <!-- Progress Bar -->
                <vue-progress-bar></vue-progress-bar>

            </template>
            <!-- Vue : END ------------------------------------------ ------------------------------------------ -->


        </div>
        <!-- ------------------------------------------ ------------------------------------------ ------------------------------------------>

        <!-- Scripts -->
        {{-- <script src="{{ asset('js/manifest.js') }}"></script> --}}
        {{-- <script src="{{ asset('js/vendor.js') }}"></script> --}}
        <script src="{{ asset('js/app.js') }}"></script>

    </body>

</html>
