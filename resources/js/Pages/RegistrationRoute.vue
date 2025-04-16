<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { useDateFormat } from '@vueuse/core';
import { useRoute } from "vue-router";

// Reactive state
const event = ref(null);
const registeredTeams = ref([]);
const teamName = ref("");
const captainName = ref("");
const phone = ref("");
const loading = ref(false);
const message = ref("");
const error = ref("");

const route = useRoute();

// Data fetching
const fetchEventData = async () => {
  try {
    const eventId = route.params.id || "";
    const response = await axios.get(`/api/event-results/${eventId}`);
    event.value = response.data.event;

    if (event.value) {
      await fetchRegisteredTeams();
    }
  } catch (err) {
    console.error("Nepodařilo se načíst události:", err);
    error.value = "Nepodařilo se načíst informace o události.";
  }
};

const fetchRegisteredTeams = async () => {
  try {
    const response = await axios.get(`/api/events/${event.value.id}/registrations`);
    registeredTeams.value = response.data || [];
  } catch (err) {
    console.error("Nepodařilo se načíst přihlášené týmy:", err);
  }
};

onMounted(fetchEventData);

// Computed properties
const formattedDate = computed(() => {
  return event.value ? useDateFormat(event.value.date, 'D. MMMM YYYY', { locales: 'cs-CZ' }) : '';
});

const formattedRegistrationStart = computed(() => {
  if (!event.value?.register_from) return '';
  const formatted = useDateFormat(event.value.register_from, 'D. M. YYYY v HH:mm', { locales: 'cs-CZ' });
  return formatted.value;
});

const isFutureEvent = computed(() => {
  return event.value && new Date(event.value.date) > new Date();
});

const isRegistrationOpen = computed(() => {
  return event.value && new Date(event.value.register_from) < new Date();
});

const isNotFull = computed(() => {
  return event.value && event.value.capacity > registeredTeams.value.length;
});

const canRegister = computed(() => {
  return isFutureEvent.value && isRegistrationOpen.value && isNotFull.value;
});

const capacityStatus = computed(() => {
  return event.value ? `Obsazeno ${registeredTeams.value.length} stolů z ${event.value.capacity}` : "";
});

const statusMessage = computed(() => {
  if (!event.value) return "Žádná nadcházející událost.";

  if (!isFutureEvent.value) {
    return "Tato událost již proběhla";
  }

  if (!isRegistrationOpen.value) {
    return `Registrace bude spuštěna ${formattedRegistrationStart.value}`;
  }

  if (!isNotFull.value) {
    return "Kapacita události byla naplněna";
  }

  return "Registruj se!";
});

// Update phone validation function
const validatePhone = (phone) => {
  // Povoluje formáty: +420 606 657 706, +420606657706, 606657706, 606 657 706
  const cleaned = phone.replace(/\s+/g, '');
  return /^(\+[1-9][0-9][0-9])?[1-9][0-9]{8}$/.test(cleaned);
};

// Modify submitRegistration to include validation
const submitRegistration = async () => {
  if (!event.value) return;

  // Validate phone number before submission
  if (!validatePhone(phone.value)) {
    error.value = "Zadejte platné české telefonní číslo (9 nebo 12 číslic)";
    return;
  }

  loading.value = true;
  message.value = "";
  error.value = "";

  try {
    const response = await axios.post(`/api/events/${event.value.id}/register`, {
      team_name: teamName.value,
      captain_name: captainName.value,
      phone: phone.value,
    });

    message.value = response.data.message || "Registrace byla úspěšně odeslána!";
    teamName.value = "";
    captainName.value = "";
    phone.value = "";

    await fetchRegisteredTeams();
  } catch (err) {
    error.value = err.response?.data.message ||
        err.message ||
        "Chyba při registraci. Zkontrolujte zadané údaje.";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div v-if="event" class="container mx-auto p-4">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5 bg-white rounded p-4 shadow-md">
      <h2 class="text-2xl sm:text-3xl md:text-4xl font-black">{{ event.name }}</h2>
      <div>
        Datum konání:<br>
        <span class="block sm:inline-block text-lg font-bold">{{ formattedDate }}</span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Registration Form Column -->
      <div class="bg-gray-50 p-4 rounded shadow-md">
        <h3 class="text-xl font-semibold mb-5">Registrovat tým</h3>

        <form
            v-if="canRegister"
            @submit.prevent="submitRegistration"
            class="space-y-4"
        >
          <div>
            <label for="name" class="block mb-2 font-medium">Jméno týmu *</label>
            <input
                id="name"
                v-model="teamName"
                type="text"
                class="w-full p-2 border rounded"
                required
            />
            <p class="text-sm text-gray-500 mt-1">Maximálně 50 znaků</p>
          </div>

          <div>
            <label for="leader" class="block mb-2 font-medium">Jméno kapitána *</label>
            <input
                id="leader"
                v-model="captainName"
                type="text"
                class="w-full p-2 border rounded"
                required
            />
            <p class="text-sm text-gray-500 mt-1">Maximálně 50 znaků</p>
          </div>

          <div>
            <label for="phone" class="block mb-2 font-medium">Telefon *</label>
            <input
                id="phone"
                v-model="phone"
                type="tel"
                @input="formatPhoneInput"
                class="w-full p-2 border rounded"
                required
            />
            <p class="text-sm text-gray-500 mt-1">Zadejte platné telefonní číslo</p>
          </div>

          <div class="text-center">
            <button
                type="submit"
                class="max-w-full w-auto text-center bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-700 disabled:bg-green-400"
                :disabled="loading"
            >
              {{ loading ? "Odesílám..." : "Odeslat přihlášku" }}
            </button>
          </div>

          <p v-if="message" class="p-3 text-green-700 bg-green-100 rounded">
            {{ message }}
          </p>
          <p v-if="error" class="p-3 text-red-700 bg-red-100 rounded">
            {{ error }}
          </p>
        </form>

        <div v-else class="p-3 rounded bg-white text-center text-gray-700">
          {{ statusMessage }}
        </div>
      </div>

      <!-- Registered Teams Column -->
      <div class="bg-gray-50 p-4 rounded shadow-md">
        <div class="flex flex-col gap-2 sm:flex-row items-start sm:justify-between sm:items-center mb-5">
          <h3 class="text-xl font-semibold">Přihlášené týmy</h3>
          <div class="px-4 py-1.5 bg-primary-lighter font-medium rounded-full text-xs sm:text-sm">
            {{ capacityStatus }}
          </div>
        </div>

        <div v-if="registeredTeams.length > 0" class="space-y-2">
          <div
              class="px-4 text-sm text-gray-600 rounded flex flex-col sm:flex-row items-start sm:justify-between gap-1 sm:gap-3 sm:items-center"
          >
            <div>Název týmu</div>
            <div>Kapitán</div>
          </div>
          <div
              v-for="team in registeredTeams"
              :key="team.id"
              class="py-2 px-4 bg-white rounded shadow-sm flex flex-col sm:flex-row items-start sm:justify-between gap-1 sm:gap-3 sm:items-center"
          >
            <div class="font-medium">{{ team.name }}</div>
            <div class="text-gray-600 text-sm">{{ team.leader }}</div>
          </div>
        </div>

        <div v-else class="p-3 bg-white rounded text-center text-gray-700">
          Zatím žádné přihlášené týmy
        </div>
      </div>
    </div>
  </div>

  <div v-else class="p-3 bg-gray-200 rounded text-center text-gray-700">
    <span v-if="!error">Načítání události...</span>
    <span v-else class="text-red-600">{{ error }}</span>
  </div>
</template>