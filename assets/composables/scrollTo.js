import { useRouter } from 'vue-router'

export function useScrollTo() {
    const router = useRouter()

    function scrollTo(id) {
        router.push('/').then(() => {
            setTimeout(() => {
                document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' })
            }, 100)
        })
    }

    return { scrollTo }
}
