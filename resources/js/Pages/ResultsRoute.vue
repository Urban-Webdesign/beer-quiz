<script setup>
import axios from "axios";
import { ref, onMounted, watch } from "vue";
import { formatCzechDate } from '../dateFormatter.js'
import { useRoute, useRouter } from "vue-router";

const event = ref(null);
const results = ref([]);
const previousEventId = ref(null);
const nextEventId = ref(null);

const route = useRoute();

const fetchEventResults = async () => {
  try {
    const eventId = route.params.id || "";
    const response = await axios.get(`/api/event-results/${eventId}`);

    event.value = response.data.event; // Assign the event data
    results.value = response.data.results; // Assign the results array

    previousEventId.value = response.data.previous_event_id || null;
    nextEventId.value = response.data.next_event_id || null;

    // Najdi nejvyšší skóre
    const maxScore = Math.max(...results.value.map(r => r.score));

    // Spočítej, kolik týmů má nejvyšší skóre
    const teamsWithMaxScore = results.value.filter(r => r.score === maxScore);

    // Nastav inShootout pouze pokud jsou alespoň dva týmy se stejným nejvyšším skóre
    const isShootout = teamsWithMaxScore.length > 1;
    results.value.forEach(r => {
      r.inShootout = isShootout && r.score === maxScore;
    });

  } catch (error) {
    // Do something with the error
    console.log("Chyba při načítání události:", error);
  }
};

// Fetch data on component mount
onMounted(() => {
  fetchEventResults();
});

// Watch for changes in route parameters and refetch data
watch(
    () => route.params.id,
    () => {
      fetchEventResults();
    }
);
</script>

<template>
  <div>

    <span v-if="event" class="font-semibold text-xl block text-center">{{ formatCzechDate(event.date) }}</span>
    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-center mb-3" v-if="event">{{ event.name }}</h2>

    <div class="grid grid-cols-2 lg:grid-cols-[1fr,4fr,1fr] lg:items-center gap-4">
      <router-link
          class="order-2 lg:order-1 rounded lg:h-60 flex flex-col gap-3 justify-center items-center uppercase px-3 py-2 bg-white/25 hover:bg-white/50"
          :class="{ 'opacity-50 pointer-events-none': !previousEventId }"
          :to="previousEventId ? `/kviz-${previousEventId}` : ''"
          :aria-disabled="!previousEventId"
      >
        <svg
            class="w-6 h-6 md:w-8 md:h-8 lg:w-10 lg:h-10 text-gray-800"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="none"
            viewBox="0 0 24 24"
        >
          <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m15 19-7-7 7-7"
          />
        </svg>
        Předchozí
      </router-link>

      <div class="col-span-2 lg:col-span-1 order-1 lg:order-2">
        <div class="bg-gray-50 p-3 rounded-xl">
          <div
              v-if="results && results.length"
              class="border-4 border-gray-700 max-w-xl rounded-xl mx-auto border-dotted p-2 sm:p-4 md:p-8 text-lg sm:text-xl"
          >
            <div
                class="flex flex-row items-center justify-between gap-3 sm:grid sm:grid-cols-[3fr,2fr,1fr] p-3 border-b-2 border-gray-600/25 border-dotted"
                v-for="result in results"
                :key="result.id"
            >
              <div class="font-black basis-[40%] flex items-center gap-2">
                {{ result.team.name }}

                <div v-if="result.inShootout" class="tooltip rounded-full w-7 h-7 text-base bg-amber-100 shadow-sm flex justify-center items-center">
                  <span class="relative left-[1px] -top-[.5px]">🎯</span>
                  <span class="tooltiptext bg-white shadow-sm text-gray-900 text-sm p-2">Rozstřel</span>
                </div>
              </div>
              <div class="text-sm sm:text-base">
                ({{ result.score }} <span class="hidden sm:inline-block">bodů</span><span class="sm:hidden">b.</span>)
              </div>
              <div class="font-bold order-1 basis-10 justify-center sm:order-3 sm:m-auto cursor-default flex items-center gap-1">

                <div v-if="result.position === 1" class="tooltip rounded-full w-10 h-10 bg-amber-100 shadow-sm flex justify-center items-center">🏆
                  <span class="tooltiptext bg-white shadow-sm text-gray-900 text-sm p-2">Vítězství</span>
                </div>

                {{ result.position !== 1 ? result.position + '.' : ''}}
              </div>
            </div>
          </div>
          <p v-else class="text-center">
            Výsledky tohoto kola se nepodařilo načíst.
          </p>
        </div>
      </div>

      <router-link
          class="rounded order-2 lg:order-3 lg:h-60 flex flex-col gap-3 justify-center items-center uppercase px-3 py-2 bg-white/25 hover:bg-white/50"
          :class="{ 'opacity-50 pointer-events-none': !nextEventId }"
          :to="nextEventId ? `/kviz-${nextEventId}` : ''"
          :aria-disabled="!nextEventId"
      >
        <svg
            class="w-6 h-6 md:w-8 md:h-8 lg:w-10 lg:h-10 text-gray-800"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="none"
            viewBox="0 0 24 24"
        >
          <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m9 5 7 7-7 7"
          />
        </svg>
        Další
      </router-link>
    </div>
  </div>
</template>
