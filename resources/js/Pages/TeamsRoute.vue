<script setup>
import axios from "axios";
import { ref } from "vue";

const teams = ref([]);

const fetchTeams = async () => {
    try {
        const response = await axios.get("/api/teams");
        teams.value = response.data;
    } catch (error) {
        // Do something with the error
        console.log("Chyba při načítání týmů:", error);
    }
};
fetchTeams();
</script>

<template>
  <div>

    <div class="flex flex-col gap-2 mb-4 lg:flex-row lg:justify-between">
      <h2 class="text-2xl font-bold">Týmy podle počtu účastí</h2>

      <div class="flex flex-row gap-2">
        <div class="tooltip rounded shadow w-10 h-10 bg-primary-lighter flex justify-center items-center">🏆
          <span class="tooltiptext bg-white shadow-sm text-gray-900 text-sm p-2">Vítězství</span>
        </div>
        <div class="tooltip rounded shadow w-10 h-10 bg-primary-lighter flex justify-center items-center">🎯
          <span class="tooltiptext bg-white shadow-sm text-gray-900 text-sm p-2">Rozstřelů</span>
        </div>
      </div>
    </div>

    <div
        class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 lg:gap-4"
        v-if="teams && teams.length"
    >
        <div
            class="rounded shadow-md bg-gray-50 p-2 md:p-4 font-semibold flex flex-col gap-1 items-center"
            v-for="team in teams"
            :key="team.id"
        >
            <span class="text-xl font-black">{{ team.name }}</span>
            <span>účastí: {{ team.results_count }}</span>
            <div class="flex flex-row gap-2 mt-2">
              <div v-if="team.victories_count > 0" class="rounded p-2 bg-white shadow flex justify-center items-center">🏆 {{ team.victories_count }}</div>
              <div v-if="team.shootouts_count > 0" class="rounded p-2 bg-white shadow flex justify-center items-center">🎯 {{ team.shootouts_count }}</div>
            </div>
        </div>
    </div>
    <p v-else>Žádné týmy nebyly nalezeny.</p>
  </div>
</template>
