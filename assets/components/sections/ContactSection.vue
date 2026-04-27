<script setup>
import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted, computed} from "vue";
import { Mail, Phone,  } from '@lucide/vue';
import Socials from "../ui/Socials.vue";


const { t, locale } = useI18n();

const email = ref(null);
const phone = ref(null);
const phoneWhatsApp = computed(() =>
    phone.value ? '33' + phone.value.slice(1) : null
)
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

const form = ref({ name: '', from: '', subject: '', message: '' })
const status = ref(null)

async function submit() {
    status.value = 'sending'
    try {
        const res = await fetch('/api/email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(form.value),
        })
        if (res.ok) {
            status.value = 'success'
            form.value = { name: '', from: '', subject: '', message: '' }
        } else {
            status.value = 'error'
        }
    } catch {
        status.value = 'error'
    }
}
</script>

<template>
    <section id="contact">
        <SectionTitle :title="t('contact.title')" :subtitle="t('contact.subtitle')" />
        <div class="contact-container">
            <div class="contact-item">
                <div class="icon">
                    <a :href="`mailto:${email}`" target="_blank" rel="noopener noreferrer"><Mail /></a>
                </div>
                <div>
                    <h4>{{ t('contact.email') }}</h4>
                    <p>{{ email }}</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="icon">
                    <a :href="`https://wa.me/${phoneWhatsApp}`" target="_blank" rel="noopener noreferrer"><Phone /></a>
                </div>
                <div>
                    <h4>{{ t('contact.phone') }}</h4>
                    <p>{{ phone }}</p>
                </div>
            </div>
            <div class="contact-socials">
                <h4>{{ t('contact.socials') }}</h4>
                <Socials
                    :facebook= "facebook"
                    :instagram= "instagram"
                    :youtube= "youtube"
                    :soundcloud= "soundcloud"
                />
            </div>
        </div>
        <div>
            <form @submit.prevent="submit">
                <label for="name">{{ t('contact.name') }}</label>
                <input id="name" v-model="form.name" type="text" :placeholder="t('contact.placeholder_name')" required />

                <label for="email">{{ t('contact.email') }}</label>
                <input id="email" v-model="form.from" type="email" :placeholder="t('contact.placeholder_email')" required />

                <label for="subject">{{ t('contact.subject') }}</label>
                <input id="subject" v-model="form.subject" type="text" :placeholder="t('contact.placeholder_subject')" required />

                <label for="message">{{ t('contact.message') }}</label>
                <textarea id="message" v-model="form.message" :placeholder="t('contact.placeholder_message')" required />

                <button type="submit" :disabled="status === 'sending'">
                    {{ status === 'sending' ? t('contact.is_sending') : t('contact.send') }}
                </button>

                <p v-if="status === 'success'">{{ t('contact.success') }}</p>
                <p v-if="status === 'error'">{{ t('contact.error') }}</p>
            </form>
        </div>
    </section>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;

#contact {
    h4 {
        font-family: "Raleway", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--gradient-end);
        font-size: $size-12;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .contact-container {
        display: flex;
        flex-direction: column;
        gap: $size-26;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: $size-16;

        p{
            font-size: $size-16;
            color: var(--text);
            opacity: .8;
        }
    }

    .contact-socials {
        display: flex;
        flex-direction: column;
        gap: $size-16;
    }
}
</style>
