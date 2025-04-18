<script setup>
import { ref } from "vue";

const teams = ref([]);
const loading = ref(true);
const error = ref(false);

const fetchTeams = async () => {
  loading.value = true;
  error.value = false;
  try {
    const response = await fetch("/api/teams");
    if (!response.ok) throw new Error("Network response was not ok");
    teams.value = await response.json();
  } catch (err) {
    console.error("Chyba při načítání týmů:", err);
    error.value = true;
  } finally {
    loading.value = false;
  }
};
fetchTeams();
</script>

<template>
  <div>
    <!-- Hlavička -->
    <div class="flex flex-col gap-2 mb-4 lg:flex-row lg:justify-between">
      <h2 class="text-2xl sm:text-3xl md:text-4xl font-black">Týmy podle počtu účastí</h2>
      <div class="flex flex-row gap-2">
        <div class="tooltip rounded shadow w-10 h-10 bg-primary-lighter flex justify-center items-center">🗓
          <span class="tooltiptext bg-white shadow-sm text-gray-900 text-sm p-2">Účastí</span>
        </div>
        <div class="tooltip rounded shadow w-10 h-10 bg-primary-lighter flex justify-center items-center">🏆
          <span class="tooltiptext bg-white shadow-sm text-gray-900 text-sm p-2">Vítězství</span>
        </div>
        <div class="tooltip rounded shadow w-10 h-10 bg-primary-lighter flex justify-center items-center">🎯
          <span class="tooltiptext bg-white shadow-sm text-gray-900 text-sm p-2">Rozstřelů</span>
        </div>
      </div>
    </div>

    <!-- Skeleton loader -->
    <div
        v-if="loading"
        class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 lg:gap-4"
    >
      <div
          v-for="i in 8"
          :key="'skeleton-' + i"
          class="min-h-[120px] rounded shadow-sm p-4 bg-gray-100 animate-pulse flex flex-col gap-4"
      >
        <div class="h-6 bg-gray-300 rounded w-3/4"></div>
        <div class="flex gap-2">
          <div class="h-8 w-12 bg-gray-300 rounded"></div>
          <div class="h-8 w-12 bg-gray-300 rounded"></div>
          <div class="h-8 w-12 bg-gray-300 rounded"></div>
        </div>
      </div>
    </div>

    <!-- Obsah po načtení -->
    <div
        v-else-if="teams.length"
        class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 lg:gap-4"
    >
      <a
          v-for="team in teams"
          :key="team.id"
          :href="'/tymy/' + team.id"
          class="min-h-[120px] rounded shadow-sm bg-gray-50 hover:bg-white hover:shadow-lg p-2 md:p-4 font-semibold flex flex-col gap-1 items-center"
      >
        <span class="text-xl font-black">{{ team.name }}</span>
        <div class="flex flex-row gap-2 mt-2">
          <div v-if="team.results_count > 0" class="text-lg rounded p-2 bg-white shadow flex justify-center items-center">🗓 {{ team.results_count }}</div>
          <div v-if="team.victories_count > 0" class="text-lg rounded p-2 bg-white shadow flex justify-center items-center">🏆 {{ team.victories_count }}</div>
          <div v-if="team.shootouts_count > 0" class="text-lg rounded p-2 bg-white shadow flex justify-center items-center">🎯 {{ team.shootouts_count }}</div>
        </div>
      </a>
    </div>

    <!-- Chybová hláška nebo žádné výsledky -->
    <div v-else class="text-center text-gray-700 mt-8">
      <p v-if="error">Nepodařilo se načíst seznam týmů. Zkuste to prosím později.</p>
      <p v-else>Žádné týmy nebyly nalezeny.</p>
    </div>
  </div>
</template>
