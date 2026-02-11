import { createI18n } from 'vue-i18n'

const messages = {
  ar: {
    appName: 'منصة أمان',
    login: 'تسجيل الدخول',
    email: 'البريد الإلكتروني',
    password: 'كلمة المرور',
    dashboard: 'لوحة التحكم',
    logout: 'تسجيل الخروج',
  },
}

export const i18n = createI18n({
  legacy: false,
  locale: 'ar',
  fallbackLocale: 'ar',
  messages,
})

