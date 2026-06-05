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
        <div class="hero-container">
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
            <img src="/assets/images/logo.svg" fetchpriority="high" loading="eager" :alt="t('main.logo')">
        </div>
        <div class="title">
            <h1>
                <div class="wrapper">
                    <span>{{ t('main.title_part1') }}</span>
                    <span>{{ t('main.title_part2') }}</span>
                </div>
                <span>{{ t('main.title_part3') }}</span>
                <span>{{ t('main.title_part4') }}</span>
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
    gap: $size-20;
    background: radial-gradient(ellipse at 50% 40%, #4a2000 0%, #{$color-brown} 45%, #{$color-brown-dark} 100%);
    overflow: hidden;

    .hero-container {
        position: relative;
        z-index: 1;
        width: fit-content;

        .sun-rays {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: min(100vw, 100vh);
            height: min(100vw, 100vh);
            pointer-events: none;
            overflow: visible;
            z-index: 0;
        }
        img {
            width: 100%;
            display: block;
            max-height: 40vh;
            position: relative;
            z-index: 2;
            filter: drop-shadow(0 0 48px rgba(200, 148, 58, 0.35));
        }
    }

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
            gap: $size-8;

            .wrapper {
                display: flex;
                gap: $size-16;
                width: 100%;
                justify-content: center;
            }

            span {
                display: block;
            }

            span:nth-child(3) {
                color: var(--accent);
                font-size: clamp($size-24, 5.5vw, $size-64);
                margin-top: $size-6;
                font-weight: 700;

            }
        }

        h2 {
            text-align: center;
            font-weight: 600;
            color: var(--accent-light);
            margin-top: $size-40;
            letter-spacing: .2em;
        }
    }
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
               span:nth-child(3) {
                   margin-top: $size-12;
               }
           }

           h2 {
               margin-top: $size-48;
           }


       }
    }
}

@media (min-width: $lg) {
    .hero {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 0;

        .hero-container {
            width: 60%;

            img {
                max-height: 60vh;
            }
        }

       .title {
           margin-top: 0;
           width: 40%;

           h1 {
               text-align: left;

               .wrapper {
                   flex-direction: column;
                   gap: 0;
               }
           }

           h2 {
               text-align: left;
           }
       }
    }
}

</style>
