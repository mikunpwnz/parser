<template>
    <v-app id="inspire">
        <v-navigation-drawer
            v-model="drawer"
            app
            clipped
        >
            <v-list dense>
                <v-list-group>
                    <template v-slot:activator>
                        <v-list-item-action>
                            <v-icon>mdi-account-group</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Группы</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <router-link to="/">
                        <v-list-item
                            link>
                            <v-list-item-action>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Список групп</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </router-link>
                </v-list-group>

                <v-list-group>
                    <template v-slot:activator>
                        <v-list-item-action>
                            <v-icon>mdi-music</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Музыка</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <router-link to="/notes">
                        <v-list-item
                            link>
                            <v-list-item-action>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Список музыки</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </router-link>
                </v-list-group>

                <v-list-group>
                    <template v-slot:activator>
                        <v-list-item-action>
                            <v-icon>mdi-face-woman</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Бабы</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <router-link to="/girls">
                        <v-list-item
                            link>
                            <v-list-item-action>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Список нормальных</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </router-link>
                    <router-link to="/search">
                        <v-list-item
                            link>
                            <v-list-item-action>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Поиск</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </router-link>
                </v-list-group>

                <v-list-group>
                    <template v-slot:activator>
                        <v-list-item-action>
                            <v-icon>mdi-application</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Приложения</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <router-link to="/applications">
                        <v-list-item
                            link>
                            <v-list-item-action>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Список приложений</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </router-link>
                </v-list-group>

                <v-list-group>
                    <template v-slot:activator>
                        <v-list-item-action>
                            <v-icon>mdi-account-plus</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Друзья</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <router-link to="/friends">
                        <v-list-item
                            link>
                            <v-list-item-action>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Список друзей</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </router-link>
                    <router-link to="/friendsnorm">
                        <v-list-item
                            link>
                            <v-list-item-action>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Список норм друзей</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </router-link>
                </v-list-group>

            </v-list>
        </v-navigation-drawer>
        <v-app-bar
            app
            clipped-left
        >
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>Парсер</v-toolbar-title>
        </v-app-bar>
        <v-main>
            <v-container
                class="fill-height"
                fluid
            >
                <router-view></router-view>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
export default {
    name: 'App',
    data: () => ({
        drawer: null,
    }),

    created() {
        this.$vuetify.theme.dark = true
    },
}
</script>
