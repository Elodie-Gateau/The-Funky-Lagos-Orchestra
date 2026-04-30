<script setup>
import { useI18n } from "vue-i18n";
import NavMenu from "../ui/NavMenu.vue";
import { ref, onMounted, onUnmounted } from "vue";

const { t } = useI18n();

const sunAngle = ref(0);
let animFrame = null;
let targetAngle = 0;
let currentAngle = 0;
let isIdle = true;
let idleTimer = null;

function onMouseMove(e) {
    const cx = window.innerWidth / 2;
    const cy = window.innerHeight * 0.4;
    targetAngle = Math.atan2(e.clientY - cy, e.clientX - cx) * (180 / Math.PI);
    isIdle = false;
    clearTimeout(idleTimer);
    idleTimer = setTimeout(() => { isIdle = true; }, 3000);
}

function tick() {
    if (isIdle) {
        currentAngle += 0.12;
    } else {
        let diff = targetAngle - currentAngle;
        while (diff > 180) diff -= 360;
        while (diff < -180) diff += 360;
        currentAngle += diff * 0.04;
    }
    sunAngle.value = currentAngle;
    animFrame = requestAnimationFrame(tick);
}

onMounted(() => {
    window.addEventListener("mousemove", onMouseMove);
    tick();
});

onUnmounted(() => {
    window.removeEventListener("mousemove", onMouseMove);
    if (animFrame) cancelAnimationFrame(animFrame);
    clearTimeout(idleTimer);
});
</script>

<template>
    <section class="hero">
        
        <svg class="sun-rays" viewBox="-500 -500 1000 1000" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <g :transform="`rotate(${sunAngle})`">
                <!-- Rayons cardinaux -->
                <polygon points="-9,0 9,0 2,-480" fill="rgba(200,148,58,0.35)"/>
                <polygon points="-7,4 7,-4 480,12" fill="rgba(200,148,58,0.25)"/>
                <polygon points="-9,0 9,0 2,480" fill="rgba(200,148,58,0.35)"/>
                <polygon points="-7,4 7,-4 -480,12" fill="rgba(200,148,58,0.25)"/>
                <!-- Rayons diagonaux -->
                <polygon points="0,-9 0,9 340,-340" fill="rgba(240,192,96,0.28)"/>
                <polygon points="0,-9 0,9 340,340" fill="rgba(240,192,96,0.28)"/>
                <polygon points="0,-9 0,9 -340,-340" fill="rgba(240,192,96,0.28)"/>
                <polygon points="0,-9 0,9 -340,340" fill="rgba(240,192,96,0.28)"/>
                <!-- Rayons secondaires -->
                <polygon points="-4,0 4,0 1,-420" fill="rgba(200,148,58,0.14)"/>
                <polygon points="-4,0 4,0 420,0" fill="rgba(200,148,58,0.12)"/>
                <polygon points="-4,0 4,0 1,420" fill="rgba(200,148,58,0.14)"/>
                <polygon points="-4,0 4,0 -420,0" fill="rgba(200,148,58,0.12)"/>
                <!-- Rayons intermédiaires à 22.5° -->
                <polygon points="-4,2 4,-2 155,-375" fill="rgba(200,148,58,0.18)"/>
                <polygon points="-2,-4 2,4 375,-155" fill="rgba(200,148,58,0.18)"/>
                <polygon points="2,-4 -2,4 375,155" fill="rgba(200,148,58,0.18)"/>
                <polygon points="4,2 -4,-2 155,375" fill="rgba(200,148,58,0.18)"/>
                <polygon points="4,-2 -4,2 -155,375" fill="rgba(200,148,58,0.18)"/>
                <polygon points="2,4 -2,-4 -375,155" fill="rgba(200,148,58,0.18)"/>
                <polygon points="-2,4 2,-4 -375,-155" fill="rgba(200,148,58,0.18)"/>
                <polygon points="-4,-2 4,2 -155,-375" fill="rgba(200,148,58,0.18)"/>
            </g>
        </svg>

        <img src="/assets/images/logo.svg" :alt="t('main.logo')">

        <div class="title">
            <h1>{{ t('main.title_part1') }}
                <span>{{ t('main.title_part2') }}</span>
                <span>{{ t('main.title_part3') }}</span>
            </h1>
            <h2>{{ t('home.subtitle') }}</h2>
        </div>
    </section>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/breakpoints' as *;
@use '/assets/styles/utils/variables' as *;


.hero {
    position: relative;
    height: 90vh;
    min-height: 600px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 1.25rem;
    background: radial-gradient(ellipse at 50% 40%, #4a2000 0%, #{$color-brown} 45%, #{$color-brown-dark} 100%);
    overflow: hidden;

    .title {
        position: relative;
        z-index: 1;

        h1 {
            text-align: center;
            display: flex;
            flex-direction: column;
            row-gap: 3.2px;
            line-height: 1;
            color: var(--background);

            span {
                display: block;
            }

            span:nth-child(2) {
                color: var(--accent);
                font-size: $size-22;
                margin-top: $size-6;
                font-weight: 800;
            }
        }

        h2 {
            text-align: center;
            font-size: $size-14;
            font-weight: 600;
            color: var(--accent-light);
            margin-top: $size-10;
        }
    }
}

.sun-rays {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: min(110vw, 110vh);
    height: min(110vw, 110vh);
    pointer-events: none;
    overflow: visible;
}

img {
    max-height: 40vh;
    position: relative;
    z-index: 1;
    filter: drop-shadow(0 0 48px rgba(200, 148, 58, 0.35));
}

.menu {
    position: relative;
    z-index: 1;
}

@media (min-width: $md) {
    .hero {
        height: 94vh;
       .title {
           margin-top: $size-64;

           h1 {
               span:nth-child(2) {
                   margin-top: $size-12;
                   font-size: $size-34;
               }
           }

           h2 {
               margin-top: $size-40;
               font-size: $size-16;
           }


       }
    }
}

</style>
