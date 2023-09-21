<template>
  <PageLayout>
    <template v-slot:header>
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Surveys</h1>
        <router-link :to="{ name: 'SurveyCreate' }"
          class="py-2 px-3 bg-emerald-500 hover:bg-emerald-600 rounded-md text-white">
          Add new Survey
        </router-link>
      </div>
    </template>
    <div v-if="surveys.loading" class="flex justify-center">
      <h1 class="text-lg">Loading...</h1>
    </div>
    <div v-else>
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
        <SurveyListItem v-for="(survey, ind) in surveys.data" :key="survey.id" class="opacity-0 animate-fade-in-down"
          :style="{ animationDelay: `${ind * 0.2}s` }" :survey="survey" @delete="deleteSurvey(survey)">

        </SurveyListItem>
      </div>
      
      <!-- <pre> {{ surveys.links }}</pre> -->
      <div class="flex justify-center mt-5">
        <nav class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px">
          <a href="#" v-for="(link, ind) in surveys.links" :key="ind" 
          v-html="link.label" 
          :disabled="!link.url"
          @click="getForPage(link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
            link.active 
            ?'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
            :'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
          ]">
          
        </a>
        </nav>
      </div>

    </div>



  </PageLayout>
</template>

<script setup>
import PageLayout from '../components/PageLayout.vue';
import SurveyListItem from '../components/SurveyListItem.vue';
import store from '../store';
import { computed } from 'vue';

const surveys = computed(() => store.state.surveys);
store.dispatch("getSurveys");

function deleteSurvey(survey) {
  if (confirm("Are you sure?")) {
    store.dispatch("deleteSurvey", survey.id)
      .then(() => {
        store.dispatch("getSurveys");
      })
  }
}
function getForPage (link){
  if(!link.url || link.active){
    return
  }
  store.dispatch('getSurveys' , {url : link.url});
}
</script>
  
<style></style>