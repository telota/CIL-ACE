<template>
  <div>

  <!-- Query Form -->
    <v-row justify="center" align="center">
      <v-col cols="12" sm="10">

        <v-card tile raised class="mt-4">
          <!-- Header Bar  ----------------------------------------------------- ----------------------------------------------------- -->
          <v-card
            tile
            class="bar_prim"
            :flat="filterExpanded"
            :disabled="!items [0]"
            @click="filterExpanded = !filterExpanded"
          >
            <v-card-text class="d-flex component-wrap">
              <div style="width: 20%">
                &ensp;
              </div>
              <div class="headline d-flex justify-center black--text" style="width: 60%; font-variant:small-caps;">
                Archivum Corporis Electronicum
              </div>
              <div style="width: 20%" class="d-flex justify-end">
                <v-icon v-text="filterExpanded ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"></v-icon>
              </div>
            </v-card-text>
          </v-card>

          <!-- Filter Container  ----------------------------------------------------- ----------------------------------------------------- -->
          <v-expand-transition>
            <v-card-text v-if="filterExpanded">
              <div class="caption">
                Für die Suche nach anderen Editionen siehe die verbindlichen
                <b style="cursor: pointer" @click="abbreviations.active = true" v-text="'Abkürzungen'"></b>.
              </div>
              <v-row>
                <v-col cols="12" sm="5" xl="6" class="pt-0">
                  <v-text-field
                    id="search_name"
                    v-model="query.name"
                    :key="'A' + queryRefresh"
                    label="Inschrift"
                    clearable
                    v-on:keyup.enter="RunSearch()"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="7" xl="6" class="d-md-flex flex-wrap justify-space-between pt-0">
                  <v-checkbox
                    v-model="query.has_imprints"
                    :key="'B' + queryRefresh"
                    label="Abklatsche"
                    class="mr-5"
                  ></v-checkbox>
                  <v-checkbox
                    v-model="query.has_imprints_3d"
                    :key="'C' + queryRefresh"
                    label="3D-Abklatsche"
                    class="mr-5"
                  ></v-checkbox>
                  <v-checkbox
                    v-model="query.has_fotos"
                    :key="'D' + queryRefresh"
                    label="Fotos"
                    class="mr-5"
                  ></v-checkbox>
                  <v-checkbox
                    v-model="query.has_scheden"
                    :key="'E' + queryRefresh"
                    label="Scheden"
                  ></v-checkbox>
                </v-col>
              </v-row>

              <!-- Search Button ----------------------------------------------------- -->
              <v-row justify="center" align="center">
                <v-col cols="12" sm="4" class="mb-n4 mt-n2">
                  <v-btn large tile block color="primary" @click="RunSearch()">
                    <span class="title">Suche</span>
                  </v-btn>
                  <div class="pt-2 d-flex justify-center">
                    <v-btn text small @click="ResetFilters(true)">Suche zurücksetzen</v-btn>
                  </div>
                </v-col>
              </v-row>

            </v-card-text>
          </v-expand-transition>

          <!-- Result Count ----------------------------------------------------- -->
          <v-card
            tile
            flat
            :loading="loading"
            class="bar_prim"
          >
            <v-card-text
              class="text-center body-1"
              v-html="!items[0] ? (loading ? 'Ihre Anfrage wird ausgeführt ...' : no_result) : (!count_formated ? '' : (count_formated + ' Treffer'))"
            ></v-card-text>
          </v-card>
        </v-card>

        <!-- Result Container ----------------------------------------------------- ----------------------------------------------------- -->
        <v-expand-transition>
          <div v-if="count_formated != null" class="mt-5">

            <!-- Pagination -->
            <v-card
              tile
              class="bar_prim mb-5 d-flex component-wrap"
            >
              <v-spacer></v-spacer>
              <!-- First -->
              <v-btn
                :tile="pagination.previous ? true : false"
                :text="pagination.previous ? false : true"
                depressed
                :disabled="pagination.previous ? false : true"
                class="bar_prim"
                @click="Navigate(pagination.first)"
              >
                <v-icon v-text="'first_page'"></v-icon>
              </v-btn>
              <!-- Previous -->
              <v-btn
                :tile="pagination.previous ? true : false"
                :text="pagination.previous ? false : true"
                depressed
                :disabled="pagination.previous ? false : true"
                class="bar_prim"
                @click="Navigate(pagination.previous)"
              >
                <v-icon v-text="'navigate_before'"></v-icon>
              </v-btn>
              <!-- Page -->
              <v-btn
                text
                disabled
                v-text="pagination.page.current + ' / ' + pagination.page.total"
              ></v-btn>
              <!-- Next -->
              <v-btn
                :tile="pagination.next ? true : false"
                :text="pagination.next ? false : true"
                depressed
                :disabled="pagination.next ? false : true"
                class="bar_prim"
                @click="Navigate(pagination.next)"
              >
                <v-icon v-text="'navigate_next'"></v-icon>
              </v-btn>
              <!-- Last -->
              <v-btn
                :tile="pagination.next ? true : false"
                :text="pagination.next ? false : true"
                depressed
                :disabled="pagination.next ? false : true"
                class="bar_prim"
                @click="Navigate(pagination.last)"
              >
                <v-icon v-text="'last_page'"></v-icon>
              </v-btn>
              <v-spacer></v-spacer>
            </v-card>

            <!-- Results -->
            <v-card
              v-for="item in items"
              :key="item.id"
              tile
            >
              <!-- Item Header -->
              <v-card
                @click="ToggleItem(item.id, item.self)"
                tile
                flat
              >
                <v-card-title>
                  <div class="d-flex mb-n2" style="width: 100%">
                    <div v-html="item.name" class="ml-auto pt-1 font-weight-bold body-1" style="width: 100%"></div>
                    <div class="pl-5 mr-auto">
                      <v-icon v-text="itemsExpanded.includes(item.id) ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"></v-icon>
                    </div>
                  </div>
                </v-card-title>
              </v-card>
              <!-- Item Body -->
              <v-expand-transition>
                <div
                  v-if="itemsExpanded.includes(item.id)"
                  style="border-top: 2px solid #b51212; background-color: #fefefe"
                >
                  <v-card-text>
                    <div v-for="d in [itemsDetails[item.id]]" :key="d.id">
                      <!-- <div class="d-flex justify-space-between">
                        <div class="title mt-n2 mb-1" v-html="label('references_main')"></div>
                        <!- Clauss
                        <form
                          v-if="d.edcs"
                          id="edcs"
                          method="post"
                          :action="$store.state.settings.edcs"
                          target="_blank"
                        >
                          <input type="hidden" name="p_edcs_id" :value="d.edcs" />
                          <input type="hidden" name="s_sprache" value="de" />
                          <input type="hidden" name="r_auswahl" value="und" />
                          <input type="hidden" name="r_sortierung" value="Provinz" />
                          <input type="hidden" name="cmdsubmit" value="go" />
                          <span v-text="'Text und weitere Informationen bei'"></span>
                          <input type="submit" value="EDCS" class="font-weight-bold"/>
                        </form>
                      </div>
                      <!- References
                      <div class="pl-3">
                        <table>
                          <tr
                            v-for="(ref, r) in d.name_object"
                            :key="r"
                          >
                            <td
                              class="font-weight-bold"
                              v-html="ref.ref_short"
                            ></td>
                            <td
                              class="pl-3"
                              v-html="ref.ref_full.replace('u00b2', '²')"
                            ></td>
                          </tr>
                      </table>
                      </div>-->
                      <!-- Header -->
                      <div class="body-1 pb-2">
                        <div class="d-flex flex-wrap">
                          <div
                            v-for="(ref, r) in d.name_object"
                            :key="r"
                            class="d-flex flex-nowrap"
                          >
                            <div v-if="ref.delimiter" v-html="'&nbsp;' + ref.delimiter + '&nbsp;'"></div>
                            <div v-text="ref.clamped.start"></div>
                            <v-tooltip bottom>
                              <template v-slot:activator="{on}">
                                <v-hover v-slot="{ hover }">
                                  <div
                                    v-on="on"
                                    v-html="ref.ref_short"
                                    :class="hover ? 'accent--text' : ''"
                                  ></div>
                                </v-hover>
                              </template>
                              <span v-html="ref.ref_full.replace('u00b2', '²')"></span>
                            </v-tooltip>
                            <div v-text="ref.clamped.end"></div>
                          </div>
                        </div>
                      </div>
                      <!-- Clauss -->
                      <form
                        v-if="d.edcs"
                        id="edcs"
                        method="post"
                        action="http://db.edcs.eu/epigr/epi_ergebnis.php"
                        target="_blank"
                      >
                        <input type="hidden" name="p_edcs_id" :value="d.edcs" />
                        <input type="hidden" name="s_sprache" value="de" />
                        <input type="hidden" name="r_auswahl" value="und" />
                        <input type="hidden" name="r_sortierung" value="Provinz" />
                        <input type="hidden" name="cmdsubmit" value="go" />
                        <span v-text="'Text und weitere Informationen bei'"></span>
                        <input type="submit" value="EDCS" class="font-weight-bold"/>
                      </form>
                      <!-- <div>Zitationslink: {{ CiteLink(d.concordance) }} <a :href="CiteLink(d.concordance)" style="text-decoration: none;">&#x1F5D7;</a></div> -->
                      <!-- Resources -->
                      <template v-for="record in ['imprints', 'fotos', 'scheden']">
                        <div :key="record" v-if="d[record]">
                          <!-- Header -->
                          <div class="pt-3 title d-flex align-start">
                            <div v-text="label(record)"></div>
                            <a :href="info[record]" target="_blank" v-html="'&nbsp;&#9432;'"></a>
                          </div>
                          <!-- Body -->
                          <v-row>
                            <v-col
                              v-for="r in d[record]"
                              :key="r.id"
                              cols="12"
                              sm="6"
                              md="3"
                              lg="2"
                            >
                              <!-- Tile -->
                              <v-card
                                tile
                                class="bar_prim pa-1"
                                style="position: relative"
                              >
                                <v-btn
                                  v-if="record === 'imprints' && r.link"
                                  fab
                                  x-small
                                  color="primary"
                                  v-text="'3D'"
                                  class="mr-1"
                                  style="position: absolute; right: 0; z-index: 10"
                                  @click="OpenNewBrowserTab(r.link)"
                                ></v-btn>
                                <!-- Placeholder -->
                                <v-responsive
                                  v-if="record === 'imprints' ? !r.fotos : !r.link"
                                  aspect-ratio="1"
                                  class="d-flex align-center text-center caption text-uppercase"
                                >
                                  <div v-if="r.link" v-text="label('only_3d')"></div>
                                  <div v-else v-text="label('no_digital')"></div>
                                </v-responsive>
                                <!-- Image Tile -->
                                <v-card
                                  v-else
                                  tile
                                  flat
                                  class="transparent"
                                  @click="ImageDialog(record, r)"
                                >
                                  <v-img
                                    contain
                                    aspect-ratio="1"
                                    :src="digilib_scaler + (record === 'imprints' ? r.fotos[0].link : r.link) + '&dw=300&dh=300'"
                                  ></v-img>
                                </v-card>
                                <div
                                  class="caption pa-1 mb-n1 text-center"
                                  v-text="r.fmid"
                                ></div>
                                <!--<v-card
                                  v-if="im.link"
                                  tile
                                  flat
                                  class="caption transparent text-center"
                                  v-text="'3D-Scan verfügbar'"
                                  @click="OpenNewBrowserTab(im.link)"
                                ></v-card>-->
                              </v-card>
                            </v-col>
                          </v-row>
                          <!-- 3D Credits -->
                          <div v-if="record === 'imprints' ? (d[record].find((r) => r)) : false" class="caption mt-1">
                            <a href="https://www.einsteinfoundation.de/" target="_blank" v-text="label('credit_3d')"></a>
                          </div>
                        </div>
                      </template>
                    </div>
                  </v-card-text>
                </div>
              </v-expand-transition>
            </v-card>

            <!-- Pagination -->
            <v-card
              tile
              class="bar_prim mb-5 mt-5 d-flex component-wrap"
            >
              <v-spacer></v-spacer>
              <!-- First -->
              <v-btn
                :tile="pagination.previous ? true : false"
                :text="pagination.previous ? false : true"
                depressed
                :disabled="pagination.previous ? false : true"
                class="bar_prim"
                @click="Navigate(pagination.first)"
              >
                <v-icon v-text="'first_page'"></v-icon>
              </v-btn>
              <!-- Previous -->
              <v-btn
                :tile="pagination.previous ? true : false"
                :text="pagination.previous ? false : true"
                depressed
                :disabled="pagination.previous ? false : true"
                class="bar_prim"
                @click="Navigate(pagination.previous)"
              >
                <v-icon v-text="'navigate_before'"></v-icon>
              </v-btn>
              <!-- Page -->
              <v-btn
                text
                disabled
                v-text="pagination.page.current + ' / ' + pagination.page.total"
              ></v-btn>
              <!-- Next -->
              <v-btn
                :tile="pagination.next ? true : false"
                :text="pagination.next ? false : true"
                depressed
                :disabled="pagination.next ? false : true"
                class="bar_prim"
                @click="Navigate(pagination.next)"
              >
                <v-icon v-text="'navigate_next'"></v-icon>
              </v-btn>
              <!-- Last -->
              <v-btn
                :tile="pagination.next ? true : false"
                :text="pagination.next ? false : true"
                depressed
                :disabled="pagination.next ? false : true"
                class="bar_prim"
                @click="Navigate(pagination.last)"
              >
                <v-icon v-text="'last_page'"></v-icon>
              </v-btn>
              <v-spacer></v-spacer>
            </v-card>

          </div>
        </v-expand-transition>
      </v-col>
    </v-row>

    <!-- Abbreviations -->
    <v-dialog
      v-model="abbreviations.active"
      :max-width="$vuetify.breakpoint.lgAndUp ? '50%' : '67%'"
      scrollable
      :fullscreen="$vuetify.breakpoint.smAndDown"
    >
      <v-card tile>
        <!-- Header -->
        <div class="d-flex align-center justify-space-between">
          <div class="font-weight-bold caption ml-3" v-html="'Abkürzungen'"></div>
          <v-btn text depressed small @click="abbreviations.active = false">
            <v-icon small v-text="'clear'"></v-icon>
          </v-btn>
        </div>
        <div style="border-bottom: 3px solid #b51212; background-color: #fefefe"></div>
        <v-card-title class="bar_prim">
          <!--<span class="headline">Abkürzungen</span>-->
          <v-text-field
            v-model="abbreviations.search"
            append-icon="search"
            label="Suche"
            dense
            single-line
            hide-details
            clearable
            class="pa-0 ma-0 mb-2"
            style="max-width: 300px"
          ></v-text-field>
        </v-card-title>
        <!--<div style="border-bottom: 3px solid #b51212; background-color: #fefefe"></div>-->
        <v-card-text class="pt-5" :style="$vuetify.breakpoint.smAndDown ? '' : 'height: 500px'">
          <div v-for="(a, i) in abbreviations_items" :key="i" class="mb-4">
            <div class="d-flex component-warp font-weight-bold">
              <div v-text="a.k"></div>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    v-bind="attrs"
                    v-on="on"
                    x-small
                    class="ml-3"
                    v-text="'content_copy'"
                    @click="query.name = a.k; abbreviations.active = false"
                  ></v-icon>
                </template>
                <span>Abkürzung in Suchfeld kopieren</span>
              </v-tooltip>
            </div>
            <span class="caption" v-text="a.v"></span>
          </div>
        </v-card-text>
        <!--<v-card-actions class="primary">
          <v-spacer></v-spacer>
          <v-btn text dark @click="abbreviations.active = false">ZURÜCK</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>-->
      </v-card>
    </v-dialog>

    <!-- Image -->
    <v-dialog
      v-model="image.active"
      :max-width="img_size.width"
      :max-height="img_size.height + 150"
    >
      <v-card>
        <!-- Header -->
        <div class="d-flex align-center justify-space-between">
          <div class="font-weight-bold caption ml-3" v-html="image.id"></div>
          <v-btn text depressed small @click="ImageDialog()">
            <v-icon small v-text="'clear'"></v-icon>
          </v-btn>
        </div>
        <div style="border-bottom: 3px solid #b51212; background-color: #fefefe"></div>
        <div class="pa-3">
          <!-- Image -->
          <v-img
            contain
            :src="image.link"
            :max-width="img_size.width"
            :max-height="img_size.height"
          ></v-img>
          <!-- Footer -->
          <div class="d-flex align-end justify-space-between mt-2">
            <div>
              <span class="font-weight-bold" v-html="image.id"></span><span v-if="image.left" v-html="', ' + image.left"></span>
            </div>
            <div v-if="image.right" v-html="image.right"></div>
          </div>
        </div>
      </v-card>
    </v-dialog>

  </div>
