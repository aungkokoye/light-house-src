import { useRouter } from 'vue-router'
import axios from 'axios'

export function useAdminGuard() {
    const router = useRouter()

    // Returns the /api/me data if user is admin, otherwise redirects and returns null
    async function requireAdmin() {
        if (!localStorage.getItem('token')) {
            router.push('/login')
            return null
        }
        try {
            const { data: me } = await axios.get('/api/me')
            if (!me.roles?.some(r => r.name === 'admin')) {
                router.replace('/403')
                return null
            }
            return me
        } catch {
            router.push('/login')
            return null
        }
    }

    return { requireAdmin }
}
