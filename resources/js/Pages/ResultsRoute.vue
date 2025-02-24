<script setup>
import axios from "axios";
import { ref } from "vue";

const events = ref([]);

const fetchTeams = async () => {
    try {
        const response = await axios.get("/api/events");
        events.value = response.data;
    } catch (error) {
        // Do something with the error
        console.log("Chyba při načítání událostí:", error);
    }
};
fetchTeams();
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Proběhlá kola</h2>

    <div
        v-if="events && events.length"
        class="flex flex-col gap-2 text-left text-gray-900"
    >
        <div
            v-for="event in events"
            :key="event.id"
            class="rounded grid grid-cols-1 md:grid-cols-[1fr,3fr] items-center gap-1 border-2 border-gray-700 p-2 md:p-4 text-lg md:text-xl"
        >
            <td class="font-semibold pl-3 md:pl-4">{{ event.date }}</td>
            <td class="font-black flex flex-col gap-2">
                <a
                    class="rounded hover:text-black px-3 py-2 md:p-3 underline hover:no-underline bg-white/80 hover:bg-white/50"
                    :href="`/kviz-${event.id}`"
                >
                    {{ event.name }}
                </a>
                <div class="text-sm md:text-base font-normal pl-3 md:pl-4">
                  🏆<span v-if="event.shootout">🎯</span>&nbsp;
                    <span class="font-black">{{ event.winning_team }}</span
                    >, zúčastněných týmů:
                    <span class="font-black">{{ event.teams_count }}</span
                    >, průměrné body:
                    <span class="font-black">{{ event.average_points }}</span>
                </div>
            </td>
        </div>
    </div>
    <p v-else>Žádné události nebyly nalezeny.</p>
  </div>
</template>
