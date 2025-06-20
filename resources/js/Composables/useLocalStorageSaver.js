import { ref, computed, onMounted, onUnmounted, watch } from "vue";

export function useLocalStorageSaver(form, resourceId, formFields, keyPrefix = 'edit-draft') {
    const localStorageKey = computed(() => `${keyPrefix}-${resourceId}`);
    const isSavedToLocalStorage = ref(false);
    const isDataLoaded = ref(false);

    const hasFormContent = computed(() => {
        return formFields.some(field => {
            const value = form[field];
            if (Array.isArray(value)) {
                return value.length > 0;
            }
            if (typeof value === 'string') {
                return value.trim() !== '';
            }
            return value !== null && value !== undefined;
        });
    });

    const saveToLocalStorage = () => {
        if (hasFormContent.value) {
            const formData = {};
            formFields.forEach(field => {
                formData[field] = form[field];
            });
            formData.savedAt = new Date().toISOString();
            localStorage.setItem(localStorageKey.value, JSON.stringify(formData));
            isSavedToLocalStorage.value = true;
        } else {
            localStorage.removeItem(localStorageKey.value);
            isSavedToLocalStorage.value = false;
        }
    };

    const loadFromLocalStorage = () => {
        const savedData = localStorage.getItem(localStorageKey.value);
        if (savedData) {
            try {
                const parsedData = JSON.parse(savedData);
                formFields.forEach(field => {
                    if (parsedData[field] !== undefined) {
                        form[field] = parsedData[field];
                    }
                });
                isSavedToLocalStorage.value = true;
            } catch (error) {
                console.error('Error loading saved data:', error);
                localStorage.removeItem(localStorageKey.value);
                isSavedToLocalStorage.value = false;
            }
        }
        isDataLoaded.value = true;
    };

    let saveTimeout;
    watch(form, () => {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveToLocalStorage();
        }, 500);
    }, { deep: true });

    onMounted(() => {
        loadFromLocalStorage();
    });

    onUnmounted(() => {
        clearTimeout(saveTimeout);
    });

    const clearLocalStorage = () => {
        localStorage.removeItem(localStorageKey.value);
        isSavedToLocalStorage.value = false;
    };

    return {
        isSavedToLocalStorage,
        isDataLoaded,
        hasFormContent,
        clearLocalStorage
    };
}
