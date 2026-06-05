<script setup>
import { X } from '@lucide/vue';
import { ref } from 'vue'
import NavMenu from "../ui/NavMenu.vue";
import LangSwitcher from "../ui/LangSwitcher.vue";

const isOpen = ref(false)
const props = defineProps({
    tracks: Array,
    events: Array,
    photos: Array,
})
</script>

<template>
    <div class="mobile-menu">
        <LangSwitcher v-if="!isOpen" />
        <button class="hamburger" @click="isOpen = true" aria-label="Ouvrir le menu">
            <span />
            <span />
            <span class="short" />
        </button>
    </div>

    <div class="desktop-nav">
        <div class="wrapper">
            <NavMenu />
            <LangSwitcher />
        </div>
    </div>

    <Transition name="backdrop">
        <div v-if="isOpen" class="backdrop" @click="isOpen = false" />
    </Transition>

    <Transition name="panel">
        <aside v-if="isOpen" class="slide-panel">
            <div class="panel-header">
                <button class="btn-close" @click="isOpen = false" aria-label="Fermer">
                    <X :size="16" />
                </button>
            </div>

            <div class="panel-nav" @click="isOpen = false">
                <NavMenu :tracks="tracks" :events="events" :photos="photos"/>
            </div>

            <div class="panel-footer">
                <LangSwitcher />
            </div>
        </aside>
    </Transition>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/variables' as *;
@use '/assets/styles/utils/breakpoints' as *;

/* ── Barre mobile ── */
.mobile-menu {
    background-color: var(--background);
    color: var(--title);
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    padding: $size-14;
    border-bottom: 1px solid rgba(42, 16, 0, 0.08);
    box-shadow: 0 2px 12px rgba(42, 16, 0, 0.04);
    height: 10vh;
    z-index: 50;

    @media (min-width: $md) {
        display: none;
    }
}

.hamburger {
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px;
    display: flex;
    flex-direction: column;
    gap: 5px;

    span {
        width: 22px;
        height: 2px;
        background: #{$color-brown};
        border-radius: 2px;
        display: block;

        &.short {
            width: 14px;
        }
    }
}

/* ── Nav desktop ── */
.desktop-nav {
    display: none;

    @media (min-width: $md) {
        display: block;
        width: 100%;
        position: sticky;
        top: 0;
        border-bottom: 1px solid rgba(42, 16, 0, 0.08);
        box-shadow: 0 2px 12px rgba(42, 16, 0, 0.04);
        height: 6vh;
        z-index: 999;
        background-color: var(--background);

        .wrapper {
            display: flex;
            width: 100%;
            justify-content: center;
            align-items: center;
            height: 6vh;

            :deep(nav) {
                width: 100%;
                ul {
                    padding: $size-20;

                    li {
                        box-shadow: none;
                        justify-content: center;

                        a{
                            justify-content: center;
                            color: var(--title);
                            font-family: "Raleway", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                            font-size: $size-12;
                            font-weight: 600;
                            letter-spacing: 0.15em;
                            width: auto;
                            height: auto;
                            position: relative;
                        }

                        a:hover {
                            color: var(--secondary);
                        }

                        a::after {
                            content: '';
                            position: absolute;
                            bottom: 0;
                            left: 0;
                            height: 1.5px;
                            background-color: var(--secondary);
                            width: 0;
                            transition: all 0.3s ease-in-out;
                        }

                        a:hover::after {
                            width: 100%;
                        }
                    }
                }
            }
            }

            :deep(.lang-switcher) {
                width: 10%;
                justify-content: flex-start;
            }
    }

}
    @media (min-width: $lg) {
        .wrapper {
            max-width: 1000px;
            margin: 0 auto;
        }
    }

/* ── Backdrop ── */
.backdrop {
    position: fixed;
    inset: 0;
    background: rgba(42, 16, 0, 0.35);
    z-index: 90;
}

.backdrop-enter-active,
.backdrop-leave-active {
    transition: opacity 0.3s ease;
}

.backdrop-enter-from,
.backdrop-leave-to {
    opacity: 0;
}

/* ── Panneau latéral ── */
.slide-panel {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 290px;
    background: #{$color-cream};
    z-index: 100;
    display: flex;
    flex-direction: column;
    box-shadow: -8px 0 40px rgba(42, 16, 0, 0.18);
    border-left: 3px solid #{$color-gold};
}

.panel-enter-active,
.panel-leave-active {
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.panel-enter-from,
.panel-leave-to {
    transform: translateX(105%);
}

/* ── Header du panneau ── */
.panel-header {
    padding: $size-18 $size-22;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.btn-close {
    background: rgba(42, 16, 0, 0.06);
    border: none;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #{$color-brown};
    transition: background 0.15s;

    &:hover {
        background: rgba(42, 16, 0, 0.12);
    }
}

/* ── Navigation dans le panel ── */
.panel-nav {
    flex: 1;
    overflow-y: auto;
    padding-top: $size-8;

    :deep(nav) {
        height: auto;

        ul {
            height: auto;
            flex-direction: column;
            justify-content: flex-start;
            gap: 0;

            li {
                height: auto;
                justify-content: flex-start;
                border-left: 3px solid transparent;
                transition: all 0.15s;

                &:hover {
                    border-left-color: #{$color-red};
                    background: rgba(196, 43, 28, 0.05);
                }

                a {
                    padding: $size-12 $size-22;
                    text-align: left;
                    height: auto;
                    border: none;
                    color: #{$color-brown-mid};
                    font-family: 'Raleway', sans-serif;
                    font-weight: 700;
                    font-size: $size-14;
                    letter-spacing: 0.1em;
                    text-transform: uppercase;
                    width: 100%;

                    &:hover {
                        color: #{$color-red};
                    }
                }
            }
        }
    }
}

/* ── Footer du panneau ── */
.panel-footer {
    padding: $size-16 $size-22;
    border-top: 1px solid rgba(42, 16, 0, 0.07);
    display: flex;
    flex-direction: column;
    gap: $size-10;
    align-items: stretch;
}
</style>
