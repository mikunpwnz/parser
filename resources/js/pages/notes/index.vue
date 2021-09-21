<template>
    <v-container fluid>
        <v-row justify="center">
            <v-dialog
                v-model="modal"
                persistent
                max-width="600px"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        class="mb-4"
                        color="primary"
                        dark
                        v-bind="attrs"
                        v-on="on"
                    >
                        Добавить музыку
                    </v-btn>
                </template>
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Добавить музыку</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="12"
                                    md="12"
                                >
                                    <v-text-field
                                        :error-messages="form.errors.errors.title"
                                        label="Введите название музыки"
                                        required
                                        v-model="form.title"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="12"
                                    md="12"
                                >
                                    <v-textarea
                                        :error-messages="form.errors.errors.girls_id"
                                        label="Список баб"
                                        required
                                        v-model="form.girls_id"
                                    ></v-textarea>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="blue darken-1"
                            text
                            @click="closeModal"
                        >
                            Закрыть
                        </v-btn>
                        <v-btn
                            @click.prevent="submit"
                            color="blue darken-1"
                            text
                        >
                            Добавить
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>




        <v-row dense>
            <v-col
                v-for="note in notes"
                :key="note.id"
                :lg="3"
                :md="4"
                :sm="6"
            >
                <v-card>
                    <v-img
                        class="white--text align-end"
                        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                        height="200px"
                    >
                        <v-card-title v-text="note.title"></v-card-title>
                        <v-card-text v-text="note.status"></v-card-text>
                    </v-img>

                    <v-card-actions>
                        <router-link
                            v-if="note.progress === 100"
                            :to="'/note/' + note.id"
                            exact-path
                            custom
                            v-slot="{ href, route, navigate, isActive, isExactActive }"
                        >
                            <v-btn
                                :href="href"
                                @click="navigate"
                                block
                                x-large
                                color="primary"
                                dark
                            >
                                Открыть
                            </v-btn>
                        </router-link>
                        <v-progress-linear
                            v-else
                            v-model="note.progress"
                            height="25"
                        >
                            <strong>{{ Math.ceil(note.progress) }}%</strong>
                        </v-progress-linear>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

    </v-container>
</template>
<script>
export default {
    data: () => ({
        modal: false,
        notes: [],
        form: new Form({
            title: '',
            girls_id: '',
        })
    }),
    methods: {
        submit() {
            this.form.post('/api/note')
                .then(({data}) => {
                    this.closeModal();
                    console.log(data)
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                })
                .catch(() => {

                });
        },
        closeModal() {
            this.modal = false;
            this.form.reset();
        },
        // socket() {
        //     axios.post("/api/note/socket")
        //         .then(({data}) => {
        //             console.log('pf')
        //             console.log(data)
        //         })
        // }
    },
    created() {
        axios.get("api/note")
            .then(({data}) => {
                console.log(data)
                this.notes = data
            });
    },
    mounted() {
        Echo.channel('note-added')
            .listen('NoteAdded', (e) => {
                console.log(e)
                this.notes.push(e.note)
            })
        Echo.channel('note-progress')
            .listen('ProgressAddedForNoteEvent', (e) => {
                console.log(e)
                let note_id = e.note_id
                let progress = e.progress
                let status = e.status
                let notes = this.notes.find(note => note.id === note_id)
                console.log(notes)
                let index = this.notes.indexOf(notes)
                this.notes[index].progress = progress
                this.notes[index].status = status
            })
    },
}
</script>
