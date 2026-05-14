import { useRouter } from 'vue-router'

export function useGoBack() {
    const router = useRouter()

    // Navigate back to a list page, restoring its last saved filters.
    // storageKey: sessionStorage key where the list page saves its URL.
    // fallback: route to use when nothing is stored (e.g. direct navigation).
    function goBack(fallback, storageKey = null) {
        if (storageKey) {
            const stored = sessionStorage.getItem(storageKey)
            if (stored) { router.push(stored); return }
        }
        router.push(fallback)
    }

    return { goBack }
}
