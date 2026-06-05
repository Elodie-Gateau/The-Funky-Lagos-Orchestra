<script setup>
import {useI18n} from "vue-i18n";
import {dateFormatDay, dateFormatMonth, dateFormatYear} from "../../composables/dateFormat.js";

const { t, locale } = useI18n();

defineProps({
    event: {
        type: Object,
        required: true
    }
})


</script>

<template>
    <li>
        <div class="event-date">
            <span>{{ dateFormatDay(event.date, locale) }}</span>
            <span>{{ dateFormatMonth(event.date, locale) }}</span>
            <span>{{ dateFormatYear(event.date) }}</span>
        </div>
        <div class="event-info">
            <span>{{ event.name }}</span>
            <span>{{ event.location }}</span>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                <span>{{ event.city }}</span>
            </div>
        </div>
    </li>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;

li{
    background: var(--text-button);
    border-radius: $size-6;
    overflow: hidden;
    display: grid;
    grid-template-columns: $size-72 1fr;
    transition: transform .2s;
    box-shadow: 0 4px 18px rgba(42, 16, 0, .1);
    max-width: 1200px;
    align-items: stretch;

    .event-date{
        background: var(--secondary);
        color: var(--text-button);
        width: $size-72;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 0 $size-8;
        font-family: "Raleway", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;

        & > span:first-child{
            font-size: $size-28;
            font-weight: 900;
            line-height: 1;
            margin: 0;
            padding: 0;
        }
        & > span:nth-child(2){
            font-size: $size-10;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-top: .15rem;
        }
        & > span:last-child{
            font-size: $size-14;
            font-weight: 700;
            letter-spacing: .12em;
        }
    }

    .event-info{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        padding: $size-16 $size-20;
        gap: $size-4;
        line-height: 1.2;

        & > span:first-child{
            font-family: "Raleway", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: $size-16;
            font-weight: 700;
            color: var(--title);
        }

        & > span:nth-child(2){
            font-size: $size-14;
            color: var(--text);
        }

        & > div:last-child{
            font-size: $size-12;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: $size-4;

            & > svg{
                color: var(--secondary);
                width: $size-18;
                height: $size-18;
            }
        }
    }

}

li:hover {
    transform: translateY(-2px);
}

</style>
