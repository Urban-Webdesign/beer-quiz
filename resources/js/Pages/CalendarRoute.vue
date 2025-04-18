<script setup>
import { ref, computed, onMounted } from "vue";
import EventCard from './EventCard.vue';

const events = ref([]);
const isLoading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    const res = await fetch("/api/events");
    if (!res.ok) throw new Error("Network response was not ok");
    events.value = await res.json();
  } catch (err) {
    console.error("Failed to load events:", err);
    error.value = "Nepodařilo se načíst události. Zkuste to prosím později.";
  } finally {
    isLoading.value = false;
  }
});

const now = new Date();

const eventsByYear = computed(() => {
  const grouped = {};
  for (const ev of events.value) {
    const year = new Date(ev.date).getFullYear();
    (grouped[year] ||= []).push(ev);
  }
  return Object.entries(grouped)
      .sort(([a], [b]) => b - a)
      .map(([year, evs]) => ({
        year: +year,
        events: evs.sort((a, b) => new Date(b.date) - new Date(a.date)),
      }));
});
</script>

<template>
  <div>
    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black mb-2">Kalendář akcí</h2>

    <div v-if="isLoading" class="text-center py-8">
      Načítám události...
    </div>

    <div v-else-if="error" class="text-red-500 text-center py-8">
      {{ error }}
    </div>

    <div v-else-if="eventsByYear.length" class="flex flex-col gap-6">
      <div v-for="group in eventsByYear" :key="group.year" class="flex flex-col gap-3">
        <span class="text-3xl font-black">{{ group.year }}</span>
        <EventCard
            v-for="event in group.events"
            :key="event.id"
            :event="event"
            :now="now"
        />
      </div>
    </div>

    <p v-else class="text-center py-8">
      Žádné události nebyly nalezeny.
    </p>
  </div>
</template>