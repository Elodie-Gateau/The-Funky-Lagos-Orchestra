<script setup>

defineProps({
    facebook: String,
    instagram: String,
    youtube: String,
    soundcloud: String
})
</script>

<template>
    <div class="socials">
        <div v-if="facebook" class="icon">
            <a  :href="facebook" :aria-label="facebook">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
        </div>
        <div v-if="instagram" class="icon">
            <a :href="instagram" :aria-label="instagram">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
            </a>
        </div>
        <div v-if="youtube" class="icon">
            <a :href="youtube" :aria-label="youtube">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M21.8 8s-.2-1.4-.8-2c-.8-.8-1.6-.8-2-.9C16.8 5 12 5 12 5s-4.8 0-7 .1c-.4.1-1.2.1-2 .9-.6.6-.8 2-.8 2S2 9.6 2 11.2v1.5c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.8.8 1.8.8 2.2.8C6.8 19 12 19 12 19s4.8 0 7-.2c.4-.1 1.2-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.5C22 9.6 21.8 8 21.8 8zM10 14.5v-5l5.5 2.5-5.5 2.5z"/></svg>
            </a>
        </div>
        <div v-if="soundcloud" class="icon">
            <a :href="soundcloud" :aria-label="soundcloud">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M1.175 12.225c-.015.108-.03.216-.03.333 0 .117.015.225.03.333l-.03.117.03.108c0 .9.72 1.62 1.62 1.62.9 0 1.62-.72 1.62-1.62V9.783c0-.9-.72-1.62-1.62-1.62-.9 0-1.62.72-1.62 1.62v2.442zm3.996 2.358c0 .9.72 1.62 1.62 1.62.9 0 1.62-.72 1.62-1.62V8.55c0-.9-.72-1.62-1.62-1.62-.9 0-1.62.72-1.62 1.62v6.033zm3.996.54c0 .9.72 1.62 1.62 1.62.9 0 1.62-.72 1.62-1.62V7.47c0-.9-.72-1.62-1.62-1.62-.9 0-1.62.72-1.62 1.62v7.653zm3.996-.54c0 .9.72 1.62 1.62 1.62.9 0 1.62-.72 1.62-1.62V9c0-.9-.72-1.62-1.62-1.62-.9 0-1.62.72-1.62 1.62v5.583zM19.8 9.3c-.27-2.7-2.43-4.8-5.13-4.8-1.35 0-2.565.513-3.483 1.35C11.07 5.985 11.016 6 11.016 6v8.883h7.317C19.8 14.883 21 13.683 21 12.183c0-1.35-.99-2.43-2.25-2.7l.05-.183z"/></svg>
            </a>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;

.socials {
    display: flex;
    gap: $size-16;
}

</style>
