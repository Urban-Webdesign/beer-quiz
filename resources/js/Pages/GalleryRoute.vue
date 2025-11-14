<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'

const events = ref([])
let lightbox = null

onMounted(async () => {
    try {
        const res = await fetch('/api/events-with-gallery')
        if (!res.ok) {
            console.error('Failed to load events:', res.status, await res.text())
            return
        }

        const data = await res.json()
        console.log('Loaded events:', data) // <— see what actually comes from API

        events.value = Array.isArray(data) ? data : []

        // Wait for DOM to be updated with the new events
        await nextTick()

        lightbox = new PhotoSwipeLightbox({
            gallery: '.pswp-gallery',
            children: 'a',
            pswpModule: () => import('photoswipe'),
        })

        lightbox.init()
    } catch (e) {
        console.error('Error loading gallery:', e)
    }
})

onBeforeUnmount(() => {
    if (lightbox) {
        lightbox.destroy()
        lightbox = null
    }
})
</script>

<template>
    <div>
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-black mb-2">Galerie</h2>

        <!-- helpful debug states -->
        <p v-if="!events || events.length === 0" class="text-sm text-gray-500">
            Zatím tu není žádná galerie.
        </p>

        <div v-else>
            <div v-for="event in events" :key="event.id" class="mb-6">
                <div v-if="event.gallery && event.gallery.length > 0">
                    <h3 class="text-lg md:text-2xl font-semibold mb-2">
                        {{ event.name }} ({{ event.date }})
                    </h3>

                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:flex md:flex-wrap md:flex-row gap-2 pswp-gallery"
                    >
                        <a
                            v-for="image in event.gallery"
                            :key="image.url"
                            :href="image.url"
                            :data-pswp-width="image.width || 1200"
                            :data-pswp-height="image.height || 800"
                            target="_blank"
                            rel="noopener"
                        >
                            <img
                                :src="image.url"
                                alt="Galerie"
                                class="md:max-w-[300px] md:max-h-[150px] w-full h-auto rounded shadow-sm hover:shadow-lg transition"
                            />
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Root element for PhotoSwipe (v5 doesn’t actually require the old .pswp element,
             but keeping it won’t hurt) -->
        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"></div>
    </div>
</template>
