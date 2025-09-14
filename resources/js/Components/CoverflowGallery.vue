
<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed } from 'vue'

const props = defineProps({
  images: { type: Array, required: true }, // array of string URLs
  autoplay: { type: [Boolean, Number], default: true }, // enable by default
  autoSpeed: { type: Number, default: 0.06 }, // slides per second
  loop: { type: Boolean, default: true },
  centerScale: { type: Number, default: 1 },
  sideScale: { type: Number, default: 0.9 },
  spacing: { type: Number, default: 220 },
  height: { type: [Number, String], default: 360 }
})

const current = ref(0)
const root = ref(null)
const containerWidth = ref(0)
let rafId = null
let lastTs = 0
let roRef = null

// compute slide width so exactly three slides can fit comfortably
const slideGap = 14 // px gap between slides (slightly smaller gap)
const slideWidthPx = computed(() => {
  const w = containerWidth.value || 0
  const numericHeight = typeof props.height === 'number' ? props.height : parseFloat(String(props.height)) || 360
  if (!w) return Math.min(numericHeight, 360)
  // More conservative calculation: start from a strict one-third then reserve
  // extra gap space so three slides fit with breathing room.
  const rawThird = Math.floor(w / 3)
  const reserved = Math.ceil(slideGap * 1.8)
  const raw = Math.max(48, rawThird - reserved)
  // ensure we don't exceed the visual height or become tiny
  const clamped = Math.max(64, Math.min(raw, numericHeight))
  return clamped
})

const spacingPx = computed(() => {
  return slideWidthPx.value + slideGap
})

const styleRoot = computed(() => ({
  '--height': typeof props.height === 'number' ? `${props.height}px` : props.height,
  '--slide-w': `${slideWidthPx.value}px`,
  '--gap': `${slideGap}px`
}))

const tick = (ts) => {
  const n = props.images.length
  if (!props.autoplay || n <= 1) { pause(); return }
  if (!lastTs) lastTs = ts
  const dt = (ts - lastTs) / 1000
  lastTs = ts
  const nextVal = current.value + (props.autoSpeed * dt)
  if (props.loop && n > 0) {
    current.value = ((nextVal % n) + n) % n
  } else {
    current.value = Math.min(n - 1, nextVal)
  }
  rafId = requestAnimationFrame(tick)
}

const play = () => {
  if (!props.autoplay) return
  cancelAnimationFrame(rafId)
  lastTs = 0
  rafId = requestAnimationFrame(tick)
}
const pause = () => { cancelAnimationFrame(rafId); rafId = null }

const clampIndex = (i) => {
  const n = props.images.length
  if (props.loop) {
    return ((i % n) + n) % n
  }
  return Math.max(0, Math.min(n - 1, i))
}

// (spacingPx is computed above as slideWidth + gap)

const slideStyle = (i) => {
  const n = props.images.length
  let d = i - current.value
  if (props.loop) {
    if (d > n / 2) d -= n
    if (d < -n / 2) d += n
  }
  const absd = Math.abs(d)

  const rotateY = 0
  const translateX = d * spacingPx.value
  const translateZ = 0

  // Smooth scale and opacity based on distance to the current index
  const scale = props.sideScale + (props.centerScale - props.sideScale) * Math.max(0, 1 - Math.min(absd, 1))

  // Center fully visible; neighbors fade to 0.7 at distance 1; beyond 1 -> 0
  // Show center and immediate neighbors strongly.
  // Also show second neighbors faintly so a third image is visible when layout clips.
  let opacity = 0
  if (absd <= 1) {
    opacity = 1 - 0.3 * absd
  } else if (absd <= 2) {
    // second neighbor: subtle presence
    opacity = 0.25 - 0.08 * (absd - 1)
  } else {
    opacity = 0
  }

  const transform = `translate(-50%, -50%) translateX(${translateX}px) translateZ(${translateZ}px) rotateY(${rotateY}deg) scale(${scale})`
  return {
    transform,
    opacity,
    zIndex: 1000 - Math.round(absd * 10),
    pointerEvents: opacity < 0.02 ? 'none' : 'auto'
  }
}

let measure
onMounted(() => {
  // measure container width and listen for resizes
  measure = () => { containerWidth.value = root.value ? root.value.clientWidth : 0 }
  measure()
  window.addEventListener('resize', measure)
  // observe the root for layout changes (images/font load)
  roRef = new ResizeObserver(measure)
  if (root.value) roRef.observe(root.value)
  play()
})

onBeforeUnmount(() => {
  pause()
  window.removeEventListener('resize', measure)
  if (roRef) {
    try { roRef.disconnect() } catch (e) {}
    roRef = null
  }
})

watch(() => props.autoplay, () => { pause(); play() })
</script>

<template>
  <div class="coverflow-root" ref="root" :style="styleRoot">
    <div class="coverflow-viewport" :style="{ '--count': images.length }">
      <div
        v-for="(url, i) in images"
        :key="i"
        class="coverflow-slide"
        :class="{ active: i === current }"
        :style="slideStyle(i)"
      >
        <img :src="url" :alt="`image ${i+1}`" draggable="false" />
      </div>
    </div>
  </div>

</template>

<style scoped>
.coverflow-root {
  --height: 360px;
  position: relative;
  height: var(--height);
  user-select: none;
  overflow: hidden;
  margin: 0 auto;
  max-width: 1100px;
  width: 100%;
}
.coverflow-viewport {
  height: 100%;
  width: 100%;
  position: relative;
  transform-style: preserve-3d;
  display: flex;
  align-items: center;
  justify-content: center;
  /* fade edges gently so second neighbors remain visible */
  -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0), rgba(0,0,0,1) 6%, rgba(0,0,0,1) 94%, rgba(0,0,0,0));
  mask-image: linear-gradient(to right, rgba(0,0,0,0), rgba(0,0,0,1) 6%, rgba(0,0,0,1) 94%, rgba(0,0,0,0));
}
.coverflow-slide {
  position: absolute;
  /* width driven by JS via --slide-w; fallback to height-based sizing */
  width: var(--slide-w, min(calc(var(--height) * 1.1), 92vw));
  height: var(--height);
  left: 50%;
  top: 50%;
  transition: transform 0s linear, opacity 400ms ease;
  border-radius: 12px;
  box-shadow: 0 18px 40px rgba(10,10,20,0.35);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #111;
}
.coverflow-slide img{
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  -webkit-user-drag: none;
}
.coverflow-slide.active{
  box-shadow: 0 30px 60px rgba(10,10,20,0.5);
}
/* controls removed for a minimalist conveyor style */

@media (max-width: 900px){
}
@media (max-width: 480px){
  .coverflow-root { --height: 200px }
}
</style>
