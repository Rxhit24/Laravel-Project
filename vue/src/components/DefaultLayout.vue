
<template>
    <div class="min-h-full">
        <div class="bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-white rounded-md">
                            <img class="h-10 w-50" src="../assets/aone.svg"
                                alt="Workflow" />
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <!-- Nav Links -->
                                <router-link v-for="item in navigation" :key="item.name" :to="item.to"
                                    active-class="bg-gray-900 text-white" :class="[
                                        this.$route.name === item.to.name
                                            ? ''
                                            : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                                        'px-3 py-2 rounded-md text-sm font-medium',
                                    ]">{{ item.name }}
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <a @click="logout" :class="[
                            'block px-4 py-2 rounded-md text-sm text-white hover:bg-red-600  cursor-pointer',
                        ]">Sign out</a>
                    </div>
                    <div class="mr-2 flex md:hidden">
                        <!-- Mobile menu button -->
                        <button
                            class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                            <span class="sr-only">Open main menu</span>
                            <MenuIcon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                            <XIcon v-else class="block h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-100">
                <router-link v-for="item in navigation" :key="item.name" :to="item.to" active-class="bg-gray-900 text-white"
                    :class="[
                        this.$route.name === item.to.name
                            ? ''
                            : 'text-gray-900 hover:bg-gray-700 hover:text-white',
                        'block px-3 py-2 rounded-md text-base font-medium',
                    ]">{{ item.name }}
                </router-link>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-700 bg-gray-200">
                <div class="flex items-center px-5">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="black">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-xl font-semibold  text-gray-700">
                            {{ user.name }}
                        </div>
                        <div class="text-md font-medium text-gray-500">
                            {{ user.email }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <button @click="logout"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-white hover:bg-red-500 cursor-pointer">
                        Sign out
                    </button>
                </div>
                
            </div>
        </div>
    </div>
    <router-view></router-view>
    <Notifications></Notifications>
    
</template>

<script>
import { MenuIcon, XIcon } from '@heroicons/vue/outline'
import { useStore } from 'vuex';
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import Notifications from './Notifications.vue';



const navigation = [
    { name: 'Dashboard', to: { name: "Dashboard" } },
    { name: 'Surveys', to: { name: "Surveys" } },
]

const userNavigation = [
    // { name: 'Your Profile', },
    // { name: 'Settings', },
    { name: 'Logout', },
]


export default {
    name: "DefaultLayout",
    data() {
        return {
            navigation,
            userNavigation,
            open: false,
            
        }
    },
    components :{
        MenuIcon,
        XIcon,
        Notifications
    },
    setup() {
        const store = useStore();
        const router = useRouter();
        return {
            user: computed(() => store.state.user.data),
            logout
        };
        function logout() {
            store.dispatch('logout')
                .then(() => {
                    router.push({
                        name: 'Login'
                    })
                })

        }
    }
}

</script>

<style scoped></style>