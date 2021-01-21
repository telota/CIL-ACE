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
        <style>
            a {
                text-decoration: none;
            }

            .breadcrumbs li a {
                text-decoration: none;
            }

            #editor-body {
                background: url('/ace/img/background.jpg');
            }

            .loader {
                position: fixed;
                width: 100%;
                height: 100%;
                left: 0;
                top: 0;
            }

            .loader-half-transparent {
                background: rgba(220, 220, 220, 0.5);
                z-index: 501;
            }

            .loader-footer {
                display: block;
                position: absolute;
                top: 100%;
                left: 50%;
                width: 500px;
                height: 54px;
                margin: -100px 0 0 -250px;
                color: #222222;
                text-align: center;
                font-family: sans-serif;
                font-size: 12px;
                line-height: 1.5;
            }

            .loader-background {
                position: fixed;
                width: 500px;
                height: 500px;
                margin: -250px 0 0 -250px;
                top: 50%;
                left: 50%;
            }

            .loader-background-gradient {
                position: fixed;
                width: 500px;
                height: 500px;
                margin: -250px 0 0 -250px;
                top: 50%;
                left: 50%;
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#232323",endColorstr="#181818",GradientType=1);
            }

            .loader-text {
                display: block;
                position: absolute;
                top: 50%;
                left: 50%;
                width: 350px;
                height: 60px;
                margin: -30px 0 0 -175px;
                color: #222222;
                text-align: center;
                font-family: sans-serif;
                font-size: 60px;
            }

            .loader-spinner {
                display: block;
                position: relative;
                left: 50%;
                top: 50%;
                width: 350px;
                height: 350px;
                margin: -175px 0 0 -175px;
            }

            .loader-spinner {
                border: 8px solid transparent;
                border-top-color: #222222;
                border-bottom-color: #222222;
                border-radius: 50%;
                -webkit-animation: loader 3s linear infinite;
                -moz-animation: loader 3s linear infinite;
                -o-animation: loader 3s linear infinite;
                animation: loader 3s linear infinite;
            }

            @keyframes loader {
                0% {
                    -webkit-transform: rotate(0deg);
                    -ms-transform: rotate(0deg);
                    transform: rotate(0deg);
                }

                100% {
                    -webkit-transform: rotate(360deg);
                    -ms-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }
        </style>
        {{--<link rel="shortcut icon" href="favicon.ico">--}}
        {{--<link rel="icon" type="image/png" href="/favicon" sizes="96x96">--}}
        {{--<link rel="icon" type="image/png" href="favicon.png" sizes="96x96">--}}
        {{--<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">--}}
        {{--<link href="{{ asset('ace/css/extend.css') }}" rel="stylesheet">--}}
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
                        <v-row justify="center" align="center">
                            <v-col cols="12" sm="10">
                                <!-- ACE -->
                                <div
                                    class="title text-center pt-1"
                                    v-text="$vuetify.breakpoint.mdAndUp ? 'Archivum Corporis Electronicum' : 'ACE'"
                                    style="position: absolute; left: 0; right: 0;"
                                ></div>
                                <div class="d-flex justify-space-between" style="width: 100%;">
                                    <!-- CIL Logo -->
                                    <div>
                                        <a href="https://cil.bbaw.de" alt="CIL Homepage" target="_blank" style="z-index: 3">
                                            <v-img
                                                v-if="$vuetify.breakpoint.mdAndUp"
                                                src="/ace/img/cil-logo.png"
                                                max-height="45"
                                                max-width="250"
                                                contain
                                            ></v-img>
                                            <div v-else class="title accent--text pt-1" v-text="'CIL'"></div>
                                        </a>
                                    </div>
                                    <!-- BBAW Logo -->
                                    <div>
                                        <a href="https://www.bbaw.de" alte="BBAW Homepage" target="_blank">
                                            <v-img
                                                v-if="$vuetify.breakpoint.mdAndUp"
                                                src="/ace/img/bbaw-logo.svg"
                                                max-height="45"
                                                max-width="150"
                                                contain
                                            ></v-img>
                                            <div v-else class="title accent--text pr-8 pt-1" v-text="'BBAW'"></div>
                                        </a>
                                    </div>
                                </div>
                            </v-col>
                        </v-row>
                        <!-- Language -->
                        <div class="d-flex justify-end align-center" style="position: absolute; right: 5px; z-index: 3">
                            <v-btn
                                icon
                                class="title pa-2"
                                v-text="$root.language === 'de' ? 'EN' : 'DE'"
                                @click="$root.language = $root.language === 'de' ? 'en' : 'de'"
                            ></v-btn>
                        </div>
                    </v-app-bar>

<!-- Beta -->
<div class="title text-center red--text" style="width: 100%; z-index:20; position: absolute; top: 68px">
    <span>Diese Seite befindet sich noch im Aufbau. Bitte nutzen Sie die</span>
    <a href="http://cil-old.bbaw.de/dateien/datenbank.php">alten Seite.</a>
</div>


                    <!-- Routed Component -->
                    <v-main class="app_bg" style="background: url('/ace/img/background.jpg')">
                        <v-fade-transition>
                            <div class="pa-5">
                                <router-view></router-view>
                            </div>
                        </v-fade-transition>
                    </v-main>

                    <!-- Tracking Consent -->
                    <v-card
                        v-if="consent === null"
                        tile
                        style="position: fixed; bottom: 50px; right: 20px; width: 250px"
                    >
                        <v-card-text class="caption text-justify">
                            <span v-text="$root.label('consent_note')"></span>
                            <a
                                href="https://www.bbaw.de/datenschutz"
                                target="_blank"
                                class="font-weight-bold"
                                style="text-decoration: none"
                                v-text="$root.label('consent_declaration')"
                            ></a>.
                        </v-card-text>
                            <v-card-actions class="pt-0">
                            <v-spacer></v-spacer>
                            <v-btn small text @click="consent = false" v-text="$root.label('decline')" class="bar_prim--text"></v-btn>
                            <v-spacer></v-spacer>
                            <v-btn small text @click="consent = true" v-text="$root.label('accept')"></v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>


                    <!-- Footer -->
                    <v-footer app fixed class="primary d-flex justify-space-between white--text">
                        <div>
                            2020&ndash;{!! date('y') !!}&ensp;<a href="https://www.bbaw.de" target="_blank" class="white--text" v-text="$root.label('bbaw')"></a>
                        </div>
                        <div class="d-md-flex justify-end">
                            <div class="ml-5" style="cursor: pointer" v-text="$root.label('about_header')" @click="dialog.about = true"></div>
                            <div class="ml-5" style="cursor: pointer" v-text="$root.label('license_header')" @click="dialog.license = true"></div>
                            <div class="ml-5"><a href="https://www.bbaw.de/datenschutz" target="_blank" class="white--text" v-text="$root.label('consent_declaration')"></a></div>
                        </div>
                    </v-footer>

                </v-app>


                <!-- Legal Notice -->
                <v-dialog v-model="dialog.about" max-width="500">
                    <v-card tile>
                        <v-card-text>
                            <div class="pa-5 title d-flex justify-center" v-text="$root.label('about_header')"></div>
                            <div>
                                <p>
                                    <b v-text="$root.label('about_published_by')"></b><br />
                                    <a
                                        href="https://www.bbaw.de"
                                        target="_blank"
                                        v-text="$root.label('bbaw')"
                                    ></a><br />
                                    Jägerstraße 22/23<br />
                                    DE-10117 Berlin
                                </p><p>
                                    <b v-text="$root.label('about_represented_by')"></b><br />
                                    Professor Dr. Dr. h. c. mult. Christoph Markschies<br />
                                    Tel.: +49 30 20 37 06 45/-20, E-Mail: bbaw(at)bbaw.de
                                </p><p>
                                    <b v-text="$root.label('about_legal_status')"></b><br />
                                    <span v-text="$root.label('about_legal_entity')"></span>
                                </p><p>
                                    <b v-text="$root.label('about_vat')"></b><br />
                                    DE 167 449 058 (<span v-text="$root.label('about_vat_note')"></span>)
                                </p><p>
                                    <b v-text="$root.label('about_technical')"></b><br />
                                    Telota - IT/DH<br />
                                    <span v-text="$root.label('bbaw')"></span><br />
                                    Jägerstraße 22/23<br />
                                    DE-10117 Berlin
                                </p><p>
                                    <div class="primary--text" style="cursor: pointer" v-text="$root.label('license_note')" @click="dialog = { about: false, license: true }"></div>
                                </p>
                            </div>
                            <div class="mb-n3 d-flex justify-center"><v-btn text @click="dialog.about = false">Close</v-btn></div>
                        </v-card-text>
                    </v-card>
                </v-dialog>


                <!-- Copyright -->
                <v-dialog v-model="dialog.license" max-width="500">
                    <v-card tile>
                        <v-card-text>
                            <div class="pa-5 title d-flex justify-center" v-text="$root.label('license_header')"></div>
                            <div>
                                <p>
                                    <b v-text="$root.label('license_rd')"></b><br/>
                                    <a href="https://cil.bbaw.de/index.php?id=16" target="_blank" v-text="$root.label('imprints')"></a>:
                                    <a href="https://creativecommons.org/licenses/by/4.0/deed.de" target="_blank">CC&nbsp;BY&nbsp;4.0</a><br />
                                    <a href="https://cil.bbaw.de/index.php?id=18" target="_blank" v-text="$root.label('scheden')"></a>:
                                    <a href="https://creativecommons.org/licenses/by/4.0/deed.de" target="_blank">CC&nbsp;BY&nbsp;4.0</a><br />
                                    <a href="https://cil.bbaw.de/index.php?id=17" target="_blank" v-text="$root.label('fotos')"></a>:
                                    <span v-text="$root.label('license_right_ask')"></span>
                                </p>
                                <p>
                                    <b v-text="$root.label('license_sw')"></b><br />
                                    ACE App<br />
                                    <a href="https://www.apache.org/licenses/LICENSE-2.0.html" target="_blank">Apache&nbsp;Software, License Version 2.0</a><br />
                                    <span v-text="$root.label('license_available')"></span>
                                    <a href="https://github.com/" target="_blank">Github</a><br />
                                    <span v-text="$root.label('license_author')"></span>: Jan Köster
                                </p>
                            </div>
                            <div class="mb-n3 d-flex justify-center"><v-btn text @click="dialog.license=false">Close</v-btn></div>
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
        <script src="/ace/js/app.js"></script>

    </body>

</html>
