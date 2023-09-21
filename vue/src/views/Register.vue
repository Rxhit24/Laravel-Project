<!-- <template>
    <div class="card border-0 shadow">
        <div class="card-body border-2 border-primary">
            <svg class="mx-auto my-3 bi bi-person-circle" xmlns="http://www.w3.org/2000/svg" width="50" height="50"
                fill="currentColor" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                <path fill-rule="evenodd"
                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
            </svg>
            <form @submit.prevent="register">
                <input type="text" name="" id="name" autocomplete="name" class="form-control py-2 me-2" placeholder="Full Name"
                    v-model="user.name" />
                <input type="email" name="" id="email" autocomplete="email" class="form-control my-4 py-2" placeholder="Email"
                    v-model="user.email" />

                <input type="password" name="" id="password" class="form-control my-4 py-2" placeholder="Password"
                    v-model="user.password" />
                <input type="password" name="" id="password_c" class="form-control my-4 py-2" au
                    placeholder="Password Confirmation" v-model="user.password_confirmation" />
                <div class="text-center mt-3">
                    <button class="btn btn-primary bg-primary my-3" >
                        Sign Up
                    </button>
                    <router-link :to="{ name: 'Login' }" class="nav-link">Already have an account ?</router-link>
                </div>
            </form>
        </div>
    </div>
</template> -->
<template>
    <div>
        <!-- <pre>{{ errors }}</pre> -->
        <div>
            <img class="mx-auto h-20 w-auto" src="../assets/aone.svg"
                alt="Workflow" />
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Register for free
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                {{ " " }}
                <router-link :to="{ name: 'Login' }" class="font-medium text-indigo-600 hover:text-indigo-500">
                    login to your account
                </router-link>
            </p>
        </div>
        <form class="mt-8 space-y-6" @submit.prevent="register">
            <Alert v-if="Object.keys(errors).length" class="flex-col items-stretch text-sm">
                <div v-for="(field, i) of Object.keys(errors)" :key="i">
                    <div v-for="(error, ind) of errors[field] || []" :key="ind">
                        * {{ error }}
                    </div>
                </div>
            </Alert>

            <input type="hidden" name="remember" value="true" />
            <div class="rounded-md shadow-sm -space-y-px">
                <input name="name" 
                class="appearance-none rounded-none relative rounded-t-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" v-model="user.name"  placeholder="Full Name"
                    inputClass="rounded-t-md" />
                <input type="email" 
                class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                name="email" v-model="user.email"  placeholder="Email Address" />
                <input type="password" 
                class=" relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                name="password" v-model="user.password"  placeholder="Password" />
                <input type="password" class="appearance-none rounded-none rounded-b-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                name="password_confirmation" v-model="user.password_confirmation" 
                    placeholder="Confirm Password" inputClass="rounded-b-md" />
            </div>
            <div>
                <button :disabled="loading" class="group relative w-full flex justify-center py-2 px-4 border border-transparent
            bg-indigo-600 hover:bg-indigo-700 text-sm font-medium rounded-md text-white focus:outline-none 
            focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :class="{ 'cursor-not-allowed bg-gray-500 hover:bg-gray-500' : loading }">
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                Register
            </button>
            </div>
        </form>
    </div>
</template>
  


<script setup>
import { useRouter } from "vue-router";
import store from '../store';
import {ref} from 'vue';

import Alert from "../components/Alert.vue";

const router = useRouter();

const errors = ref({});

const loading = ref(false);

const user = {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
};
function register() {
    // console.log('R1');
    loading.value = true;
    store.dispatch('register', user)
        .then(() => {
            loading.value = false;
            router.push({
                name: 'Dashboard'
            })
        }).catch((err)=>{
            // console.log(err.response.data.errors);
            loading.value = false;
            errors.value = err.response.data.errors;
        })
}
// export default {
//     name: "Register",
//     data() {
//         return {
//             user :{
//                 name: '',
//                 email: '',
//                 password: '',
//                 password_c: '',
//             },

//         }

//     },
//     setup() {
//         const router = useRouter();
//         const store = useStore();
//         return {
//             register,
//         };

//         function register(ev) {
//             console.log('R1')
//             ev.preventDeafult();
//             store.dispatch('register', user)
//             .then((res) => {
//                 router.push({
//                     name: 'Dashboard'
//                 })
//             })
//         }
//     }
// }
</script>
<style></style>
