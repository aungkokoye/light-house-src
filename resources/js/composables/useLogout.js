import { useRouter } from 'vue-router'
import axios from 'axios'
import { clearAuth } from '../bootstrap'

export function useLogout() {
    const router = useRouter()

    async function logout() {
        try {
            await axios.post('/api/logout')
        } catch { /* token may already be invalid */ } finally {
            clearAuth()
            router.push('/login')
        }
    }

    return { logout }
}
