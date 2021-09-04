<style>
.v-card--reveal {
    align-items: center;
    bottom: 0;
    justify-content: center;
    opacity: 0.8;
    position: absolute;
    width: 100%;
}
</style>
<template>
    <v-container fluid>
        <v-pagination
            v-model="page"
            :length="length"
            circle
            @input="getGirlFromGroup(groupId,page)"
        >

        </v-pagination>
        <v-row>
            <v-col
                class="mt-2"
                v-for="girl in girls"
                :key="girl.id"
                :lg="4"
                :md="6"
                :sm="12"
                :xs="12"
            >

                <v-card class="">
                    <v-hover v-slot="{ hover }">
                        <v-img
                            contain
                            :src="girl.photo"
                            class="white--text align-end"
                            height="300px"
                        >
                            <v-card-text class="bg-dark p-0 pl-5">{{ girl.first_name }} {{ girl.last_name }} | {{ girl.bdate }}</v-card-text>
                            <v-expand-transition>
                                <div
                                    v-if="hover"
                                    class="d-flex transition-fast-in-fast-out teal darken-2 v-card--reveal white--text"
                                    style="height: 100%;"
                                >
                                    <div>
                                        <p v-for="group in girl.groups">{{ group.title }}</p>
                                    </div>
                                </div>
                            </v-expand-transition>
                        </v-img>
                    </v-hover>
                    <v-card-actions
                        :style="styleObject(girl)"
                    >
                        <v-btn
                            target="_blank"
                            :href="girl.url"
                            x-large
                            icon
                        >
                            <v-icon
                                color="cyan accent-1"
                            >mdi-android-messages
                            </v-icon>
                        </v-btn>

                        <v-card-text class="p-0 pr-1">{{ girl.last_seen }}</v-card-text>
                        <v-spacer></v-spacer>
                        <v-btn
                            x-large
                            icon
                            @click="like(girl)"
                        >
                            <v-icon
                                color="pink lighten-2"
                            >mdi-heart
                            </v-icon>
                        </v-btn>
                        <v-btn
                            x-large
                            icon
                            @click="dislike(girl)"
                        >
                            <v-icon
                                color="black"
                            >mdi-delete
                            </v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-pagination
            v-model="page"
            :length="length"
            circle
            @input="getGirlFromGroup(groupId,page)"
        >

        </v-pagination>
    </v-container>
</template>

<script>
export default {
    data: () => ({
        girls: {},
        length: 0,
        page: 1,
        groupId: '',
    }),
    methods: {
        getGirlFromGroup(id, page = 1) {
            console.log(id)
            console.log(page)

        },
        styleObject(girl) {
            if (girl.wrote === 1) {
                return 'background-color: #2a61a3';
            }
            if (girl.need_to_write === 1) {
                return 'background-color: #1e5837';
            }
        },
        like(girl) {
            let id = girl.id
            axios.post('/api/girl/like', {
                id: id
            })
                .then(({data}) => {
                    girl.wrote = 0;
                    girl.need_to_write = 1;
                })
                .catch(() => {
                });
        },

        dislike(girl) {
            let id = girl.id
            axios.post('/api/girl/dislike', {
                id: id
            })
                .then(({data}) => {
                    girl.wrote = 1;
                    girl.need_to_write = 0;
                })
                .catch(() => {
                });
        }
    },
    mounted() {
        this.groupId = this.$route.params.id
        this.getGirlFromGroup(this.groupId)
        console.log(this.$route.params.id);

    }
}
</script>
