<template>
<div class="d-flex justify-center" style="width: 100%">
    <!-- First -->
    <v-btn
        :tile="pagination.previous ? true : false"
        :text="pagination.previous ? false : true"
        depressed
        :disabled="pagination.previous ? false : true"
        class="bar_prim"
        @click="$emit('navigate', pagination.first)"
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
        @click="$emit('navigate', pagination.previous)"
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
        @click="$emit('navigate', pagination.next)"
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
        @click="$emit('navigate', pagination.last)"
    >
        <v-icon v-text="'last_page'"></v-icon>
    </v-btn>

</div>
</template>

<script>
export default {
    props: {
        pagination: { type: Object, required: true }
    }
}
</script>
