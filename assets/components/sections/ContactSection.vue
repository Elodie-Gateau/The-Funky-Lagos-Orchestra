<script setup>
import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted, computed} from "vue";
import { Mail, Phone,  } from '@lucide/vue';
import Socials from "../ui/Socials.vue";


const { t, locale } = useI18n();

const email = ref(null);
const phone = ref(null);
const facebook = ref(null);
const instagram = ref(null);
const youtube = ref(null);
const soundcloud = ref(null);

onMounted(async() => {
    const res = await fetch('/api/settings', {credentials: 'include'})
    const data = await res.json();
    email.value = data.email;
    phone.value = data.phone;
    facebook.value = data.facebook;
    instagram.value = data.instagram;
    youtube.value = data.youtube;
    soundcloud.value = data.soundcloud;
})

</script>

<template>
    <section id="contact">
        <SectionTitle :title="t('contact.title')" :subtitle="t('contact.subtitle')" />
        <div>
            <div>
                <Mail />
                <div>
                    <h4>{{ t('contact.email') }}</h4>
                    <p>{{ email }}</p>
                </div>
            </div>
            <div>
                <Phone />
                <div>
                    <h4>{{ t('contact.phone') }}</h4>
                    <p>{{ phone }}</p>
                </div>
            </div>
            <div>
                <h4>{{ t('contact.socials') }}</h4>
                <Socials
                    :facebook= "facebook"
                    :instagram= "instagram"
                    :youtube= "youtube"
                    :soundcloud= "soundcloud"
                />
            </div>
        </div>
    </section>
</template>

<style scoped>

</style>
