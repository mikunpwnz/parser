<template>
    <v-container fluid>
        <h1
            v-if="friends.length === 0"
        >
            Пользователи не найдены
        </h1>
        <v-pagination
            v-if="friends.length !== 0"
            v-model="page"
            :length="length"
            circle
            @input="getFriends(page)"
        >

        </v-pagination>
        <v-row>
            <v-col
                class="mt-2"
                v-for="friend in friends"
                :key="friend.id"
                :lg="4"
                :md="6"
                :sm="12"
                :xs="12"
            >

                <v-card class="">
                    <v-hover v-slot="{ hover }">
                        <v-img
                            contain
                            :src="'http://46.175.146.87/'+friend.photo"
                            class="white--text align-end"
                            height="300px"
                        >
                            <v-card-text class="bg-dark p-0 pl-5">{{ friend.first_name }} {{ friend.last_name }} |
                                {{ friend.bdate }}
                            </v-card-text>
                        </v-img>
                    </v-hover>
                    <div class="input-margin">
                        <v-text-field
                            placeholder="Введите описание"
                            v-model="friend.description"
                            @change="setDescription(friend)"
                            :success-messages="suc[friend.id]"
                        >
                        </v-text-field>
                    </div>
                    <v-card-actions
                        :style="styleObject(friend)"
                    >
                        <v-btn
                            target="_blank"
                            :href="friend.url"
                            x-large
                            icon
                        >
                            <v-icon
                                color="cyan accent-1"
                            >mdi-android-messages
                            </v-icon>
                        </v-btn>

                        <v-btn
                            v-if="friend.instagram !== '---'"
                            target="_blank"
                            :href="friend.instagram"
                            x-large
                            icon
                        >
                            <v-icon
                                color="cyan accent-1"
                            >mdi-instagram
                            </v-icon>
                        </v-btn>

                        <v-card-text class="p-0 pr-1">{{ friend.last_seen }}</v-card-text>
                        <v-spacer></v-spacer>
                        <v-btn
                            x-large
                            icon
                            @click="like(friend)"
                        >
                            <v-icon
                                color="pink lighten-2"
                            >mdi-heart
                            </v-icon>
                        </v-btn>
                        <v-btn
                            x-large
                            icon
                            @click="dislike(friend)"
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
            v-if="friends.length !== 0"
            v-model="page"
            :length="length"
            circle
            @input="getFriends(page)"
        >
        </v-pagination>
    </v-container>
</template>

<script>
export default {
    data: () => ({
        friends: {},
        length: 0,
        page: 1,
        suc: [],
    }),
    methods: {
        setDescription (friend) {
            let id = friend.id
            let description = friend.description
            axios.post('/api/friend/description', {
                id: id,
                description: description
            })
                .then(({data}) => {
                    console.log(data)
                    this.suc = []
                    console.log(this.suc[friend.id])
                    this.suc[friend.id] = data
                    setTimeout(this.clearLoad,2000)
                })
                .catch(() => {
                });
        },
        clearLoad() {
            this.suc = ''
        },
        getFriends(page = 1) {
            // window.scrollTo({
            //     top: 0,
            //     left: 0,
            //     behavior: "smooth"
            // })
            console.log('defolt')
            axios.get('/api/friend' + '?page=' + page)
                .then(({data}) => {
                    this.friends = data.data
                    this.length = data.last_page
                    console.log(data)
                })
                .catch(() => {
                });

        },
        styleObject(friend) {
            if (friend.wrote === 1) {
                return 'background-color: #2a61a3';
            }
            if (friend.need_to_write === 1) {
                return 'background-color: #1e5837';
            }
        },
        like(friend) {
            let id = friend.id
            axios.post('/api/friend/like', {
                id: id
            })
                .then(({data}) => {
                    friend.wrote = 0;
                    friend.need_to_write = 1;
                    this.getFriends(this.page)
                })
                .catch(() => {
                });
        },

        dislike(friend) {
            let id = friend.id
            axios.post('/api/friend/dislike', {
                id: id
            })
                .then(({data}) => {
                    friend.wrote = 1;
                    friend.need_to_write = 0;
                    this.getFriends(this.page)
                })
                .catch(() => {
                });
        }
    },
    mounted() {
        this.getFriends()
        console.log(this.$route.path);
    },
    created() {
        this.getFriends()
        console.log(this.$route.path);
    },
}
</script>
