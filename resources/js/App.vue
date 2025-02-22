<template>
    <img
        class="w-full absolute z-0"
        src="../img/background.svg"
        alt="Beer Foam Background"
    />

    <header class="pt-10 lg:pt-20">
        <div class="flex flex-col items-center relative">
            <h1
                class="font-semibold text-4xl sm:text-5xl md:text-6xl lg:text-8xl mb-3 text-gray-900"
            >
                Pioneer Beer kvíz
            </h1>
            <p class="text-lg px-2 lg:text-xl text-center">
                Tady se ukáže, jak moc se vyznáš v
                <span class="font-black">pivu</span>, ve
                <span class="font-black">chmelu</span> a v
                <span class="font-black">historii Žatce</span>!
            </p>

            <div
                class="flex max-w-full w-72 flex-col justify-center items-strech text-center font-bold md:text-lg sm:flex-row gap-3 p-4"
            >
                <router-link
                    class="underline hover:no-underline px-3 py-2 sm:p-3 bg-white/80 hover:bg-white/50"
                    to="/"
                    >Úvod</router-link
                >
                <router-link
                    class="underline hover:no-underline px-3 py-2 sm:p-3 bg-white/80 hover:bg-white/50"
                    to="/vysledky"
                    >Výsledky</router-link
                >
                <router-link
                    class="underline hover:no-underline px-3 py-2 sm:p-3 bg-white/80 hover:bg-white/50"
                    to="/tymy"
                >Týmy</router-link
                >
                <router-link
                    class="underline hover:no-underline px-3 py-2 sm:p-3 bg-white/80 hover:bg-white/50"
                    to="/registrace"
                >Registrace</router-link
                >
            </div>
        </div>
    </header>

    <main
        class="z-10 relative p-4 sm:p-8 md:p-12 max-w-screen-lg flex flex-col m-auto"
    >
        <router-view v-slot="{ Component, route }">
            <div :key="route.name">
              <transition name="fade">
                <component :is="Component" :key="route.path" />
              </transition>
            </div>
        </router-view>
    </main>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
  opacity: 1;
}
</style>
<script setup lang="ts">
</script>