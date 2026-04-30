import { ref, onMounted } from 'vue'

export function useRecaptcha(containerId) {
    const widgetId = ref(null)
    const captchaError = ref('')

    function renderWidget() {
        const el = document.getElementById(containerId)
        if (!el || widgetId.value !== null) return
        widgetId.value = grecaptcha.render(containerId, {
            sitekey: window.__RECAPTCHA_SITE_KEY__,
        })
    }

    onMounted(() => {
        if (window.__recaptchaLoaded) {
            renderWidget()
        } else {
            window.__recaptchaReadyQueue = window.__recaptchaReadyQueue || []
            window.__recaptchaReadyQueue.push(renderWidget)
        }
    })

    function getToken() {
        return widgetId.value !== null ? grecaptcha.getResponse(widgetId.value) : ''
    }

    function reset() {
        captchaError.value = ''
        if (widgetId.value !== null) grecaptcha.reset(widgetId.value)
    }

    return { getToken, reset, captchaError }
}
