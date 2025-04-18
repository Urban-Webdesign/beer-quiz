<script setup>
import { useDateFormat } from "@vueuse/core";
import { formatCzechDateTime, formatCzechMonth } from "../dateFormatter";

defineProps({
  event: Object,
  now: Date,
});

const isRegistrationOpen = (event, now) => {
  if (!event.register_from) return false;
  const from = new Date(event.register_from);
  const to = new Date(event.date);
  return from < now && now < to;
};
</script>

<template>
  <div class="min-h-[150px] rounded grid grid-cols-1 md:grid-cols-[120px,3fr] items-center gap-4 sm:gap-6 md:gap-8 shadow-md bg-gray-50 px-3 py-5 sm:p-5 md:p-4 text-lg">
    <div class="font-semibold text-base bg-white rounded shadow p-3 gap-1 flex md:flex-col justify-center md:items-center">
      <span class="md:text-sm">{{ useDateFormat(event.date, 'dddd', { locales: 'cs-CZ' }) }}</span>
      <span class="md:text-2xl">{{ useDateFormat(event.date, 'D.') }}</span>
      <span>{{ formatCzechMonth(event.date) }}</span>
    </div>

    <div class="flex flex-col text-base gap-3 sm:gap-5 lg:flex-row lg:justify-between items-start lg:items-center">
      <div class="flex flex-col gap-2">
        <h3 class="font-black text-xl">{{ event.name }}</h3>

        <div v-if="event.teams_count <= 0" class="flex flex-col gap-2">
          <span v-if="!event.register_from || new Date(event.register_from) > now">
            Registrace bude spuštěna <strong class="font-black">{{ event.register_from ? formatCzechDateTime(event.register_from) : 'později' }}</strong>.
          </span>
          <span v-else-if="event.capacity <= event.registrations_count">
            Na tenhle večer už jsou bohužel všechny stoly obsazeny.
          </span>
          <span v-else-if="event.capacity > event.registrations_count && new Date(event.date) > now">
            Plán na tento čtvrteční večer je jasný. Poskládej tříčlenný tým a posílej přihlášku!
          </span>
          <span v-else>
            Tenhle večer už proběhl, ale ještě jsme nestihli doplnit výsledky.
          </span>
        </div>

        <div v-else class="font-normal max-w-md">
          V tomto kole
          <span v-if="event.shootout">rozhodoval až <strong>🎯rozstřel</strong> a po těsném souboji</span>
          zvítězil tým <strong>🏆{{ event.winning_team }}</strong>. Celkem se zúčastnilo
          <strong>{{ event.teams_count }} týmů</strong>. Bodový průměr kola je <strong>{{ event.average_points }}</strong>.
        </div>
      </div>

      <div class="flex flex-col gap-2">
        <a
            v-if="isRegistrationOpen(event, now)"
            :href="`/registrace-${event.id}`"
            class="w-auto rounded-full font-medium tracking-wider shadow-md text-white px-4 py-2 bg-green-600 hover:bg-green-700"
        >
          <span v-if="event.capacity > event.registrations_count">Přihlásit tým!</span>
          <span v-else>Přihlášené týmy</span>
        </a>

        <a
            v-if="event.teams_count > 0"
            :href="`/kviz-${event.id}`"
            class="w-auto rounded-full font-medium tracking-wider shadow-md text-white px-4 py-2 bg-gray-600 hover:bg-gray-700"
        >
          Výsledková tabule
        </a>
      </div>
    </div>
  </div>
</template>