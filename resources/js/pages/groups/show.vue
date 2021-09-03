<template>

</template>

<script>
export default {
    methods: {
        getGirlFromGroup(id) {
            axios.get('/api/group/'+id)
                .then(({data}) => {
                    console.log(data)
            })
                .catch(() => {
                });
        }
    },
    mounted() {
        this.getGirlFromGroup(this.$route.params.id)
        console.log(this.$route.params.id);
    }
}
</script>
