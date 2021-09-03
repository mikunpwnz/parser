import GroupIndex from '../pages/groups/index.vue'
import GirlsIndex from '../pages/girls/index.vue'
import GirlsNorm from '../pages/girls/indexGood.vue'
import ApplicationIndex from '../pages/applications/index'

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
    ]
}
