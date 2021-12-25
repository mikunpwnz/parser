import GroupIndex from '../pages/groups/index.vue'
import GirlsIndex from '../pages/girls/index.vue'
import GirlsNorm from '../pages/girls/indexGood.vue'
import GirlsNote from '../pages/girls/indexNote'
import SearchGirl from '../pages/girls/search'
import ApplicationIndex from '../pages/applications/index'
import NotesIndex from '../pages/notes/index'
import FriendsIndex from '../pages/friends/index'
import FriendIndexNorm from '../pages/friends/indexNorm'
import websocket from '../pages/websocket/websocket-test'

export default {
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'groupindex',
            component: GroupIndex,
        },
        {
            path: '/group/:id',
            name: 'girlsindex',
            component: GirlsIndex,
        },
        {
            path: '/girls',
            name: 'girlnorm',
            component: GirlsNorm,
        },
        {
            path: '/applications',
            name: 'applicationindex',
            component: ApplicationIndex,
        },
        {
            path: '/notes',
            name: 'notesindex',
            component: NotesIndex,
        },
        {
            path: '/note/:id',
            name: 'girlsnote',
            component: GirlsNote,
        },
        {
            path: '/search',
            name: 'searchgirl',
            component: SearchGirl,
        },
        {
            path: '/friends',
            name: 'indexfriends',
            component: FriendsIndex,
        },
        {
            path: '/friendsnorm',
            name: 'indexfriendsnorm',
            component: FriendIndexNorm,
        },
        {
            path: '/websocket',
            name: 'websocket',
            component: websocket,
        },
    ]
}
