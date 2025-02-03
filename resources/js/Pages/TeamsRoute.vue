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
    <h2 class="text-2xl font-bold mb-4">Týmy podle počtu účastí</h2>

    <div
        class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 lg:gap-4"
        v-if="teams && teams.length"
    >
        <div
            class="border-2 border-gray-700 p-2 md:p-4 font-semibold flex flex-col items-center gap-3"
            v-for="team in teams"
            :key="team.id"
        >
            <span class="text-xl font-black">{{ team.name }}</span>
            <span>účasti: {{ team.results_count }}</span>
        </div>
    </div>
    <p v-else>Žádné týmy nebyly nalezeny.</p>
  </div>
</template>
