<script setup>
import axios from "axios";
import { ref, computed } from "vue";
import { useDateFormat } from "@vueuse/core";
import { formatCzechDateTime, formatCzechMonth } from '../dateFormatter.js'

const events = ref([]);

const fetchTeams = async () => {
  try {
    const response = await axios.get("/api/events");
    events.value = response.data;
  } catch (error) {
    //console.log("Chyba při načítání událostí:", error);
  }
};
fetchTeams();

// Helper function for proper date comparison
const isFuture = (dateString) => {
  const eventDate = new Date(dateString);
  const today = new Date();
  return eventDate > today;
};

const isPast = (dateString) => {
  const eventDate = new Date(dateString);
  const today = new Date();
  return eventDate < today;
};

const isRegistrationOpen = (event) => {
  if (!event.register_from) return false;
  const registerFrom = new Date(event.register_from);
  const today = new Date();
  return registerFrom < today && today < new Date(event.date);
};

// Group events by year
const eventsByYear = computed(() => {
  const grouped = {};
  events.value.forEach(event => {
    const year = new Date(event.date).getFullYear();
    if (!grouped[year]) {
      grouped[year] = [];
    }
    grouped[year].push(event);
  });

  // Sort years in descending order
  return Object.entries(grouped)
      .sort(([yearA], [yearB]) => yearB - yearA)
      .map(([year, events]) => ({
        year: Number(year),
        events: events.sort((a, b) => new Date(b.date) - new Date(a.date)) // Sort events within year
      }));
});
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Kalendář akcí</h2>

    <div v-if="eventsByYear.length" class="flex flex-col gap-6">
      <div v-for="group in eventsByYear" :key="group.year" class="flex flex-col gap-3">
        <span class="text-3xl font-black">
          {{ group.year }}
        </span>

        <div class="flex flex-col gap-3 text-left text-gray-900">
          <div
              v-for="event in group.events"
              :key="event.id"
              class="rounded grid grid-cols-1 md:grid-cols-[120px,3fr] items-center gap-4 sm:gap-6 md:gap-8 shadow-md bg-gray-50 px-3 py-5 sm:p-5 md:p-4 text-lg"
          >
            <div class="font-semibold text-base  bg-white rounded shadow p-3 gap-1 flex md:flex-col justify-center md:items-center">
              <span class="md:text-sm">{{ useDateFormat(event.date, 'dddd', { locales: 'cs-CZ' }) }}</span>
              <span class="md:text-2xl">{{ useDateFormat(event.date, 'D.') }}</span>
              <span>{{ formatCzechMonth(event.date) }}</span>
            </div>

            <div class="flex flex-col text-base gap-3 sm:gap-5 lg:flex-row lg:justify-between items-start lg:items-center">
              <div class="flex flex-col gap-2">
                <h3 class="font-black text-xl">
                  {{ event.name }}
                </h3>

                <div v-if="event.teams_count <= 0" class="flex flex-col gap-2">
                  <span v-if="!event.register_from || new Date(event.register_from) > new Date()">
                    Na registraci jsi tu moc brzy. Ještě potrénuj a vrať se sem <strong class="font-black">{{ event.register_from ? formatCzechDateTime(event.register_from) : 'později' }}</strong>.
                  </span>
                  <span v-else-if="event.capacity <= event.registrations_count">
                    Na tenhle večer už jsou bohužel všechny stoly obsazeny.
                  </span>
                  <span v-else-if="event.capacity > event.registrations_count && isFuture(event.date)">
                    Plán na tento čtvrteční večer je jasný. Poskládej tříčlenný tým a posílej přihlášku!
                  </span>
                  <span v-else>
                    Tenhle večer už proběhl, ale ještě jsme nestihli doplnit výsledky.
                  </span>
                </div>
                <div v-else class="font-normal max-w-md">
                  V tomto kole <div class="inline-block" v-if="event.shootout">rozhodoval až <span class="font-black">🎯rozstřel</span> a po těsném souboji</div> zvítězil tým <span class="font-black">🏆{{ event.winning_team }}</span>. Celkem se zúčastnilo <span class="font-black">{{ event.teams_count }} týmů</span>. Bodový průměr kola je <span class="font-black">{{ event.average_points }}</span>.
                </div>
              </div>

              <a v-if="isRegistrationOpen(event)" :href="`/registrace-${event.id}`" class="w-auto rounded-full font-medium tracking-wider shadow-md text-white px-4 py-2 bg-green-600 hover:bg-green-700">
                Přihlásit tým!
              </a>
              <a v-if="event.teams_count > 0" :href="`/kviz-${event.id}`" class="w-auto rounded-full font-medium tracking-wider shadow-md text-white px-4 py-2 bg-blue-600 hover:bg-blue-700">
                Výsledková tabule
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p v-else>Žádné události nebyly nalezeny.</p>
  </div>
</template>