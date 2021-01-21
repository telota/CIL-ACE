<template>
  <div>

  <!-- Query Form -->
    <v-row justify="center" align="center">
      <v-col cols="12" sm="10">

        <v-card tile raised class="mt-4">
            <!-- Header -->
            <div
                v-if="filterExpanded"
                class="d-flex justify-space-between align-end"
                style="width: 100%"
            >
                <div
                    class="caption pl-4"
                    style="cursor: pointer"
                    v-html="$root.label('abbreviations_note')"
                    @click="abbreviations.active = true"
                ></div>
                <v-btn
                    icon
                    class="ml-1 mr-1 mt-1"
                    :disabled="!items[0]"
                    @click="filterExpanded = !filterExpanded"
                >
                    <v-icon v-text="filterExpanded ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"></v-icon>
                </v-btn>
            </div>

            <!-- Filter Container  ----------------------------------------------------- ----------------------------------------------------- -->
            <v-expand-transition>
                <v-card-text v-if="filterExpanded">
                <v-row>
                    <!-- Name -->
                    <v-col cols=12 lg=6 class="pt-0">
                    <v-text-field
                        id="search_name"
                        v-model="query.name"
                        :key="'A' + queryRefresh"
                        :label="$root.label('inscription')"
                        clearable
                        v-on:keyup.enter="RunSearch()"
                    ></v-text-field>
                    </v-col>
                    <!-- Resources -->
                    <v-col cols=12 lg=6 :class="$vuetify.breakpoint.lgAndUp ? 'pt-8' : 'pt-0'">
                        <v-row class="pt-0">
                            <v-col
                                v-for="record in ['has_imprints', 'has_imprints_3d', 'has_fotos', 'has_scheden']"
                                :key="record + queryRefresh"
                                cols=6
                                xl=3
                                class="pt-0 ma-0"
                            >
                                <v-checkbox
                                    v-model="query[record]"
                                    :label="$root.label(record.slice(4))"
                                    class="ma-0 pa-0"
                                ></v-checkbox>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <!-- Search Button ----------------------------------------------------- -->
                <v-row justify="center" align="center">
                    <v-col cols="12" sm="4" class="mb-n4 mt-n2">
                    <v-btn large tile block color="primary" @click="RunSearch()">
                        <span
                            class="title"
                            v-text="$root.label('search')"
                        ></span>
                    </v-btn>
                    <div class="pt-2 mb-4 d-flex justify-center">
                        <v-btn
                            text
                            small
                            v-text="$root.label('search_reset')"
                            @click="ResetFilters(true)"
                        ></v-btn>
                    </div>
                    </v-col>
                </v-row>

                </v-card-text>
            </v-expand-transition>
        </v-card>

        <v-expand-transition>
            <v-card v-if="searched" tile class="bar_prim mt-5" :loading="loading">
                <!-- Result Count ----------------------------------------------------- -->
                <div class="d-flex align-center justify-center">
                    <v-btn
                        v-if="!filterExpanded"
                        icon
                        right
                        class="mr-l"
                        :disabled="!items[0]"
                        @click="filterExpanded = !filterExpanded"
                    >
                        <v-icon v-text="filterExpanded ? 'keyboard_arrow_up' : 'search'"></v-icon>
                    </v-btn>
                    <v-spacer></v-spacer>
                    <div
                        class="body-1 pb-2 pt-4"
                        v-html="!items[0] ? (loading ? $root.label('processing') : no_result) : (!count_formated ? '' : (count_formated + ' ' + $root.label('records')))"
                    ></div>
                    <v-spacer></v-spacer>
                    <v-btn
                        v-if="!filterExpanded"
                        icon
                        right
                        class="mr-1"
                        :disabled="!items[0]"
                        @click="filterExpanded = !filterExpanded"
                    >
                        <v-icon v-text="filterExpanded ? 'keyboard_arrow_up' : 'search'"></v-icon>
                    </v-btn>
                </div>
                <!-- Pagination -->
                <pagination :pagination="pagination" v-on:navigate="(emit) => { Navigate(emit) }"></pagination>
            </v-card>
        </v-expand-transition>

        <!-- Result Container ----------------------------------------------------- ----------------------------------------------------- -->
        <v-expand-transition>
          <div v-if="count_formated != null" class="mt-5">

            <!-- Results -->
            <v-card
              v-for="item in items"
              :key="item.id"
              tile
            >
              <v-expand-transition>
                <!-- Item Header -->
                <v-card
                    v-if="!itemsExpanded.includes(item.id)"
                    tile
                    flat
                    @click="ToggleItem(item.id, item.self)"
                >
                    <v-card-title >
                        <div class="d-flex mb-n2" style="width: 100%">
                            <div v-html="item.name" class="ml-auto pt-1 font-weight-bold body-1" style="width: 100%"></div>
                            <div class="pl-5 mr-auto">
                            <v-icon v-text="itemsExpanded.includes(item.id) ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"></v-icon>
                            </div>
                        </div>
                    </v-card-title>
                </v-card>
                <!-- Item Body -->
                <div
                  v-if="itemsExpanded.includes(item.id)"
                  style="border-top: 2px solid #b51212; background-color: #fefefe"
                >
                    <v-card-text>
                        <div v-for="d in [itemsDetails[item.id]]" :key="d.id">
                        <!-- Header -->
                        <div class="d-flex justify-space-between align-start">
                            <div class="body-1 font-weight-bold pb-2">
                                <span
                                    v-for="(ref, r) in d.name_object"
                                    :key="r"
                                ><!--
                                    --><span v-if="ref.delimiter" v-html="'&nbsp;' + ref.delimiter + '&nbsp;'"></span><!--
                                    --><span v-text="ref.clamped.start"></span><!--
                                    --><v-tooltip bottom><!--
                                        --><template v-slot:activator="{on}"><!--
                                            --><v-hover v-slot="{ hover }"><!--
                                                --><span
                                                    v-on="on"
                                                    v-html="ref.ref_short"
                                                    :class="hover ? 'accent--text' : ''"
                                                ></span><!--
                                            --></v-hover><!--
                                        --></template><!--
                                        --><span v-html="ref.ref_full.replace('u00b2', '²')"></span><!--
                                    --></v-tooltip><!--
                                    --><span v-text="ref.clamped.end"></span><!--
                                --></span>
                            </div>
                            <v-btn icon class="mr-n1 mt-n1" @click="ToggleItem(item.id, item.self)">
                                <v-icon v-text="itemsExpanded.includes(item.id) ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"></v-icon>
                            </v-btn>
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
                        <span v-text="$root.label('edcs_note')"></span>
                        <input type="submit" value="EDCS" class="font-weight-bold"/>
                      </form>
                      <!-- <div>Zitationslink: {{ CiteLink(d.concordance) }} <a :href="CiteLink(d.concordance)" style="text-decoration: none;">&#x1F5D7;</a></div> -->
                      <!-- Resources -->
                      <template v-for="record in ['imprints', 'fotos', 'scheden']">
                        <div :key="record" v-if="d[record]">
                          <!-- Header -->
                          <div class="pt-3 pb-1 title d-flex align-start">
                            <div v-text="$root.label(record)"></div>
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
                                  <div v-if="r.link" v-text="$root.label('only_3d')"></div>
                                  <div v-else v-text="$root.label('no_digital')"></div>
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
                              </v-card>
                            </v-col>
                          </v-row>
                          <!-- 3D Credits -->
                          <div v-if="record === 'imprints' ? (d[record].find((r) => r)) : false" class="caption mt-1">
                            <a href="https://www.einsteinfoundation.de/" target="_blank" v-text="$root.label('credit_3d')"></a>
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
                <pagination :pagination="pagination" v-on:navigate="(emit) => { Navigate(emit) }"></pagination>
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
          <div class="font-weight-bold caption ml-3" v-html="$root.label('abbreviations')"></div>
          <v-btn text depressed small @click="abbreviations.active = false">
            <v-icon small v-text="'clear'"></v-icon>
          </v-btn>
        </div>
        <div style="border-bottom: 2px solid #b51212; background-color: #fefefe"></div>
        <v-card-title class="bar_prim">
          <!--<span class="headline">Abkürzungen</span>-->
          <v-text-field
            v-model="abbreviations.search"
            append-icon="search"
            :label="$root.label('search')"
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
                <span v-text="$root.label('abbreviations_copy')"></span>
              </v-tooltip>
            </div>
            <span class="caption" v-text="a.v"></span>
          </div>
        </v-card-text>
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
        <div style="border-bottom: 2px solid #b51212; background-color: #fefefe"></div>
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

