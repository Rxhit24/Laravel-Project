import {createRouter, createWebHistory} from 'vue-router'

import Login from '../views/Login.vue'
import Surveys from '../views/Surveys.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'
import SurveyPageView from '../views/SurveyPageView.vue'
import DefaultLayout from '../components/DefaultLayout.vue'
import AuthLayout from '../components/AuthLayout.vue'
import SurveyView from '../views/SurveyView.vue'

import store from '../store'

const routes = [
    {
        path: '/' ,
        redirect: '/dashboard',
        component: DefaultLayout,
        meta: {requireAuth : true},
        children: [
            { path: '/dashboard', name: 'Dashboard', component: Dashboard},
            { path: '/surveys', name: 'Surveys', component: Surveys},
            { path: '/surveys/create', name: 'SurveyCreate', component: SurveyView},
            { path: '/surveys/:id', name: 'SurveyView', component: SurveyView},
        ]
    },
    {
        path: '/',
        redirect: '/login',
        name: 'AuthLayout',
        meta: {isGuest : true},
        component: AuthLayout,
        children: [
            {
                path : '/login' ,
                name : 'Login',
                component: Login
            },
            {
                path: '/register' ,
                name: 'Register',
                component: Register
            },
        ]
    },
    {
        path: '/view/survey/:slug',
        name: 'SurveyPageView',
        component: SurveyPageView
    }
    
    
]
const router = createRouter({
    history : createWebHistory(),
    routes
})

router.beforeEach((to ,from, next) => {
    if(to.meta.requireAuth && !store.state.user.token){
        next({name :'Login'})
    }else if(store.state.user.token && to.meta.isGuest){
        next({name : "Dashboard"})
    }
    else{
        next()
    }
})

export default router;