<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'

const events = ref([])
let lightbox

onMounted(async () => {
  const res = await fetch('/api/events-with-gallery')
  events.value = await res.json()

  // Wait for DOM to render images
  await nextTick()

  lightbox = new PhotoSwipeLightbox({
    gallery: '.pswp-gallery',
    children: 'a',
    pswpModule: () => import('photoswipe')
  })

  lightbox.init()
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

    <div v-for="event in events" :key="event.id" class="mb-6">
      <div v-if="event.gallery.length > 0">
        <h3 class="text-lg md:text-2xl font-semibold mb-2">{{ event.name }} ({{ event.date }})</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:flex md:flex-wrap md:flex-row gap-2 pswp-gallery">
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

    <!-- Root element for PhotoSwipe -->
    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"></div>
  </div>
</template>
