<script setup>
import kalendar from '../../img/kalendar.jpg';
import vysledky from '../../img/vysledky.jpg';
import tymy from '../../img/tymy.jpg';
import { ref, onMounted } from 'vue';
import {useDateFormat} from "@vueuse/core";
import {formatCzechDateTime, formatCzechMonth} from "../dateFormatter.js";

// State for next event
const nextEvent = ref(null);
const loading = ref(true);
const error = ref(null);
// Fetch next upcoming event
const fetchNextEvent = async () => {
  try {
    loading.value = true;
    const response = await fetch('/api/events/next');
    if (!response.ok) throw new Error("Network response was not ok");
    nextEvent.value = await response.json();
  } catch (err) {
    console.error("Failed to fetch next event:", err);
    error.value = "Další kolo soutěže bude včas zveřejněno.";
  } finally {
    loading.value = false;
  }
};

const isRegistrationOpen = (event) => {
  if (!event.register_from) return false;
  const registerFrom = new Date(event.register_from);
  const today = new Date();
  return registerFrom < today && today < new Date(event.date);
};

onMounted(fetchNextEvent);
</script>

<template>
  <div class="max-w-[450px] md:max-w-none mx-auto">
  <header class="mb-4 sm:mb-8 md:mb-12 ">
    <div class="flex flex-col items-center relative">
      <h1 class="font-semibold text-4xl sm:text-5xl md:text-6xl lg:text-8xl mb-3 text-gray-900">
        Pioneer Beer Quiz
      </h1>
      <p class="text-lg px-2 lg:text-xl text-center">
        Předveď své znalosti v pub kvízu nejenom o <span class="font-black">chmelu</span> a <span class="font-black">pivu</span>!
      </p>
    </div>
  </header>

  <!-- Next Event Banner -->
  <h2 class="text-lg md:text-xl font-bold text-center md:text-left pb-4 md:pb-0 md:px-4 md:py-1.5 md:bg-gray-50 md:inline-block md:rounded-t">Nejbližší kolo kvízu</h2>
  <div class="mb-8 lg:mb-12 rounded md:rounded-ss-none shadow-md bg-gray-50 px-3 py-5 sm:p-5 md:p-4 text-lg">
    <div v-if="!loading && nextEvent" class="grid grid-cols-1 md:grid-cols-[120px,3fr] items-center gap-4 sm:gap-6 md:gap-8">
      <div class="font-semibold text-base bg-white rounded shadow p-3 gap-1 flex md:flex-col justify-center md:items-center">
        <span class="md:text-sm">{{ useDateFormat(nextEvent.date, 'dddd', { locales: 'cs-CZ' }) }}</span>
        <span class="md:text-2xl">{{ useDateFormat(nextEvent.date, 'D.') }}</span>
        <span>{{ formatCzechMonth(nextEvent.date) }}</span>
      </div>

      <div class="flex flex-col text-base gap-3 sm:gap-5 md:flex-row md:justify-between items-center ">

        <div class="flex flex-col items-center md:items-start gap-1">
            <h3 class="font-bold text-xl sm:text-2xl md:text-3xl mb-3">
              {{ nextEvent.name }}
            </h3>
            <div v-if="nextEvent.date">Začátek: <span class="font-bold">{{ useDateFormat(nextEvent.date, 'H:mm') }}</span></div>
            <div v-if="nextEvent.capacity">
              Kapacita:
              <span class="font-bold">{{ nextEvent.capacity }} týmů</span>
              <span v-if="isRegistrationOpen(nextEvent)" class="ml-1">(volná místa: {{ nextEvent.capacity - nextEvent.registrations_count }})</span>
            </div>
        </div>

        <a :href="`/registrace-${nextEvent.id}`" class="flex flex-row items-center gap-1.5 rounded-full font-medium tracking-wider shadow-md text-white px-4 py-2" :class="nextEvent.capacity > nextEvent.registrations_count ? 'bg-green-600 hover:bg-green-700' : ' bg-gray-600 hover:bg-gray-700'">
          <span v-if="nextEvent.capacity > nextEvent.registrations_count">Přihlásit tým!</span>
          <span v-else>Detail události</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
          </svg>
        </a>

      </div>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-[120px,3fr] items-center gap-4 sm:gap-6 md:gap-8 animate-pulse">
      <div class="bg-white rounded shadow p-3 gap-1 flex md:flex-col justify-center md:items-center h-full">
        <div class="w-10 h-4 bg-gray-200 rounded mb-1"></div>
        <div class="w-6 h-6 bg-gray-200 rounded mb-1"></div>
        <div class="w-16 h-4 bg-gray-200 rounded"></div>
      </div>

      <div class="flex flex-col text-base gap-3 sm:gap-5 lg:flex-row lg:justify-between items-start lg:items-center">
        <div class="flex flex-col sm:flex-row gap-1 w-full">
          <div class="flex-1">
            <div class="w-3/4 h-7 bg-gray-200 rounded mb-2"></div>
            <div class="w-1/2 h-4 bg-gray-200 rounded mb-1"></div>
            <div class="w-2/3 h-4 bg-gray-200 rounded"></div>
          </div>
        </div>
        <div class="w-40 h-10 bg-gray-200 rounded-full"></div>
      </div>
    </div>

    <div v-if="error" class="w-full font-medium py-3 flex justify-center items-center">{{ error }}</div>
  </div>

  <!-- Original Cards Section -->
  <div class="mb-8 md:mb-12 lg:mb-16 grid md:grid-cols-3 gap-10 md:gap-6">
    <div class="rounded shadow-sm bg-gray-50 p-5 flex flex-col gap-5 items-center justify-between">
      <div class="text-center flex flex-col items-center gap-3">
        <div class="aspect-[2/1] w-full">
          <img :src="kalendar" alt="Pioneer Beer kvíz - kalendář" class="mb-3 rounded shadow-sm">
        </div>
        <h3 class="text-xl font-black">Přehled všech kol</h3>
        <p class="text-center">V kalendáři najdeš všechna uskutečněná kola stejně tak jako ta, co se teprve uskuteční.</p>
      </div>
      <a href="/kalendar" target="_top" class="min-w-[150px] max-w-full w-auto font-medium text-center bg-green-600 text-white px-5 py-1.5 rounded-full hover:bg-green-700 disabled:bg-green-400">
        Kalendář
      </a>
    </div>
    <div class="rounded shadow-sm bg-gray-50 p-5 flex flex-col gap-5 items-center justify-between">
      <div class="text-center flex flex-col items-center gap-3">
        <div class="aspect-[2/1] w-full">
          <img :src="vysledky" alt="Pioneer Beer kvíz - výsledky" class="mb-3 rounded shadow-sm">
        </div>
        <h3 class="text-xl font-black">Nejnovější výsledky</h3>
        <p class="text-center">Výsledková tabule ti automaticky zobrazí nejnovější výsledky.</p>
      </div>
      <a href="/kviz" target="_top" class="min-w-[150px] max-w-full w-auto font-medium text-center bg-green-600 text-white px-5 py-1.5 rounded-full hover:bg-green-700 disabled:bg-green-400">
        Výsledky
      </a>
    </div>
    <div class="rounded shadow-sm bg-gray-50 p-5 flex flex-col gap-5 items-center justify-between">
      <div class="text-center flex flex-col items-center gap-3">
        <div class="aspect-[2/1] w-full">
          <img :src="tymy" alt="Pioneer Beer kvíz - týmy" class="mb-3 rounded shadow-sm">
        </div>
        <h3 class="text-xl font-black">Seznam týmů</h3>
        <p class="text-center">Historický přehled všech týmů, které se této soutěže zúčastnily.</p>
      </div>
      <a href="/tymy" target="_top" class="min-w-[150px] max-w-full w-auto font-medium text-center bg-green-600 text-white px-5 py-1.5 rounded-full hover:bg-green-700 disabled:bg-green-400">
        Týmy
      </a>
    </div>
  </div>

  <!-- Quiz Information Section -->
  <section class="bg-white rounded-lg shadow-sm p-5">
    <h2 class="text-2xl md:text-3xl text-center font-bold mb-6 text-gray-800">O Pioneer Beer Quizu</h2>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <h3 class="text-xl font-semibold mb-2 text-gray-700">Jak to funguje?</h3>
        <ul class="space-y-7">
          <li class="flex items-start">
            <span class="mr-4">ℹ️</span>
            <div class="flex flex-col gap-2">
              <span>Pravidelné pub kvízy v příjemném prostředí Pioneer Beer pivovaru v Žatci.</span>
              <span>Pořádá Vladimír Valeš z Chmelařského muzea v Žatci.</span>
            </div>
          </li>
          <li class="flex items-start">
            <span class="mr-4">🧠</span>
            <div class="flex flex-col gap-2">
              <span>21 zajímavých otázek z různých oblastí (pivo, chmel, historie, kultura, zeměpis, příroda, sport, Žatec).</span>
              <span>Správná odpověď se volí ze tří možností (A, B, C).</span>
            </div>
          </li>
          <li class="flex items-start">
            <span class="mr-4">👥</span>
            <span>3 členné týmy, celkem soutěží 9 týmů.</span>
          </li>
          <li class="flex items-start">
            <span class="mr-4">⏱️</span>
            <span>Celý kvíz trvá asi 75 minut včetně přestávky v polovině.</span>
          </li>
        </ul>
      </div>

      <div>
        <h3 class="text-xl font-semibold mb-2 text-gray-700">Praktické informace</h3>
        <ul class="space-y-7">
          <li class="flex items-start">
            <span class="mr-4">📍</span>
            <span><strong>Místo konání:</strong> nám. Prokopa Velkého 303, 438 01 Žatec</span>
          </li>
          <li class="flex items-start">
            <span class="mr-4">💵</span>
            <span><strong>Vstupní poplatek:</strong> 150 Kč za tým</span>
          </li>
          <li class="flex items-start">
            <span class="mr-4">🏆️</span>
            <span><strong>Ceny:</strong> Každý člen vítězného týmu obdrží odměnu od pivovaru Pioneer Beer.</span>
          </li>
          <li class="flex items-start">
            <span class="mr-4">📅</span>
            <span>
                <strong>Termíny:</strong> Budou včas zveřejněny zde na webu. Kvízy se konají ve čtvrtek od 19:30 zpravidla 8x do roka (od října do května).<br>
                <span class="text-sm text-gray-600">Registrace se otevírá týdem předem v 18:00.</span>
            </span>
          </li>
          <li class="flex items-start">
            <span class="mr-4">📸</span>
            <span><strong>Fotoreportáž:</strong> Účastníci berou na vědomé, že svou účastí v soutěži souhlasí s pořízením fotografií své osoby a se sdílením těchto fotografií na sociálních sítích.</span>
          </li>
        </ul>
      </div>
    </div>
  </section>
  </div>
</template>