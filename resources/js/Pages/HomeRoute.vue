<script setup>
  import kalendar from '../../img/kalendar.jpg';
  import vysledky from '../../img/vysledky.jpg';
  import tymy from '../../img/tymy.jpg';
</script>

<template>
    <header class="mb-4 sm:mb-8">
      <div class="flex flex-col items-center relative">
        <h1
            class="font-semibold text-4xl sm:text-5xl md:text-6xl lg:text-8xl mb-3 text-gray-900"
        >
          Pioneer Beer kvíz
        </h1>
        <p class="text-lg px-2 lg:text-xl text-center">
          Předveď své znalosti v pub kvízu nejenom o <span class="font-black">chmelu</span> a <span class="font-black">pivu</span>!
        </p>
      </div>
    </header>

    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-7 sm:gap-4 md:gap-6">
      <div class="rounded shadow-sm bg-gray-50 p-3 md:p-5 flex flex-col gap-2 items-center justify-between">
        <div class="text-center flex flex-col items-center">
            <img :src="kalendar" alt="Pioneer Beer kvíz - kalendář" class="mb-3 rounded shadow-sm">
            <h3 class="text-xl font-black">Přehled všech kol</h3>
            <p class="text-center">V kalendáři najdeš všechna uskutečněná kola stejně tak jako ta, co se teprve uskuteční.</p>
        </div>
        <a href="/kalendar" target="_top" class="min-w-[150px] max-w-full w-auto font-medium text-center bg-green-600 text-white px-5 py-1.5 rounded-full hover:bg-green-700 disabled:bg-green-400">
          Kalendář
        </a>
      </div>
      <div class="rounded shadow-sm bg-gray-50 p-3 md:p-5 flex flex-col gap-2 items-center justify-between">
        <div class="text-center flex flex-col items-center">
            <img :src="vysledky" alt="Pioneer Beer kvíz - výsledky" class="mb-3 rounded shadow-sm">
            <h3 class="text-xl font-black">Nejnovější výsledky</h3>
            <p class="text-center">Výsledková tabule ti automaticky zobrazí nejnovější výsledky.</p>
        </div>
        <a href="/kviz" target="_top" class="min-w-[150px] max-w-full w-auto font-medium text-center bg-green-600 text-white px-5 py-1.5 rounded-full hover:bg-green-700 disabled:bg-green-400">
          Výsledky
        </a>
      </div>
      <div class="rounded shadow-sm bg-gray-50 p-3 md:p-5 flex flex-col gap-2 items-center justify-between">
        <div class="text-center flex flex-col items-center">
            <img :src="tymy" alt="Pioneer Beer kvíz - týmy" class="mb-3 rounded shadow-sm">
            <h3 class="text-xl font-black">Seznam týmů</h3>
            <p class="text-center">Historický přehled všech týmů, které se této soutěže zúčastnily.</p>
        </div>
        <a href="/tymy" target="_top" class="min-w-[150px] max-w-full w-auto font-medium text-center bg-green-600 text-white px-5 py-1.5 rounded-full hover:bg-green-700 disabled:bg-green-400">
          Týmy
        </a>
      </div>
    </div>
</template>
<script setup lang="ts">
</script>