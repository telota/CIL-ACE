<template><div></div>
</template>


<script>
export default {
    props: {
        data:       {type: Object}
    },

    created () {
        if (!this.data.language) {
            alert ('Error: App didn\'t receive any backend data!')
        }
        else {
            this.$root.InitializeSession(this.data);
        }
    }

}
</script>
