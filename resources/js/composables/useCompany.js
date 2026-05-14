const COMPANY_NAME     = import.meta.env.VITE_COMPANY_NAME ?? ''
const COMPANY_NAME_MAIN = COMPANY_NAME.split(' ').slice(0, 1).join(' ')
const COMPANY_NAME_SUB  = COMPANY_NAME.split(' ').slice(1).join(' ')

export function useCompany() {
    return { COMPANY_NAME, COMPANY_NAME_MAIN, COMPANY_NAME_SUB }
}