</template>

<script>
// import Logo from '~/components/Logo.vue'
// import VuetifyLogo from '~/components/VuetifyLogo.vue'

export default {
  components: {
    // Logo,
    // VuetifyLogo
  },

  data () {
    return {
      loading: false,
      filterExpanded: true,
      no_result: '&ensp;',
      itemsExpanded: [],
      items: [],
      itemsDetails: {},
      checkboxes: [
        { value: 'has_imprints', label: 'Abklatsche' },
        { value: 'has_imprints_3d', label: '3D-Abklatsche' },
        { value: 'has_fotos', label: 'Fotos' },
        { value: 'has_scheden', label: 'Scheden' }
      ],
      query: {},
      queryKeys: [
        'id',
        'name',
        'KO',
        'has_imprints',
        'has_imprints_3d',
        'has_fotos',
        'has_scheden'
      ],
      abbreviations: {
        active: false,
        search: ''
      },
      image: {
        active: false
      },
      img_size: {
        width: 1000,
        height: 750
      },
      info: {
        imprints: 'https://cil.bbaw.de/index.php?id=16',
        fotos: 'https://cil.bbaw.de/index.php?id=17',
        scheden: 'https://cil.bbaw.de/index.php?id=18'
      },
      copyrights: {
        imprints: { string: '&copy;&nbsp;CC&nbsp;BY-ND&nbsp;4.0', link: 'https://creativecommons.org/licenses/by-nd/4.0/deed.de' },
        fotos: null,
        scheden: { string: '&copy;&nbsp;CC&nbsp;BY-ND&nbsp;4.0', link: 'https://creativecommons.org/licenses/by-nd/4.0/deed.de' }
      },
      queryRefresh: 0,
      queryDialog: false,
      pagination: {
        offset: 0,
        limit: 50,
        count: 0,
        page: {
          current: 1,
          total: 1
        },
        first: null,
        previous: null,
        next: null,
        last: null
      },
      //digilib_scaler: this.$store.state.settings.digilib.scaler,
      //digilib_viewer: this.$store.state.settings.digilib.viewer
    }
  },

  computed: {
    /* given_id () {
        return this.$route.params.id != undefined ? this.$route.params.id : this.prop_id;
    }, */
    labels () {
      return this.$root.labels
    },

    api () { // Get adres of api depending on mode (set in settings)
      return '/api/inscriptions'
    },

    count_formated () { // Beautify result counter
      let count = this.pagination.count

      if (count > 0) {
        if (count > 999999) {
          count = count.toString().substring(0, count.toString().length - 6) + '.' + count.toString().substring(count.toString().length - 6, count.toString().length - 3) + '.' + count.toString().substring(count.toString().length - 3)
        }
        if (count > 999) {
          count = count.toString().substring(0, count.toString().length - 3) + '.' + count.toString().substring(count.toString().length - 3)
        }
        return count
      } else { return null }
    },

    query_ko () { // Get Concordance if given
      return this.$route.query.KO
    },

    abbreviations_items () { // Handler for Search in Abbreviation Dialog
      const content = []
      // Return items
      if (this.abbreviations.search) {
        return content.filter((row) => {
          if (row.k.toLowerCase().includes(this.abbreviations.search.toLowerCase()) || row.v.toLowerCase().includes(this.abbreviations.search.toLowerCase())) {
            return row
          }
        })
      } else {
        return content
      }
    }
  },

  created () {
    this.ImageDialog(null)
    this.ResetFilters()
    this.CheckConcordance() // Execute Query immediately if KO is given
  },

  methods: {
    label (key) {
      return this.labels[key] ? this.labels[key] : key
    },
    OpenNewBrowserTab (url) { // Handler for Links to external resources
      window.open(url, '_blank')
    },

    /* CiteLink (co) {
      const location = window.location.href.charAt(window.location.href.length - 1) === '/' ? window.location.href.substring(0, window.location.href.length - 1) : window.location.href
      return location + '?KO=' + co
    }, */

    async RunSearch () { // Execute Query
      this.itemsExpanded = []
      this.items = []
      this.itemsDetails = {}
      const fetch = await this.FetchData(this.BuildFetchURL())
      // Check if result
      if (fetch?.contents?.[0]) {
        // this.filterExpanded = false
      }
      else {
        this.no_result = 'Die Suche erbrachte kein Ergebnis.'
        setTimeout(() => { this.no_result = '&ensp;' }, 4000)
      }
      // JK: Set Pagination
      this.pagination = {
        count: fetch.pagination.count,
        page: fetch.pagination.page,
        first: fetch.pagination.firstPage,
        previous: fetch.pagination.previousPage,
        next: fetch.pagination.nextPage,
        last: fetch.pagination.nextPage,
      }
      console.log(this.pagination)
      // JK: Set Items
      this.items = fetch.contents
      console.log(this.items)
    },

    BuildFetchURL () { // Constructor for API Call
      const self = this
      let url = this.api
      const params = []
      // Iterate over query Keys
      this.queryKeys.forEach((key) => {
        if (self.query[key]) {
          const value = self.query[key] === true ? 1 : encodeURI(self.query[key])
          params.push(key + '=' + value)
        }
      })
      if (params[0]) {
        url = url + '?' + params.join('&')
      }
      return url
    },

    async CheckConcordance () { // Method to handle concordance if given as URL Parameter
      if (this.query_ko) {
        this.query.KO = this.query_ko
        await this.RunSearch()
        if (this.items.length === 1) {
          this.ToggleItem(this.items[0].id, this.items[0].self)
        }
        this.query.KO = null
      }
    },

    async Navigate (url) { // Method for Navigation Elements
      this.itemsExpanded = []
      this.items = []
      this.itemsDetails = {}
      const fetch = await this.FetchData(url)
      // Set Pagination
      this.pagination.count = fetch.count
      this.pagination.page = fetch.page
      this.pagination.first = fetch.previousPage ? fetch.firstPage : null
      this.pagination.previous = fetch.previousPage
      this.pagination.next = fetch.nextPage
      this.pagination.last = fetch.nextPage ? fetch.lastPage : null
      console.log(this.pagination)
      // Set Result Items
      this.items = fetch.contents
      console.log(this.items)
    },

    async FetchData (url) { // Axios Fetch to given URL
      this.loading = true
      const fetch = await axios.get(url).catch((error) => { console.log(error) })
      this.loading = false
      return fetch ? fetch.data : {}
    },

    async ToggleItem (id, url) { // Toggle Details in Result List
      if (!this.itemsExpanded.includes(id)) {
        const fetch = await this.FetchData(url)
        this.itemsDetails[id] = fetch.contents ? fetch.contents[0] : {}
        this.itemsExpanded.push(id)
      } else {
        this.itemsExpanded.splice(this.itemsExpanded.indexOf(id), 1)
        delete this.itemsDetails[id]
      }
    },

    ResetFilters () { // Reset Search Form Fields to empty
      const self = this
      if (self.query_ko) { this.$router.replace({ query: {} }) }
      this.queryKeys.forEach((key) => { self.query[key] = null })
      ++this.queryRefresh
    },

    ImageDialog (entity, data) {
      const self = this
      if (entity) {
        const id = data.fmid ? data.fmid : (data.id ? data.id : '--')
        const right = []
        const left = []
        // Author and Year
        if (data.author || data.year) {
          const creation = []
          if (data.author) { creation.push(data.author) }
          if (data.year) { creation.push(data.year) }
          left.push(creation.join(' '))
        }
        // Copyright
        if (this.copyrights[entity]) {
          left.push('<a href="' + this.copyrights[entity].link + '" target="_blank">' + this.copyrights[entity].string + '</a>')
        } else {
          left.push('&copy;&nbsp;' + this.label('copy_right_ask'))
        }
        // Information Link on right side
        if (Object.keys(this.info).includes(entity)) {
          right.push('<a href="' + this.info[entity] + '" target="_blank">' + this.label('further_information') + '</a>')
        }
        // Image Link
        const link = this.digilib_scaler + (entity === 'imprints' ? data.fotos?.[0]?.link : data.link) + '&dw=' + this.img_size.width + '&dh=' + this.img_size.height
        // Write Data
        this.image.entity = entity
        this.image.id = id
        this.image.link = link
        this.image.right = right.join(', ')
        this.image.left = left.join(', ')
        this.image.active = true
      } else {
        Object.keys(this.image).forEach((key) => {
          self.image[key] = key === 'active' ? false : null
        })
      }
    }
  }
}
</script>

<style>
  a {
    text-decoration: none !important;
  }
</style>
