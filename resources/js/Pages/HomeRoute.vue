<script setup>
import axios from "axios";
import { ref, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

const event = ref(null);
const results = ref([]);
const previousEventId = ref(null);
const nextEventId = ref(null);

// Access the current route and router
const route = useRoute();
const router = useRouter();

const fetchEventResults = async () => {
    try {
        const eventId = route.params.id || "";
        const response = await axios.get(`/api/event-results/${eventId}`);

        event.value = response.data.event; // Assign the event data
        results.value = response.data.results; // Assign the results array

        previousEventId.value = response.data.previous_event_id || null;
        nextEventId.value = response.data.next_event_id || null;
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
        <div class="grid grid-cols-2 lg:grid-cols-[1fr,4fr,1fr] gap-4">
            <a
                class="order-2 lg:order-1 lg:h-full flex flex-col gap-3 justify-center items-center uppercase px-3 py-2 bg-white/25 hover:bg-white/50"
                :class="{ 'opacity-50 pointer-events-none': !previousEventId }"
                :href="previousEventId ? `/kviz-${previousEventId}` : ''"
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

                Předchozí</a
            >

            <div class="col-span-2 lg:col-span-1 order-1 lg:order-2">
                <span
                    v-if="event"
                    class="font-semibold text-xl block text-center"
                    >{{ event.date }}</span
                >
                <h2 class="text-3xl font-black mb-2 text-center" v-if="event">
                    {{ event.name }}
                </h2>

                <div
                    v-if="results && results.length"
                    class="border-4 border-gray-700 border-dotted p-2 sm:p-4 md:p-8 text-xl"
                >
                    <div
                        class="grid grid-cols-1 gap-1 sm:grid-cols-[3fr,2fr,1fr] p-3 border-b-2 border-gray-600/25 border-dotted"
                        v-for="result in results"
                        :key="result.id"
                    >
                        <div class="font-black order-2 sm:order-1">
                            {{ result.team.name }}
                        </div>
                        <div class="order-3 sm:order-2">
                            ({{ result.score }} bodů)
                        </div>
                        <div class="font-bold order-1 sm:order-3">
                            {{ result.position }}.
                        </div>
                    </div>
                </div>
                <p v-else class="text-center">
                    Výsledky tohoto kola se nepodařilo načíst.
                </p>
            </div>

            <a
                class="order-2 lg:order-3 lg:h-full flex flex-col gap-3 justify-center items-center uppercase px-3 py-2 bg-white/25 hover:bg-white/50"
                :class="{ 'opacity-50 pointer-events-none': !nextEventId }"
                :href="nextEventId ? `/kviz-${nextEventId}` : ''"
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
            </a>
        </div>
    </div>
</template>