export default {
  data () {
    return {
      loading: false,
      filterExpanded: true,
      searched: false,
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
        imprints: { string: 'CC&nbsp;BY&nbsp;4.0', link: 'https://creativecommons.org/licenses/by/4.0/deed.de' },
        fotos: null,
        scheden: { string: 'CC&nbsp;BY&nbsp;4.0', link: 'https://creativecommons.org/licenses/by/4.0/deed.de' }
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
      digilib_scaler: 'https://digilib.bbaw.de/digitallibrary/servlet/Scaler?fn=silo10/CIL/',
      //digilib_viewer: this.$store.state.settings.digilib.viewer
    }
  },

  computed: {
    /* given_id () {
        return this.$route.params.id != undefined ? this.$route.params.id : this.prop_id;
    }, */
    api () {
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

    givenConcordance () { // Get Concordance if given
        let co = null
        if (this.$route.params.ko) {
            co = this.$route.params.ko
        } else if (this.$route.query.KO || this.$route.query.ko) {
            co = this.$route.query.KO ? this.$route.query.KO : this.$route.query.ko
        }
        if (co) {
            if (co.slice(0, 2).toLowerCase() !== 'ko') {
                co = 'KO' + co.padStart(7, '0')
            }
        }
        return co
    },

    abbreviations_items () { // Handler for Search in Abbreviation Dialog
      const content = this.$abbreviations
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

    watch: {
        givenConcordance: function() { this.CheckConcordance() }
    },

  created () {
    this.ImageDialog(null)
    this.CheckConcordance()
    this.ResetFilters()
  },

  methods: {
    OpenNewBrowserTab (url) { // Handler for Links to external resources
      window.open(url, '_blank')
    },

    /* CiteLink (co) {
      const location = window.location.href.charAt(window.location.href.length - 1) === '/' ? window.location.href.substring(0, window.location.href.length - 1) : window.location.href
      return location + '?KO=' + co
    }, */

    async RunSearch () { // Execute Query
        this.searched = true
      this.itemsExpanded = []
      this.items = []
      this.itemsDetails = {}
      const fetch = await this.FetchData(this.BuildFetchURL())
      // Check if result
      if (fetch?.contents?.[0]) {
        // this.filterExpanded = false
      }
      else {
        this.no_result = this.$root.label('no_records')
        setTimeout(() => { this.no_result = '&ensp;' }, 4000)
      }
      // JK: Set Pagination
      this.pagination = {
        count: fetch.pagination.count,
        page: fetch.pagination.page,
        first: fetch.pagination.firstPage,
        previous: fetch.pagination.previousPage,
        next: fetch.pagination.nextPage,
        last: fetch.pagination.lastPage,
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
      if (this.givenConcordance) {
        this.query.KO = this.givenConcordance
        await this.RunSearch()
        if (this.items.length === 1) {
          this.ToggleItem(this.items[0].id, this.items[0].self)
        }
        this.query.KO = null
        // URL Cleanup
        if (this.$route.name !== 'search') { this.$router.push({ name: 'search' }) }
        if (this.$route.query.ko || this.$route.query.KO) { this.$router.replace({ query: {} }) }
      }
    },

    async Navigate (url) { // Method for Navigation Elements
      this.itemsExpanded = []
      this.items = []
      this.itemsDetails = {}
      const fetch = await this.FetchData(url)
      // Set Pagination
      this.pagination = {
        count: fetch.pagination.count,
        page: fetch.pagination.page,
        first: fetch.pagination.firstPage,
        previous: fetch.pagination.previousPage,
        next: fetch.pagination.nextPage,
        last: fetch.pagination.lastPage,
      }
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
      //if (this.$route.name !== 'search') { this.$router.push({ name: 'search' }) }
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
          left.push(this.$root.label('copy_right_ask'))
        }
        // Information Link on right side
        if (Object.keys(this.info).includes(entity)) {
          right.push('<a href="' + this.info[entity] + '" target="_blank">' + this.$root.label('further_information') + '</a>')
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
