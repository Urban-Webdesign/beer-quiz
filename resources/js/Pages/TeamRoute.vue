<script setup>
import axios from "axios";
import { ref } from "vue";
import {useRoute} from "vue-router";
import { formatCzechDate } from '../dateFormatter.js'
import {useDateFormat} from "@vueuse/core";

const team = ref(null);

const route = useRoute();

const fetchTeam = async () => {
  try {
    const teamId = route.params.id || "";
    const response = await axios.get(`/api/teams/${teamId}`);
    team.value = response.data;
  } catch (error) {
    // Do something with the error
    console.log("Chyba při načítání týmu:", error);
  }
};
fetchTeam();
</script>

<template>
  <div v-if="team" class="flex flex-col gap-5 md:gap-8">
      <h2 class="text-2xl sm:text-3xl md:text-4xl font-black">Tým {{ team.name }}</h2>

    <div>
      <h3 class="text-xl font-bold mb-2">Přehled</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
        <div class="rounded shadow-sm bg-gray-50 p-2 md:p-4 font-semibold flex flex-col gap-1 items-center">
          <span>🗓️ Účasti</span>
          <span class="font-black text-2xl">{{ team.stats.total_participations}}</span>
        </div>
        <div class="rounded shadow-sm bg-gray-50 p-2 md:p-4 font-semibold flex flex-col gap-1 items-center">
          <span>🏆 Vítězství</span>
          <span class="font-black text-2xl">{{ team.stats.victories}}</span>
        </div>
        <div class="rounded shadow-sm bg-gray-50 p-2 md:p-4 font-semibold flex flex-col gap-1 items-center">
          <span>🎯 Rozstřely</span>
          <span class="font-black text-2xl">{{ team.stats.shootouts}}</span>
        </div>
      </div>
    </div>

    <div>
      <h3 class="text-xl font-bold mb-2">Týmové účasti</h3>
      <div v-for="participation in team.participations"
           class="justify-items-center sm:justify-items-start rounded shadow-md bg-primary-lighter p-2 sm:py-4 md:p-4 grid grid-cols-1 sm:grid-cols-[150px,1fr,150px] gap-2 mb-2 items-center">
        <div>
          {{ formatCzechDate(participation.event.date) }} {{ useDateFormat(participation.event.date, 'YYYY') }}
        </div>
        <div>
          {{ participation.event.name }}
        </div>
        <div class="font-bold">
          {{ participation.position }}. ({{ participation.score }} bodů)
        </div>
      </div>
    </div>

  </div>
  <div v-else>
    Tento tým nebyl nalezen.
  </div>
</template>
