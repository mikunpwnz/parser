<template>
    <v-btn
        class="mb-4"
        color="primary"
        dark
        @click="send"
    >
        Обновить
    </v-btn>
</template>
<script>
export default {
    methods: {
        send() {
            axios.post("websocket-test")
                .then(({data}) => {
                });
        }
    },
    mounted() {
        console.log('ky')
        Echo.channel('note-added')
            .listen('NoteAdded', (e) => {
                console.log(e)
            })
    }
}
</script>
