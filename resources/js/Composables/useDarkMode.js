import { ref, onMounted } from 'vue';

const isDark = ref(false);

function setTheme(theme) {
    isDark.value = theme === 'dark';
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', theme);
}

function toggleDark() {
    setTheme(isDark.value ? 'light' : 'dark');
}

onMounted(() => {
    const userTheme = localStorage.getItem('theme');
    if (userTheme === 'dark' || (!userTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        setTheme('dark');
    } else {
        setTheme('light');
    }
});

export function useDarkMode() {
    return { isDark, toggleDark };
}
