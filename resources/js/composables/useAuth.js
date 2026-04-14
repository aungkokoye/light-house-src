import { useRouter } from 'vue-router'
import axios from 'axios'

export function useAuth() {
    const router = useRouter()

    async function requireAuth(userRef, loadingRef) {
        if (!localStorage.getItem('token')) {
            router.push('/login')
            return false
        }
        try {
            const { data } = await axios.get('/api/me')
            userRef.value = data
            return true
        } catch {
            router.push('/login')
            return false
        } finally {
            if (loadingRef) loadingRef.value = false
        }
    }

    return { requireAuth }
}
